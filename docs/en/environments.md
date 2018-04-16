Project Environments
====================

A environment can include several branches - base branch (default project branch) and optional additional branches 
(which merge into base).

When you commit to a branch, builds all environments in which branch included (base branch implicitly included to all 
environments).

When you build a environment, additional branches merged into base branch.

For example, it can be useful when you delay merging into master or test some branches at once. Or deploy.


Config example and explanation
------------------------------
Configuration is specified on project edit page.

In this example, there are three environments:
* Production (named `pr`) is associated with the default branch for the project.
* Release candidate (`rc`) - branch by default plus branch `feature-A`
* Test (`test`) - branch by default plus branch `feature-B`

```yml
pr:
rc:
    - feature-A
test:
    - feature-B
```

When you push commits to `master` branch, three builds will be created - one for each of the environments.

If push commit to branch `feature-A` - build for `rc` environment will be created.

If push commit to branch `feature-C` - no build will be created.

You can use variable `%ENVIRONMENT%` in project config.

```yml
setup:
  mysql:
    - "DROP DATABASE IF EXISTS project_name_%ENVIRONMENT%;"
    - "CREATE DATABASE project_name_%ENVIRONMENT%;"
test:
    ...
deploy:
    mage:
        env: %ENVIRONMENT%
```


Webhooks to include branches in the environment
-----------------------------------------------

### GOGS

Prepare project in GOGS web-admin:

* Create webhook.

* Set "Payload URL" to php-censor webhook URL like: `http://php-censor.local/webhook/gogs/<project_id>`.

* Enable triggering "Pull request".

* Create labels for your environments in the format: `env:<environment-name>` (For example `env:test`).

* After creating the pull request, to include the branch in the environment, add the appropriate labels.
