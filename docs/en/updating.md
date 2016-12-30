Updating PHP Censor
-------------------

Updating PHP Censor to the latest release, or even dev-master updates is something that will need to be done from time to time. Most of this may be self-explanatory, but for clarity and completeness, it should be added to the documentation.

1. Go to your PHP Censor root folder in a Terminal.
2. Pull the latest code. This would look like this: `git pull`
3. Update the PHP Censor database: `./bin/console php-censor-migrations:migrate`
4. Update the composer and its packages: `composer self-update && composer update`
5. Return to the PHP Censor admin screens and check your desired plugins are still installed correctly.
7. Run a build to make sure everything is working as expected.
