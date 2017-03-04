<?php

return [
    'language_name' => 'Deutsch',
    'language' => 'Sprache',

    // Log in:
    'log_in_to_app' => 'In PHP Censor einloggen',
    'login_error' => 'Fehlerhafte Emailadresse oder fehlerhaftes Passwort',
    'forgotten_password_link' => 'Passwort vergessen?',
    'reset_emailed' => 'Wir haben Ihnen einen Link geschickt, um Ihr Passwort zurückzusetzen',
    'reset_header' => '<strong>Keine Panik!</strong><br>Geben Sie einfach unten Ihre Emailadresse an
                        und wir senden Ihnen einen Link, um Ihr Passwort zurückzusetzen',
    'reset_email_address' => 'Geben Sie Ihre Emailadresse an:',
    'reset_send_email' => 'Link senden',
    'reset_enter_password' => 'Bitte geben Sie ein neues Passwort ein',
    'reset_new_password' => 'Neues Passwort:',
    'reset_change_password' => 'Passwort ändern',
    'reset_no_user_exists' => 'Es existiert kein User mit dieser Emailadresse, versuchen Sie es bitte noch einmal.',
    'reset_email_body' => 'Hallo %s,

Sie haben diese Email erhalten, weil Sie, oder jemand anders, einen Link zum Zurücksetzen Ihres Passwortes für PHP Censor verlangt hat.

Wenn Sie diesen Link verlangt haben, klicken Sie bitte hier, um Ihr Passwort zurückzusetzen: %ssession/reset-password/%d/%s

Falls nicht, ignorieren Sie diese Email bitte, und es wird nichts geändert.

Danke,

PHP Censor',

    'reset_email_title' => 'PHP Censor Passwort zurücksetzen für %s',
    'reset_invalid' => 'Fehlerhafte Anfrage für das Zurücksetzen eines Passwortes',
    'email_address' => 'Emailadresse',
    'login' => 'Login / Emailadresse',
    'password' => 'Passwort',
    'log_in' => 'Einloggen',


    // Top Nav
    'toggle_navigation' => 'Navigation umschalten',
    'n_builds_pending' => '%d Builds ausstehend',
    'n_builds_running' => '%d Builds werden ausgeführt',
    'edit_profile' => 'Profil bearbeiten',
    'sign_out' => 'Ausloggen',
    'branch_x' => 'Branch: %s',
    'created_x' => 'Erstellt: %s',
    'started_x' => 'Gestartet: %s',

    // Sidebar
    'hello_name' => 'Hallo, %s',
    'dashboard' => 'Dashboard',
    'admin_options' => 'Administration',
    'add_project' => 'Projekt hinzufügen',
    'settings' => 'Einstellungen',
    'manage_users' => 'Benutzereinstellungen',
    'plugins' => 'Plugins',
    'view' => 'Ansehen',
    'build_now' => 'Jetzt bauen',
    'edit_project' => 'Projekt bearbeiten',
    'delete_project' => 'Projekt löschen',

    // Project Summary:
    'no_builds_yet' => 'Bisher noch keine Builds!',
    'x_of_x_failed' => '%d der letzten %d Builds sind fehlgeschlagen.',
    'x_of_x_failed_short' => '%d / %d fehlgeschlagen.',
    'last_successful_build' => ' Der letzte erfolgreiche Build war %s.',
    'never_built_successfully' => ' Dieses Projekt hatte bisher noch keinen erfolgreichen Build.',
    'all_builds_passed' => 'Jeder der letzten %d Builds war erfolgreich.',
    'all_builds_passed_short' => '%d / %d erfolgreich.',
    'last_failed_build' => ' Der letzte fehlgeschlagene Build war %s.',
    'never_failed_build' => ' Dieses Projekt hat keine fehlgeschlagenen Builds.',
    'view_project' => 'Projekt ansehen',

    // Timeline:
    'latest_builds' => 'Die neusten Builds',
    'pending' => 'Ausstehend',
    'running' => 'Wird ausgeführt',
    'success' => 'Erfolg',
    'failed' => 'Fehlgeschlagen',
    'manual_build' => 'Manueller Build',

    // Add/Edit Project:
    'new_project' => 'Neues Projekt',
    'project_x_not_found' => 'Projekt mit ID %d existiert nicht.',
    'project_details' => 'Projektdetails',
    'public_key_help' => 'Um Ihnen den Einstieg zu erleichtern, haben wir ein SSH-Key-Paar für dieses Projekt
generiert. Um es zu verwenden, fügen Sie einfach den folgenden Public Key im Abschnitt
"Deploy Keys" Ihrer bevorzugten Quellcodehostingplattform hinzu.',
    'select_repository_type' => 'Wählen Sie den Typ des Repositories...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'remote' => 'Externe URL',
    'local' => 'Lokaler Pfad',
    'hg'    => 'Mercurial',
    'svn'   => 'Subversion',

    'where_hosted' => 'Wo wird Ihr Projekt gehostet?',
    'choose_github' => 'Wählen Sie ein GitHub Repository:',

    'repo_name' => 'Name/URL (extern) oder Pfad (lokal) des Repositories',
    'project_title' => 'Projekttitel',
    'project_private_key' => 'Private Key für den Zugang zum Repository
                                (leer lassen für lokale und oder anonyme externe Zugriffe)',
    'build_config' => 'PHP Censor Buildkonfiguration für dieses Projekt
                                (falls Sie Ihrem Projektrepository kein .php-censor.yml (.phpci.yml|phpci.yml) hinzufügen können)',
    'default_branch' => 'Name des Standardbranches',
    'allow_public_status' => 'Öffentliche Statusseite und -bild für dieses Projekt einschalten?',
    'archived' => 'Archiviert',
    'archived_menu' => 'Archiviert',
    'save_project' => 'Projekt speichern',

    'error_mercurial' => 'Mercurial Repository-URL muss mit http://, oder https:// beginnen',
    'error_remote' => 'Repository-URL muss mit git://, http://, oder https:// beginnen',
    'error_gitlab' => 'GitLab Repositoryname muss im Format "user@domain.tld:owner/repo.git" sein',
    'error_github' => 'Repositoryname muss im Format "besitzer/repo" sein',
    'error_bitbucket' => 'Repositoryname muss im Format "besitzer/repo" sein',
    'error_path' => 'Der angegebene Pfad existiert nicht',

    // View Project:
    'all_branches' => 'Alle Branches',
    'builds' => 'Builds',
    'id' => 'ID',
    'date' => 'Datum',
    'project' => 'Projekt',
    'commit' => 'Commit',
    'branch' => 'Branch',
    'status' => 'Status',
    'prev_link' => '&laquo; Vorherige',
    'next_link' => 'Nächste &raquo;',
    'public_key' => 'Public Key',
    'delete_build' => 'Build löschen',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'Um für dieses Projekt automatisch einen Build zu starten, wenn neue Commits gepushed
                                werden, fügen Sie die untenstehende URL in der
                                <a href="https://github.com/%s/settings/hooks">Webhooks and Services</a>-Sektion Ihres
                                GitHub Repositories als neuen "Webhook" hinzu.',

    'webhooks_help_gitlab' => 'Um für dieses Projekt automatisch einen Build zu starten, wenn neue Commits gepushed werden, fügen Sie die untenstehende URL in der Web Hooks Sektion Ihres GitLab Repositories hinzu.',

    'webhooks_help_bitbucket' => 'Um für dieses Projekt automatisch einen Build zu starten, wenn neue Commits gepushed werden, fügen Sie die untenstehende URL als "POST" Service in der <a href="https://bitbucket.org/%s/admin/services">Services</a>-Sektion Ihres Bitbucket Repositories hinzu.',

    // View Build
    'errors' => 'Fehler',
    'information' => 'Information',

    'build_x_not_found' => 'Build mit ID %d existiert nicht.',
    'build_n' => 'Build %d',
    'rebuild_now' => 'Build neu starten',


    'committed_by_x' => 'Committed von %s',
    'commit_id_x' => 'Commit: %s',

    'chart_display' => 'Dieses Diagramm wird angezeigt, sobald der Build abgeschlossen ist.',

    'build' => 'Build',
    'lines' => 'Zeilen',
    'comment_lines' => 'Kommentarzeilen',
    'noncomment_lines' => 'Nicht-Kommentarzeilen',
    'logical_lines' => 'Zeilen mit Logik',
    'lines_of_code' => 'Anzahl Codezeilen',
    'build_log' => 'Buildprotokoll',
    'quality_trend' => 'Qualitätstrend',
    'codeception_errors' => 'Codeception Errors',
    'phpmd_warnings' => 'PHPMD Warnings',
    'phpcs_warnings' => 'PHPCS Warnings',
    'phpcs_errors' => 'PHPCS Errors',
    'phplint_errors' => 'Lint Errors',
    'phpunit_errors' => 'PHPUnit Errors',
    'phpdoccheck_warnings' => 'Fehlende Docblocks',
    'issues' => 'Probleme',

    'codeception' => 'Codeception',
    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Fehlende Docblocks',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHP Unit',
    'technical_debt' => 'Technische Schulden',
    'behat' => 'Behat',

    'codeception_feature' => 'Feature',
    'codeception_suite' => 'Suite',
    'codeception_time' => 'Zeit',
    'codeception_synopsis' => '<strong>%1$d</strong> Tests in <strong>%2$f</strong> Sekunden ausgeführt.
                               <strong>%3$d</strong> Fehler.',

    'file' => 'Datei',
    'line' => 'Zeile',
    'class' => 'Klasse',
    'method' => 'Methode',
    'message' => 'Nachricht',
    'start' => 'Start',
    'end' => 'Ende',
    'from' => 'Von',
    'to' => 'Bis',
    'result' => 'Resultat',
    'ok' => 'OK',
    'took_n_seconds' => 'Benötigte %d Sekunden',
    'build_started' => 'Build gestartet',
    'build_finished' => 'Build abgeschlossen',
    'test_message' => 'Nachricht',
    'test_no_message' => 'Keine Nachricht',
    'test_success' => 'Erfolgreich: %d',
    'test_fail' => 'Fehlschläge: %d',
    'test_skipped' => 'Übersprungen: %d',
    'test_error' => 'Fehler: %d',
    'test_todo' => 'Todos: %d',
    'test_total' => '%d Test(s)',

    // Users
    'name' => 'Name',
    'password_change' => 'Passwort (leerlassen, wenn Sie es nicht ändern möchten)',
    'save' => 'Speichern &raquo;',
    'update_your_details' => 'Aktualisieren Sie Ihre Details',
    'your_details_updated' => 'Ihre Details wurden aktualisiert.',
    'add_user' => 'Benutzer hinzufügen',
    'is_admin' => 'Administrator?',
    'yes' => 'Ja',
    'no' => 'Nein',
    'edit' => 'Bearbeiten',
    'edit_user' => 'Benutzer bearbeiten',
    'delete_user' => 'Benutzer löschen',
    'user_n_not_found' => 'Benutzer mit ID %d existiert nicht.',
    'is_user_admin' => 'Ist dieser Benutzer Administrator?',
    'save_user' => 'Benutzer speichern',

    // Settings:
    'settings_saved' => 'Ihre Einstellungen wurden gespeichert.',
    'settings_check_perms' => 'Ihre Einstellungen konnten nicht gespeichert werden, bitte überprüfen Sie die
                                Berechtigungen Ihrer config.yml-Datei',
    'settings_cannot_write' => 'PHP Censor konnte config.yml nicht schreiben. Einstellungen könnten nicht richtig gespeichert werden, bis das Problem behoben ist.',
    'settings_github_linked' => 'Ihr GitHub-Konto wurde verknüpft.',
    'settings_github_not_linked' => 'Ihr GitHub-Konto konnte nicht verknüpft werden.',
    'build_settings' => 'Buildeinstellungen',
    'github_application' => 'GitHub-Applikation',
    'github_sign_in' => 'Bevor Sie anfangen GitHub zu verwenden, müssen Sie sich erst <a href="%s">einloggen</a> und PHP Censor Zugriff auf Ihr Nutzerkonto gewähren',
    'github_app_linked' => 'PHP Censor wurde erfolgreich mit Ihrem GitHub-Konto verknüpft.',
    'github_where_to_find' => 'Wo Sie diese finden...',
    'github_where_help' => 'Wenn Sie der Besitzer der Applikation sind, die Sie gerne verwenden möchten, können Sie
                            diese Einstellungen in Ihrem "<a href="https://github.com/settings/applications">applications</a>
                            settings"-Bereich finden.',

    'email_settings' => 'Emaileinstellungen',
    'email_settings_help' => 'Bevor PHP Censor E-Mails zum Buildstatus verschicken kann,
                                müssen Sie Ihre SMTP-Einstellungen unten konfigurieren',

    'application_id' => 'Applikations-ID',
    'application_secret' => 'Applikations-Secret',

    'smtp_server' => 'SMTP Server',
    'smtp_port' => 'SMTP Port',
    'smtp_username' => 'SMTP Benutzername',
    'smtp_password' => 'SMTP Passwort',
    'from_email_address' => 'Absenderadresse',
    'default_notification_address' => 'Standardadresse für Benachrichtigungen',
    'use_smtp_encryption' => 'SMTP-Verschlüsselung verwenden?',
    'none' => 'Keine',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Einen Build als fehlgeschlagen ansehen nach',
    '5_mins' => '5 Minuten',
    '15_mins' => '15 Minuten',
    '30_mins' => '30 Minuten',
    '1_hour' => '1 Stunde',
    '3_hours' => '3 Stunden',

    // Plugins
    'cannot_update_composer' => 'PHP Censor kann composer.json nicht für Sie aktualisieren, da Schreibrechte benötigt werden.',
    'x_has_been_removed' => '%s wurde entfernt.',
    'x_has_been_added' => '%s wurde für Sie dem composer.json hinzugefügt und wird installiert, sobald Sie das nächste mal composer update ausführen.',
    'enabled_plugins' => 'Eingeschaltene Plugins',
    'provided_by_package' => 'Von Package bereitgestellt',
    'installed_packages' => 'Installierte Packages',
    'suggested_packages' => 'Vorgeschlagene Packages',
    'title' => 'Titel',
    'description' => 'Beschreibung',
    'version' => 'Version',
    'install' => 'Installieren &raquo;',
    'remove' => 'Entfernen &raquo;',
    'search_packagist_for_more' => 'Packagist nach mehr Packages durchsuchen',
    'search' => 'Suchen &raquo;',

    // Summary plugin
    'build-summary' => 'Zusammenfassung',
    'stage' => 'Abschnitt',
    'duration' => 'Dauer',
    'plugin' => 'Plugin',
    'stage_setup' => 'Vorbereitung',
    'stage_test' => 'Test',
    'stage_complete' => 'Vollständig',
    'stage_success' => 'Erfolg',
    'stage_failure' => 'Fehlschlag',
    'stage_broken'  => 'Defekt',
    'stage_fixed' => 'Behoben',

    // Update
    'update_app' => 'Datenbank wird aktualisiert, um den Änderungen der Models zu entsprechen.',
    'updating_app' => 'Aktualisiere PHP Censor-Datenbank:',
    'not_installed' => 'PHP Censor scheint nicht installiert zu sein.',
    'install_instead' => 'Bitte installieren Sie PHP Censor stattdessen via php-censor:install.',

    // Build Plugins:
    'passing_build' => 'Durchlaufender Build',
    'failing_build' => 'Fehlschlagender Build',
    'log_output' => 'Protokollausgabe: ',

    // Error Levels:
    'critical' => 'Kritisch',
    'high' => 'Hoch',
    'normal' => 'Normal',
    'low' => 'Niedrig',

    // Plugins that generate errors:
    'php_mess_detector' => 'PHP Mess Detector',
    'php_code_sniffer' => 'PHP Code Sniffer',
    'php_unit' => 'PHP Unit',
    'php_cpd' => 'PHP Copy/Paste Detector',
    'php_docblock_checker' => 'PHP Docblock Checker',
];
