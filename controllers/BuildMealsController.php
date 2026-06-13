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

        // chqrgement des données de la table

        //$ref_ciqual = $this->ciqualFoodModel->getAll();

        // Inclusion des vues
        require_once 'views/layout/header.php';
        require_once 'views/build_meals.php';
        require_once 'views/layout/footer.php';
    }

}