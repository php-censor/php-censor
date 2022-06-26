Changelog 2.0
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [2.0.10 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.10) (2022-06-26)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.9...2.0.10)

### Fixed

- Updated dependencies. Fixed:
    - `guzzlehttp/guzzle` (6.5.7) | CVE-2022-31090: CURLOPT_HTTPAUTH option not cleared on change of origin | https://github.com/guzzle/guzzle/security/advisories/GHSA-25mq-v84q-4j7r

    - `guzzlehttp/guzzle` (6.5.7) | CVE-2022-31091: Change in port should be considered a change in origin https://github.com/guzzle/guzzle/security/advisories/GHSA-q559-8m2m-g699


## [2.0.9 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.9) (2022-06-11)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.8...2.0.9)

### Fixed

- Updated dependencies. Fixed:
  - `guzzlehttp/guzzle` (6.5.6) | CVE-2022-31042: Failure to strip the Cookie header on change in host or HTTP downgrade | https://github.com/guzzle/guzzle/security/advisories/GHSA-f2wf-25xc-69c9

  - `guzzlehttp/guzzle` (6.5.6) | CVE-2022-31043: Fix failure to strip Authorization header on HTTP downgrade | https://github.com/guzzle/guzzle/security/advisories/GHSA-w248-ffj2-4v5q

### Changed

- Added secrets to PHP Censor CI config (`.php-censor.yml`).


## [2.0.8 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.8) (2022-06-08)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.7...2.0.8)

### Fixed

- Updated dependencies. Fixed:
  - `guzzlehttp/guzzle` (6.5.5) | CVE-2022-29248: Cross-domain cookie leakage | https://github.com/guzzle/guzzle/security/advisories/GHSA-cwmx-hcrq-mhc3.

  - `guzzlehttp/psr7` (1.8.3) | CVE-2022-24775: Inproper parsing of HTTP headers | https://github.com/guzzle/psr7/security/advisories/GHSA-q7rv-6hp3-vh96.


## [2.0.7 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.7) (2022-01-19)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.6...2.0.7)

### Fixed

- **[PhpCsFixer]** Problems with `udiff` option.


## [2.0.6 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.6) (2021-12-19)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.5...2.0.6)

### Fixed

- **[Codeception]** Updated Codeception version (See: [CVE-2021-23420](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-23420)).

### Changed

- Several documentation improvements.
- Improved code style.


## [2.0.5 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.5) (2021-08-22)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.4...2.0.5)

### Fixed

- Bug with wrong type when field "access_information" is empty (null).
- **[PhpCsFixer]** Support for version 3.0+. Pull request [#414](https://github.com/php-censor/php-censor/pull/414).
  Thanks to [@StudioMaX](https://github.com/StudioMaX).
- **[Mysql, Pgsql, Sqlite]** Variables interpolation for queries. Pull requests
  [#415](https://github.com/php-censor/php-censor/pull/415), [#416](https://github.com/php-censor/php-censor/pull/416).
  Thanks to [@KieranFYI](https://github.com/KieranFYI).

### Removed

- Useless TravisCI and CodeCov configs.


## [2.0.4 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.4) (2021-06-12)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.3...2.0.4)

### Fixed

- **[PhpStan]** Option `directories` and `directory`. 
Issue [#408](https://github.com/php-censor/php-censor/issues/#408). Pull request
[#409](https://github.com/php-censor/php-censor/pull/409). Thanks to [@StudioMaX](https://github.com/StudioMaX).
- **[SecurityChecker]** Option `allowed_warnings`.
- Security issue with old Chart.js version (Chart.js upgraded from version `1.1.1` to `3.3.0`).


## [2.0.3 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.3) (2021-04-20)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.2...2.0.3)

### Fixed

- **[Mage, Mage3, DeployerOrg]** Options `binary_path`, `priority_path` for Mage/Mage3/DeployerOrg plugins.
  Pull request [#406](https://github.com/php-censor/php-censor/pull/406). Thanks to [@gnomii](https://github.com/gnomii).


## [2.0.2 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.2) (2021-03-21)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.1...2.0.2)

### Fixed

- SSH keys generating (Removed unwanted symbols). Issue [#403](https://github.com/php-censor/php-censor/issues/#403).
- Environments (Case when you may get environment from another project). Issue
[#405](https://github.com/php-censor/php-censor/issues/#405).
- Localizations for "Notify" plugins.
- Deprecations from PHP 8.0. Pull request [#404](https://github.com/php-censor/php-censor/pull/404). Thanks to 
[@ismaail](https://github.com/ismaail).

### Changed

- **[SecurityChecker]** Reimplement the plugin because package `sensiolabs/security-checker` was archived/abandoned
(See [README](https://github.com/sensiolabs/security-checker#sensiolabs-security-checker)). Now plugin uses `symfony`
binary (Symfony CLI) or `fabpot/local-php-security-checker` tool for working. See
[documentation](https://github.com/php-censor/php-censor/blob/release-1.3/docs/en/plugins/security_checker.md).

### Removed

- Useless empty doc file about cronjob.
- Useless empty ru doc pages.


## [2.0.1 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.1) (2021-01-17)

[Full Changelog](https://github.com/php-censor/php-censor/compare/2.0.0...2.0.1)

### Fixed

- **[PhpCpd]** Param "--names-exclude" for plugin PhpCpd (version 6+). Issue
  [#401](https://github.com/php-censor/php-censor/issues/#401).

### Changed

- Added `.phpunit.result.cache` file to `.gitignore`.
- Improved `CHANGELOG.md`.
- Improved `.php-censor.yml` config.


## [2.0.0 (Rick Sanchez)](https://github.com/php-censor/php-censor/tree/2.0.0) (2021-01-10)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.3.0...2.0.0)

### [How to upgrade from v1 to v2](/docs/UPGRADE_2.0.md)

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
