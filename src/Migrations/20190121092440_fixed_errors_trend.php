<?php

use Phinx\Migration\AbstractMigration;

class FixedErrorsTrend extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE build SET errors_total = NULL");
        $this->execute("UPDATE build SET errors_total_previous = NULL");
    }

    public function down()
    {
    }
}
