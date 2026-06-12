<?php
// index.php

// 1. Activation de la session (Indispensable en ligne 1 pour que le rôle ADMIN fonctionne partout !)
session_start();

// ⚠️ ATTENTION : À COMMENTER OBLIGATOIREMENT LES 3 LIGNES DI-DESSOUS AVANT LA MISE EN LIGNE (PRODUCTION) !
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Dans index.php, avant d'instancier les contrôleurs
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection(); // Récupère ta connexion PDO

// 2. Inclusion des contrôleurs et configurations nécessaires
require_once 'controllers/DashboardController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/OffFoodController.php';
require_once 'controllers/SettingsController.php';

// 3. Récupération de l'action dans l'URL. Si vide, l'action par défaut est 'dashboard'
$action = $_GET['action'] ?? 'dashboard';


$authController = new AuthController();
$offFoodController = new OffFoodController();
$settingsController = new SettingsController();
$dashboardController = new DashboardController($db);

// 4. Système de protection des pages (Vérification de connexion)
// Si l'utilisateur n'est pas connecté ET qu'il cherche à aller ailleurs que sur login ou register -> Redirection forcée !
$public_actions = ['login', 'register'];
if (!isset($_SESSION['user_id']) && !in_array($action, $public_actions)) {
    header('Location: index.php?action=login');
    exit();
}

// 5. Aiguillage des requêtes (Le Routeur)
switch ($action) {
    case 'register':
        $authController->registerAction();
        break;

    case 'login':
        $authController->loginAction();
        break;

    case 'logout':
        $authController->logoutAction();
        break;

    case 'foods': // <-- ROUTE POUR LE CATALOGUE D'ALIMENTS
        $offFoodController->indexAction();
        break;

    case 'settings':
        $settingsController->indexAction();
        break;

    case 'manage_tags': 
        $settingsController->manageTagsAction(); // Acces page de gestion des noms de favori
        break;

    case 'add_tag':
        $settingsController->addTagAction(); // Ajout nom de groupe de favori
        break;

    case 'edit_tag':
        $settingsController->editTagAction(); // Acces formulaire édition nom de groupe de favori
        break;

    case 'update_tag':
        $settingsController->updateTagAction(); // Mettre à jour nom de groupe de favori
        break;

    case 'delete_tag': 
        $settingsController->deleteTagAction(); // effacer nom de groupe de favoris
    break;

    case 'manage_profile': 
        $settingsController->manageProfileAction(); // Acces page de gestion profil
    break;

    case 'update_profile': 
        $settingsController->updateProfileAction(); // Ajoute ou mets à jour un progile
    break;

    case 'dashboard':
        // NE FAIS PLUS LE REQUIRE ICI. 
        // Appelle la méthode du contrôleur qui va tout gérer :
        $dashboardController->dashboardAction();
        break;

    default:

        // Page 404 ou redirection si l'action n'existe pas
        header('Location: index.php?action=dashboard');
        exit();
}