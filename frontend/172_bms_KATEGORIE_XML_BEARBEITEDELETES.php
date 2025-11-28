<?php
try {
	if (isset($args_arr['oKategorie']->kKategorie) && is_numeric($args_arr['oKategorie']->kKategorie) && $args_arr['oKategorie']->kKategorie > 0) {

		if(!class_exists('bms_update')){
			require_once (dirname(__FILE__) . '/class.bms_update.php');
		}
		
		$bms_update = new bms_update();
		$bms_update -> setDeleteByKkategorie($args_arr['oKategorie']->kKategorie);
		
	}
}
catch (Exception $oEx) {
	error_log("\nError: \n" . print_r($oEx, true) . " \n", 3, PFAD_ROOT . 'jtllogs/bms_error.txt');
}