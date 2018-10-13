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

### Internal plugins

#### Dependencies

* [Composer](plugins/composer.md) - `composer`
* [Security Checker](plugins/security_checker.md) - SensioLabs Security Checker Plugin (`security_checker`).

#### Tests

* [Atoum](plugins/atoum.md) - `atoum`
* [Behat](plugins/behat.md) - `behat`
* [Codeception](plugins/codeception.md) - `codeception`
* [PHP Unit](plugins/php_unit.md) - `php_unit`
* [PHP Spec](plugins/php_spec.md) - `php_spec`

#### Code style

* [Lint](plugins/lint.md) - `lint`
* [PDepend](plugins/pdepend.md) - `pdepend`
* [Phan](plugins/phan.md) - `phan`
* [PHP Code Sniffer](plugins/php_code_sniffer.md) - `php_code_sniffer`
* [PHP Copy/Paste Detector](plugins/php_cpd.md) - `php_cpd`
* [PHP Coding Standards Fixer](plugins/php_cs_fixer.md) - `php_cs_fixer`
* [PHP Docblock Checker](plugins/php_docblock_checker.md) - `php_docblock_checker`
* [PHP Loc](plugins/php_loc.md) - `php_loc`
* [PHP Mess Detector](plugins/php_mess_detector.md) - `php_mess_detector`
* [PHP Parallel Lint](plugins/php_parallel_lint.md) - `php_parallel_lint`
* PHP Tal Lint - `php_tal_lint`
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

#### Notifications

* [Campfire](plugins/campfire.md) - `campfire`
* [Email](plugins/email.md) - `email`
* [FlowDock](plugins/flowdock_notify.md) - `flowdock_notify`
* [HipChat](plugins/hipchat_notify.md) - `hipchat_notify`
* [IRC](plugins/irc.md) - `irc`
* [Slack](plugins/slack_notify.md) - `slack_notify`
* [XMPP](plugins/xmpp.md) - `xmpp`

#### Other

* [Clean Build](plugins/clean_build.md) - `clean_build`
* [Copy Build](plugins/copy_build.md) - `copy_build`
* [Env](plugins/env.md) - `env`
* Git - `git`
* [Grunt](plugins/grunt.md) - `grunt`
* Gulp - `gulp`
* [Package Build](plugins/package_build.md) - `package_build`
* [Phar](plugins/phar.md) - `phar`
* [Shell](plugins/shell.md) - `shell`
* Wipe - `wipe`

### Third-party plugins

* [Telegram](https://github.com/php-censor/php-censor-telegram-plugin) - Telegram plugin by 
[@LEXASOFT](https://github.com/LEXASOFT)
* [Deployer](https://github.com/php-censor/php-censor-deployer-plugin) - Plugin for [Deployer](http://deployer.org) by 
[@ketchoop](https://github.com/ketchoop)
