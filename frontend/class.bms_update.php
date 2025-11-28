<?php
use JTL\Shop;

class bms_update {
	public function __construct(){
	}
	

	public function setUpdateByKhersteller($khersteller){
		if(!$khersteller || !is_numeric($khersteller)){
			return;
		}

		$query = "SELECT
				kArtikel
				FROM tartikel AS A
				WHERE A.kHersteller = :kHersteller";


		$result = Shop::Container()->getDB()->queryPrepared(
			$query,
			['kHersteller' => $khersteller],
			JTL\DB\ReturnType::ARRAY_OF_OBJECTS
		);
		$kArtikelARR = array();
		foreach ($result as $elem) {
			$kArtikelARR[] = $elem->kArtikel;
			//$this->setUpdateByKartikel($elem['kArtikel']);
		}
		$this->setUpdateByKartikelARR($kArtikelARR);
	}

	
	public function setUpdateByKkategorie($kKategorie){
		if(!$kKategorie || !is_numeric($kKategorie)){
			return;
		}
		
		//Check for kOberkategorie and Sub?
		$query = "SELECT
			kArtikel
			FROM tkategorie  AS A
			LEFT JOIN tkategorieartikel AS B ON A.kKategorie = B.kKategorie
			WHERE
			(A.kKategorie = :kKategorie OR A.kOberKategorie = :kKategorie)
			AND B.kArtikel != ''
			";

		$result = Shop::Container()->getDB()->queryPrepared(
			$query,
			['kKategorie' => $kKategorie],
			JTL\DB\ReturnType::ARRAY_OF_OBJECTS
		);
		$kArtikelARR = array();
		foreach ($result as $elem) {
			//$this->setUpdateByKartikel($elem->kArtikel);
			$kArtikelARR[] = $elem->kArtikel;
		}
		$this->setUpdateByKartikelARR($kArtikelARR);
	}
	

	
	public function setDeleteByKkategorie($kKategorie){
	}
	


	public function setDeleteByKartikel($kArtikel){
		if(!$kArtikel || !is_numeric($kArtikel)){
			return;
		}
		
		$query = "INSERT IGNORE INTO xplugin_bms_delete_kArticle
				SET kArtikel = :kArtikel";

		$result = Shop::Container()->getDB()->queryPrepared(
			$query,
			['kArtikel' => $kArtikel],
			JTL\DB\ReturnType::AFFECTED_ROWS
		);
		
		
	}
	

	public function setUpdateByKartikel($kArtikel){
		if (!$kArtikel || !is_numeric($kArtikel)){
			return;
		}
		Shop::Container()->getDB()->queryPrepared(
            'INSERT IGNORE INTO xplugin_bms_update_kArticle SET kArtikel = :kArtikel',
            [
                'kArtikel' => $kArtikel
            ],
            JTL\DB\ReturnType::AFFECTED_ROWS
        );
		
	}



	
	public function setUpdateByKartikelARR(&$kArtikelARR){
		if (is_array($kArtikelARR) && count($kArtikelARR) > 0){
			$query = 'INSERT IGNORE INTO xplugin_bms_update_kArticle 
			values ';
			$i = 0;
			foreach($kArtikelARR as $kArtikel){
				if(is_numeric($kArtikel)){
					if($i > 0){
						$query .= ',';
					}
					$query .= '(' . $kArtikel . ')';
					$i++;
				}
			}
			Shop::Container()->getDB()->executeQuery($query, 3);
		}
		return;
	}
	
}