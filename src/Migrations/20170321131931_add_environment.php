<?php

use Phinx\Migration\AbstractMigration;

class AddEnvironment extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('environment');

        if (!$this->hasTable('environment')) {
            $table->create();
        }

        if (!$table->hasColumn('project_id')) {
            $table
                ->addColumn('project_id', 'integer')
                ->save();
        }

        if (!$table->hasColumn('name')) {
            $table
                ->addColumn('name', 'string', ['limit' => 250])
                ->save();
        }

        if (!$table->hasColumn('branches')) {
            $table
                ->addColumn('branches', 'text')
                ->save();
        }

        if (!$table->hasIndex(['project_id', 'name'])) {
            $table
                ->addIndex(['project_id', 'name'])
                ->save();
        }

        $table = $this->table('build');

        if (!$table->hasColumn('environment')) {
            $table
                ->addColumn('environment', 'string', ['limit' => 250])
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('environment');

        if ($this->hasTable('environment')) {
            $table->drop();
        }

        $table = $this->table('build');

        if ($table->hasColumn('environment')) {
            $table
                ->removeColumn('environment')
                ->save();
        }
    }
}
