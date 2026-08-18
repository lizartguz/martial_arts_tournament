<?php

$en = require dirname(__DIR__).'/en/mma.php';

$en['authorization']['protected'] = 'Protégé';
$en['roles']['names'] = ['super_manager' => 'Root', 'admin' => 'Administrateur', 'publisher' => 'Éditeur', 'sales' => 'Ventes', 'checkin' => 'Contrôle d’entrée', 'support' => 'Support', 'subscriber' => 'Abonné'];
$en['menu'] = [
    'dashboard' => 'Accueil',
    'events' => ['group' => 'Événements', 'events' => 'Événements', 'fights' => 'Combats', 'results' => 'Résultats', 'media' => 'Médias des événements'],
    'fighters' => ['group' => 'Combattants', 'fighters' => 'Combattants', 'teams' => 'Équipes et salles', 'weight_classes' => 'Catégories de poids', 'rankings' => 'Classements'],
    'content' => ['group' => 'Contenu', 'news' => 'Actualités', 'landing' => 'Landing page', 'sponsors' => 'Sponsors'],
    'commerce' => ['group' => 'Abonnements et paiements', 'plans' => 'Plans', 'subscribers' => 'Abonnés', 'subscriptions' => 'Abonnements', 'payments' => 'Paiements', 'purchase_requests' => 'Demandes d’achat'],
    'tickets' => ['group' => 'Billets', 'links' => 'Liens de billets', 'orders' => 'Commandes de billets', 'checkins' => 'Validation d’entrée'],
    'security' => ['group' => 'Utilisateurs et sécurité', 'users' => 'Utilisateurs', 'roles' => 'Rôles et permissions'],
    'settings' => ['group' => 'Configuration', 'system' => 'Paramètres du système', 'notifications' => 'Notifications', 'logs' => 'Logs'],
];
$en['admin']['common'] = ['active' => 'Actif', 'inactive' => 'Inactif', 'empty' => 'Aucun enregistrement à afficher.', 'not_available' => 'Non disponible', 'yes' => 'Oui', 'no' => 'Non', 'developed_by' => 'Développé par', 'filters' => ['search' => 'Rechercher', 'status' => 'Statut', 'all' => 'Tous', 'per_page' => 'Par page', 'clear' => 'Effacer'], 'columns' => ['actions' => 'Actions']];
$en['admin']['events'] = [
    'page_title' => 'Événements',
    'table_title' => 'Liste des événements',
    'table_subtitle' => 'Filtrez, créez, modifiez, publiez ou supprimez les événements sans dépendances actives.',
    'create' => 'Nouvel événement',
    'edit' => 'Modifier l’événement',
    'delete_title' => 'Supprimer l’événement',
    'delete_warning' => 'Cette action supprimera l’événement :',
    'search_placeholder' => 'Nom, sous-titre ou slug...',
    'content_summary' => ':fights combats · :tickets liens',
    'image_help' => 'Les images JPG, PNG ou WebP sont optimisées à l’enregistrement. Taille maximale : 5 MB.',
    'filters' => ['venue' => 'Lieu', 'featured' => 'Mis en avant', 'from' => 'Du', 'to' => 'Au'],
    'columns' => ['event' => 'Événement', 'venue' => 'Lieu', 'date' => 'Date', 'content' => 'Contenu', 'status' => 'Statut', 'featured' => 'Mis en avant'],
    'status' => ['draft' => 'Brouillon', 'published' => 'Publié', 'archived' => 'Archivé', 'cancelled' => 'Annulé'],
    'actions' => ['publish' => 'Publier'],
    'form' => ['name' => 'Nom', 'slug' => 'Slug', 'subtitle' => 'Sous-titre', 'description' => 'Description', 'venue_id' => 'Lieu', 'starts_at' => 'Date et heure de l’événement', 'doors_open_at' => 'Ouverture des portes', 'timezone' => 'Fuseau horaire', 'stream_url' => 'URL de diffusion', 'ticket_url' => 'URL de billets', 'status' => 'Statut', 'is_featured' => 'Marquer comme mis en avant', 'poster_image' => 'Affiche', 'banner_image' => 'Bannière'],
    'messages' => ['created' => 'Événement enregistré avec succès.', 'updated' => 'Événement mis à jour avec succès.', 'published' => 'Événement publié avec succès.', 'deleted' => 'Événement supprimé avec succès.', 'delete_blocked' => 'Impossible de supprimer un événement avec des combats, liens de billets ou demandes associés.'],
];
$en['admin']['event_media'] = [
    'page_title' => 'Médias des événements',
    'table_title' => 'Liste des médias',
    'table_subtitle' => 'Filtrez, importez, modifiez ou supprimez les médias publics des événements.',
    'create' => 'Nouveau média',
    'edit' => 'Modifier le média',
    'delete_title' => 'Supprimer le média',
    'delete_warning' => 'Cette action supprimera le média :',
    'search_placeholder' => 'Titre, description ou événement...',
    'image_help' => 'Les images JPG, PNG ou WebP sont optimisées à l’enregistrement. Taille maximale : 5 MB.',
    'featured' => 'Mis en avant',
    'untitled' => 'Sans titre',
    'filters' => ['event' => 'Événement', 'file_type' => 'Type', 'category' => 'Catégorie'],
    'columns' => ['media' => 'Média', 'event' => 'Événement', 'category' => 'Catégorie', 'order' => 'Ordre', 'status' => 'Statut'],
    'file_types' => ['image' => 'Image', 'video' => 'Vidéo'],
    'categories' => ['gallery' => 'Galerie', 'weigh_in' => 'Pesée', 'faceoff' => 'Face-à-face', 'backstage' => 'Backstage', 'highlight' => 'Highlight', 'sponsor' => 'Sponsor', 'other' => 'Autre'],
    'form' => ['event_id' => 'Événement', 'file_type' => 'Type de fichier', 'file_path' => 'URL de la vidéo', 'category' => 'Catégorie', 'title' => 'Titre', 'description' => 'Description', 'is_featured' => 'Marquer comme mis en avant', 'display_order' => 'Ordre', 'status' => 'Statut', 'media_image' => 'Image'],
    'messages' => ['created' => 'Média enregistré avec succès.', 'updated' => 'Média mis à jour avec succès.', 'deleted' => 'Média supprimé avec succès.'],
    'validation' => ['image_required' => 'Vous devez sélectionner une image pour enregistrer ce média.'],
];

$en['admin']['news'] = [
    'page_title' => 'Actualités',
    'table_title' => 'Liste des actualités',
    'table_subtitle' => 'Filtrez, créez, modifiez, publiez ou supprimez les publications éditoriales.',
    'create' => 'Nouvelle actualité',
    'edit' => 'Modifier l’actualité',
    'delete_title' => 'Supprimer l’actualité',
    'delete_warning' => 'Cette action supprimera l’actualité :',
    'search_placeholder' => 'Titre, slug ou résumé...',
    'image_help' => 'L’image de couverture JPG, PNG ou WebP est optimisée à l’enregistrement. Taille maximale : 5 MB.',
    'filters' => ['featured' => 'Mis en avant', 'from' => 'Depuis', 'to' => 'Jusqu’à'],
    'columns' => ['post' => 'Actualité', 'author' => 'Auteur', 'published_at' => 'Publication', 'status' => 'Statut', 'featured' => 'Mis en avant'],
    'status' => ['draft' => 'Brouillon', 'published' => 'Publié', 'archived' => 'Archivé'],
    'actions' => ['publish' => 'Publier'],
    'form' => ['title' => 'Titre', 'slug' => 'Slug', 'excerpt' => 'Résumé', 'content' => 'Contenu', 'status' => 'Statut', 'is_featured' => 'Marquer comme mis en avant', 'published_at' => 'Date de publication', 'cover_image' => 'Image de couverture'],
    'messages' => ['created' => 'Actualité enregistrée avec succès.', 'updated' => 'Actualité mise à jour avec succès.', 'published' => 'Actualité publiée avec succès.', 'deleted' => 'Actualité supprimée avec succès.'],
];

$en['admin']['fights'] = [
    'page_title' => 'Combats',
    'table_title' => 'Liste des combats',
    'table_subtitle' => 'Filtrez, programmez, modifiez ou supprimez les combats sans résultat officiel.',
    'create' => 'Nouveau combat',
    'edit' => 'Modifier le combat',
    'delete_title' => 'Supprimer le combat',
    'delete_warning' => 'Cette action supprimera le combat :',
    'search_placeholder' => 'Titre, combattant ou surnom...',
    'image_help' => 'Les images promotionnelles JPG, PNG ou WebP sont optimisées à l’enregistrement. Taille maximale : 5 MB.',
    'filters' => ['event' => 'Événement', 'bout_type' => 'Type', 'weight_class' => 'Catégorie'],
    'columns' => ['fight' => 'Combat', 'event' => 'Événement', 'weight_class' => 'Catégorie', 'rounds' => 'Rounds', 'order' => 'Ordre', 'status' => 'Statut'],
    'status' => ['scheduled' => 'Programmé', 'live' => 'En direct', 'finished' => 'Terminé', 'cancelled' => 'Annulé'],
    'bout_type' => ['regular' => 'Régulier', 'main_event' => 'Combat principal', 'co_main_event' => 'Co-principal', 'title_fight' => 'Combat pour le titre', 'exhibition' => 'Exhibition'],
    'flags' => ['main_event' => 'Principal', 'featured' => 'Mis en avant', 'has_result' => 'Avec résultat'],
    'form' => ['event_id' => 'Événement', 'weight_class_id' => 'Catégorie de poids', 'corner_red_fighter_id' => 'Coin rouge', 'corner_blue_fighter_id' => 'Coin bleu', 'title' => 'Titre', 'bout_type' => 'Type de combat', 'rounds' => 'Rounds', 'display_order' => 'Ordre sur la carte', 'starts_at' => 'Date et heure du combat', 'status' => 'Statut', 'is_main_event' => 'Combat principal', 'is_featured' => 'Mis en avant', 'notes' => 'Notes internes', 'promo_image' => 'Image promotionnelle'],
    'messages' => ['created' => 'Combat enregistré avec succès.', 'updated' => 'Combat mis à jour avec succès.', 'deleted' => 'Combat supprimé avec succès.', 'delete_blocked' => 'Impossible de supprimer un combat avec un résultat officiel.'],
];
$en['admin']['fight_results'] = [
    'page_title' => 'Résultats',
    'table_title' => 'Résultats des combats',
    'table_subtitle' => 'Filtrez les combats, consultez les résultats officiels et saisissez le vainqueur, la méthode, le round et le temps.',
    'search_placeholder' => 'Événement, combat, combattant ou méthode...',
    'modal_title' => 'Gérer le résultat officiel',
    'pending' => 'En attente',
    'no_custom_title' => 'Sans titre personnalisé',
    'round_time_value' => 'R : :round · T : :time',
    'filters' => ['event' => 'Événement', 'result_type' => 'Résultat', 'result_state' => 'Enregistrement'],
    'result_state' => ['with' => 'Avec résultat', 'without' => 'Sans résultat'],
    'columns' => ['fight' => 'Combat', 'event' => 'Événement', 'result' => 'Résultat', 'winner' => 'Vainqueur', 'round_time' => 'Round / temps'],
    'result_types' => ['ko_tko' => 'KO/TKO', 'submission' => 'Soumission', 'decision' => 'Décision', 'draw' => 'Match nul', 'no_contest' => 'No contest', 'disqualification' => 'Disqualification'],
    'corners' => ['red' => 'Rouge : :fighter', 'blue' => 'Bleu : :fighter'],
    'actions' => ['manage' => 'Gérer le résultat'],
    'form' => ['result_type' => 'Type de résultat', 'winner_fighter_id' => 'Vainqueur', 'no_winner' => 'Sans vainqueur', 'method' => 'Méthode', 'round' => 'Round', 'time' => 'Temps', 'official_notes' => 'Notes officielles'],
    'messages' => ['saved' => 'Résultat officiel enregistré avec succès.'],
    'validation' => ['winner_required' => 'Vous devez sélectionner un vainqueur pour ce type de résultat.', 'winner_corner' => 'Le vainqueur doit appartenir au coin rouge ou bleu du combat.', 'round_limit' => 'Le round ne peut pas dépasser les :rounds rounds configurés pour le combat.'],
];

$en['admin']['fighters'] = [
    'page_title' => 'Combattants',
    'table_title' => 'Liste des combattants',
    'table_subtitle' => 'Filtrez, créez, modifiez ou supprimez les combattants sans combats ni classements associés.',
    'create' => 'Nouveau combattant',
    'edit' => 'Modifier le combattant',
    'delete_title' => 'Supprimer le combattant',
    'delete_warning' => 'Cette action supprimera le combattant :',
    'search_placeholder' => 'Nom, surnom ou slug...',
    'record_summary' => ':wins-:losses-:draws · NC :nc',
    'image_help' => 'Les images JPG, PNG ou WebP sont optimisées à l’enregistrement. Taille maximale : 5 MB.',
    'filters' => ['gender' => 'Genre', 'weight_class' => 'Catégorie', 'team' => 'Équipe'],
    'columns' => ['fighter' => 'Combattant', 'team' => 'Équipe', 'weight_class' => 'Catégorie', 'record' => 'Palmarès', 'fights' => 'Combats', 'status' => 'Statut'],
    'gender' => ['male' => 'Masculin', 'female' => 'Féminin'],
    'stance' => ['orthodox' => 'Orthodoxe', 'southpaw' => 'Gaucher', 'switch' => 'Alternée'],
    'form' => ['first_name' => 'Prénom', 'last_name' => 'Nom', 'nickname' => 'Surnom', 'slug' => 'Slug', 'gender' => 'Genre', 'country_id' => 'Pays', 'city_id' => 'Ville', 'fighter_team_id' => 'Équipe/salle', 'weight_class_id' => 'Catégorie de poids', 'birthdate' => 'Date de naissance', 'height_cm' => 'Taille (cm)', 'reach_cm' => 'Allonge (cm)', 'stance' => 'Garde', 'bio' => 'Biographie', 'wins' => 'Victoires', 'losses' => 'Défaites', 'draws' => 'Nuls', 'no_contests' => 'Sans décision', 'status' => 'Statut', 'profile_image' => 'Image de profil', 'cover_image' => 'Image de couverture'],
    'messages' => ['created' => 'Combattant enregistré avec succès.', 'updated' => 'Combattant mis à jour avec succès.', 'deleted' => 'Combattant supprimé avec succès.', 'delete_blocked' => 'Impossible de supprimer un combattant avec des combats ou classements associés.'],
];
$en['admin']['sponsors'] = ['page_title' => 'Sponsors', 'table_title' => 'Liste des sponsors', 'table_subtitle' => 'Filtrez, créez, modifiez ou supprimez les sponsors sans événements liés.', 'create' => 'Nouveau sponsor', 'edit' => 'Modifier le sponsor', 'delete_title' => 'Supprimer le sponsor', 'delete_warning' => 'Cette action supprimera le sponsor :', 'search_placeholder' => 'Nom, slug, site web ou e-mail...', 'image_help' => 'Les logos JPG, PNG ou WebP sont optimisés lors de l’enregistrement. Taille maximale : 5 MB.', 'events_summary' => ':count événements', 'events_help' => 'Maintenez Ctrl ou Cmd enfoncé pour sélectionner plusieurs événements.', 'filters' => ['event' => 'Événement'], 'columns' => ['sponsor' => 'Sponsor', 'website' => 'Site web', 'email' => 'E-mail', 'events' => 'Événements', 'order' => 'Ordre', 'status' => 'Statut'], 'form' => ['name' => 'Nom', 'slug' => 'Slug', 'website_url' => 'Site web', 'contact_email' => 'E-mail de contact', 'description' => 'Description', 'display_order' => 'Ordre', 'status' => 'Statut', 'logo_path' => 'Logo', 'events' => 'Événements liés'], 'messages' => ['created' => 'Sponsor enregistré avec succès.', 'updated' => 'Sponsor mis à jour avec succès.', 'deleted' => 'Sponsor supprimé avec succès.', 'delete_blocked' => 'Impossible de supprimer un sponsor lié à des événements.']];
$en['admin']['subscription_plans'] = ['page_title' => 'Plans d’abonnement', 'table_title' => 'Liste des plans', 'table_subtitle' => 'Filtrez, créez, modifiez ou supprimez les plans sans utilisation active.', 'create' => 'Nouveau plan', 'edit' => 'Modifier le plan', 'delete_title' => 'Supprimer le plan', 'delete_warning' => 'Cette action supprimera le plan :', 'search_placeholder' => 'Nom, slug ou description...', 'duration_summary' => ':days jours', 'discount_summary' => ':discount% de remise', 'features_summary' => ':count avantages', 'usage_summary' => ':subscriptions abonnements - :requests demandes', 'filters' => ['billing_period' => 'Période'], 'columns' => ['plan' => 'Plan', 'price' => 'Prix', 'period' => 'Période', 'usage' => 'Utilisation', 'order' => 'Ordre', 'status' => 'Statut'], 'billing_periods' => ['monthly' => 'Mensuel', 'quarterly' => 'Trimestriel', 'yearly' => 'Annuel', 'one_time' => 'Paiement unique', 'lifetime' => 'À vie'], 'form' => ['name' => 'Nom', 'slug' => 'Slug', 'description' => 'Description', 'price' => 'Prix', 'currency' => 'Devise', 'billing_period' => 'Période de facturation', 'duration_days' => 'Durée en jours', 'discount_percentage' => 'Pourcentage de remise', 'display_order' => 'Ordre', 'status' => 'Statut'], 'features' => ['title' => 'Avantages', 'add' => 'Ajouter un avantage', 'name' => 'Avantage', 'description' => 'Description', 'feature_key' => 'Clé technique', 'value' => 'Valeur', 'display_order' => 'Ordre', 'status' => 'Statut', 'help' => 'Seuls les avantages avec un nom sont enregistrés. Les avantages actifs sont aussi synchronisés dans le champ JSON features du plan.'], 'messages' => ['created' => 'Plan enregistré avec succès.', 'updated' => 'Plan mis à jour avec succès.', 'deleted' => 'Plan supprimé avec succès.', 'delete_blocked' => 'Impossible de supprimer un plan avec des abonnements ou des demandes d’achat associées.']];
$en['admin']['subscribers'] = ['page_title' => 'Abonnés', 'table_title' => 'Liste des abonnés', 'table_subtitle' => 'Filtrez, consultez et mettez à jour les données de base sans changer les rôles.', 'edit' => 'Modifier l’abonné', 'search_placeholder' => 'Nom, e-mail, téléphone ou document...', 'identity_value' => 'Document : :value', 'last_login_value' => 'Dernière connexion : :date', 'last_login_empty' => 'Aucune connexion enregistrée', 'activity_summary' => ':subscriptions abonnements - :payments paiements - :requests demandes', 'filters' => ['subscription_status' => 'Abonnement'], 'columns' => ['subscriber' => 'Abonné', 'contact' => 'Contact', 'subscription' => 'Abonnement', 'activity' => 'Activité', 'status' => 'Statut'], 'subscription_status' => ['none' => 'Sans abonnement', 'pending' => 'En attente', 'active' => 'Actif', 'expired' => 'Expiré', 'cancelled' => 'Annulé', 'suspended' => 'Suspendu'], 'form' => ['name' => 'Prénom', 'lastname' => 'Nom', 'email' => 'E-mail', 'number_phone' => 'Téléphone', 'identity_document' => 'Document d’identité', 'state' => 'Statut du compte'], 'messages' => ['updated' => 'Abonné mis à jour avec succès.']];

$en['admin']['rankings'] = [
    'page_title' => 'Classements',
    'table_title' => 'Liste des classements',
    'table_subtitle' => 'Filtrez, créez et ajustez les positions officielles par catégorie et genre.',
    'create' => 'Nouveau classement',
    'edit' => 'Modifier le classement',
    'search_placeholder' => 'Combattant, surnom ou catégorie...',
    'champion' => 'Champion',
    'filters' => ['weight_class' => 'Catégorie', 'gender' => 'Genre'],
    'columns' => ['position' => 'Position', 'fighter' => 'Combattant', 'weight_class' => 'Catégorie', 'record' => 'Palmarès', 'movement' => 'Mouvement', 'status' => 'Statut'],
    'movement' => ['same' => 'Aucun changement', 'up' => 'Monte de :places', 'down' => 'Descend de :places'],
    'form' => ['weight_class_id' => 'Catégorie de poids', 'gender' => 'Genre', 'fighter_id' => 'Combattant', 'position' => 'Position actuelle', 'previous_position' => 'Position précédente', 'is_champion' => 'Marquer comme champion', 'ranked_at' => 'Date du classement', 'status' => 'Statut'],
    'messages' => ['created' => 'Classement enregistré avec succès.', 'updated' => 'Classement mis à jour avec succès.'],
    'validation' => ['gender_mismatch' => 'Le combattant sélectionné ne correspond pas au genre du classement.', 'weight_class_mismatch' => 'Le combattant sélectionné ne correspond pas à la catégorie de poids du classement.', 'position_taken' => 'Un classement actif existe déjà avec cette position pour la catégorie et le genre sélectionnés.'],
];

$en['admin']['fighter_teams'] = [
    'page_title' => 'Équipes et salles',
    'table_title' => 'Liste des équipes',
    'table_subtitle' => 'Filtrez, créez, modifiez ou supprimez les équipes sans combattants associés.',
    'create' => 'Nouvelle équipe',
    'edit' => 'Modifier l’équipe',
    'delete_title' => 'Supprimer l’équipe',
    'delete_warning' => 'Cette action supprimera l’équipe :',
    'search_placeholder' => 'Nom, coach, téléphone ou slug...',
    'image_help' => 'Les logos JPG, PNG ou WebP sont optimisés à l’enregistrement. Taille maximale : 5 MB.',
    'filters' => ['city' => 'Ville'],
    'columns' => ['team' => 'Équipe', 'city' => 'Ville', 'coach' => 'Coach', 'contact' => 'Contact', 'fighters' => 'Combattants', 'status' => 'Statut'],
    'form' => ['name' => 'Nom', 'slug' => 'Slug', 'city_id' => 'Ville', 'coach_name' => 'Coach', 'contact_phone' => 'Téléphone', 'description' => 'Description', 'status' => 'Statut', 'logo_path' => 'Logo'],
    'messages' => ['created' => 'Équipe enregistrée avec succès.', 'updated' => 'Équipe mise à jour avec succès.', 'deleted' => 'Équipe supprimée avec succès.', 'delete_blocked' => 'Impossible de supprimer une équipe avec des combattants associés.'],
];
$en['admin']['purchase_requests'] = [
    'page_title' => 'Demandes d’achat',
    'table_title' => 'Liste des demandes',
    'table_subtitle' => 'Filtrez, assignez des responsables et mettez à jour le statut de chaque demande.',
    'search_placeholder' => 'Nom, e-mail, téléphone ou UUID...',
    'modal_title' => 'Gérer la demande',
    'delete_title' => 'Supprimer la demande',
    'delete_warning' => 'Cette action supprimera la demande de :',
    'filters' => ['request_type' => 'Type', 'channel' => 'Canal', 'assigned_to' => 'Assigné à', 'from' => 'Du', 'to' => 'Au'],
    'columns' => ['request' => 'Demande', 'contact' => 'Contact', 'related_to' => 'Lié à', 'status' => 'Statut', 'assigned_to' => 'Assigné à', 'created_at' => 'Date'],
    'status' => ['pending' => 'En attente', 'in_review' => 'En révision', 'contacted' => 'Contacté', 'converted' => 'Converti', 'closed' => 'Fermé', 'rejected' => 'Rejeté'],
    'request_types' => ['general_contact' => 'Contact général', 'event_ticket' => 'Billets d’événement', 'subscription' => 'Abonnement', 'payment_proof' => 'Justificatif de paiement'],
    'channels' => ['whatsapp' => 'WhatsApp', 'phone' => 'Téléphone', 'email' => 'E-mail'],
    'assignment' => ['unassigned' => 'Non assigné', 'me' => 'Assignées à moi'],
    'actions' => ['manage' => 'Gérer', 'assign_to_me' => 'M’assigner', 'close' => 'Fermer'],
    'details' => ['title' => 'Détails de la demande', 'contact' => 'Contact', 'request_type' => 'Type', 'email' => 'E-mail', 'channel' => 'Canal', 'phone' => 'Téléphone/WhatsApp', 'related_to' => 'Lié à', 'message' => 'Message', 'proof' => 'Justificatif'],
    'proof' => ['available' => 'Justificatif', 'open' => 'Ouvrir le justificatif privé', 'none' => 'Aucun justificatif chargé'],
    'form' => ['status' => 'Statut', 'assigned_to' => 'Responsable', 'notes' => 'Notes internes'],
    'messages' => ['updated' => 'Demande mise à jour avec succès.', 'assigned' => 'Demande assignée avec succès.', 'closed' => 'Demande fermée avec succès.', 'deleted' => 'Demande supprimée avec succès.'],
];
$en['admin']['dashboard']['page_title'] = 'Tableau de bord';
$en['admin']['weight_classes']['page_title'] = 'Catégories de poids';
$en['landing'] = ['login' => 'Connexion', 'hero_text' => 'Événements, combats, classements et abonnements pour un promoteur d’arts martiaux.', 'view_events' => 'Voir les événements', 'contact' => 'Contact', 'events_title' => 'Événements actifs', 'events_subtitle' => 'Événements publiés, à venir et passés qui restent actifs.', 'featured' => 'À la une', 'empty_events' => 'Aucun événement publié pour le moment.', 'no_image' => 'Aucune image disponible', 'back' => 'Retour', 'fights_title' => 'Programme des combats', 'empty_fights' => 'Cet événement n’a pas encore de combats publiés.', 'vs' => 'vs', 'nav' => ['home' => 'Accueil', 'fighters' => 'Combattants', 'news' => 'Actualités', 'subscription' => 'Abonnement', 'contact' => 'Contact'], 'footer' => ['quick_links' => 'Liens rapides', 'contact' => 'Contact', 'follow_us' => 'Suivez-nous', 'rights' => 'Tous droits réservés.'], 'fighters' => ['featured_title' => 'Combattants en vedette', 'featured_subtitle' => 'Découvrez quelques athlètes de notre effectif.', 'view_all' => 'Voir tous les combattants', 'title' => 'Combattants', 'subtitle' => 'Découvrez l’effectif complet des combattants actifs.', 'empty' => 'Aucun combattant publié pour le moment.', 'wins' => 'Victoires', 'losses' => 'Défaites', 'draws' => 'Nuls', 'bio_title' => 'Biographie', 'fight_history' => 'Historique des combats', 'round' => 'Round', 'result_win' => 'Victoire', 'result_loss' => 'Défaite', 'result_pending' => 'En attente', 'no_fights' => 'Aucun combat enregistré pour le moment.'], 'news' => ['section_title' => 'Dernières actualités', 'view_all' => 'Voir toutes les actualités', 'title' => 'Actualités', 'subtitle' => 'Annonces, nouveautés et interviews.', 'read_more' => 'Lire la suite', 'empty' => 'Aucune actualité publiée pour le moment.'], 'event' => ['prev' => 'Événement précédent', 'next' => 'Événement suivant', 'main_event' => 'Combat principal', 'tickets_title' => 'Billetterie', 'price_from' => 'À partir de', 'no_tickets' => 'Les liens de vente seront publiés prochainement.', 'contact_cta' => 'Contactez-nous'], 'subscription' => ['title' => 'Abonnement', 'subtitle' => 'Choisissez le plan qui vous convient et profitez d’avantages exclusifs.', 'empty' => 'Aucun plan disponible pour le moment.', 'cta' => 'Je veux m’abonner'], 'contact_page' => ['title' => 'Contact', 'subtitle' => 'Envoyez-nous votre demande, notre équipe vous répondra rapidement.', 'about_event' => 'Vous nous contactez au sujet de : :event', 'about_plan' => 'Vous nous contactez au sujet de : :plan', 'success' => 'Votre demande a été envoyée avec succès. Nous vous contacterons rapidement.', 'direct_title' => 'Vous préférez nous contacter directement ?', 'direct_hint' => 'Vous pouvez aussi contacter notre équipe via l’un de ces canaux.', 'form' => ['name' => 'Nom complet', 'email' => 'E-mail', 'phone' => 'Téléphone', 'whatsapp' => 'WhatsApp', 'channel' => 'Canal de contact préféré', 'type' => 'Motif du contact', 'message' => 'Message', 'proof' => 'Justificatif de paiement', 'proof_hint' => 'Optionnel. Formats acceptés : JPG, JPEG, PNG ou PDF, 5 Mo maximum.', 'submit' => 'Envoyer la demande'], 'channel_options' => ['whatsapp' => 'WhatsApp', 'phone' => 'Téléphone', 'email' => 'E-mail'], 'type_options' => ['general_contact' => 'Demande générale', 'event_ticket' => 'Billets d’événement', 'subscription' => 'Abonnement', 'payment_proof' => 'Justificatif de paiement']]];
$en['uploads']['payment_proofs'] = ['invalid_type' => 'Format non pris en charge. Utilisez JPG, JPEG, PNG ou PDF.', 'max_size' => 'Le justificatif ne peut pas dépasser :max MB.'];
$en['uploads']['public_images'] = ['invalid_type' => 'Format non pris en charge. Utilisez JPG, PNG, GIF ou WebP.', 'max_size' => 'L’image ne peut pas dépasser :max MB.', 'process_failed' => 'L’image sélectionnée n’a pas pu être traitée.'];

$en['admin']['user_subscriptions'] = [
    'page_title' => 'Abonnements utilisateur',
    'table_title' => 'Liste des abonnements',
    'table_subtitle' => 'Filtrez, créez, modifiez ou annulez les abonnements utilisateur sans traiter de paiements automatiques.',
    'create' => 'Nouvel abonnement',
    'edit' => 'Modifier l’abonnement',
    'cancel_title' => 'Annuler l’abonnement',
    'cancel_warning' => 'Cette action marquera l’abonnement comme annulé :',
    'search_placeholder' => 'Abonné, e-mail, téléphone, plan ou slug...',
    'period_value' => ':start - :end',
    'open_ended' => 'Sans date de fin',
    'renewal_value' => 'Renouvellement : :date',
    'payments_summary' => ':count paiements',
    'filters' => [
        'plan' => 'Plan',
        'from' => 'Du',
        'to' => 'Au',
    ],
    'columns' => [
        'subscriber' => 'Abonné',
        'plan' => 'Plan',
        'period' => 'Période',
        'status' => 'Statut',
        'payments' => 'Paiements',
        'source' => 'Origine',
    ],
    'status' => [
        'pending' => 'En attente',
        'active' => 'Actif',
        'expired' => 'Expiré',
        'cancelled' => 'Annulé',
        'suspended' => 'Suspendu',
    ],
    'sources' => [
        'manual' => 'Manuel',
        'admin' => 'Administration',
        'purchase_request' => 'Demande d’achat',
        'import' => 'Importation',
        'other' => 'Autre',
    ],
    'actions' => [
        'cancel' => 'Annuler l’abonnement',
    ],
    'form' => [
        'user_id' => 'Abonné',
        'subscription_plan_id' => 'Plan',
        'starts_at' => 'Date et heure de début',
        'ends_at' => 'Date et heure de fin',
        'trial_ends_at' => 'Fin de la période d’essai',
        'renewal_at' => 'Date de renouvellement',
        'status' => 'Statut',
        'source' => 'Origine',
        'metadata_note' => 'Note interne',
    ],
    'messages' => [
        'created' => 'Abonnement enregistré avec succès.',
        'updated' => 'Abonnement mis à jour avec succès.',
        'cancelled' => 'Abonnement annulé avec succès.',
    ],
    'validation' => [
        'subscriber_required' => 'L’utilisateur sélectionné doit avoir le rôle abonné.',
        'ends_at_after_start' => 'La date de fin doit être postérieure ou égale à la date de début.',
        'trial_ends_at_after_start' => 'La date d’essai doit être postérieure ou égale à la date de début.',
        'renewal_at_after_start' => 'La date de renouvellement doit être postérieure ou égale à la date de début.',
    ],
];

$en['admin']['subscription_payments'] = [
    'page_title' => 'Paiements d’abonnement',
    'table_title' => 'Liste des paiements',
    'table_subtitle' => 'Filtrez, enregistrez, mettez à jour, confirmez ou annulez les paiements manuels.',
    'create' => 'Nouveau paiement',
    'edit' => 'Modifier le paiement',
    'confirm_title' => 'Confirmer le paiement',
    'confirm_warning' => 'Cette action marquera le paiement comme payé :',
    'cancel_title' => 'Annuler le paiement',
    'cancel_warning' => 'Cette action marquera le paiement comme échoué :',
    'search_placeholder' => 'Abonné, e-mail, téléphone, plan, fournisseur ou transaction...',
    'paid_at_value' => 'Payé : :date',
    'not_paid' => 'Aucune date de paiement',
    'filters' => [
        'payment_method' => 'Méthode',
        'from' => 'Du',
        'to' => 'Au',
    ],
    'columns' => [
        'subscriber' => 'Abonné',
        'subscription' => 'Abonnement',
        'amount' => 'Montant',
        'method' => 'Méthode',
        'proof' => 'Justificatif',
        'status' => 'Statut',
    ],
    'status' => [
        'pending' => 'En attente',
        'paid' => 'Payé',
        'failed' => 'Échoué',
        'refunded' => 'Remboursé',
        'expired' => 'Expiré',
    ],
    'payment_methods' => [
        'manual_transfer' => 'Virement manuel',
        'cash' => 'Espèces',
        'qr' => 'Paiement QR',
        'whatsapp' => 'WhatsApp',
        'gateway' => 'Passerelle',
        'other' => 'Autre',
    ],
    'actions' => [
        'confirm' => 'Confirmer le paiement',
        'cancel' => 'Annuler le paiement',
    ],
    'proof' => [
        'open' => 'Ouvrir le justificatif',
        'none' => 'Sans justificatif',
        'help' => 'JPG, JPEG, PNG ou PDF. Les images sont optimisées à l’enregistrement. Taille maximale : 5 MB.',
    ],
    'form' => [
        'user_id' => 'Abonné',
        'user_subscription_id' => 'Abonnement',
        'no_subscription' => 'Sans abonnement lié',
        'amount' => 'Montant',
        'currency' => 'Devise',
        'payment_method' => 'Méthode de paiement',
        'provider' => 'Fournisseur',
        'provider_transaction_id' => 'ID de transaction',
        'payment_url' => 'URL de paiement',
        'paid_at' => 'Date de paiement',
        'expires_at' => 'Date d’expiration',
        'status' => 'Statut',
        'notes' => 'Notes internes',
        'payment_proof' => 'Justificatif de paiement',
    ],
    'messages' => [
        'created' => 'Paiement enregistré avec succès.',
        'updated' => 'Paiement mis à jour avec succès.',
        'confirmed' => 'Paiement confirmé avec succès.',
        'cancelled' => 'Paiement annulé avec succès.',
    ],
    'validation' => [
        'subscriber_required' => 'L’utilisateur sélectionné doit avoir le rôle abonné.',
        'subscription_user_mismatch' => 'L’abonnement sélectionné n’appartient pas à l’abonné sélectionné.',
    ],
];

$en['admin']['ticket_links'] = [
    'page_title' => 'Liens de billets',
    'table_title' => 'Liste des liens de billets',
    'table_subtitle' => 'Filtrez, créez, modifiez ou supprimez les liens publics de vente associés aux événements.',
    'create' => 'Nouveau lien',
    'edit' => 'Modifier le lien',
    'delete_title' => 'Supprimer le lien de billets',
    'delete_warning' => 'Cette action supprimera le lien de billets :',
    'search_placeholder' => 'Fournisseur, libellé, URL ou événement...',
    'open_start' => 'Sans limite de début',
    'open_end' => 'Sans limite de fin',
    'filters' => [
        'event' => 'Événement',
        'sale_channel' => 'Canal',
    ],
    'columns' => [
        'link' => 'Lien',
        'event' => 'Événement',
        'channel' => 'Canal',
        'price' => 'Prix à partir de',
        'window' => 'Fenêtre de vente',
        'status' => 'Statut',
    ],
    'sale_channels' => [
        'external' => 'Plateforme externe',
        'whatsapp' => 'WhatsApp',
        'phone' => 'Téléphone',
        'streaming' => 'Streaming',
        'vip' => 'VIP',
        'other' => 'Autre',
    ],
    'form' => [
        'event_id' => 'Événement',
        'provider_name' => 'Fournisseur',
        'label' => 'Libellé visible',
        'sale_channel' => 'Canal de vente',
        'url' => 'URL',
        'price_from' => 'Prix à partir de',
        'currency' => 'Devise',
        'starts_at' => 'Début de vente',
        'ends_at' => 'Fin de vente',
        'display_order' => 'Ordre',
        'status' => 'Statut',
    ],
    'messages' => [
        'created' => 'Lien de billets enregistré avec succès.',
        'updated' => 'Lien de billets mis à jour avec succès.',
        'deleted' => 'Lien de billets supprimé avec succès.',
    ],
    'validation' => [
        'ends_after_start' => 'La date de fin de vente doit être postérieure ou égale à la date de début.',
    ],
];

$en['admin']['system_settings'] = [
    'page_title' => 'Paramètres du système',
    'form_title' => 'Configuration générale',
    'form_subtitle' => 'Ces valeurs sont reflétées dans le panneau d’administration, la connexion et la landing publique.',
    'image_help' => 'Les images JPG, PNG ou WebP sont optimisées lors de l’enregistrement. Taille maximale : 5 MB.',
    'sections' => [
        'identity' => 'Identité',
        'contact' => 'Contact',
        'social' => 'Réseaux sociaux',
        'seo' => 'Landing publique et SEO',
    ],
    'form' => [
        'product_name' => 'Nom du produit',
        'public_title' => 'Titre public',
        'contact_email' => 'E-mail de contact',
        'contact_phone' => 'Téléphone de contact',
        'whatsapp_phone' => 'Téléphone WhatsApp',
        'short_description' => 'Description courte',
        'seo_title' => 'Titre SEO',
        'seo_description' => 'Description SEO',
        'landing_show_rankings' => 'Afficher les rankings sur la landing publique',
        'logo_path' => 'Logo',
        'favicon_path' => 'Favicon',
    ],
    'social' => [
        'facebook' => 'URL Facebook',
        'instagram' => 'URL Instagram',
        'youtube' => 'URL YouTube',
        'tiktok' => 'URL TikTok',
    ],
    'actions' => [
        'save' => 'Enregistrer les paramètres',
    ],
    'messages' => [
        'updated' => 'Paramètres du système mis à jour avec succès.', 'updated_with_images' => 'Paramètres et images mis à jour avec succès.', 'uploading' => 'Téléversement de l’image...',
    ],
];

$en['menu']['events']['venues'] = 'Salles';
$en['admin']['venues'] = [
    'page_title' => 'Salles',
    'table_title' => 'Liste des salles',
    'table_subtitle' => 'Filtrez, créez, modifiez ou supprimez des salles sans événements liés.',
    'create' => 'Nouvelle salle',
    'edit' => 'Modifier la salle',
    'delete_title' => 'Supprimer la salle',
    'delete_warning' => 'Cette action supprimera la salle :',
    'search_placeholder' => 'Nom, adresse, contact, téléphone ou slug...',
    'image_help' => 'Les images JPG, PNG ou WebP sont optimisées lors de l’enregistrement. Taille maximale : 5 MB.',
    'filters' => ['city' => 'Ville'],
    'columns' => ['venue' => 'Salle', 'location' => 'Emplacement', 'capacity' => 'Capacité', 'contact' => 'Contact', 'events' => 'Événements', 'status' => 'Statut'],
    'form' => ['name' => 'Nom', 'slug' => 'Slug', 'city_id' => 'Ville', 'address' => 'Adresse', 'latitude' => 'Latitude', 'longitude' => 'Longitude', 'capacity' => 'Capacité', 'contact_name' => 'Nom du contact', 'contact_phone' => 'Téléphone du contact', 'status' => 'Statut', 'image' => 'Image de la salle'],
    'messages' => ['created' => 'Salle enregistrée avec succès.', 'updated' => 'Salle mise à jour avec succès.', 'deleted' => 'Salle supprimée avec succès.', 'delete_blocked' => 'Une salle avec des événements liés ne peut pas être supprimée.'],
];

$en['admin']['users'] = [
    'page_title' => 'Utilisateurs',
    'table_title' => 'Liste des utilisateurs',
    'table_subtitle' => 'Filtrez, créez, modifiez ou supprimez les utilisateurs autorisés par la hiérarchie des rôles.',
    'create' => 'Nouvel utilisateur',
    'edit' => 'Modifier l’utilisateur',
    'delete_title' => 'Supprimer l’utilisateur',
    'delete_warning' => 'Cette action supprimera l’utilisateur :',
    'search_placeholder' => 'Nom, e-mail, téléphone ou document...',
    'password_help' => 'Lors de la modification, laissez ce champ vide pour conserver le mot de passe actuel.',
    'selected_roles' => 'rôles sélectionnés',
    'hierarchy_readonly' => 'Lecture seule par hiérarchie',
    'filters' => ['role' => 'Rôle'],
    'columns' => ['user' => 'Utilisateur', 'contact' => 'Contact', 'roles' => 'Rôles', 'last_login' => 'Dernière connexion', 'status' => 'Statut'],
    'form' => ['name' => 'Prénom', 'lastname' => 'Nom', 'email' => 'E-mail', 'number_phone' => 'Téléphone', 'identity_document' => 'Document d’identité', 'state' => 'Statut', 'password' => 'Mot de passe', 'roles' => 'Rôles'],
    'messages' => ['created' => 'Utilisateur enregistré avec succès.', 'updated' => 'Utilisateur mis à jour avec succès.', 'deleted' => 'Utilisateur supprimé avec succès.', 'self_delete_blocked' => 'Vous ne pouvez pas supprimer votre propre compte utilisateur.'],
    'validation' => ['roles_allowed' => 'Sélectionnez au moins un rôle que vous êtes autorisé à attribuer.'],
];

$en['menu']['fighters']['media'] = 'Médias des combattants';
$en['admin']['fighter_media'] = [
    'page_title' => 'Médias des combattants',
    'table_title' => 'Liste des médias des combattants',
    'table_subtitle' => 'Filtrez, envoyez, modifiez ou supprimez les médias publics des combattants.',
    'create' => 'Nouveau média',
    'edit' => 'Modifier le média',
    'delete_title' => 'Supprimer le média',
    'delete_warning' => 'Cette action supprimera le média :',
    'search_placeholder' => 'Titre, description, combattant ou surnom...',
    'image_help' => 'Les images JPG, PNG ou WebP sont optimisées lors de l’enregistrement. Taille maximale : 5 MB.',
    'featured' => 'Mis en avant',
    'untitled' => 'Sans titre',
    'filters' => ['fighter' => 'Combattant', 'file_type' => 'Type'],
    'columns' => ['media' => 'Média', 'fighter' => 'Combattant', 'order' => 'Ordre', 'status' => 'Statut'],
    'file_types' => ['image' => 'Image', 'video' => 'Vidéo'],
    'form' => ['fighter_id' => 'Combattant', 'file_type' => 'Type de fichier', 'file_path' => 'URL vidéo', 'title' => 'Titre', 'description' => 'Description', 'is_featured' => 'Marquer comme mis en avant', 'display_order' => 'Ordre', 'status' => 'Statut', 'media_image' => 'Image'],
    'messages' => ['created' => 'Média du combattant enregistré avec succès.', 'updated' => 'Média du combattant mis à jour avec succès.', 'deleted' => 'Média du combattant supprimé avec succès.'],
    'validation' => ['image_required' => 'Vous devez sélectionner une image pour enregistrer ce média de combattant.'],
];

$en['admin']['landing'] = [
    'page_title' => 'Landing publique',
    'form_title' => 'Configuration de la landing',
    'form_subtitle' => 'Ces valeurs apparaissent sur la page publique et doivent rester alignées avec les événements publiés actifs.',
    'latest_events' => 'Aperçu des événements publiés',
    'empty_events' => 'Aucun événement publié à prévisualiser.',
    'stats' => ['published' => 'Publiés', 'featured' => 'Mis en avant', 'drafts' => 'Brouillons'],
    'actions' => ['save' => 'Enregistrer la landing', 'open_public' => 'Ouvrir la page publique', 'manage_events' => 'Gérer les événements'],
    'form' => ['public_title' => 'Titre public', 'short_description' => 'Description courte', 'seo_title' => 'Titre SEO', 'seo_description' => 'Description SEO', 'landing_show_rankings' => 'Afficher les rankings sur la landing publique'],
    'messages' => ['updated' => 'Landing publique mise à jour avec succès.'],
];

$en['subscriber_portal'] = [
    'menu' => ['dashboard' => 'Accueil', 'purchases' => 'Mes achats', 'events' => 'Mes événements', 'subscription' => 'Mon abonnement', 'profile' => 'Profil'],
    'dashboard' => ['title' => 'Accueil abonné', 'subtitle' => 'Consultez votre compte, abonnement, achats et événements disponibles.', 'cards' => ['account' => 'Statut du compte', 'subscription' => 'Statut de l’abonnement', 'events' => 'Événements disponibles'], 'latest_purchases' => 'Derniers paiements', 'latest_requests' => 'Dernières demandes', 'next_events' => 'Événements disponibles'],
    'actions' => ['view_all' => 'Tout voir', 'save' => 'Enregistrer', 'back_to_purchases' => 'Retour à mes achats'],
    'empty' => ['no_subscription' => 'Aucun abonnement actif', 'no_purchases' => 'Aucun achat enregistré.', 'no_requests' => 'Aucune demande enregistrée.', 'no_events' => 'Vous n’avez aucun événement disponible.'],
    'events' => ['title' => 'Mes événements', 'subtitle' => 'Événements associés à vos demandes, achats ou avantages d’accès.'],
    'purchases' => ['title' => 'Mes achats', 'subtitle' => 'Consultez les paiements et demandes enregistrés pour votre compte.', 'payments' => 'Paiements', 'requests' => 'Demandes', 'detail_title' => 'Détail de l’achat', 'detail_subtitle' => 'Informations complètes de ce paiement ou de cette demande.', 'proof_status' => 'Statut du justificatif', 'proof_uploaded_label' => 'Justificatif envoyé', 'proof_missing_label' => 'Aucun justificatif envoyé', 'notes' => 'Notes', 'message' => 'Message', 'related_event' => 'Événement associé', 'upload_proof_title' => 'Envoyer ou renvoyer un justificatif', 'upload_proof_hint' => 'Formats acceptés : JPG, JPEG, PNG ou PDF, 5 Mo maximum.', 'upload_proof_submit' => 'Envoyer le justificatif', 'proof_field' => 'Justificatif de paiement', 'proof_uploaded' => 'Justificatif envoyé avec succès. Notre équipe l’examinera prochainement.', 'proof_not_allowed' => 'Cet enregistrement n’accepte plus de nouveau justificatif. Contactez le support.', 'contact_title' => 'Besoin d’aide avec cet achat ?', 'contact_hint' => 'Contactez notre équipe commerciale ou support via l’un de ces canaux.'],
    'subscription' => ['title' => 'Mon abonnement', 'subtitle' => 'Consultez votre plan actuel et l’historique des abonnements.', 'current' => 'Abonnement actuel', 'history' => 'Historique des abonnements', 'benefits' => 'Avantages du plan', 'contact_title' => 'Vous voulez renouveler ou changer de plan ?', 'contact_hint' => 'Contactez notre équipe commerciale pour renouveler, changer de plan ou poser vos questions.'],
    'profile' => ['title' => 'Profil', 'subtitle' => 'Mettez à jour vos propres coordonnées.', 'updated' => 'Profil mis à jour avec succès.', 'form' => ['name' => 'Prénom', 'lastname' => 'Nom', 'email' => 'E-mail', 'number_phone' => 'Téléphone', 'identity_document' => 'Document d’identité', 'current_password' => 'Mot de passe actuel', 'new_password' => 'Nouveau mot de passe', 'confirm_password' => 'Confirmer le mot de passe'], 'password_title' => 'Changer le mot de passe', 'password_hint' => 'Utilisez au moins 8 caractères et évitez de réutiliser d’anciens mots de passe.', 'password_updated' => 'Mot de passe mis à jour avec succès.', 'password_submit' => 'Mettre à jour le mot de passe'],
    'columns' => ['concept' => 'Concept', 'amount' => 'Montant', 'method' => 'Méthode', 'status' => 'Statut', 'date' => 'Date', 'channel' => 'Canal', 'plan' => 'Plan', 'start' => 'Début', 'end' => 'Fin', 'period' => 'Période'],
];

$en['menu']['reports'] = ['group' => 'Rapports', 'events' => 'Rapports événements', 'subscriptions' => 'Rapports abonnements', 'sales' => 'Rapports ventes'];
$en['admin']['reports'] = [
    'page_title' => 'Rapports',
    'table_title' => 'Rapports opérationnels',
    'table_subtitle' => 'Filtrez par type de rapport, période et statut sans modifier les enregistrements.',
    'types' => ['events' => 'Événements', 'subscriptions' => 'Abonnements', 'sales' => 'Ventes'],
    'filters' => ['type' => 'Type de rapport', 'from' => 'Depuis', 'to' => 'Jusqu’à'],
    'stats' => ['total' => 'Total', 'published' => 'Publiés', 'featured' => 'Mis en avant', 'requests' => 'Demandes', 'active' => 'Actifs', 'pending' => 'En attente', 'expired' => 'Expirés', 'paid' => 'Payés', 'amount' => 'Montant payé'],
    'columns' => ['event' => 'Événement', 'venue' => 'Lieu', 'date' => 'Date', 'fights' => 'Combats', 'requests' => 'Demandes', 'subscriber' => 'Abonné', 'plan' => 'Plan', 'period' => 'Période', 'payments' => 'Paiements', 'concept' => 'Concept', 'amount' => 'Montant', 'method' => 'Méthode'],
];

$en['admin']['logs'] = [
    'page_title' => 'Logs',
    'table_title' => 'Log de l’application',
    'file_size' => 'Taille du fichier',
    'last_modified' => 'Dernière modification',
    'total_entries' => 'Entrées totales',
    'filtered_entries' => 'Entrées filtrées',
    'detail_title' => 'Détail de l’entrée du log',
    'context' => 'Contexte',
    'trace' => 'Trace',
    'raw' => 'Entrée originale',
    'has_context' => 'Inclut un contexte',
    'has_trace' => 'Inclut une trace',
    'filters' => ['level' => 'Niveau', 'all_levels' => 'Tous les niveaux', 'from_date' => 'Depuis', 'to_date' => 'Jusqu’à', 'search_placeholder' => 'Rechercher message, contexte ou trace...'],
    'columns' => ['datetime' => 'Date et heure', 'level' => 'Niveau', 'env' => 'Environnement', 'message' => 'Message'],
    'actions' => ['download' => 'Télécharger le log', 'view_detail' => 'Voir le détail'],
];

$en['landing']['fighters']['result_draw'] = 'Nul / Sans décision';

return $en;
