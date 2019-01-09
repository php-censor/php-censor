<?php

use Phinx\Migration\AbstractMigration;

class ErrorsTable extends AbstractMigration
{
    public function up()
    {
        $this
            ->table('build_error')

            ->addColumn('build_id', 'integer')
            ->addColumn('plugin', 'string', ['limit' => 100])
            ->addColumn('file', 'string', ['limit' => 250, 'null' => true])
            ->addColumn('line_start', 'integer', ['null' => true])
            ->addColumn('line_end', 'integer', ['null' => true])
            ->addColumn('severity', 'integer', ['limit' => 255])
            ->addColumn('message', 'string', ['limit' => 250])
            ->addColumn('created_date', 'datetime')

            ->addIndex(['build_id', 'created_date'])

            ->addForeignKey(
                'build_id',
                'build',
                'id',
                ['delete'=> 'CASCADE', 'update' => 'CASCADE']
            )

            ->save();
    }

    public function down()
    {
        $this
            ->table('build_error')
            ->dropForeignKey('build_id')
            ->save();

        $this
            ->table('build_error')
            ->drop()
            ->save();
    }
}
