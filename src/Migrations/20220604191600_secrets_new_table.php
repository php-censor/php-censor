<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SecretsNewTable extends AbstractMigration
{
    public function up()
    {
        $secrets = $this->table('secrets');
        $payloadOptions = [];

        $secrets
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('value', 'text', $payloadOptions)
            ->addColumn('create_date', 'datetime')
            ->addColumn('user_id', 'integer', ['null' => true])

            ->addIndex(['name'], ['unique' => true])

            ->save();

        $secrets
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->save();
    }

    public function down()
    {
        $secrets = $this->table('secrets');

        $secrets
            ->drop()
            ->save();
    }
}
