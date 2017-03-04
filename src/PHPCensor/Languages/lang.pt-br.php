<?php

return [
    'language_name' => 'Português Brasil',
    'language' => 'Idioma',

    // Log in:
    'log_in_to_app' => 'Acessar o PHP Censor',
    'login_error' => 'Email ou senha incorretos',
    'forgotten_password_link' => 'Perdeu sua senha?',
    'reset_emailed' => 'We\'ve emailed you a link to reset your password.',
    'reset_header' => '<strong>Não se preocupe!</strong><br>Basta digitar o seu endereço de e-mail abaixo e nós lhe enviaremos um email com um link para redefinir sua senha.',
    'reset_email_address' => 'Digite seu endereço de e-mail:',
    'reset_send_email' => 'Solicitar nova senha',
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
    'reset_invalid' => 'Invalid password reset request.',
    'email_address' => 'Endereço de e-mail',
    'login' => 'Login / Email Address',
    'password' => 'Senha',
    'log_in' => 'Acessar',


    // Top Nav
    'toggle_navigation' => 'Toggle Navigation',
    'n_builds_pending' => '%d builds pending',
    'n_builds_running' => '%d builds running',
    'edit_profile' => 'Editar Perfil',
    'sign_out' => 'Sair',
    'branch_x' => 'Branch: %s',
    'created_x' => 'Criado: %s',
    'started_x' => 'Iniciado: %s',

    // Sidebar
    'hello_name' => 'Olá, %s',
    'dashboard' => 'Dashboard',
    'admin_options' => 'Opções do Admin',
    'add_project' => 'Adicionar Projeto',
    'settings' => 'Configurações',
    'manage_users' => 'Gerênciar Usuários',
    'plugins' => 'Plugins',
    'view' => 'View',
    'build_now' => 'Compilar Agora',
    'edit_project' => 'Editar Projeto',
    'delete_project' => 'Deletar Projeto',

    // Project Summary:
    'no_builds_yet' => 'Nenhuma compilação ainda!',
    'x_of_x_failed' => '%d out of the last %d builds failed.',
    'x_of_x_failed_short' => '%d / %d failed.',
    'last_successful_build' => ' The last successful build was %s.',
    'never_built_successfully' => ' This project has never built successfully.',
    'all_builds_passed' => 'All of the last %d builds passed.',
    'all_builds_passed_short' => '%d / %d passed.',
    'last_failed_build' => ' The last failed build was %s.',
    'never_failed_build' => ' This project has never failed a build.',
    'view_project' => 'Ver projeto',

    // Timeline:
    'latest_builds' => 'Últimas compilações',
    'pending' => 'Pendente',
    'running' => 'Correndo',
    'success' => 'Sucesso',
    'failed' => 'Fracassado',
    'manual_build' => 'Compilação manual',

    // Add/Edit Project:
    'new_project' => 'New Project',
    'project_x_not_found' => 'Project with ID %d does not exist.',
    'project_details' => 'Detalhes do Projeto',
    'public_key_help' => 'Para tornar mais fácil de começar, Geramos Um par de chaves SSH para você usar para este projeto. Para usá-lo, basta adicionar a seguinte chave pública na seção "deploy keys" no provedor onde hospeda seu código.',
    'select_repository_type' => 'Selecione o tipo de repositório...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'remote' => 'Remote URL',
    'local' => 'Local Path',
    'hg'    => 'Mercurial',
    'svn'   => 'Subversion',

    'where_hosted' => 'Onde seu projeto está hospedado?',
    'choose_github' => 'Choose a GitHub repository:',

    'repo_name' => 'Nome do repositório / URL (Remota) ou Caminho (Local)',
    'project_title' => 'Titulo do projeto',
    'project_private_key' => 'Chave privada usada para acessar o repositório
                                (Deixe em branco para controles remotos locais e/ou anônimos)',
    'build_config' => 'PHP Censor construir configuração para este projeto
                                (if you cannot add a .php-censor.yml (.phpci.yml|phpci.yml) file in the project repository)',
    'default_branch' => 'Nome padrão do branch',
    'allow_public_status' => 'Habilitar página de status pública e imagem para este projeto?',
    'archived' => 'Arquivado',
    'archived_menu' => 'Arquivado',
    'save_project' => 'Salvar Projeto',

    'error_mercurial' => 'Mercurial repository URL must be start with http:// or https://',
    'error_remote' => 'Repository URL must be start with git://, http:// or https://',
    'error_gitlab' => 'GitLab Repository name must be in the format "user@domain.tld:owner/repo.git"',
    'error_github' => 'Repository name must be in the format "owner/repo"',
    'error_bitbucket' => 'Repository name must be in the format "owner/repo"',
    'error_path' => 'The path you specified does not exist.',

    // View Project:
    'all_branches' => 'All Branches',
    'builds' => 'Builds',
    'id' => 'ID',
    'date' => 'Date',
    'project' => 'Project',
    'commit' => 'Commit',
    'branch' => 'Branch',
    'status' => 'Status',
    'prev_link' => '&laquo; Prev',
    'next_link' => 'Next &raquo;',
    'public_key' => 'Public Key',
    'delete_build' => 'Delete Build',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'To automatically build this project when new commits are pushed, add the URL below
                                as a new "Webhook" in the <a href="https://github.com/%s/settings/hooks">Webhooks
                                and Services</a>  section of your GitHub repository.',

    'webhooks_help_gitlab' => 'To automatically build this project when new commits are pushed, add the URL below
                                as a "WebHook URL" in the Web Hooks section of your GitLab repository.',

    'webhooks_help_bitbucket' => 'To automatically build this project when new commits are pushed, add the URL below
                                as a "POST" service in the
                                <a href="https://bitbucket.org/%s/admin/services">
                                Services</a> section of your Bitbucket repository.',

    // View Build
    'errors' => 'Erros',
    'information' => 'Informação',

    'build_x_not_found' => 'Build with ID %d does not exist.',
    'build_n' => 'Compilação %d',
    'rebuild_now' => 'Recompilar Agora',


    'committed_by_x' => 'Committed by %s',
    'commit_id_x' => 'Commit: %s',

    'chart_display' => 'This chart will display once the build has completed.',

    'build' => 'Build',
    'lines' => 'Lines',
    'comment_lines' => 'Comment lines',
    'noncomment_lines' => 'Non-Comment lines',
    'logical_lines' => 'Logical lines',
    'lines_of_code' => 'Lines of code',
    'build_log' => 'Log de compilação',
    'quality_trend' => 'Quality trend',
    'codeception_errors' => 'Codeception errors',
    'phpmd_warnings' => 'PHPMD warnings',
    'phpcs_warnings' => 'PHPCS warnings',
    'phpcs_errors' => 'PHPCS errors',
    'phplint_errors' => 'Lint errors',
    'phpunit_errors' => 'PHPUnit errors',
    'phpdoccheck_warnings' => 'Missing docblocks',
    'issues' => 'Issues',

    'codeception' => 'Codeception',
    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Missing Docblocks',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHP Unit',
    'technical_debt' => 'Technical Debt',
    'behat' => 'Behat',

    'codeception_feature' => 'Feature',
    'codeception_suite' => 'Suite',
    'codeception_time' => 'Time',
    'codeception_synopsis' => '<strong>%1$d</strong> tests carried out in <strong>%2$f</strong> seconds.
                               <strong>%3$d</strong> failures.',

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
    'name' => 'Nome',
    'password_change' => 'Password (leave blank if you don\'t want to change)',
    'save' => 'Save &raquo;',
    'update_your_details' => 'Update your details',
    'your_details_updated' => 'Your details have been updated.',
    'add_user' => 'Adicionar Usuário',
    'is_admin' => 'É Administrador?',
    'yes' => 'Sim',
    'no' => 'Não',
    'edit' => 'Edit.',
    'edit_user' => 'Editar Usuário',
    'delete_user' => 'Deletar Usuário',
    'user_n_not_found' => 'User with ID %d does not exist.',
    'is_user_admin' => 'Este usuário é um administrador?',
    'save_user' => 'Salvar Usuário',

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
    'github_linked' => 'PHP Censor is successfully linked to GitHub account.',
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
    'enabled_plugins' => 'Plugins Habilitados',
    'provided_by_package' => 'Provided By Package',
    'installed_packages' => 'Pacotes instalados',
    'suggested_packages' => 'Suggested Packages',
    'title' => 'Title',
    'description' => 'Description',
    'version' => 'Version',
    'install' => 'Install &raquo;',
    'remove' => 'Remove &raquo;',
    'search_packagist_for_more' => 'Search Packagist for more packages',
    'search' => 'Search &raquo;',

    // Summary plugin
    'build-summary' => 'Summary',
    'stage' => 'Stage',
    'duration' => 'Duration',
    'plugin' => 'Plugin',
    'stage_setup' => 'Setup',
    'stage_test' => 'Test',
    'stage_complete' => 'Complete',
    'stage_success' => 'Success',
    'stage_failure' => 'Failure',
    'stage_broken'  => 'Broken',
    'stage_fixed' => 'Fixed',

    // Update
    'update_app' => 'Update the database to reflect modified models.',
    'updating_app' => 'Updating PHP Censor database: ',
    'not_installed' => 'PHP Censor does not appear to be installed.',
    'install_instead' => 'Please install PHP Censor via php-censor:install instead.',

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
    'php_mess_detector' => 'PHP Mess Detector',
    'php_code_sniffer' => 'PHP Code Sniffer',
    'php_unit' => 'PHP Unit',
    'php_cpd' => 'PHP Copy/Paste Detector',
    'php_docblock_checker' => 'PHP Docblock Checker',
    'behat' => 'Behat',
    'technical_debt' => 'Technical Debt',
];
