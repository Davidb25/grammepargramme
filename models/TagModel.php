<?php
// models/settings/TagModel.php

class TagModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getUserTags($userId) {
        // La table est 'user_favorite_tags' et la colonne est 'tag_name'
        $stmt = $this->db->prepare("SELECT * FROM user_favorite_tags WHERE user_id = ? ORDER BY tag_name ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTag($userId, $name) {
        $stmt = $this->db->prepare("INSERT INTO user_favorite_tags (user_id, tag_name) VALUES (?, ?)");
        return $stmt->execute([$userId, $name]);
    }

    public function getTagById($tagId, $userId) {
        // On prépare la requête pour récupérer un tag spécifique
        $stmt = $this->db->prepare("SELECT * FROM user_favorite_tags WHERE id = ? AND user_id = ?");
        $stmt->execute([$tagId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTag($userId, $tagId, $newName) {
        // On met à jour le nom du tag en vérifiant que le tag appartient bien à l'utilisateur
        $stmt = $this->db->prepare("UPDATE user_favorite_tags SET tag_name = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$newName, $tagId, $userId]);
    }

    public function deleteTag($userId, $tagId) {
        // Suppression propre
        $stmt = $this->db->prepare("DELETE FROM user_food_tags WHERE tag_id = ? AND user_id = ?");
        $stmt->execute([$tagId, $userId]);

        $stmt = $this->db->prepare("DELETE FROM user_favorite_tags WHERE id = ? AND user_id = ?");
        return $stmt->execute([$tagId, $userId]);
    }
}