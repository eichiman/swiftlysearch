<?php
declare(strict_types=1);

namespace Plugin\bms;



use JTL\Alert\Alert;
use JTL\Catalog\Category\Kategorie;
use JTL\Catalog\Product\Artikel;
use JTL\Consent\Item;
use JTL\Events\Dispatcher;
use JTL\Events\Event;
use JTL\Helpers\Form;
use JTL\Helpers\Tax;
use JTL\Link\LinkInterface;
use JTL\Plugin\Bootstrapper;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use JTLShop\SemVer\Version;
use JTL\Session\Frontend;
use JTL\DB\ReturnType;
use JTL\Minify\MinifyService;

use Imagick;

use JTL\Language\LanguageHelper;

/**
 * Class Bootstrap
 * @package Plugin\bms
 */
class Bootstrap extends Bootstrapper
{

    private $bms = false;
    private $bms_template = false;

    public function installed()
    {
        parent::installed();


        mkdir(PFAD_ROOT . PFAD_MEDIAFILES . 'bms_banner', 0777, true);
    }

    public function enabled()
    {
        $minify  = new MinifyService();
		$minify->flushCache();
    }

    /**
     * @inheritdoc
     */
    public function updated($oldVersion, $newVersion)
    {

        $minify  = new MinifyService();
		$minify->flushCache();

        Shop::Container()->getCache()->flushTags(array(CACHING_GROUP_PLUGIN, CACHING_GROUP_TEMPLATE,'cache_'.$this->getPlugin()->getPluginID()));
    }


    public function uninstalled(bool $deleteData = true)
    {
        if ($deleteData) {
            @unlink(PFAD_ROOT . PFAD_MEDIAFILES . '/bms_banner');
        }

        parent::uninstalled($deleteData);
    }

    public function boot(Dispatcher $dispatcher)
    {
        parent::boot($dispatcher);
        if (Shop::isFrontend() === false) {
            return;
        }
       
        $dispatcher->listen('shop.hook.' . \HOOK_SMARTY_OUTPUTFILTER, function (array $args) {
            $bms = new Bms();
        
            $smarty = $args['smarty'];
            $smarty->assign('bms_show', 'true');

            //die("HOOK SMARTY");
            //Deaktiviert
            if ($bms->getSettings('search_status') == '' || $bms->getSettings('search_status') == 'deactivated') {
                $smarty->assign('bms_show', 'false');
                return;
            }
            //AdminUser
            else if ($bms->getSettings('search_status') == 'admin' && !Shop::isAdmin()) {
                $smarty->assign('bms_show', 'false');
                return;
            }
            $smarty->assign('bms_plugin', $this->getPlugin());
            //falsche ausgabe/daten??? die($bms->getViewSettings('view_container_class'));
            $smarty->assign('view_container_css', $bms->getViewSettings('view_container_class'));
            $smarty->assign('specialCSS', $bms->getViewSettings('specialCSS'));
           

            $bms_standardView = 'gallery';
            $countViews = 0;

            if ($bms->getViewSettings('view_table_status') > 0) {
                $countViews++;
                $smarty->assign('bms_showViewSelectorTable', 'true');
                if ($bms->getViewSettings('view_table_status') == 2) {
                    $bms_standardView = 'table';
                }
            }
            else {
                $smarty->assign('bms_showViewSelectorTable', 'false');
            }
            if ($bms->getViewSettings('view_list_status') > 0) {
                $countViews++;
                $smarty->assign('bms_showViewSelectorList', 'true');
                if ($bms->getViewSettings('view_list_status') == 2) {
                    $bms_standardView = 'list';
                }
            }
            else {
                $smarty->assign('bms_showViewSelectorList', 'false');
            }
            if ($bms->getViewSettings('view_gallery_status') > 0) {
                $countViews++;
                $smarty->assign('bms_showViewSelectorGallery', 'true');
                if ($bms->getViewSettings('view_gallery_status') == 2) {
                    $bms_standardView =  'gallery';
                }
            }
            else {
                $smarty->assign('bms_showViewSelectorGallery', 'false');
            }
            

            $smarty->assign('bms_standardView', $bms_standardView);
            if ($countViews > 1) {
                $smarty->assign('bms_showViewSelector', 'true');
            } else {
                $smarty->assign('bms_showViewSelector', 'false');
            }


            $smarty->assign('bms_filter_showCategoryFilter', $bms->getSettings('filter_showCategoryFilter'));
            $smarty->assign('bms_filter_showWarengruppenFilter', $bms->getSettings('filter_showWarengruppenFilter'));
            $smarty->assign('bms_filter_showPriceFilter', $bms->getSettings('filter_showPriceFilter'));
            $smarty->assign('bms_filter_showManuFilter', $bms->getSettings('filter_showManuFilter'));
            

            $stockFilter = $bms->getSettings('filter_showStockFilter');
            $stockFilterChecked = false;

            if ($stockFilter) {
                if (strpos($stockFilter, 'order-checked-') === 0) {
                    $stockFilterChecked = true;
                    // "-checked" entfernen
                    $stockFilter = str_replace('-checked', '', $stockFilter);
                }
                $smarty->assign('bms_filter_showStockFilter', $stockFilter);
            }
            $smarty->assign('bms_filter_stockFilterChecked', $stockFilterChecked);

            

            $smarty->assign('bms_filter_showTopartikelFilter', $bms->getSettings('filter_showTopartFilter'));
            $smarty->assign('bms_filter_showBestsellerFilter', $bms->getSettings('filter_showBestsellerFilter'));
            $smarty->assign('bms_filter_showSpecialPriceFilter', $bms->getSettings('filter_showSpecialPriceFilter'));
            //TODO Check TODOMerkmale!!!
            $smarty->assign('bms_filter_showMerkmalFilter', 'active');
            

            $items = array();
            $itemsARR = array(
                'view_gallery_image',
                'view_gallery_title',
                'view_gallery_artnr',
                'view_gallery_manufacturer',
                'view_gallery_han', 
                'view_gallery_ean',
                'view_gallery_availability',
                'view_gallery_price',
                'view_gallery_cart',
                'view_gallery_gotooffer',
                'view_gallery_description'
            );
            foreach($itemsARR as $elem){
                if($bms->getViewSettings($elem) > 0){
                    $items[$elem] = $bms->getViewSettings($elem);
                }
                else {
                    $items[$elem] = null;
                }
            }
            asort($items);
            $smarty->assign('bms_galleryItems', $items);


            $items = array();
            $itemsARR = array(
                'view_table_artnr', 
                'view_table_title', 
                'view_table_han', 
                'view_table_avail', 
                'view_table_price', 
                'view_table_manufacturer', 
                'view_table_cart', 
                'view_table_gotooffer'
            );
            foreach($itemsARR as $elem){
                if($bms->getViewSettings($elem) > 0){
                    $items[$elem] = $bms->getViewSettings($elem);
                }
                else {
                    $items[$elem] = null;
                }
            }
            asort($items, SORT_NUMERIC);
            $smarty->assign('bms_tableItems', $items);


            $items = array();
            $itemsARR = array(
                'view_list_artnr', 
                'view_list_manufacturer',
                'view_list_han',
                'view_list_ean',
                'view_list_category',
                'view_list_price',
                'view_list_cart',
                'view_list_gotooffer',
                'view_list_description',
            );
            foreach($itemsARR as $elem){
                if($bms->getViewSettings($elem) > 0){
                    $items[$elem] = $bms->getViewSettings($elem);
                }
                else {
                    $items[$elem] = null;
                }
            }
            $smarty->assign('bms_listItems', $items);


            $jtl_data = array();
            //TODO Session Data??? => shop::
            $kKunde = Frontend::getCustomer()->kKunde;
            if (!is_numeric($kKunde) || $kKunde < 1) {
                if(Shop::getSettings([CONF_GLOBAL])['global']['global_sichtbarkeit'] > 1){
                    $kKunde = 'x' . Shop::getSettings([CONF_GLOBAL])['global']['global_sichtbarkeit'];
                }
                else {
                    $kKunde = 'N';
                }
            }
            $jtl_data[] = $kKunde;
            
            $customerGroupID = Frontend::getCustomer()->kKundengruppe;
            if (!$customerGroupID) {
                $customerGroupID = Frontend::getCustomerGroup()->getID();
            }
            $jtl_data[] = $customerGroupID;
            $jtl_data[] = session_id();
            $jtl_data[] = time();
            $jtl_data[] = Frontend::getInstance()->getLanguage()->cISOSprache ?? '';
            $jtl_data[] = Frontend::getCurrency()->getID() ?? '';
            $steuerData = '';
            $ii = 0;
            $forceNetto = false;
            if(isset($_COOKIE["wnmBruttoNetto_IsBrutto"]) && $_COOKIE["wnmBruttoNetto_IsBrutto"] == "1"){
                $forceNetto = true;
            }
            if(!Frontend::getCustomerGroup()->getIsMerchant()){
                if(key_exists('Steuersatz', $_SESSION)){
                    foreach ($_SESSION['Steuersatz'] as $key=>$elem){
                        if($ii >= 1){
                            $steuerData .= '||';
                        }
                        if($forceNetto){
                            $elem = 0;
                        }
                        $steuerData .=  $key.'-'.$elem;
                        $ii++;
                    }
                }
            }
            $jtl_data[] = $steuerData;
            $jtl_data[] = Shop::getSettings([CONF_GLOBAL])['global']['global_preis0'];
            $jtl_data[] = Shop::getSettings([CONF_GLOBAL])['global']['consistent_gross_prices'];

            $JTLSettings = implode("_", $jtl_data);

            $bms_settingsJS = array();
            $bms_settingsJS['es'] = $bms->getShop('es_server'); 
            $bms_settingsJS['searchTimeout'] = $bms->getSettings('search_timeout');
            $bms_settingsJS['dataJtl'] = $JTLSettings;
            if(Frontend::getCurrency()->getID() != ''){
                $bms_settingsJS['kWaehrung'] = Frontend::getCurrency()->getID();
            }
            $bms_settingsJS['dataHash'] =  md5('hash' . $JTLSettings . 'bms');
            $bms_settingsJS['standardView'] = $bms_standardView;

            $bms_settingsJS['viewTarget'] = 'content';
            if($bms->getViewSettings('view_target') != 'content'){
                $bms_settingsJS['viewTarget'] = $bms->getViewSettings('view_target');
            }

            $bms_settingsJS['viewFilter'] = $bms->getViewSettings('view_filter');
            
            
            //kein Bild
            $smarty->assign('noImagePath', Shop::getImageBaseURL() . BILD_KEIN_ARTIKELBILD_VORHANDEN);
            $bms_settingsJS['noImagePath'] = Shop::getImageBaseURL() . BILD_KEIN_ARTIKELBILD_VORHANDEN;
            $smarty->assign('noImageAlt', 'kein Bild');
            $bms_settingsJS['noImageAlt'] = 'kein Bild';
            

            if ($bms->getViewSettings('view_gallery_status') > 0) {
                $bms_settingsJS['viewGallery'] = 'true';
            }
            
            if ($bms->getViewSettings('view_list_status') > 0) {
                $bms_settingsJS['viewList'] = 'true';
            }

            if ($bms->getViewSettings('view_table_status') > 0) {
                $bms_settingsJS['viewTable'] = 'true';
            }
            
            if ($bms->getSettings('searchcache') == 'active') {
                $bms_settingsJS['searchcache'] = 'true';
            }

            if ($bms->getSettings('saveviewsettings') != '' && $bms->getSettings('saveviewsettings') == 'active'){
                $bms_settingsJS['saveviewsettings'] = 'true';
            }

            if ($bms->getSettings('hashurlsettings') != '' && $bms->getSettings('hashurlsettings') == 'disabled'){
                $bms_settingsJS['hashurlsettings'] = 'false';
            }

            if ($bms->getViewSettings('js_clone') != ''){
                $bms_settingsJS['jsclone'] = $bms->getViewSettings('js_clone');
            }

            if(file_exists($this->getPlugin()->getPaths()->getFrontendPath() . 'template/custom/bms.tpl')){
                $this->bms_template = 'cstm';
            }
            else if ($bms->getViewSettings('template') != '' && $bms->getViewSettings('template') != 'auto'){
                $this->bms_template = $bms->getViewSettings('template');
            }
            else {
                $this->bms_template = Shop::Container()->getTemplateService()->getActiveTemplate()->cParent;
                if($this->bms_template == ''){
                    $this->bms_template = Shop::Container()->getTemplateService()->getActiveTemplate()->cTemplate;
                }   
                if($this->bms_template != ''){
                    $this->bms_template = strtolower($this->bms_template);
                }
            }
            $bms_settingsJS['viewTemplate'] = $this->bms_template;
            
            
            $bms_settingsJS['bannerTimeout'] =  $bms->getBannerSettings('timeout'); 
            $bms_settingsJS['shopURL'] = Shop::getURL();
            

            $smarty->assign('bms_settingsJS', $bms_settingsJS);

            if ($bms_settingsJS['viewTemplate'] == 'admorris_pro' || $bms_settingsJS['viewTemplate'] == 'snackys'){
                $smarty->assign('bs3CSSUpdateSRC', '/plugins/bms/frontend/css/bms_bs3.css?v=' . $this->getPlugin()->getCurrentVersion() );
            }
            else {
                $smarty->assign('bs3CSSUpdateSRC', '');
            }
            
            $smarty->assign('bms_backgroundCSS', $bms->getBackgroundCSS());

            //Banner
            $bannerARR = array();
            if ($bms->getBannerSettings('status') != 'deactivated'){
                if (Shop::isAdmin() || $bms->getBannerSettings('status') == 'active'){
                    foreach ($bms->getBanners() as $item){
                        if (!Shop::isAdmin()){
                            if ($item->status == 'admin'){
                                //continue;
                            }
                        }
                        $bannerARR[] = $item;
                    }
                }
                else{
                }

            }
            else{
            } 
            $smarty->assign('bms_banner', $bannerARR);
            $smarty->assign('bms_bannerPosition', $bms->getBannerSettings('position') );

            

            // Other Templates?
            if($this->bms_template == 'cstm'){
                $file     = $this->getPlugin()->getPaths()->getFrontendPath() . 'template/custom/bms.tpl';
                $file_custom     = $this->getPlugin()->getPaths()->getFrontendPath() . 'template/custom/bms_custom.tpl';
            }
            else if($this->bms_template == 'nova'){
                $file     = $this->getPlugin()->getPaths()->getFrontendPath() . 'template/nova/bms.tpl';
                $file_custom     = $this->getPlugin()->getPaths()->getFrontendPath() . 'template/nova/bms_custom.tpl';
            }
            else {
                $file     = $this->getPlugin()->getPaths()->getFrontendPath() . 'template/standard/bms.tpl';
                $file_custom     = $this->getPlugin()->getPaths()->getFrontendPath() . 'template/standard/bms_custom.tpl';
            }
            
            if(file_exists($file_custom)){
                pq('body')->append($smarty->fetch($file_custom));
            }
            elseif(file_exists($file)){
                pq('body')->append($smarty->fetch($file));
            }
            
        });
    }

    /**
     * @inheritdoc
     */
    public function renderAdminMenuTab(string $tabName, int $menuID, JTLSmarty $smarty): string
    {
        $plugin  = $this->getPlugin();

        if (!$this->bms) {
            $this->bms = new Bms();
            $this->bms->getLicenceData();

        }

        if ($this->bms->getSearchLicence() == 'active' || $this->bms->getSearchLicence() == 'test' || $this->bms->getSearchLicence() == 'devel') {
            if ($this->bms->getSearchLicence() == 'test' ) {
                if($this->bms->getEndTestLicence() < time()){
                    //$alert->addAlert(Alert::TYPE_DANGER, __('Testlizenz abgelaufen!!!'), 'dateTestlic');
                }
                else {
                    //$alert->addAlert(Alert::TYPE_INFO, __('Testlizenz noch x Tage!!!!'), 'dateTestlic');
                }
                //Check Date!
                
                //Testcode TODO $smarty->assign('endTestlic', date("d.m.Y H:s", $this->bms->getEndTestLicence()));
            }
            $smarty->assign('licence', $this->bms->getSearchLicence());
            $smarty->assign('search_status', $this->bms->getSettings('search_status'));
            $smarty->assign('bmsAdminAction',  $this->bms->getShopinterface());
            $smarty->assign('bmsAdminSyncPW', $this->bms->getShop('syncPass'));
            $smarty->assign('bmsVersion', $this->getPlugin()->getCurrentVersion());
            $smarty->assign('endTestlic', '');
            $smarty->assign('bmsCurrentVersion', $this->bms->getCurrentVersion());
            $postDataARR = array();
            $postDataARR['indexName'] = $this->bms->getIndexName();
            $ch = curl_init($this->bms->getShop('es_server') . '/api'); //todo get from Settings!!
            //echo $this->bms->getShop('es_server') . '/api';
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataARR);

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            //curl_setopt( $ch, CURLOPT_HEADER, true ); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Origin: ' . URL_SHOP));
            if(isset($_GET['user_agent'])){
                curl_setopt($ch, CURLOPT_USERAGENT, $_GET['user_agent']);
            }
            elseif(isset($_GET['HTTP_USER_AGENT'])){
                curl_setopt($ch, CURLOPT_USERAGENT, SERVER['HTTP_USER_AGENT']);
            }
            
            
            if (!is_numeric(CURLOPT_TIMEOUT_MS) || CURLOPT_TIMEOUT_MS <= 0) {
                define('CURLOPT_CONNECTTIMEOUT_MS', 155); //some PHP Bugs!!!
            }
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 400);

            $response = curl_exec($ch);
            //echo PHP_EOL.$response;
            if($response && $response != ''){
                $statJSON = json_decode($response);
            }
            else {
                $statJSON = '{}';
            }
            
            $smarty->assign('bmsStats',  $statJSON);
            //$smarty->assign('bmsStats',  '');
            //echo "<pre>";print_r($statJSON);echo "</pre>";die();
            return $smarty->assign(
                'adminURL',
                Shop::getURL() . '/' . \PFAD_ADMIN . 'plugin.php?kPlugin=' . $plugin->getID()
            )
                ->fetch($plugin->getPaths()->getAdminPath() . 'templates/statustab.tpl');
                
        }
        elseif ($this->bms->getSearchLicence() == 'inactive' || $this->bms->getSearchLicence() == 'new'){
            if ($this->bms->getSearchLicence() == 'new' || $this->bms->getLicenceError() == 'newShop'){
                return $smarty->assign(
                    'adminURL',
                    Shop::getURL() . '/' . \PFAD_ADMIN . 'plugin.php?kPlugin=' . $plugin->getID() . '#plugin-tab-' . $menuID
                )->fetch($plugin->getPaths()->getAdminPath() . 'templates/lizenz_test_form.tpl');
            }
            
            switch ($this->bms->getLicenceError()){

                case 'mail':
                    return $smarty->assign(
                        'adminURL',
                        Shop::getURL() . '/' . \PFAD_ADMIN . 'plugin.php?kPlugin=' . $plugin->getID() . '#plugin-tab-' . $menuID
                    )->fetch($plugin->getPaths()->getAdminPath() . 'templates/lizenz_mail_form.tpl');
                    break;
                case 'mailSent':
                    return $smarty->fetch($plugin->getPaths()->getAdminPath() . 'templates/lizenz_mail_sent.tpl');
                    break;
                case 'new':
                case 'newShop':
                    break;                    
                default:
                    die($this->bms->getLicenceError());
            }
        }
        return $smarty->assign(
            'adminURL',
            Shop::getURL() . '/' . \PFAD_ADMIN . 'plugin.php?kPlugin=' . $plugin->getID() . '#plugin-tab-' . $menuID
        )->assign(
            'licencestatus',
            $this->bms->getSearchLicence()
        )->assign(
            'errorcode',
            $this->bms->getLicenceError()
        )->fetch($plugin->getPaths()->getAdminPath() . 'templates/lizenz_error.tpl');

        
        
    }


}