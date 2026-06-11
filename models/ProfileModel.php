<?php
// models/ProfileModel.php

class ProfileModel {
    private $db;
    private $table = "user_profiles";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Récupère le profil complet d'un utilisateur par son ID
    public function getProfile($userId) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne le tableau ou false
    }

    // Enregistre un profil
public function saveProfile($userId, $data) {
    // 1. On vérifie si un profil existe déjà pour cet utilisateur
    $currentProfile = $this->getProfile($userId);
    
    // 2. Requête pour insérer ou mettre à jour le profil (UPSERT)
    $query = "INSERT INTO user_profiles (user_id, sexe, taille, poids, date_naissance, niveau_activite, objectif_poids) 
              VALUES (:user_id, :sexe, :taille, :poids, :date_naissance, :niveau_activite, :objectif_poids)
              ON DUPLICATE KEY UPDATE 
              sexe = VALUES(sexe), 
              taille = VALUES(taille), 
              poids = VALUES(poids), 
              date_naissance = VALUES(date_naissance), 
              niveau_activite = VALUES(niveau_activite), 
              objectif_poids = VALUES(objectif_poids)";
              
    $stmt = $this->db->prepare($query);
    $success = $stmt->execute([
        'user_id' => $userId,
        'sexe' => $data['sexe'],
        'taille' => $data['taille'],
        'poids' => $data['poids'],
        'date_naissance' => $data['date_naissance'],
        'niveau_activite' => $data['niveau_activite'],
        'objectif_poids' => !empty($data['objectif_poids']) ? $data['objectif_poids'] : null
    ]);

    // 3. Logique d'initialisation de l'historique :
    // On n'ajoute dans weight_history que si le profil n'existait pas avant (c'est une création)
    if ($success && !$currentProfile) {
        $this->addInitialWeight($userId, $data['poids']);
    }

    return $success;
}

    // Méthode dédiée pour la première pesée
    private function addInitialWeight($userId, $poids) {
        $query = "INSERT INTO weight_history (user_id, poids, date_pesee) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $poids]);
    }
}