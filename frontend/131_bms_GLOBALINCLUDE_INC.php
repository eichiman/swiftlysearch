<?php declare(strict_types=1);

namespace Plugin\bms;
use JTL\Helpers\Request;
// set some value to registry

if (isset($_GET['bms']) && $_GET['bms'] == 'true') {
	$bms = new Bms();
	$ch = curl_init($bms->getShop('es_server')); // TODO: get from Settings!!
	curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//curl_setopt( $ch, CURLOPT_HEADER, true ); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Origin: ' . URL_SHOP));
	curl_setopt($ch, CURLOPT_USERAGENT, $_GET['user_agent'] ? $_GET['user_agent'] : $_SERVER['HTTP_USER_AGENT']);
	if (!is_numeric(CURLOPT_TIMEOUT_MS) || CURLOPT_TIMEOUT_MS <= 0) {
		define('CURLOPT_CONNECTTIMEOUT_MS', 155); //some PHP Bugs!!!
	}
	curl_setopt($ch, CURLOPT_TIMEOUT_MS, 400);

	$response = curl_exec($ch);
	die($response);
}
else if (isset($_GET['bms_config'])&& $_GET['bms_config'] == 'true') {
	$bms = new Bms();
	if (key_exists('syncPass', $_POST)) {
		if( $bms->getShop('syncPass') == Request::postVar('syncPass')){
			
			if (key_exists('data', $_POST)) {
				$bms->setJSONSettings(Request::postVar('data'));
			}
		}
	}
	die();
}
elseif (isset ( $_GET ['bms'] )) {
	if (isset ( $_POST ['pw'] )) {
		//ob_start('ob_gzhandler');
		$index = new BmsIndex ($_POST ['pw'], $plugin);
		
		$index -> setUpdateType(filter_var($_GET['bms'], FILTER_SANITIZE_SPECIAL_CHARS));

		if($index->getUpdateType() == 'CmsNews'){
			$index->getCmsNewsCSV();
			die();
		}


		 //echo '<pre>';print_r($_POST);echo '</pre><hr>'; //testcode 
		if (isset($_POST ['sp']) && $_POST ['sp'] == 'true') {
			$index->resetSonderpreis();
		}
		
		if (isset($_GET ['getAttribute']) && $_GET ['getAttribute'] == 'true') {
			$index->setShowAttribute();
		}
		
		
		if (isset($_POST['indexing_merkmal_status']) && strlen(trim($_POST['indexing_merkmal_status'])) > 2) {
			$index -> setIndexMerkmalStatus($_POST['indexing_merkmal_status']);
		}

		if (isset($_POST['varkombi']) && strlen(trim($_POST['varkombi'])) > 2) {
			$index -> setVarKombi($_POST['varkombi']);
		}

		if (isset($_POST['lastKArtikel']) && is_numeric($_POST['lastKArtikel'])){
			$index->setLimitLastKArtikel($_POST['lastKArtikel']);
		}

		if (isset($_POST['setUeberVerkaufAsLager'])){
			$index->setUeberVerkaufAsLager();
		}

		if (isset($_POST['limit']) && is_numeric($_POST['limit'])){
			$index->setLimit($_POST['limit']);
		}

		
		if (isset($_POST ['forceUpdate']) && $_POST ['forceUpdate'] == 'true') {
			$index->setForceUpdate();
		}
		
		if (isset($_POST ['staffel']) && $_POST ['staffel'] != '') {
			$index->setStaffel($_POST['staffel']);
		}

		$index->getCSV();		
	} 

	die ();
}

?>