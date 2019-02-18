PHP Censor Documentation
========================

Getting Started
---------------

* [Installing PHP Censor](../../README.md#installing)
    * [Adding a virtual host](virtual_host.md)
    * [Run builds using a worker](workers/worker.md)
    * [Run builds using cronjob](workers/cron.md) **[DEPRECATED]**
* [Adding PHP Censor support to your projects](configuring_project.md)
* [Updating PHP Censor](../../README.md#updating)
* [Configuring PHP Censor](configuring_application.md)

Using PHP Censor
----------------

* Automatically building commits pushed to
    * [Git](sources/git.md)
    * [Github (Git)](sources/github.md)
    * [Bitbucket (Git)](sources/bitbucket.md)
    * [GitLab (Git)](sources/gitlab.md)
    * Gogs (Git)
    * Mercurial/Hg
    * Bitbucket (Hg)
    * Subversion/Svn
* [Injecting variables into messages](interpolation.md)
* [Project Status Images and Status Page](status.md)
* [Build environments](environments.md)
* [Periodical builds](periodical_builds.md)
* [Console commands](commands.md)
* [CCMenu/CCTray integration](ccmenu.md)

Plugins
-------

### Internal Plugins

#### Dependencies

* [Composer](plugins/composer.md) - `composer`
* [Security Checker](plugins/security_checker.md) - SensioLabs Security Checker Plugin (`security_checker`).

#### Tests

* [Atoum](plugins/atoum.md) - `atoum`
* [Behat](plugins/behat.md) - `behat`
* [Codeception](plugins/codeception.md) - `codeception`
* [PHP Unit](plugins/php_unit.md) - `php_unit`
* [PHP Spec](plugins/php_spec.md) - `php_spec`

#### Code Style

* [Lint](plugins/lint.md) - `lint`
* [PDepend](plugins/pdepend.md) - `pdepend`
* [Phan](plugins/phan.md) - `phan`
* [Psalm](plugins/psalm.md) - `psalm`
* [Pahout](plugins/pahout.md) - `pahout`
* [PHP Code Sniffer](plugins/php_code_sniffer.md) - `php_code_sniffer`
* [PHP Copy/Paste Detector](plugins/php_cpd.md) - `php_cpd`
* [PHP Coding Standards Fixer](plugins/php_cs_fixer.md) - `php_cs_fixer`
* [PHP Docblock Checker](plugins/php_docblock_checker.md) - `php_docblock_checker`
* [PHP Loc](plugins/php_loc.md) - `php_loc`
* [PHP Mess Detector](plugins/php_mess_detector.md) - `php_mess_detector`
* [PHP Parallel Lint](plugins/php_parallel_lint.md) - `php_parallel_lint`
* [PHP Tal Lint](plugins/php_tal_lint.md) - `php_tal_lint`
* [Technical Debt](plugins/technical_debt.md) - `technical_debt`
* [SensioLabs Insight](plugins/sensiolabs_insight.md) - `sensiolabs_insight`

#### Databases

* [MySQL](plugins/mysql.md) - `mysql`
* [PostgreSQL](plugins/pgsql.md) - `pgsql`
* [SQLite](plugins/sqlite.md) - `sqlite`

#### Deployment

* [Mage](plugins/mage.md) - `mage`
* [Mage v3](plugins/mage3.md) - `mage3`
* [Phing](plugins/phing.md) - `phing`
* [Deployer](plugins/deployer.md) - `deployer`
* [DeployerOrg](plugins/deployer_org.md) - `deployer_org`

#### Notifications

* [Campfire](plugins/campfire.md) - `campfire`
* [Email](plugins/email.md) - `email`
* [FlowDock](plugins/flowdock_notify.md) - `flowdock_notify`
* [HipChat](plugins/hipchat_notify.md) - `hipchat_notify`
* [IRC](plugins/irc.md) - `irc`
* [Slack](plugins/slack_notify.md) - `slack_notify`
* [XMPP](plugins/xmpp.md) - `xmpp`
* [Telegram](plugins/telegram.md) - `telegram`

#### Other

* [Clean Build](plugins/clean_build.md) - `clean_build`
* [Copy Build](plugins/copy_build.md) - `copy_build`
* [Env](plugins/env.md) - `env`
* [Git](plugins/git.md) - `git`
* [Grunt](plugins/grunt.md) - `grunt`
* [Gulp](plugins/gulp.md) - `gulp`
* [Package Build](plugins/package_build.md) - `package_build`
* [Phar](plugins/phar.md) - `phar`
* [Shell](plugins/shell.md) - `shell`
* [Wipe](plugins/wipe.md) - `wipe`
