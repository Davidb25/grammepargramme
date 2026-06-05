<?php
// controllers/FoodController.php

require_once 'config/database.php';
require_once 'models/FoodModel.php';

class FoodController {
    private $db;
    private $foodModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->foodModel = new FoodModel($this->db);
    }

    public function indexAction() {
$success = '';
$error = '';

    // Si on reçoit une demande de suppression classique
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        
        try {
            // On utilise ton modèle pour supprimer (en veillant à utiliser la table food_items)
            $database = new Database();
            $db = $database->getConnection();
            
            $stmt = $db->prepare("DELETE FROM food_items WHERE id = :id");
            $stmt->execute(['id' => $delete_id]);
            
            $success = "Aliment supprimé avec succès !";
        } catch (PDOException $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
}

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $calories = filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_INT);
            $protein = filter_input(INPUT_POST, 'protein', FILTER_VALIDATE_FLOAT);
            $carbs = filter_input(INPUT_POST, 'carbs', FILTER_VALIDATE_FLOAT);
            $sugars = filter_input(INPUT_POST, 'sugars', FILTER_VALIDATE_FLOAT);
            $fat = filter_input(INPUT_POST, 'fat', FILTER_VALIDATE_FLOAT);
            $saturated_fat = filter_input(INPUT_POST, 'saturated_fat', FILTER_VALIDATE_FLOAT);
            $fibers = filter_input(INPUT_POST, 'fibers', FILTER_VALIDATE_FLOAT);
            $salt = filter_input(INPUT_POST, 'salt', FILTER_VALIDATE_FLOAT);
            $barcode = strip_tags(trim($_POST['barcode'] ?? ''));
            $image_path = strip_tags(trim($_POST['image_path'] ?? '')); // <-- AJOUT
            $off_url = strip_tags(trim($_POST['off_url'] ?? ''));       // <-- AJOUT

            if ($name && $calories !== false && $protein !== false && $carbs !== false && $sugars !== false && $fat !== false && $saturated_fat !== false && $fibers !== false && $salt !== false) {
                
                if ($id) {
                    $result = $this->foodModel->update($id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url);
                    $message = "L'aliment a été modifié avec succès !";
                } else {
                    $result = $this->foodModel->create($name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url);
                    $message = "L'aliment \"" . htmlspecialchars($name) . "\" a été ajouté !";
                }

                if ($result) {
                    $success = $message;
                } else {
                    $error = "Une erreur est survenue en base de données.";
                }
            } else {
                $error = "Veuillez remplir correctement tous les champs numériques.";
            }
        }

        $foods = $this->foodModel->getAll();

        require_once 'views/layout/header.php';
        require_once 'views/food_list.php';
        require_once 'views/layout/footer.php';
    }
}