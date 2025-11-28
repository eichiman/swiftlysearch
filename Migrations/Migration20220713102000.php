<?php declare(strict_types=1);

namespace Plugin\bms\Migrations;

use JTL\Plugin\Migration;
use JTL\Update\IMigration;

class Migration20220713102000 extends Migration implements IMigration
{
    public function up()
    {
          
      $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_bms_update_kArticle` (
        `kArtikel` int(10) unsigned NOT NULL, 
        PRIMARY KEY (kArtikel)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
      
        
      $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_bms_settings` (
          kKey tinyint(1) unsigned NOT NULL
          , sValue TEXT NOT NULL 
          , PRIMARY KEY (kKey)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

      $standardJson = '{"shop": {"shopinterface": "https://www.swiftlysearch.de/shopinterface"}}';
      $this->execute(
        "INSERT INTO xplugin_bms_settings (kKey, sValue) VALUES ('1', '" . $standardJson . "')");
        
      $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_bms_delete_kArticle` (
          `kArtikel` int(10) unsigned NOT NULL, 
        PRIMARY KEY (kArtikel)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
      

      
        $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_bms_merkmale` (
            kArtikel int(10) unsigned NOT NULL
            , kSprache tinyint(3) unsigned NOT NULL 
            , sMerkmal TEXT NOT NULL 
            , PRIMARY KEY (kArtikel, kSprache)
            , INDEX(kArtikel)
            , INDEX(kSprache)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');


        $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_bms_preis` (
          kArtikel int(10) unsigned NOT NULL
          , sPreis TEXT NOT NULL 
          , PRIMARY KEY (kArtikel)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_bms_sonderpreis` (
          kArtikel int(10) unsigned NOT NULL
          , sSonderpreis TEXT NOT NULL 
          , PRIMARY KEY (kArtikel)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
    }

    public function down()
    {
      $this->execute("DROP TABLE IF EXISTS `xplugin_bms_update_kArticle`");
      $this->execute("DROP TABLE IF EXISTS `xplugin_bms_delete_kArticle`");
      $this->execute("DROP TABLE IF EXISTS `xplugin_bms_settings`");
      $this->execute("DROP TABLE IF EXISTS `xplugin_bms_merkmale`");
      $this->execute("DROP TABLE IF EXISTS `xplugin_bms_preis`");
      $this->execute("DROP TABLE IF EXISTS `xplugin_bms_sonderpreis`");
    }
}
