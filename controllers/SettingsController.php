<?php
// controllers/SettingsController.php

require_once 'config/database.php';


class SettingsController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

    }

    public function indexAction() {

        // Inclusion des vues
        require_once 'views/layout/header.php';
        require_once 'views/settings.php';
        require_once 'views/layout/footer.php';
    }
}