PHP Censor
----------

PHP Censor is a fork of PHPCI (And B8Framework). PHP Censor is a free and open source (BSD-2-Clause license) continuous
integration tool specifically designed for PHP. We've  built it with simplicity in mind, so whilst it doesn't
do *everything* Jenkins can do, it is a breeze to set up and use.

What it does
============

* Clones your project from Github, Bitbucket or a local path
* Allows you to set up and tear down test databases.
* Installs your project's Composer dependencies.
* Runs through any combination of the [supported plugins](docs/README.md)).
* You can mark directories for the plugins to ignore.
* You can mark certain plugins as being allowed to fail (but still run.)

What it doesn't do (yet)
========================

* Virtualised testing.
* Multiple PHP-version tests.
* Install PEAR or PECL extensions.
* Deployments - We strongly recommend using [Deployer](http://phpdeployment.org)

Documentation
=============

[PHP Censor documentation](docs/README.md)

License
=======

The PHP Censor is open source software licensed under the BSD-2-Clause license.
