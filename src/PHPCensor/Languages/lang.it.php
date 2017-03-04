<?php

return [
    'language_name' => 'Italiano',
    'language' => 'Lingua',

    // Log in:
    'log_in_to_app' => 'Accedi a PHP Censor',
    'login_error' => 'Indirizzo email o password errati',
    'forgotten_password_link' => 'Hai dimenticato la tua password?',
    'reset_emailed' => 'Ti abbiamo inviato un link via email per ripristinare la tua password.',
    'reset_header' => '<strong>Non preoccuparti!</strong><br>E\' sufficiente inserire il tuo indirizzo email di seguito e ti invieremo una email con il link per il ripristino della tua password.',
    'reset_email_address' => 'Inserisci il tuo indirizzo email:',
    'reset_send_email' => 'Invia il link di reset della password',
    'reset_enter_password' => 'Per favore inserisci la nuova password',
    'reset_new_password' => 'Nuova password:',
    'reset_change_password' => 'Cambia password',
    'reset_no_user_exists' => 'Non esiste nessun utente con questo indirizzo email, per favore prova ancora.',
    'reset_email_body' => 'Ciao %s,

hai ricevuto questa email perché tu, o qualcun\'altro, ha richiesto un reset della password per PHP Censor.

Se questa mail è tua, per favore apri il seguente link per ripristinare la tua password: %ssession/reset-password/%d/%s

altrimenti, per favore, ignora questa email e nessuna azione verrà intrapresa.

Grazie,

PHP Censor',

    'reset_email_title' => 'Ripristino della password di PHP Censor per %s',
    'reset_invalid' => 'Richeista di ripristino password non valida.',
    'email_address' => 'Indirizzo Email',
    'login' => 'Login / Email Address',
    'password' => 'Password',
    'log_in' => 'Accedi',

    // Top Nav
    'toggle_navigation' => 'Alterna navigazione',
    'n_builds_pending' => '%d build in attesa',
    'n_builds_running' => '%d build in corso',
    'edit_profile' => 'Modifica il Profilo',
    'sign_out' => 'Disconnettiti',
    'branch_x' => 'Branch: %s',
    'created_x' => 'Creato: %s',
    'started_x' => 'Avviato: %s',

    // Sidebar
    'hello_name' => 'Ciao, %s',
    'dashboard' => 'Dashboard',
    'admin_options' => 'Opzioni di amministrazione',
    'add_project' => 'Aggiungi un Progetto',
    'settings' => 'Impostazioni',
    'manage_users' => 'Gestisci Utenti',
    'plugins' => 'Plugins',
    'view' => 'Visualizzazione',
    'build_now' => 'Avvia una build ora',
    'edit_project' => 'Modifica il Progetto',
    'delete_project' => 'Cancella il Progetto',

    // Project Summary:
    'no_builds_yet' => 'Ancora nessuna build!',
    'x_of_x_failed' => '%d delle ultime %d build sono fallite.',
    'x_of_x_failed_short' => '%d / %d fallite.',
    'last_successful_build' => ' L\'ultima build è %s.',
    'never_built_successfully' => ' Questo progetto non ha nessuna build eseguita con successo.',
    'all_builds_passed' => 'Tutte le ultime %d build sono valide.',
    'all_builds_passed_short' => '%d / %d valide.',
    'last_failed_build' => ' L\'ultima build è %s.',
    'never_failed_build' => ' Questo progetto non ha nessuna build fallita.',
    'view_project' => 'Visualizza il Progetto',

    // Timeline:
    'latest_builds' => 'Ultime Build',
    'pending' => 'In attesa',
    'running' => 'In corso',
    'success' => 'Successo',
    'failed' => 'Fallita',
    'manual_build' => 'Build Manuale',

    // Add/Edit Project:
    'new_project' => 'Nuovo Progetto',
    'project_x_not_found' => 'Progetto con ID %d non esistente.',
    'project_details' => 'Dettagli del Progetto',
    'public_key_help' => 'Per rendere più facile la procedura, abbiamo generato una chiave SSH per te da
                          usare per questo progetto. Per usarla, aggiungi la chiave pubblica alle "deploy keys"
                          della piattaforma di gestione del codice che hai scelto.',
    'select_repository_type' => 'Seleziona il tipo di repository...',
    'github' => 'GitHub',
    'bitbucket' => 'Bitbucket',
    'gitlab' => 'GitLab',
    'remote' => 'URL Remoto',
    'local' => 'Percorso Locale',
    'hg'    => 'Mercurial',

    'where_hosted' => 'Dove è archiviato il tuo progetto?',
    'choose_github' => 'Scegli il repository di GitHub:',

    'repo_name' => 'Nome del Repository / URL (Remoto) o Percorso (Locale)',
    'project_title' => 'Titolo del Progetto',
    'project_private_key' => 'Chiave provata da usare per accedere al repository
                                (lascia vuota per repository locali o remoti con accesso anonimo)',
    'build_config' => 'condigurazione della build di PHP Censor per questo progetto
                                (se non puoi aggiungere il file .php-censor.yml (.phpci.yml|phpci.yml) nel repository di questo progetto)',
    'default_branch' => 'Nome del branch di default',
    'allow_public_status' => 'Vuoi rendere pubblica la pagina dello stato e l\'immagine per questo progetto?',
    'archived' => 'Archived',
    'archived_menu' => 'Archived',
    'save_project' => 'Salva il Progetto',

    'error_mercurial' => 'L\'URL del repository Mercurial URL deve iniziare con http:// o https://',
    'error_remote' => 'L\'URL del repository deve iniziare con git://, http:// o https://',
    'error_gitlab' => 'Il nome del repository di GitLab deve essere nel seguente formato "utente@dominio.tld:proprietario/repository.git"',
    'error_github' => 'Il nome del repository deve essere nel formato "proprietario/repository"',
    'error_bitbucket' => 'Il nome del repository deve essere nel formato "proprietario/repository"',
    'error_path' => 'The path you specified does not exist.',
    'error_path' => 'Il percorso che hai indicato non esiste.',

    // View Project:
    'all_branches' => 'Tutti i Branche',
    'builds' => 'Builds',
    'id' => 'ID',
    'date' => 'Data',
    'project' => 'Progetto',
    'commit' => 'Commit',
    'branch' => 'Branch',
    'status' => 'Stato',
    'prev_link' => '&laquo; Precedente',
    'next_link' => 'Successivo &raquo;',
    'public_key' => 'Chiave pubblica',
    'delete_build' => 'Rimuovi build',

    'webhooks' => 'Webhooks',
    'webhooks_help_github' => 'Per effettuare la build automatica di questo progetto quando vengono inseriti nuovi commit,
                                aggiungi l\'URL seguente come "Webhook" nella sezione
                                <a href="https://github.com/%s/settings/hooks">Webhooks and Services</a> del tuo
                                repository su GitHub.',

    'webhooks_help_gitlab' => 'Per effettuare la build automatica di questo progetto quando vengono inseriti nuovi commit,
                                aggiungi l\'URL seguente come "Webhook URL" nella sezione "WebHook URL" del tuo
                                repository GitLab.',

    'webhooks_help_bitbucket' => 'Per effettuare la build automatica di questo progetto quando vengono inseriti nuovi
                                    commit, aggiungi l\'URL seguente come serizio "POST" nella sezione
                                    <a href="https://bitbucket.org/%s/admin/services">Services</a> del tuo repository su
                                    BITBUCKET.',

    // View Build
    'build_x_not_found' => 'La build con ID %d non esite.',
    'build_n' => 'Build %d',
    'rebuild_now' => 'Esegui nuovamente la build ora',


    'committed_by_x' => 'Inviato da %s',
    'commit_id_x' => 'Commit: %s',

    'chart_display' => 'Questo grafico verrà mostrato una volta terminata la build.',

    'build' => 'Build',
    'lines' => 'Linee',
    'comment_lines' => 'Linee di commenti',
    'noncomment_lines' => 'Linee che non sono commenti',
    'logical_lines' => 'Linee di logica',
    'lines_of_code' => 'Linee di codice',
    'build_log' => 'Log della build',
    'quality_trend' => 'Trend della qualità',
    'codeception_errors' => 'Errori di Codeception',
    'phpmd_warnings' => 'Avvisi di PHPMD',
    'phpcs_warnings' => 'Avvisi di PHPCS',
    'phpcs_errors' => 'Errori di PHPCS',
    'phplint_errors' => 'Errori di Lint',
    'phpunit_errors' => 'Errori di PHPUnit',
    'phpdoccheck_warnings' => 'Docblocks mancanti',
    'issues' => 'Segnalazioni',

    'codeception' => 'Codeception',
    'phpcpd' => 'PHP Copy/Paste Detector',
    'phpcs' => 'PHP Code Sniffer',
    'phpdoccheck' => 'Docblocks mancanti',
    'phpmd' => 'PHP Mess Detector',
    'phpspec' => 'PHP Spec',
    'phpunit' => 'PHP Unit',

    'file' => 'File',
    'line' => 'Lina',
    'class' => 'Classe',
    'method' => 'Metodo',
    'message' => 'Messaggio',
    'start' => 'Inizia',
    'end' => 'Finisci',
    'from' => 'Da',
    'to' => 'A',
    'result' => 'Risultati',
    'ok' => 'OK',
    'took_n_seconds' => 'Sono stati impiegati %d seconds',
    'build_started' => 'Build Avviata',
    'build_finished' => 'Build Terminata',
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
    'password_change' => 'Password (lascia vuota se non vuoi modificarla)',
    'save' => 'Salva &raquo;',
    'update_your_details' => 'Aggiorna le tue informazioni',
    'your_details_updated' => 'Le tue informazioni sono state aggiornate.',
    'add_user' => 'Aggiung utent',
    'is_admin' => 'E\' amministratore?',
    'yes' => 'Si',
    'no' => 'No',
    'edit' => 'Modifica',
    'edit_user' => 'Modifica utente',
    'delete_user' => 'Cancella utente',
    'user_n_not_found' => 'L\'utente con ID %d non esiste.',
    'is_user_admin' => 'Questo utente è un amministratore?',
    'save_user' => 'Salva utente',

    // Settings:
    'settings_saved' => 'Le configurazioni sono state salvate.',
    'settings_check_perms' => 'Le configurazioni non possono essere salvate, controlla i permessi del filer config.yml.',
    'settings_cannot_write' => 'PHP Censor non può scrivere il file config.yml, le configurazioni potrebbero non essere
                                salvate correttamente fintanto che il problema non verrà risolto.',
    'settings_github_linked' => 'Il tuo account GitHub è stato collegato.',
    'settings_github_not_linked' => 'Il tuo account GitHub non può essere collegato.',
    'build_settings' => 'Configurazioni della build',
    'github_application' => 'Applicazione GitHub',
    'github_sign_in' => 'Prima di poter iniziare ad usare GitHub, è necessario <a href="%s">collegarsi</a> e garantire
                            a PHP Censor l\'accesso al tuo account.',
    'github_app_linked' => 'PHP Censor è stato collegato correttamente al tuo account GitHub.',
    'github_where_to_find' => 'Dove trovare queste...',
    'github_where_help' => 'Se sei il proprietario dell\'applicazione, puoi trovare queste informazioni nell\'area delle
                              configurazioni dell\'<a href="https://github.com/settings/applications">applicazione</a>.',

    'email_settings' => 'Impostazioni Email',
    'email_settings_help' => 'Prima che possa inviare le email con lo status PHP Censor, devi configurare l\'SMTP qui sotto.',

    'application_id' => 'ID dell\'Applicazione',
    'application_secret' => 'Secret dell\'Applicazione',

    'smtp_server' => 'Server SMTP',
    'smtp_port' => 'Porta SMTP',
    'smtp_username' => 'Username SMTP',
    'smtp_password' => 'Password SMTP',
    'from_email_address' => 'Indirizzio Email del mittente',
    'default_notification_address' => 'Indirizzo email delle notifiche predefinito',
    'use_smtp_encryption' => 'Utilizzare l\'Encrypting per SMTP?',
    'none' => 'No',
    'ssl' => 'SSL',
    'tls' => 'TLS',

    'failed_after' => 'Considera la build fallita dopo',
    '5_mins' => '5 Minuti',
    '15_mins' => '15 Minuti',
    '30_mins' => '30 Minuti',
    '1_hour' => '1 Ora',
    '3_hours' => '3 Ore',

    // Plugins
    'cannot_update_composer' => 'PHP Censor non può aggiornare composer.json per te non essendo scrivibile.',
    'x_has_been_removed' => '%s è stato rimosso.',
    'x_has_been_added' => '%s è stato aggiunto al file composer.json per te, verrà installato la prossima volta che eseguirai
                            composer update.',
    'enabled_plugins' => 'Plugins attivati',
    'provided_by_package' => 'Fornito dal pacchetto',
    'installed_packages' => 'Pacchetti installati',
    'suggested_packages' => 'Paccehtti suggeriti',
    'title' => 'Titolo',
    'description' => 'Descrizione',
    'version' => 'Versione',
    'install' => 'Installa &raquo;',
    'remove' => 'Rimuovi &raquo;',
    'search_packagist_for_more' => 'Cerca altri pacchetti su Packagist',
    'search' => 'Cerca &raquo;',

    // Update
    'update_app' => 'Aggiorna il database per riflettere le modifiche ai model.',
    'updating_app' => 'Aggiornamenti del database di PHP Censor: ',
    'not_installed' => 'PHP Censor sembra non essere installato.',
    'install_instead' => 'Per favore installa PHP Censor tramite php-censor:install.',

    // Build Plugins:
    'passing_build' => 'Build passata',
    'failing_build' => 'Build fallita',
    'log_output' => 'Log: ',
];
