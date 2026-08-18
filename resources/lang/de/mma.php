<?php

$en = require dirname(__DIR__).'/en/mma.php';

$en['authorization']['protected'] = 'Geschützt';
$en['roles']['names'] = ['super_manager' => 'Root', 'admin' => 'Administrator', 'publisher' => 'Publisher', 'sales' => 'Vertrieb', 'checkin' => 'Einlasskontrolle', 'support' => 'Support', 'subscriber' => 'Abonnent'];
$en['menu'] = [
    'dashboard' => 'Start',
    'events' => ['group' => 'Events', 'events' => 'Events', 'fights' => 'Kämpfe', 'results' => 'Ergebnisse', 'media' => 'Event-Medien'],
    'fighters' => ['group' => 'Kämpfer', 'fighters' => 'Kämpfer', 'teams' => 'Teams und Gyms', 'weight_classes' => 'Gewichtsklassen', 'rankings' => 'Rankings'],
    'content' => ['group' => 'Inhalte', 'news' => 'Nachrichten', 'landing' => 'Landingpage', 'sponsors' => 'Sponsoren'],
    'commerce' => ['group' => 'Abos und Zahlungen', 'plans' => 'Pläne', 'subscribers' => 'Abonnenten', 'subscriptions' => 'Abos', 'payments' => 'Zahlungen', 'purchase_requests' => 'Kaufanfragen'],
    'tickets' => ['group' => 'Tickets', 'links' => 'Ticket-Links', 'orders' => 'Ticket-Bestellungen', 'checkins' => 'Einlassvalidierung'],
    'security' => ['group' => 'Benutzer und Sicherheit', 'users' => 'Benutzer', 'roles' => 'Rollen und Berechtigungen'],
    'settings' => ['group' => 'Einstellungen', 'system' => 'Systemeinstellungen', 'notifications' => 'Benachrichtigungen', 'logs' => 'Logs'],
];
$en['admin']['common'] = ['active' => 'Aktiv', 'inactive' => 'Inaktiv', 'empty' => 'Keine Einträge vorhanden.', 'not_available' => 'Nicht verfügbar', 'yes' => 'Ja', 'no' => 'Nein', 'developed_by' => 'Entwickelt von', 'filters' => ['search' => 'Suchen', 'status' => 'Status', 'all' => 'Alle', 'per_page' => 'Pro Seite', 'clear' => 'Zurücksetzen'], 'columns' => ['actions' => 'Aktionen']];
$en['admin']['events'] = [
    'page_title' => 'Events',
    'table_title' => 'Eventliste',
    'table_subtitle' => 'Filtern, erstellen, bearbeiten, veröffentlichen oder löschen Sie Events ohne aktive Abhängigkeiten.',
    'create' => 'Neues Event',
    'edit' => 'Event bearbeiten',
    'delete_title' => 'Event löschen',
    'delete_warning' => 'Diese Aktion löscht das Event:',
    'search_placeholder' => 'Name, Untertitel oder Slug...',
    'content_summary' => ':fights Kämpfe · :tickets Links',
    'image_help' => 'JPG-, PNG- oder WebP-Bilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'filters' => ['venue' => 'Ort', 'featured' => 'Hervorgehoben', 'from' => 'Von', 'to' => 'Bis'],
    'columns' => ['event' => 'Event', 'venue' => 'Ort', 'date' => 'Datum', 'content' => 'Inhalt', 'status' => 'Status', 'featured' => 'Hervorgehoben'],
    'status' => ['draft' => 'Entwurf', 'published' => 'Veröffentlicht', 'archived' => 'Archiviert', 'cancelled' => 'Abgesagt'],
    'actions' => ['publish' => 'Veröffentlichen'],
    'form' => ['name' => 'Name', 'slug' => 'Slug', 'subtitle' => 'Untertitel', 'description' => 'Beschreibung', 'venue_id' => 'Ort', 'starts_at' => 'Datum und Uhrzeit des Events', 'doors_open_at' => 'Einlasszeit', 'timezone' => 'Zeitzone', 'stream_url' => 'Stream-URL', 'ticket_url' => 'Ticket-URL', 'status' => 'Status', 'is_featured' => 'Als hervorgehoben markieren', 'poster_image' => 'Poster', 'banner_image' => 'Banner'],
    'messages' => ['created' => 'Event erfolgreich erstellt.', 'updated' => 'Event erfolgreich aktualisiert.', 'published' => 'Event erfolgreich veröffentlicht.', 'deleted' => 'Event erfolgreich gelöscht.', 'delete_blocked' => 'Ein Event mit zugehörigen Kämpfen, Ticket-Links oder Anfragen kann nicht gelöscht werden.'],
];
$en['admin']['event_media'] = [
    'page_title' => 'Event-Medien',
    'table_title' => 'Medienliste',
    'table_subtitle' => 'Öffentliche Event-Medien filtern, hochladen, bearbeiten oder löschen.',
    'create' => 'Neues Medium',
    'edit' => 'Medium bearbeiten',
    'delete_title' => 'Medium löschen',
    'delete_warning' => 'Diese Aktion löscht das Medium:',
    'search_placeholder' => 'Titel, Beschreibung oder Event...',
    'image_help' => 'JPG-, PNG- oder WebP-Bilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'featured' => 'Hervorgehoben',
    'untitled' => 'Ohne Titel',
    'filters' => ['event' => 'Event', 'file_type' => 'Typ', 'category' => 'Kategorie'],
    'columns' => ['media' => 'Medium', 'event' => 'Event', 'category' => 'Kategorie', 'order' => 'Reihenfolge', 'status' => 'Status'],
    'file_types' => ['image' => 'Bild', 'video' => 'Video'],
    'categories' => ['gallery' => 'Galerie', 'weigh_in' => 'Wiegen', 'faceoff' => 'Faceoff', 'backstage' => 'Backstage', 'highlight' => 'Highlight', 'sponsor' => 'Sponsor', 'other' => 'Sonstiges'],
    'form' => ['event_id' => 'Event', 'file_type' => 'Dateityp', 'file_path' => 'Video-URL', 'category' => 'Kategorie', 'title' => 'Titel', 'description' => 'Beschreibung', 'is_featured' => 'Als hervorgehoben markieren', 'display_order' => 'Reihenfolge', 'status' => 'Status', 'media_image' => 'Bild'],
    'messages' => ['created' => 'Medium erfolgreich erstellt.', 'updated' => 'Medium erfolgreich aktualisiert.', 'deleted' => 'Medium erfolgreich gelöscht.'],
    'validation' => ['image_required' => 'Sie müssen ein Bild auswählen, um dieses Medium zu speichern.'],
];

$en['admin']['news'] = [
    'page_title' => 'Nachrichten',
    'table_title' => 'Nachrichtenliste',
    'table_subtitle' => 'Redaktionelle Beiträge filtern, erstellen, bearbeiten, veröffentlichen oder löschen.',
    'create' => 'Neue Nachricht',
    'edit' => 'Nachricht bearbeiten',
    'delete_title' => 'Nachricht löschen',
    'delete_warning' => 'Diese Aktion löscht die Nachricht:',
    'search_placeholder' => 'Titel, Slug oder Auszug...',
    'image_help' => 'JPG-, PNG- oder WebP-Titelbilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'filters' => ['featured' => 'Hervorgehoben', 'from' => 'Von', 'to' => 'Bis'],
    'columns' => ['post' => 'Nachricht', 'author' => 'Autor', 'published_at' => 'Veröffentlichung', 'status' => 'Status', 'featured' => 'Hervorgehoben'],
    'status' => ['draft' => 'Entwurf', 'published' => 'Veröffentlicht', 'archived' => 'Archiviert'],
    'actions' => ['publish' => 'Veröffentlichen'],
    'form' => ['title' => 'Titel', 'slug' => 'Slug', 'excerpt' => 'Auszug', 'content' => 'Inhalt', 'status' => 'Status', 'is_featured' => 'Als hervorgehoben markieren', 'published_at' => 'Veröffentlichungsdatum', 'cover_image' => 'Titelbild'],
    'messages' => ['created' => 'Nachricht erfolgreich erstellt.', 'updated' => 'Nachricht erfolgreich aktualisiert.', 'published' => 'Nachricht erfolgreich veröffentlicht.', 'deleted' => 'Nachricht erfolgreich gelöscht.'],
];

$en['admin']['fights'] = [
    'page_title' => 'Kämpfe',
    'table_title' => 'Kampfliste',
    'table_subtitle' => 'Filtern, planen, bearbeiten oder löschen Sie Kämpfe ohne offizielles Ergebnis.',
    'create' => 'Neuer Kampf',
    'edit' => 'Kampf bearbeiten',
    'delete_title' => 'Kampf löschen',
    'delete_warning' => 'Diese Aktion löscht den Kampf:',
    'search_placeholder' => 'Titel, Kämpfer oder Kampfname...',
    'image_help' => 'JPG-, PNG- oder WebP-Promobilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'filters' => ['event' => 'Event', 'bout_type' => 'Typ', 'weight_class' => 'Klasse'],
    'columns' => ['fight' => 'Kampf', 'event' => 'Event', 'weight_class' => 'Klasse', 'rounds' => 'Runden', 'order' => 'Reihenfolge', 'status' => 'Status'],
    'status' => ['scheduled' => 'Geplant', 'live' => 'Live', 'finished' => 'Beendet', 'cancelled' => 'Abgesagt'],
    'bout_type' => ['regular' => 'Regulär', 'main_event' => 'Hauptkampf', 'co_main_event' => 'Co-Hauptkampf', 'title_fight' => 'Titelkampf', 'exhibition' => 'Ausstellung'],
    'flags' => ['main_event' => 'Hauptkampf', 'featured' => 'Hervorgehoben', 'has_result' => 'Mit Ergebnis'],
    'form' => ['event_id' => 'Event', 'weight_class_id' => 'Gewichtsklasse', 'corner_red_fighter_id' => 'Rote Ecke', 'corner_blue_fighter_id' => 'Blaue Ecke', 'title' => 'Titel', 'bout_type' => 'Kampftyp', 'rounds' => 'Runden', 'display_order' => 'Reihenfolge auf der Card', 'starts_at' => 'Datum und Uhrzeit des Kampfes', 'status' => 'Status', 'is_main_event' => 'Hauptkampf', 'is_featured' => 'Hervorgehoben', 'notes' => 'Interne Notizen', 'promo_image' => 'Promobild'],
    'messages' => ['created' => 'Kampf erfolgreich erstellt.', 'updated' => 'Kampf erfolgreich aktualisiert.', 'deleted' => 'Kampf erfolgreich gelöscht.', 'delete_blocked' => 'Ein Kampf mit offiziellem Ergebnis kann nicht gelöscht werden.'],
];
$en['admin']['fight_results'] = [
    'page_title' => 'Ergebnisse',
    'table_title' => 'Kampfergebnisse',
    'table_subtitle' => 'Kämpfe filtern, offizielle Ergebnisse prüfen und Sieger, Methode, Runde und Zeit erfassen.',
    'search_placeholder' => 'Event, Kampf, Kämpfer oder Methode...',
    'modal_title' => 'Offizielles Ergebnis verwalten',
    'pending' => 'Ausstehend',
    'no_custom_title' => 'Kein eigener Titel',
    'round_time_value' => 'R: :round · Z: :time',
    'filters' => ['event' => 'Event', 'result_type' => 'Ergebnis', 'result_state' => 'Eintrag'],
    'result_state' => ['with' => 'Mit Ergebnis', 'without' => 'Ohne Ergebnis'],
    'columns' => ['fight' => 'Kampf', 'event' => 'Event', 'result' => 'Ergebnis', 'winner' => 'Sieger', 'round_time' => 'Runde / Zeit'],
    'result_types' => ['ko_tko' => 'KO/TKO', 'submission' => 'Submission', 'decision' => 'Entscheidung', 'draw' => 'Unentschieden', 'no_contest' => 'No Contest', 'disqualification' => 'Disqualifikation'],
    'corners' => ['red' => 'Rot: :fighter', 'blue' => 'Blau: :fighter'],
    'actions' => ['manage' => 'Ergebnis verwalten'],
    'form' => ['result_type' => 'Ergebnistyp', 'winner_fighter_id' => 'Sieger', 'no_winner' => 'Kein Sieger', 'method' => 'Methode', 'round' => 'Runde', 'time' => 'Zeit', 'official_notes' => 'Offizielle Notizen'],
    'messages' => ['saved' => 'Offizielles Ergebnis erfolgreich gespeichert.'],
    'validation' => ['winner_required' => 'Für diesen Ergebnistyp muss ein Sieger ausgewählt werden.', 'winner_corner' => 'Der Sieger muss zur roten oder blauen Ecke des Kampfes gehören.', 'round_limit' => 'Die Runde darf nicht höher sein als die für den Kampf konfigurierten :rounds Runden.'],
];

$en['admin']['fighters'] = [
    'page_title' => 'Kämpfer',
    'table_title' => 'Kämpferliste',
    'table_subtitle' => 'Filtern, erstellen, bearbeiten oder löschen Sie Kämpfer ohne zugehörige Kämpfe oder Rankings.',
    'create' => 'Neuer Kämpfer',
    'edit' => 'Kämpfer bearbeiten',
    'delete_title' => 'Kämpfer löschen',
    'delete_warning' => 'Diese Aktion löscht den Kämpfer:',
    'search_placeholder' => 'Name, Kampfname oder Slug...',
    'record_summary' => ':wins-:losses-:draws · NC :nc',
    'image_help' => 'JPG-, PNG- oder WebP-Bilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'filters' => ['gender' => 'Geschlecht', 'weight_class' => 'Klasse', 'team' => 'Team'],
    'columns' => ['fighter' => 'Kämpfer', 'team' => 'Team', 'weight_class' => 'Klasse', 'record' => 'Bilanz', 'fights' => 'Kämpfe', 'status' => 'Status'],
    'gender' => ['male' => 'Männlich', 'female' => 'Weiblich'],
    'stance' => ['orthodox' => 'Orthodox', 'southpaw' => 'Linksauslage', 'switch' => 'Wechselnd'],
    'form' => ['first_name' => 'Vorname', 'last_name' => 'Nachname', 'nickname' => 'Kampfname', 'slug' => 'Slug', 'gender' => 'Geschlecht', 'country_id' => 'Land', 'city_id' => 'Stadt', 'fighter_team_id' => 'Team/Gym', 'weight_class_id' => 'Gewichtsklasse', 'birthdate' => 'Geburtsdatum', 'height_cm' => 'Größe (cm)', 'reach_cm' => 'Reichweite (cm)', 'stance' => 'Auslage', 'bio' => 'Biografie', 'wins' => 'Siege', 'losses' => 'Niederlagen', 'draws' => 'Unentschieden', 'no_contests' => 'No Contests', 'status' => 'Status', 'profile_image' => 'Profilbild', 'cover_image' => 'Titelbild'],
    'messages' => ['created' => 'Kämpfer erfolgreich erstellt.', 'updated' => 'Kämpfer erfolgreich aktualisiert.', 'deleted' => 'Kämpfer erfolgreich gelöscht.', 'delete_blocked' => 'Ein Kämpfer mit zugehörigen Kämpfen oder Rankings kann nicht gelöscht werden.'],
];
$en['admin']['sponsors'] = ['page_title' => 'Sponsoren', 'table_title' => 'Sponsorenliste', 'table_subtitle' => 'Filtere, erstelle, bearbeite oder lösche Sponsoren ohne verknüpfte Events.', 'create' => 'Neuer Sponsor', 'edit' => 'Sponsor bearbeiten', 'delete_title' => 'Sponsor löschen', 'delete_warning' => 'Diese Aktion löscht den Sponsor:', 'search_placeholder' => 'Name, Slug, Website oder E-Mail...', 'image_help' => 'JPG-, PNG- oder WebP-Logos werden beim Speichern optimiert. Maximale Größe: 5 MB.', 'events_summary' => ':count Events', 'events_help' => 'Halte Strg oder Cmd gedrückt, um mehrere Events auszuwählen.', 'filters' => ['event' => 'Event'], 'columns' => ['sponsor' => 'Sponsor', 'website' => 'Website', 'email' => 'E-Mail', 'events' => 'Events', 'order' => 'Reihenfolge', 'status' => 'Status'], 'form' => ['name' => 'Name', 'slug' => 'Slug', 'website_url' => 'Website', 'contact_email' => 'Kontakt-E-Mail', 'description' => 'Beschreibung', 'display_order' => 'Reihenfolge', 'status' => 'Status', 'logo_path' => 'Logo', 'events' => 'Verknüpfte Events'], 'messages' => ['created' => 'Sponsor erfolgreich erstellt.', 'updated' => 'Sponsor erfolgreich aktualisiert.', 'deleted' => 'Sponsor erfolgreich gelöscht.', 'delete_blocked' => 'Ein Sponsor mit verknüpften Events kann nicht gelöscht werden.']];
$en['admin']['subscription_plans'] = ['page_title' => 'Abonnementpläne', 'table_title' => 'Planliste', 'table_subtitle' => 'Filtere, erstelle, bearbeite oder lösche Pläne ohne aktive Nutzung.', 'create' => 'Neuer Plan', 'edit' => 'Plan bearbeiten', 'delete_title' => 'Plan löschen', 'delete_warning' => 'Diese Aktion löscht den Plan:', 'search_placeholder' => 'Name, Slug oder Beschreibung...', 'duration_summary' => ':days Tage', 'discount_summary' => ':discount% Rabatt', 'features_summary' => ':count Vorteile', 'usage_summary' => ':subscriptions Abos - :requests Anfragen', 'filters' => ['billing_period' => 'Zeitraum'], 'columns' => ['plan' => 'Plan', 'price' => 'Preis', 'period' => 'Zeitraum', 'usage' => 'Nutzung', 'order' => 'Reihenfolge', 'status' => 'Status'], 'billing_periods' => ['monthly' => 'Monatlich', 'quarterly' => 'Vierteljährlich', 'yearly' => 'Jährlich', 'one_time' => 'Einmalzahlung', 'lifetime' => 'Lebenslang'], 'form' => ['name' => 'Name', 'slug' => 'Slug', 'description' => 'Beschreibung', 'price' => 'Preis', 'currency' => 'Währung', 'billing_period' => 'Abrechnungszeitraum', 'duration_days' => 'Laufzeit in Tagen', 'discount_percentage' => 'Rabattprozentsatz', 'display_order' => 'Reihenfolge', 'status' => 'Status'], 'features' => ['title' => 'Vorteile', 'add' => 'Vorteil hinzufügen', 'name' => 'Vorteil', 'description' => 'Beschreibung', 'feature_key' => 'Technischer Schlüssel', 'value' => 'Wert', 'display_order' => 'Reihenfolge', 'status' => 'Status', 'help' => 'Nur Vorteile mit Namen werden gespeichert. Aktive Vorteile werden zusätzlich im JSON-Feld features des Plans synchronisiert.'], 'messages' => ['created' => 'Plan erfolgreich erstellt.', 'updated' => 'Plan erfolgreich aktualisiert.', 'deleted' => 'Plan erfolgreich gelöscht.', 'delete_blocked' => 'Ein Plan mit Abos oder Kaufanfragen kann nicht gelöscht werden.']];
$en['admin']['subscribers'] = ['page_title' => 'Abonnenten', 'table_title' => 'Abonnentenliste', 'table_subtitle' => 'Filtere, prüfe und aktualisiere Basisdaten, ohne Rollen zu ändern.', 'edit' => 'Abonnent bearbeiten', 'search_placeholder' => 'Name, E-Mail, Telefon oder Dokument...', 'identity_value' => 'Dokument: :value', 'last_login_value' => 'Letzte Anmeldung: :date', 'last_login_empty' => 'Keine Anmeldung erfasst', 'activity_summary' => ':subscriptions Abos - :payments Zahlungen - :requests Anfragen', 'filters' => ['subscription_status' => 'Abo'], 'columns' => ['subscriber' => 'Abonnent', 'contact' => 'Kontakt', 'subscription' => 'Abo', 'activity' => 'Aktivität', 'status' => 'Status'], 'subscription_status' => ['none' => 'Kein Abo', 'pending' => 'Ausstehend', 'active' => 'Aktiv', 'expired' => 'Abgelaufen', 'cancelled' => 'Storniert', 'suspended' => 'Gesperrt'], 'form' => ['name' => 'Vorname', 'lastname' => 'Nachname', 'email' => 'E-Mail', 'number_phone' => 'Telefon', 'identity_document' => 'Ausweisdokument', 'state' => 'Kontostatus'], 'messages' => ['updated' => 'Abonnent erfolgreich aktualisiert.']];

$en['admin']['rankings'] = [
    'page_title' => 'Rankings',
    'table_title' => 'Rankingliste',
    'table_subtitle' => 'Offizielle Positionen nach Klasse und Geschlecht filtern, erstellen und anpassen.',
    'create' => 'Neues Ranking',
    'edit' => 'Ranking bearbeiten',
    'search_placeholder' => 'Kämpfer, Kampfname oder Klasse...',
    'champion' => 'Champion',
    'filters' => ['weight_class' => 'Klasse', 'gender' => 'Geschlecht'],
    'columns' => ['position' => 'Position', 'fighter' => 'Kämpfer', 'weight_class' => 'Klasse', 'record' => 'Bilanz', 'movement' => 'Bewegung', 'status' => 'Status'],
    'movement' => ['same' => 'Keine Änderung', 'up' => 'Steigt :places', 'down' => 'Fällt :places'],
    'form' => ['weight_class_id' => 'Gewichtsklasse', 'gender' => 'Geschlecht', 'fighter_id' => 'Kämpfer', 'position' => 'Aktuelle Position', 'previous_position' => 'Vorherige Position', 'is_champion' => 'Als Champion markieren', 'ranked_at' => 'Rankingdatum', 'status' => 'Status'],
    'messages' => ['created' => 'Ranking erfolgreich erstellt.', 'updated' => 'Ranking erfolgreich aktualisiert.'],
    'validation' => ['gender_mismatch' => 'Der ausgewählte Kämpfer gehört nicht zum Geschlecht des Rankings.', 'weight_class_mismatch' => 'Der ausgewählte Kämpfer gehört nicht zur Gewichtsklasse des Rankings.', 'position_taken' => 'Für die ausgewählte Klasse und das Geschlecht existiert bereits ein aktives Ranking mit dieser Position.'],
];

$en['admin']['fighter_teams'] = [
    'page_title' => 'Teams und Gyms',
    'table_title' => 'Teamliste',
    'table_subtitle' => 'Filtern, erstellen, bearbeiten oder löschen Sie Teams ohne zugehörige Kämpfer.',
    'create' => 'Neues Team',
    'edit' => 'Team bearbeiten',
    'delete_title' => 'Team löschen',
    'delete_warning' => 'Diese Aktion löscht das Team:',
    'search_placeholder' => 'Name, Coach, Telefon oder Slug...',
    'image_help' => 'JPG-, PNG- oder WebP-Logos werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'filters' => ['city' => 'Stadt'],
    'columns' => ['team' => 'Team', 'city' => 'Stadt', 'coach' => 'Coach', 'contact' => 'Kontakt', 'fighters' => 'Kämpfer', 'status' => 'Status'],
    'form' => ['name' => 'Name', 'slug' => 'Slug', 'city_id' => 'Stadt', 'coach_name' => 'Coach', 'contact_phone' => 'Telefon', 'description' => 'Beschreibung', 'status' => 'Status', 'logo_path' => 'Logo'],
    'messages' => ['created' => 'Team erfolgreich erstellt.', 'updated' => 'Team erfolgreich aktualisiert.', 'deleted' => 'Team erfolgreich gelöscht.', 'delete_blocked' => 'Ein Team mit zugehörigen Kämpfern kann nicht gelöscht werden.'],
];
$en['admin']['purchase_requests'] = [
    'page_title' => 'Kaufanfragen',
    'table_title' => 'Anfragenliste',
    'table_subtitle' => 'Filtern, Verantwortliche zuweisen und den Status jeder Anfrage aktualisieren.',
    'search_placeholder' => 'Name, E-Mail, Telefon oder UUID...',
    'modal_title' => 'Anfrage verwalten',
    'delete_title' => 'Anfrage löschen',
    'delete_warning' => 'Diese Aktion löscht die Anfrage von:',
    'filters' => ['request_type' => 'Typ', 'channel' => 'Kanal', 'assigned_to' => 'Zugewiesen', 'from' => 'Von', 'to' => 'Bis'],
    'columns' => ['request' => 'Anfrage', 'contact' => 'Kontakt', 'related_to' => 'Bezogen auf', 'status' => 'Status', 'assigned_to' => 'Zugewiesen', 'created_at' => 'Datum'],
    'status' => ['pending' => 'Ausstehend', 'in_review' => 'In Prüfung', 'contacted' => 'Kontaktiert', 'converted' => 'Konvertiert', 'closed' => 'Geschlossen', 'rejected' => 'Abgelehnt'],
    'request_types' => ['general_contact' => 'Allgemeiner Kontakt', 'event_ticket' => 'Event-Tickets', 'subscription' => 'Abonnement', 'payment_proof' => 'Zahlungsnachweis'],
    'channels' => ['whatsapp' => 'WhatsApp', 'phone' => 'Telefon', 'email' => 'E-Mail'],
    'assignment' => ['unassigned' => 'Nicht zugewiesen', 'me' => 'Mir zugewiesen'],
    'actions' => ['manage' => 'Verwalten', 'assign_to_me' => 'Mir zuweisen', 'close' => 'Schließen'],
    'details' => ['title' => 'Anfragedetails', 'contact' => 'Kontakt', 'request_type' => 'Typ', 'email' => 'E-Mail', 'channel' => 'Kanal', 'phone' => 'Telefon/WhatsApp', 'related_to' => 'Bezogen auf', 'message' => 'Nachricht', 'proof' => 'Nachweis'],
    'proof' => ['available' => 'Nachweis', 'open' => 'Privaten Nachweis öffnen', 'none' => 'Kein Nachweis hochgeladen'],
    'form' => ['status' => 'Status', 'assigned_to' => 'Verantwortlicher', 'notes' => 'Interne Notizen'],
    'messages' => ['updated' => 'Anfrage erfolgreich aktualisiert.', 'assigned' => 'Anfrage erfolgreich zugewiesen.', 'closed' => 'Anfrage erfolgreich geschlossen.', 'deleted' => 'Anfrage erfolgreich gelöscht.'],
];
$en['admin']['dashboard']['page_title'] = 'Dashboard';
$en['admin']['weight_classes']['page_title'] = 'Gewichtsklassen';
$en['landing'] = ['login' => 'Anmelden', 'hero_text' => 'Events, Kämpfe, Ranglisten und Abos für einen Kampfsport-Veranstalter.', 'view_events' => 'Events ansehen', 'contact' => 'Kontakt', 'events_title' => 'Aktive Events', 'events_subtitle' => 'Veröffentlichte, bevorstehende und vergangene Events, die weiterhin aktiv sind.', 'featured' => 'Hervorgehoben', 'empty_events' => 'Es gibt noch keine veröffentlichten Events.', 'no_image' => 'Kein Bild verfügbar', 'back' => 'Zurück', 'fights_title' => 'Kampfkarte', 'empty_fights' => 'Für dieses Event wurden noch keine Kämpfe veröffentlicht.', 'vs' => 'vs', 'nav' => ['home' => 'Start', 'fighters' => 'Kämpfer', 'news' => 'News', 'subscription' => 'Abo', 'contact' => 'Kontakt'], 'footer' => ['quick_links' => 'Schnellzugriff', 'contact' => 'Kontakt', 'follow_us' => 'Folge uns', 'rights' => 'Alle Rechte vorbehalten.'], 'fighters' => ['featured_title' => 'Kämpfer im Fokus', 'featured_subtitle' => 'Lerne einige Athleten unseres Kaders kennen.', 'view_all' => 'Alle Kämpfer ansehen', 'title' => 'Kämpfer', 'subtitle' => 'Lerne den vollständigen Kader aktiver Kämpfer kennen.', 'empty' => 'Es gibt noch keine veröffentlichten Kämpfer.', 'wins' => 'Siege', 'losses' => 'Niederlagen', 'draws' => 'Unentschieden', 'bio_title' => 'Biografie', 'fight_history' => 'Kampfhistorie', 'round' => 'Runde', 'result_win' => 'Sieg', 'result_loss' => 'Niederlage', 'result_pending' => 'Ausstehend', 'no_fights' => 'Es sind noch keine Kämpfe erfasst.'], 'news' => ['section_title' => 'Neueste News', 'view_all' => 'Alle News ansehen', 'title' => 'News', 'subtitle' => 'Ankündigungen, Neuigkeiten und Interviews.', 'read_more' => 'Weiterlesen', 'empty' => 'Es gibt noch keine veröffentlichten News.'], 'event' => ['prev' => 'Vorheriges Event', 'next' => 'Nächstes Event', 'main_event' => 'Hauptkampf', 'tickets_title' => 'Tickets', 'price_from' => 'Ab', 'no_tickets' => 'Verkaufslinks werden in Kürze veröffentlicht.', 'contact_cta' => 'Kontaktiere uns'], 'subscription' => ['title' => 'Abo', 'subtitle' => 'Wähle den passenden Plan und genieße exklusive Vorteile.', 'empty' => 'Derzeit sind keine Pläne verfügbar.', 'cta' => 'Ich möchte abonnieren'], 'contact_page' => ['title' => 'Kontakt', 'subtitle' => 'Sende uns deine Anfrage, unser Team meldet sich in Kürze bei dir.', 'about_event' => 'Deine Anfrage betrifft: :event', 'about_plan' => 'Deine Anfrage betrifft: :plan', 'success' => 'Deine Anfrage wurde erfolgreich gesendet. Wir melden uns in Kürze bei dir.', 'direct_title' => 'Möchtest du uns lieber direkt kontaktieren?', 'direct_hint' => 'Du kannst unser Team auch über einen dieser Kanäle erreichen.', 'form' => ['name' => 'Vollständiger Name', 'email' => 'E-Mail', 'phone' => 'Telefon', 'whatsapp' => 'WhatsApp', 'channel' => 'Bevorzugter Kontaktkanal', 'type' => 'Grund der Kontaktaufnahme', 'message' => 'Nachricht', 'proof' => 'Zahlungsbeleg', 'proof_hint' => 'Optional. Erlaubte Formate: JPG, JPEG, PNG oder PDF, maximal 5 MB.', 'submit' => 'Anfrage senden'], 'channel_options' => ['whatsapp' => 'WhatsApp', 'phone' => 'Telefon', 'email' => 'E-Mail'], 'type_options' => ['general_contact' => 'Allgemeine Anfrage', 'event_ticket' => 'Event-Tickets', 'subscription' => 'Abo', 'payment_proof' => 'Zahlungsbeleg']]];
$en['uploads']['payment_proofs'] = ['invalid_type' => 'Nicht unterstütztes Format. Verwenden Sie JPG, JPEG, PNG oder PDF.', 'max_size' => 'Der Nachweis darf nicht größer als :max MB sein.'];
$en['uploads']['public_images'] = ['invalid_type' => 'Nicht unterstütztes Format. Verwenden Sie JPG, PNG, GIF oder WebP.', 'max_size' => 'Das Bild darf nicht größer als :max MB sein.', 'process_failed' => 'Das ausgewählte Bild konnte nicht verarbeitet werden.'];

$en['admin']['user_subscriptions'] = [
    'page_title' => 'Benutzerabonnements',
    'table_title' => 'Abonnementliste',
    'table_subtitle' => 'Benutzerabonnements filtern, erstellen, bearbeiten oder stornieren, ohne automatische Zahlungen zu verarbeiten.',
    'create' => 'Neues Abonnement',
    'edit' => 'Abonnement bearbeiten',
    'cancel_title' => 'Abonnement stornieren',
    'cancel_warning' => 'Diese Aktion markiert das Abonnement als storniert:',
    'search_placeholder' => 'Abonnent, E-Mail, Telefon, Plan oder Slug...',
    'period_value' => ':start - :end',
    'open_ended' => 'Kein Enddatum',
    'renewal_value' => 'Verlängerung: :date',
    'payments_summary' => ':count Zahlungen',
    'filters' => [
        'plan' => 'Plan',
        'from' => 'Von',
        'to' => 'Bis',
    ],
    'columns' => [
        'subscriber' => 'Abonnent',
        'plan' => 'Plan',
        'period' => 'Zeitraum',
        'status' => 'Status',
        'payments' => 'Zahlungen',
        'source' => 'Quelle',
    ],
    'status' => [
        'pending' => 'Ausstehend',
        'active' => 'Aktiv',
        'expired' => 'Abgelaufen',
        'cancelled' => 'Storniert',
        'suspended' => 'Gesperrt',
    ],
    'sources' => [
        'manual' => 'Manuell',
        'admin' => 'Administration',
        'purchase_request' => 'Kaufanfrage',
        'import' => 'Import',
        'other' => 'Sonstiges',
    ],
    'actions' => [
        'cancel' => 'Abonnement stornieren',
    ],
    'form' => [
        'user_id' => 'Abonnent',
        'subscription_plan_id' => 'Plan',
        'starts_at' => 'Startdatum und Uhrzeit',
        'ends_at' => 'Enddatum und Uhrzeit',
        'trial_ends_at' => 'Testzeitraum endet am',
        'renewal_at' => 'Verlängerungsdatum',
        'status' => 'Status',
        'source' => 'Quelle',
        'metadata_note' => 'Interne Notiz',
    ],
    'messages' => [
        'created' => 'Abonnement erfolgreich erstellt.',
        'updated' => 'Abonnement erfolgreich aktualisiert.',
        'cancelled' => 'Abonnement erfolgreich storniert.',
    ],
    'validation' => [
        'subscriber_required' => 'Der ausgewählte Benutzer muss die Abonnentenrolle haben.',
        'ends_at_after_start' => 'Das Enddatum muss nach oder gleich dem Startdatum sein.',
        'trial_ends_at_after_start' => 'Das Testdatum muss nach oder gleich dem Startdatum sein.',
        'renewal_at_after_start' => 'Das Verlängerungsdatum muss nach oder gleich dem Startdatum sein.',
    ],
];

$en['admin']['subscription_payments'] = [
    'page_title' => 'Abonnementzahlungen',
    'table_title' => 'Zahlungsliste',
    'table_subtitle' => 'Manuelle Zahlungen filtern, erfassen, aktualisieren, bestätigen oder stornieren.',
    'create' => 'Neue Zahlung',
    'edit' => 'Zahlung bearbeiten',
    'confirm_title' => 'Zahlung bestätigen',
    'confirm_warning' => 'Diese Aktion markiert die Zahlung als bezahlt:',
    'cancel_title' => 'Zahlung stornieren',
    'cancel_warning' => 'Diese Aktion markiert die Zahlung als fehlgeschlagen:',
    'search_placeholder' => 'Abonnent, E-Mail, Telefon, Plan, Anbieter oder Transaktion...',
    'paid_at_value' => 'Bezahlt: :date',
    'not_paid' => 'Kein Zahlungsdatum',
    'filters' => [
        'payment_method' => 'Methode',
        'from' => 'Von',
        'to' => 'Bis',
    ],
    'columns' => [
        'subscriber' => 'Abonnent',
        'subscription' => 'Abonnement',
        'amount' => 'Betrag',
        'method' => 'Methode',
        'proof' => 'Nachweis',
        'status' => 'Status',
    ],
    'status' => [
        'pending' => 'Ausstehend',
        'paid' => 'Bezahlt',
        'failed' => 'Fehlgeschlagen',
        'refunded' => 'Erstattet',
        'expired' => 'Abgelaufen',
    ],
    'payment_methods' => [
        'manual_transfer' => 'Manuelle Überweisung',
        'cash' => 'Barzahlung',
        'qr' => 'QR-Zahlung',
        'whatsapp' => 'WhatsApp',
        'gateway' => 'Gateway',
        'other' => 'Sonstiges',
    ],
    'actions' => [
        'confirm' => 'Zahlung bestätigen',
        'cancel' => 'Zahlung stornieren',
    ],
    'proof' => [
        'open' => 'Nachweis öffnen',
        'none' => 'Kein Nachweis',
        'help' => 'JPG, JPEG, PNG oder PDF. Bilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    ],
    'form' => [
        'user_id' => 'Abonnent',
        'user_subscription_id' => 'Abonnement',
        'no_subscription' => 'Kein verknüpftes Abonnement',
        'amount' => 'Betrag',
        'currency' => 'Währung',
        'payment_method' => 'Zahlungsmethode',
        'provider' => 'Anbieter',
        'provider_transaction_id' => 'Transaktions-ID',
        'payment_url' => 'Zahlungs-URL',
        'paid_at' => 'Zahlungsdatum',
        'expires_at' => 'Ablaufdatum',
        'status' => 'Status',
        'notes' => 'Interne Notizen',
        'payment_proof' => 'Zahlungsnachweis',
    ],
    'messages' => [
        'created' => 'Zahlung erfolgreich erfasst.',
        'updated' => 'Zahlung erfolgreich aktualisiert.',
        'confirmed' => 'Zahlung erfolgreich bestätigt.',
        'cancelled' => 'Zahlung erfolgreich storniert.',
    ],
    'validation' => [
        'subscriber_required' => 'Der ausgewählte Benutzer muss die Abonnentenrolle haben.',
        'subscription_user_mismatch' => 'Das ausgewählte Abonnement gehört nicht zum ausgewählten Abonnenten.',
    ],
];

$en['admin']['ticket_links'] = [
    'page_title' => 'Ticket-Links',
    'table_title' => 'Ticket-Link-Liste',
    'table_subtitle' => 'Öffentliche Verkaufslinks zu Events filtern, erstellen, bearbeiten oder löschen.',
    'create' => 'Neuer Link',
    'edit' => 'Link bearbeiten',
    'delete_title' => 'Ticket-Link löschen',
    'delete_warning' => 'Diese Aktion löscht den Ticket-Link:',
    'search_placeholder' => 'Anbieter, Label, URL oder Event...',
    'open_start' => 'Kein Startlimit',
    'open_end' => 'Kein Endlimit',
    'filters' => [
        'event' => 'Event',
        'sale_channel' => 'Kanal',
    ],
    'columns' => [
        'link' => 'Link',
        'event' => 'Event',
        'channel' => 'Kanal',
        'price' => 'Preis ab',
        'window' => 'Verkaufsfenster',
        'status' => 'Status',
    ],
    'sale_channels' => [
        'external' => 'Externe Plattform',
        'whatsapp' => 'WhatsApp',
        'phone' => 'Telefon',
        'streaming' => 'Streaming',
        'vip' => 'VIP',
        'other' => 'Sonstiges',
    ],
    'form' => [
        'event_id' => 'Event',
        'provider_name' => 'Anbieter',
        'label' => 'Sichtbares Label',
        'sale_channel' => 'Verkaufskanal',
        'url' => 'URL',
        'price_from' => 'Preis ab',
        'currency' => 'Währung',
        'starts_at' => 'Verkaufsstart',
        'ends_at' => 'Verkaufsende',
        'display_order' => 'Reihenfolge',
        'status' => 'Status',
    ],
    'messages' => [
        'created' => 'Ticket-Link erfolgreich erstellt.',
        'updated' => 'Ticket-Link erfolgreich aktualisiert.',
        'deleted' => 'Ticket-Link erfolgreich gelöscht.',
    ],
    'validation' => [
        'ends_after_start' => 'Das Verkaufsende muss nach oder gleich dem Verkaufsstart sein.',
    ],
];

$en['admin']['system_settings'] = [
    'page_title' => 'Systemeinstellungen',
    'form_title' => 'Allgemeine Konfiguration',
    'form_subtitle' => 'Diese Werte werden im Adminbereich, im Login und auf der öffentlichen Landingpage angezeigt.',
    'image_help' => 'JPG-, PNG- oder WebP-Bilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'sections' => [
        'identity' => 'Identität',
        'contact' => 'Kontakt',
        'social' => 'Soziale Netzwerke',
        'seo' => 'Öffentliche Landingpage und SEO',
    ],
    'form' => [
        'product_name' => 'Produktname',
        'public_title' => 'Öffentlicher Titel',
        'contact_email' => 'Kontakt-E-Mail',
        'contact_phone' => 'Kontakttelefon',
        'whatsapp_phone' => 'WhatsApp-Telefon',
        'short_description' => 'Kurzbeschreibung',
        'seo_title' => 'SEO-Titel',
        'seo_description' => 'SEO-Beschreibung',
        'landing_show_rankings' => 'Rankings auf der öffentlichen Landingpage anzeigen',
        'logo_path' => 'Logo',
        'favicon_path' => 'Favicon',
    ],
    'social' => [
        'facebook' => 'Facebook-URL',
        'instagram' => 'Instagram-URL',
        'youtube' => 'YouTube-URL',
        'tiktok' => 'TikTok-URL',
    ],
    'actions' => [
        'save' => 'Einstellungen speichern',
    ],
    'messages' => [
        'updated' => 'Systemeinstellungen erfolgreich aktualisiert.', 'updated_with_images' => 'Einstellungen und Bilder erfolgreich aktualisiert.', 'uploading' => 'Bild wird hochgeladen...',
    ],
];

$en['menu']['events']['venues'] = 'Veranstaltungsorte';
$en['admin']['venues'] = [
    'page_title' => 'Veranstaltungsorte',
    'table_title' => 'Liste der Veranstaltungsorte',
    'table_subtitle' => 'Filtere, erstelle, bearbeite oder lösche Veranstaltungsorte ohne verknüpfte Events.',
    'create' => 'Neuer Veranstaltungsort',
    'edit' => 'Veranstaltungsort bearbeiten',
    'delete_title' => 'Veranstaltungsort löschen',
    'delete_warning' => 'Diese Aktion löscht den Veranstaltungsort:',
    'search_placeholder' => 'Name, Adresse, Kontakt, Telefon oder Slug...',
    'image_help' => 'JPG-, PNG- oder WebP-Bilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'filters' => ['city' => 'Stadt'],
    'columns' => ['venue' => 'Ort', 'location' => 'Standort', 'capacity' => 'Kapazität', 'contact' => 'Kontakt', 'events' => 'Events', 'status' => 'Status'],
    'form' => ['name' => 'Name', 'slug' => 'Slug', 'city_id' => 'Stadt', 'address' => 'Adresse', 'latitude' => 'Breitengrad', 'longitude' => 'Längengrad', 'capacity' => 'Kapazität', 'contact_name' => 'Kontaktname', 'contact_phone' => 'Kontakttelefon', 'status' => 'Status', 'image' => 'Bild des Veranstaltungsorts'],
    'messages' => ['created' => 'Veranstaltungsort erfolgreich erstellt.', 'updated' => 'Veranstaltungsort erfolgreich aktualisiert.', 'deleted' => 'Veranstaltungsort erfolgreich gelöscht.', 'delete_blocked' => 'Ein Veranstaltungsort mit verknüpften Events kann nicht gelöscht werden.'],
];

$en['admin']['users'] = [
    'page_title' => 'Benutzer',
    'table_title' => 'Benutzerliste',
    'table_subtitle' => 'Filtere, erstelle, bearbeite oder lösche Benutzer, die durch die Rollenhierarchie erlaubt sind.',
    'create' => 'Neuer Benutzer',
    'edit' => 'Benutzer bearbeiten',
    'delete_title' => 'Benutzer löschen',
    'delete_warning' => 'Diese Aktion löscht den Benutzer:',
    'search_placeholder' => 'Name, E-Mail, Telefon oder Dokument...',
    'password_help' => 'Beim Bearbeiten leer lassen, um das aktuelle Passwort beizubehalten.',
    'selected_roles' => 'ausgewählte Rollen',
    'hierarchy_readonly' => 'Schreibgeschützt durch Hierarchie',
    'filters' => ['role' => 'Rolle'],
    'columns' => ['user' => 'Benutzer', 'contact' => 'Kontakt', 'roles' => 'Rollen', 'last_login' => 'Letzte Anmeldung', 'status' => 'Status'],
    'form' => ['name' => 'Vorname', 'lastname' => 'Nachname', 'email' => 'E-Mail', 'number_phone' => 'Telefon', 'identity_document' => 'Identitätsdokument', 'state' => 'Status', 'password' => 'Passwort', 'roles' => 'Rollen'],
    'messages' => ['created' => 'Benutzer erfolgreich erstellt.', 'updated' => 'Benutzer erfolgreich aktualisiert.', 'deleted' => 'Benutzer erfolgreich gelöscht.', 'self_delete_blocked' => 'Du kannst dein eigenes Benutzerkonto nicht löschen.'],
    'validation' => ['roles_allowed' => 'Wähle mindestens eine Rolle aus, die du zuweisen darfst.'],
];

$en['menu']['fighters']['media'] = 'Kämpfer-Medien';
$en['admin']['fighter_media'] = [
    'page_title' => 'Kämpfer-Medien',
    'table_title' => 'Liste der Kämpfer-Medien',
    'table_subtitle' => 'Filtere, lade hoch, bearbeite oder lösche öffentliche Kämpfer-Medien.',
    'create' => 'Neues Medium',
    'edit' => 'Medium bearbeiten',
    'delete_title' => 'Medium löschen',
    'delete_warning' => 'Diese Aktion löscht das Medium:',
    'search_placeholder' => 'Titel, Beschreibung, Kämpfer oder Spitzname...',
    'image_help' => 'JPG-, PNG- oder WebP-Bilder werden beim Speichern optimiert. Maximale Größe: 5 MB.',
    'featured' => 'Hervorgehoben',
    'untitled' => 'Ohne Titel',
    'filters' => ['fighter' => 'Kämpfer', 'file_type' => 'Typ'],
    'columns' => ['media' => 'Medien', 'fighter' => 'Kämpfer', 'order' => 'Reihenfolge', 'status' => 'Status'],
    'file_types' => ['image' => 'Bild', 'video' => 'Video'],
    'form' => ['fighter_id' => 'Kämpfer', 'file_type' => 'Dateityp', 'file_path' => 'Video-URL', 'title' => 'Titel', 'description' => 'Beschreibung', 'is_featured' => 'Als hervorgehoben markieren', 'display_order' => 'Reihenfolge', 'status' => 'Status', 'media_image' => 'Bild'],
    'messages' => ['created' => 'Kämpfer-Medium erfolgreich erstellt.', 'updated' => 'Kämpfer-Medium erfolgreich aktualisiert.', 'deleted' => 'Kämpfer-Medium erfolgreich gelöscht.'],
    'validation' => ['image_required' => 'Du musst ein Bild auswählen, um dieses Kämpfer-Medium zu speichern.'],
];

$en['admin']['landing'] = [
    'page_title' => 'Öffentliche Landingpage',
    'form_title' => 'Landingpage-Konfiguration',
    'form_subtitle' => 'Diese Werte erscheinen auf der öffentlichen Seite und müssen zu den aktiven veröffentlichten Events passen.',
    'latest_events' => 'Vorschau veröffentlichter Events',
    'empty_events' => 'Es gibt keine veröffentlichten Events für die Vorschau.',
    'stats' => ['published' => 'Veröffentlicht', 'featured' => 'Hervorgehoben', 'drafts' => 'Entwürfe'],
    'actions' => ['save' => 'Landingpage speichern', 'open_public' => 'Öffentliche Seite öffnen', 'manage_events' => 'Events verwalten'],
    'form' => ['public_title' => 'Öffentlicher Titel', 'short_description' => 'Kurzbeschreibung', 'seo_title' => 'SEO-Titel', 'seo_description' => 'SEO-Beschreibung', 'landing_show_rankings' => 'Rankings auf der öffentlichen Landingpage anzeigen'],
    'messages' => ['updated' => 'Öffentliche Landingpage erfolgreich aktualisiert.'],
];

$en['subscriber_portal'] = [
    'menu' => ['dashboard' => 'Start', 'purchases' => 'Meine Käufe', 'events' => 'Meine Events', 'subscription' => 'Mein Abo', 'profile' => 'Profil'],
    'dashboard' => ['title' => 'Abonnenten-Start', 'subtitle' => 'Prüfe dein Konto, Abo, Käufe und verfügbare Events.', 'cards' => ['account' => 'Kontostatus', 'subscription' => 'Abo-Status', 'events' => 'Verfügbare Events'], 'latest_purchases' => 'Letzte Zahlungen', 'latest_requests' => 'Letzte Anfragen', 'next_events' => 'Verfügbare Events'],
    'actions' => ['view_all' => 'Alle anzeigen', 'save' => 'Änderungen speichern', 'back_to_purchases' => 'Zurück zu meinen Käufen'],
    'empty' => ['no_subscription' => 'Kein aktives Abo', 'no_purchases' => 'Keine Käufe registriert.', 'no_requests' => 'Keine Anfragen registriert.', 'no_events' => 'Du hast keine verfügbaren Events.'],
    'events' => ['title' => 'Meine Events', 'subtitle' => 'Events, die mit deinen Anfragen, Käufen oder Zugangsleistungen verbunden sind.'],
    'purchases' => ['title' => 'Meine Käufe', 'subtitle' => 'Prüfe Zahlungen und Anfragen, die für dein Konto registriert sind.', 'payments' => 'Zahlungen', 'requests' => 'Anfragen', 'detail_title' => 'Kaufdetail', 'detail_subtitle' => 'Vollständige Informationen zu dieser Zahlung oder Anfrage.', 'proof_status' => 'Belegstatus', 'proof_uploaded_label' => 'Beleg hochgeladen', 'proof_missing_label' => 'Kein Beleg hochgeladen', 'notes' => 'Notizen', 'message' => 'Nachricht', 'related_event' => 'Zugehöriges Event', 'upload_proof_title' => 'Beleg hochladen oder erneut senden', 'upload_proof_hint' => 'Erlaubte Formate: JPG, JPEG, PNG oder PDF, maximal 5 MB.', 'upload_proof_submit' => 'Beleg hochladen', 'proof_field' => 'Zahlungsbeleg', 'proof_uploaded' => 'Beleg erfolgreich hochgeladen. Unser Team wird ihn in Kürze prüfen.', 'proof_not_allowed' => 'Dieser Eintrag akzeptiert keinen neuen Beleg mehr. Bitte wende dich an den Support.', 'contact_title' => 'Brauchst du Hilfe bei diesem Kauf?', 'contact_hint' => 'Kontaktiere unser Vertriebs- oder Support-Team über einen dieser Kanäle.'],
    'subscription' => ['title' => 'Mein Abo', 'subtitle' => 'Prüfe deinen aktuellen Plan und den Abo-Verlauf.', 'current' => 'Aktuelles Abo', 'history' => 'Abo-Verlauf', 'benefits' => 'Vorteile des Plans', 'contact_title' => 'Möchtest du verlängern oder upgraden?', 'contact_hint' => 'Kontaktiere unser Vertriebsteam, um zu verlängern, upzugraden oder Fragen zu deinem Abo zu klären.'],
    'profile' => ['title' => 'Profil', 'subtitle' => 'Aktualisiere deine eigenen Kontaktdaten.', 'updated' => 'Profil erfolgreich aktualisiert.', 'form' => ['name' => 'Vorname', 'lastname' => 'Nachname', 'email' => 'E-Mail', 'number_phone' => 'Telefon', 'identity_document' => 'Ausweisdokument', 'current_password' => 'Aktuelles Passwort', 'new_password' => 'Neues Passwort', 'confirm_password' => 'Passwort bestätigen'], 'password_title' => 'Passwort ändern', 'password_hint' => 'Verwende mindestens 8 Zeichen und vermeide die Wiederverwendung alter Passwörter.', 'password_updated' => 'Passwort erfolgreich aktualisiert.', 'password_submit' => 'Passwort aktualisieren'],
    'columns' => ['concept' => 'Konzept', 'amount' => 'Betrag', 'method' => 'Methode', 'status' => 'Status', 'date' => 'Datum', 'channel' => 'Kanal', 'plan' => 'Plan', 'start' => 'Start', 'end' => 'Ende', 'period' => 'Zeitraum'],
];

$en['menu']['reports'] = ['group' => 'Berichte', 'events' => 'Eventberichte', 'subscriptions' => 'Abo-Berichte', 'sales' => 'Verkaufsberichte'];
$en['admin']['reports'] = [
    'page_title' => 'Berichte',
    'table_title' => 'Operative Berichte',
    'table_subtitle' => 'Filtere nach Berichtstyp, Zeitraum und Status, ohne Datensätze zu ändern.',
    'types' => ['events' => 'Events', 'subscriptions' => 'Abos', 'sales' => 'Verkäufe'],
    'filters' => ['type' => 'Berichtstyp', 'from' => 'Von', 'to' => 'Bis'],
    'stats' => ['total' => 'Gesamt', 'published' => 'Veröffentlicht', 'featured' => 'Hervorgehoben', 'requests' => 'Anfragen', 'active' => 'Aktiv', 'pending' => 'Ausstehend', 'expired' => 'Abgelaufen', 'paid' => 'Bezahlt', 'amount' => 'Bezahlter Betrag'],
    'columns' => ['event' => 'Event', 'venue' => 'Ort', 'date' => 'Datum', 'fights' => 'Kämpfe', 'requests' => 'Anfragen', 'subscriber' => 'Abonnent', 'plan' => 'Plan', 'period' => 'Zeitraum', 'payments' => 'Zahlungen', 'concept' => 'Konzept', 'amount' => 'Betrag', 'method' => 'Methode'],
];

$en['admin']['logs'] = [
    'page_title' => 'Logs',
    'table_title' => 'Anwendungslog',
    'file_size' => 'Dateigröße',
    'last_modified' => 'Letzte Änderung',
    'total_entries' => 'Einträge gesamt',
    'filtered_entries' => 'Gefilterte Einträge',
    'detail_title' => 'Logeintrag-Detail',
    'context' => 'Kontext',
    'trace' => 'Trace',
    'raw' => 'Originaleintrag',
    'has_context' => 'Enthält Kontext',
    'has_trace' => 'Enthält Trace',
    'filters' => ['level' => 'Level', 'all_levels' => 'Alle Level', 'from_date' => 'Von', 'to_date' => 'Bis', 'search_placeholder' => 'Nach Nachricht, Kontext oder Trace suchen...'],
    'columns' => ['datetime' => 'Datum und Uhrzeit', 'level' => 'Level', 'env' => 'Umgebung', 'message' => 'Nachricht'],
    'actions' => ['download' => 'Log herunterladen', 'view_detail' => 'Detail anzeigen'],
];

$en['landing']['fighters']['result_draw'] = 'Unentschieden / No Contest';

return $en;
