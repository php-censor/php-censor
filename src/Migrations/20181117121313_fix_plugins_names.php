<?php

use Phinx\Migration\AbstractMigration;

class FixPluginsNames extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE build_meta SET meta_key = 'php_cpd-warnings' WHERE meta_key = 'phpcpd-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_code_sniffer-warnings' WHERE meta_key = 'phpcs-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_code_sniffer-errors' WHERE meta_key = 'phpcs-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_cs_fixer-warnings' WHERE meta_key = 'phpcsfixer-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_docblock_checker-warnings' WHERE meta_key = 'phpdoccheck-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_loc-data' WHERE meta_key = 'phploc'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_mess_detector-warnings' WHERE meta_key = 'phpmd-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_parallel_lint-errors' WHERE meta_key = 'phplint-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_spec-data' WHERE meta_key = 'phpspec'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_tal_lint-warnings' WHERE meta_key = 'phptallint-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_tal_lint-errors' WHERE meta_key = 'phptallint-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_tal_lint-data' WHERE meta_key = 'phptallint-data'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_unit-data' WHERE meta_key = 'phpunit-data'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_unit-errors' WHERE meta_key = 'phpunit-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'php_unit-coverage' WHERE meta_key = 'phpunit-coverage'");
    }

    public function down()
    {
        $this->execute("UPDATE build_meta SET meta_key = 'phpcpd-warnings' WHERE meta_key = 'php_cpd-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpcs-warnings' WHERE meta_key = 'php_code_sniffer-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpcs-errors' WHERE meta_key = 'php_code_sniffer-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpcsfixer-warnings' WHERE meta_key = 'php_cs_fixer-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpdoccheck-warnings' WHERE meta_key = 'php_docblock_checker-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'phploc' WHERE meta_key = 'php_loc-data'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpmd-warnings' WHERE meta_key = 'php_mess_detector-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'phplint-errors' WHERE meta_key = 'php_parallel_lint-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpspec' WHERE meta_key = 'php_spec-data'");
        $this->execute("UPDATE build_meta SET meta_key = 'phptallint-warnings' WHERE meta_key = 'php_tal_lint-warnings'");
        $this->execute("UPDATE build_meta SET meta_key = 'phptallint-errors' WHERE meta_key = 'php_tal_lint-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'phptallint-data' WHERE meta_key = 'php_tal_lint-data'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpunit-data' WHERE meta_key = 'php_unit-data'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpunit-errors' WHERE meta_key = 'php_unit-errors'");
        $this->execute("UPDATE build_meta SET meta_key = 'phpunit-coverage' WHERE meta_key = 'php_unit-coverage'");
    }
}
