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

public function create($category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url) {
        $sql = "INSERT INTO food_items (category_id, name, kcal_per_100g, proteins_per_100g, carbohydrates_per_100g, sugar_per_100g, fat_per_100g, saturated_fat_per_100g, fibers_per_100g, salt_per_100g, barcode, image_path, off_url) 
                VALUES (:category_id, :name, :calories, :protein, :carbs, :sugars, :fat, :saturated_fat, :fibers, :salt, :barcode, :image_path, :off_url)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'category_id' => $category_id,
            'name' => $name,
            'calories' => $calories,
            'protein' => $protein,
            'carbs' => $carbs,
            'sugars' => $sugars,
            'fat' => $fat,
            'saturated_fat' => $saturated_fat,
            'fibers' => $fibers,
            'salt' => $salt,
            'barcode' => $barcode,
            'image_path' => $image_path,
            'off_url' => $off_url
        ]);
    }

    public function update($id, $category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url) {
        $sql = "UPDATE food_items SET 
                    category_id = :category_id,
                    name = :name, 
                    kcal_per_100g = :calories, 
                    proteins_per_100g = :protein, 
                    carbohydrates_per_100g = :carbs, 
                    sugar_per_100g = :sugars, 
                    fat_per_100g = :fat, 
                    saturated_fat_per_100g = :saturated_fat, 
                    fibers_per_100g = :fibers, 
                    salt_per_100g = :salt, 
                    barcode = :barcode, 
                    image_path = :image_path, 
                    off_url = :off_url 
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'category_id' => $category_id,
            'name' => $name,
            'calories' => $calories,
            'protein' => $protein,
            'carbs' => $carbs,
            'sugars' => $sugars,
            'fat' => $fat,
            'saturated_fat' => $saturated_fat,
            'fibers' => $fibers,
            'salt' => $salt,
            'barcode' => $barcode,
            'image_path' => $image_path,
            'off_url' => $off_url
        ]);
    }

    /**
     * Vérifie si un aliment existe déjà par son code-barres ou son nom
     * (Exclut l'ID actuel en cas de modification d'un produit existant)
     */
    public function checkDuplicate($name, $barcode = null, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM food_items WHERE (LOWER(name) = LOWER(:name)";
        
        // Si un code-barres est fourni, on vérifie aussi s'il existe déjà
        if (!empty($barcode)) {
            $sql .= " OR barcode = :barcode";
        }
        
        $sql .= ")";
        
        // En mode modification, on ne veut pas s'auto-bloquer si on ne change pas le nom/code-barres
        if (!empty($excludeId)) {
            $sql .= " AND id != :excludeId";
        }

        $stmt = $this->db->prepare($sql);
        
        $params = ['name' => trim($name)];
        if (!empty($barcode)) {
            $params['barcode'] = trim($barcode);
        }
        if (!empty($excludeId)) {
            $params['excludeId'] = $excludeId;
        }

        $stmt->execute($params);
        return $stmt->fetchColumn() > 0; // Renvoie true si un doublon existe, false sinon
    }
}