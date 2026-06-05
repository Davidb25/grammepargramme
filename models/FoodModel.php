<?php
// models/FoodModel.php

class FoodModel {
    private $db;
    private $table = "food_items";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode = null, $image_path = null, $off_url = null) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, kcal_per_100g, proteins_per_100g, carbohydrates_per_100g, sugar_per_100g, fat_per_100g, saturated_fat_per_100g, fibers_per_100g, salt_per_100g, barcode, image_path, off_url) 
                  VALUES (:name, :calories, :protein, :carbs, :sugars, :fat, :saturated_fat, :fibers, :salt, :barcode, :image_path, :off_url)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'name' => $name,
            'calories' => $calories,
            'protein' => $protein,
            'carbs' => $carbs,
            'sugars' => $sugars,
            'fat' => $fat,
            'saturated_fat' => $saturated_fat,
            'fibers' => $fibers,
            'salt' => $salt,
            'barcode' => !empty($barcode) ? $barcode : null,
            'image_path' => !empty($image_path) ? $image_path : null,
            'off_url' => !empty($off_url) ? $off_url : null
        ]);
    }

    public function update($id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode = null, $image_path = null, $off_url = null) {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, kcal_per_100g = :calories, proteins_per_100g = :protein, 
                      carbohydrates_per_100g = :carbs, sugar_per_100g = :sugars, 
                      fat_per_100g = :fat, saturated_fat_per_100g = :saturated_fat, 
                      fibers_per_100g = :fibers, salt_per_100g = :salt, barcode = :barcode,
                      image_path = :image_path, off_url = :off_url
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'calories' => $calories,
            'protein' => $protein,
            'carbs' => $carbs,
            'sugars' => $sugars,
            'fat' => $fat,
            'saturated_fat' => $saturated_fat,
            'fibers' => $fibers,
            'salt' => $salt,
            'barcode' => !empty($barcode) ? $barcode : null,
            'image_path' => !empty($image_path) ? $image_path : null,
            'off_url' => !empty($off_url) ? $off_url : null
        ]);
    }
}