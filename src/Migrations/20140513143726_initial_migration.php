<?php

use Phinx\Migration\AbstractMigration;

/**
 * Initial migration to create database.
 */
class InitialMigration extends AbstractMigration
{
    public function up()
    {
        $build     = $this->table('build');
        $buildMeta = $this->table('build_meta');

        $build
            ->addColumn('project_id', 'integer')
            ->addColumn('commit_id', 'string', ['limit' => 50])
            ->addColumn('status', 'integer', ['limit' => 4])
            ->addColumn('log', 'text')
            ->addColumn('branch', 'string', ['limit' => 50])
            ->addColumn('created', 'datetime')
            ->addColumn('started', 'datetime')
            ->addColumn('finished', 'datetime')
            ->addColumn('committer_email', 'string', ['limit' => 250])
            ->addColumn('commit_message', 'text')
            ->addColumn('extra', 'text')

            ->addIndex(['project_id'])
            ->addIndex(['status'])

            ->save();

        $buildMeta
            ->addColumn('project_id', 'integer')
            ->addColumn('build_id', 'integer')
            ->addColumn('meta_key', 'string', ['limit' => 250])
            ->addColumn('meta_value', 'text')

            ->addIndex(['build_id', 'meta_key'])

            ->save();

        $this
            ->table('project')

            ->addColumn('title', 'string', ['limit' => 250])
            ->addColumn('reference', 'string', ['limit' => 250])
            ->addColumn('git_key', 'text')
            ->addColumn('public_key', 'text')
            ->addColumn('type', 'string', ['limit' => 50])
            ->addColumn('access_information', 'string', ['limit' => 250])
            ->addColumn('last_commit', 'string', ['limit' => 250])
            ->addColumn('build_config', 'text')
            ->addColumn('allow_public_status', 'integer')

            ->addIndex(['title'])

            ->save();

        $this
            ->table('user')

            ->addColumn('email', 'string', ['limit' => 250])
            ->addColumn('hash', 'string', ['limit' => 250])
            ->addColumn('name', 'string', ['limit' => 250])
            ->addColumn('is_admin', 'integer')

            ->addIndex(['email'])

            ->save();

        $build
            ->addForeignKey(
                'project_id',
                'project',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->save();

        $buildMeta
            ->addForeignKey(
                'build_id',
                'build',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->addForeignKey(
                'project_id',
                'project',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->save();
    }

    public function down()
    {
        $build     = $this->table('build');
        $buildMeta = $this->table('build_meta');

        $build
            ->dropForeignKey('project_id')
            ->save();

        $buildMeta
            ->dropForeignKey('build_id')
            ->dropForeignKey('project_id')
            ->save();

        $build
            ->drop()
            ->save();

        $buildMeta
            ->drop()
            ->save();

        $this
            ->table('project')
            ->drop()
            ->save();

        $this
            ->table('user')
            ->drop()
            ->save();
    }
}
