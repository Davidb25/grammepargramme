<?php
// models/FoodModel.php

class FoodModel {
    private $db;
    private $table = "food_items";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

public function getAll($userId = 1) {
    // On sélectionne f.*, le nom de la catégorie (c.name),
    // ET le nom personnalisé (ufc.custom_name) renommé en custom_name
    $sql = "SELECT f.*, c.name AS category_name, ufc.custom_name 
            FROM food_items f
            LEFT JOIN categories c ON f.category_id = c.id
            LEFT JOIN user_food_customization ufc ON f.id = ufc.food_item_id AND ufc.user_id = :user_id
            ORDER BY COALESCE(ufc.custom_name, f.name) ASC"; // Trie par le surnom s'il existe, sinon par le nom d'origine
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
     * Renvoie le type de doublon trouvé, ou false si tout est OK
     */
    public function checkDuplicate($name, $barcode = null, $excludeId = null) {
        // 1. Priorité absolue : On vérifie si le code-barres est déjà utilisé par un AUTRE produit
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
                return 'barcode'; // C'est le code-barres qui est en doublon
            }
        }

        // 2. Deuxième rideau : On vérifie si le nom exact est déjà pris par un AUTRE produit
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
            return 'name'; // C'est le nom qui est en doublon
        }

        return false; // Aucun doublon, la voie est libre !
    }

public function saveCustomName($foodItemId, $customName, $userId = 1) {
    // Si le surnom est vide, on supprime la personnalisation existante
    if (empty(trim($customName))) {
        $sql = "DELETE FROM user_food_customization WHERE user_id = :user_id AND food_item_id = :food_item_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'food_item_id' => $foodItemId
        ]);
    }

    // Sinon, on insère ou on met à jour si ça existe déjà
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