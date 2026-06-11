<?php
// index.php

// 1. Activation de la session (Indispensable en ligne 1 pour que le rôle ADMIN fonctionne partout !)
session_start();

// ⚠️ ATTENTION : À COMMENTER OBLIGATOIREMENT LES 3 LIGNES DI-DESSOUS AVANT LA MISE EN LIGNE (PRODUCTION) !
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Inclusion des contrôleurs et configurations nécessaires
require_once 'controllers/AuthController.php';
require_once 'controllers/FoodController.php';
require_once 'controllers/SettingsController.php';

// 3. Récupération de l'action dans l'URL. Si vide, l'action par défaut est 'dashboard'
$action = $_GET['action'] ?? 'dashboard';

$authController = new AuthController();
$foodController = new FoodController();
$settingsController = new SettingsController();

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
        $foodController->indexAction();
        break;

    case 'dashboard':
        // Inclusion du tableau de bord temporaire
        require_once 'views/layout/header.php';
        require_once 'views/dashboard.php';
        require_once 'views/layout/footer.php';
        break;

    case 'settings':
        $settingsController->indexAction();
    break;

    default:
        // Page 404 ou redirection si l'action n'existe pas
        header('Location: index.php?action=dashboard');
        exit();
}