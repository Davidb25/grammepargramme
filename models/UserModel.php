<?php
// models/UserModel.php

class UserModel {
    private $db;
    private $table = "users";

    // Le constructeur reçoit la connexion PDO de la base de données
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Méthode pour inscrire un nouvel utilisateur
    public function register($email, $password) {
        // On vérifie d'abord si l'email existe déjà
        $checkQuery = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute(['email' => $email]);
        
        if ($checkStmt->rowCount() > 0) {
            return false; // L'email existe déjà !
        }

        // Si l'email est libre, on sécurise le mot de passe (Hachage pro)
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insertion en BDD
        $query = "INSERT INTO " . $this->table . " (email, password_hash) VALUES (:email, :password_hash)";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            'email' => $email,
            'password_hash' => $password_hash
        ]);
    }

    // Méthode pour connecter un utilisateur
    public function login($email, $password) {
        // On cherche l'utilisateur par son email
        $query = "SELECT id, password_hash FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // Vérification ultra-sécurisée du mot de passe haché
            if (password_verify($password, $user['password_hash'])) {
                return $user; // On retourne l'utilisateur (on aura besoin de son id)
            }
        }
        return false; // Mauvais email ou mot de passe
    }
}