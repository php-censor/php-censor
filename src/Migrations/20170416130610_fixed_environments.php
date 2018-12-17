<?php

use Phinx\Migration\AbstractMigration;

class FixedEnvironments extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')
            ->changeColumn('environment', 'string', ['limit' => 250, 'null' => true])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build')
            ->changeColumn('environment', 'string', ['limit' => 250])
            ->save();
    }
}
