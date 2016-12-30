Installing PHP Censor
---------------------

What you'll need
================

* PHP 5.4.0 or above
* A web server (Nginx or Apache)
* [Composer](https://getcomposer.org/download/)
* [Git](http://git-scm.com/downloads)
* A MySQL server to connect to.
* The following functions need to be enabled: `exec()`, `shell_exec()` and `proc_open()` in php.ini.
* PHP must have OpenSSL support enabled.

Installing PHP Censor from Composer
===================================

* Go to the directory in which you want to install PHP Censor, for example: `/var/www`
* Download Composer if you haven't already: `curl -sS https://getcomposer.org/installer | php`
* Download PHP Censor: `./composer.phar create-project corpsee/php-censor php-censor --keep-vcs --no-dev`
* Go to the newly created PHP Censor directory, and install Composer dependencies: `cd php-censor && ../composer.phar install`
* Run the PHP Censor installer: `./bin/console php-censor:install`
* [Add a virtual host to your web server](virtual_host.md), pointing to the `public` directory within your new PHP Censor directory. You'll need to set up rewrite rules to point all non-existent requests to PHP Censor.
* [Set up the PHP Censor Worker](workers/worker.md), or you can run builds using the [daemon](workers/daemon.md) or [a cron-job](workers/cron.md) to run PHP Censor builds.

Installing PHP Censor Manually
==============================

* Go to the directory in which you want to install PHP Censor, for example: `/var/www`
* [Download PHP Censor](https://github.com/corpsee/php-censor/releases/latest) and unzip it.
* Go to the PHP Censor directory: `cd /var/www/php-censor`
* Install dependencies using Composer: `composer install`
* Install PHP Censor itself: `./bin/console php-censor:install`
* [Add a virtual host to your web server](virtual_host.md), pointing to the `public` directory within your new PHP Censor directory. You'll need to set up rewrite rules to point all non-existent requests to PHP Censor.
* [Set up the PHP Censor Worker](workers/worker.md), or you can run builds using the [daemon](workers/daemon.md) or [a cron-job](workers/cron.md) to run PHP Censor builds.
