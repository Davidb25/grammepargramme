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



    // public function getAll() {

    //     $query = "SELECT * FROM " . $this->table . " ORDER BY alim_nom_fr ASC " ;
    //     $stmt = $this->db->prepare($query);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);     
    // }
}