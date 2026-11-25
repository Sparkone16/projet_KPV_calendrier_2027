<?php
/**
 * Classe de gestion des calendriers et des événements en base de données.
 */
class App
{

    /**
     * Instance de connexion PDO.
     * @var PDO
     */
    private $pdo;

    /**
     * Constructeur de la classe CalendarManager.
     *
     * @param PDO $pdo Connexion PDO active vers la base de données
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ====================
    // CALENDRIERS 
    // ====================

    /**
     * Récupère la liste de tous les calendriers.
     *
     * @return array Tableau associatif des calendriers
     */
    public function getCalendars()
    {
        $query = "SELECT * FROM G_KPV_calendrier ORDER BY id_calendrier ASC";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Crée un nouveau calendrier.
     *
     * @param string $nom Nom du calendrier
     * @param string $description Description du calendrier
     * @param string $couleur Code couleur hexadécimal (ex: #b77ee6)
     * @return int Identifiant du calendrier créé
     */
    public function createCalendar($nom, $description, $couleur)
    {
        $query = "INSERT INTO G_KPV_calendrier (nom_calendrier, description_calendrier, couleur_calendrier)
                  VALUES (:nom, :description, :couleur)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":nom", $nom, PDO::PARAM_STR);
        $statement->bindValue(":description", $description, PDO::PARAM_STR);
        $statement->bindValue(":couleur", $couleur, PDO::PARAM_STR);
        $statement->execute();
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Supprime un calendrier (et ses événements associés via ON DELETE CASCADE).
     *
     * @param int $id Identifiant du calendrier à supprimer
     * @return bool True si la suppression a réussi
     */
    public function deleteCalendar($id)
    {
        $query = "DELETE FROM G_KPV_calendrier WHERE id_calendrier = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        return $statement->execute();
    }

    // ====================
    // ÉVÉNEMENTS 
    // ====================

    /**
     * Récupère tous les événements (avec la couleur et le nom de leur calendrier).
     *
     * @return array Liste des événements
     */
    public function getEvents()
    {
        $query = "SELECT e.*, c.nom_calendrier, c.couleur_calendrier
                  FROM G_KPV_evenement e, G_KPV_calendrier c
                  WHERE e.id_calendrier = c.id_calendrier
                  ORDER BY e.date_evenement ASC, e.heure_evenement ASC";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Recherche des événements par mot-clé dans leur description.
     *
     * @param string $motCle Mot-clé à rechercher
     * @return array Liste des événements correspondants
     */
    public function searchEvents($motCle)
    {
        $query = "SELECT e.*, c.nom_calendrier, c.couleur_calendrier
                  FROM G_KPV_evenement e, G_KPV_calendrier c
                  WHERE e.id_calendrier = c.id_calendrier
                  AND e.description_evenement LIKE :motCle
                  ORDER BY e.date_evenement ASC, e.heure_evenement ASC";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":motCle", "%" . $motCle . "%", PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Ajoute un nouvel événement dans un calendrier.
     *
     * @param array $data Données de l'événement
     * @return int Identifiant de l'événement créé
     */
    public function createEvent($data)
    {
        $query = "INSERT INTO G_KPV_evenement
                  (nom_evenement, date_evenement, heure_evenement, duree_evenement,
                   journee_evenement, description_evenement, icone_evenement, id_calendrier)
                  VALUES
                  (:nom, :date, :heure, :duree, :journee, :description, :icone, :id_calendrier)";

        $journee = !empty($data['journee_evenement']) ? 1 : 0;

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":nom", $data['nom_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":date", $data['date_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":heure", $journee ? null : $data['heure_evenement'], $journee ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $statement->bindValue(":duree", $journee ? null : (int) $data['duree_evenement'], $journee ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $statement->bindValue(":journee", $journee, PDO::PARAM_INT);
        $statement->bindValue(":description", $data['description_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":icone", $data['icone_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":id_calendrier", (int) $data['id_calendrier'], PDO::PARAM_INT);
        $statement->execute();

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Modifie un événement existant.
     *
     * @param int $id Identifiant de l'événement
     * @param array $data Nouvelles données de l'événement
     * @return bool True si la mise à jour a réussi
     */
    public function updateEvent($id, $data)
    {
        $query = "UPDATE G_KPV_evenement
                  SET nom_evenement = :nom,
                      date_evenement = :date,
                      heure_evenement = :heure,
                      duree_evenement = :duree,
                      journee_evenement = :journee,
                      description_evenement = :description,
                      icone_evenement = :icone,
                      id_calendrier = :id_calendrier
                  WHERE id_evenement = :id";

        $journee = !empty($data['journee_evenement']) ? 1 : 0;

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->bindValue(":nom", $data['nom_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":date", $data['date_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":heure", $journee ? null : $data['heure_evenement'], $journee ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $statement->bindValue(":duree", $journee ? null : (int) $data['duree_evenement'], $journee ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $statement->bindValue(":journee", $journee, PDO::PARAM_INT);
        $statement->bindValue(":description", $data['description_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":icone", $data['icone_evenement'], PDO::PARAM_STR);
        $statement->bindValue(":id_calendrier", (int) $data['id_calendrier'], PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * Supprime un événement par son identifiant.
     *
     * @param int $id Identifiant de l'événement
     * @return bool True si la suppression a réussi
     */
    public function deleteEvent($id)
    {
        $query = "DELETE FROM G_KPV_evenement WHERE id_evenement = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        return $statement->execute();
    }

    /**
     * Duplique un événement existant.
     *
     * @param int $id Identifiant de l'événement à dupliquer
     * @return int|false Identifiant du nouvel événement, ou false si introuvable
     */
    public function duplicateEvent($id)
    {
        $query = "SELECT * FROM G_KPV_evenement WHERE id_evenement = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        $event = $statement->fetch();

        if (!$event) {
            return false;
        }

        $event['nom_evenement'] = $event['nom_evenement'] . " (copie)";
        return $this->createEvent($event);
    }
}
?>