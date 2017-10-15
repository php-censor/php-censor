<?php

use Phinx\Migration\AbstractMigration;

class AddedLanguageAndPerPageForUser extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('user');

        if (!$table->hasColumn('language')) {
            $table
                ->addColumn('language', 'string', ['limit' => 5, 'null' => true])
                ->save();
        }

        if (!$table->hasColumn('per_page')) {
            $table
                ->addColumn('per_page', 'integer', ['null' => true])
                ->save();
        }
    }

    public function down()
    {
        $table = $this->table('user');

        if ($table->hasColumn('language')) {
            $table
                ->removeColumn('language')
                ->save();
        }

        if ($table->hasColumn('per_page')) {
            $table
                ->removeColumn('per_page')
                ->save();
        }
    }
}
