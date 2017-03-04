<?php

return [
    'language_name' => 'Dansk',
    'language' => 'Sprog',

    // Log in:
    'log_in_to_app' => 'Log ind i PHP Censor',
    'login_error' => 'Forkert email-adresse eller adgangskode',
    'forgotten_password_link' => 'Har du glemt din adgangskode?',
    'reset_emailed' => 'Vi har sendt dig en email med et link til at nulstille din adgangskode.',
    'reset_header' => '<strong>Bare rolig!</strong><br>Indtast blot din email-adresse, så sender
vi dig et link til at nulstille din adgangskode.',
    'reset_email_address' => 'Indtast din email-adresse:',
    'reset_send_email' => 'Send nulstillings-link',
    'reset_enter_password' => 'Indtast venligst en ny adgangskode',
    'reset_new_password' => 'Ny adgangskode:',
    'reset_change_password' => 'Skift adgangskode',
    'reset_no_user_exists' => 'Der findes ingen bruger med den email-adresse, prøv igen.',
    'reset_email_body' => 'Hej %s,

Du modtager denne email fordi du eller en anden person har anmodet om at nulstille din adgangskode til PHP Censor.

Hvis det var dig kan du klikke følgende link for at nulstille din adgangskode: %ssession/reset-password/%d/%s

Hvis det ikke var dig kan du ignorere denne email og intet vil ske.

Tak,

PHP Censor',

    'reset_email_title' => 'PHP Censor Adgangskode-nulstilling for %s',
    'reset_invalid' => 'Ugyldig anmodning om adgangskode-nulstilling.',
    'email_address' => 'Email-addresse',
    'login' => 'Login / Email Address',
    'password' => 'Adgangskode',
    'log_in' => 'Log ind',


    // Top Nav
    'toggle_navigation' => 'Vis/skjul navigation',
    'n_builds_pending' => '%d builds i køen',
    'n_builds_running' => '%d builds kører',
    'edit_profile' => 'Redigér profil',
    'sign_out' => 'Log ud',
    'branch_x' => 'Branch: %s',
    'created_x' => 'Oprettet: %s',
    'started_x' => 'Startet: %s',

    // Sidebar
    'hello_name' => 'Hej %s',
    'dashboard' => 'Dashboard',
    'admin_options' => 'Administrator-indstillinger',
    'add_project' => 'Tilføj projekt',
    'settings' => 'Indstillinger',
    'manage_users' => 'Administrér brugere',
    'plugins' => 'Plugins',
    'view' => 'Vis',
    'build_now' => 'Start build nu',
    'edit_project' => 'Redigér projekt',
    'delete_project' => 'Slet projekt',

    // Project Summary:
    'no_builds_yet' => 'Ingen builds pt.!',
    'x_of_x_failed' => '%d af de sidste %d builds fejlede.',
    'x_of_x_failed_short' => '%d / %d fejlede.',
    'last_successful_build' => 'Sidste succesfulde build var %s.',
    'never_built_successfully' => 'Dette projekt har indtil videre ingen succesfulde builds.',
    'all_builds_passed' => 'All de sidste %d builds fejlede.',
    'all_builds_passed_short' => '%d / %d lykkedes.',
    'last_failed_build' => 'Det sidste mislykkede build var %s',
    'never_failed_build' => 'Dette projekt er endnu ikke blevet kørt.',
    'view_project' => 'Vis Projekt',

    // Timeline:
    'latest_builds' => 'Nyeste Builds',
    'pending' => 'Venter',
    'running' => 'Kører',
    'success' => 'Succes',
    'failed' => 'Fejlede',
    'manual_build' => 'Manuelt Build',

    // Add/Edit Project:
    'new_project' => 'Nyt Projekt',
    'project_x_not_found' => 'Projektet med ID %d findes ikke.',
    'project_details' => 'Projekt-detaljer',
    'public_key_help' => 'For at gøre det lettere at starte har vi genereret en SSH-nøgle som du kan bruge til dette projekt. For at bruge den behøver du blot tilføje den følgende public key til "deployment keys" sektionen
i din foretrukne hosting-platform.',
    'select_repository_type' => 'Vælg repository-type...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'remote' => 'Ekstern URL',
    'local' => 'Lokalt filsystem',
    'hg'    => 'Mercurial',

    'where_hosted' => 'Hvor er dit projekt hosted?',
    'choose_github' => 'Vælg et GitHub-repository:',

    'repo_name' => 'Repository-navn / URL (ekstern) eller filsystem-sti (lokal)',
    'project_title' => 'Projekt-titel',
    'project_private_key' => 'Privat nøgle med adgang til dette repository
(tom for lokal nøgle og/eller anonym adgang)',
    'build_config' => 'PHP Censor build-konfiguration for dette projekt
(hvis du ikke har mulighed for at tilføje en .php-censor.yml (.phpci.yml|phpci.yml) fil i projektets repository)',
    'default_branch' => 'Default branch navn',
    'allow_public_status' => 'Tillad offentlig status-side og -billede for dette projekt?',
    'archived' => 'Archived',
    'archived_menu' => 'Archived',
    'save_project' => 'Gem Projekt',

    'error_mercurial' => 'Mercurial repository-URL skal starte med http:// eller https://',
    'error_remote' => 'Repository-URL skal starte med git://, http:// eller https://',
    'error_gitlab' => 'GitLab repository-navn skal være i formatet "user@domæne.tld:ejernavn/repositorynavn.git"',
    'error_github' => 'Repository-navn skal være i formatet "ejernavn/repositorynavn"',
    'error_bitbucket' => 'Repository-navn skal være i formatet "ejernavn/repositorynavn"',
    'error_path' => 'Stien du indtastede findes ikke.',

    // View Project:
    'all_branches' => 'Alle branches',
    'builds' => 'Builds',
    'id' => 'ID',
    'date' => 'Date',
    'project' => 'Projekt',
    'commit' => 'Commit',
    'branch' => 'Branch',
    'status' => 'Status',
    'prev_link' => '&laquo; Forrige',
    'next_link' => 'Næste &raquo;',
    'public_key' => 'Offentlig nøgle',
    'delete_build' => 'Slet Build',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'For at køre dette build automatisk når nye commits bliver pushed skal du tilføje nedenstående
URL som nyt "Webhook" i <a href="https://github.com/%s/settings/hooks">Webhooks
and Services</a> under dit GitHub-repository.',

    'webhooks_help_gitlab' => 'For at køre dette build automatisk når nye commits bliver pushed kan du tilføje nedenstående URL
som en "WebHook URL" i Web Hooks-sektionen i dit GitLab-repository.',

    'webhooks_help_bitbucket' => 'For at køre dette build automatisk når nye commits bliver pushed skal du tilføje nedenstående
URL som "POST" service i
<a href="https://bitbucket.org/%s/admin/services">
Services</a> sektionen under dit Bitbucket-repository.',

    // View Build
    'build_x_not_found' => 'Build med ID %d findes ikke.',
    'build_n' => 'Build %d',
    'rebuild_now' => 'Gentag Build',


    'committed_by_x' => 'Committed af %s',
    'commit_id_x' => 'Commit: %s',

    'chart_display' => 'Denne graf vises når buildet er færdigt.',

    'build' => 'Build',
    'lines' => 'Linjer',
    'comment_lines' => 'Kommentar-linjer',
    'noncomment_lines' => 'Ikke-kommentar-linjer',
    'logical_lines' => 'Logiske linjer',
    'lines_of_code' => 'Kode-linjer',
    'build_log' => 'Build-log',
    'quality_trend' => 'Kvalitets-trend',
    'codeception_errors' => 'Codeception-fejl',
    'phpmd_warnings' => 'PHPMD-advarsler',
    'phpcs_warnings' => 'PHPCS-advarsler',
    'phpcs_errors' => 'PHPCS-fejl',
    'phplint_errors' => 'Lint-fejl',
    'phpunit_errors' => 'PHPUnit-fejl',
    'phpdoccheck_warnings' => 'Manglende Docblocks',
    'issues' => 'Sager',

    'codeception' => 'Codeception',
    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Manglende Docblocks',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHP Unit',

    'file' => 'Fil',
    'line' => 'Linje',
    'class' => 'Klasse',
    'method' => 'Funktion',
    'message' => 'Besked',
    'start' => 'Start',
    'end' => 'Slut',
    'from' => 'Fra',
    'to' => 'Til',
    'result' => 'Resultat',
    'ok' => 'OK',
    'took_n_seconds' => 'Tog %d sekunder',
    'build_started' => 'Build Startet',
    'build_finished' => 'Build Afsluttet',
    'test_message' => 'Message',
    'test_no_message' => 'No message',
    'test_success' => 'Successful: %d',
    'test_fail' => 'Failures: %d',
    'test_skipped' => 'Skipped: %d',
    'test_error' => 'Errors: %d',
    'test_todo' => 'Todos: %d',
    'test_total' => '%d test(s)',

    // Users
    'name' => 'Navn',
    'password_change' => 'Adgangskode (tom hvis du ikke ønsker at ændre koden)',
    'save' => 'Gem &raquo;',
    'update_your_details' => 'Opdatér oplysninger',
    'your_details_updated' => 'Dine oplysninger blev gemt.',
    'add_user' => 'Tilføj bruger',
    'is_admin' => 'Administrator?',
    'yes' => 'Ja',
    'no' => 'Nej',
    'edit' => 'Redigér',
    'edit_user' => 'Redigér Bruger',
    'delete_user' => 'Slet Bruger',
    'user_n_not_found' => 'Brugeren med ID %d findes ikke.',
    'is_user_admin' => 'Er denne bruger en administrator?',
    'save_user' => 'Gem Bruger',

    // Settings:
    'settings_saved' => 'Dine indstillinger blev gemt.',
    'settings_check_perms' => 'Dine indstillinger kunne ikke gemmes, kontrollér rettighederne på din config.yml fil.',
    'settings_cannot_write' => 'PHP Censor kan ikke skrive til din config.yml fil, indstillinger bliver muligvis ikke gemt korrekt før dette problem løses.',
    'settings_github_linked' => 'Din GitHub-konto er nu tilsluttet.',
    'settings_github_not_linked' => 'Din GitHub-konto kunne ikke tilsluttes.',
    'build_settings' => 'Build-indstillinger',
    'github_application' => 'GitHub-applikation',
    'github_sign_in' => 'Før du kan bruge GitHub skal du <a href="%s">logge ind</a> og give PHP Censor
adgang til din konto.',
    'github_app_linked' => 'PHP Censor blev tilsluttet din GitHub-konto.',
    'github_where_to_find' => 'Hvor disse findes...',
    'github_where_help' => 'Hvis du ejer applikationen du ønsker at bruge kan du finde denne information i
<a href="https://github.com/settings/applications">applications</a> under indstillinger.',

    'email_settings' => 'Email-indstillinger',
    'email_settings_help' => 'Før PHP Censor kan sende build-notifikationer via email
skal du konfigurere nedenstående SMTP-indstillinger.',

    'application_id' => 'Application ID',
    'application_secret' => 'Application Secret',

    'smtp_server' => 'SMTP-server',
    'smtp_port' => 'SMTP-port',
    'smtp_username' => 'SMTP-brugernavn',
    'smtp_password' => 'SMTP-adgangskode',
    'from_email_address' => 'Fra email-adresse',
    'default_notification_address' => 'Default notifikations-email-adresse',
    'use_smtp_encryption' => 'Brug SMTP-kryptering?',
    'none' => 'Ingen',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Betragt et build som fejlet efter',
    '5_mins' => '5 minutter',
    '15_mins' => '15 minutter',
    '30_mins' => '30 minutter',
    '1_hour' => '1 time',
    '3_hours' => '3 timer',

    // Plugins
    'cannot_update_composer' => 'PHP Censor kan ikke opdatere composer.json da filen ikke kan skrives.',
    'x_has_been_removed' => '%s er blevet slettet.',
    'x_has_been_added' => '%s blev tilføjet til composer.json for dig og vil blive installeret næste gang
du kører composer update.',
    'enabled_plugins' => 'Aktive plugins',
    'provided_by_package' => 'Via pakke',
    'installed_packages' => 'Installerede pakker',
    'suggested_packages' => 'Forslag til pakker',
    'title' => 'Titel',
    'description' => 'Beskrivelse',
    'version' => 'Version',
    'install' => 'Installér &raquo;',
    'remove' => 'Fjern &raquo;',
    'search_packagist_for_more' => 'Søg på Packagist efter flere pakker',
    'search' => 'Søg &raquo;',

    // Update
    'update_app' => 'Opdatér databasen med ændrede modeller',
    'updating_app' => 'Opdaterer PHP Censor-database:',
    'not_installed' => 'PHP Censor lader til ikke at være installeret.',
    'install_instead' => 'Installér venligst PHP Censor via php-censor:install istedet.',

    // Build Plugins:
    'passing_build' => 'Succesfuldt Build',
    'failing_build' => 'Fejlet Build',
    'log_output' => 'Log-output:',
];
