<?php

use Phinx\Migration\AbstractMigration;

/**
 * Initial migration to create database.
 */
class InitialMigration extends AbstractMigration
{
    public function up()
    {
        $this->createBuildTable();
        $this->createBuildMetaTable();
        $this->createProjectTable();
        $this->createUserTable();

        // Set up foreign keys:
        $build = $this->table('build');

        if (!$build->hasForeignKey('project_id')) {
            $build->addForeignKey('project_id', 'project', 'id', ['delete'=> 'CASCADE', 'update' => 'CASCADE'])->save();
        }

        $buildMeta = $this->table('build_meta');

        if (!$buildMeta->hasForeignKey('build_id')) {
            $buildMeta->addForeignKey('build_id', 'build', 'id', ['delete'=> 'CASCADE', 'update' => 'CASCADE'])->save();
        }

        if (!$buildMeta->hasForeignKey('project_id')) {
            $buildMeta->addForeignKey('project_id', 'project', 'id', ['delete'=> 'CASCADE', 'update' => 'CASCADE'])->save();
        }
    }

    protected function createBuildTable()
    {
        $table = $this->table('build');

        if (!$this->hasTable('build')) {
            $table->create();
        }

        if (!$table->hasColumn('project_id')) {
            $table->addColumn('project_id', 'integer')->save();
        }

        if (!$table->hasColumn('commit_id')) {
            $table->addColumn('commit_id', 'string', ['limit' => 50])->save();
        }

        if (!$table->hasColumn('status')) {
            $table->addColumn('status', 'integer', ['limit' => 4])->save();
        }

        if (!$table->hasColumn('log')) {
            $table->addColumn('log', 'text')->save();
        }

        if (!$table->hasColumn('branch')) {
            $table->addColumn('branch', 'string', ['limit' => 50])->save();
        }

        if (!$table->hasColumn('created')) {
            $table->addColumn('created', 'datetime')->save();
        }

        if (!$table->hasColumn('started')) {
            $table->addColumn('started', 'datetime')->save();
        }

        if (!$table->hasColumn('finished')) {
            $table->addColumn('finished', 'datetime')->save();
        }

        if (!$table->hasColumn('committer_email')) {
            $table->addColumn('committer_email', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('commit_message')) {
            $table->addColumn('commit_message', 'text')->save();
        }

        if (!$table->hasColumn('extra')) {
            $table->addColumn('extra', 'text')->save();
        }

        if ($table->hasColumn('plugins')) {
            $table->removeColumn('plugins')->save();
        }

        if (!$table->hasIndex(['project_id'])) {
            $table->addIndex(['project_id'])->save();
        }

        if (!$table->hasIndex(['status'])) {
            $table->addIndex(['status'])->save();
        }
    }

    protected function createBuildMetaTable()
    {
        $table = $this->table('build_meta');

        if (!$this->hasTable('build_meta')) {
            $table->create();
        }

        if (!$table->hasColumn('project_id')) {
            $table->addColumn('project_id', 'integer')->save();
        }

        if (!$table->hasColumn('build_id')) {
            $table->addColumn('build_id', 'integer')->save();
        }

        if (!$table->hasColumn('meta_key')) {
            $table->addColumn('meta_key', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('meta_value')) {
            $table->addColumn('meta_value', 'text')->save();
        }

        if (!$table->hasIndex(['build_id', 'meta_key'])) {
            $table->addIndex(['build_id', 'meta_key'])->save();
        }
    }

    protected function createProjectTable()
    {
        $table = $this->table('project');

        if (!$this->hasTable('project')) {
            $table->create();
        }

        if (!$table->hasColumn('title')) {
            $table->addColumn('title', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('reference')) {
            $table->addColumn('reference', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('git_key')) {
            $table->addColumn('git_key', 'text')->save();
        }

        if (!$table->hasColumn('public_key')) {
            $table->addColumn('public_key', 'text')->save();
        }

        if (!$table->hasColumn('type')) {
            $table->addColumn('type', 'string', ['limit' => 50])->save();
        }

        if (!$table->hasColumn('access_information')) {
            $table->addColumn('access_information', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('last_commit')) {
            $table->addColumn('last_commit', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('build_config')) {
            $table->addColumn('build_config', 'text')->save();
        }

        if (!$table->hasColumn('allow_public_status')) {
            $table->addColumn('allow_public_status', 'integer')->save();
        }

        if ($table->hasColumn('token')) {
            $table->removeColumn('token')->save();
        }

        if (!$table->hasIndex(['title'])) {
            $table->addIndex(['title'])->save();
        }
    }

    protected function createUserTable()
    {
        $table = $this->table('user');

        if (!$this->hasTable('user')) {
            $table->create();
        }

        if (!$table->hasColumn('email')) {
            $table->addColumn('email', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('hash')) {
            $table->addColumn('hash', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('name')) {
            $table->addColumn('name', 'string', ['limit' => 250])->save();
        }

        if (!$table->hasColumn('is_admin')) {
            $table->addColumn('is_admin', 'integer')->save();
        }

        if (!$table->hasIndex(['email'])) {
            $table->addIndex(['email'])->save();
        }
    }
}
