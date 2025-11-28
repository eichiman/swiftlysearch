<?php declare(strict_types=1);

namespace Plugin\bms;

use JTL\Shop;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\DB\ReturnType;
use JTL\Language\LanguageHelper;
use JTL\Session\Frontend;

class bms_update {
    public function __construct(){
    }

    public function setUpdateByKhersteller($khersteller){
        if (!$khersteller || !is_numeric($khersteller)) {
            return;
        }

        $query = "SELECT kArtikel FROM tartikel AS A WHERE A.kHersteller = :kHersteller";

        $result = Shop::Container()->getDB()->queryPrepared(
            $query,
            ['kHersteller' => $khersteller],
            ReturnType::AFFECTED_ROWS
        );
        $kArtikelARR = [];
        foreach ($result as $elem) {
            $kArtikelARR[] = $elem->kArtikel;
        }
        $this->setUpdateByKartikelARR($kArtikelARR);
    }

    public function setUpdateByKkategorie($kKategorie){
        if (!$kKategorie || !is_numeric($kKategorie)) {
            return;
        }

        $query = "SELECT kArtikel FROM tkategorie AS A
                  LEFT JOIN tkategorieartikel AS B ON A.kKategorie = B.kKategorie
                  WHERE (A.kKategorie = :kKategorie OR A.kOberKategorie = :kKategorie)
                  AND B.kArtikel != ''";

        $result = Shop::Container()->getDB()->queryPrepared(
            $query,
            ['kKategorie' => $kKategorie],
            ReturnType::AFFECTED_ROWS
        );
        $kArtikelARR = [];
        foreach ($result as $elem) {
            $kArtikelARR[] = $elem->kArtikel;
        }
        $this->setUpdateByKartikelARR($kArtikelARR);
    }

    public function setDeleteByKkategorie($kKategorie){
        // Implementation needed
    }

    public function setDeleteByKartikel($kArtikel){
        if (!$kArtikel || !is_numeric($kArtikel)) {
            return;
        }

        $query = "INSERT IGNORE INTO xplugin_bms_delete_kArticle SET kArtikel = :kArtikel";

        Shop::Container()->getDB()->queryPrepared(
            $query,
            ['kArtikel' => $kArtikel],
            ReturnType::AFFECTED_ROWS
        );
    }

    public function setUpdateByKartikel($kArtikel){
        if (!$kArtikel || !is_numeric($kArtikel)) {
            return;
        }

        Shop::Container()->getDB()->queryPrepared(
            'INSERT IGNORE INTO xplugin_bms_update_kArticle SET kArtikel = :kArtikel',
            ['kArtikel' => $kArtikel],
            ReturnType::AFFECTED_ROWS
        );
    }

    public function setUpdateByKartikelARR(&$kArtikelARR){
        if (is_array($kArtikelARR) && count($kArtikelARR) > 0) {
            $query = 'INSERT IGNORE INTO xplugin_bms_update_kArticle VALUES ';
            $query .= implode(',', array_map(fn($kArtikel) => "($kArtikel)", $kArtikelARR));
            Shop::Container()->getDB()->executeQuery($query, 3);
        }
    }
}

class Bms
{
    private string $indexName = '';
    private $settings = null;
    private string $settingsJSON = '';
    private $status;
    private $statusErrorCode = 0;
    private $endTestLicence;
    private $cacheID = 'bms_settings';
    private $licenceStatus = '';

    public function __construct()
    {
        $this->loadSettings();
        $this->setIndexName();
    }

    private function loadSettings(){
        $this->settings = Shop::Container()->getCache()->get($this->cacheID);
        if ($this->settings === false) {
            $this->refreshCache();
        } else {
            $this->settingsJSON = json_encode($this->settings);
        }
    }

    private function refreshCache(){
        $settingsJson = Shop::Container()->getDB()->query(
            "SELECT kkey, sValue FROM xplugin_bms_settings", 
            ReturnType::SINGLE_OBJECT
        );

        $this->settingsJSON = $settingsJson->sValue;
        if ($settingsJson->sValue) {
            $this->settings = json_decode($settingsJson->sValue);
        }
        Shop::Container()->getCache()->set($this->cacheID, $this->settings);
    }

    public function setJSONSettings(string $json): void
    {
        Shop::Container()->getDB()->queryPrepared(
            'INSERT INTO xplugin_bms_settings (kKey, sValue) VALUES (1, :sValue) 
            ON DUPLICATE KEY UPDATE sValue = :sValue',
            ['sValue' => $json],
            ReturnType::AFFECTED_ROWS
        );

        $settings = json_decode($json);
        if (!$settings) {
            return;
        }

        // Unlink all banners
        if (property_exists($settings, 'banner')) {
            $bannerDir = PFAD_ROOT . PFAD_MEDIAFILES . 'bms_banner/';
            if (!is_dir($bannerDir)) {
                mkdir($bannerDir);
            }
            if (is_dir($bannerDir)) {
                foreach ($settings->banner as $elem) {
                    $filename = str_replace("banner/", "", $elem->file);
                    if (!file_exists($bannerDir . $filename)) {
                        copy($this->getShopinterface() . '/image/banner_' . $elem->id, $bannerDir . $filename);
                    }
                }
            }
        }

        // Unlink all backgrounds
        if (property_exists($settings, 'background')) {
            $backgroundDir = PFAD_ROOT . PFAD_MEDIAFILES . 'bms_background/';
            if (!is_dir($backgroundDir)) {
                mkdir($backgroundDir);
            }
            if (is_dir($backgroundDir)) {
                foreach ($settings->background as $elem) {
                    $filename = str_replace("background/", "", $elem->file);
                    if (!file_exists($backgroundDir . $filename)) {
                        copy($this->getShopinterface() . '/image/background_' . $elem->id, $backgroundDir . $filename);
                    }
                }
            }
        }

        $this->refreshCache();
        $this->loadSettings();
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    private function setIndexName()
    {
        $indexName = Shop::getURL();
        $indexName = str_replace(['http://', 'https://'], '', $indexName);
        $indexName = preg_replace('/[^-_a-z0-9]/i', '-', $indexName);
        $this->indexName = $indexName;
    }

    private $shopSettingsChecked = false;
    public function getShopSettings($key) {
        if (!$this->shopSettingsChecked) {
            if ($this->settings && property_exists($this->settings, 'shopsettings')) {
                $this->shopSettingsChecked = true;
            } else {
                return;
            }
        }

        if (property_exists($this->settings->shopsettings, $key)) {
            return $this->settings->shopsettings->$key;
        }
    }

    public function getLicenceData()
    {
        $postArray = [
            'indexName' => $this->indexName,
            'domain' => Shop::getURL(),
            'api' => 'getLicenceData'
        ];

        if (key_exists('activationHash', $_GET)) {
            $postArray['activationHash'] = Request::getVar('activationHash');
        }

        if ($this->settings && property_exists($this->settings, 'shop') && property_exists($this->settings->shop, 'syncPass')) {
            $postArray['syncPass'] = $this->settings->shop->syncPass;
        }

        if (key_exists('bms_lizenz_mail', $_POST) && Form::validateToken()) {
            $postArray['bms_lizenz_mail'] = Request::postVar('bms_lizenz_mail');
        }

        if (key_exists('adminURL', $_POST) && Form::validateToken()) {
            $postArray['adminURL'] = Request::postVar('adminURL');
        }

        $languageARR = array_map(fn($lang) => $lang->iso, Frontend::getLanguages());
        $postArray['languagesShop'] = implode(",", $languageARR);

        $postdata = http_build_query($postArray);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'timeout' => 20,
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            ]
        ];

        $context  = stream_context_create($opts);
        $result = file_get_contents($this->getShopinterface(), false, $context);

        if ($result === "inactive-mail") {
            $this->setLicenceStatus('mail', 'inactive');
        } else if ($result === "inactive-mailSent") {
            $this->setLicenceStatus('mailSent', 'inactive');
        } else if ($result === "inactive-newShop") {
            $this->setLicenceStatus('newShop', 'inactive');
        } else if ($result && preg_match("/^error-([0-9]+)$/i", $result, $match)) {
            $this->setLicenceStatus($match[1], 'error');
        } else if ($result !== "") {
            if ($this->settingsJSON !== $result) {
                $this->setJSONSettings($result);
            }
        } else {
            $this->setLicenceStatus('error-404', 'error-404');
        }
    }

    private function setLicenceStatus($code, $status) {
        $this->licenceStatus = $status === 'inactive' ? 'inactive' : 'error';
        $this->licenceStatusCode = is_numeric($code) ? $code : $code;
    }

    public function getLicenceError() {
        return $this->licenceStatusCode;
    }

    public function getShopinterface() {
        if ($this->settings && property_exists($this->settings, 'shop') && property_exists($this->settings->shop, 'shopinterface')) {
            return $this->settings->shop->shopinterface;
        }
        return "https://www.bm-suche.de/shopinterface";
    }

    private $shopChecked = false;
    public function getShop($key) {
        if (!$this->shopChecked) {
            if ($this->settings && property_exists($this->settings, 'shop')) {
                $this->shopChecked = true;
            } else {
                return "";
            }
        }
        if (property_exists($this->settings->shop, $key)) {
            return $this->settings->shop->$key;
        }
    }

    public function getSearchLicence(){
        if ($this->licenceStatus !== '') {
            return $this->licenceStatus;
        }

        if ($this->settings && property_exists($this->settings, 'shop') && property_exists($this->settings->shop, 'licence')) {
            $this->licenceStatus = $this->settings->shop->licence;
        }

        return $this->licenceStatus;
    }

    private $settingsChecked = false;
    public function getSettings($key) {
        if (!$this->settingsChecked) {
            if ($this->settings && property_exists($this->settings, 'settings')) {
                $this->settingsChecked = true;
            } else {
                return;
            }
        }
        if (property_exists($this->settings->settings, $key)) {
            return $this->settings->settings->$key;
        }
    }

    public function getBackgroundCSS() {
        if (!$this->settings || !property_exists($this->settings, 'backgroundSettings') || !property_exists($this->settings, 'background')) {
            return '';
        }

        if ($this->settings !== '' && property_exists($this->settings, 'viewsettings') && property_exists($this->settings->viewsettings, 'view_target') && $this->settings->viewsettings->view_target === 'content') {
            return '';
        }

        if ($this->settings->backgroundSettings->imageStatus === 'active' || (Shop::isAdmin() && $this->settings->backgroundSettings->imageStatus === 'admin')) {
            $backgrounds = is_array($this->settings->background ?? null)? $this->settings->background: [];
            $backgroundARR = array_filter(
                $backgrounds,
                fn($background) =>
                    ($background->status ?? '') === 'active' ||
                    (Shop::isAdmin() && ($background->status ?? '') === 'admin')
            );

            $backgroundColor = '';
            $imageRepeat = '';
            $imageFile = '';
            $containerStyle = '';
            if (count($backgroundARR) > 0) {
                $rndm = rand(0, count($backgroundARR) - 1);
                $bg   = $backgroundARR[$rndm];

                $imageFile = !empty($bg->file)? str_replace('background/', '/' . PFAD_MEDIAFILES . 'bms_background/', $bg->file): '';

                $backgroundColor = trim($bg->backgroundColor ?? '')?: trim($this->settings->backgroundSettings->backgroundColor ?? '');

                $imageRepeat    = trim($bg->imageRepeat ?? '');
                $containerStyle = trim($bg->imageContainerStyle ?? '') . trim($this->settings->backgroundSettings->containerStyle ?? '');
            } else {
                $backgroundColor = trim($this->settings->backgroundSettings->backgroundColor ?? '');
                $containerStyle  = trim($this->settings->backgroundSettings->containerStyle ?? '');
            }

            $CSS = '';
            if ($backgroundColor !== '') {
                $CSS .= 'background-color:' . $backgroundColor . ';';
            }
            if ($imageRepeat !== '') {
                $CSS .= $imageRepeat;
            }
            if ($imageFile !== '') {
                $CSS .= 'background-image:url(' . $imageFile . ');';
            }
            if ($containerStyle !== '') {
                $CSS .= $containerStyle;
            }

            return $CSS;
        }
        return '';
    }

    public function getCurrentVersion() {
        if ($this->settings && property_exists($this->settings, 'plugin') && property_exists($this->settings->plugin, 'jtl5')) {
            return $this->settings->plugin->jtl5;
        }
    }

    public function getBanners(){
        if ($this->settings !== '' && property_exists($this->settings, 'viewsettings') && property_exists($this->settings->viewsettings, 'view_target') && $this->settings->viewsettings->view_target === 'content') {
            return [];
        }

        if ($this->settings && property_exists($this->settings, 'banner')) {
            foreach ($this->settings->banner as $elem) {
                $elem->file = str_replace('banner/', '/' . PFAD_MEDIAFILES . 'bms_banner/', $elem->file);
            }
            return $this->settings->banner;
        }
    }

    private $bannerSettingsChecked = false;
    public function getBannerSettings($key) {
        if (!$this->bannerSettingsChecked) {
            if ($this->settings && property_exists($this->settings, 'bannerSettings')) {
                $this->bannerSettingsChecked = true;
            } else {
                return;
            }
        }
        if (property_exists($this->settings->bannerSettings, $key)) {
            return $this->settings->bannerSettings->$key;
        }
    }

    public function getViewSettings($key) {
        if ($this->settings !== '' && property_exists($this->settings, 'viewsettings') && property_exists($this->settings->viewsettings, $key)) {
            return $this->settings->viewsettings->$key;
        }
    }

    public function getEndTestLicence() {
        return $this->endTestLicence;
    }
}