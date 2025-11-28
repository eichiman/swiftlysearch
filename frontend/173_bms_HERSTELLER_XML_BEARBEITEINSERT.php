<?php
try {
	if (isset($args_arr['oHersteller']->kHersteller) && is_numeric($args_arr['oHersteller']->kHersteller) && $args_arr['oHersteller']->kHersteller > 0) {
	
		if(!class_exists('bms_update')){
			require_once (dirname(__FILE__) . '/class.bms_update.php');
		}
		
		$bms_update = new bms_update();
		$bms_update -> setUpdateByKhersteller($args_arr['oHersteller']->kHersteller);
		
	}
} catch (Exception $oEx) {
	error_log("\nError: \n" . print_r($oEx, true) . " \n", 3, PFAD_ROOT . 'jtllogs/bms_error.txt');
}
?>