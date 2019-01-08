<?php


use Phinx\Migration\AbstractMigration;

class AddBuildErrorsTotals extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build')
            ->addColumn('errors_total', 'integer', ['null' => true])
            ->addColumn('errors_total_previous', 'integer', ['null' => true])
            ->addColumn('errors_new', 'integer', ['null' => true])
            ->save();
    }

    public function down()
    {
        $this
            ->table('build')
            ->removeColumn('errors_total')
            ->removeColumn('errors_total_previous')
            ->removeColumn('errors_new')
            ->save();
    }
}
