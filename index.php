<?php
// index.php

// Activation de la session (essentiel pour la future connexion)
session_start();

// Inclusion automatique du contrôleur
require_once 'controllers/AuthController.php';

// On récupère l'action demandée dans l'URL (ex: index.php?action=register)
// Si aucune action n'est définie, on ira par défaut sur le dashboard plus tard
$action = $_GET['action'] ?? 'register';

$authController = new AuthController();

// Aiguillage des requêtes (Le Routeur)
switch ($action) {
    case 'register':
        $authController->registerAction();
        break;
        
    default:
        // En attendant d'avoir le dashboard, on redirige vers l'inscription
        $authController->registerAction();
        break;
}