Updating
========

* Go to your PHP Censor directory (to `/var/www/php-censor.local` for example):

    ```bash
    cd /var/www/php-censor.local
    ```

* Pull the latest code from the repository by Git (If you want the latest `master` branch):

    ```bash
    git checkout master
    git pull -r
    ```

  Or pull the latest version:

    ```bash
    git fetch
    git checkout <version>
    ```

* Update the Composer dependencies: `composer install`

* Update the database scheme:

    ```bash
    ./bin/console php-censor-migrations:migrate
    ```

* Restart Supervisord workers (If you use workers and Supervisord):

    ```bash
    sudo supervisorctl status
    sudo supervisorctl restart <worker:worker_00>
    ...
    sudo supervisorctl restart <worker:worker_nn>
    ```

  Or restart Systemd workers (If you use workers and Systemd):

    ```bash
    sudo systemctl restart <worker@1.service>
    ...
    sudo systemctl restart <worker@n.service>
    ```
