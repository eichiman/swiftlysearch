<?php declare(strict_types=1);
namespace Plugin\bms;

use JTL\Shop;
use JTL\DB\ReturnType;
use PDO;
use Imagick;

class BmsIndex {
    private $bms;
    private $compressAndDownload=true;
    private $fh;
    private $plugin;
    private $catDataARR = [];
    private $updateType = 'full';
    private $varKombi = 'all';
    private $IndexMerkmalStatus = 'active';
    private $lastKArtikel;
    private $limitLastKArtikel = 0;
    private $limit = 3000;
    private $batchStart = 0;
    private $batchEnd = 0;
    private $batchMaxkArtikel = 0;
    
    private $staffel = 'fix';
    private $JTL_imageSettings = [];
    private $resetSonderpreis = false;
    private $bUeberverkaufAsLager = false;
    private $showAttribute = false;
    private $imgARR = [];
    private $kategorieSichtbarkeit = [];
    private $kategorieRabattARR = [];
    private $tSteuersatzARR = false;
    private $tkundengruppeARR = [];
    private $artikelSichtbarkeit = [];
    private $tmpFilePrefix = 'BMS_';
    private $bms_tmpfname = '';
    private $bms_tmpzipfname = '';
    private ?bool $hasWebPSupport = null;
    private ?bool $hasAvifSupport = null;

    private $lockHandle = null;
    private string $lockFile = '/tmp/bms_index.lock';
    private bool $lockCreated = false;
    private bool $forceUpdate = false;

    public function __construct($pw, &$plugin)
    {
        if (!$this->bms) {
            $this->bms = new Bms();
        }
        $this->checkPW($pw);
        $this->plugin = $plugin;
    }

    public function __destruct()
    {
        @unlink($this->bms_tmpfname);
        @unlink($this->bms_tmpzipfname);
        $this->releaseLock(); // Lock im Destructor aufheben
    }

    private function checkPW($pw)
    {
        //if($pw=="test"){return true;}
        if (strlen(trim($pw)) <= 8) {
            $this->throwError('login 1');
            return false;
        }
        if ($this->bms->getShop('syncPass') == $pw) {
            return true;
        }
        $this->throwError('login 2');
        return false;
    }

    private function throwError($code)
    {
        die('BMS Index Error => ' . $code);
    }

    
    public function setForceUpdate(bool $force = true): void
    {
        $this->forceUpdate = $force;
        if ($force) {
            // evtl. altes Lock brutal entfernen
            if (file_exists($this->lockFile)) {
                @unlink($this->lockFile);
            }
        }
    }

    public function setStaffel($staffel)
    {
        switch($staffel){
            case 'ab':
                $this->staffel = 'ab';
                break;
            case 'fix':
            default:
                $this->staffel = 'fix';
                break;
        }
    }

    private function acquireLock(): bool
    {
        $this->lockHandle = fopen($this->lockFile, 'c'); // c = create falls nicht vorhanden
        if ($this->lockHandle === false) {
            return false;
        }

        if ($this->forceUpdate) {
            // Direkt sperren, alte Sperre überschreiben
            if (flock($this->lockHandle, LOCK_EX)) {
                ftruncate($this->lockHandle, 0);
                fwrite($this->lockHandle, (string)time());
                fflush($this->lockHandle);
                $this->lockCreated = true;
                return true;
            }
        } else {
            // versuchen exklusiv zu sperren, nicht blockierend
            if (flock($this->lockHandle, LOCK_EX | LOCK_NB)) {
                ftruncate($this->lockHandle, 0);
                fwrite($this->lockHandle, (string)time());
                fflush($this->lockHandle);
                $this->lockCreated = true;
                return true;
            }
        }

        return false; // Lock nicht bekommen
    }

    private function releaseLock(): void
    {
        if ($this->lockCreated && $this->lockHandle) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            @unlink($this->lockFile);
            $this->lockCreated = false;
            $this->lockHandle = null;
        }
    }

    private function getLockAge(): int
    {
        if (!is_file($this->lockFile)) {
            return -1; // Kein Lockfile vorhanden
        }

        $content = trim((string)@file_get_contents($this->lockFile));
        if ($content === '' || !ctype_digit($content)) {
            return -2; // Ungültiger Inhalt
        }

        $age = time() - (int)$content;
        return $age >= 0 ? $age : -3; // -3 = Zukunftszeit / kaputte Uhr
    }

    public function setUpdateType($type)
    {
        switch ($type) {
            case 'csvIncremental':
                $this->updateType = 'incremental';
                break;
            case 'CmsNews':
                $this->updateType = 'CmsNews';
                break;
            case 'csvFull':
                $this->updateType = 'full';
                break;

            default:
                $this->throwError('not implemented UpdateType');
                break;
        }
    }
    public function getUpdateType()
    {
        return $this->updateType;
    }

    public function setVarKombi($varKombi)
    {
        switch ($varKombi) {
            case 'parents':
                $this->varKombi = 'parents';
                break;
            case 'childs':
                $this->varKombi = 'childs';
                break;
            case 'all':
            default:
                break;
        }
    }

    public function setIndexMerkmalStatus($status)
    {
        switch ($status) {
            case 'inactive':
                $this->IndexMerkmalStatus = 'inactive';
                break;
            default:
                $this->IndexMerkmalStatus = 'active';
                break;
        }
    }
    public function setLimitLastKArtikel($offset)
    {
        if (is_numeric($offset)) {
            $this->limitLastKArtikel = $offset;
        }
    }
    public function setUeberVerkaufAsLager()
    {
        $this->bUeberverkaufAsLager = true;
    }
    public function setLimit($limit)
    {
        if (is_numeric($limit)) {
            $this->limit = $limit;
        }
    }


    public function resetSonderpreis()
    {
        $this->resetSonderpreis = true;
    }

    public function setShowAttribute()
    {
        $this->showAttribute = true;
    }



    private function getSQLFilterLagerAndVarKombi(){
        $sql = '';
        //1 => alle Artikel
        //2 => nur Lager > 0
        //3 => Lager > 0 oder Ueberverkauf = Y
        switch (Shop::getSettings([CONF_GLOBAL])['global']['artikel_artikelanzeigefilter']) {
            case '2':
                $sql .= 'A.`fLagerbestand` > 0 ';
                break;
            case '3':
                $sql .= ' ( A.`fLagerbestand` > 0 OR A.`cLagerBeachten` = "N" OR A.`cLagerKleinerNull` = "Y") ';
                break;
            case '1':
            default:
                break;
        }


        switch ($this->varKombi) {
            case 'childs':
                if($sql !== ''){
                    $sql .= 'AND ';
                }
                $sql .= ' A.`nIstVater` = 0 ';
                break;
            case 'parents':
                if($sql !== ''){
                    $sql .= 'AND ';
                }
                $sql .= ' A.`kVaterArtikel` = 0 ';
                break;
            case 'all':
            default:
                break;
        }
        return $sql;
    }


    private function getBatchSize(){
        if ($this->updateType == 'incremental') {
            $sql = "select kArtikel from xplugin_bms_update_inc_kArticle order by kArtikel desc limit 0,1";
        }
        else {
            $sql = "select kArtikel from tartikel order by kArtikel desc limit 0,1";
        }
        $batches = Shop::Container()->getDB()->executeQuery($sql, 9);
        $this->batchMaxkArtikel = $batches[0]['kArtikel'];     

        $sql = 'SELECT count(*) as cntLanguages FROM tsprache';
        $result = Shop::Container()->getDB()->executeQuery($sql, 9);
        $cntLanguages = $result[0]['cntLanguages'];
        if($cntLanguages > 0 && $this->limit > 0){
            $this->limit = ceil($this->limit / $cntLanguages);
        }


        $separator = false;

        //getBatchStart + End
        
        $sql = 'SELECT MIN(kArtikel) AS start_kArtikel, MAX(kArtikel) AS end_kArtikel
            FROM (
                SELECT A.kArtikel ';
                if ($this->updateType == 'incremental') {
                    $sql .= 'FROM xplugin_bms_update_inc_kArticle AS B LEFT JOIN tartikel AS A ON A.kArtikel = B.kArtikel ';
                }
                else {
                    $sql .= 'FROM tartikel AS A ';
                }

                $addSQL = $this->getSQLFilterLagerAndVarKombi();
                if($this->limitLastKArtikel > 0){
                    $sql .= 'WHERE A.kArtikel > ' . $this->limitLastKArtikel . ' ';
                    if($addSQL !== ""){
                        $sql .= 'AND ' . $addSQL;
                    }
                }
                elseif($addSQL !== ""){
                    $sql .= 'WHERE ' . $addSQL;
                }
                $sql .= ' ORDER BY A.kArtikel LIMIT ' . $this->limit . '
            ) AS temp;';

        $batches = Shop::Container()->getDB()->executeQuery($sql, 9);

        foreach ($batches as $batch){
            $this->batchStart = $batch['start_kArtikel'];
            $this->batchEnd = $batch['end_kArtikel'];
        }

        if($this->batchMaxkArtikel == $this->batchEnd &&$this->updateType == 'incremental'){
            $sql = 'TRUNCATE TABLE xplugin_bms_update_inc_kArticle ';
            Shop::Container()->getDB()->executeQuery($sql, 3);
        }

        if($this->batchMaxkArtikel == $this->limitLastKArtikel || $this->batchEnd <= 0){
            die('LIMIT||0||' . $this->limit . PHP_EOL);
        }
    }

    private function getkArtikelForUpdate(){
        $sql = 'SELECT 1 FROM xplugin_bms_update_inc_kArticle LIMIT 1';
        $result = Shop::Container()->getDB()->executeQuery($sql, 9);

        if (!empty($result)) {return;} // bereits vorbereitet / noch Daten im Index


        $sql = "
            SELECT 
                EXISTS(SELECT 1 FROM xplugin_bms_update_kArticle LIMIT 1) AS hasUpdate,
                EXISTS(SELECT 1 FROM xplugin_bms_delete_kArticle LIMIT 1) AS hasDelete
        ";
        $result = Shop::Container()->getDB()->executeQuery($sql, 1);

        if (!$result->hasUpdate && !$result->hasDelete) {
            die('EOF|nothingToDo');
        }


        $sql = 'CREATE TEMPORARY TABLE xplugin_bms_tmp_kartikels (kArtikel INT,INDEX idx_kartikel (kArtikel));';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);

        //ist Vater, alle Kinder suchen und updaten:
        $sql = 'INSERT IGNORE INTO xplugin_bms_tmp_kartikels (kArtikel) 
            SELECT kArtikel FROM tartikel WHERE 
            kVaterartikel in (SELECT kArtikel from xplugin_bms_update_kArticle) 
            AND kArtikel NOT IN (SELECT kArtikel from xplugin_bms_update_kArticle)
            ';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);

        $sql = 'INSERT IGNORE INTO xplugin_bms_update_kArticle (kArtikel) 
            SELECT kArtikel FROM xplugin_bms_tmp_kartikels';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);



        //Vater zu Angeboten suchen
        $sql = 'INSERT IGNORE INTO xplugin_bms_tmp_kartikels (kArtikel) 
            SELECT kVaterArtikel FROM tartikel 
            WHERE kVaterArtikel > 0 
            AND kArtikel IN (SELECT kArtikel from xplugin_bms_update_kArticle) 
            GROUP BY kVaterArtikel';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);

        $sql = 'INSERT IGNORE INTO xplugin_bms_update_kArticle (kArtikel) 
            SELECT kArtikel FROM xplugin_bms_tmp_kartikels';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);


        //nochmal, da evtl neue Kinder: ist Vater, alle Kinder suchen und updaten:
        $sql = 'INSERT IGNORE INTO xplugin_bms_tmp_kartikels (kArtikel) 
            SELECT kArtikel FROM tartikel WHERE 
            kVaterartikel in (SELECT kArtikel from xplugin_bms_update_kArticle) 
            AND kArtikel NOT IN (SELECT kArtikel from xplugin_bms_update_kArticle)
            ';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);

        $sql = 'INSERT IGNORE INTO xplugin_bms_update_kArticle (kArtikel) 
            SELECT kArtikel FROM xplugin_bms_tmp_kartikels';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);

        $sql = 'DROP TEMPORARY TABLE xplugin_bms_tmp_kartikels';
        $result = Shop::Container()->getDB()->executeQuery($sql, 3);
        
                
        $sql = "
            INSERT IGNORE INTO xplugin_bms_update_inc_kArticle (kArtikel)
            SELECT A.kArtikel
            FROM xplugin_bms_update_kArticle AS A
            LEFT JOIN tartikelattribut AS B
                ON A.kArtikel = B.kArtikel
            AND B.cName = 'bms_hide'
            WHERE B.kArtikel IS NULL
        ";
        Shop::Container()->getDB()->executeQuery($sql, 3);

           
        $sql = "
            INSERT IGNORE INTO xplugin_bms_delete_kArticle (kArtikel)
            SELECT A.kArtikel
            FROM xplugin_bms_update_kArticle AS A
            LEFT JOIN tartikelattribut AS B
                ON A.kArtikel = B.kArtikel
            AND B.cName = 'bms_hide'
            WHERE B.kArtikel > 0
        ";
        Shop::Container()->getDB()->executeQuery($sql, 3);

        $sql = 'TRUNCATE TABLE xplugin_bms_update_kArticle';
        Shop::Container()->getDB()->executeQuery($sql, 3);

    }

    public function getCSV()
    {

        if (!$this->acquireLock()) {
            if (!$this->forceUpdate) {
                $age = $this->getLockAge();
                if ($age >= 0) {
                    die("locked - is running - time in sek: " . $age);
                }
            }
        }

        if ($this->resetSonderpreis) {
            $sql = 'INSERT IGNORE INTO xplugin_bms_update_kArticle (kArtikel)  
                SELECT kArtikel
                FROM tartikelsonderpreis AS A
                WHERE
                A.dStart = "' . date('Y-m-d', strtotime('yesterday')) . '"
                OR A.dStart = "' . date('Y-m-d', strtotime('today')) . '"
                OR A.dEnde = "' . date('Y-m-d', strtotime('today')) . '"
                ';
            $result = Shop::Container()->getDB()->executeQuery($sql, 3);
        }


        //IncUpdate => Welche Artikel updaten?
        if ($this->updateType == 'incremental') {

            $this->getkArtikelForUpdate(); //TODO => in DB schreiben
            //Aus DB 2000 holen, bis fertig und immer erweitern....
        }

        //BatchSize
        $this->getBatchSize();
        


        

        $sql = 'SELECT * FROM tkategoriesichtbarkeit';
        $result = Shop::Container()->getDB()->executeQuery($sql, 10);
        while ($elem = $result->fetchObject()) {
            $this->kategorieSichtbarkeit[$elem->kKategorie][] = [$elem->kKundengruppe];
        }

        $sql = 'SELECT * FROM tartikelsichtbarkeit';
        $result = Shop::Container()->getDB()->executeQuery($sql, 10);
        while ($elem = $result->fetchObject()) {
            $this->artikelSichtbarkeit[$elem->kArtikel][$elem->kKundengruppe] = true;
        }

        //tkategoriekundengruppe => KategorieRabatt
        $sql = 'select
            B.kArtikel
            , A.fRabatt
            , kKundengruppe
            FROM tkategoriekundengruppe AS A
            LEFT JOIN tkategorieartikel AS B ON A.kKategorie = B.kKategorie';

        $result = Shop::Container()->getDB()->executeQuery($sql, 10);
        while ($elem = $result->fetchObject()) {
            $this->kategorieRabattARR[$elem->kArtikel][$elem->kKundengruppe] = $elem->fRabatt;
        }

        //tkundengruppe => Rabatt auf Kundengruppe, netto/brutto
        $sql = 'SELECT
            kKundengruppe
            , fRabatt
            , nNettoPreise
            , cStandard
            FROM tkundengruppe';


        $result = Shop::Container()->getDB()->executeQuery($sql, 10);
        while ($elem = $result->fetchObject()) {
            $this->tkundengruppeARR[$elem->kKundengruppe]['fRabatt'] = $elem->fRabatt;
            $this->tkundengruppeARR[$elem->kKundengruppe]['nNettoPreise'] = $elem->nNettoPreise;
            if ($elem->cStandard == 'Y') {
                if (key_exists('global_sichtbarkeit', Shop::getSettings([CONF_GLOBAL])['global']) && Shop::getSettings([CONF_GLOBAL])['global']['global_sichtbarkeit'] == '2') {
                    //ToDo Only Show if LoggedIn :s
                }
            }
            $this->tkundengruppeARR[$elem->kKundengruppe]['c'] = $elem->nNettoPreise;
        }

        $this->JTL_imageSettings = Shop::getSettings(CONF_BILDER);

        $headerARR[] = 'kArtikel';
        $headerARR[] = 'cName';
        $headerARR[] = 'cArtNr';
        $headerARR[] = 'kSteuerklasse';
        $headerARR[] = 'fMwSt';
        $headerARR[] = 'cHAN';
        $headerARR[] = 'cBarcode';
        $headerARR[] = 'cHersteller';
        $headerARR[] = 'kHersteller';
        $headerARR[] = 'cSeo';
        $headerARR[] = 'image';
        $headerARR[] = 'cISO';
        $headerARR[] = 'cSuchbegriffe';
        $headerARR[] = 'topartikel';
        $headerARR[] = 'verfuegbarkeit';
        $headerARR[] = 'bestseller';
        $headerARR[] = 'dErstellt';
        $headerARR[] = 'cBeschreibung';
        $headerARR[] = 'cKurzBeschreibung';
        $headerARR[] = 'catIDs';
        $headerARR[] = 'boost';
        $headerARR[] = 'catARR';
        $headerARR[] = 'Merkmal';
        $headerARR[] = 'price';
        $headerARR[] = 'varKombi';
        $headerARR[] = 'fLagerbestand';
        $headerARR[] = 'cISBN';
        $headerARR[] = 'cASIN';
        $headerARR[] = 'artikelsichtbarkeit';
        $headerARR[] = 'VPE';
        $headerARR[] = 'nSort';
        $headerARR[] = 'fMindestbestellmenge';
        $headerARR[] = 'sEigenschaft';
        $headerARR[] = 'bmsSplit';
        $headerARR[] = 'cWarengruppe';
        $headerARR[] = 'kWarengruppe';
        if ($this->showAttribute) {
            $headerARR[] = 'Attribute';
        }
        /*
             $headerARR[] = 'childNames';
             $headerARR[] = 'childArtnr';
             $headerARR[] = 'childHAN';
             */


        $this->createTempFiles();
            
        if ($this->limitLastKArtikel == 0) {
            fwrite($this->fh, '"');
            fwrite($this->fh, implode('"~"', $headerARR));
            fwrite($this->fh, '"' . PHP_EOL);
        }

        if ($this->batchEnd > 0 ) {
            if ($this->limitLastKArtikel == 0) {
                if ($this->updateType == 'full' ){
                    $sql = 'TRUNCATE TABLE xplugin_bms_update_kArticle ';
                    Shop::Container()->getDB()->executeQuery($sql, 3);

                    $sql = 'TRUNCATE TABLE xplugin_bms_delete_kArticle ';
                    Shop::Container()->getDB()->executeQuery($sql, 3);
                }

                //Prepare Preis
                $sql = 'TRUNCATE TABLE xplugin_bms_preis';
                Shop::Container()->getDB()->executeQuery($sql, 3);

                //Prepare SonderPreis
                $sql = 'TRUNCATE TABLE xplugin_bms_sonderpreis';
                Shop::Container()->getDB()->executeQuery($sql, 3);
            }

            
            $sql = 'INSERT INTO xplugin_bms_preis (kArtikel, sPreis) ';
            $sql .= 'SELECT A.kArtikel, GROUP_CONCAT(
                DISTINCT CONCAT (
                                L.kKundengruppe
                                , "_"
                                , ';
                //Günstigster Staffelpreis... evtl wieder raus oder Switch
                //, S.fVKNetto
                if($this->staffel == 'ab'){
                    $sql .= '(SELECT CONCAT(tpd.fVKNetto, "_", tpd.nAnzahlAb) FROM tpreisdetail tpd WHERE tpd.kPreis = L.kPreis ORDER BY tpd.nAnzahlAb DESC LIMIT 1)';
                }
                else {
                    $sql .= '(SELECT CONCAT(tpd.fVKNetto, "_", tpd.nAnzahlAb) FROM tpreisdetail tpd WHERE tpd.kPreis = L.kPreis ORDER BY tpd.nAnzahlAb ASC LIMIT 1)';
                }
                
                                
            $sql .= ') SEPARATOR "||" ) AS priceData ';

            if ($this->updateType == 'full' ){
                $sql .= 'FROM tartikel AS A ';
            } else {
                $sql .= 'FROM xplugin_bms_update_inc_kArticle AS B ';
                $sql .= 'LEFT JOIN tartikel AS A ON A.kArtikel = B.kArtikel ';
            }
            
            //$sql .= 'LEFT JOIN tpreis AS L ON A.kArtikel = L.kArtikel LEFT JOIN tpreisdetail AS S ON L.kPreis = S.kPreis AND S.nAnzahlAb = "0" ';
            $sql .= 'LEFT JOIN tpreis AS L ON A.kArtikel = L.kArtikel ';
            if($this->batchEnd > 0){
                if ($this->updateType == 'full' ){
                    $sql .= ' WHERE A.kArtikel ';
                } else {
                    $sql .= ' WHERE B.kArtikel ';
                }
                
                $sql .= ' BETWEEN ' .  $this->batchStart;
                $sql .= ' AND ' .  $this->batchEnd . ' ';
            }                

            
            
            $addSQL = $this->getSQLFilterLagerAndVarKombi();
            if($addSQL !== ""){
                $sql .= ' AND ' . $this->getSQLFilterLagerAndVarKombi();
            }

            $sql .= ' GROUP BY A.kArtikel ';
            $sql .= 'ON DUPLICATE KEY UPDATE sPreis = VALUES(sPreis)';



            $result = Shop::Container()->getDB()->executeQuery($sql, 3);


                                
            $sql = 'INSERT IGNORE INTO xplugin_bms_sonderpreis (kArtikel, sSonderpreis) ';
            $sql .= 'SELECT A.kArtikel, GROUP_CONCAT(
                DISTINCT CONCAT (O.kKundengruppe, "_", O.fNettoPreis ) SEPARATOR "||"
                ) AS sonderpreisData ';
            
            if ($this->updateType == 'full' ){
                $sql .= 'FROM tartikel AS A ';
            } else {
                $sql .= 'FROM xplugin_bms_update_inc_kArticle AS B LEFT JOIN tartikel AS A ON A.kArtikel = B.kArtikel ';
            }

            $sql .= 'LEFT JOIN tartikelsonderpreis AS N ON A.kArtikel = N.kArtikel
            LEFT JOIN tsonderpreise AS O ON N.kArtikelSonderpreis = O.kArtikelSonderpreis ';
            $sql .= 'WHERE N.cAktiv="Y" ';
            if($this->batchEnd > 0){
                if ($this->updateType == 'full' ){
                    $sql .= ' AND A.kArtikel';
                } else {
                    $sql .= ' AND B.kArtikel';
                }
                $sql .= ' BETWEEN ' .  $this->batchStart;
                $sql .= ' AND ' .  $this->batchEnd . ' ';
            }
            $sql .= 'AND (N.nIstDatum = 0 OR (N.dStart <= "' . date('Y-m-d') . '" AND N.dEnde >= "' . date('Y-m-d') . '")) ';
            $sql .= 'AND (N.nIstAnzahl = 0 OR (N.nAnzahl <= A.fLagerbestand)) ';

            $sql .= 'GROUP BY A.kArtikel';
            Shop::Container()->getDB()->executeQuery($sql, 3);
   
                

            if($this->IndexMerkmalStatus == 'active'){
                if($this->limitLastKArtikel == 0){
                    //Prepare Merkmale
                    $sql = 'TRUNCATE TABLE xplugin_bms_merkmale';
                    Shop::Container()->getDB()->executeQuery($sql, 3);
                    
                    //Prepare Eigenschaften
                    $sql = 'TRUNCATE TABLE xplugin_bms_eigenschaften';
                    Shop::Container()->getDB()->executeQuery($sql, 3);
                }
            }
            

            //Standard zuerst wg Bildern
            $sql = 'SELECT
                kSprache
                , cISO
                , cStandard as cShopStandard
                FROM
                tsprache order by cShopStandard DESC
                ';
            $result = Shop::Container()->getDB()->executeQuery($sql, 9);

            foreach ($result as $language) {

                 //TODO Performance HelperDBs
                //Check Merkmale
                //CheckPriceData
                //CheckSonderpreise
                //=> To DB for Performance Issues!




                $this->prepareMerkmale($language);

                $this->prepareEigenschaften($language);

                $this->getCatData($language);

                // $sql = 'SELECT * FROM tpreis LIMIT 0,5';
                // $result = Shop::Container()->getDB()->executeQuery($sql, 9);
                // echo '<pre>';print_r($result);echo '</pre>';
                // echo '<hr>';
                // $sql = 'SELECT * FROM tpreisdetail LIMIT 0,5';
                // $result = Shop::Container()->getDB()->executeQuery($sql, 9);
                // echo '<pre>';print_r($result);echo '</pre>';
                // die();

                

                Shop::Container()->getDB()->executeQuery('SET SQL_BIG_SELECTS=1', 1);
                $sqlQuery = $this->getItemSQL($language);
                $result = Shop::Container()->getDB()->executeQuery($sqlQuery, 10);
                if($result === false){
                    $this->logMySQLErrorQuery($sqlQuery);            
                }
                else {
                    $counter = 0;
                    //echo "<pre>";
                    while ($elem = $result->fetchObject()) {
                        $tmpData = $this->getCSVItem($elem, $language);
                        if($tmpData !== ''){
                            fwrite($this->fh, $tmpData);
                        } 
                        
                        $counter++;                 
                    }
                }

                //echo "<pre>";print_r($result);
                //die($sql);

            }
        }


        $sql = 'SELECT kArtikel FROM xplugin_bms_delete_kArticle';
        $result = Shop::Container()->getDB()->executeQuery($sql, 10);
        $tmpDataARR = [];
        while ($elem = $result->fetchObject()) {
            fwrite($this->fh, '"' . $elem->kArtikel . '"~"delete"' . PHP_EOL);
        }

        $sql = 'TRUNCATE TABLE xplugin_bms_delete_kArticle ';
        Shop::Container()->getDB()->executeQuery($sql, 3);


        $lastkArtikel = 0;
        if ($this->batchMaxkArtikel !== 0 && $this->lastKArtikel !== $this->batchMaxkArtikel) {
            $lastkArtikel = $this->lastKArtikel;
        }
        fwrite($this->fh, 'LIMIT||' . $lastkArtikel . '||' . $this->limit . PHP_EOL);

        //Waehrung
        $sql = 'SELECT 
            kWaehrung,cISO,cName,cNameHTML,fFaktor,cStandard,cVorBetrag,cTrennzeichenCent,cTrennzeichenTausend 
            FROM twaehrung';
        $result = Shop::Container()->getDB()->executeQuery($sql, 10);
        $tmpDataARR = [];
        while ($elem = $result->fetchObject()) {
            $tmpDataARR[] = implode('_',(array) $elem);
        }
        fwrite($this->fh, 'CURR||' . implode('||',$tmpDataARR) . PHP_EOL);

        $template = Shop::Container()->getTemplateService()->getActiveTemplate()->cParent;
        if($template == ''){
            $template = Shop::Container()->getTemplateService()->getActiveTemplate()->cTemplate;
        }
        if($template){
            $template = strtolower($template);
        }
        
        $version = "";
        if(defined('APPLICATION_VERSION')){
            $version = APPLICATION_VERSION;
        }
        else if(defined('JTL_VERSION')){
            $version = JTL_VERSION;
        }
        fwrite($this->fh, 'EOF||' . $version . '||' . $this->plugin->getMeta()->getVersion() . '||' . $template);


        fclose($this->fh);
        //echo '<hr>'.filesize($bms_tmpfname).'<hr>';
        
        $this->readOutput();

        return true;
    }

    private function logMySQLErrorQuery($sqlQuery){
        $errorInfo = Shop::Container()->getDB()->getPDO()->errorInfo();
        //log query if errors $sqlQuery
        try {
            $logger = \method_exists($this->plugin, 'getLogger') // ab JTL-Shop 5.2.0
                ? $this->plugin->getLogger()
                : Shop::Container()->getLogService();
            $logger->error(
                'MySQL Error: {msg} (SQLSTATE {sqlstate}, Code {code}) in query: {query}',
                [
                    'sqlstate' => $errorInfo[0] ?? 'n/a',
                    'code'     => $errorInfo[1] ?? 'n/a',
                    'msg'      => $errorInfo[2] ?? 'Unknown error',
                    'query'    => $sqlQuery,
                    'plgn'     => $this->plugin->getPluginID(),
                ]
            );
        } catch (Exception $e) {
        }        
    }

    private function createTempFiles(){
        $this->removeOldTempFiles();
        
        $this->bms_tmpfname = tempnam(sys_get_temp_dir(), $this->tmpFilePrefix);
        $this->fh = @fopen($this->bms_tmpfname, 'w+');
        if ($this->fh == false) {
            $fallbackTmpDir = PFAD_ROOT . PFAD_COMPILEDIR;
            @unlink($this->bms_tmpfname);
            $this->bms_tmpfname = tempnam($fallbackTmpDir, $this->tmpFilePrefix);
            $this->fh = @fopen($this->bms_tmpfname, 'w+');
            if ($this->fh == false) {
                die('fh2 not writable: ' . $this->fh . ' - ' . $this->bms_tmpfname);
            }
        }
    }
    private function removeOldTempFiles(){
        $tempDir = sys_get_temp_dir();
        $files = scandir($tempDir);
        $maxAge = 1800; // 30 minutes in seconds
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $tempDir . DIRECTORY_SEPARATOR . $file;
                // Prüfen, ob es sich um eine Datei handelt
                if (is_file($filePath)) {
                    // Überprüfen, ob der Dateiname mit dem Präfix "BMS_" beginnt
                    if (strpos($file, $this->tmpFilePrefix) === 0) {
                        // Änderungsdatum der Datei abrufen
                        $fileModTime = filemtime($filePath);
                        // Aktuelle Zeit
                        $currentTime = time();                        
                        // Prüfen, ob die Datei älter als 30 Minuten ist
                        if (($currentTime - $fileModTime) > $maxAge) {
                            // Datei löschen, wenn sie älter als 30 Minuten ist
                            @unlink($filePath);
                        }
                    }
                }
            }
        }
    }

    private function readOutput(){
        //TODO Compress CMS News
        
        if($this->compressAndDownload){
            $this->bms_tmpzipfname = tempnam(sys_get_temp_dir(), 'BMS_');
            $fp = @gzopen($this->bms_tmpzipfname, 'wb');
            //die($bms_tmpzipfname);
            if ($fp == false) {
                $fallbackTmpDir = PFAD_ROOT . PFAD_COMPILEDIR;
                $this->bms_tmpzipfname = tempnam($fallbackTmpDir, 'BMS_');
                $fp = @gzopen($this->bms_tmpzipfname, 'wb');
                if ($fp == false) {
                    die('fp not writable: ' . $fp . ' - ' . $fallbackTmpDir);
                }
            }

            // Compress the file
            if ($fp_in = fopen($this->bms_tmpfname, 'rb')) {
                while (!feof($fp_in)) {
                    gzwrite($fp, fread($fp_in, 1024 * 512));
                }
                fclose($fp_in);
            }


            // Close the gz file and we're done
            gzclose($fp);
            //readfile($bms_tmpfname);
            //echo '<hr>'.$bms_tmpfname.'<hr>';
            //echo '<hr>'.$bms_tmpzipfname.'<hr>';
            //die();
            //echo '<hr>'.filesize($bms_tmpfname).'<hr>';
            unlink($this->bms_tmpfname);

            //echo '<hr>'.filesize($bms_tmpzipfname).'<hr>';/tmp/systemd-private-fb65969a9e09428c896fb6e45eb36df2-apache2.service-Y8a7Sf/tmp/
            //echo '<hr>'.$bms_tmpzipfname.'<hr>';die();
            //header('Content-Encoding: gzip');
            //header('Content-Type: application/gzip');
            //die();

            //Workarround for Cloudflare
            header('Content-Type: application/octet-stream');
            header('Content-Encoding: gzip');
            header('Cache-Control: no-transform');
            //Workarround for Cloudflare
            
            readfile($this->bms_tmpzipfname);

            unlink($this->bms_tmpzipfname);
        }
        else {
            readfile($this->bms_tmpfname);
            unlink($this->bms_tmpfname);
        }
    }

    private function getCatData($languageARR)
    {
        $sql = 'SELECT kKategorie, cWert, cName FROM tkategorieattribut where cName LIKE "bms_%"';
        $res = Shop::Container()->getDB()->queryPrepared(
            $sql,
            [],
            ReturnType::QUERYSINGLE
        );
        $catAttrARR = [];
        while (($elem = $res->fetch(PDO::FETCH_OBJ)) !== false) {
            //print_r($tmpCat);
            $catAttrARR[$elem->kKategorie][$elem->cName] = $elem->cWert;
        }


        $sql = 'SELECT
            A.kKategorie
            , COALESCE(B.cName, A.cName) AS cName
            , nSort
            , kOberKategorie
            FROM tkategorie AS A
            LEFT JOIN tkategoriesprache AS B ON A.kKategorie = B.kKategorie AND B.kSprache = :kSprache';
        $res = Shop::Container()->getDB()->queryPrepared(
            $sql,
            [
                'kSprache' => $languageARR['kSprache']
            ],
            ReturnType::QUERYSINGLE
        );
        $tmpCatARR = [];
        while (($elem = $res->fetch(PDO::FETCH_OBJ)) !== false) {
            //print_r($tmpCat);
            $tmpCatARR[$elem->kKategorie]['cName'] = $elem->cName;
            $tmpCatARR[$elem->kKategorie]['nSort'] = $elem->nSort;
            $tmpCatARR[$elem->kKategorie]['kOberKategorie'] = $elem->kOberKategorie;
            if (key_exists($elem->kKategorie, $catAttrARR)) {
                //print_r($catAttrARR[$elem->kKategorie] );
                //die("TREFFER");
                if (key_exists('bms_boost', $catAttrARR[$elem->kKategorie])) {
                    $tmpCatARR[$elem->kKategorie]['bms_boost'] = (int) $catAttrARR[$elem->kKategorie]['bms_boost'];
                }

                if (key_exists('bms_split', $catAttrARR[$elem->kKategorie])) {
                    $tmpCatARR[$elem->kKategorie]['bms_split'] = (int) $catAttrARR[$elem->kKategorie]['bms_split'];
                }

                if (key_exists('bms_hide', $catAttrARR[$elem->kKategorie])) {
                    $tmpCatARR[$elem->kKategorie]['bms'] = 'hide';
                } else if (key_exists('bms_ignoreHide', $catAttrARR[$elem->kKategorie])) {
                    $tmpCatARR[$elem->kKategorie]['bms'] = 'ignoreHide';
                } else if (key_exists('bms_ignoreBoost', $catAttrARR[$elem->kKategorie])) {
                    $tmpCatARR[$elem->kKategorie]['bms'] = 'ignoreBoost';
                }
            }
        }
        unset($catAttrARR);

        $this->catDataARR = [];
        foreach ($tmpCatARR as $catID => $tmpCat) {
            $contentARR = [];
            $bmsFlag = false;
            $contentARR['bms'] = '';
            if (key_exists('bms', $tmpCat)) {
                $contentARR['bms'] = $tmpCat['bms'];
                $bmsFlag = true;
            }
            if (key_exists('bms_boost', $tmpCat)) {
                $contentARR['bms_boost'] = $tmpCat['bms_boost'];
            }

            $contentARR['title'] = $tmpCat['nSort'] . '_' . $tmpCat['cName'] . '_' . $catID;


            if ($tmpCat['kOberKategorie'] > 0) {

                $this->getParent($tmpCat['kOberKategorie'], $contentARR, 0, $tmpCatARR, $bmsFlag);
            }


            $this->catDataARR[$catID] = $contentARR;
        }
        unset($tmpCatARR);
    }

    private function getCSVItem($elem, $language)
    {
        $item = '"';
        $item .= $this->CSV_elem($elem->kArtikel) . '"~"';
        $this->lastKArtikel = $elem->kArtikel;
        $item .= $this->CSV_elem($elem->cName) . '"~"';
        $item .= $this->CSV_elem($elem->cArtNr) . '"~"';
        $item .= $this->CSV_elem($elem->kSteuerklasse) . '"~"';
        $item .= $this->CSV_elem($elem->fMwSt) . '"~"';
        $item .= $this->CSV_elem($elem->cHAN) . '"~"';
        $item .= $this->CSV_elem($elem->cBarcode) . '"~"';
        $item .= $this->CSV_elem($elem->cHersteller) . '"~"';
        $item .= $this->CSV_elem($elem->kHersteller) . '"~"';
        $item .= $this->CSV_elem($elem->cSeo) . '"~"';
        $image = $this->getPictureLink($elem);
        $item .= $this->CSV_elem($image) . '"~"';
        $item .= $this->CSV_elem($language['cISO']) . '"~"';
        $item .= $this->CSV_elem($elem->cSuchbegriffe) . '"~"';

        if ($elem->cTopArtikel == 'Y') {
            $item .= 'Y"~"';
        } else {
            $item .= 'N"~"';
        }
        
        $item .= $this->CSV_elem($elem->BMSLieferzeit) . '"~"';
        
        if ($elem->isBestseller > 0) {
            //key_exists('global_bestseller_minanzahl', Shop::getSettings([CONF_GLOBAL])['global']) && $elem->bestseller >= Shop::getSettings([CONF_GLOBAL])['global']['global_bestseller_minanzahl']) {
            $item .= $this->CSV_elem($elem->bestseller) . '"~"';
        } else {
            $item .= '0"~"';
        }


        $item .= $this->CSV_elem($elem->dErstellt) . '"~"';

        $elem->cBeschreibung = strip_tags($elem->cBeschreibung ?? '');
        if (strlen($elem->cBeschreibung) > 5000) {
            $elem->cBeschreibung = preg_replace('/^(.{0,5000}[^ ]*).*/ims', '\\1...', $elem->cBeschreibung);
        }
        $item .= $this->CSV_elem($elem->cBeschreibung) . '"~"';

        $elem->cKurzBeschreibung = strip_tags($elem->cKurzBeschreibung ?? '');
        if (strlen($elem->cKurzBeschreibung) > 1000) {
            $elem->cKurzBeschreibung = preg_replace('/^(.{0,1000}[^ ]*).*/ims', '\\1...', $elem->cKurzBeschreibung);
        }
        $item .= $this->CSV_elem($elem->cKurzBeschreibung) . '"~"';
        $item .= $this->CSV_elem($this->getParentCatIDs($elem->catIDs, $this->catDataARR)) . '"~"';
        $catCSV = '';
        $catIDARR = [];
        $boost = '';
        $split = '';
        if( $elem->catIDs){
            $catIDARR = explode(',', $elem->catIDs);
            $i = 0;
            foreach ($catIDARR as $catID) {

                if (
                    key_exists($catID, $this->catDataARR)
                    &&
                    key_exists('bms', $this->catDataARR[$catID])
                ) {
                    if ($this->catDataARR[$catID]['bms'] == 'hide') {
                        return "";
                    }
                }

                if (
                    key_exists($catID, $this->catDataARR)
                    &&
                    key_exists('bms_boost', $this->catDataARR[$catID])
                    &&
                    is_numeric($this->catDataARR[$catID]['bms_boost'])
                ) {
                    $boost = $this->catDataARR[$catID]['bms_boost'];
                }


                if (
                    key_exists($catID, $this->catDataARR)
                    &&
                    key_exists('bms_split', $this->catDataARR[$catID])
                ) {
                    $split = $this->catDataARR[$catID]['bms_split'];
                }




                if (key_exists($catID, $this->kategorieSichtbarkeit)) {
                    foreach ($this->kategorieSichtbarkeit[$catID] as $kKundengruppe) {
                        if (is_numeric($kKundengruppe)){
                            $this->artikelSichtbarkeit[$elem->kArtikel][$kKundengruppe] = true;
                        }
                    }
                }
                if ($i > 0) {
                    $catCSV .= '#';
                }
                if (key_exists($catID, $this->catDataARR)) {
                    $catCSV .= $this->catDataARR[$catID]['title'];
                    $i++;
                }
                //$item .= 'BLUBB';
            }
        }
        //TODO ArtikelBoost???artikelattribute
        if (is_numeric($elem->bmsBoost)) {
            if (is_numeric($boost)){
                $boost += $elem->bmsBoost;
            }
            else {
                $boost = $elem->bmsBoost;
            }
        }
        $item .= $boost . '"~"';
        $item .= $this->CSV_elem($catCSV) . '"~"';
        $item .= $this->CSV_elem($elem->Merkmal) . '"~"';


        $item .= $this->CSV_elem($this->getPriceData($elem)) . '"~"';

        $varKombData = $elem->kVaterArtikel . '_' . $elem->nIstVater;

        $item .= $this->CSV_elem($varKombData) . '"~"';

        $item .= $this->CSV_elem($elem->fLagerbestand) . '"~"';

        $item .= $this->CSV_elem($elem->cISBN) . '"~"';

        $item .= $this->CSV_elem($elem->cASIN) . '"~"';

        //
        if (key_exists($elem->kArtikel, $this->artikelSichtbarkeit)) {
            $ii = 0;
            foreach ($this->artikelSichtbarkeit[$elem->kArtikel] as $kKundengruppe => $elem2) {
                if ($ii !== 0) {
                    $item .= ',';
                }
                $item .= $kKundengruppe;
                $ii++;
            }
        }
        $item .= '"~"';
        if ($elem->cVPE !== 'N' && $elem->fVPEWert > 0 && $elem->cVPEEinheit !== '' && $elem->cVPEEinheit !== '0') {
            $item .= $this->CSV_elem($elem->fVPEWert) . '||' . $this->CSV_elem($elem->cVPEEinheit);
        }
        $item .= '"~"';

        $item .= $this->CSV_elem($elem->nSort) . '"~"';


        $item .= $this->CSV_elem($elem->fMindestbestellmenge) . '"~"';
        $item .= $this->CSV_elem($elem->sEigenschaft) . '"~"';
        if($elem->bmsSplit != ''){
            if($elem->bmsSplit == 'NONE') {
                $split = '';
            }
            else {
                $split=$elem->bmsSplit;
            }
        }
        $item .= $this->CSV_elem($split) . '"~"';


        $item .= $this->CSV_elem($elem->cWarengruppe) . '"~"';
        $item .= $this->CSV_elem($elem->kWarengruppe) . '"';

        if ($this->showAttribute) {
            $item .= '~"';
            $item .= $this->CSV_elem($elem->attributeData) . '"';
        }

        /*
             $item .= $this->CSV_elem($elem->childNames) . '"~"';
             $item .= $this->CSV_elem($elem->childArtnr) . '"~"';
             $item .= $this->CSV_elem($elem->childHAN) . ''';
             */
        //echo '\n\n$item\n\n';
        $item .= PHP_EOL;
        return $item;
    }

    private function getParent($kKategorie, &$contentARR, $i, $tmpCatARR, $bmsFlag)
    {
        $i++;

        if ($i > 10) {
            return false;
        } else {
            if (key_exists($kKategorie, $tmpCatARR)) {
                if (!$bmsFlag && key_exists('bms', $tmpCatARR[$kKategorie])) {
                    $contentARR['bms'] = $tmpCatARR[$kKategorie]['bms'];
                    $bmsFlag = true;
                }
                if (key_exists('bms_boost', $tmpCatARR[$kKategorie]) && $contentARR['bms'] !== 'ignoreBoost') {
                    $contentARR['bms_boost'] = +$tmpCatARR[$kKategorie]['bms_boost'];
                }



                $contentARR['title'] = $tmpCatARR[$kKategorie]['nSort'] . '_' . $tmpCatARR[$kKategorie]['cName'] . '_' . $kKategorie . '|' . $contentARR['title'];
                if ($tmpCatARR[$kKategorie]['kOberKategorie'] > 0) {
                    $this->getParent($tmpCatARR[$kKategorie]['kOberKategorie'], $contentARR, $i, $tmpCatARR, $bmsFlag);
                }
            }
        }
        //return $content;
    }

    private function CSV_elem($elem)
    {
        if (!isset($elem) || $elem == null) {
            return '';
        }
        if ($elem == '' || is_numeric($elem)) {
            return $elem;
        }
        $elem = trim($elem);

        if (mb_internal_encoding() !== 'UTF-8') {
            if (version_compare(PHP_VERSION, '8.2.0') >= 0) {  
                $elem = mb_convert_encoding($elem, 'UTF-8', 'UTF-8');
            } else {
                $elem = utf8_encode($elem);
            }
        }

        if (strpos($elem, '&') !== false) {
            $elem = html_entity_decode($elem, ENT_COMPAT);
        }

        if (strpos($elem, '\\') !== false || 
            strpos($elem, '\'') !== false || 
            strpos($elem, '"') !== false || 
            strpos($elem, "\0") !== false) {
                $elem = addslashes($elem);
        }
        
        return $elem;
    }

    private function getPictureLink(&$elem)
    {
        if(key_exists($elem->kArtikel, $this->imgARR)){
            return $this->imgARR[$elem->kArtikel];
        }

        if ($elem->imageNR !== '') {


            $dbImageNr = $elem->imageNR;

            //Get first Image!
            if (is_numeric($dbImageNr) && $dbImageNr > 0) {
                switch ($this->JTL_imageSettings['bilder']['bilder_artikel_namen']) {
                    case 0:
                        $imageName = $elem->kArtikel;
                        break;
                    case 1:
                        $imageName = $elem->cArtNr;
                        break;
                    case 2:
                        $imageName = empty($elem->cSeo) ? $elem->cName : $elem->cSeo;
                        break;
                    case 3:
                        $imageName = sprintf('%s_%s', $elem->cArtNr, empty($elem->cSeo) ? $elem->cName : $elem->cSeo);
                        break;
                    case 4:
                        $imageName = $elem->cBarcode;
                        break;
                }

                $imageName = strtolower($imageName);

                $replacePairs = [
                    '.' => '-',
                    ' ' => '-',
                    '.' => '-',
                    '/' => '-',
                    'ä' => 'ae', 
                    'ö' => 'oe', 
                    'ü' => 'ue', 
                    'ß' => 'ss',
                    'Ä' => 'Ae', 
                    // ISO-8859-1 Kodierungen für Sonderzeichen
                    "\xe4" => 'ae', 
                    "\xf6" => 'oe', 
                    "\xfc" => 'ue', 
                    "\xdf" => 'ss',
                    "\xc4" => 'Ae', 
                    "\xd6" => 'Oe', 
                    "\xdc" => 'Ue'
                ];

                $imageName = strtr($imageName,$replacePairs);

                $imageName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $imageName);

                $dirMD = PFAD_MEDIA_IMAGE . 'product/' . $elem->kArtikel . '/md';

                $newImage = '';
                $newImage .= $dirMD . '/' . $imageName;
                if ($dbImageNr > 1) {
                    $newImage .= '~' . $dbImageNr;
                }

                $fileEnding = strtolower($this->JTL_imageSettings['bilder']['bilder_dateiformat']);
                if ($fileEnding == 'auto' || $fileEnding == 'auto_webp'){
                    $fileEnding = 'jpg';
                    if($this->hasWebPSupport()){
                        $fileEnding = 'webp';
                    }
                }
                elseif ($fileEnding == 'auto_avif'){
                    $fileEnding = 'jpg';
                    if($this->hasAvifSupport()){
                        $fileEnding = 'avif';
                    }
                }

                $newImage .= '.' . $fileEnding;
                if (0 && !file_exists($newImage) && $elem->kVaterArtikel > 0) {
                    $dirMD = PFAD_MEDIA_IMAGE . 'product/' . $elem->kVaterArtikel . '/md';
                    $imageName .= '-' . $elem->kArtikel;
                    $newImage = '';
                    $newImage .= $dirMD . '/' . $imageName;
                    if ($dbImageNr > 1) {
                        $newImage .= '~' . $dbImageNr;
                    }

                   
                    $newImage .= '.' . $fileEnding;
                }
                $this->imgARR[$elem->kArtikel] = $newImage;
                return $this->imgARR[$elem->kArtikel];                
            } else {
                return '';
            }
        }
        return '';
    }

    private function getParentCatIDs($catList)
    {
        if (!$catList || $catList == '') {
            return '';
        }

        $tmpListARR = explode(',', $catList);
        foreach ($tmpListARR as $elem) {
            if (key_exists($elem, $this->catDataARR)) {
                preg_match_all('/_([0-9]+)/', $this->catDataARR[$elem]['title'], $match);

                foreach ($match[1] as $newCatID) {

                    $tmpListARR[] = $newCatID;
                }
                //die($catDataARR[$elem]);
            }
        }
        $tmpListARR = array_unique($tmpListARR);
        return implode(',', $tmpListARR);
    }

    private function getPriceData(&$elem)
    {
        $tmpPriceARR = [];
        $preisARR = explode('||', $elem->priceData ?? '');  // Kundengruppe_VKNetto

        foreach ($preisARR as $elem2) {
            if ($elem2 !== '') {
                $tmpData = explode('_', $elem2);
                $kKundengruppe = $tmpData[0];
                $fVK = (float) $tmpData[1];
                $nAnzahlAb = (int) $tmpData[2];

                $kundenGruppenRabatt = $this->tkundengruppeARR[$kKundengruppe]['fRabatt'] ?? 0;
                $kategorieRabatt = $this->kategorieRabattARR[$elem->kArtikel][$kKundengruppe] ?? 0;

                $newPrice = $fVK;
                if ($kategorieRabatt > 0 || $kundenGruppenRabatt > 0) {
                    $rabatt = max($kategorieRabatt, $kundenGruppenRabatt);
                    $tmpPriceARR[$kKundengruppe]['sp'] = 'true';
                    $newPrice = $fVK - ($fVK / 100 * $rabatt);
                }

                $newPrice = $newPrice * 100;
                $tmpPriceARR[$kKundengruppe]['price'] = $newPrice;
                if($nAnzahlAb > 0){
                    $tmpPriceARR[$kKundengruppe]['iStaffelAb'] = $nAnzahlAb;
                }
            }
        }


        if ($elem->sonderpreisData) {
            $sonderpreisARR = explode('||', $elem->sonderpreisData ?? '');  // Kundengruppe_VKNetto
            if ($sonderpreisARR[0] !== '') {
                foreach ($sonderpreisARR as $elem2) {
                    $tmpData = explode('_', $elem2);
                    $kKundengruppe = $tmpData[0];
                    $fVK = (float) $tmpData[1];

                    $newPrice = $fVK * 100;
                    if (isset($tmpPriceARR[$kKundengruppe]['price']) && $newPrice < $tmpPriceARR[$kKundengruppe]['price']) {
                        $tmpPriceARR[$kKundengruppe]['price'] = $newPrice;
                        $tmpPriceARR[$kKundengruppe]['sp'] = 'true';
                    }
                }
            }
        }

        $data = '';
        foreach ($tmpPriceARR as $kKundengruppe => $price) {
            if ($data !== '') {
                $data .= ',';
            }
            $data .= $kKundengruppe . '_' . $price['price'];
            if (isset($price['sp']) && $price['sp'] == 'true') {
                $data .= '_SP';
            }
            if (isset($price['iStaffelAb']) && $price['iStaffelAb']>0) {
                $data .= '_ST-' . $price['iStaffelAb'];
            }
        }
        return $data;
    }


    /**
     * @return string
     */
    private static function getImageDriver(): string
    {
        return \extension_loaded('imagick') && !\FORCE_IMAGEDRIVER_GD ? 'imagick' : 'gd';
    }

    /**
     * @return bool
     */
    private function hasWebPSupport(): bool
    {
        if ($this->hasWebPSupport === null) {
            $this->hasWebPSupport = self::getImageDriver() == 'imagick'
                ? \count(\Imagick::queryFormats('WEBP')) > 0
                : (\gd_info()['WebP Support'] ?? false);
        }

        return $this->hasWebPSupport;
    }

    /**
     * @return bool
     */
    private function hasAvifSupport(): bool
    {
        if ($this->hasAvifSupport === null) {
            $this->hasAvifSupport = self::getImageDriver() == 'imagick'
                ? \count(\Imagick::queryFormats('AVIF')) > 0
                : (\gd_info()['AVIF Support'] ?? false);
        }

        return $this->hasAvifSupport;
    }



    
    public function getCmsNewsCSV() {
        $this->createTempFiles();
        
        //get News
        //get CMS
        //
        // each language own index
        $sql = "SELECT * FROM tsprache ORDER BY cStandard DESC";
        $sprachen = Shop::Container()->getDB()->executeQuery($sql, 9);
        
        $headerARR[] = 'type';
        $headerARR[] = 'kArtikel';
        $headerARR[] = 'cTitle';
        $headerARR[] = 'cContent';
        $headerARR[] = 'cSeo';
        $headerARR[] = 'cISO';
        $headerARR[] = 'cKundengruppen';
        $headerARR[] = 'dGueltigVon';
        $headerARR[] = 'dGueltigBis';
        $headerARR[] = 'cPreviewImage';
        
        
        
        fwrite($this->fh, '"');
        fwrite($this->fh, implode('"~"', $headerARR));
        fwrite($this->fh, '"' . PHP_EOL);

        foreach ( $sprachen as $sprache ) {   
            $sql = "SELECT A.kLink, A.cTitle, A.cContent, A.cSeo, B.cKundengruppen
            FROM tlinksprache AS A
            left join tlink AS B ON A.kLink = B.kLink
            WHERE cISOSprache = '" . $sprache["cISO"] . "'
            AND A.ctitle != '' ";
            //$sql .= "AND nLinkart = '1'";
            $sql .= "AND nLinkart != '25' ";
            $sql .= "AND bIsActive = '1' ";


            
            $result = Shop::Container()->getDB()->executeQuery($sql, 10);
            while ($elem = $result->fetchObject()) {
                $tmpData = $this->getCMSCSV($elem, $sprache["cISO"]);
                if($tmpData !== ''){
                    fwrite($this->fh, $tmpData);
                }
            }
            
            
          
            $sql = 'SELECT 
            A.title,
            A.content,
            A.languageCode,
            B.kNews,
            B.cKundengruppe,
            B.dGueltigVon,
            B.cPreviewImage 
            FROM tnewssprache AS A LEFT JOIN tnews AS B 
            ON A.kNews=B.kNews 
            WHERE 
            A.languageCode = "' .  $sprache ["cISO"]  . '" 
            AND B.nAktiv = "1" 
            ';

            $result = Shop::Container()->getDB()->executeQuery($sql, 10);
            while ($elem = $result->fetchObject()) {
                $tmpData = $this->getNewsCSV($elem, $sprache["cISO"]);
                if($tmpData !== ''){
                    fwrite($this->fh, $tmpData);
                }
            }
            
        }
        
        fwrite($this->fh, 'EOF|CMS_NEWS');
        fclose($this->fh);
        //echo '<hr>'.filesize($bms_tmpfname).'<hr>';
        
        $this->readOutput();
    }
    
    private function getCMSCSV($data, $cISOSprache){
        $CSV = '';
        $CSV .= '"cms"~"';
        $CSV .= '99999999' . $this->CSV_elem($data->kLink) . '"~"';//kArtikel/ID must be numeric.... prevent sameID CMS<=>News
        $CSV .= $this->CSV_elem($data->cTitle) . '"~"';
        $CSV .= $this->CSV_elem($data->cContent) . '"~"';
        $CSV .= $this->CSV_elem($data->cSeo) . '"~"';
        $CSV .= $this->CSV_elem($cISOSprache) . '"~"';
        $CSV .= $this->CSV_elem($data->cKundengruppen) . '"~"';
        $CSV .= '0000-00-00 00:00:00"~"';
        $CSV .= '0000-00-00 00:00:00"~"';
        $CSV .= $this->CSV_elem($data->cPreviewImage) . '"';
        $CSV .= PHP_EOL;
        return $CSV;
    }
    
    private function getNewsCSV($elem, $cISOSprache){
        $CSV = '';
        $CSV .= '"news"~"';
        $CSV .= '88888888' . $this->CSV_elem($elem->kNews) . '"~"';//kArtikel/ID must be numeric.... prevent sameID CMS<=>News
        $CSV .= $this->CSV_elem($elem->title) . '"~"';
        $CSV .= $this->CSV_elem($elem->content) . '"~"';
        $CSV .= $this->CSV_elem($elem->cSeo) . '"~"';
        $CSV .= $this->CSV_elem($elem->languageCode) . '"~"';
        $CSV .= $this->CSV_elem($elem->cKundengruppe) . '"~"';
        $CSV .= $this->CSV_elem($elem->dGueltigVon) . '"~"';
        $CSV .= '0000-00-00 00:00:00"~"';
        $CSV .= '"';
        $CSV .= PHP_EOL;
        
        return $CSV;
    }

    private function prepareMerkmale($language){
        if($this->IndexMerkmalStatus == 'active'){

            //echo $sql."<hr>";

            $sql = 'INSERT IGNORE INTO xplugin_bms_merkmale (kArtikel, kSprache, sMerkmal) ';
            $sql .= 'SELECT E.kArtikel, ' . $language['kSprache'] . ' AS kSprache, GROUP_CONCAT(DISTINCT
            CONCAT (
                    
                    LPAD(F.nSort,3,"0"), "_"
                    , COALESCE(J.cName, F.cName)
                    , "_"
                    , F.cTyp
                    , "_"
                    , REPLACE(E.kMerkmal,"_","")
                    , "=="
                    , LPAD(G.nSort,3,"0")
                    , "_"
                    , REPLACE(H.cWert,"_","")
                    , "_"
                    , E.`kMerkmalWert`
                    )
            
            SEPARATOR "||"
            ) AS Merkmal ';
            $sql .= 'FROM ';

            if ($this->updateType == 'full' ){
                $sql .= 'tartikelmerkmal AS E ';
            } else {
                $sql .= 'xplugin_bms_update_inc_kArticle AS A LEFT JOIN tartikelmerkmal AS E ON A.kArtikel = E.kArtikel ';
            }
            
            
            $sql .= 'LEFT JOIN tmerkmal AS F ON E.kMerkmal = F.kMerkmal
            LEFT JOIN tmerkmalwert AS G ON E.kMerkmalWert = G.kMerkmalWert
            LEFT JOIN tmerkmalwertsprache AS H ON E.kMerkmalWert = H.kMerkmalWert AND H.kSprache = ' . $language['kSprache'] . '
            LEFT JOIN tmerkmalsprache AS J ON F.kMerkmal = J.kMerkmal AND J.kSprache = ' . $language['kSprache'] . ' ';

            if($this->batchEnd > 0){
                if ($this->updateType == 'full' ){
                    $sql .= ' WHERE E.kArtikel ';
                }
                else {
                    $sql .= ' WHERE A.kArtikel ';
                }
                $sql .= 'BETWEEN ' .  $this->batchStart;
                $sql .= ' AND ' .  $this->batchEnd . ' ';
            }
            $sql .= 'GROUP BY E.kArtikel';
            Shop::Container()->getDB()->executeQuery($sql, 3);
        }
    }
        

    private function prepareEigenschaften($language){

        //echo $sql."<hr>";

        $sql = 'INSERT IGNORE INTO xplugin_bms_eigenschaften (kArtikel, kSprache, sEigenschaft) ';
        $sql .= 'SELECT A.kArtikel, ' . $language['kSprache'] . ' AS kSprache, GROUP_CONCAT(DISTINCT
            CONCAT (COALESCE(D.cName, C.cName), "__", COALESCE(F.cName, E.cName))        
            SEPARATOR "||"
            ) AS sEigenschaft ';
            if ($this->updateType == 'full' ){
                $sql .= 'FROM tartikel AS A ';
            } else {
                $sql .= 'FROM xplugin_bms_update_inc_kArticle AS G LEFT JOIN tartikel AS A ON G.kArtikel = A.kArtikel ';
            }
        
        $sql .= '  
            LEFT JOIN teigenschaftkombiwert AS B ON A.kEigenschaftKombi = B.kEigenschaftKombi 
            LEFT JOIN teigenschaft AS C ON B.kEigenschaft = C.kEigenschaft  
            LEFT JOIN teigenschaftsprache AS D ON B.kEigenschaft = D.kEigenschaft AND D.kSprache=' . $language['kSprache'] . ' 
            LEFT JOIN teigenschaftwert AS E ON B.kEigenschaftWert = E.kEigenschaftWert  
            LEFT JOIN teigenschaftwertsprache AS F ON B.kEigenschaftWert = F.kEigenschaftWert AND F.kSprache=' . $language['kSprache'] . '   

            WHERE C.cWaehlbar = "Y" AND C.cTyp != "PFLICHT-FREIFELD" AND C.cTyp != "FREIFELD"';
            if($this->batchEnd > 0){
                if ($this->updateType == 'full' ){
                    $sql .= ' AND A.kArtikel ';
                }
                else {
                    $sql .= ' AND G.kArtikel ';
                }
                $sql .= 'BETWEEN ' .  $this->batchStart;
                $sql .= ' AND ' .  $this->batchEnd . ' ';
            }
            $sql .= 'Group by B.kEigenschaftKombi';
            //die($sql);
        Shop::Container()->getDB()->executeQuery($sql, 3);
    }

    private function getItemSQL($language){
        $sql = 'SELECT A.kArtikel ';

        if ($language['cShopStandard'] !== 'Y') {
            $sql .= ', COALESCE(I.cName, A.`cName`) AS cName
                , I.cSeo
                , COALESCE(I.`cBeschreibung`, A.`cBeschreibung`) AS cBeschreibung
                , COALESCE(I.`cKurzBeschreibung`, A.`cKurzBeschreibung`) AS cKurzBeschreibung
                ';
        } else {
            $sql .= ', A.cName
                , A.cSeo
                , A.`cBeschreibung`
                , A.`cKurzBeschreibung`
                ';
        }

        if (!$this->bUeberverkaufAsLager) {
            $sql .= ',  IF(A.fLagerbestand > 0, "0", IF(A.fLieferantenlagerbestand > 0, A.fLieferzeit,"")) as BMSLieferzeit ';
        } else {
            $sql .= ',  IF(A.fLagerbestand > 0, "0", IF(A.fLieferantenlagerbestand > 0, A.fLieferzeit, IF(A.cLagerKleinerNull = "Y", 999,""))) as BMSLieferzeit ';
        }
        $sql .= '
            , A.`cArtNr`
            , A.`kSteuerklasse`
            , A.`fMwSt`
            , A.`cHAN`
            , A.`cBarcode`
            , A.`kHersteller`
            , A.`cTopArtikel`
            , A.`cNeu`
            , A.`dErstellt`
            , A.`cLagerBeachten`
            , A.`cLagerKleinerNull`
            , A.`cSuchbegriffe`
            , A.`kVaterArtikel`
            , A.`nIstVater`
            , A.`fLagerbestand`
            , A.`cISBN`
            , A.`cASIN`
            , A.`fVPEWert`
            , A.`cVPEEinheit`
            , A.`cVPE`
            , A.`nSort`
            
            , B.`cName` AS cHersteller
            
            , C.`fDurchschnittsBewertung`
            
            , K.`fAnzahl` as bestseller
            , K.`isBestseller` as isBestseller
            , T.`cWert` as bmsBoost 
            , X.`cWert` as bmsSplit 
            , A.`fMindestbestellmenge`
            , U.`sEigenschaft`
            , V.`cName` as cWarengruppe 
            , V.`kWarengruppe` as kWarengruppe 
            
            , GROUP_CONCAT(DISTINCT D.kKategorie) as catIDs';
            if($this->IndexMerkmalStatus == 'active'){
                $sql .= ', E.sMerkmal AS Merkmal';
            }
            else {
                $sql .= ', "" AS Merkmal';
            }
            $sql .= ', L.sPreis AS priceData
            , MIN( M.nNr ) AS imageNR
            , N.sSonderpreis AS sonderpreisData';

        if ($this->showAttribute) {
            $sql .= ', GROUP_CONCAT(
                DISTINCT CONCAT (R.nSort, "==", R.kAttribut, "==", R.cName , "==" , R.cStringWert, "==" , R.cTextWert) SEPARATOR "||"
                ) AS attributeData';
        }
        /*
            , GROUP_CONCAT(P.cName SEPARATOR ' ' ) AS childNames
            , GROUP_CONCAT(P.`cHAN` SEPARATOR ' ' ) AS childHAN
            , GROUP_CONCAT(P.`cArtNr` SEPARATOR ' ' ) AS childArtnr
            */
        
        if ($this->updateType == 'full' ){
            $sql .= ' FROM tartikel AS A ';
        } else {
            $sql .= ' FROM xplugin_bms_update_inc_kArticle AS F LEFT JOIN tartikel AS A ON A.kArtikel = F.kArtikel ';
        }
        

        $sql .= 'LEFT JOIN thersteller AS B ON A.kHersteller = B.kHersteller
            LEFT JOIN tartikelext AS C ON A.kArtikel=C.kArtikel
            LEFT JOIN tkategorieartikel AS D ON A.kArtikel = D.kArtikel ';
        if($this->IndexMerkmalStatus == 'active'){
            $sql .= 'LEFT JOIN xplugin_bms_merkmale AS E ON A.kArtikel = E.kArtikel AND E.kSprache = ' . $language['kSprache'] . ' ';
        }
        
        if ($language['cShopStandard'] !== 'Y') {
            $sql .= 'LEFT JOIN tartikelsprache AS I ON A.kArtikel = I.kArtikel AND I.kSprache = ' . $language['kSprache'] . ' ';
        }
        $sql .= 'LEFT JOIN tbestseller AS K ON A.kArtikel = K.kArtikel
            LEFT JOIN xplugin_bms_preis AS L ON A.kArtikel = L.kArtikel 

            LEFT JOIN tartikelpict AS M ON A.kArtikel = M.kArtikel
            LEFT JOIN xplugin_bms_sonderpreis AS N ON A.kArtikel = N.kArtikel ';

        //if ($this->updateType !== 'full') {
            //$sql .= 'LEFT JOIN tartikel AS P ON A.`kArtikel` = P.`kVaterArtikel` AND P.`kVaterArtikel` > 0 ';
        //}

        if ($this->showAttribute) {
            $sql .= 'LEFT JOIN tattribut AS R ON A.`kArtikel`= R.`kArtikel` ';
        }

        //move to DB instead of Join???
        $sql .= 'LEFT JOIN tartikelattribut AS Q ON A.`kArtikel`= Q.`kArtikel` AND Q.`cName` = "bms_hide" ';
        $sql .= 'LEFT JOIN tartikelattribut AS T ON A.`kArtikel`= T.`kArtikel` AND T.`cName` = "bms_boost" ';
        $sql .= 'LEFT JOIN tartikelattribut AS X ON A.`kArtikel`= X.`kArtikel` AND X.`cName` = "bms_split" ';

        $sql .= 'LEFT JOIN xplugin_bms_eigenschaften AS U ON A.`kArtikel`= U.`kArtikel` ';
        $sql .= 'LEFT JOIN twarengruppe AS V ON A.`kWarengruppe`= V.`kWarengruppe` ';


        if($this->batchEnd > 0){
            if ($this->updateType == 'full' ){
                $sql .= 'WHERE A.kArtikel';
            }
            else {
                $sql .= 'WHERE F.kArtikel';
            }
            $sql .= ' BETWEEN ' .  $this->batchStart;
            $sql .= ' AND ' .  $this->batchEnd . ' ';
        }
        else {
            die("Error Batch");
        }

        
        $addSQL = $this->getSQLFilterLagerAndVarKombi();
        if($addSQL !== ""){
            $sql .= ' AND ' . $this->getSQLFilterLagerAndVarKombi();
        }

        $sql .= ' AND Q.`cName` IS NULL ';

        
        //$sql .= " AND A.cArtNr = '127209'";
        //$sql .= " AND A.kArtikel = '870'";

        
        $sql .= ' GROUP BY A.kArtikel ';
        $sql .= ' ORDER BY kArtikel ASC ';

        //$sql .= 'ORDER BY A.`nIstVater` DESC ';
        return $sql;
    }


}