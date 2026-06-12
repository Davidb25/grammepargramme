<?php
// ajax/delete_food.php

// On remonte d'un niveau (../) pour sortir d'ajax, puis on entre dans config/
require_once __DIR__ . '/../config/database.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        // Instanciation de ta classe Database d'après ton fichier database.php
        $database = new Database();
        $db = $database->getConnection(); // Ou la méthode que tu utilises pour récupérer la connexion PDO
        
        // Requête de suppression sécurisée
        $stmt = $db->prepare("DELETE FROM off_food_items WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Aliment supprimé avec succès.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}