Run Builds Using a Worker
=========================

The PHP Censor Worker runs in the background on your server and waits for new builds to be added to a Beanstalkd queue. Unless already running a build, the worker will pick up and start running new builds almost immediately after their creation.

The worker is the recommended way to run PHP Censor builds. You can run several workers all watching one queue, allowing jobs to be run simultaneously without the overhead of polling your MySQL database. 

If you can't run Beanstalkd on your server, or would prefer to run builds on a regular schedule, you should consider using the [build daemon](workers/daemon.md) or [running builds via Cron](workers/cron.md).

Pre-Requisites
--------------

* You need to install [Beanstalkd](http://kr.github.io/beanstalkd/) - On Ubuntu, this is as simple as running `apt-get install beanstalkd`.
* [Supervisord](http://supervisord.org/) needs to be installed and running on your server.

Setting up the PHP Censor worker
--------------------------------

### On a new installation

Setting up the worker on a new installation of PHP Censor is as simple as entering the appropriate values for your Beanstalkd server hostname and queue name when running the PHP Censor installer. By default, the installer assumes that you'll be using beanstalkd on `localhost` and will use the queue name `php-censor-queue`.

![PHP Censor Worker Installer](https://www.phptesting.org/media/render/f48f63699a04444630352643af18b643)

### On an existing installation

On an existing installation, to set up the worker, you simply need to add the beanstalkd host and queue names directly into your `config.yml` file. You should add a `worker` key beneath the `php-censor` section, with the properties `host` and `queue` as outlined in the screenshot below:

![PHP Censor Worker Config](https://www.phptesting.org/media/render/9a88e9298670f2913f5798e68b94c9ed)

Running the PHP Censor worker
-----------------------------

Once you've set up PHP Censor to add your jobs to a beanstalkd queue, you need to start the worker so that it can pick up and run your builds. On most servers, it is best to manage this using supervisord. The following instructions work on Ubuntu, but will need slight amendments for other distributions.

Using your preferred text editor, create a file named `php-censor.conf` under `/etc/supervisor/conf.d`. In it, enter the following config:

```
[program:php-censor]
command=/path/to/php-censor/bin/console php-censor:worker
process_name=%(program_name)s_%(process_num)02d
stdout_logfile=/var/log/php-censor.log
stderr_logfile=/var/log/php-censor-err.log
user=php-censor
autostart=true
autorestart=true
environment=HOME="/home/php-censor",USER="php-censor"
numprocs=2
```

You'll need to edit the '/path/to/php-censor', the `user` value and the `environment` value to suit your server. The user needs to be an actual system user with suitable permissions to execute PHP and PHP Censor.

Once you've created this file, simply restart supervisord using the command `service supervisor restart` and 2 instances of PHP Censor's worker should start immediately. You can verify this by running the command `ps aux | grep php-censor`, which should give you output as follows:

```
➜  ~ ps aux | grep php-censor
php-censor    19057  0.0  0.9 200244 18720 ?        S    03:00   0:01 php /php-censor/console php-censor:worker
php-censor    19058  0.0  0.9 200244 18860 ?        S    03:00   0:01 php /php-censor/console php-censor:worker
```

That's it! Now, whenever you create a new build in PHP Censor, it should start building immediately.
