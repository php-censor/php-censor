<?php

use Phinx\Migration\AbstractMigration;

class FixedProjectDefaultBranch2 extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE project SET default_branch = 'default' WHERE (type = 'hg' OR type = 'bitbucket-hg') AND (default_branch = '' OR default_branch IS NULL)");
        $this->execute("UPDATE project SET default_branch = 'trunk' WHERE type = 'svn' AND (default_branch = '' OR default_branch IS NULL)");
        $this->execute("UPDATE project SET default_branch = 'master' WHERE default_branch = '' OR default_branch IS NULL");
    }

    public function down()
    {
    }
}
