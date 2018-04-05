<?php

use Phinx\Migration\AbstractMigration;

class AddedTagColumnToBuildTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build');

        if (!$table->hasColumn('tag')) {
            $table
                ->addColumn('tag', 'string', ['limit' => 250, 'null' => true])
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('build');

        if ($table->hasColumn('tag')) {
            $table
                ->removeColumn('tag')
                ->save();
        }
    }
}
