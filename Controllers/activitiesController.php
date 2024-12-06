<?php
// On appelle les modèles pour faire les requêtes.
require_once(__DIR__.'/../Models/activitiesModel.php');
require_once(__DIR__.'/../Models/sessionsModel.php');

/**
 * Récupère et affiche toutes les activités
 * 
 * Cette fonction récupère toutes les activités, génère le HTML pour les afficher,
 * et prépare le formulaire d'ajout d'une nouvelle activité.
 * 
 * @return string Le contenu HTML de la page des activités
 */
function getAllActivities() {
    try {
        $resultActivities = getActivities();
        $list = "";
        $page = "";
        // On affiche les lignes de la liste d'activités en fonction des résultats de la requête et en remplaçant les placeholders de la vue concernées
        if ($resultActivities) {
            foreach ($resultActivities as $activity) {
                $line = file_get_contents('./Views/listActivities.html');
                $line = str_replace("[NumActivity]",$activity["numActivity"], $line);
                $line = str_replace("[NameActivity]",$activity["nameActivity"], $line);
                $list = $list . $line;
            }
        }
        // On récupère le formulaire pour l'ajout d'une activité en remplaçant les placeholders
        $form = file_get_contents('./Views/formActivities.html');
        $form = str_replace("[actionActivity]", "index.php?action=createActivity", $form);
        $form = str_replace("[addChange]", "Ajouter", $form);
        $form = str_replace("[name]", "", $form);
        $form = str_replace("[cost]", "", $form);
        $form = str_replace("[price]", "", $form);
    
        // On s'occupe ensuite de l'affichage du code HTML en imbriquant les morceaux dans le bon ordre
        $page = $page . file_get_contents('./Views/activities.html');
        $page = str_replace("[list]", $list, $page);
        $page = str_replace("[form]", $form, $page);
        return $page;
    } catch (Exception $e) {
        error_log("Erreur dans getAllActivities: " . $e->getMessage());
        return "Une erreur est survenue lors de la récupération des activités.";
    }
}

/**
 * Ajoute une nouvelle activité
 * 
 * Cette fonction crée une nouvelle activité dans la base de données. 
 * Elle redirige ensuite vers la liste des activités.
 */
function addActivity() {
    try {
        // Validation et nettoyage des entrées
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cost = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_FLOAT);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        // Vérification des données
        if ($name === null || $cost === false || $price === false || $cost < 0 || $price < 0) {
            throw new Exception('Données invalides');
        }
        // Création de l'activité
        createActivity($name, $cost, $price);
        header('Location: index.php?action=listActivities');
    } catch (Exception $e) {
        error_log("Erreur dans addActivity: " . $e->getMessage());
        echo "Une erreur est survenue lors de l'ajout de l'activité.";
    }
}

/**
 * Supprime une activité
 * 
 * Cette fonction supprime l'activité spécifiée de la base de données
 * et redirige vers la liste des activités.
 * 
 * @param int $id L'identifiant de l'activité à supprimer
 */
function deleteActi(int $id) {
    try {
        if ($id <= 0) {
            throw new Exception('ID d\'activité invalide');
        }
        deleteActivity($id);
        header('Location: index.php?action=listActivities');
    } catch (Exception $e) {
        error_log("Erreur dans deleteActi: " . $e->getMessage());
        echo "Une erreur est survenue lors de la suppression de l'activité.";
    }
}

/**
 * Affiche les détails d'une activité
 * 
 * Cette fonction récupère et affiche les détails d'une activité spécifique,
 * y compris ses sessions associées et un formulaire de modification.
 * 
 * @param int $id L'identifiant de l'activité à afficher
 * @return string Le contenu HTML de la page de détails de l'activité
 */
function getActivityDetails(int $id) {
    try {
        // On initialise les variables
        $list = "";
        $activityDetails = "";
        $form = "";
        $page = "";

        // On stocke les résultats des méthodes nécessaires en utilisant l'id
        $resultDetails = getActivitiesDetails($id);
        $resultSessions = getSessionsByActivity($id);

        // Si la méthode a bien retourné un résultat on récupère l'affichage des détails et on le modifie avec les détails du résultat
        if ($resultDetails) {
            $activityDetails = file_get_contents('./Views/detailsActivity.html');
            $activityDetails = str_replace("[id]",$resultDetails[0]["numActivity"], $activityDetails);
            $activityDetails = str_replace("[nameActivity]",$resultDetails[0]["nameActivity"], $activityDetails);
            $activityDetails = str_replace("[cost]",$resultDetails[0]["unitCost"], $activityDetails);
            $activityDetails = str_replace("[price]",$resultDetails[0]["unitPrice"], $activityDetails); 
        }

        // On fait de même pour le formulaire de modification
        if ($resultDetails) {
            $form = file_get_contents('./Views/formActivities.html');
            $form = str_replace("[actionActivity]", "index.php?action=updateActivity&id=".$resultDetails[0]["numActivity"], $form);
            $form = str_replace("[addChange]", "Modifier", $form);
            $form = str_replace("[name]", $resultDetails[0]["nameActivity"], $form);
            $form = str_replace("[cost]", $resultDetails[0]["unitCost"], $form);
            $form = str_replace("[price]", $resultDetails[0]["unitPrice"], $form);
        }

        // Si la méthode a bien retourné un résultat on construit la liste avec les détails du résultat
        if ($resultSessions) {
            foreach ($resultSessions as $activitySession) {
                $date = new DateTime($activitySession["startDate"]);
                $formattedDate = $date -> format('d M');
                $time = new DateTime($activitySession["startTime"]);
                $formattedTime = $time -> format('H:i');
                $listElement = file_get_contents('./Views/listSessions.html');
                $listElement = str_replace("[NumSession]", str_pad($activitySession["numSession"], 2, "0", STR_PAD_LEFT), $listElement);
                $listElement = str_replace("[DescripSession]","Séance n°".str_pad($activitySession["numSession"], 2, "0", STR_PAD_LEFT), $listElement);
                $listElement = str_replace("[Date]",$formattedDate, $listElement);
                $listElement = str_replace("[Heure]",$formattedTime, $listElement);
                $listElement = str_replace("[visible]","d-none", $listElement);
                $listElement = str_replace("[modi]","", $listElement);
                $list = $list . $listElement;
            }
        }

        // On s'occupe ensuite de l'affichage du code HTML en imbriquant les morceaux dans le bon ordre
        $page = $page . $activityDetails;
        $page = str_replace("[form]", $form, $page);
        $page = $page . file_get_contents('./Views/sessions.html');
        $page = str_replace("[list]", $list, $page);
        $page = str_replace("[formSession]", file_get_contents('./Views/formSession.html'), $page);
        $page = str_replace("[idActivity]", $id, $page);
return $page;
    } catch (Exception $e) {
        error_log("Erreur dans getActivityDetails: " . $e->getMessage());
        return "Une erreur est survenue lors de la récupération des détails de l'activité.";
    }
}

/**
 * Met à jour une activité
 * 
 * Cette fonction met à jour les informations d'une activité existante
 * dans la base de données et redirige vers la page de détails de l'activité.
 * 
 * @param int $id L'identifiant de l'activité à mettre à jour
 */
function updateActi(int $id) {
    try {
        // Validation et nettoyage des entrées
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cost = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_FLOAT);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        // Vérification des données
        if ($name === null || $cost === false || $price === false || $cost < 0 || $price < 0) {
            throw new Exception('Données invalides');
        }
        // Mise à jour de l'activité
        updateActivity($id, $name, $cost, $price);
        header('Location: index.php?action=detailsActivity&id='.$id);
    } catch (Exception $e) {
        error_log("Erreur dans updateActi: " . $e->getMessage());
        echo "Une erreur est survenue lors de la mise à jour de l'activité.";
    }
}
?>