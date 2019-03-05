<?php


use Phinx\Migration\AbstractMigration;

class AddedBuildParentId extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')
            ->addColumn('parent_id', 'integer', ['default' => 0])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build')
            ->removeColumn('parent_id')
            ->update();
    }
}
