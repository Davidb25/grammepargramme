<?php
// controllers/ApiController.php

class ApiController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Méthode utilitaire pour envoyer du JSON proprement
    private function jsonResponse($data) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function getFoodFavStatusAction() {
        $currentUserId = $_SESSION['user_id'] ?? null;
        $foodItemId = (int)($_REQUEST['food_id'] ?? $_GET['fav_id'] ?? 0);

        if (!$currentUserId || !$foodItemId) {
            $this->jsonResponse(['is_favorite' => false, 'tags' => []]);
        }

        try {
            $stmt = $this->db->prepare("SELECT tag_id FROM user_food_tags WHERE user_id = ? AND food_item_id = ?");
            $stmt->execute([$currentUserId, $foodItemId]);
            $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $isFavorite = (count($tags) > 0);
            $cleanTags = array_values(array_map('intval', array_filter($tags, function($v) { 
                return $v !== null && (int)$v !== 1; 
            })));

            $this->jsonResponse(['is_favorite' => $isFavorite, 'tags' => $cleanTags]);
        } catch (PDOException $e) {
            $this->jsonResponse(['is_favorite' => false, 'tags' => []]);
        }
    }

    public function saveFoodFavoritesAction() {
        $currentUserId = $_SESSION['user_id'] ?? null;
        $foodItemId = (int)($_REQUEST['food_id'] ?? $_GET['fav_id'] ?? 0);

        if (!$currentUserId || !$foodItemId) {
            $this->jsonResponse(['success' => false, 'message' => 'Session ou ID invalide.']);
        }

        try {
            $isFavorite = (int)($_REQUEST['is_favorite'] ?? 0);
            $tagsJson = $_REQUEST['tags'] ?? '[]';
            $incomingTags = json_decode($tagsJson, true);

            $this->db->beginTransaction();

            $stmtDelete = $this->db->prepare("DELETE FROM user_food_tags WHERE user_id = ? AND food_item_id = ?");
            $stmtDelete->execute([$currentUserId, $foodItemId]);

            if ($isFavorite === 1) {
                $validTags = is_array($incomingTags) ? array_filter(array_map('intval', $incomingTags)) : [];
                
                $stmtInsert = $this->db->prepare("INSERT INTO user_food_tags (user_id, food_item_id, tag_id) VALUES (?, ?, ?)");
                
                if (!empty($validTags)) {
                    foreach ($validTags as $tagId) {
                        $stmtInsert->execute([$currentUserId, $foodItemId, $tagId]);
                    }
                } else {
                    $stmtInsert->execute([$currentUserId, $foodItemId, 1]);
                }
            }

            $this->db->commit();
            $this->jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}