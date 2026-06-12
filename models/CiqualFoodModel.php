<?php
// models/CiqualFoodModel.php

class CiqualFoodModel {
    private $db;
    private $table = "ciqual";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }
}