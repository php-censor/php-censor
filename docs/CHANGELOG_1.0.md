# Change Log


The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [1.0.16 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.16) (2020-06-14)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.15...1.0.16)

### Fixed

- Deprecation error on PHP 7.4 by Phinx (Phinx version upgraded from version 0.10 to 0.11). Issue 
[#363](https://github.com/php-censor/php-censor/issues/363).

### Changed

- Updated dependencies.
- Improved change log markup.


## [1.0.15 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.15) (2020-04-26)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.14...1.0.15)

### Fixed

- Old builds removing for MySQL. Pull request [#352](https://github.com/php-censor/php-censor/pull/352). Thanks to 
[@garas](https://github.com/garas).
- [TechnicalDept] Replace curly braces with square brackets for `PHP 7.4` compatibility. Pull request 
[#351](https://github.com/php-censor/php-censor/pull/351). Thanks to [@benr77](https://github.com/benr77).
- [Git] Option `actions`.

### Changed

- Updated dependencies.


## [1.0.14 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.14) (2019-12-25)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.13...1.0.14)

### Added

- TravisCI configuration for `PHP 7.4`.

### Fixed

- Atoum plugin output.
- Sqlite plugin queries option.

### Changed

- Updated dependencies (Fixed [CVE-2019-18889]( https://symfony.com/cve-2019-18889): Forbid 
serializing AbstractAdapter and TagAwareAdapter instances in `symfony/cache:v3.4.34`).


## [1.0.13 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.13) (2019-11-12)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.12...1.0.13)

### Fixed

- PHPCodesniffer for installation on PHP 7.3 (PHPCodesniffer version updated to v3.5 from v3.2). Issue 
[#334](https://github.com/php-censor/php-censor/issues/334). Pull 
request [#335](https://github.com/php-censor/php-censor/pull/335). Thanks to [@xl32](https://github.com/xl32).
- Bitbucket webhook for removing branch event. Issue [#337](https://github.com/php-censor/php-censor/issues/337).

### Changed

- Updated dependencies.


## [1.0.12 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.12) (2019-10-30)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.11...1.0.12)

### Fixed

- TravisCI config.
- Migrations output during installation. Issue [#315](https://github.com/php-censor/php-censor/issues/315).
- Option `binary_path`. Issue [#318](https://github.com/php-censor/php-censor/issues/318).

### Changed

- Updated dependencies.


## [1.0.11 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.11) (2019-06-15)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.10...1.0.11)

### Added

- `FUNDING.yml` config for GitHub.

### Fixed

- Potential bug with `proc_open` function parameter `cwd`.

### Changed

- Updated dependencies.


## [1.0.10 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.10) (2019-05-18)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.9...1.0.10)

### Fixed

- Phing plugin property `directory`. Issue [#304](https://github.com/php-censor/php-censor/issues/304).

### Removed

- Useless `.htaccess.dist` file from `public` directory. Issue 
[#305](https://github.com/php-censor/php-censor/issues/305).


## [1.0.9 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.9) (2019-05-12)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.8...1.0.9)

### Fixed

Phing plugin property `directory`. Issue #304.

### Changed

- Improved default value of installation URL for support both http-protocols (`http` and `https`). Issue 
[#303](https://github.com/php-censor/php-censor/issues/303).


## [1.0.8 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.8) (2019-04-26)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.7...1.0.8)

### Fixed

- [CVE-2019-10910](https://symfony.com/cve-2019-10910) and  [CVE-2019-10912](https://symfony.com/cve-2019-10912) in 
Symfony components (Updated components versions).

### Changed

- Renamed PHPUnit config (`phpunit.xml` -> `phpunit.xml.dist`).


## [1.0.7 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.7) (2019-03-30)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.6...1.0.7)

### Added

- Optional port for queue (`Pheanstalk`) and fixed documentation about worker setting up. Issue 
[#288](https://github.com/php-censor/php-censor/issues/288).

### Fixed

- Config path for PHPCodeSniffer config. Issue [#287](https://github.com/php-censor/php-censor/issues/287).
- GitHub sources links for errors with only one line.

### Changed

- Improved code style.
- Improved documentation (About configuring projects).


## [1.0.6 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.6) (2019-03-06)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.5...1.0.6)

### Added

- Information about actual releases and release branches to `README.md`.

### Fixed

- Validation for fields `project.access_information` and `build.extra` in models `Project` and `Build`.

### Changed

- Improved code style.


## [1.0.5 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.5) (2019-02-10)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.4...1.0.5)

### Fixed

- Overload for plugin options: "directory" and "binary_path". Issue 
[#270](https://github.com/php-censor/php-censor/issues/270).
- Models creation for cases when we have more columns in DB then model fields (Case: new feature with new columns in 
the another branch). Issue [#270](https://github.com/php-censor/php-censor/issues/270).
- Guzzle version for correct Slack plugin working. Issue [#270](https://github.com/php-censor/php-censor/issues/270).
- Behavior of application config option `email_settings.from_address` for case when `from_address` like 
`test@test.test` without user name (Now the addresses like `test@test.test` will be transform automatically to format: 
`PHP Censor <test@test.test>`). Issue [#270](https://github.com/php-censor/php-censor/issues/270).

### Changed

- Improved documentation for plugins. Issue [#271](https://github.com/php-censor/php-censor/issues/271). Pull requests 
[#275](https://github.com/php-censor/php-censor/pull/275), [#273](https://github.com/php-censor/php-censor/pull/273), 
[#274](https://github.com/php-censor/php-censor/pull/274). Thanks to [@benr77](https://github.com/benr77).


## [1.0.4 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.4) (2019-02-02)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.3...1.0.4)

### Fixed

- Calls of the `chdir` command in plugins. Issue [#264](https://github.com/php-censor/php-censor/issues/264).
- Errors trend for the first build.

### Changed

- Improved documentation. Pull request [#267](https://github.com/php-censor/php-censor/pull/267). Thanks to 
[@ptejada](https://github.com/ptejada).


## [1.0.3 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.3) (2019-01-27)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.2...1.0.3)

### Fixed

- Errors trend processing (total errors count and previous build errors count).
- Rebuild without debug for builds with debug.
- PhpCodeSniffer and PhpMessDetector plugins output for non-debug mode.
- Codeception plugin config (codeception.yml) path. Issue [#262](https://github.com/php-censor/php-censor/issues/262).
- Paths with symlinks for plugins.
- Arrow icon for build errors trend for pending/running builds (Arrow removed).
- Method `getDiffLineNumber` for case errors without file (`$file = NULL`).


## [1.0.2 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.2) (2019-01-13)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.1...1.0.2)

### Fixed

- MySQL column types after updating Phinx version. Issue [#239](https://github.com/php-censor/php-censor/issues/239).


## [1.0.1 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.1) (2019-01-09)

[Full Changelog](https://github.com/php-censor/php-censor/compare/1.0.0...1.0.1)

### Fixed

- Migrations for MySQL. Issue [#249](https://github.com/php-censor/php-censor/issues/249).


## [1.0.0 (Morty Smith)](https://github.com/php-censor/php-censor/tree/1.0.0) (2019-01-08)

[Full Changelog](https://github.com/php-censor/php-censor/compare/0.25.0...1.0.0)

### Added

- Total errors trend to the dashboard for builds.
- `PHP_CENSOR_*` env variables (Like `PHPCI_*`).
- Several missing interpolation variables (`%PHPCI_BRANCH%`, `%PHPCI_BRANCH_URI%`, `%PHPCI_ENVIRONMENT%`).
- **A lot of notices about deprecated features for version 1.0 (It will be delete in version 2.0): cronjob worker 
(Use worker instead), `phpci.yml`/`.phpci.yml` configs (Use `.php-censor.yml` instead), a lot of plugin options, 
`PHPCI_*` interpolation and env variables etc.**

### Fixed

- Wrong namespace in BuildInterpolator for PHP version 5.6.
- Wrong namespace in PHPSpec plugin constructor.


## Other versions

- [0.x change log](/docs/CHANGELOG_0.x.md)
