<?php
// controllers/OffFoodController.php

require_once 'config/database.php';
require_once 'models/FoodModel.php';

class OffFoodController {
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

        // =========================================================================
        // ENREGISTREMENT ET RÉCUPÉRATION DES FAVORIS (INTERCEPTION STANDARD)
        // =========================================================================
        if (isset($_GET['subaction'])) {
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');

            $currentUserId = $_SESSION['user_id'] ?? null;
            $foodItemId = (int)($_REQUEST['food_id'] ?? $_GET['fav_id'] ?? 0);

            if (!$currentUserId || !$foodItemId) {
                echo json_encode(['success' => false, 'message' => 'Données manquantes ou session expirée.']);
                exit;
            }

            // 1. RÉCUPÉRATION DES STATUTS
            if ($_GET['subaction'] === 'get_food_fav_status') {
                try {
                    $stmt = $this->db->prepare("SELECT tag_id FROM user_food_tags WHERE user_id = ? AND food_item_id = ?");
                    $stmt->execute([$currentUserId, $foodItemId]);
                    $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    $isFavorite = (count($tags) > 0);

                    // ADAPTÉ : On nettoie le tableau pour le JS en enlevant le tag technique 1 (favori général)
                    $cleanTags = array_values(array_map('intval', array_filter($tags, function($v) { 
                        return $v !== null && (int)$v !== 1; 
                    })));

                    echo json_encode([
                        'is_favorite' => $isFavorite,
                        'tags' => $cleanTags
                    ]);
                } catch (PDOException $e) {
                    echo json_encode(['is_favorite' => false, 'tags' => []]);
                }
                exit;
            }

            // 2. SAUVEGARDER LES FAVORIS
            if ($_GET['subaction'] === 'save_food_favorites') {
                try {
                    $isFavorite = (int)($_REQUEST['is_favorite'] ?? 0);
                    $tagsJson = $_REQUEST['tags'] ?? '[]';
                    $incomingTags = json_decode($tagsJson, true);

                    $this->db->beginTransaction();

                    // Nettoyage complet pour cet aliment
                    $stmtDelete = $this->db->prepare("DELETE FROM user_food_tags WHERE user_id = ? AND food_item_id = ?");
                    $stmtDelete->execute([$currentUserId, $foodItemId]);

                    // Insertion si l'interrupteur est activé
                    if ($isFavorite === 1) {
                        $validTags = [];
                        if (is_array($incomingTags)) {
                            $validTags = array_filter(array_map('intval', $incomingTags));
                        }

                        if (!empty($validTags)) {
                            // CAS A : Des sous-groupes sont cochés (ex: Petit-déjeuner ID 2, etc.)
                            $stmtInsert = $this->db->prepare("INSERT INTO user_food_tags (user_id, food_item_id, tag_id) VALUES (?, ?, ?)");
                            foreach ($validTags as $tagId) {
                                $stmtInsert->execute([$currentUserId, $foodItemId, $tagId]);
                            }
                        } else {
                            // CAS B : Uniquement le curseur général -> ADAPTÉ : On insère le nouvel ID 1 !
                            $stmtInsert = $this->db->prepare("INSERT INTO user_food_tags (user_id, food_item_id, tag_id) VALUES (?, ?, 1)");
                            $stmtInsert->execute([$currentUserId, $foodItemId]);
                        }
                    }

                    $this->db->commit();
                    echo json_encode(['success' => true]);
                } catch (PDOException $e) {
                    if ($this->db->inTransaction()) $this->db->rollBack();
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
                exit;
            }
        }

        // Si on reçoit une demande de suppression classique...
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
                // Stop ! Tu n'es pas admin, tu ne passes pas.
                header("Location: index.php?action=foods&error=Action non autorisée.");
                exit();
            }

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

        // 1. On récupère le VRAI ID de l'utilisateur connecté dans la session
        $currentUserId = $_SESSION['user_id'] ?? null;

        // 2. Récupère le filtre "Mes aliments persos" s'il est activé
        $onlyCustom = isset($_GET['filter_custom']) && $_GET['filter_custom'] == '1';

        // 3. Récupère le filtre par étiquette de favoris
        $filterTagId = null;
        if (isset($_GET['filter_tag'])) {
            $filterTagId = ($_GET['filter_tag'] === 'all') ? 'all' : (int)$_GET['filter_tag'];
        }

        // 4. On passe les paramètres au modèle pour obtenir la liste filtrée
        $foods = $this->foodModel->getAll($currentUserId, $onlyCustom, $filterTagId);

        // 5. On récupère TOUTES les étiquettes de l'utilisateur pour construire le menu de filtrage dans la vue
        $userTags = $this->foodModel->getUserTags($currentUserId);


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