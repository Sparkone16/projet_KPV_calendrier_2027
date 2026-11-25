<?php
/**
 * API AJAX de l'application de calendrier.
 * Reçoit une action en GET ou POST et renvoie une réponse au format JSON.
 */

header('content-type: application/json; charset=utf-8');

require_once __DIR__ . '/db/initialisation.php';
require_once __DIR__ . '/db/App.php';

$manager = new App($pdo);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

try {
    switch ($action) {

        // --- Lister les icônes disponibles dans le dossier images/ ---
        case 'list_icons':
            $fichiers = scandir(__DIR__ . '/images');
            $icones = array();
            foreach ($fichiers as $fichier) {
                if ($fichier !== '.' && $fichier !== '..') {
                    $icones[] = 'images/' . $fichier;
                }
            }
            echo json_encode(array('status' => 'ok', 'data' => $icones), JSON_UNESCAPED_SLASHES);
            break;

        // --- Calendriers ---
        case 'list_calendars':
            $calendriers = $manager->getCalendars();
            echo json_encode(array('status' => 'ok', 'data' => $calendriers), JSON_UNESCAPED_SLASHES);
            break;

        case 'create_calendar':
            $nom = isset($_POST['nom_calendrier']) ? trim($_POST['nom_calendrier']) : '';
            $desc = isset($_POST['description_calendrier']) ? trim($_POST['description_calendrier']) : '';
            $couleur = isset($_POST['couleur_calendrier']) ? trim($_POST['couleur_calendrier']) : '#b77ee6';

            if ($nom === '') {
                echo json_encode(array('status' => 'error', 'message' => 'Le nom du calendrier est obligatoire.'));
                break;
            }

            $id = $manager->createCalendar($nom, $desc, $couleur);
            echo json_encode(array('status' => 'ok', 'id' => $id));
            break;

        case 'delete_calendar':
            $id = isset($_POST['id_calendrier']) ? (int) $_POST['id_calendrier'] : 0;
            $manager->deleteCalendar($id);
            echo json_encode(array('status' => 'ok'));
            break;

        // --- Événements ---
        case 'list_events':
            $evenements = $manager->getEvents();
            echo json_encode(array('status' => 'ok', 'data' => $evenements), JSON_UNESCAPED_SLASHES);
            break;

        case 'search_events':
            $motCle = isset($_GET['q']) ? trim($_GET['q']) : '';
            $resultats = $manager->searchEvents($motCle);
            echo json_encode(array('status' => 'ok', 'data' => $resultats), JSON_UNESCAPED_SLASHES);
            break;

        case 'create_event':
            $id = $manager->createEvent($_POST);
            echo json_encode(array('status' => 'ok', 'id' => $id));
            break;

        case 'update_event':
            $id = isset($_POST['id_evenement']) ? (int) $_POST['id_evenement'] : 0;
            $manager->updateEvent($id, $_POST);
            echo json_encode(array('status' => 'ok'));
            break;

        case 'delete_event':
            $id = isset($_POST['id_evenement']) ? (int) $_POST['id_evenement'] : 0;
            $manager->deleteEvent($id);
            echo json_encode(array('status' => 'ok'));
            break;

        case 'duplicate_event':
            $id = isset($_POST['id_evenement']) ? (int) $_POST['id_evenement'] : 0;
            $newId = $manager->duplicateEvent($id);
            echo json_encode(array('status' => 'ok', 'id' => $newId));
            break;

        default:
            echo json_encode(array('status' => 'error', 'message' => 'Action inconnue.'));
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>