Installing
==========

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
# Check if the service is running:
/etc/init.d/beanstalkd status
# If it's not running, start it:
/etc/init.d/beanstalkd start
```

* Install PHP Censor itself:

```bash
cd ./php-censor.local

# Interactive installation
./bin/console php-censor:install

# Non-interactive installation (useful for automation)
./bin/console php-censor:install \
    --url='http://php-censor.local' \
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

* [Add a virtual host to your web server](virtual_host.md), pointing to the `public` directory within your new
  PHP Censor directory. You'll need to set up rewrite rules to point all non-existent requests to PHP Censor;

* [Set up the PHP Censor Worker](workers/worker.md) (required for background tasks);

## Additional Configuration

**Database Setup Tips:**
* For MySQL/MariaDB:
    * Ensure that the user has proper permissions on the database.
    * Recommended to use UTF-8 encoding for best compatibility. 
* For PostgreSQL:
    * If using SSL, adjust the `--db-pgsql-sslmode` option based on your server settings (e.g., `require`, `verify-full`).

**Beanstalkd Queue Setup:**
* Ensure that Beanstalkd is set to start automatically on reboot:
```bash
sudo systemctl enable beanstalkd
```

## Installing via Docker

If you want to install PHP Censor as a Docker container, you can use
[php-censor/docker-php-censor](https://github.com/php-censor/docker-php-censor) project. This method simplifies environment management and is especially useful for development setups.

## Troubleshooting
* **Common Issues:**
    * **Database Connection Issues:** Ensure that the database credentials are correct and that the database server is running.
    * **Permission Errors:** Verify that the web server user has appropriate permissions on the PHP Censor directories.
    * **Queue Issues:** If jobs are not being processed, check the status of the Beanstalkd queue and ensure that the worker is running.
* **Logs:**
    * Check the PHP Censor logs in `./runtime/logs` for detailed error messages if the installation fails.  
