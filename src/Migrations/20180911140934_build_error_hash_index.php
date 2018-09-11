<?php

use Phinx\Migration\AbstractMigration;

class BuildErrorHashIndex extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('build_error');

        if (!$table->hasIndex(['hash'])) {
            $table->addIndex(['hash'])->save();
        }
    }

    public function down()
    {
        $table = $this->table('build_error');

        if ($table->hasIndex(['hash'])) {
            $table->removeIndex(['hash'])->save();
        }
    }
}
