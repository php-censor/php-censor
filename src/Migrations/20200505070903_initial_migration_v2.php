<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;
use PHPCensor\Model\Build;

class InitialMigrationV2 extends AbstractMigration
{
    private const LATEST_V1_MIGRATION_NAME = 'FixedDatabase';

    private function getLatestV1Migration()
    {
        return $this->fetchRow(\sprintf(
            "SELECT * FROM migration WHERE migration_name = '%s' LIMIT 1",
            self::LATEST_V1_MIGRATION_NAME
        ));
    }

    private function isNewInstallationUp(): bool
    {
        $isNewInstallation = !$this->hasTable('build');
        if (!$isNewInstallation && !$this->getLatestV1Migration()) {
            throw new \RuntimeException(
                'You should upgrade your PHP Censor to latest 1.2 release before you can upgrade it to release 2.0'
            );
        }

        return $isNewInstallation;
    }

    private function isNewInstallationDown(): bool
    {
        if ($this->getLatestV1Migration()) {
            return false;
        }

        return true;
    }

    public function up()
    {
        if (!$this->isNewInstallationUp()) {
            return;
        }

        $builds        = $this->table('builds');
        $buildMetas    = $this->table('build_metas');
        $buildErrors   = $this->table('build_errors');
        $projects      = $this->table('projects');
        $projectGroups = $this->table('project_groups');
        $users         = $this->table('users');
        $environments  = $this->table('environments');

        $databaseType          = $this->getAdapter()->getAdapterType();
        $buildLogOptions       = ['null' => true];
        $buildMetaValueOptions = [];
        if ('mysql' === $databaseType) {
            $buildLogOptions['limit']       = MysqlAdapter::TEXT_LONG;
            $buildMetaValueOptions['limit'] = MysqlAdapter::TEXT_LONG;
        }

        $builds
            ->addColumn('project_id', 'integer')
            ->addColumn('commit_id', 'string', ['limit' => 50])
            ->addColumn('status', 'integer', ['limit' => 4])
            ->addColumn('log', 'text', $buildLogOptions)
            ->addColumn('branch', 'string', ['limit' => 250])
            ->addColumn('tag', 'string', ['limit' => 250, 'null' => true])
            ->addColumn('create_date', 'datetime')
            ->addColumn('start_date', 'datetime', ['null' => true])
            ->addColumn('finish_date', 'datetime', ['null' => true])
            ->addColumn('committer_email', 'string', ['limit' => 512, 'null' => true])
            ->addColumn('commit_message', 'text', ['null' => true])
            ->addColumn('extra', 'text', ['null' => true])
            ->addColumn('environment_id', 'integer', ['null' => true])
            ->addColumn('source', 'integer', ['default' => Build::SOURCE_UNKNOWN])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('errors_total', 'integer', ['null' => true])
            ->addColumn('errors_total_previous', 'integer', ['null' => true])
            ->addColumn('errors_new', 'integer', ['null' => true])
            ->addColumn('parent_id', 'integer', ['null' => true])

            ->addIndex(['project_id'])
            ->addIndex(['status'])

            ->save();

        $buildMetas
            ->addColumn('build_id', 'integer')
            ->addColumn('meta_key', 'string', ['limit' => 250])
            ->addColumn('meta_value', 'text', $buildMetaValueOptions)

            ->addIndex(['build_id', 'meta_key'])

            ->save();

        $buildErrors
            ->addColumn('build_id', 'integer')
            ->addColumn('plugin', 'string', ['limit' => 100])
            ->addColumn('file', 'string', ['limit' => 250, 'null' => true])
            ->addColumn('line_start', 'integer', ['null' => true])
            ->addColumn('line_end', 'integer', ['null' => true])
            ->addColumn('severity', 'integer', ['limit' => 255])
            ->addColumn('message', 'text')
            ->addColumn('create_date', 'datetime')
            ->addColumn('hash', 'string', ['limit' => 32, 'default' => ''])
            ->addColumn('is_new', 'boolean', ['default' => false])

            ->addIndex(['build_id', 'create_date'])
            ->addIndex(['hash'])

            ->save();

        $environments
            ->addColumn('project_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 250])
            ->addColumn('branches', 'text')

            ->addIndex(['project_id', 'name'])

            ->save();

        $projects
            ->addColumn('title', 'string', ['limit' => 250])
            ->addColumn('reference', 'string', ['limit' => 250])
            ->addColumn('default_branch', 'string', ['limit' => 250])
            ->addColumn('ssh_private_key', 'text', ['null' => true])
            ->addColumn('ssh_public_key', 'text', ['null' => true])
            ->addColumn('access_information', 'string', ['limit' => 250, 'null' => true])
            ->addColumn('allow_public_status', 'integer', ['default' => 0])
            ->addColumn('type', 'string', ['limit' => 50])
            ->addColumn('build_config', 'text', ['null' => true])
            ->addColumn('archived', 'boolean', ['default' => false])
            ->addColumn('group_id', 'integer', ['default' => 1])
            ->addColumn('default_branch_only', 'integer', ['default' => 0])
            ->addColumn('create_date', 'datetime')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('overwrite_build_config', 'integer', ['default' => 1])

            ->addIndex(['title'])

            ->save();

        $projectGroups
            ->addColumn('title', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('create_date', 'datetime')
            ->addColumn('user_id', 'integer', ['null' => true])

            ->save();

        $users
            ->addColumn('email', 'string', ['limit' => 250])
            ->addColumn('hash', 'string', ['limit' => 250])
            ->addColumn('name', 'string', ['limit' => 250])
            ->addColumn('is_admin', 'integer', ['default' => 0])
            ->addColumn('provider_key', 'string', ['default' => 'internal'])
            ->addColumn('provider_data', 'string', ['null'  => true])
            ->addColumn('language', 'string', ['limit' => 5, 'null' => true])
            ->addColumn('per_page', 'integer', ['null' => true])
            ->addColumn('remember_key', 'string', ['limit' => 32, 'null' => true])

            ->addIndex(['email'], ['unique' => true])
            ->addIndex(['name'])

            ->save();

        $builds
            ->addForeignKey(
                'project_id',
                'projects',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->addForeignKey(
                'parent_id',
                'builds',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->addForeignKey(
                'environment_id',
                'environments',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->save();

        $buildMetas
            ->addForeignKey(
                'build_id',
                'builds',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->save();

        $buildErrors
            ->addForeignKey(
                'build_id',
                'builds',
                'id',
                ['delete'=> 'CASCADE', 'update' => 'CASCADE']
            )
            ->save();

        $projects
            ->addForeignKey(
                'group_id',
                'project_groups',
                'id',
                ['delete' => 'RESTRICT', 'update' => 'CASCADE']
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->save();

        $projectGroups
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->save();

        $environments
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
        if (!$this->isNewInstallationDown()) {
            return;
        }

        $builds        = $this->table('builds');
        $buildMetas    = $this->table('build_metas');
        $buildErrors   = $this->table('build_errors');
        $projects      = $this->table('projects');
        $projectGroups = $this->table('project_groups');
        $users         = $this->table('users');
        $environments  = $this->table('environments');

        $builds
            ->dropForeignKey('project_id')
            ->dropForeignKey('user_id')
            ->dropForeignKey('parent_id')
            ->dropForeignKey('environment_id')
            ->save();

        $buildMetas
            ->dropForeignKey('build_id')
            ->save();

        $buildErrors
            ->dropForeignKey('build_id')
            ->save();

        $projects
            ->dropForeignKey('group_id')
            ->dropForeignKey('user_id')
            ->save();

        $projectGroups
            ->dropForeignKey('user_id')
            ->save();

        $environments
            ->dropForeignKey('project_id')
            ->save();

        $builds
            ->drop()
            ->save();

        $buildMetas
            ->drop()
            ->save();

        $buildErrors
            ->drop()
            ->save();

        $environments
            ->drop()
            ->save();

        $projects
            ->drop()
            ->save();

        $projectGroups
            ->drop()
            ->save();

        $users
            ->drop()
            ->save();
    }
}
