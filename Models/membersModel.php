<?php
require_once(__DIR__.'/connexionDB.php');
/**
 * Récupère tous les adhérents de la base de données.
 *
 * @return array $result Un tableau contenant tous les adhérents (numMember, name).
 */
function getMembers() {
    try {
        $connexion = testConnexion();
        if ($connexion){
            $sql = 'SELECT numMember, name 
                    FROM Members';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getMembers: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les détails d'un adhérent spécifique.
 *
 * @param int $idMember L'identifiant de l'adhérent à récupérer.
 * @return array $result Un tableau contenant les détails de l'adhérent.
 */
function getDetailsMember(int $idMember) {
    try {
        $connexion = testConnexion();
        if ($connexion){
            $sql = 'SELECT numMember, name, address, postCode, city, dateBirth 
                    FROM Members 
                    WHERE numMember = :numMember';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numMember', $idMember, PDO::PARAM_INT);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getDetailsMember: " . $e->getMessage());
        return null;
    }
}

/**
 * Crée un nouvel adhérent dans la base de données.
 *
 * @param string $name Le nom de l'adhérent.
 * @param string $address L'address de l'adhérent.
 * @param string $postCode Le code postal de l'adhérent.
 * @param string $city La city de l'adhérent.
 * @param string $dateBirth La date de naissance de l'adhérent.
 */
function createMember(string $name, string $address, string $postCode, string $city, string $dateBirth) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'INSERT INTO Members (name, address, postCode, city, dateBirth) 
                    VALUES (:name, :address, :postCode, :city, :dateBirth)';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':name', $name, PDO::PARAM_STR);
            $resultStatement->bindParam(':address', $address, PDO::PARAM_STR);
            $resultStatement->bindParam(':postCode', $postCode, PDO::PARAM_STR);
            $resultStatement->bindParam(':city', $city, PDO::PARAM_STR);
            $resultStatement->bindParam(':dateBirth', $dateBirth, PDO::PARAM_STR);
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans createMember: " . $e->getMessage());
    }
}

/**
 * Supprime un adhérent de la base de données.
 *
 * @param int $idMember L'identifiant de l'adhérent à supprimer.
 */
function deleteMember(int $idMember) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'DELETE FROM Members 
                    WHERE numMember = :numMember';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numMember', $idMember, PDO::PARAM_INT);
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans deleteMember: " . $e->getMessage());
    }
}

/**
 * Met à jour les informations d'un adhérent existant.
 *
 * @param int $idMember L'identifiant de l'adhérent à mettre à jour.
 * @param string $name Le nouveau nom de l'adhérent.
 * @param string $address La nouvelle address de l'adhérent.
 * @param string $postCode Le nouveau code postal de l'adhérent.
 * @param string $city La nouvelle city de l'adhérent.
 * @param string $dateBirth La nouvelle date de naissance de l'adhérent.
 */
function updateMember(int $idMember, string $name, string $address, string $postCode, string $city, string $dateBirth) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'UPDATE Members 
                    SET name = :name, address = :address, postCode = :postCode, city = :city, dateBirth = :dateBirth 
                    WHERE numMember = :numMember';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numMember', $idMember, PDO::PARAM_INT);
            $resultStatement->bindParam(':name', $name, PDO::PARAM_STR);
            $resultStatement->bindParam(':address', $address, PDO::PARAM_STR);
            $resultStatement->bindParam(':postCode', $postCode, PDO::PARAM_STR);
            $resultStatement->bindParam(':city', $city, PDO::PARAM_STR);
            $resultStatement->bindParam(':dateBirth', $dateBirth, PDO::PARAM_STR);
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans updateMember: " . $e->getMessage());
    }
}
?>