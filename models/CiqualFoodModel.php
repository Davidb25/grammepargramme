<?php
// models/CiqualFoodModel.php

class CiqualFoodModel {
    private $db;
    private $table = "food_items";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }
}