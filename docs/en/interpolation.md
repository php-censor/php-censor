Injecting variables into messages
=================================

Most strings used in the build configuration can have variables related to the build inserted into them with the 
following syntax:

```
"My important message is about %VARIABLE%"
```

Where `VARIABLE` can be one of the following:

* **COMMIT** - The commit hash.

* **SHORT_COMMIT** - The shortened version of the commit hash.

* **COMMIT_EMAIL** - The Email address of the committer.

* **COMMIT_MESSAGE** - The message written by the committer.

* **COMMIT_URI** - The URL to the commit.

* **BRANCH** - The name of the branch.

* **BRANCH_URI** - The URL to the branch.

* **ENVIRONMENT** - Build environment (See [environments](environments.md)).

* **PROJECT** - The ID of the project.

* **BUILD** - The build number.

* **PROJECT_TITLE** - The name of the project.

* **PROJECT_URL** - The URL to the project in PHP Censor.

* **BUILD_PATH** - The path to the build.

* **BUILD_URI** - The URL to the build in PHP Censor.
