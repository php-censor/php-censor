[![PHPCensor](http://phpci.corpsee.com/build-status/image/9?branch=master&label=PHPCensor&style=flat-square)](http://phpci.corpsee.com/build-status/view/9?branch=master)
[![TravisCI](https://img.shields.io/travis/corpsee/php-censor/master.svg?label=TravisCI&style=flat-square)](https://travis-ci.org/corpsee/php-censor?branch=master)
[![Latest Version](https://img.shields.io/packagist/v/corpsee/php-censor.svg?label=Version&style=flat-square)](https://packagist.org/packages/corpsee/php-censor)
[![Total downloads](https://img.shields.io/packagist/dt/corpsee/php-censor.svg?label=Downloads&style=flat-square)](https://packagist.org/packages/corpsee/php-censor)
[![License](https://img.shields.io/packagist/l/corpsee/php-censor.svg?label=License&style=flat-square)](https://packagist.org/packages/corpsee/php-censor)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/26f28bee-a861-45b2-bc18-ed2ac7defd22.svg?label=Insight&style=flat-square)](https://insight.sensiolabs.com/projects/26f28bee-a861-45b2-bc18-ed2ac7defd22)

PHP Censor
==========

**PHP Censor** is a open source self-hosted continuous integration server for PHP projects (Fork of 
[PHPCI](https://www.phptesting.org)).

[![Dashboard](docs/screenshots/dashboard.png)](docs/screenshots/dashboard.png)

More [screenshots](docs/en/screenshots.md).

System requirements
-------------------

* Unix-like OS (**Windows isn't supported**)

* PHP 5.6+ (with OpenSSL support and enabled functions: `exec()`, `shell_exec()` and `proc_open()`)

* Web-server (Nginx or Apache2)

* Database (MySQL/MariaDB or PostgreSQL)

* Beanstalkd queue (Optional)

Features
--------

* Clone project from Github, Bitbucket, Gitlab, Git, Mercurial, SVN or from local directory;

* Set up and tear down database tests for PostgreSQL, MySQL or SQLite;

* Install Composer dependencies;

* Run tests for PHPUnit, Atoum, Behat, Codeception and PHPSpec;

* Check code via Lint, PHPParallelLint, Pdepend, PHPCodeSniffer, PHPCpd, PHPCsFixer, PHPDocblockChecker, PHPLoc, 
PHPMessDetect, PHPTalLint and TechnicalDept;

* Run through any combination of the other [supported plugins](docs/en/README.md), including Campfire, CleanBuild, 
CopyBuild, Deployer, Env, Git, Grunt, Gulp, PackageBuild, Phar, Phing, Shell and Wipe;

* Send notifications on Email, XMPP, Slack, IRC, Flowdock and HipChat;

Configuring
-----------

There are several ways to set up the project:

* Add project without any project config (Runs "zero-config" plugins, including: Composer, TechnicalDept, PHPLoc, 
PHPCpd, PHPCodeSniffer, PHPMessDetect, PHPDocblockChecker, PHPParallelLint, PHPUnit and Codeception);

* Similar to [Travis CI](https://travis-ci.org), to support PHP Censor in your project, you simply need to add a 
`.php-censor.yml` (`phpci.yml`/`.phpci.yml` for backward compatibility with PHPCI) file to the root of your repository;

* Add project config in PHP Censor project page (And it will cancel file config from project repository);

The project config should look something like this:

```yml
setup:
  composer:
    action: "install"
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

More details about [configuring project](docs/en/config.md).

Installing
----------

* Go to the directory in which you want to install PHP Censor, for example: `/var/www`;

* Download PHP Censor from this repository and unzip it (to `/var/www/php-censor.local` for example);

* Go to the PHP Censor directory: `cd /var/www/php-censor.local`;

* Install dependencies using Composer: `composer install`;

* Create empty database for application;

* Install Beanstalkd queue (`aptitude install beanstalkd`);

* Install PHP Censor itself: `./bin/console php-censor:install`;

* [Add a virtual host to your web server](docs/en/virtual_host.md), pointing to the `public` directory within your new
PHP Censor directory. You'll need to set up rewrite rules to point all non-existent requests to PHP Censor;

* [Set up the PHP Censor Worker](docs/en/workers/worker.md), or [a cron-job](docs/en/workers/cron.md) to run PHP
Censor builds;

More details about [installation](docs/en/installing.md).

Updating
--------

* Go to your PHP Censor directory (to `/var/www/php-censor.local` for example);

* Pull the latest code. This would look like this: `git pull`;

* Update the PHP Censor database: `./bin/console php-censor-migrations:migrate`;

* Update the Composer dependencies: `composer install`

Migrations
----------

Run to apply latest migrations:

```bash
cd /path/to/php-censor
./bin/console php-censor-migrations:migrate
```

Run to create new migration:

```bash
cd /path/to/php-censor
./bin/console php-censor-migrations:create NewMigrationName
```

Tests
-----

```bash
cd /path/to/php-censor
./vendor/bin/phpunit
```

For Phar plugin tests set 'phar.readonly' setting to Off (0) in `php.ini` config. Otherwise tests will be skipped.

For database B8Framework tests create empty 'b8_test' database on 'localhost' with user/password: `root/root`.
Otherwise database tests will be skipped.

Documentation
-------------

[Full PHP Censor documentation](docs/en/README.md).

License
-------

PHP Censor is open source software licensed under the [BSD-2-Clause license](LICENSE.md).
