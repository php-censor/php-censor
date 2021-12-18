[![PHP Censor](http://ci.php-censor.info/build-status/image/2?branch=master&label=PHPCensor&style=flat-square)](http://ci.php-censor.info/build-status/view/2?branch=master)
[![Latest Version](https://img.shields.io/packagist/v/php-censor/php-censor.svg?label=Version&style=flat-square)](https://packagist.org/packages/php-censor/php-censor)
[![Total downloads](https://img.shields.io/packagist/dt/php-censor/php-censor.svg?label=Downloads&style=flat-square)](https://packagist.org/packages/php-censor/php-censor)
[![License](https://img.shields.io/packagist/l/php-censor/php-censor.svg?label=License&style=flat-square)](https://packagist.org/packages/php-censor/php-censor)
   
   
<p align="center">
    <img width="500" height="auto" src="docs/images/php-censor-black.png" alt="PHP Censor" />
</p>
   
   
**PHP Censor** is an open source, self-hosted, continuous integration server for PHP projects 
([PHPCI](https://www.phptesting.org) fork). [Official twitter @php_censor](https://twitter.com/php_censor).

PHP Censor versions:

| Version              | Latest   | Branch        |                                Status                                 | Minimal PHP Version |
| :------------------: | :------: | :-----------: |:---------------------------------------------------------------------:| :-----------------: |
| `1.0` (Morty Smith)  | `1.0.16` | `release-1.0` |                     Old version (**UNSUPPORTED**)                     | `>=5.6, <8.0`       |
| `1.1` (Birdperson)   | `1.1.6`  | `release-1.1` |                     Old version (**UNSUPPORTED**)                     | `>=5.6, <8.0`       |
| `1.2` (Summer Smith) | `1.2.4`  | `release-1.2` |                     Old version (**UNSUPPORTED**)                     | `>=5.6, <8.0`       |
| `1.3` (Jerry Smith)  | `1.3.5`  | `release-1.3` |                     Old version (**UNSUPPORTED**)                     | `>=5.6, <8.0`       |
| `2.0` (Rick Sanchez) | `2.0.5`  | `release-2.0` | Current stable version ([Upgrade from v1 to v2](docs/UPGRADE_2.0.md)) | `>=7.4`             |
| `2.1`                | WIP      | `master`      |                      Feature minor version (WIP)                      | `>=7.4`             |

[![Dashboard](docs/screenshots/dashboard.png)](docs/screenshots/dashboard.png)

More [screenshots](docs/en/screenshots.md).

* [System requirements](#system-requirements)
* [Features](#features)
* [Changelog](#changelog)
* [Roadmap](#roadmap)
* [Installing](#installing)
* [Updating](#updating)
* [Configuring project](#configuring-project)
* [Migrations](#migrations)
* [Code style](#code-style)
* [Tests](#tests)
* [Documentation](#documentation)
* [License](#license)

## System requirements

* Unix-like OS (**Windows isn't supported**);

* PHP 7.4+ (with OpenSSL support and enabled functions: `exec()`, `shell_exec()` and `proc_open()`);

* Web-server (Nginx or Apache2);

* Database (MySQL/MariaDB or PostgreSQL);

* Beanstalkd queue;

## Features

* Clone project from [GitHub](docs/en/sources/github.md), [Bitbucket](docs/en/sources/bitbucket.md) (Git/Hg), 
[GitLab](docs/en/sources/gitlab.md), [Git](docs/en/sources/git.md), Hg (Mercurial), SVN (Subversion) or from local 
directory;

* Set up and tear down database tests for [PostgreSQL](docs/en/plugins/pgsql.md), [MySQL](docs/en/plugins/mysql.md) or 
[SQLite](docs/en/plugins/sqlite.md);

* Install [Composer](docs/en/plugins/composer.md) dependencies;

* Run tests for PHPUnit, Atoum, Behat, Codeception and PHPSpec;

* Check code via Lint, PHPParallelLint, Pdepend, PHPCodeSniffer, PHPCpd, PHPCsFixer, PHPDocblockChecker, PHPLoc, 
PHPMessDetector, PHPTalLint and TechnicalDebt;

* Run through any combination of the other [supported plugins](docs/en/README.md#plugins), including Campfire, 
CleanBuild, CopyBuild, Deployer, Env, Git, Grunt, Gulp, PackageBuild, Phar, Phing, Shell and Wipe;

* Send notifications to Email, XMPP, Slack, IRC, Flowdock, HipChat and 
[Telegram](docs/en/plugins/telegram_notify.md);

* Use your LDAP-server for authentication;

## Changelog

[Versions changelog](CHANGELOG.md).

## Roadmap

See [milestones](https://github.com/php-censor/php-censor/milestones).

## Installing

See [Installing](docs/en/installing.md) section in documentation;

## Updating

See [Updating](docs/en/updating.md) section in documentation;

## Configuring project

There are several ways to set up the project:

* Add project without any project config (Runs "zero-config" plugins, including: Composer, TechnicalDebt, PHPLoc, 
PHPCpd, PHPCodeSniffer, PHPMessDetector, PHPDocblockChecker, PHPParallelLint, PHPUnit and Codeception);

* Similar to [Travis CI](https://travis-ci.org), to support PHP Censor in your project, you simply need to add a 
`.php-censor.yml` file to the root of your repository;

* Add project config in PHP Censor project page (And it will cancel file config from project repository);

The project config should look something like this:

```yml
setup:
  composer:
    action:    "install"
    directory: "."
test:
  php_unit:
    config: "phpunit.xml"
  php_mess_detector:
    allow_failures: true
  php_code_sniffer:
    standard: "PSR2"
  php_cpd:
    allow_failures: true
complete:
  email_notify:
    default_mailto_address: admin@php-censor.local
```

More details about [configuring project](docs/en/configuring_project.md) in documentation.

## Migrations

Run to apply latest migrations:

```bash
cd /path/to/php-censor
./bin/console php-censor-migrations:migrate
```

Run to create a new migration:

```bash
cd /path/to/php-censor
./bin/console php-censor-migrations:create NewMigrationName
```

## Code style

```bash
cd /path/to/php-censor

./vendor/bin/php-cs-fixer fix --allow-risky=yes
```

## Tests

```bash
cd /path/to/php-censor

./vendor/bin/phpunit --configuration ./phpunit.xml.dist --coverage-html ./tests/runtime/coverage -vvv --colors=always
```

For Phar plugin tests set 'phar.readonly' setting to Off (0) in `php.ini` config. Otherwise the tests will be skipped.  

For database tests create an empty 'test_db' database on 'localhost' with user/password: `root/<empty>` 
for MySQL and with user/password: `postgres/<empty>` for PostgreSQL (You can change default test user, password and 
database name in `phpunit.xml[.dist]` config constants). If connection failed the tests will be skipped.

## Documentation

[Full PHP Censor documentation](docs/en/README.md).

## License

PHP Censor is open source software licensed under the [BSD-2-Clause license](LICENSE).
