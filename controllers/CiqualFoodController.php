<?php
// controllers/CiqualFoodController.php

require_once 'config/database.php';
require_once 'models/CiqualFoodModel.php';

class CiqualFoodController {
    private $db;
    private $ciqualFoodModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ciqualFoodModel = new CiqualFoodModel($this->db);
    }

    public function indexAction() {
        // 1. On démarre la session si ce n'est pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Inclusion des vues
        require_once 'views/layout/header.php';
        require_once 'views/ciqual_food_list.php';
        require_once 'views/layout/footer.php';
    }

}