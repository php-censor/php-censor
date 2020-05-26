<?php

use Phinx\Migration\AbstractMigration;

class FixedDatabase extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE build SET create_date = '2016-06-23 00:00:00' WHERE create_date IS NULL");
        $this->execute("UPDATE project SET create_date = '2016-06-23 00:00:00' WHERE create_date IS NULL");
        $this->execute("UPDATE project_group SET create_date = '2016-06-23 00:00:00' WHERE create_date IS NULL");

        $build        = $this->table('build');
        $buildError   = $this->table('build_error');
        $buildMeta    = $this->table('build_meta');
        $environment  = $this->table('environment');
        $project      = $this->table('project');
        $projectGroup = $this->table('project_group');
        $user         = $this->table('user');

        $build
            ->addColumn('environment_id', 'integer', ['null' => true])

            ->changeColumn('create_date', 'datetime')
            ->changeColumn('user_id', 'integer', ['null' => true])
            ->changeColumn('parent_id', 'integer', ['null' => true])

            ->save();

        $this->execute("UPDATE build SET user_id = NULL WHERE user_id = 0");
        $this->execute("UPDATE build SET parent_id = NULL WHERE parent_id = 0");

        $projectsItems = $this->fetchAll("SELECT id FROM project");
        foreach ($projectsItems as $projectItem) {
            $environments = $this->fetchAll("SELECT id, name FROM environment WHERE project_id = " . (int)$projectItem['id']);
            foreach ($environments as $environment) {
                $this->execute("UPDATE build SET environment_id = " . (int)$environment['id'] . " WHERE environment = '{$environment['name']}' AND project_id = " . (int)$projectItem['id']);
            }
        }

        $build
            ->removeColumn('environment')

            ->save();

        $project
            ->changeColumn('create_date', 'datetime')
            ->changeColumn('user_id', 'integer', ['null' => true])

            ->save();

        $this->execute("UPDATE project SET user_id = NULL WHERE user_id = 0");

        $projectGroup
            ->changeColumn('create_date', 'datetime')
            ->changeColumn('user_id', 'integer', ['null' => true])

            ->save();

        $this->execute("UPDATE project_group SET user_id = NULL WHERE user_id = 0");

        $build
            ->addForeignKey(
                'user_id',
                'user',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->addForeignKey(
                'parent_id',
                'build',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )
            ->addForeignKey(
                'environment_id',
                'environment',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )

            ->save();

        $project
            ->addForeignKey(
                'user_id',
                'user',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )

            ->save();

        $projectGroup
            ->addForeignKey(
                'user_id',
                'user',
                'id',
                ['delete' => 'SET NULL', 'update' => 'CASCADE']
            )

            ->save();

        $environment
            ->addForeignKey(
                'project_id',
                'project',
                'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE']
            )

            ->save();

        $build->rename('builds')->save();
        $buildError->rename('build_errors')->save();
        $buildMeta->rename('build_metas')->save();
        $environment->rename('environments')->save();
        $project->rename('projects')->save();
        $projectGroup->rename('project_groups')->save();
        $user->rename('users')->save();

        if ('pgsql' === $this->getAdapter()->getAdapterType()) {
            $this->execute("ALTER SEQUENCE build_id_seq RENAME TO builds_id_seq");
            $this->execute("ALTER SEQUENCE build_error_id_seq RENAME TO build_errors_id_seq");
            $this->execute("ALTER SEQUENCE build_meta_id_seq RENAME TO build_metas_id_seq");
            $this->execute("ALTER SEQUENCE environment_id_seq RENAME TO environments_id_seq");
            $this->execute("ALTER SEQUENCE project_id_seq RENAME TO projects_id_seq");
            $this->execute("ALTER SEQUENCE project_group_id_seq RENAME TO project_groups_id_seq");
            $this->execute("ALTER SEQUENCE user_id_seq RENAME TO users_id_seq");
        }
    }

    public function down()
    {
    }
}
