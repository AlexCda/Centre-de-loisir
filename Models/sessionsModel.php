<?php
require_once(__DIR__.'/connexionDB.php');
/**
 * Crée une nouvelle séance dans la base de données.
 *
 * @param int $idActivity L'identifiant de l'activité associée à la séance.
 * @param string $startDate La date de début de la séance.
 * @param string $startTime L'heure de début de la séance.
 */
function createSession(int $idActivity, string $startDate, string $startTime) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'INSERT INTO Sessions (numActivity, startDate, startTime) 
                    VALUES (:idActivity, :startDate, :startTime)';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':idActivity', $idActivity, PDO::PARAM_INT);
            $resultStatement->bindParam(':startDate', $startDate, PDO::PARAM_STR);
            $resultStatement->bindParam(':startTime', $startTime, PDO::PARAM_STR);
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans createSession: " . $e->getMessage());
    }
}

/**
 * Crée un nouvel avis (participation) dans la base de données.
 *
 * @param int $numSession Le numéro de la séance.
 * @param string $numMember Le nom de l'adhérent (utilisé pour trouver son numéro).
 * @param int $score La score d'appréciation donnée par l'adhérent.
 */
function createRating(int $numSession, string $numMember, ?int $score) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'INSERT INTO Attendances (numSession, numMember, score) 
                    VALUES (:numSession, (SELECT numMember FROM Members WHERE name = :numMember), :score)';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numSession', $numSession, PDO::PARAM_INT);
            $resultStatement->bindParam(':numMember', $numMember, PDO::PARAM_STR);
            if ($score === null) {
                $resultStatement->bindValue(':score', null, PDO::PARAM_NULL);
            } else {
                $resultStatement->bindParam(':score', $score, PDO::PARAM_INT);
            }
            $resultStatement->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur dans createRating: " . $e->getMessage());
    }
}

/**
 * Met à jour un avis (participation) existant dans la base de données.
 *
 * @param int $numSession Le numéro de la séance.
 * @param string $numMember Le nom de l'adhérent (utilisé pour trouver son numéro).
 * @param int $score La nouvelle score d'appréciation.
 */
function updateRating(int $numSession, string $numMember, ?int $score) {
    try {
        $connexion = testConnexion();
        if ($connexion) {
            $sql = 'UPDATE Attendances  
                    SET score = :score 
                    WHERE numSession = :numSession AND numMember = (SELECT numMember 
                                                                    FROM Members 
                                                                    WHERE name = :numMember)';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':numSession', $numSession, PDO::PARAM_INT);
            $resultStatement->bindParam(':numMember', $numMember, PDO::PARAM_STR);
            if ($score === null) {
                $resultStatement->bindValue(':score', null, PDO::PARAM_NULL);
            } else {
                $resultStatement->bindParam(':score', $score, PDO::PARAM_INT);
            }
            $resultStatement->execute();
        } 
    } catch (PDOException $e) {
        error_log("Erreur dans updateRating: " . $e->getMessage());
    }
}

/**
 * Récupère les détails d'une séance spécifique.
 *
 * @param int $idSession L'identifiant de la séance.
 * @return array Les détails de la séance.
 */
function getDetailsSessionById(int $idSession) {
    try {
        $connexion = testConnexion();
        if ($connexion){
            $sql = 'SELECT nameActivity, startDate, startTime 
                    FROM Sessions 
                        INNER JOIN Activities 
                                ON Sessions.numActivity=Activities.numActivity 
                    WHERE numSession = :idSession';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':idSession', $idSession, PDO::PARAM_INT);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getDetailsSessionById: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les participations pour une séance spécifique.
 *
 * @param int $idSession L'identifiant de la séance.
 * @return array Les participations à la séance.
 */
function getAttendancesBySession(int $idSession) {
    try {
        $connexion = testConnexion();
        if ($connexion){
            $sql = 'SELECT name, score 
                    FROM Attendances 
                        INNER JOIN Members 
                                ON Attendances.numMember=Members.numMember
                    WHERE numSession = :idSession';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':idSession', $idSession, PDO::PARAM_INT);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getAttendancesBySession: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les adhérents qui ne participent pas à une séance spécifique.
 *
 * @param int $idSession L'identifiant de la séance.
 * @return array Les adhérents ne participant pas à la séance.
 */
function getAttendancesNotInSession(int $idSession) {
    try {
        $connexion = testConnexion();
        if ($connexion){
            $sql = 'SELECT name
                    FROM Members
                    WHERE name NOT IN (  SELECT name 
                                        FROM Attendances 
                                            INNER JOIN Members 
                                                    ON Attendances.numMember=Members.numMember
                                        WHERE numSession = :idSession)';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':idSession', $idSession, PDO::PARAM_INT);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getAttendancesNotInSession: " . $e->getMessage());
        return null;
    } 
}

/**
 * Récupère toutes les séances pour une activité spécifique.
 *
 * @param int $idActivity L'identifiant de l'activité.
 * @return array Les séances de l'activité.
 */
function getSessionsByActivity(int $idActivity) {
    try {
        $connexion = testConnexion();
        if ($connexion){
            $sql = 'SELECT numSession, startDate, startTime 
                    FROM Sessions 
                    WHERE numActivity = :idActivity';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':idActivity', $idActivity, PDO::PARAM_INT);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getSessionsByActivity: " . $e->getMessage());
        return null;
    } 
}

/**
 * Récupère toutes les séances auxquelles un adhérent participe.
 *
 * @param int $idMember L'identifiant de l'adhérent.
 * @return array Les séances auxquelles l'adhérent participe.
 */
function getSessionsByMember(int $idMember) {
    try {
        $connexion = testConnexion();
        if ($connexion){
            $sql = 'SELECT Sessions.numSession, startDate, startTime, Sessions.numActivity, nameActivity, score 
                    FROM Sessions 
                        INNER JOIN Attendances 
                                ON Sessions.numSession=Attendances.numSession 
                        INNER JOIN Activities 
                            ON Sessions.numActivity=Activities.numActivity 
                    WHERE numMember = :idMember';
            $resultStatement = $connexion->prepare($sql);
            $resultStatement->bindParam(':idMember', $idMember, PDO::PARAM_INT);
            $resultStatement->execute();
            $result = $resultStatement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getSessionsByMember: " . $e->getMessage());
        return null;
    }
}
?>