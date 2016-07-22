Run Builds Using Cron
---------------------

Running builds using cron is a quick and simple method of getting up and running with PHP Censor. It also removes the need for PHP Censor to be running all the time.

If you want a little more control over how PHP Censor runs, you may want to [set up the PHP Censor daemon](workers/daemon.md) instead.

Setting up the Cron Job
=======================

You'll want to set up PHP Censor to run as a regular cronjob, so run `crontab -e` and enter the following:

```sh
* * * * * /usr/bin/php /path/to/php-censor/console php-censor:run-builds
```

**Note:** Make sure you change the `/path/to/php-censor` to the directory in which you installed PHP Censor, and update the PHP path if necessary.
