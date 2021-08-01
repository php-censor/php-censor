Upgrade from v1 to v2
=====================

1. [Upgrade your PHP Censor installation to latest v1 release](https://github.com/php-censor/php-censor/blob/release-1.3/README.md#updating) 
(`1.3.*`).
2. If you use [cronjob worker](https://github.com/php-censor/php-censor/blob/release-1.3/docs/en/workers/cron.md), you 
should migrate to [daemon worker](en/workers/worker.md).
3. Fix all deprecations from v1 on your installation:
    * You should rename `phpci.yml` or `.phpci.yml` project configs to `.php-censor.yml`.
    * You should rename `PHPCI_*` variables to `PHP_CENSOR_*`.
    * You should rename `b8.database` section of application config to `php-censor.database`.
    * Etc... ([See v2.0.0 changelog](https://github.com/php-censor/php-censor/releases/tag/2.0.0)).
4. [Upgrade your PHP Censor installation to v2](../README.md#updating) (`2.0.0`).
5. (Optional) You may remove manually DB table `migration` (In v2 uses new table `migrations`).
