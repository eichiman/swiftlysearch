<?php
try {
	if (isset($args_arr['oArtikel']->kArtikel) && is_numeric($args_arr['oArtikel']->kArtikel) && $args_arr['oArtikel']->kArtikel > 0) {

		if(!class_exists('bms_update')){
			require_once (dirname(__FILE__) . '/class.bms_update.php');
		}

		$bms_update = new bms_update();
		$bms_update -> setUpdateByKartikel( $args_arr['oArtikel']->kArtikel);
		
	}
} catch (Exception $oEx) {
	error_log("\nError: \n" . print_r($oEx, true) . " \n", 3, PFAD_ROOT . 'jtllogs/bms_error.txt');
}