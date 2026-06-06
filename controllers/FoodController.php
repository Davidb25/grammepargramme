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

        // Si on reçoit un formulaire d'ajout ou de modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            
            // Récupération de la catégorie
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

            $calories = filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_INT);
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
                
                // ON VÉRIFIE LES DOUBLONS STRICTS (NOM OU CODE-BARRES EXISTANT)
                $isDuplicate = $this->foodModel->checkDuplicate($name, $barcode, $id);

                if ($isDuplicate) {
                    // Si doublon trouvé, on prépare le message rouge et on n'insère rien
                    $error = "Impossible d'enregistrer : un aliment avec ce nom ou ce code-barres existe déjà dans ton catalogue !";
                } else {
                    // Si tout est au vert, on procède à l'enregistrement
                    $result = false;
                    $message = "";

                    if ($id) {
                        $result = $this->foodModel->update($id, $category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url);
                        $message = "L'aliment a été modifié avec succès !";
                    } else {
                        $result = $this->foodModel->create($category_id, $name, $calories, $protein, $carbs, $sugars, $fat, $saturated_fat, $fibers, $salt, $barcode, $image_path, $off_url);
                        $message = "L'aliment \"" . htmlspecialchars($name) . "\" a été ajouté !";
                    }

                    // Vérification du statut de la requête de création/modification
                    if ($result) {
                        $success = $message;
                    } else {
                        $error = "Une erreur est survenue en base de données.";
                    }
                }
            } else {
                $error = "Veuillez remplir correctement tous les champs numériques.";
            }
        }

        // Récupération de tous les aliments pour le tableau
        $foods = $this->foodModel->getAll();

        // ===================================================================
        // <-- ETAPE 2 : RÉCUPÉRATION DES CATÉGORIES POUR LA LISTE DÉROULANTE
        // ===================================================================
        try {
            // On interroge la table categories créée en étape 1
            $stmtCat = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
            $stmtCat->execute();
            $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $categories = []; // En cas de problème, on initialise un tableau vide pour ne pas crasher
            $error = "Erreur lors du chargement des catégories : " . $e->getMessage();
        }
        // ===================================================================

        // Inclusion des vues
        require_once 'views/layout/header.php';
        require_once 'views/food_list.php';
        require_once 'views/layout/footer.php';
    }
}