Changelog 1.3
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


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
