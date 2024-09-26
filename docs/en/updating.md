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

## Additional Considerations 
* **Pre-Update Backup**: It's recommended to back up your database and any important configuration files before proceeding with the update to prevent data loss in case of issues.
* **Version Compatibility Check**: Before updating to a new version, review the release notes or changelog for any breaking changes or compatibility issues that may affect your setup.
* **Clear Cache**: After updating, clear any application cache to ensure that the latest changes are reflected. This can usually be done with:
    ```bash
    ./bin/console cache:clear
    ```
* **Post-Update Testing:** After the update, check the functionality of your application to ensure everything is working correctly.
* **Rollback Instructions:** If the update fails or issues arise, you can roll back to the previous version by checking out the specific commit or version tag and running the migration commands again.
