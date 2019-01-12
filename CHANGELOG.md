# Change Log


The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to 
[Semantic Versioning](http://semver.org/spec/v2.0.0.html).


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
`PHPCi_*` interpolation and env variables etc.**

### Fixed

- Wrong namespace in BuildInterpolator for PHP version 5.6.
- Wrong namespace in PHPSpec plugin constructor.


## Other versions

- [0.x change log](/docs/CHANGELOG_0.x.md)
