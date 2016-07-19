Installing PHPCI
----------------

What you'll need
================

* PHP 5.4.0 or above
* A web server (Nginx or Apache)
* [Composer](https://getcomposer.org/download/)
* [Git](http://git-scm.com/downloads)
* A MySQL server to connect to.
* The following functions need to be enabled: `exec()`, `shell_exec()` and `proc_open()` in php.ini.
* PHP must have OpenSSL support enabled.

Installing PHPCI from Composer
==============================

* Go to the directory in which you want to install PHPCI, for example: `/var/www`
* Download Composer if you haven't already: `curl -sS https://getcomposer.org/installer | php`
* Download PHPCI: `./composer.phar create-project block8/phpci phpci --keep-vcs --no-dev`
* Go to the newly created PHPCI directory, and install Composer dependencies: `cd phpci && ../composer.phar install`
* Run the PHPCI installer: `./console phpci:install`
* [Add a virtual host to your web server](virtual_host.md), pointing to the `public` directory within your new PHPCI directory. You'll need to set up rewrite rules to point all non-existent requests to PHPCI.
* [Set up the PHPCI Worker](workers/worker.md), or you can run builds using the [PHPCI daemon](workers/daemon.md) or [a cron-job](workers/cron.md) to run PHPCI builds.

Installing PHPCI Manually
=========================

* Go to the directory in which you want to install PHPCI, for example: `/var/www`
* [Download PHPCI](https://github.com/Block8/PHPCI/releases/latest) and unzip it.
* Go to the PHPCI directory: `cd /var/www/phpci`
* Install dependencies using Composer: `composer install`
* Install PHPCI itself: `php ./console phpci:install`
* [Add a virtual host to your web server](virtual_host.md), pointing to the `public` directory within your new PHPCI directory. You'll need to set up rewrite rules to point all non-existent requests to PHPCI.
* [Set up the PHPCI Worker](workers/worker.md), or you can run builds using the [PHPCI daemon](workers/daemon.md) or [a cron-job](workers/cron.md) to run PHPCI builds.
