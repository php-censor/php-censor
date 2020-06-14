[![PHP Censor](http://ci.php-censor.info/build-status/image/2?branch=master&label=PHPCensor&style=flat-square)](http://ci.php-censor.info/build-status/view/2?branch=master)
[![Travis CI](https://img.shields.io/travis/php-censor/php-censor/master.svg?label=TravisCI&style=flat-square)](https://travis-ci.org/php-censor/php-censor?branch=master)
[![Codecov](https://img.shields.io/codecov/c/github/php-censor/php-censor.svg?label=Codecov&style=flat-square)](https://codecov.io/gh/php-censor/php-censor)
[![Latest Version](https://img.shields.io/packagist/v/php-censor/php-censor.svg?label=Version&style=flat-square)](https://packagist.org/packages/php-censor/php-censor)
[![Total downloads](https://img.shields.io/packagist/dt/php-censor/php-censor.svg?label=Downloads&style=flat-square)](https://packagist.org/packages/php-censor/php-censor)
[![License](https://img.shields.io/packagist/l/php-censor/php-censor.svg?label=License&style=flat-square)](https://packagist.org/packages/php-censor/php-censor)
   
   
<p align="center">
    <img width="500" height="auto" src="docs/images/php-censor-black.png" alt="PHP Censor" />
</p>
   
   
**PHP Censor** is an open source, self-hosted, continuous integration server for PHP projects 
([PHPCI](https://www.phptesting.org) fork). [Official twitter @php_censor](https://twitter.com/php_censor).

Actual PHP Censor versions and release branches:

| Version            | Branch            | Status                            | Minimal PHP Version |
| :----------------- | :-------------    | :-------------------------------- | :------------------ |
| `1.0`              | `release-1.0`     | Last stable version               | `5.6`               |
| `1.1`              | `release-1.1`     | Current stable version            | `5.6`               |
| `1.2`              | `master`          | Future stable minor version (WIP) | `5.6`               |
| `2.0`              | `pre-release-2.0` | Future stable major version (WIP) | `7.1`               |

[![Dashboard](docs/screenshots/dashboard.png)](docs/screenshots/dashboard.png)

More [screenshots](docs/en/screenshots.md).

* [System requirements](#system-requirements)
* [Features](#features)
* [Changelog](#changelog)
* [Roadmap](#roadmap)
* [Installing](#installing)
* [Installing via Docker](#installing-via-docker)
* [Updating](#updating)
* [Configuring project](#configuring-project)
* [Migrations](#migrations)
* [Tests](#tests)
* [Documentation](#documentation)
* [License](#license)

## System requirements

* Unix-like OS (**Windows isn't supported**);

* PHP 7.1+ (with OpenSSL support and enabled functions: `exec()`, `shell_exec()` and `proc_open()`);

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
[Telegram](https://github.com/LEXASOFT/PHP-Censor-Telegram-Plugin);

* Use your LDAP-server for authentication;

## Changelog

[Versions changelog](CHANGELOG.md).

## Roadmap

See [milestones](https://github.com/php-censor/php-censor/milestones).

## Installing

* Go to the directory in which you want to install PHP Censor, for example: `/var/www`:

```bash
cd /var/www
```

* Create project by Composer:

```bash
composer create-project \
    php-censor/php-censor \
    php-censor.local \
    --keep-vcs
```

Or download [latest archive](https://github.com/php-censor/php-censor/releases/latest) from GitHub, unzip it and run 
`composer install`.

* Create an empty database for your application (MySQL/MariaDB or PostgreSQL);

* Install Beanstalkd Queue (Optional, if you are going to use a queue with Worker):

```bash
# For Debian-based
aptitude install beanstalkd
```

* Install PHP Censor itself:

```bash
cd ./php-censor.local

# Interactive installation
./bin/console php-censor:install

# Non-interactive installation
./bin/console php-censor:install \
    --db-type=pgsql \
    --db-host=localhost \
    --db-pgsql-sslmode=prefer \
    --db-name=php-censor \
    --db-user=php-censor \
    --db-password=php-censor \
    --db-port=default \ # Value 'default': 5432 for PostgreSQL and 3306 for MySQL
    --admin-name=admin \
    --admin-password=admin \
    --admin-email='admin@php-censor.local' \
    --queue-host=localhost \
    --queue-port=11300 \
    --queue-name=php-censor

# Non-interactive installation with prepared config.yml file
./bin/console php-censor:install \
    --config-from-file=yes \
    --admin-name=admin \
    --admin-password=admin \
    --admin-email='admin@php-censor.local'
```

* [Add a virtual host to your web server](docs/en/virtual_host.md), pointing to the `public` directory within your new
PHP Censor directory. You'll need to set up rewrite rules to point all non-existent requests to PHP Censor;

* [Set up the PHP Censor Worker](docs/en/workers/worker.md);

## Installing via Docker

If you want to install PHP Censor as a Docker container, you can use 
[php-censor/docker-php-censor](https://github.com/php-censor/docker-php-censor) project.

## Updating

* Go to your PHP Censor directory (to `/var/www/php-censor.local` for example):

    ```bash
    cd /var/www/php-censor.local
    ```

* Pull the latest code from the repository by Git (If you want the latest `master` branch):

    ```bash
    git checkout master
    git pull -r
    ```

    Or pull the latest version:

    ```bash
    git fetch
    git checkout <version>
    ```

* Update the Composer dependencies: `composer install`

* Update the database scheme:

    ```bash
    ./bin/console php-censor-migrations:migrate
    ```

* Restart Supervisord workers (If you use workers and Supervisord):

    ```bash
    sudo supervisorctl status
    sudo supervisorctl restart <worker:worker_00>
    ...
    sudo supervisorctl restart <worker:worker_nn>
    ```
    
    Or restart Systemd workers (If you use workers and Systemd):
    
    ```bash
    sudo systemctl restart <worker@1.service>
    ...
    sudo systemctl restart <worker@n.service>
    ```

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
  email:
    default_mailto_address: admin@php-censor.local
```

More details about [configuring project](docs/en/configuring_project.md).

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
