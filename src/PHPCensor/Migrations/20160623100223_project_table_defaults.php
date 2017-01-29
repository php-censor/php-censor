<?php

use Phinx\Migration\AbstractMigration;

class ProjectTableDefaults extends AbstractMigration
{
    public function change()
    {
        $this->table('project')
             ->changeColumn('build_config', 'text', ['null' => true])
             ->save();
    }
}
