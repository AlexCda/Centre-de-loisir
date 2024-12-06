<?php
// On récupère les controllers
require_once(__DIR__.'/Controllers/activitiesController.php');
require_once(__DIR__.'/Controllers/membersController.php');
require_once(__DIR__.'/Controllers/sessionsController.php');

// On récupère l'action demandée si il y en a une, sinon par défaut on affiche la liste d'activités
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = 'listActivities';
}
// On récupère la page d'index qui contient la structure le menu et l'appel au script javaScript
$page = file_get_contents('./Views/index.html');

// Selon l'action on appelle la bonne fonction et on met à jour l'affichage, si nécessaire
switch ($action) {
    case 'listActivities':
        $content = getAllActivities();
        $page = str_replace("[title]", "Activités", $page);
        $page = str_replace("[page]", $content, $page);
        break;
    case 'listMembers':
        $content = getAllMembers();
        $page = str_replace("[title]", "Adhérents", $page);
        $page = str_replace("[page]", $content, $page);
        break;
    case 'createMember':
        addMember();
        break;
    case 'createActivity':
        addActivity();
        break;
    case 'deleteActivity':
        deleteActi($_GET['id']);
        break;
    case 'deleteMember':
        deleteMemb($_GET['id']);
        break;
    case 'detailsActivity':
        $content = getActivityDetails($_GET['id']);
        $page = str_replace("[title]", getActivitiesDetails($_GET['id'])[0]['nameActivity'], $page);
        $page = str_replace("[page]", $content, $page);
        break;
    case 'detailsMember':
        $content = getMemberDetails($_GET['id']);
        $page = str_replace("[title]", "Adhérent", $page);
        $page = str_replace("[page]", $content, $page);
        break;
    case 'updateActivity':
        updateActi($_GET['id']);
        break;
    case 'updateMember':
        updateMemb($_GET['id']);
        break;
    case 'createSession':
        addSession();
        break;
    case 'detailsSession':
        $content = getSessionDetails($_GET['id']);
        $page = str_replace("[title]", "Séance", $page);
        $page = str_replace("[page]", $content, $page);
        break;
    case 'getFormRating':
        $content = getFormRating($_GET['count']);
        $page = str_replace("[title]", "Avis", $page);
        $page = str_replace("[page]", $content, $page);
        break;
    case 'createRating':
        addRating($_GET['nb']);
        break;
    case 'updateRating':
        updateRat();
        break;
}

// On affiche la page
echo $page;
?>