<?php

use Phinx\Migration\AbstractMigration;

class FixedPhpStanPluginName extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE build_errors SET plugin = 'php_stan' WHERE plugin = 'phpstan'");
    }

    public function down()
    {
        $this->execute("UPDATE build_errors SET plugin = 'phpstan' WHERE plugin = 'php_stan'");
    }
}
