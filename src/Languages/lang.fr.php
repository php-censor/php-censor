<?php

return [
    'language_name' => 'Français',
    'language' => 'Langue',
    'per_page'      => 'Item par page',
    'default'       => 'Défaut',

    // Log in:
    'log_in_to_app' => 'Connectez-vous à PHP Censor',
    'login_error' => 'Adresse email ou mot de passe invalide',
    'forgotten_password_link' => 'Mot de passe oublié&nbsp;?',
    'reset_emailed' => 'Nous vous avons envoyé un email avec un lien pour réinitialiser votre mot de passe.',
    'reset_header' => '<strong>Pas d\'inquiétude</strong><br>Entrez simplement votre adresse email ci-dessous
                            et nous vous enverrons un message avec un lien pour réinitialiser votre mot de passe.',
    'reset_email_address' => 'Entrez votre adresse email&nbsp;:',
    'reset_send_email' => 'Envoyer le mail',
    'reset_enter_password' => 'Veuillez entrer un nouveau mot de passe',
    'reset_new_password' => 'Nouveau mot de passe&nbsp;:',
    'reset_change_password' => 'Modifier le mot de passe',
    'reset_no_user_exists' => 'Il n\'existe aucun utilisateur avec cette adresse email, merci de réessayer.',
    'reset_email_body' => 'Bonjour %s,

Vous avez reçu cet email parce qu\'une demande de réinitialisation de mot de passe a été faite pour votre compte PHP Censor.

Si c\'est bien vous, merci de cliquer sur le lien suivant pour réinitialiser votre mot de passe&nbsp;: %ssession/reset-password/%d/%s

Sinon, merci d\'ignorer ce message.

Merci,

PHP Censor',

    'reset_email_title' => 'Réinitialisation du mot de passe PHP Censor pour %s',
    'reset_invalid' => 'Requête de réinitialisation de mot de passe invalide.',
    'email_address' => 'Adresse email',
    'login' => 'Identifiant / Adresse email',
    'password' => 'Mot de passe',
    'remember_me' => 'Se souvenir de moi',
    'log_in' => 'Connexion',


    // Top Nav
    'toggle_navigation' => 'Afficher/cacher la navigation',
    'n_builds_pending' => '%d builds en attente',
    'n_builds_running' => '%d builds en cours d\'exécution',
    'edit_profile' => 'Éditer le profil',
    'sign_out' => 'Déconnexion',
    'branch_x' => 'Branche&nbsp;: %s',
    'created_x' => 'Créé à&nbsp;: %s',
    'started_x' => 'Démarré à&nbsp;: %s',
    'environment_x' => 'Environnement&nbsp;: %s',

    // Sidebar
    'hello_name' => 'Salut %s',
    'dashboard' => 'Tableau de bord',
    'admin_options' => 'Options d\'administration',
    'add_project' => 'Ajouter un projet',
    'project_groups' => 'Groupes de projets',
    'settings' => 'Paramètres',
    'manage_users' => 'Gérer les utilisateurs',
    'plugins' => 'Plugins',
    'view' => 'Voir',
    'build_now' => 'Démarrer le build',
    'build_now_debug' => 'Démarrer le build en mode debug',
    'edit_project' => 'Éditer le projet',
    'delete_project' => 'Supprimer le projet',

    // Project Summary:
    'no_builds_yet' => 'Aucun build pour le moment&nbsp;!',
    'x_of_x_failed' => '%d des %d derniers builds ont échoué.',
    'x_of_x_failed_short' => '%d échecs / %d.',
    'last_successful_build' => ' Le dernier build réussi date du %s.',
    'never_built_successfully' => ' Aucun build de ce projet n\'a réussi.',
    'all_builds_passed' => 'Les %d derniers builds ont réussi.',
    'all_builds_passed_short' => '%d réussites / %d.',
    'last_failed_build' => ' Le dernier build en échec date du %s.',
    'never_failed_build' => ' Aucun build de ce projet n\'a échoué.',
    'view_project' => 'Voir le projet',
    'projects_with_build_errors' => 'Erreurs de build',
    'no_build_errors' => "Pas d'erreur de build",

    // Timeline:
    'latest_builds' => 'Derniers builds',
    'pending' => 'En attente',
    'running' => 'En cours',
    'success' => 'Terminé',
    'failed' => 'Échoué',
    'failed_allowed' => 'Échoué (Permis)',
    'error'  => 'Erreur',
    'skipped' => 'Ignoré',
    'trace'   => 'Trace de la pile',
    'manual_build' => 'Build manuel',

    // Add/Edit Project:
    'new_project' => 'Nouveau Projet',
    'project_x_not_found' => 'Il n\'existe pas de projet avec l\'ID %d.',
    'project_details' => 'Détails du projet',
    'public_key_help' => 'Pour pouvoir démarrer plus facilement, nous avons généré une paire de clés SSH à utiliser avec ce projet.
                            Pour l\'utiliser, il faut simplement ajouter la clé publique dans la section "Clés de déploiement"
                            de votre outil d\'hébergement de code.',
    'select_repository_type' => 'Sélectionnez le type de dépôt...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'git' => 'Git',
    'local' => 'Chemin local',
    'hg'    => 'Mercurial',
    'svn'   => 'Subversion',

    'where_hosted' => 'Où est hébergé votre projet&nbsp;?',
    'choose_github' => 'Choisissez un dépôt GitHub&nbsp;:',

    'repo_name' => 'Nom du dépôt / URL (distant) ou chemin (local)',
    'project_title' => 'Titre du projet',
    'project_private_key' => 'Clé privée à utiliser pour accéder au dépôt
                                (laissez le champ vide pour les dépôts locaux ou les URLs distantes anonymes)',
    'build_config' => 'Configuration PHP Censor spécifique pour ce projet
        (si vous ne pouvez pas ajouter de fichier .php-censor.yml (.phpci.yml|phpci.yml) à la racine du dépôt)',
    'default_branch' => 'Nom de la branche par défaut',
    'default_branch_only' => 'Build la branche par défaut seulement',
    'overwrite_build_config' => "Remplacer la configuration du fichier dans le dépôt par la configuration dans
        la base de données ? Si la case à cocher n'est pas cochée, la configuration dans la base de données sera
        fusionnée avec celle du fichier.",
    'allow_public_status' => 'Activer la page de statut publique et l\'image pour ce projet&nbsp;?',
    'archived' => 'Archivé',
    'archived_menu' => 'Archivé',
    'save_project' => 'Enregistrer le projet',
    'environments_label'  => 'Environnements (yaml)',

    'error_hg' => 'Les URLs de dépôt Mercurial doivent commencer par http:// ou https://',
    'error_git' => 'Les URLs de dépôt Git doivent commencer par git://, http:// ou https://',
    'error_gitlab' => 'Le nom du dépôt GitLab doit avoir le format "user@domain.tld:owner/repo.git"',
    'error_github' => 'Le nom du dépôt GitHub doit être dans le format "propriétaire/dépôt"',
    'error_bitbucket' => 'Le nom du dépôt doit être dans le format "propriétaire/dépôt"',
    'error_path' => 'Le chemin que vous avez spécifié n\'existe pas.',

    // View Project:
    'all_branches' => 'Toutes les branches',
    'all' => 'Toutes',
    'builds' => 'Builds',
    'id' => 'ID',
    'date' => 'Date',
    'project' => 'Projet',
    'commit' => 'Commit',
    'branch' => 'Branche',
    'environment' => 'Environnement',
    'status' => 'Statut',
    'prev_link' => '&laquo; Précédent',
    'next_link' => 'Suivant &raquo;',
    'public_key' => 'Clé Publique',
    'delete_build' => 'Supprimer le build',
    'build_source' => 'Build la source',

    'source_unknown'                      => 'Inconnue',
    'source_manual_web'                   => 'Manuel (depuis le portail Web)',
    'source_manual_console'               => 'Manuel (depuis le CLI)',
    'source_periodical'                   => 'Périodique',
    'source_webhook_push'                 => 'Webhook (Push)',
    'source_webhook_pull_request_created' => 'Webhook (Pull request)',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'Pour générer un build quand de nouveaux commits sont poussés, ajouter l\'url suivante
                                en tant que nouveau "Webhook" dans la section <a href="https://github.com/%s/settings/hooks">Webhooks
                                and Services</a> de votre dépôt GitHub.',

    'webhooks_help_gitlab' => 'Pour générer un build quand de nouveaux commits sont poussés, ajouter l\'url suivante
                                en tant que "WebHook URL" dans la section "Web Hooks" de votre dépôt GitLab.',

    'webhooks_help_gogs' => 'Pour générer un build quand de nouveaux commits sont poussés, ajouter l\'url suivante
                                en tant que "WebHook URL" dans la section "Web Hooks" de votre dépôt Gogs.',

    'webhooks_help_bitbucket' => 'Pour générer un build quand de nouveaux commits sont poussés, ajouter l\'url suivante
                                en tant que service "POST" dans la section
                                <a href="https://bitbucket.org/%s/admin/services">
                                Services</a> de votre dépôt Bitbucket.',

    // Project Groups
    'group_projects' => 'Groupes de projets',
    'project_group'  => 'Groupe du projet',
    'group_count'    => 'Nombre de projets',
    'group_edit'     => 'Éditer',
    'group_delete'   => 'Supprimer',
    'group_add'      => 'Ajouter un groupe',
    'group_add_edit' => 'Ajouter / Éditer un Groupe',
    'group_title'    => 'Titre du groupe',
    'group_save'     => 'Sauvegarder le groupe',


    // View Build
    'errors' => 'Erreurs',
    'information' => 'Informations',
    'is_new'            => 'Est-ce nouveau ?',
    'new'               => 'Nouveau',
    'build_x_not_found' => 'Le Build avec l\'ID %d n\'existe pas.',
    'build_n' => 'Build %d',
    'rebuild_now' => 'Relancer maintenant',

    'all_errors' => 'Toutes les erreurs',
    'only_new'   => 'Seulement les nouvelles erreurs',
    'only_old'   => 'Seulement les anciennes erreurs',
    'new_errors' => 'Nouvelles erreurs',

    'committed_by_x' => 'Committé par %s',
    'commit_id_x' => 'Commit&nbsp;: %s',

    'chart_display' => 'Ce graphique s\'affichera une fois que le build sera terminé.',

    'build' => 'Build',
    'lines' => 'Lignes',
    'classes' => 'Classes',
    'methods' => 'Méthodes',
    'comment_lines' => 'Lignes de commentaires',
    'noncomment_lines' => 'Lignes qui ne sont pas des commentaires',
    'logical_lines' => 'Lignes logiques',
    'lines_of_code' => 'Lignes de code',
    'coverage' => 'PHPUnit code coverage',
    'build_log' => 'Log du build',
    'quality_trend' => 'Tendance de la qualité',
    'codeception_errors' => 'Erreurs Codeception',
    'phan_warnings' => 'Alertes Phan',
    'phpmd_warnings' => 'Alertes PHPMD',
    'phpcs_warnings' => 'Alertes PHPCS',
    'phpcs_errors' => 'Erreurs PHPCS',
    'phplint_errors' => 'Erreurs de Lint',
    'phpunit_errors' => 'Erreurs PHPUnit',
    'phpcpd_warnings' => 'Alertes PHP Copy/Paste Detector',
    'phpdoccheck_warnings' => 'Blocs de documentation manquants',
    'php_cs_fixer_warnings' => 'Alertes PHP CS Fixer',
    'issues' => 'Tickets',
    'merged_branches' => 'Branches mergéess',

    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Missing Docblocks',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHP Unit',

    'codeception_feature' => 'Feature',
    'codeception_suite' => 'Suite',
    'codeception_time' => 'Time',
    'codeception_synopsis' => '<strong>%1$d</strong> tests exécutés en <strong>%2$f</strong> secondes.
                               <strong>%3$d</strong> échecs.',
    'suite' => 'Suite',
    'test'  => 'Test',
    'file' => 'Fichier',
    'line' => 'Ligne',
    'class' => 'Classe',
    'method' => 'Méthode',
    'message' => 'Message',
    'start' => 'Démarrage',
    'end' => 'Fin',
    'from' => 'À partir de',
    'to' => 'jusque',
    'result' => 'Resultat',
    'ok' => 'OK',
    'took_n_seconds' => 'Exécuté en %d secondes',
    'build_started' => 'Build démarré',
    'build_finished' => 'Build terminé',
    'test_message' => 'Message',
    'test_no_message' => 'Pas de message',
    'test_success' => 'Réussi(s) : %d',
    'test_fail' => 'Échec(s) : %d',
    'test_skipped' => 'Passé(s) : %d',
    'test_error' => 'Erreurs : %d',
    'test_todo' => 'Todos : %d',
    'test_total' => '%d test(s)',

    // Users
    'name' => 'Nom',
    'password_change' => 'Mot de passe (laissez vide si vous ne voulez pas le changer)',
    'save' => 'Sauvegarder &raquo;',
    'update_your_details' => 'Mettre à jour vos préférences',
    'your_details_updated' => 'Vos préférences ont été bien mises à jour.',
    'add_user' => 'Ajouter un utilisateur',
    'is_admin' => 'Est-il administrateur&nbsp;?',
    'yes' => 'Oui',
    'no' => 'Non',
    'edit' => 'Éditer',
    'edit_user' => 'Éditer l\'utilisateur',
    'delete_user' => 'Supprimer l\'utilisateur',
    'user_n_not_found' => 'L\'utilisateur avec l\'ID %d n\'existe pas.',
    'is_user_admin' => 'Est-ce que cet utilisateur est administrateur&nbsp;?',
    'save_user' => 'Sauvegarder l\'utilisateur',

    // Settings:
    'settings_saved' => 'Vos paramètres ont été sauvegardés.',
    'settings_check_perms' => 'Vos paramètres n\'ont pas pu être sauvegardés, vérifiez les permissions sur le fichier config.yml.',
    'settings_cannot_write' => 'PHP Censor ne peut pas écrire dans votre fichier config.yml, les paramètres ne pourront pas être sauvegardés correctement
                                tant que ce ne sera pas corrigé.',
    'settings_github_linked' => 'Votre compte GitHub n\'a pas été lié.',
    'settings_github_not_linked' => 'Votre compte GitHub ne peut pas être lié.',
    'build_settings' => 'Configuration du Build',
    'github_application' => 'Application GitHub',
    'github_sign_in' => 'Avant de commencer à utiliser GitHub, vous devez vous <a href="%s">connecter</a> et autoriser
                            PHP Censor à accéder à votre compte.',
    'github_app_linked' => 'PHP Censor s\'est connecté avec succès au compte GitHub.',
    'github_where_to_find' => 'Où trouver ces informations...',
    'github_where_help' => 'Si vous souhaitez utiliser une application qui vous appartient, vous pouvez trouver ces informations
                        dans la zone de paramètres <a href="https://github.com/settings/applications">applications</a>.',

    'email_settings' => 'Configuration email',
    'email_settings_help' => 'Avant que PHP Censor puisse envoyer des emails concernant les statuts de build,
                                vous devez entrer les configurations SMTP ci-dessous.',

    'application_id' => 'Identifiant d\'application',
    'application_secret' => 'Clé secrète de l\'application',

    'smtp_server' => 'Serveur SMTP',
    'smtp_port' => 'Port SMTP',
    'smtp_username' => 'Nom d\'utilisateur SMTP',
    'smtp_password' => 'Mot de passe SMTP',
    'from_email_address' => 'Adresse à partir de laquelle sont envoyés les emails',
    'default_notification_address' => 'Adresse de notification par défaut',
    'use_smtp_encryption' => 'Est-ce que vous voulez utiliser le chiffrement SMTP',
    'none' => 'Non',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Considérer qu\'un build a échoué après',
    '5_mins' => '5 Minutes',
    '15_mins' => '15 Minutes',
    '30_mins' => '30 Minutes',
    '1_hour' => '1 Heure',
    '3_hours' => '3 Heures',

    // Plugins
    'cannot_update_composer' => 'PHP Censor ne peut pas mettre à jour le fichier composer.json pour vous, il n\'est pas modifiable.',
    'x_has_been_removed' => '%s a été supprimé.',
    'x_has_been_added' => '%s a été ajouté au fichier composer.json pour vous et il sera installé la prochaine fois
                            que vous lancerez "composer update".',
    'enabled_plugins' => 'Plugins activés',
    'provided_by_package' => 'Fournis par le paquet',
    'installed_packages' => 'Paquets installés',
    'suggested_packages' => 'Paquets suggérés',
    'title' => 'Titre',
    'description' => 'Description',
    'version' => 'Version',
    'install' => 'Installer &raquo;',
    'remove' => 'Supprimer &raquo;',
    'search_packagist_for_more' => 'Rechercher sur Packagist pour trouver plus de paquets',
    'search' => 'Rechercher &raquo;',

    // Summary plugin
    'build-summary' => 'Résumé',
    'stage' => 'Étape',
    'duration' => 'Durée',
    'seconds'        => 'sec.',
    'plugin' => 'Plugin',
    'stage_setup' => 'Préparation',
    'stage_test' => 'Test',
    'stage_deploy'   => 'Déployé',
    'stage_complete' => 'Terminé',
    'stage_success' => 'Succès',
    'stage_failure' => 'Échec',
    'stage_broken'   => 'Cassé',
    'stage_fixed'    => 'Réparé',
    'severity'       => 'Gravité',

    'all_plugins'     => 'Tous les plugins',
    'all_severities'  => 'Toutes les gravités',
    'filters'         => 'Filtres',
    'errors_selected' => 'Erreurs sélectionnées',

    'build_details'  => 'Détails du Build',
    'commit_details' => 'Détails du Commit',
    'committer'      => 'Committer',
    'commit_message' => 'Message du commit',
    'timing'         => 'Timing',
    'created'        => 'Créé',
    'started'        => 'Démarré',
    'finished'       => 'Terminé',

    // Update
    'update_app' => 'Mise à jour de la base de données pour refléter les modifications apportées aux modèles.',
    'updating_app' => 'Mise à jour de la base de données PHP Censor : ',
    'not_installed' => 'PHP Censor n\'a pas l\'air d\'être installé.',
    'install_instead' => 'Merci d\'installer PHP Censor grâce à la commande php-censor:install.',

    // Create Build Command
    'add_to_queue_failed' => 'Build créé avec succès mais échec de l\'ajout à la file d\'attente des Builds. Cela arrive
            généralement quand PHP Censor est configuré pour utiliser un serveur beanstalkd qui n\'existe pas ou qui
            n\'est pas démarré.',

    // Build Plugins:
    'passing_build' => 'Build réussi',
    'failing_build' => 'Build en echec',
    'log_output'    => 'Sortie de log : ',

    // Error Levels:
    'critical' => 'Critique',
    'high' => 'Haut',
    'normal' => 'Normal',
    'low' => 'Bas',

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

    'confirm_message' => 'L\'article sera définitivement supprimé. Êtes-vous sûr&nbsp;?',
    'confirm_title'   => 'Confirmation de suppression d\'un article',
    'confirm_ok'      => 'Supprimer',
    'confirm_cancel'  => 'Annuler',
    'confirm_success' => 'L\'article a été supprimé avec succès.',
    'confirm_failed'  => 'Échec de la suppresion! Le serveur a répondu : ',

    'public_status_title' => 'Statut public',
    'public_status_image' => 'Image de statut',
    'public_status_page'  => 'Page publique de statut',
];
