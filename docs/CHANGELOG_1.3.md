Changelog 1.3
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [1.3.5 (Jerry Smith)](https://github.com/php-censor/php-censor/tree/1.3.5) (2021-08-22)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.3.4...1.3.5)

### Fixed

- **[PhpCsFixer]** Support for version 3.0+. Pull request [#414](https://github.com/php-censor/php-censor/pull/414).
  Thanks to [@StudioMaX](https://github.com/StudioMaX).
- **[Mysql, Pgsql, Sqlite]** Variables interpolation for queries. Pull requests
  [#415](https://github.com/php-censor/php-censor/pull/415), [#416](https://github.com/php-censor/php-censor/pull/416).
  Thanks to [@KieranFYI](https://github.com/KieranFYI).

### Removed

- Useless TravisCI and CodeCov configs.


## [1.3.4 (Jerry Smith)](https://github.com/php-censor/php-censor/tree/1.3.4) (2021-06-12)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.3.3...1.3.4)

### Fixed

- **[PhpStan]** Option `directories` and `directory`.
  Issue [#408](https://github.com/php-censor/php-censor/issues/#408). Pull request
  [#409](https://github.com/php-censor/php-censor/pull/409). Thanks to [@StudioMaX](https://github.com/StudioMaX).
- **[SecurityChecker]** Option `allowed_warnings`.
- Security issue with old Chart.js version (Chart.js upgraded from version `1.1.1` to `3.3.0`).


## [1.3.3 (Jerry Smith)](https://github.com/php-censor/php-censor/tree/1.3.3) (2021-04-20)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.3.2...1.3.3)

### Fixed

- **[Mage, Mage3, DeployerOrg]** Options `binary_path`, `priority_path` for Mage/Mage3/DeployerOrg plugins.
  Pull request [#406](https://github.com/php-censor/php-censor/pull/406). Thanks to [@gnomii](https://github.com/gnomii).


## [1.3.2 (Jerry Smith)](https://github.com/php-censor/php-censor/tree/1.3.2) (2021-03-21)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.3.1...1.3.2)

### Fixed

- SSH keys generating (Removed unwanted symbols). Issue [#403](https://github.com/php-censor/php-censor/issues/#403).
- Environments (Case when you may get environment from another project). Issue
  [#405](https://github.com/php-censor/php-censor/issues/#405).
- Localizations for "Notify" plugins.

### Changed

- **[SecurityChecker]** Reimplement the plugin because package `sensiolabs/security-checker` was archived/abandoned
  (See [README](https://github.com/sensiolabs/security-checker#sensiolabs-security-checker)). Now plugin uses `symfony`
  binary (Symfony CLI) or `fabpot/local-php-security-checker` tool for working. See
  [documentation](https://github.com/php-censor/php-censor/blob/release-1.3/docs/en/plugins/security_checker.md).


## [1.3.1 (Jerry Smith)](https://github.com/php-censor/php-censor/tree/1.3.1) (2021-01-17)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.3.0...1.3.1)

### Fixed

- **[PhpCpd]** Param "--names-exclude" for plugin PhpCpd (version 6+). Issue
  [#401](https://github.com/php-censor/php-censor/issues/#401).

### Changed

- Added `.phpunit.result.cache` file to `.gitignore`.
- Improved `CHANGELOG.md`.
- Improved `.php-censor.yml` config.


## [1.3.0 (Jerry Smith)](https://github.com/php-censor/php-censor/tree/1.3.0) (2021-01-02)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.2.4...1.3.0)

### Added

- **[CampfireNotify]** Missing option `verbose`. Issue [#386](https://github.com/php-censor/php-censor/issues/#386).
- Initial Bahasa Indonesia (ID) localization. Pull request [#378](https://github.com/php-censor/php-censor/pull/378).
  Thanks to [@masitings](https://github.com/masitings).
- New variable for interpolation: `%SYSTEM_VERSION%` (Env: `%PHP_CENSOR_SYSTEM_VERSION%`) with PHP Censor installation
  version. Issue [#374](https://github.com/php-censor/php-censor/issues/374).

### Changed

- **[PhpStan]** Renamed PhpStan plugin from `phpstan` to `php_stan` and deprecated old name. Issue
  [#390](https://github.com/php-censor/php-censor/issues/#390).
- **[Codeception]** Deprecated option `executable` (Use the options `binary_path` and `binary_name` instead).
  Issue [#394](https://github.com/php-censor/php-censor/issues/#394).
- **[SensiolabsInsight]** Deprecated option `executable` (Use the options `binary_path` and `binary_name` instead).
  Issue [#388](https://github.com/php-censor/php-censor/issues/#388).
- **[CampfireNotify]** Deprecated variable `%buildurl%` (Use the variable `%BUILD_LINK%` instead).
  Issue [#387](https://github.com/php-censor/php-censor/issues/#387).
- Improved auth options names (`authToken`, `api_key`, `api_token`, `token` -> `auth_token`) for several plugins
  (`CampfireNotify`, `HipchatNotify`, `FlowdockNotify`, `TelegramNotify`, `SensiolabInsight`, `BitbucketNotify`) and
  deprecated old names. Issue [#389](https://github.com/php-censor/php-censor/issues/#389).
- Improved Brazilian Portuguese (pt-BR) localization. Issue [#348](https://github.com/php-censor/php-censor/issues/348).
  Pull request [#375](https://github.com/php-censor/php-censor/pull/375). Thanks to
  [@flavioheleno](https://github.com/flavioheleno).
- Improved Spanish (ES) localization. Issue [#344](https://github.com/php-censor/php-censor/issues/344). Pull request
  [#373](https://github.com/php-censor/php-censor/pull/373). Thanks to [@ptejada](https://github.com/ptejada).
- Renamed all notification plugins to form with a suffix `_notify` (With backward compatibility) and **deprecated old
  names without the suffix `_notify`** (`campfire` -> `campfire_notify`, `telegram` -> `telegram_notify`, `xmpp`
  -> `xmpp_notify`, `email` -> `email_notify`, `irc` -> `irc_notify`). Issue
  [#376](https://github.com/php-censor/php-censor/issues/376).
- Improved branches dropdown on project page (Added scroll). Issue
  [#397](https://github.com/php-censor/php-censor/issues/#397).


## Other versions

- [0.x Changelog](/docs/CHANGELOG_0.x.md)
- [1.0 Changelog](/docs/CHANGELOG_1.0.md)
- [1.1 Changelog](/docs/CHANGELOG_1.1.md)
- [1.2 Changelog](/docs/CHANGELOG_1.2.md)
