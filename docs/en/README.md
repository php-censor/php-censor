PHP Censor documentation
========================

Getting Started
---------------

* Installing PHP Censor (See [README](../../README.md))
    * [Adding a Virtual Host](virtual_host.md)
    * [Run builds using a worker](workers/worker.md)
    * [Run builds using cronjob](workers/cron.md)
* [Adding PHP Censor Support to Your Projects](configuring_project.md)
* Updating PHP Censor (See [README](../../README.md))
* [Configuring PHP Censor](configuring.md)

Using PHP Censor
----------------

* Automatically building commits pushed to
    * [Github](sources/github.md)
    * [Bitbucket](sources/bitbucket.md)
    * [GitLab](sources/gitlab.md)
    * [Git](sources/git.md)
* [Injecting variables into messages](interpolation.md)
* [Project Status Images and Status Page](status.md)
* [Build environments](environments.md)

Plugins
-------

### Internal plugins

* [Atoum](plugins/atoum.md) - `atoum`
* [Behat](plugins/behat.md) - `behat`
* [Campfire](plugins/campfire.md) - `campfire`
* [Clean Build](plugins/clean_build.md) - `clean_build`
* [Codeception](plugins/codeception.md) - `codeception`
* [Composer](plugins/composer.md) - `composer`
* [Copy Build](plugins/copy_build.md) - `copy_build`
* [Deployer](plugins/deployer.md) - `deployer`
* [Email](plugins/email.md) - `email`
* [Env](plugins/env.md) - `env`
* [Grunt](plugins/grunt.md) - `grunt`
* [Hipchat](plugins/hipchat_notify.md) - `hipchat_notify`
* [IRC](plugins/irc.md) - `irc`
* [Lint](plugins/lint.md) - `lint`
* [Mage](plugins/mage.md) - `mage`
* [Mage v3](plugins/mage3.md) - `mage3`
* [MySQL](plugins/mysql.md) - `mysql`
* [Package Build](plugins/package_build.md) - `package_build`
* [PDepend](plugins/pdepend.md) - `pdepend`
* [PostgreSQL](plugins/pgsql.md) - `pgsql`
* [SQLite](plugins/sqlite.md) - `sqlite`
* [Phar](plugins/phar.md) - `phar`
* [Phing](plugins/phing.md) - `phing`
* [PHP Code Sniffer](plugins/php_code_sniffer.md) - `php_code_sniffer`
* [PHP Copy/Paste Detector](plugins/php_cpd.md) - `php_cpd`
* [PHP Coding Standards Fixer](plugins/php_cs_fixes.md) - `php_cs_fixer`
* [PHP Docblock Checker](plugins/php_docblock_checker.md) - `php_docblock_checker`
* [PHP Loc](plugins/php_loc.md) - `php_loc`
* [PHP Mess Detector](plugins/php_mess_detector.md) - `php_mess_detector`
* [PHP Parallel Lint](plugins/php_parallel_lint.md) - `php_parallel_lint`
* [PHP Spec](plugins/php_spec.md) - `php_spec`
* [PHP Unit](plugins/php_unit.md) - `php_unit`
* [Shell](plugins/shell.md) - `shell`
* [Slack](plugins/slack_notify.md) - `slack_notify`
* [Technical Debt](plugins/technical_debt.md) - `technical_debt`
* [Security Checker](plugins/security_checker.md) - SensioLabs Security Checker Plugin (`security_checker`).
* [XMPP](plugins/xmpp.md) - `xmpp`

### Third-party plugins

* [Telegram](https://github.com/LEXASOFT/PHP-Censor-Telegram-Plugin) - Telegram plugin by 
[@LEXASOFT](https://github.com/LEXASOFT)
* [Deployer](https://github.com/ket4yii/phpcensor-deployer-plugin) - Plugin for [Deployer](http://deployer.org) by 
[@ket4yii](https://github.com/ket4yii)
