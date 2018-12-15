<?php

use Phinx\Migration\AbstractMigration;
use PHPCensor\Model\Build;

class AddedSourceColumnToBuildTable extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')
            ->addColumn('source', 'integer', ['default' => Build::SOURCE_UNKNOWN])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build')
            ->removeColumn('source')
            ->save();
    }
}
