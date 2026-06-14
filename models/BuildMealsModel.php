<?php
// models/BuildMealsModel.php

class BuildMealsModel {
    private $db;
    private $table = "ref_ciqual";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }


    public function getAllCategories() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En loguant l'erreur, tu gardes une trace sans interrompre l'appli
            error_log("Erreur SQL : " . $e->getMessage());
            return [];
        }
    }

    public function getAllOffProducts() {
        
        $sql = "SELECT f.*, c.name AS category_name FROM off_food_items f
                INNER JOIN categories c ON f.category_id = c.id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}