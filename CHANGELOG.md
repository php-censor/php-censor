Changelog 1.3
=============

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [1.3.0 (Summer Smith)](https://github.com/php-censor/php-censor/tree/1.3.0) (2020-10-18)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.2.4...1.3.0)

### Added

- Initial Bahasa Indonesia (ID) localization. Pull request [#378](https://github.com/php-censor/php-censor/pull/378). 
Thanks to [@masitings](https://github.com/masitings).
- New variable for interpolation: `%SYSTEM_VERSION%` (Env: `%PHP_CENSOR_SYSTEM_VERSION%`) with PHP Censor installation 
version. Issue [#374](https://github.com/php-censor/php-censor/issues/374).

### Changed

- Improved Brazilian Portuguese (pt-BR) localization. Issue [#348](https://github.com/php-censor/php-censor/issues/348).
Pull request [#375](https://github.com/php-censor/php-censor/pull/375). Thanks to 
[@flavioheleno](https://github.com/flavioheleno).
- Improved Spanish (ES) localization. Issue [#344](https://github.com/php-censor/php-censor/issues/344). Pull request 
[#373](https://github.com/php-censor/php-censor/pull/373). Thanks to [@ptejada](https://github.com/ptejada).
- Renamed all notification plugins to form with a suffix `_notify` (With backward compatibility) and **deprecated old 
names without the suffix `_notify`** (`campfire` -> `campfire_notify`, `telegram` -> `telegram_notify`, `xmpp` 
-> `xmpp_notify`, `email` -> `email_notify`, `irc` -> `irc_notify`). Issue 
[#376](https://github.com/php-censor/php-censor/issues/376).


## Other versions

- [0.x Changelog](/docs/CHANGELOG_0.x.md)
- [1.0 Changelog](/docs/CHANGELOG_1.0.md)
- [1.1 Changelog](/docs/CHANGELOG_1.1.md)
- [1.2 Changelog](/docs/CHANGELOG_1.2.md)
