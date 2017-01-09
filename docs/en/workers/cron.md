Run builds using cronjob
========================

Running builds using cron is a quick and simple method of getting up and running with PHP Censor. It also removes the need for PHP Censor to be running all the time.

If you want a little more control over how PHP Censor runs, you may want to [set up the daemon](workers/daemon.md) instead.

Setting up the Cron Job
-----------------------

You'll want to set up PHP Censor to run as a regular cronjob, so run `crontab -e` and enter the following:

```sh
SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

* * * * * /path/to/php-censor/bin/console php-censor:run-builds
```

And for one running worker:

```sh
SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

* * * * * flock -n /tmp/run-builds.lock --command '/path/to/php-censor/bin/console php-censor:run-builds'
```
