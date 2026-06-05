<?php
// models/FoodModel.php

class FoodModel {
    private $db;
    private $table = "food_items";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Récupérer tous les aliments du catalogue par ordre alphabétique
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Ajouter un nouvel aliment complet dans le catalogue
    public function create($name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $salt, $barcode = null) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, kcal_per_100g, proteins_per_100g, carbohydrates_per_100g, sugar_per_100g, fat_per_100g, saturated_fat_per_100g, salt_per_100g, barcode) 
                  VALUES (:name, :calories, :protein, :carbs, :sugars, :fat, :saturated_fat, :salt, :barcode)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'name' => $name,
            'calories' => $calories,
            'protein' => $protein,
            'carbs' => $carbs,
            'sugars' => $sugars,
            'fat' => $fat,
            'saturated_fat' => $saturated_fat,
            'salt' => $salt,
            'barcode' => !empty($barcode) ? $barcode : null
        ]);
    }
}