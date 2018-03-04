<?php

use Phinx\Migration\AbstractMigration;

class RenamedBuildTypes extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE project SET type = 'git' WHERE type = 'remote'");
        $this->execute("UPDATE project SET type = 'bitbucket-hg' WHERE type = 'bitbuckethg'");
    }

    public function down()
    {
        $this->execute("UPDATE project SET type = 'remote' WHERE type = 'git'");
        $this->execute("UPDATE project SET type = 'bitbuckethg' WHERE type = 'bitbucket-hg'");
    }
}
