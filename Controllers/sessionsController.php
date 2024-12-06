<?php
// On appelle les modèles pour faire les requêtes.
require_once(__DIR__.'/../Models/sessionsModel.php');

/**
 * Affiche les détails d'une session
 * 
 * Cette fonction récupère et affiche les détails d'une session spécifique,
 * y compris ses participants et avis associés
 * 
 * @param int $id L'identifiant de la session à afficher
 * @return string Le contenu HTML de la page de détails de la session
 */
function getSessionDetails(int $id) {
    try {
        $result = getDetailsSessionById($id);
        $page = "";
        if ($result) {
            $date = new DateTime($result[0]["startDate"]);
                $dateAffich = $date -> format('d M');
                $heure = new DateTime($result[0]["startTime"]);
                $heureAffich = $heure -> format('H:i');
            $line = file_get_contents('./Views/detailsSession.html');
            $line = str_replace("[numSession]",$id, $line);
            $line = str_replace("[nameActivity]",$result[0]["nameActivity"], $line);
            $line = str_replace("[startDate]",$dateAffich, $line);
            $line = str_replace("[startTime]",$heureAffich, $line);
            $page = $page . $line;
        }
        $result2 = getAttendancesBySession($id);
        $list = "";
        if ($result2) {
            foreach ($result2 as $attendant) {
                $line = file_get_contents('./Views/listAttendants.html');
                $memberName = urlencode($attendant["name"]);
                $line = str_replace("[NumMember]",$attendant["name"], $line);
                $line = str_replace("[NumMemberUrl]",$memberName, $line);
                $line = str_replace("[idSession]",$id, $line);
                $line = str_replace("[score]",$attendant["score"], $line);
                $list = $list . $line;
            }
        }
        $page = $page . file_get_contents('./Views/attendances.html');
        $page = str_replace("[list]", $list, $page);
        $nbMax = count(getAttendancesNotInSession($id));
        $page = str_replace("[nbMax]", $nbMax, $page);
        return $page;
    } catch (Exception $e) {
        error_log("Erreur dans getSessionDetails: " . $e->getMessage());
        return "Une erreur est survenue lors de la récupération des détails de la session.";
    }
    
}

/**
 * Ajoute une nouvelle session
 * 
 * Cette fonction crée une nouvelle session dans la base de données. 
 * Elle redirige ensuite vers la page de détails de l'activité associée à cette session.
 */
function addSession() {
    try {
        // Validation et nettoyage des entrées
        $idActivity = isset($_POST['idActivity']) ? filter_var($_POST['idActivity'], FILTER_VALIDATE_INT) : null;
        $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
        $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : null;

        // Validation des données
        if ($idActivity === false || $idActivity === null || !$startDate || !$startTime) {
            throw new Exception('Données invalides ou manquantes');
        }
        createSession($idActivity, $startDate, $startTime);
        header('Location: index.php?action=detailsActivity&id='.$idActivity);
    } catch (Exception $e) {
        error_log("Erreur dans addSession: " . $e->getMessage());
        echo "Une erreur est survenue lors de l'ajout de la session: " . $e->getMessage();
    }
}

/**
 * Affiche le formulaire de saisie des participants et de leurs notes
 * 
 * Cette fonction récupère le nombre de lignes à afficher
 * et pour chaque ligne affiche un selecteur contenant tous les membres pas encore inscrits à la session
 * 
 * @param int $count Le nombre de lignes à afficher
 * @return string Le contenu HTML de la page du formulaire
 */
function getFormRating(int $count) {
    try {
        // On récupère le nombre de lignes à afficher
        if ($count === false || $count === null) {
            $count = 1;
        }
        if(isset($_GET["session"])) {
            // $numSession=$_GET["session"];
            $numSession = isset($_GET["session"]) ? htmlspecialchars($_GET["session"], ENT_QUOTES, 'UTF-8') : '';
        }

        if(isset($_GET["score"])) {
            // $rating=$_GET["score"];
            $rating = isset($_GET["score"]) ? filter_var($_GET["score"], FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 10))) : null;
        }

        $list="";
        $page = "";

        // On stocke le bon nombre de lignes dans la variable liste
        for ($i=0; $i < $count ; $i++) { 
            $list = $list.file_get_contents('./Views/formRating.html');
            $list = str_replace("[num]", $i+1, $list);
            if(isset($_GET["name"])) {
                // $nameRating=$_GET["nom"];
                $nameRating = isset($_GET["name"]) ? filter_var($_GET["name"], FILTER_SANITIZE_STRING) : '';
                $options = file_get_contents('./Views/optionsMember.html');
                $options = str_replace("[name]", $nameRating, $options);
                $list = str_replace("[required]", "", $list);
            } else {
                $membersNotInSession = getAttendancesNotInSession($numSession);
                $options = '<option value="">--Choisissez un adhérent--</option>';
                foreach ($membersNotInSession as $member) {
                    $options = $options . file_get_contents('./Views/optionsMember.html');
                    $options = str_replace("[name]", $member['name'], $options);
                    $list = str_replace("[required]", "required", $list);
                }
            }
            $list = str_replace("[options]", $options, $list);
            $options = "";
        }

        // On s'occupe ensuite de l'affichage du code HTML en imbriquant les morceaux dans le bon ordre
        $page = $page . file_get_contents('./Views/rating.html');
        if(isset($_GET['modif'])){
            $page = str_replace("[creaModif]", 'index.php?action=updateRating', $page);
        } else {
            $page = str_replace("[creaModif]", 'index.php?action=createRating&nb='.$i, $page);
        }
        if(isset($numSession)){
            $page = str_replace("[Session]", $numSession, $page);
            $page = str_replace("[read]", "readonly", $page);
            $nameActivity = getDetailsSessionById($numSession)[0]['nameActivity'];
            $page = str_replace("[nameActivity]", "Pour l'activité : ".$nameActivity, $page);
        } else {
            $page = str_replace("[Session]", "", $page);
            $page = str_replace("[nameActivity]", "", $page);
        }
        if(isset($modif)) {
            $page = str_replace("[set]", $modif, $page);
        }
        $page = str_replace("[list]", $list, $page);
        if(isset($nameRating)){
            $page = str_replace("[nameRating]", $nameRating, $page);
        } else {
            $page = str_replace("[nameRating]", "", $page);
        }
        if(isset($rating)){
            $page = str_replace("[score]", $rating, $page);
        } else {
            $page = str_replace("[score]", "", $page);
        }
        return $page;
    } catch (Exception $e) {
        error_log("Erreur dans getFormRating: " . $e->getMessage());
        return "Une erreur est survenue lors de la génération du formulaire.";
    }
    
}

/**
 * Ajoute un ou plusieurs participants et son/leur avis
 * 
 * Cette fonction crée des participations dans la base de données. 
 * Elle redirige ensuite vers la page de détails de la session associée.
 */
function addRating(int $nb) {
    try {
        // Vérification de l'ID de la séance
        $idSession = isset($_POST['session']) ? trim($_POST['session']) : '';
        if (!$idSession) {
            throw new Exception('ID de séance manquant');
        }

        for ($i=1; $i <= $nb ; $i++) { 
            $member = isset($_POST['member'.$i]) ? trim($_POST['member'.$i]) : '';
            $score = isset($_POST['score'.$i]) && $_POST['score'.$i] !== '' ? intval($_POST['score'.$i]) : null;
            createRating($idSession, $member, $score);
        }
        header('Location: index.php?action=detailsSession&id='.$_POST['session']);
    } catch (Exception $e) {
        error_log("Erreur dans addRating: " . $e->getMessage());
        echo "Une erreur est survenue lors de l'ajout des avis: " . $e->getMessage();
    }
}

/**
 * Met à jour l'avis d'un participant à une session et redirige vers la page de détails de la session associée.
 * 
 * @param int $id L'identifiant du membre à mettre à jour
 */
function updateRat() {
    try {
        // Récupération de l'ID de la séance
        $idSession = isset($_POST['session']) ? intval($_POST['session']) : 0;

        // Récupération du nom de l'adhérent
        $member = isset($_POST['member1']) ? trim($_POST['member1']) : '';

        // Récupération de la note, en permettant la valeur NULL si non renseignée
        $score = isset($_POST['score1']) && $_POST['score1'] !== '' ? intval($_POST['score1']) : null;

        // Mise à jour de l'avis
            updateRating($idSession, $member, $score);
        // Redirection vers la page de détails de la séance
        header('Location: index.php?action=detailsSession&id='.$idSession);
    } catch (Exception $e) {
        error_log("Erreur dans updateRat: " . $e->getMessage());
        echo "Une erreur est survenue lors de la mise à jour de l'avis: " . $e->getMessage();
    }
}
?>