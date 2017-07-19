Adding PHP Censor Support to Your Projects
==========================================

Similar to Travis CI, to support PHP Censor in your project, you simply need to add a `.php-censor.yml` 
(`phpci.yml`/`.phpci.yml` for backward compatibility with PHPCI) file to the root of your repository. The file should 
look something like this:

```yml
build_settings:
  clone_depth: 1 # depth of 1 is a shallow clone, remove this line to clone entire repo
  ignore:
    - "vendor"
    - "tests"
  mysql:
    host: "localhost"
    user: "root"
    pass: ""

setup:
  mysql:
    - "DROP DATABASE IF EXISTS test;"
    - "CREATE DATABASE test;"
    - "GRANT ALL PRIVILEGES ON test.* TO test@'localhost' IDENTIFIED BY 'test';"
  composer:
    action: "install"

test:
  php_unit:
    config:
      - "PHPUnit-all.xml"
      - "PHPUnit-ubuntu-fix.xml"
    directory:
      - "tests/"
    run_from: "phpunit/"
    coverage: "tests/logs/coverage"
  php_mess_detector:
    allow_failures: true
  php_code_sniffer:
    standard: "PSR2"
  php_cpd:
    allow_failures: true
  grunt:
    task: "build"

deploy:
  deployer:
    webhook_url: "http://deployer.local/deploy/QZaF1bMIUqbMFTmKDmgytUuykRN0cjCgW9SooTnwkIGETAYhDTTYoR8C431t"
    reason:      "PHP Censor Build #%BUILD% - %COMMIT_MESSAGE%"
    update_only: true

complete:
  mysql:
    host: "localhost"
    user: "root"
    pass: ""
    - "DROP DATABASE IF EXISTS test;"

branch-dev:           # Branch-specific config (for "dev" branch)
  run-option: replace # "run-option" parameter can be set to 'replace', 'after' or 'before'
  test:
    grunt:
      task: "build-dev"
```


Stages
------

As mentioned earlier, PHP Censor is powered by plugins, there are several phases in which plugins can be run:

* `setup` - This phase is designed to initialise the build procedure.

* `test` - The tests that should be run during the build. Plugins run during this phase will contribute to the success 
or failure of the build.

* `deploy` - The deploy that should be run after the build. Plugins run during this phase will contribute to the 
success or failure of the build.

* `complete` - Always called when the `test` phase completes, regardless of success or failure. **Note** that is you 
do any DB stuff here, you will need to add the DB credentials to this section as well, as it runs in a separate 
instance.

* `success` - Called upon success of the `test` phase.

* `failure` - Called upon failure of the `test` phase.

* `fixed` - Called upon success of the `test` phase if the previous build of the branch was a failure.

* `broken` - Called upon failure of the `test` phase if the previous build of the branch was a success.

The `ignore` section is merely an array of paths that should be ignored in all tests (where possible).


Branch specific stages
----------------------

PHP Censor allows you configure plugins depending on the branch you configure in the project settings in the UI. 
You can replace a complete stage for a branch, or add extra plugins to a stage that run before or after the default 
plugins.  

### Example config

```yml
test: # Test stage config for all branches
  php_cs_fixer:
    allowed_warnings: -1
success: # Success stage config for all branches
  shell: ./notify

branch-release: # Test config for release branch
  run-option: replace # This can be set to either before, after or replace
  test:
    php_cs_fixer:
      allowed_warnings: 0
branch-master: # Test config for release branch
  run-option: after # This can be set to either before, after or replace
  success:
    shell:
      - "rsync ..."
```

### How it works

When you have configured a branch eg "stable" in the project settings in the UI. Add a new config named 
"branch-<branch>", in this case "branch-stable" to the `.php-censor.yml`. In this config, specify all stages and 
plugins you wish to run.

Also add a new config value `run-option`, that can have 3 values:

* `before` - will cause the branch specific plugins to run before the default ones.
* `after` - will cause the branch specific plugins to run after the default ones.
* `replace` - will cause the branch specific plugins to run and the default ones not.
