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

    // Affiche le catalogue et gère l'ajout d'un aliment
    public function indexAction() {
        $error = null;
        $success = null;

        // Si le formulaire d'ajout en POST a été validé
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $calories = filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_INT);
            $protein = filter_input(INPUT_POST, 'protein', FILTER_VALIDATE_FLOAT);
            $carbs = filter_input(INPUT_POST, 'carbs', FILTER_VALIDATE_FLOAT);
            $sugars = filter_input(INPUT_POST, 'sugars', FILTER_VALIDATE_FLOAT);
            $fat = filter_input(INPUT_POST, 'fat', FILTER_VALIDATE_FLOAT);
            $saturated_fat = filter_input(INPUT_POST, 'saturated_fat', FILTER_VALIDATE_FLOAT);
            $salt = filter_input(INPUT_POST, 'salt', FILTER_VALIDATE_FLOAT);
            $barcode = strip_tags(trim($_POST['barcode'] ?? ''));

            // On vérifie que tous les champs obligatoires sont bien remplis et valides
            if ($name && $calories !== false && $protein !== false && $carbs !== false && $sugars !== false && $fat !== false && $saturated_fat !== false && $salt !== false) {
                
                $result = $this->foodModel->create($name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $salt, $barcode);
                
                if ($result) {
                    $success = "L'aliment \"" . htmlspecialchars($name) . "\" a été ajouté avec succès !";
                } else {
                    $error = "Une erreur est survenue lors de l'enregistrement en base de données.";
                }
            } else {
                $error = "Veuillez remplir correctement tous les champs. Les valeurs nutritionnelles doivent être des nombres.";
            }
        }

        // On récupère la liste fraîche pour l'affichage
        $foods = $this->foodModel->getAll();

        require_once 'views/layout/header.php';
        require_once 'views/food_list.php';
        require_once 'views/layout/footer.php';
    }
}