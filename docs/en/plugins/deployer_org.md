Plugin DeployerOrg
==================

Simple plugin for [Deployer](http://deployer.org)

How to use
----------

Keyword of this plugin is simple. It means that you just need to define branch 
for configuration task name(if there is no task, plugin takes 
default value that is "deploy"), stage name(it would be just server name or defined stage)
, verbosity level(for default is normal) and filename(by default deployer takes the deployer.php file)

Plugin options
--------------

* stage(*required*) - Stage or server name
* task(*optional*) - Task name (*default task is deploy*) 
* verbosity(*optional*) - Add verbose mode to plugin execution (*default is no verbose that equal to normal in the 
option list of values below*)
  * normal
  * verbose
  * very verbose
  * debug
  * quiet 
* file(*optional*) - Filename of deployer configuration. For default deployer takes deploy.php if this field is not 
specified

Sample configuration
--------------------

```
\Ket4yii\PHPCensor\Deployer\Plugin\Deployer:
  development: # branch name
    task: sample-task # optional, default task is deploy 
    stage: dev # required, name of stage or server
    verbose: debug # optional, default is normal(no verbosity)
    file: .deploy_config.php # optional, deployer takes the deploy.php file for default
  master:
    stage: prod #required, name of stage or server
```
