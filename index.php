<?php
// index.php

// 1. Activation de la session (indispensable pour connecter un utilisateur)
session_start();

// 2. Inclusion du contrôleur d'authentification
require_once 'controllers/AuthController.php';

// 3. Récupération de l'action dans l'URL. Si vide, l'action par défaut est 'dashboard'
$action = $_GET['action'] ?? 'dashboard';

$authController = new AuthController();

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

    case 'dashboard':
        // Pour l'instant, on inclut le fichier dashboard.php temporaire
        require_once 'views/layout/header.php';
        require_once 'views/dashboard.php';
        require_once 'views/layout/footer.php';
        break;

    default:
        // Si l'action est inconnue, retour au login ou dashboard selon statut
        header('Location: index.php');
        exit();
}