<?php

use Phinx\Migration\AbstractMigration;

class AddEnvironment extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('environment')

            ->addColumn('project_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 250])
            ->addColumn('branches', 'text')

            ->addIndex(['project_id', 'name'])

            ->save();

        $this
            ->table('build')
            ->addColumn('environment', 'string', ['limit' => 250])
            ->save();
    }

    public function down()
    {
        $this
            ->table('environment')
            ->drop()
            ->save();

        $this
            ->table('build')
            ->removeColumn('environment')
            ->save();
    }
}
