Документация PHP Censor
=======================

Для начала
----------

* Установка PHP Censor (Смотри [README](../../README.md))
    * [Настройка веб-интерфейса](virtual_host.md)
    * [Запуск сборок с помощью демона (Worker-а)](workers/worker.md)
    * [Запуск сборок с помощью периодической задачи (Cronjob)](workers/cron.md)
* [Настройка проекта для сборки в PHP Censor](configuring_project.md)
* Обновление PHP Censor (Смотри [README](../../README.md))
* [Настройка PHP Censor](configuring_application.md)

Использование
-------------

* Автоматическая сборка при пуше коммитов в репозиторий для:
    * [Git](sources/git.md)
    * [Github (Git)](sources/github.md)
    * [Bitbucket (Git)](sources/bitbucket.md)
    * [GitLab (Git)](sources/gitlab.md)
    * Gogs (Git)
    * Mercurial/Hg
    * Bitbucket (Hg)
    * Subversion/Svn
* [Использование переменных в конфигурации проекта](interpolation.md)
* [Бейдж (изображение) со статусом проекта и публичная страница статуса проекта](status.md)
* [Использование окружений для проекта](environments.md)
* [Периодические сборки](periodical_builds.md)
* [Консольные команды](commands.md)
* [Интеграция с CCMenu/CCTray](ccmenu.md)

Плагины
-------

### Плагины, включенные в поставку

#### Зависимости

* [Composer](plugins/composer.md) - Плагин для работы с зависимостями при помощи Composer-а (`composer`).
* [Security Checker](plugins/security_checker.md) - Плагин для проверки наличия незакрытых уязвимостей в зависимостях 
проекта с помощью SensioLabs Security Checker (`security_checker`).

#### Тесты

* [Atoum](plugins/atoum.md) - `atoum`
* [Behat](plugins/behat.md) - `behat`
* [Codeception](plugins/codeception.md) - `codeception`
* [PHP Unit](plugins/php_unit.md) - `php_unit`
* [PHP Spec](plugins/php_spec.md) - `php_spec`

#### Стиль кода

* [Lint](plugins/lint.md) - `lint`
* [PDepend](plugins/pdepend.md) - `pdepend`
* [PHP Code Sniffer](plugins/php_code_sniffer.md) - `php_code_sniffer`
* [PHP Copy/Paste Detector](plugins/php_cpd.md) - `php_cpd`
* [PHP Coding Standards Fixer](plugins/php_cs_fixes.md) - `php_cs_fixer`
* [PHP Docblock Checker](plugins/php_docblock_checker.md) - `php_docblock_checker`
* [PHP Loc](plugins/php_loc.md) - `php_loc`
* [PHP Mess Detector](plugins/php_mess_detector.md) - `php_mess_detector`
* [PHP Parallel Lint](plugins/php_parallel_lint.md) - `php_parallel_lint`
* PHP Tal Lint - `php_tal_lint`
* Phan - `phan`
* [Technical Debt](plugins/technical_debt.md) - `technical_debt`

#### Базы данных

* [MySQL](plugins/mysql.md) - `mysql`
* [PostgreSQL](plugins/pgsql.md) - `pgsql`
* [SQLite](plugins/sqlite.md) - `sqlite`

#### Деплой

* [Mage](plugins/mage.md) - `mage`
* [Mage v3](plugins/mage3.md) - `mage3`
* [Phing](plugins/phing.md) - `phing`
* [Deployer](plugins/deployer.md) - `deployer`
* [DeployerOrg](plugins/deployer_org.md) - `deployer_org`

#### Оповещение

* [Campfire](plugins/campfire.md) - `campfire`
* [Email](plugins/email.md) - `email`
* FlowDock - `flowdock_notify`
* [HipChat](plugins/hipchat_notify.md) - `hipchat_notify`
* [IRC](plugins/irc.md) - `irc`
* [Slack](plugins/slack_notify.md) - `slack_notify`
* [XMPP](plugins/xmpp.md) - `xmpp`
* [Telegram](plugins/telegram.md) - `telegram`

#### Другое

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
