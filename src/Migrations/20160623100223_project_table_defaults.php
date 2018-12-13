<?php

use Phinx\Migration\AbstractMigration;

class ProjectTableDefaults extends AbstractMigration
{
    public function up()
    {
        $this->table('project')
             ->changeColumn('build_config', 'text', ['null' => true])
             ->save();
    }
        public function down()
    {
    }
}
