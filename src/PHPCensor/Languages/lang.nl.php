<?php

return [
    'language_name' => 'Nederlands',
    'language' => 'Taal',

    // Log in:
    'log_in_to_app' => 'Log in op PHP Censor',
    'login_error' => 'Incorrect e-mailadres of wachtwoord',
    'forgotten_password_link' => 'Wachtwoord vergeten?',
    'reset_emailed' => 'We hebben je een link gemaild om je wachtwoord opnieuw in te stellen.',
    'reset_header' => '<strong>Geen zorgen!</strong><br>Vul hieronder gewoon je e-mailadres in en we sturen
je een link on je wachtwoord te resetten.',
    'reset_email_address' => 'Vul je e-mailadres in:',
    'reset_send_email' => 'Verstuur wachtwoord reset',
    'reset_enter_password' => 'Gelieve een nieuw wachtwoord in te voeren',
    'reset_new_password' => 'Nieuw wachtwoord:',
    'reset_change_password' => 'Wijzig wachtwoord',
    'reset_no_user_exists' => 'Er bestaat geen gebruiker met dit e-mailadres, gelieve opnieuw te proberen.',
    'reset_email_body' => 'Hallo %s,

Je ontvangt deze email omdat jij, of iemand anders, je wachtwoord voor PHP Censor opnieuw wenst in te stellen.

Indien jij dit was, klik op deze link op je wachtwoord opnieuw in te stellen: %ssession/reset-password/%d/%s

Zoniet, negeer deze e-mail en er zal geen verdere actie ondernomen worden.

Bedankt,

PHP Censor',

    'reset_email_title' => 'PHP Censor wachtwoord reset voor %s',
    'reset_invalid' => 'Ongeldig wachtwoord reset verzoek',
    'email_address' => 'E-mailadres',
    'login' => 'Login / Email Address',
    'password' => 'Wachtwoord',
    'log_in' => 'Log in',


    // Top Nav
    'toggle_navigation' => 'Wissel Navigatie',
    'n_builds_pending' => '%d builds wachtend',
    'n_builds_running' => '%d builds lopende',
    'edit_profile' => 'Wijzig profiel',
    'sign_out' => 'Uitloggen',
    'branch_x' => 'Branch: %s',
    'created_x' => 'Aangemaakt: %s',
    'started_x' => 'Gestart: %s',

    // Sidebar
    'hello_name' => 'Hallo, %s',
    'dashboard' => 'Startpagina',
    'admin_options' => 'Administratie opties',
    'add_project' => 'Project toevoegen',
    'settings' => 'Instellingen',
    'manage_users' => 'Gebruikers beheren',
    'plugins' => 'Plugins',
    'view' => 'Bekijk',
    'build_now' => 'Build nu',
    'edit_project' => 'Wijzig project',
    'delete_project' => 'Verwijder project',

    // Project Summary:
    'no_builds_yet' => 'Nog geen builds!',
    'x_of_x_failed' => '%d van de laatste %d builds faalden.',
    'x_of_x_failed_short' => '%d / %d faalden.',
    'last_successful_build' => 'De laatste succesvolle build was %s.',
    'never_built_successfully' => 'Dit project heeft geen succesvolle build gehad.',
    'all_builds_passed' => 'Elk van de laatste %d builds slaagden.',
    'all_builds_passed_short' => '%d / %d slaagden.',
    'last_failed_build' => 'De laatste gefaalde build was %s.',
    'never_failed_build' => 'Dit project heeft geen gefaalde build gehad.',
    'view_project' => 'Bekijk project',

    // Timeline:
    'latest_builds' => 'Laatste builds',
    'pending' => 'In afwachting',
    'running' => 'Lopende',
    'success' => 'Succes',
    'failed' => 'Gefaald',
    'manual_build' => 'Manuele build',

    // Add/Edit Project:
    'new_project' => 'Nieuw project',
    'project_x_not_found' => 'Project met ID %d bestaat niet.',
    'project_details' => 'Project details',
    'public_key_help' => 'Om eenvoudiger te kunnen starten, hebben we een SSH sleutelpaar gegenereerd
voor dit project. Om het te gebruiken, voeg onderstaande public key toe aan de "deploy keys" sectie
van je gekozen source code hosting platform',
    'select_repository_type' => 'Selecteer repository type...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'remote' => 'Externe URL',
    'local' => 'Lokaal pad',
    'hg'    => 'Mercurial',

    'where_hosted' => 'Waar wordt je project gehost?',
    'choose_github' => 'Selecteer een GitHub repository:',

    'repo_name' => 'Repository naam / URL (extern) of pad (lokaal)',
    'project_title' => 'Projecttitel',
    'project_private_key' => 'Private key voor toegang tot repository
(laat leeg voor lokaal en/of anonieme externen)',
    'build_config' => 'PHP Censor build configuratie voor dit project
(indien je geen .php-censor.yml (.phpci.yml|phpci.yml) bestand aan de project repository kan toevoegen)',
    'default_branch' => 'Standaard branch naam',
    'allow_public_status' => 'Publieke statuspagina en afbeelding beschikbaar maken voor dit project?',
    'archived' => 'Archived',
    'archived_menu' => 'Archived',
    'save_project' => 'Project opslaan',

    'error_mercurial' => 'Mercurial repository URL dient te starten met http:// of https://',
    'error_remote' => 'Repository URL dient te starten met git://, http:// of https://',
    'error_gitlab' => 'GitLab repository naam dient in het formaat "gebruiker@domain.tld/eigenaar/repo.git" te zijn',
    'error_github' => 'Repository naam dient in het formaat "eigenaar/repo" te zijn',
    'error_bitbucket' => 'Repository naam dient in het formaat "eigenaar/repo" te zijn',
    'error_path' => 'Het opgegeven pad bestaat niet.',

    // View Project:
    'all_branches' => 'Alle brances',
    'builds' => 'Builds',
    'id' => 'ID',
    'date' => 'Datum',
    'project' => 'Project',
    'commit' => 'Commit',
    'branch' => 'Branch',
    'status' => 'Status',
    'prev_link' => '&laquo; Vorig',
    'next_link' => 'Volgend &raquo;',
    'public_key' => 'Public Key',
    'delete_build' => 'Verwijder build',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'Voor automatische builds wanneer nieuwe commits worden gepusht, dient onderstaande URL
als nieuwe "Webhook" in de <a href="https://github.com/%s/settings/hooks">Webhooks
and Services</a> sectie van je GitHub repository toegevoegd worden.',

    'webhooks_help_gitlab' => 'Voor automatische builds wanneer nieuwe commits worden gepusht, dient onderstaande URL
als nieuwe "Webhook URL" in de Web Hooks sectie van je GitLab repository toegevoegd worden.',

    'webhooks_help_bitbucket' => 'Voor automatische builds wanneer nieuwe commits worden gepusht, dient onderstaande URL
als "POST" service in de in de
<a href="https://bitbucket.org/%s/admin/services">
Services</a> sectie van je Bitbucket repository toegevoegd worden.',

    // View Build
    'build_x_not_found' => 'Build met ID %d bestaat niet.',
    'build_n' => 'Build %d',
    'rebuild_now' => 'Rebuild nu',


    'committed_by_x' => 'Committed door %s',
    'commit_id_x' => 'Commit: %s',

    'chart_display' => 'Deze grafiek wordt getoond zodra de build compleet is.',

    'build' => 'Build',
    'lines' => 'Lijnen',
    'comment_lines' => 'Commentaarlijnen',
    'noncomment_lines' => 'Niet-commentaarlijnen',
    'logical_lines' => 'Logische lijnen',
    'lines_of_code' => 'Lijnen code',
    'build_log' => 'Build Log',
    'quality_trend' => 'Kwaliteitstrend',
    'codeception_errors' => 'Codeception Fouten',
    'phpmd_warnings' => 'PHPMD Waarschuwingen',
    'phpcs_warnings' => 'PHPCS Waarschuwingen',
    'phpcs_errors' => 'PHPCS Fouten',
    'phplint_errors' => 'Lint Fouten',
    'phpunit_errors' => 'PHPUnit Fouten',
    'phpdoccheck_warnings' => 'Ontbrekende Docblocks',
    'issues' => 'Problemen',

    'codeception' => 'Codeception',
    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Ontbrekende Docblocks',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHPUnit',

    'file' => 'Bestand',
    'line' => 'Lijn',
    'class' => 'Class',
    'method' => 'Method',
    'message' => 'Boodschap',
    'start' => 'Start',
    'end' => 'Einde',
    'from' => 'Van',
    'to' => 'Tot',
    'result' => 'Resultaat',
    'ok' => 'OK',
    'took_n_seconds' => 'Duurde %d seconden',
    'build_started' => 'Build gestart',
    'build_finished' => 'Build beëindigd',
    'test_message' => 'Message',
    'test_no_message' => 'No message',
    'test_success' => 'Successful: %d',
    'test_fail' => 'Failures: %d',
    'test_skipped' => 'Skipped: %d',
    'test_error' => 'Errors: %d',
    'test_todo' => 'Todos: %d',
    'test_total' => '%d test(s)',

    // Users
    'name' => 'Naam',
    'password_change' => 'Wachtwoord (laat leeg indien je niet wenst te veranderen)',
    'save' => 'Opslaan &raquo;',
    'update_your_details' => 'Wijzig je gegevens',
    'your_details_updated' => 'Je gegevens werden gewijzigd',
    'add_user' => 'Gebruiker toevoegen',
    'is_admin' => 'Is administrator?',
    'yes' => 'Ja',
    'no' => 'Nee',
    'edit' => 'Wijzig',
    'edit_user' => 'Gebruiker wijzigen',
    'delete_user' => 'Gebruiker wissen',
    'user_n_not_found' => 'Gebruiker met ID %d bestaat niet.',
    'is_user_admin' => 'Is deze gebruiker administrator?',
    'save_user' => 'Gebruiker opslaan',

    // Settings:
    'settings_saved' => 'Je instellingen werden opgeslagen.',
    'settings_check_perms' => 'Je instellingen konden niet worden opgeslagen, controleer de permissies van je config.yml bestand.',
    'settings_cannot_write' => 'PHP Censor kan niet schrijven naar je config.yml bestand, instellingen worden mogelijks
niet goed opgeslagen tot dit opgelost is.',
    'settings_github_linked' => 'Je GitHub account werd gelinkt.',
    'settings_github_not_linked' => 'Je GitHub account kon niet gelinkt worden.',
    'build_settings' => 'Build instellingen',
    'github_application' => 'GitHub toepassing',
    'github_sign_in' => 'Vooraleer je GitHub kan gebruiken, dien je <a href="%s">in te loggen</a> en
PHP Censor toegang te verlenen tot je account.',
    'github_app_linked' => 'PHP werd succesvol gelinkt aan je GitHub account.',
    'github_where_to_find' => 'Waar zijn deze te vinden...',
    'github_where_help' => 'Indien je eigenaar bent van de toepassing die je wens te gebruiken, kan je deze informatie
in je <a href="https://github.com/settings/applications">applications</a> instellingen pagina vinden.',

    'email_settings' => 'E-mail instellingen',
    'email_settings_help' => 'Vooraleer PHP Censor je build status e-mails kan sturen,
dien je eerst je SMTP instellingen te configureren.',

    'application_id' => 'Toepassings ID',
    'application_secret' => 'Toepassings geheime code',

    'smtp_server' => 'SMTP Server',
    'smtp_port' => 'SMTP Poort',
    'smtp_username' => 'SMTP Gebruikersnaam',
    'smtp_password' => 'SMTP Wachtwoord',
    'from_email_address' => 'Van e-mailadres',
    'default_notification_address' => 'Standaard melding e-mailadres',
    'use_smtp_encryption' => 'SMTP Encryptie gebruiken?',
    'none' => 'Geen',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Beschouw een build gefaald na',
    '5_mins' => '5 minuten',
    '15_mins' => '15 minuten',
    '30_mins' => '30 minuten',
    '1_hour' => '1 uur',
    '3_hours' => '3 uur',

    // Plugins
    'cannot_update_composer' => 'PHP Censor kan composer.json niet aanpassen gezien het niet schrijfbaar is.',
    'x_has_been_removed' => '%s werd verwijderd.',
    'x_has_been_added' => '%s werd toegevoegd aan composer.json en zal geïnstalleerd worden de volgende
keer je composer update uitvoert.',
    'enabled_plugins' => 'Ingeschakelde plugins',
    'provided_by_package' => 'Voorzien door package',
    'installed_packages' => 'Geinstalleerde packages',
    'suggested_packages' => 'Voorgestelde packages',
    'title' => 'Titel',
    'description' => 'Beschrijving',
    'version' => 'Versie',
    'install' => 'Installeer &raquo;',
    'remove' => 'Verwijder &raquo;',
    'search_packagist_for_more' => 'Doorzoek Packagist naar meer packages',
    'search' => 'Zoek &raquo;',

    // Update
    'update_app' => 'Update de database naar het beeld van gewijzigde modellen.',
    'updating_app' => 'PHP Censor database wordt geüpdatet:',
    'not_installed' => 'PHP Censor lijkt niet geïnstalleerd te zijn.',
    'install_instead' => 'Gelieve PHP Censor via php-censor:install te installeren.',

    // Build Plugins:
    'passing_build' => 'Slagende build',
    'failing_build' => 'Falende build',
    'log_output' => 'Log output:',
];
