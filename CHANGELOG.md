Changelog 2.0
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [2.0.0 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.0) (2021-01-10)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.3.0...2.0.0)

### [How tp upgrade from v1 to v2](/docs/UPGRADE_2.0.md)

### Changed

- **Minimal PHP version increased to 7.4 (from 5.6)**.

### Removed

- **Deprecations from versions 1.x**:
    - Cronjob worker: `php-censor:run-builds` (Use daemon worker instead: `php-censor:worker`).
    - Project configs `phpci.yml` and `.phpci.yml` (use `.php-censor.yml` instead).
    - `PHPCI_*` interpolation and env variables (Use `PHP_CENSOR_*` instead).
    - Global application config section `b8.database` (Use `php-censor.database` instead).
    - Options `authToken`, `api_key`, `api_token`, `token` for several plugins: `CampfireNotify`, 
`HipchatNotify`, `FlowdockNotify`, `TelegramNotify`, `SensiolabInsight`, `BitbucketNotify` (Use `auth_token` instead).
    - Plugin names: `campfire`, `telegram`, `xmpp`, `email`, `irc`, `phpstan` (Use: `campfire_notify`, `telegram_notify`, 
`xmpp_notify`, `email_notify`, `irc_notify`, `php_stan` instead).
    - [Codeception] Option `path` (Use option `output_path` instead). 
    - [Codeception] Option `executable` (Use the options `binary_path` and `binary_name` instead).
    - [Grunt] Option `grunt` (Use options `binary_path` and `binary_name` instead).
    - [Gulp] Option `gulp` (Use options `binary_path` and `binary_name` instead).
    - [PHPCodeSniffer] Option `path` (Use option `directory` instead).
    - [PHPCpd] Option `path` (Use option `directory` instead).
    - [PHPDocblockChecker] Option `path` (Use option `directory` instead).
    - [PHPMessDetector] Option `path` (Use option `directory` instead).
    - [PHPUnit] Option `directory` (Use option `directories` instead).
    - [SensiolabsInsight] Option `executable` (Use the options `binary_path` and `binary_name` instead).
    - [Shell] Option `command` and commands list without any named option. Use option `commands` instead.
    - [PackageBuild] Special variables for plugin (`%build.commit%`, `%build.id%`, `%build.branch%`, `%project.title%`, `%date%` and `%time%`). Use interpolated variables instead (`%COMMIT_ID%`, `%BUILD_ID%`, `%BRANCH%`, `%PROJECT_TITLE%`, `%CURRENT_DATE%`, `CURRENT_TIME`).
    - [MySQL and PostgreSQL] Options `pass` for plugins MySQL and PostgreSQL. Use option `password` instead.
    - [MySQL, PostgreSQL, SQLite] Queries list without option for plugins MySQL, PostgreSQL and SQLite. Use the options `queries` instead.
    - [MySQL] Imports list without option for plugin MySQL. Use the options `imports` instead.
    - [Mage, Mage3] Section `mage` and `mage3` in the global application config and option `bin`. Use the plugin options `binary_path` and `binary_name` instead.
    - [CampfireNotify] Variable `%buildurl%` (Use the variable `%BUILD_LINK%` instead).

## Other versions

- [0.x Changelog](/docs/CHANGELOG_0.x.md)
- [1.0 Changelog](/docs/CHANGELOG_1.0.md)
- [1.1 Changelog](/docs/CHANGELOG_1.1.md)
- [1.2 Changelog](/docs/CHANGELOG_1.2.md)
- [1.3 Changelog](/docs/CHANGELOG_1.3.md)
