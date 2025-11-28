<?php
try {
	if (key_exists('Artikel', $args_arr) && count ($args_arr['Artikel']) > 1){

		if(!class_exists('bms_update')){
			require_once (dirname(__FILE__) . '/class.bms_update.php');
		}

		$bms_update = new bms_update();
		
		foreach ($args_arr['Artikel'] as $elem){
			$bms_update -> setUpdateByKartikel( $elem->kArtikel );
			
		}
	}
} catch (Exception $oEx) {
	error_log("\nError: \n" . print_r($oEx, true) . " \n", 3, PFAD_ROOT . 'jtllogs/bms_error.txt');
}