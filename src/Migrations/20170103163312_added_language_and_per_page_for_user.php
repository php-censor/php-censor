<?php

use Phinx\Migration\AbstractMigration;

class AddedLanguageAndPerPageForUser extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('user')

            ->addColumn('language', 'string', ['limit' => 5, 'null' => true])
            ->addColumn('per_page', 'integer', ['null' => true])

            ->save();
    }

    public function down()
    {
        $this
            ->table('user')

            ->removeColumn('language')
            ->removeColumn('per_page')

            ->save();
    }
}
