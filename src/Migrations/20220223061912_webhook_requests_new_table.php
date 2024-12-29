<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class WebhookRequestsNewTable extends AbstractMigration
{
    public function up()
    {
        $webhookRequests = $this->table('webhook_requests');
        $payloadOptions = ['null' => true];

        $webhookRequests
            ->addColumn('project_id', 'integer')
            ->addColumn('webhook_type', 'string', ['limit' => 50])
            ->addColumn('payload', 'text', $payloadOptions)
            ->addColumn('create_date', 'datetime')

            ->addIndex(['project_id'])

            ->save();

        $webhookRequests
            ->addForeignKey(
                'project_id',
                'projects',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->save();
    }

    public function down()
    {
        $webhookRequests = $this->table('webhook_requests');

        $webhookRequests
            ->dropForeignKey('project_id')
            ->save();

        $webhookRequests
            ->drop()
            ->save();
    }
}
