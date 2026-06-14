<?php
// controllers/BuildMealsController.php

require_once 'config/database.php';
require_once 'models/BuildMealsModel.php';

class BuildMealsController {
    private $db;
    private $buildMealsModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->buildMealsModel = new BuildMealsModel($this->db);
    }

    public function indexAction() {
        // 1. On démarre la session si ce n'est pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Chargement des catégories de produits
        $categories = $this->buildMealsModel->getAllCategories();

        // Inclusion des vues
        require_once 'views/layout/header.php';
        require_once 'views/build_meals.php';
        require_once 'views/layout/footer.php';
    }


public function loadCatalogOffAction() {
    // 1. Appel au modèle pour récupérer les données (tu réutilises ta requête existante !)
    $products = $this->buildMealsModel->getAllOffProducts(); // Assure-toi que cette méthode existe

    // 2. On indique au navigateur qu'on envoie du JSON
    header('Content-Type: application/json');

    // 3. On transforme le tableau PHP en texte JSON et on arrête le script
    echo json_encode($products);
    exit; // Très important pour ne pas afficher le reste de la page
}




}