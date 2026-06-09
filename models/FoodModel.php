<?php
// models/FoodModel.php

class FoodModel {
    private $db;
    private $table = "food_items";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Récupère les aliments visibles par l'utilisateur
     * @param int $userId ID de l'utilisateur connecté
     * @param bool $onlyCustom Si true, filtre UNIQUEMENT sur les aliments créés par cet utilisateur
     */
    public function getAll($userId = 1, $onlyCustom = false) {
        // Condition de base : soit l'aliment est global (NULL), soit il appartient à l'utilisateur connecté
        $whereClause = "(f.user_id IS NULL OR f.user_id = :user_id)";
        
        // Si le filtre global demande "uniquement mes aliments personnels"
        if ($onlyCustom) {
            $whereClause = "f.user_id = :user_id";
        }

        $sql = "SELECT f.*, c.name AS category_name, ufc.custom_name 
                FROM food_items f
                LEFT JOIN categories c ON f.category_id = c.id
                LEFT JOIN user_food_customization ufc ON f.id = ufc.food_item_id AND ufc.user_id = :user_id
                WHERE $whereClause
                ORDER BY COALESCE(ufc.custom_name, f.name) ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        if (!$id) return false;
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url, $food_unit = 'g') {
        // NOUVELLE LOGIQUE :
        // Si l'aliment provient d'Open Food Facts (off_url présent), il devient global/public (null).
        // Sinon (saisie 100% manuelle), on lui affecte l'ID de l'utilisateur connecté pour en faire un aliment "Perso".
        if (!empty($off_url)) {
            $userIdToInsert = null;
        } else {
            $userIdToInsert = $_SESSION['user_id'] ?? null;
        }

        $sql = "INSERT INTO food_items (category_id, name, kcal_per_100g, proteins_per_100g, carbohydrates_per_100g, sugar_per_100g, fat_per_100g, saturated_fat_per_100g, fibers_per_100g, salt_per_100g, barcode, image_path, off_url, food_unit, user_id) 
                VALUES (:category_id, :name, :calories, :protein, :carbs, :sugars, :fat, :saturated_fat, :fibers, :salt, :barcode, :image_path, :off_url, :food_unit, :user_id)";
        
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
            'barcode' => !empty($barcode) ? $barcode : null,
            'image_path' => !empty($image_path) ? $image_path : null,
            'off_url' => !empty($off_url) ? $off_url : null,
            'food_unit' => $food_unit,
            'user_id' => $userIdToInsert
        ]);
    }

    public function update($id, $category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url, $food_unit = 'g') {
        // Pour l'UPDATE, on supprime la modification du 'user_id' afin de ne pas écraser 
        // le créateur d'origine de l'aliment si un admin passe modifier une valeur.
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
                    off_url = :off_url,
                    food_unit = :food_unit
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
            'barcode' => !empty($barcode) ? $barcode : null,
            'image_path' => !empty($image_path) ? $image_path : null,
            'off_url' => !empty($off_url) ? $off_url : null,
            'food_unit' => $food_unit
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function checkDuplicate($name, $barcode = null, $excludeId = null) {
        if (!empty($barcode)) {
            $sql = "SELECT COUNT(*) FROM food_items WHERE barcode = :barcode";
            if (!empty($excludeId)) {
                $sql .= " AND id != :excludeId";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = ['barcode' => trim($barcode)];
            if (!empty($excludeId)) {
                $params['excludeId'] = $excludeId;
            }
            
            $stmt->execute($params);
            if ($stmt->fetchColumn() > 0) {
                return 'barcode';
            }
        }

        $sql = "SELECT COUNT(*) FROM food_items WHERE LOWER(name) = LOWER(:name)";
        if (!empty($excludeId)) {
            $sql .= " AND id != :excludeId";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['name' => trim($name)];
        if (!empty($excludeId)) {
            $params['excludeId'] = $excludeId;
        }

        $stmt->execute($params);
        if ($stmt->fetchColumn() > 0) {
            return 'name';
        }

        return false;
    }

    public function saveCustomName($foodItemId, $customName, $userId = 1) {
        if (empty(trim($customName))) {
            $sql = "DELETE FROM user_food_customization WHERE user_id = :user_id AND food_item_id = :food_item_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'food_item_id' => $foodItemId
            ]);
        }

        $sql = "INSERT INTO user_food_customization (user_id, food_item_id, custom_name) 
                VALUES (:user_id, :food_item_id, :custom_name)
                ON DUPLICATE KEY UPDATE custom_name = :custom_name_update";
                
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'food_item_id' => $foodItemId,
                'custom_name' => htmlspecialchars(trim($customName)),
                'custom_name_update' => htmlspecialchars(trim($customName))
            ]);
    }
}