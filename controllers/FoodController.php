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
        // 1. On démarre la session si ce n'est pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. On récupère les messages stockés en session (mémoire flash)
        $success = $_SESSION['flash_success'] ?? '';
        $error = $_SESSION['flash_error'] ?? '';

        // 3. CRUCIAL : On vide tout de suite la session pour que le message 
        // ne se réaffiche pas indéfiniment si on change de page ensuite !
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Si on reçoit une demande de suppression classique...
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $delete_id = intval($_POST['delete_id']);
            
            try {
                $stmt = $this->db->prepare("DELETE FROM food_items WHERE id = :id");
                $stmt->execute(['id' => $delete_id]);
                
                $_SESSION['flash_success'] = "Aliment supprimé avec succès !";
                header('Location: index.php?action=foods');
                exit();
                
            } catch (PDOException $e) {
                $error = "Erreur lors de la suppression : " . $e->getMessage();
            }
        }

        // Si on reçoit un formulaire d'ajout ou de modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            
            // Récupération du surnom personnalisé
            $custom_name = strip_tags(trim($_POST['custom_name'] ?? ''));
            
            // INTERCEPTION DE L'UNITÉ ('g' ou 'ml')
            $food_unit = strip_tags(trim($_POST['food_unit'] ?? 'g'));
            if (!in_array($food_unit, ['g', 'ml'])) {
                $food_unit = 'g';
            }
            
            // Récupération de la catégorie
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

            $calories_raw = filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_FLOAT);
            $calories = ($calories_raw !== false && $calories_raw !== null) ? round($calories_raw) : false;
            $protein = filter_input(INPUT_POST, 'protein', FILTER_VALIDATE_FLOAT);
            $carbs = filter_input(INPUT_POST, 'carbs', FILTER_VALIDATE_FLOAT);
            $sugars = filter_input(INPUT_POST, 'sugars', FILTER_VALIDATE_FLOAT);
            $fat = filter_input(INPUT_POST, 'fat', FILTER_VALIDATE_FLOAT);
            $saturated_fat = filter_input(INPUT_POST, 'saturated_fat', FILTER_VALIDATE_FLOAT);
            $fibers = filter_input(INPUT_POST, 'fibers', FILTER_VALIDATE_FLOAT);
            $salt = filter_input(INPUT_POST, 'salt', FILTER_VALIDATE_FLOAT);
            $barcode = strip_tags(trim($_POST['barcode'] ?? ''));
            $image_path = strip_tags(trim($_POST['image_path'] ?? '')); 
            $off_url = strip_tags(trim($_POST['off_url'] ?? ''));       

            if ($name && $calories !== false && $protein !== false && $carbs !== false && $sugars !== false && $fat !== false && $saturated_fat !== false && $fibers !== false && $salt !== false) {
                
                // ON COMMANDE LA VÉRIFICATION SPÉCIFIQUE AU MODÈLE
                $duplicateType = $this->foodModel->checkDuplicate($name, $barcode, $id);

                if ($duplicateType !== false) {
                    if ($duplicateType === 'barcode') {
                        $_SESSION['flash_error'] = "Impossible d'enregistrer : le code-barres <strong class='text-dark'>[" . htmlspecialchars($barcode) . "]</strong> est déjà attribué à un autre aliment dans ton catalogue !";
                    } else if ($duplicateType === 'name') {
                        $_SESSION['flash_error'] = "Impossible d'enregistrer : un aliment nommé <strong class='text-dark'>\"" . htmlspecialchars($name) . "\"</strong> existe déjà dans ton catalogue !";
                    }
                    
                    header('Location: index.php?action=foods');
                    exit();
                } else {
                    $result = false;
                    $message = "";

                    if ($id) {
                        // Transmission de $food_unit à la méthode update
                        $result = $this->foodModel->update($id, $category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url, $food_unit);
                        $message = "L'aliment a été modifié avec succès !";
                        $foodItemId = $id;
                    } else {

echo "<pre>";
print_r($_POST); // Pour voir ce que le formulaire envoie vraiment
echo "</pre>";
//die(); // Arrête le script pour lire le résultat



                        // Transmission de $food_unit à la méthode create
                        $result = $this->foodModel->create($category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url, $food_unit);
                        $message = "L'aliment \"" . htmlspecialchars($name) . "\" a été ajouté !";
                        $foodItemId = $this->db->lastInsertId(); 
                    }

                    if ($result) {
                        $this->foodModel->saveCustomName($foodItemId, $custom_name);
                        $_SESSION['flash_success'] = $message;
                        header('Location: index.php?action=foods');
                        exit();
                    } else {
                        $_SESSION['flash_error'] = "Une erreur est survenue en base de données.";
                        header('Location: index.php?action=foods');
                        exit();
                    }
                }
            } else {
                $error = "Veuillez remplir correctement tous les champs numériques.";
            }
        }

        // Récupération de tous les aliments pour le tableau
        $foods = $this->foodModel->getAll(1);

        // RÉCUPÉRATION DES CATÉGORIES POUR LA LISTE DÉROULANTE
        try {
            $stmtCat = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
            $stmtCat->execute();
            $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $categories = [];
            $error = "Erreur lors du chargement des catégories : " . $e->getMessage();
        }

        // Inclusion des vues
        require_once 'views/layout/header.php';
        require_once 'views/food_list.php';
        require_once 'views/layout/footer.php';
    }
}