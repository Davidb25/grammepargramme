<?php
// models/CiqualFoodModel.php

class CiqualFoodModel {
    private $db;
    private $table = "ref_ciqual";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAll() {

        $query = "SELECT * FROM " . $this->table . " ORDER BY alim_nom_fr ASC " ;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);     
    }
}