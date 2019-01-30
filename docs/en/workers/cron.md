Run builds using cronjob
========================
**Note:** This functionality is deprecated and will be deleted in 
version 2.0. Use a [worker](workers/worker.md) instead.
---

Running builds using cron is a quick and simple method of getting up and running with PHP Censor. It also removes the
need for PHP Censor to be running all the time.

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
