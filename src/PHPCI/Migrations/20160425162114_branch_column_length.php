<?php

use Phinx\Migration\AbstractMigration;

class BranchColumnLength extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build');
        $table->changeColumn('branch', 'string', ['limit' => 250, 'null' => false, 'default' => 'master']);

        $table = $this->table('project');
        $table->changeColumn('branch', 'string', ['limit' => 250, 'null' => false, 'default' => 'master']);
    }

    public function down()
    {
        $table = $this->table('build');
        $table->changeColumn('branch', 'string', ['limit' => 50, 'null' => false, 'default' => 'master']);

        $table = $this->table('project');
        $table->changeColumn('branch', 'string', ['limit' => 50, 'null' => false, 'default' => 'master']);
    }
}
