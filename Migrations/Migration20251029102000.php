<?php declare(strict_types=1);

namespace Plugin\bms\Migrations;

use JTL\Plugin\Migration;
use JTL\Update\IMigration;

class Migration20251029102000 extends Migration implements IMigration
{
    public function up()
    {
          
      $this->execute('CREATE TABLE IF NOT EXISTS `xplugin_swiftlysearch_update_inc_kArticle` (
        `kArtikel` int(10) unsigned NOT NULL, 
        PRIMARY KEY (kArtikel)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
      
        
    }

    public function down()
    {
      $this->execute("DROP TABLE IF EXISTS `xplugin_swiftlysearch_update_inc_kArticle`");
    }
}
