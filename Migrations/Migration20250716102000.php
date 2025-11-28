<?php declare(strict_types=1);

namespace Plugin\bms\Migrations;

use JTL\Plugin\Migration;
use JTL\Update\IMigration;

class Migration20250716102000 extends Migration implements IMigration
{
    public function up()
    {
          
        $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_swiftlysearch_eigenschaften` (
            kArtikel int(10) unsigned NOT NULL
            , kSprache tinyint(3) unsigned NOT NULL 
            , sEigenschaft TEXT NOT NULL 
            , PRIMARY KEY (kArtikel, kSprache)
            , INDEX(kArtikel)
            , INDEX(kSprache)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');


    }

    public function down()
    {
      $this->execute("DROP TABLE IF EXISTS `xplugin_swiftlysearch_eigenschaften`");
    }
}
