<?php
// controllers/AuthController.php




require_once 'config/database.php';
require_once 'models/UserModel.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        // On initialise la connexion à la BDD et le modèle
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new UserModel($this->db);
    }

    // Gère l'action d'inscription
    public function registerAction() {
        $error = null;
        $success = null;

        // Si le formulaire a été soumis en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';

            if ($email && strlen($password) >= 6) {
                // Appel au modèle pour insérer l'utilisateur
                $result = $this->userModel->register($email, $password);
                
                if ($result) {
                    $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                } else {
                    $error = "Cet email est déjà utilisé.";
                }
            } else {
                $error = "Veuillez fournir un email valide et un mot de passe d'au moins 6 caractères.";
            }
        }

        // On charge la vue d'inscription en lui passant les messages s'il y en a
        require_once 'views/layout/header.php';
        require_once 'views/register.php';
        require_once 'views/layout/footer.php';
    }

// Gère l'action de connexion
    public function loginAction() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';

            if ($email && $password) {
                $user = $this->userModel->login($email, $password);
                
                if ($user) {
                    // Connexion réussie : On stocke les infos dans la SESSION PHP
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = $user['role']; // Contient 'USER' ou 'ADMIN'

                    // On redirige vers le tableau de bord
                    header('Location: index.php?action=dashboard');
                    exit();
                } else {
                    $error = "Identifiants incorrects.";
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
            }
        }

        // Chargement de la vue de connexion
        require_once 'views/layout/header.php';
        require_once 'views/login.php';
        require_once 'views/layout/footer.php';
    }

    // Gère la déconnexion
    public function logoutAction() {
        session_destroy();
        header('Location: index.php?action=login');
        exit();
    }
}