<?php

use Phinx\Migration\AbstractMigration;

class AddedTagColumnToBuildTable extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')
            ->addColumn('tag', 'string', ['limit' => 250, 'null' => true])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build')
            ->removeColumn('tag')
            ->save();
    }
}
