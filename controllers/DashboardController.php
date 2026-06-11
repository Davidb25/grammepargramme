<?php
// controllers/DashboardController.php


class DashboardController {
    
    private $db;
    private $profileModel;
    private $weightModel;

    public function __construct($db) {

        $this->db = $db;
        $this->profileModel = new ProfileModel($this->db);
        //$this->weightModel = new WeightHistoryModel($db); // À créer si pas encore fait
    }


public function dashboardAction() {
    $userId = $_SESSION['user_id'];
    $profile = $this->profileModel->getProfile($userId);
    
    // On calcule la variable
    $isProfileComplete = !empty($profile['poids']) && 
                         !empty($profile['taille']) && 
                         !empty($profile['date_naissance']) && 
                         !empty($profile['sexe']);

    // On inclut les vues DEPUIS le contrôleur
    require_once 'views/layout/header.php';
    require 'views/dashboard.php';
    require_once 'views/layout/footer.php';
}
}