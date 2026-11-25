<?php
/**
 * Initailisation de la BDD avec réation des table et bdd si existe pas
 */
require_once __DIR__ . '/db/initialisation.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Mon Calendrier</title>
</head>

<body class="mode-consultation">

    <header class="topbar">
        <div class="topbar-left">
            <h1 class="app-title">Calendrier</h1>
            <div class="nav-controls">
                <button id="btn-prev" title="Précédent">&lt;</button>
                <button id="btn-today">Aujourd'hui</button>
                <button id="btn-next" title="Suivant">&gt;</button>
            </div>
            <h2 id="periode-courante">Novembre 2026</h2>
        </div>

        <div class="topbar-right">
            <div class="btn-group">
                <button class="btn-vue active" data-vue="mois">Mois</button>
                <button class="btn-vue" data-vue="semaine">Semaine</button>
                <button class="btn-vue" data-vue="jour">Jour</button>
            </div>

            <div class="btn-group">
                <button id="btn-mode-consultation" class="active">Consultation</button>
                <button id="btn-mode-edition">Édition</button>
            </div>
            <button id="btn-undo" class="edition-only" disabled>Annuler</button>
        </div>
    </header>

    <div class="layout">

        <aside class="sidebar">
            <button id="btn-new-event" class="btn-primary edition-only">+ Nouvel événement</button>

            <div class="sidebar-section">
                <h3>Rechercher</h3>
                <input type="text" id="input-search" placeholder="Mot-clé dans la description...">
                <button id="btn-clear-search" style="display:none;">Effacer la recherche</button>
            </div>

            <div class="sidebar-section">
                <div class="section-header">
                    <h3>Mes calendriers</h3>
                    <button id="btn-new-calendar" class="btn-small edition-only" title="Créer un calendrier">+</button>
                </div>
                <ul id="liste-calendriers">
                </ul>
            </div>
        </aside>

        <main class="main-content">
            <div id="resultats-recherche" style="display:none;"></div>
            <div id="calendrier-grille">
            </div>
        </main>

    </div>

    <div id="modal-details" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <div class="details-header">
                <img id="detail-icone" class="event-icon-large" src="" alt="" style="display:none;">
                <h3 id="detail-titre"></h3>
            </div>
            <p><strong>Calendrier :</strong> <span id="detail-calendrier"></span></p>
            <p><strong>Date :</strong> <span id="detail-date"></span></p>
            <p><strong>Horaire :</strong> <span id="detail-horaire"></span></p>
            <p><strong>Description :</strong></p>
            <p id="detail-description" class="detail-desc-box"></p>

            <div class="modal-actions edition-only">
                <button id="btn-edit-from-detail" class="btn-primary">Modifier</button>
                <button id="btn-duplicate-from-detail">Dupliquer</button>
                <button id="btn-delete-from-detail" class="btn-danger">Supprimer</button>
            </div>
        </div>
    </div>

    <div id="modal-event" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h3 id="modal-event-title">Nouvel événement</h3>
            <form id="form-event">
                <input type="hidden" id="event-id" name="id_evenement">

                <label for="event-nom">Titre *</label>
                <input type="text" id="event-nom" name="nom_evenement" required>

                <label for="event-calendrier">Calendrier *</label>
                <select id="event-calendrier" name="id_calendrier" required></select>

                <label for="event-date">Date *</label>
                <input type="date" id="event-date" name="date_evenement" required>

                <label class="checkbox-label">
                    <input type="checkbox" id="event-journee" name="journee_evenement" value="1">
                    Toute la journée
                </label>

                <div id="groupe-horaire" class="form-row">
                    <div>
                        <label for="event-heure">Heure de début</label>
                        <input type="time" id="event-heure" name="heure_evenement" value="09:00">
                    </div>
                    <div>
                        <label for="event-duree">Durée (minutes)</label>
                        <input type="number" id="event-duree" name="duree_evenement" min="5" step="5" value="60">
                    </div>
                </div>

                <label for="event-description">Description</label>
                <textarea id="event-description" name="description_evenement" rows="3"></textarea>

                <label>Icône de l'événement</label>
                <div class="icon-source-choice">
                    <label><input type="radio" name="icon_mode" value="server" checked> Icône prédéfinie</label>
                    <label><input type="radio" name="icon_mode" value="url"> URL d'image</label>
                </div>

                <div id="zone-icones-serveur" class="icons-grid">
                </div>

                <input type="url" id="event-icone-url" placeholder="https://exemple.com/image.png"
                    style="display:none;">
                <input type="hidden" id="event-icone" name="icone_evenement">

                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Enregistrer</button>
                    <button type="button" class="btn-cancel-modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-calendar" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h3>Nouveau calendrier</h3>
            <form id="form-calendar">
                <label for="cal-nom">Nom du calendrier *</label>
                <input type="text" id="cal-nom" name="nom_calendrier" required>

                <label for="cal-desc">Description</label>
                <textarea id="cal-desc" name="description_calendrier" rows="2"></textarea>

                <label for="cal-couleur">Couleur</label>
                <input type="color" id="cal-couleur" name="couleur_calendrier" value="#b77ee6">

                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Créer</button>
                    <button type="button" class="btn-cancel-modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" style="display:none;"></div>
    <script src="https://code.jquery.com/jquery-4.1.0.min.js"></script>
    <script src="js/app.js"></script>
</body>

</html>