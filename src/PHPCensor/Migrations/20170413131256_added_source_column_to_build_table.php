<?php

use Phinx\Migration\AbstractMigration;
use PHPCensor\Model\Build;

class AddedSourceColumnToBuildTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build');

        if (!$table->hasColumn('source')) {
            $table
                ->addColumn('source', 'integer', ['default' => Build::SOURCE_UNKNOWN])
                ->save();

            $this->execute("UPDATE build SET source = 4");
            $this->execute("UPDATE build SET source = 1, commit_id = '', commit_message = '' WHERE commit_id = 'Manual'");
            $this->execute("UPDATE build SET source = 1, commit_message = '' WHERE commit_message = 'Manual'");
        }
    }

    public function down()
    {
        $table = $this->table('build');

        if ($table->hasColumn('source')) {
            $table
                ->removeColumn('source')
                ->save();
        }
    }
}
