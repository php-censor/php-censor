<?php

return [
    'language_name' => 'English',
    'language'      => 'Language',
    'per_page'      => 'Items per page',
    'default'       => 'Default',

    // Log in:
    'log_in_to_app' => 'Log in to PHP Censor',
    'login_error' => 'Incorrect email address or password',
    'forgotten_password_link' => 'Forgotten your password?',
    'reset_emailed' => 'We\'ve emailed you a link to reset your password.',
    'reset_header' => '<strong>Don\'t worry!</strong><br>Just enter your email address below and we\'ll email
                            you a link to reset your password.',
    'reset_email_address' => 'Enter your email address:',
    'reset_send_email' => 'Email password reset',
    'reset_enter_password' => 'Please enter a new password',
    'reset_new_password' => 'New password:',
    'reset_change_password' => 'Change password',
    'reset_no_user_exists' => 'No user exists with that email address, please try again.',
    'reset_email_body' => 'Hi %s,

You have received this email because you, or someone else, has requested a password reset for PHP Censor.

If this was you, please click the following link to reset your password: %ssession/reset-password/%d/%s

Otherwise, please ignore this email and no action will be taken.

Thank you,

PHP Censor',

    'reset_email_title' => 'PHP Censor Password Reset for %s',
    'reset_invalid'     => 'Invalid password reset request.',
    'email_address'     => 'Email Address',
    'login'             => 'Login / Email Address',
    'password'          => 'Password',
    'remember_me'       => 'Remember me',
    'log_in'            => 'Log in',


    // Top Nav
    'toggle_navigation' => 'Toggle Navigation',
    'n_builds_pending' => '%d builds pending',
    'n_builds_running' => '%d builds running',
    'edit_profile' => 'Edit Profile',
    'sign_out' => 'Sign Out',
    'branch_x' => 'Branch: %s',
    'created_x' => 'Created: %s',
    'started_x' => 'Started: %s',
    'environment_x' => 'Environment: %s',

    // Sidebar
    'hello_name' => 'Hello, %s',
    'dashboard' => 'Dashboard',
    'admin_options' => 'Admin Options',
    'add_project' => 'Add Project',
    'project_groups' => 'Project Groups',
    'settings' => 'Settings',
    'manage_users' => 'Manage Users',
    'plugins' => 'Plugins',
    'view' => 'View',
    'build_now' => 'Build now',
    'build_now_debug' => 'Build now with debug',
    'edit_project' => 'Edit Project',
    'delete_project' => 'Delete Project',

    // Project Summary:
    'no_builds_yet' => 'No builds yet!',
    'x_of_x_failed' => '%d out of the last %d builds failed.',
    'x_of_x_failed_short' => '%d / %d failed.',
    'last_successful_build' => ' The last successful build was %s.',
    'never_built_successfully' => ' This project has never built successfully.',
    'all_builds_passed' => 'All of the last %d builds passed.',
    'all_builds_passed_short' => '%d / %d passed.',
    'last_failed_build' => ' The last failed build was %s.',
    'never_failed_build' => ' This project has never failed a build.',
    'view_project' => 'View Project',
    'projects_with_build_errors' => 'Build errors',
    'no_build_errors' => 'No build errors',

    // Timeline:
    'latest_builds' => 'Latest Builds',
    'pending' => 'Pending',
    'running' => 'Running',
    'success' => 'Success',
    'failed' => 'Failed',
    'failed_allowed' => 'Failed (Allowed)',
    'error'  => 'Error',
    'skipped' => 'Skipped',
    'trace'   => 'Stack trace',
    'manual_build' => 'Manual Build',

    // Add/Edit Project:
    'new_project' => 'New Project',
    'project_x_not_found' => 'Project with ID %d does not exist.',
    'project_details' => 'Project Details',
    'public_key_help' => 'To make it easier to get started, we\'ve generated an SSH key pair for you to use
                            for this project. To use it, just add the following public key to the "deploy keys" section
                            of your chosen source code hosting platform.',
    'select_repository_type' => 'Select repository type...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'git' => 'Git',
    'local' => 'Local Path',
    'hg'    => 'Mercurial',
    'svn'   => 'Subversion',

    'where_hosted' => 'Where is your project hosted?',
    'choose_github' => 'Choose a GitHub repository:',

    'repo_name' => 'Repository Name / URL (Remote) or Path (Local)',
    'project_title' => 'Project Title',
    'project_private_key' => 'Private key to use to access repository
                                (leave blank for local and/or anonymous remotes)',
    'build_config' => 'PHP Censor build config for this project
                                (if you cannot add a .php-censor.yml (.phpci.yml|phpci.yml) file in the project repository)',
    'default_branch'      => 'Default branch name',
    'default_branch_only' => 'Build default branch only',
    'overwrite_build_config' => 'Overwrite in-repository file config by in-database config? If checkbox not checked then in-database config will be merged with file config.',
    'allow_public_status' => 'Enable public status page and image for this project?',
    'archived'            => 'Archived',
    'archived_menu'       => 'Archived',
    'save_project'        => 'Save Project',
    'environments_label'  => 'Environments (yaml)',

    'error_hg' => 'Mercurial repository URL must be start with http:// or https://',
    'error_git' => 'Repository URL must be start with git://, http:// or https://',
    'error_gitlab' => 'GitLab Repository name must be in the format "user@domain.tld:owner/repo.git"',
    'error_github' => 'Repository name must be in the format "owner/repo"',
    'error_bitbucket' => 'Repository name must be in the format "owner/repo"',
    'error_path' => 'The path you specified does not exist.',

    // View Project:
    'all_branches' => 'All Branches',
    'all' => 'All',
    'builds' => 'Builds',
    'id' => 'ID',
    'date' => 'Date',
    'project' => 'Project',
    'commit' => 'Commit',
    'branch' => 'Branch',
    'environment' => 'Environment',
    'status' => 'Status',
    'prev_link' => '&laquo; Prev',
    'next_link' => 'Next &raquo;',
    'public_key' => 'Public Key',
    'delete_build' => 'Delete Build',
    'build_source' => 'Build source',

    'source_unknown'                       => 'Unknown',
    'source_manual_web'                    => 'Manual (from Web)',
    'source_manual_console'                => 'Manual (from CLI)',
    'source_periodical'                    => 'Periodical',
    'source_webhook_push'                  => 'Webhook (Push)',
    'source_webhook_pull_request_created'  => 'Webhook (Created pull request)',
    'source_webhook_pull_request_updated'  => 'Webhook (Updated pull request)',
    'source_webhook_pull_request_approved' => 'Webhook (Approved pull request)',
    'source_webhook_pull_request_merged'   => 'Webhook (Merged pull request)',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'To automatically build this project when new commits are pushed, add the URL below
                                as a new "Webhook" in the <a href="https://github.com/%s/settings/hooks">Webhooks
                                and Services</a>  section of your GitHub repository.',

    'webhooks_help_gitlab' => 'To automatically build this project when new commits are pushed, add the URL below
                                as a "WebHook URL" in the Web Hooks section of your GitLab repository.',

    'webhooks_help_gogs' => 'To automatically build this project when new commits are pushed, add the URL below
                                as a "WebHook URL" in the Web Hooks section of your Gogs repository.',

    'webhooks_help_bitbucket' => 'To automatically build this project when new commits are pushed, add the URL below
                                as a "POST" service in the
                                <a href="https://bitbucket.org/%s/admin/services">
                                Services</a> section of your Bitbucket repository.',

    // Project Groups
    'group_projects' => 'Project groups',
    'project_group'  => 'Project group',
    'group_count'    => 'Projects count',
    'group_edit'     => 'Edit',
    'group_delete'   => 'Delete',
    'group_add'      => 'Add Group',
    'group_add_edit' => 'Add / Edit Group',
    'group_title'    => 'Group Title',
    'group_save'     => 'Save Group',

    // View Build
    'errors'            => 'Errors',
    'information'       => 'Information',
    'is_new'            => 'Is new?',
    'new'               => 'New',
    'build_x_not_found' => 'Build with ID %d does not exist.',
    'build_n'           => 'Build %d',
    'rebuild_now'       => 'Rebuild Now',

    'all_errors' => 'All errors',
    'only_new'   => 'Only new errors',
    'only_old'   => 'Only old errors',
    'new_errors' => 'New errors',

    'committed_by_x' => 'Committed by %s',
    'commit_id_x' => 'Commit: %s',

    'chart_display' => 'This chart will display once the build has completed.',

    'build'   => 'Build',
    'lines'   => 'Lines',
    'classes' => 'Classes',
    'methods' => 'Methods',
    'comment_lines' => 'Comment lines',
    'noncomment_lines' => 'Non-Comment lines',
    'logical_lines' => 'Logical Lines',
    'lines_of_code' => 'Lines of code',
    'coverage' => 'PHPUnit code coverage',
    'build_log' => 'Build log',
    'quality_trend' => 'Quality trend',
    'codeception_errors' => 'Codeception errors',
    'phpmd_warnings' => 'PHPMD warnings',
    'phpcs_warnings' => 'PHPCS warnings',
    'phan_warnings' => 'Phan warnings',
    'php_cs_fixer_warnings' => 'PHP CS Fixer warnings',
    'phpcs_errors' => 'PHPCS errors',
    'phplint_errors' => 'Lint errors',
    'phpunit_errors' => 'PHPUnit errors',
    'phpcpd_warnings' => 'PHP Copy/Paste Detector warnings',
    'phpdoccheck_warnings' => 'Missing docblocks',
    'issues' => 'Issues',
    'merged_branches' => 'Merged branches',

    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Missing Docblocks',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHP Unit',

    'codeception_feature' => 'Feature',
    'codeception_suite' => 'Suite',
    'codeception_time' => 'Time',
    'codeception_synopsis' => '<strong>%1$d</strong> tests carried out in <strong>%2$f</strong> seconds.
                               <strong>%3$d</strong> failures.',
    'suite' => 'Suite',
    'test'  => 'Test',
    'file' => 'File',
    'line' => 'Line',
    'class' => 'Class',
    'method' => 'Method',
    'message' => 'Message',
    'start' => 'Start',
    'end' => 'End',
    'from' => 'From',
    'to' => 'To',
    'result' => 'Result',
    'ok' => 'OK',
    'took_n_seconds' => 'Took %d seconds',
    'build_started' => 'Build Started',
    'build_finished' => 'Build Finished',
    'test_message' => 'Message',
    'test_no_message' => 'No message',
    'test_success' => 'Successful: %d',
    'test_fail' => 'Failures: %d',
    'test_skipped' => 'Skipped: %d',
    'test_error' => 'Errors: %d',
    'test_todo' => 'Todos: %d',
    'test_total' => '%d test(s)',

    // Users
    'name' => 'Name',
    'password_change' => 'Password (leave blank if you don\'t want to change)',
    'save' => 'Save &raquo;',
    'update_your_details' => 'Update your details',
    'your_details_updated' => 'Your details have been updated.',
    'add_user' => 'Add User',
    'is_admin' => 'Is Admin?',
    'yes' => 'Yes',
    'no' => 'No',
    'edit' => 'Edit',
    'edit_user' => 'Edit User',
    'delete_user' => 'Delete User',
    'user_n_not_found' => 'User with ID %d does not exist.',
    'is_user_admin' => 'Is this user an administrator?',
    'save_user' => 'Save User',

    // Settings:
    'settings_saved' => 'Your settings have been saved.',
    'settings_check_perms' => 'Your settings could not be saved, check the permissions of your config.yml file.',
    'settings_cannot_write' => 'PHP Censor cannot write to your config.yml file, settings may not be saved properly
                                until this is rectified.',
    'settings_github_linked' => 'Your GitHub account has been linked.',
    'settings_github_not_linked' => 'Your GitHub account could not be linked.',
    'build_settings' => 'Build Settings',
    'github_application' => 'GitHub Application',
    'github_sign_in' => 'Before you can start using GitHub, you need to <a href="%s">sign in</a> and grant
                            PHP Censor access to your account.',
    'github_app_linked' => 'PHP Censor is successfully linked to GitHub account.',
    'github_where_to_find' => 'Where to find these...',
    'github_where_help' => 'If you own the application you would like to use, you can find this information within your
                            <a href="https://github.com/settings/applications">applications</a> settings area.',

    'email_settings' => 'Email Settings',
    'email_settings_help' => 'Before PHP Censor can send build status emails,
                                you need to configure your SMTP settings below.',

    'application_id' => 'Application ID',
    'application_secret' => 'Application Secret',

    'smtp_server' => 'SMTP Server',
    'smtp_port' => 'SMTP Port',
    'smtp_username' => 'SMTP Username',
    'smtp_password' => 'SMTP Password',
    'from_email_address' => 'From Email Address',
    'default_notification_address' => 'Default Notification Email Address',
    'use_smtp_encryption' => 'Use SMTP Encryption?',
    'none' => 'None',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Consider a build failed after',
    '5_mins' => '5 Minutes',
    '15_mins' => '15 Minutes',
    '30_mins' => '30 Minutes',
    '1_hour' => '1 Hour',
    '3_hours' => '3 Hours',

    // Plugins
    'cannot_update_composer' => 'PHP Censor cannot update composer.json for you as it is not writable.',
    'x_has_been_removed' => '%s has been removed.',
    'x_has_been_added' => '%s has been added to composer.json for you and will be installed next time
                            you run composer update.',
    'enabled_plugins' => 'Enabled Plugins',
    'provided_by_package' => 'Provided By Package',
    'installed_packages' => 'Installed Packages',
    'suggested_packages' => 'Suggested Packages',
    'title' => 'Title',
    'description' => 'Description',
    'version' => 'Version',
    'install' => 'Install &raquo;',
    'remove' => 'Remove &raquo;',
    'search_packagist_for_more' => 'Search Packagist for more packages',
    'search' => 'Search &raquo;',

    // Summary plugin
    'build-summary'  => 'Summary',
    'stage'          => 'Stage',
    'duration'       => 'Duration',
    'seconds'        => 'sec.',
    'plugin'         => 'Plugin',
    'stage_setup'    => 'Setup',
    'stage_test'     => 'Test',
    'stage_deploy'   => 'Deploy',
    'stage_complete' => 'Complete',
    'stage_success'  => 'Success',
    'stage_failure'  => 'Failure',
    'stage_broken'   => 'Broken',
    'stage_fixed'    => 'Fixed',
    'severity'       => 'Severity',

    'all_plugins'     => 'All plugins',
    'all_severities'  => 'All severities',
    'filters'         => 'Filters',
    'errors_selected' => 'Errors selected',

    'build_details'  => 'Build Details',
    'commit_details' => 'Commit Details',
    'committer'      => 'Committer',
    'commit_message' => 'Commit Message',
    'timing'         => 'Timing',
    'created'        => 'Created',
    'started'        => 'Started',
    'finished'       => 'Finished',

    // Update
    'update_app' => 'Update the database to reflect modified models.',
    'updating_app' => 'Updating PHP Censor database: ',
    'not_installed' => 'PHP Censor does not appear to be installed.',
    'install_instead' => 'Please install PHP Censor via php-censor:install instead.',

    // Create Build Command
    'add_to_queue_failed' => 'Build created successfully, but failed to add to build queue. This usually happens
                                when PHP Censor is set to use a beanstalkd server that does not exist,
                                or your beanstalkd server has stopped.',

    // Build Plugins:
    'passing_build' => 'Passing Build',
    'failing_build' => 'Failing Build',
    'log_output' => 'Log Output: ',


    // Error Levels:
    'critical' => 'Critical',
    'high' => 'High',
    'normal' => 'Normal',
    'low' => 'Low',

    // Plugins that generate errors:
    'php_mess_detector'    => 'PHP Mess Detector',
    'php_code_sniffer'     => 'PHP Code Sniffer',
    'php_unit'             => 'PHP Unit',
    'php_cpd'              => 'PHP Copy/Paste Detector',
    'php_docblock_checker' => 'PHP Docblock Checker',
    'composer'             => 'Composer',
    'php_loc'              => 'PHP LOC',
    'php_parallel_lint'    => 'PHP Parallel Lint',
    'email'                => 'Email',
    'atoum'                => 'Atoum',
    'behat'                => 'Behat',
    'campfire'             => 'Campfire',
    'clean_build'          => 'Clean Build',
    'codeception'          => 'Codeception',
    'copy_build'           => 'Copy Build',
    'deployer'             => 'Deployer',
    'env'                  => 'Env',
    'grunt'                => 'Grunt',
    'hipchat_notify'       => 'Hipchat',
    'irc'                  => 'IRC',
    'lint'                 => 'Lint',
    'mysql'                => 'MySQL',
    'package_build'        => 'Package Build',
    'pdepend'              => 'PDepend',
    'pgsql'                => 'PostgreSQL',
    'phan'                 => 'Phan',
    'phar'                 => 'Phar',
    'phing'                => 'Phing',
    'php_cs_fixer'         => 'PHP Coding Standards Fixer',
    'php_spec'             => 'PHP Spec',
    'shell'                => 'Shell',
    'slack_notify'         => 'Slack',
    'technical_debt'       => 'Technical Debt',
    'xmpp'                 => 'XMPP',
    'security_checker'     => 'SensioLabs Security Checker',

    'confirm_message' => 'Item will be permanently deleted. Are you sure?',
    'confirm_title'   => 'Item delete confirmation',
    'confirm_ok'      => 'Delete',
    'confirm_cancel'  => 'Cancel',
    'confirm_success' => 'Item successfully deleted.',
    'confirm_failed'  => 'Deletion failed! Server says: ',

    'public_status_title' => 'Public status',
    'public_status_image' => 'Status image',
    'public_status_page'  => 'Public status page',
];
