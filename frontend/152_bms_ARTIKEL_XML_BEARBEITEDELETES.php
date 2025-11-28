<?php
try {
	if (isset($args_arr['kArtikel']) && is_numeric($args_arr['kArtikel']) && $args_arr['kArtikel'] > 0) {

		if(!class_exists('bms_update')){
			require_once (dirname(__FILE__) . '/class.bms_update.php');
		}

		$bms_update = new bms_update();
		$bms_update -> setDeleteByKartikel($args_arr['kArtikel']);
		
	}
} catch (Exception $oEx) {
	error_log("\nError: \n" . print_r($oEx, true) . " \n", 3, PFAD_ROOT . 'jtllogs/bms_error.txt');
}