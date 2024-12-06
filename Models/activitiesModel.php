<?php
require_once(__DIR__.'/connexionDB.php');
/**
 * Récupère toutes les activités de la base de données.
 *
 * @return array $result Un tableau contenant toutes les activités (numActivity, nameActivity).
 */
function getActivities() {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'SELECT numActivity, nameActivity 
                    FROM Activities';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getActivities: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les détails d'une activité spécifique.
 *
 * @param int $idActivity L'identifiant de l'activité à récupérer.
 * @return array $result Un tableau contenant les détails de l'activité
 */
function getActivitiesDetails(int $idActivity) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'SELECT numActivity, nameActivity, unitCost, unitPrice 
                    FROM Activities 
                    WHERE numActivity = :numActivity';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numActivity', $idActivity, PDO::PARAM_INT);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getActivitiesDetails: " . $e->getMessage());
        return null;
    }
}

/**
 * Crée une nouvelle activité dans la base de données.
 *
 * @param string $name Le nom de l'activité.
 * @param float $cost Le coût unitaire de l'activité.
 * @param float $price Le price unitaire de l'activité.
 */
function createActivity(string $name, float $cost, float $price) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'INSERT INTO Activities (nameActivity, unitCost, unitPrice) 
                    VALUES (:name, :cost, :price)';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':name', $name, PDO::PARAM_STR);
            $resultStatement->bindParam(':cost', $cost, PDO::PARAM_STR);
            $resultStatement->bindParam(':price', $price, PDO::PARAM_STR);
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans createActivity: " . $e->getMessage());
    }
}

/**
 * Supprime une activité de la base de données.
 *
 * @param int $idActivity L'identifiant de l'activité à supprimer.
 */
function deleteActivity(int $idActivity) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'DELETE FROM Activities 
                    WHERE numActivity = :numActivity';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numActivity', $idActivity, PDO::PARAM_INT);
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans deleteActivity: " . $e->getMessage());
    }
}

/**
 * Met à jour les informations d'une activité existante.
 *
 * @param int $idActivity L'identifiant de l'activité à mettre à jour.
 * @param string $name Le nouveau nom de l'activité.
 * @param float $cost Le nouveau coût unitaire de l'activité.
 * @param float $price Le nouveau price unitaire de l'activité.
 */
function updateActivity(int $idActivity, string $name, float $cost, float $price) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'UPDATE Activities 
                    SET nameActivity = :name, unitCost = :cost, unitPrice = :price 
                    WHERE numActivity = :numActivity';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numActivity', $idActivity, PDO::PARAM_INT);
            $resultStatement->bindParam(':name', $name, PDO::PARAM_STR);
            $resultStatement->bindParam(':cost', $cost, PDO::PARAM_STR);
            $resultStatement->bindParam(':price', $price, PDO::PARAM_STR);
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans updateActivity: " . $e->getMessage());
    }
}
?>