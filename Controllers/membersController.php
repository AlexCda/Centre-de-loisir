<?php
// On appelle les modèles pour faire les requêtes. 
require_once(__DIR__.'/../Models/membersModel.php');
require_once(__DIR__.'/../Models/activitiesModel.php');
require_once(__DIR__.'/../Models/sessionsModel.php');

/**
 * Récupère et affiche tous les membres
 * 
 * Cette fonction récupère tous les membres, génère le HTML pour les afficher,
 * et prépare le formulaire d'ajout d'un nouveau membre.
 * 
 * @return string Le contenu HTML de la page des membres
 */
function getAllMembers() {
    try {
        $result = getMembers();
        $list = "";
        $page = "";
    
        // On créé les lignes de la liste d'adhérents en fonction des résultats de la requête et en remplaçant les placeholders de la vue concernées
        if ($result) {
            foreach ($result as $member) {
                $line1 = file_get_contents('./Views/listMembers.html');
                $line1 = str_replace("[NumMember]",$member["numMember"], $line1);
                $line1 = str_replace("[NameMember]",$member["name"], $line1);
                $list = $list . $line1;
            }
        }
    
        // On créé le formulaire avec la bonne action et le bon mot clé
        $form = file_get_contents('./Views/formMembers.html');
        $form = str_replace("[actionMember]", "index.php?action=createMember", $form);
        $form = str_replace("[addChange]", "Ajouter", $form);
        $form = str_replace("[name]", "", $form);
        $form = str_replace("[dateBirth]", "", $form);
        $form = str_replace("[address]", "", $form);
        $form = str_replace("[postCode]", "", $form);
        $form = str_replace("[city]", "", $form);
    
        // On s'occupe ensuite de l'affichage du code HTML en imbriquant les morceaux dans le bon ordre
        $page = $page . file_get_contents('./Views/members.html');
        $page = str_replace("[list]", $list, $page);
        $page = str_replace("[form]", $form, $page);
        return $page;
    } catch (Exception $e) {
        error_log("Erreur dans getAllMembers: " . $e->getMessage());
        return "Une erreur est survenue lors de la récupération des membres.";
    }
    
}

/**
 * Ajoute un nouveau membre
 * 
 * Cette fonction crée un nouveau membre dans la base de données. 
 * Elle redirige ensuite vers la liste des membres.
 */
function addMember() {
    try {
        // Validation et nettoyage des entrées
        $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : null;
        $address = isset($_POST['address']) ? filter_var($_POST['address'], FILTER_SANITIZE_STRING) : null;
        $postCode = isset($_POST['postCode']) ? filter_var($_POST['postCode'], FILTER_SANITIZE_NUMBER_INT) : null;
        $city = isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_STRING) : null;
        $dateBirth = isset($_POST['dateBirth']) ? $_POST['dateBirth'] : null;

        // Validation supplémentaire
        if (!$name || !$address || !$postCode || !$city || !$dateBirth) {
            throw new Exception('Tous les champs sont obligatoires');
        }
        // création du membre
        createMember($name, $address, $postCode, $city, $dateBirth);
        header('Location: index.php?action=listMembers');
    } catch (Exception $e) {
        error_log("Erreur dans addMember: " . $e->getMessage());
        echo "Une erreur est survenue lors de l'ajout du membre: " . $e->getMessage();
    }
}

/**
 * Supprime un membre
 * 
 * Cette fonction supprime le membre spécifié de la base de données
 * et redirige vers la liste des membres.
 * 
 * @param int $id L'identifiant du membre à supprimer
 */
function deleteMemb(int $id) {
    try {
        if ($id <= 0) {
            throw new Exception('ID de membre invalide');
        }
        deleteMember($id);
        header('Location: index.php?action=listMembers');
    } catch (Exception $e) {
        error_log("Erreur dans deleteMemb: " . $e->getMessage());
        echo "Une erreur est survenue lors de la suppression du membre: " . $e->getMessage();
    }
}

/**
 * Affiche les détails d'un membre
 * 
 * Cette fonction récupère et affiche les détails d'un membre spécifique,
 * y compris ses sessions associées et un formulaire de modification.
 * 
 * @param int $id L'identifiant du membre à afficher
 * @return string Le contenu HTML de la page de détails du membre
 */
function getMemberDetails(int $id) {
    try {
        // On initialise les variables
        $detailsMember = "";
        $form = "";
        $list = "";
        $page = "";

        // On stocke les résultats des méthodes nécessaires en utilisant l'id
        $result = getDetailsMember($id);
        $result2 = getSessionsByMember($id);

        // Si la méthode a bien retourné un résultat on récupère l'affichage des détails et on le modifie avec les détails du résultat
        if ($result) {
            $detailsMember = file_get_contents('./Views/detailsMember.html');
            $detailsMember = str_replace("[name]",$result[0]["name"], $detailsMember);
            $detailsMember = str_replace("[age]",date('Y') - intval($result[0]["dateBirth"]), $detailsMember);
            $detailsMember = str_replace("[address]",$result[0]["address"], $detailsMember);
            $detailsMember = str_replace("[postCode]",$result[0]["postCode"], $detailsMember);
            $detailsMember = str_replace("[city]",$result[0]["city"], $detailsMember);
        }

        // On fait de même pour le formulaire de modification
        if ($result) {
            $form = file_get_contents('./Views/formMembers.html');
            $form = str_replace("[actionMember]", "index.php?action=updateMember&id=".$result[0]["numMember"], $form);
            $form = str_replace("[addChange]", "Modifier", $form);
            $form = str_replace("[name]",$result[0]["name"], $form);
            $form = str_replace("[dateBirth]",$result[0]["dateBirth"], $form);
            $form = str_replace("[address]",$result[0]["address"], $form);
            $form = str_replace("[postCode]",$result[0]["postCode"], $form);
            $form = str_replace("[city]",$result[0]["city"], $form);
        }

        // Si la méthode a bien retourné un résultat on construit la liste avec les détails du résultat
        if ($result2) {
            foreach ($result2 as $memberSession) {
                $date = new DateTime($memberSession["startDate"]);
                $formattedDate = $date -> format('d M');
                $time = new DateTime($memberSession["startTime"]);
                $formattedTime = $time -> format('H:i');
                $listElement = file_get_contents('./Views/listSessions.html');
                $listElement = str_replace("[NumSession]", str_pad($memberSession["numSession"], 2, "0", STR_PAD_LEFT), $listElement);
                $listElement = str_replace("[DescripSession]",$memberSession["nameActivity"]." S". str_pad($memberSession["numSession"], 2, "0", STR_PAD_LEFT), $listElement);
                $listElement = str_replace("[Date]",$formattedDate, $listElement);
                $listElement = str_replace("[Heure]",$formattedTime, $listElement);
                $listElement = str_replace("[visible]","", $listElement);
                $listElement = str_replace("[modi]","modif=vrai&session=".$memberSession["numSession"]."&name=".$result[0]["name"]."&score=".$memberSession["score"], $listElement);
                $list = $list . $listElement;
            }
        }

        // On s'occupe ensuite de l'affichage du code HTML en imbriquant les morceaux dans le bon ordre
        $page = $page . $detailsMember;
        $page = str_replace("[form]", $form, $page);
        $page = $page . file_get_contents('./Views/sessions.html');
        $page = str_replace("[list]", $list, $page);
        $page = str_replace("[formSession]", "", $page);
        return $page;
    } catch (Exception $e) {
        error_log("Erreur dans getMemberDetails: " . $e->getMessage());
        return "Une erreur est survenue lors de la récupération des détails du membre.";
    }
    
}

/**
 * Met à jour un membre
 * 
 * Cette fonction met à jour les informations d'un membre existant
 * dans la base de données et redirige vers la page de détails du membre.
 * 
 * @param int $id L'identifiant du membre à mettre à jour
 */
function updateMemb(int $id) {
    try {
        // Validation et nettoyage des entrées
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $postCode = filter_input(INPUT_POST, 'postCode', FILTER_VALIDATE_INT);
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
        $dateBirth = filter_input(INPUT_POST, 'dateBirth', FILTER_SANITIZE_STRING);
        if (!$name || !$address || $postCode === false || !$city || !$dateBirth) {
            throw new Exception('Données invalides');
        }
        // mise à jour du membre
        updateMember($id, $_POST['name'], $_POST['address'], intval($_POST['postCode']), $_POST['city'], date($_POST['dateBirth']));
        header('Location: index.php?action=detailsMember&id='.$id);
    } catch (Exception $e) {
        error_log("Erreur dans updateMemb: " . $e->getMessage());
        echo "Une erreur est survenue lors de la mise à jour du membre: " . $e->getMessage();
    }
}
?>