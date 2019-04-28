Injecting variables into messages
=================================

Most strings used in the build configuration can have variables related to the build inserted into them with the 
following syntax:

```
"My important message is about %VARIABLE%"
```

Where `VARIABLE` can be one of the following:

* **COMMIT_ID** - The commit hash.

* **SHORT_COMMIT_ID** - The shortened version of the commit hash.

* **COMMITTER_EMAIL** - The Email address of the committer.

* **COMMIT_MESSAGE** - The message written by the committer.

* **COMMIT_LINK** - The URL to the commit.

* **BRANCH** - The name of the branch.

* **BRANCH_LINK** - The URL to the branch.

* **ENVIRONMENT** - Build environment (See [environments](environments.md)).

* **PROJECT_ID** - The ID of the project.

* **BUILD_ID** - The build number.

* **PROJECT_TITLE** - The name of the project.

* **PROJECT_LINK** - The URL to the project in PHP Censor.

* **BUILD_PATH** - The path to the build.

* **BUILD_LINK** - The URL to the build in PHP Censor.
