<?php
// controllers/SettingsController.php

require_once 'config/database.php';
require_once 'models/UserModel.php';
require_once 'models/TagModel.php';
require_once 'models/ProfileModel.php';

class SettingsController {
    private $db;
    private $tagModel;
    private $userModel;
    private $profileModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->tagModel = new TagModel($this->db);
        $this->userModel = new UserModel($this->db);
        $this->profileModel = new ProfileModel($this->db);
    }

    public function indexAction() {

        require_once 'views/layout/header.php';
        require_once 'views/settings/index.php';
        require_once 'views/layout/footer.php';
    }


    public function manageTagsAction() {

        $userId = $_SESSION['user_id'];
        $userTags = $this->tagModel->getUserTags($userId);
        
        require_once 'views/layout/header.php';
        require_once 'views/settings/manage_tags.php';
        require_once 'views/layout/footer.php';
        exit();
    }

    // Dans SettingsController.php

    public function addTagAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['tag_name'])) {
            $userId = $_SESSION['user_id'];
            $name = strip_tags(trim($_POST['tag_name']));
            $this->tagModel->createTag($userId, $name);
        }
        header('Location: index.php?action=manage_tags');
        exit();
    }

    public function deleteTagAction() {
        $tagId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($tagId) {
            $userId = $_SESSION['user_id'];
            $this->tagModel->deleteTag($userId, $tagId);
        }
        header('Location: index.php?action=manage_tags');
        exit();
    }

    // Afficher le formulaire d'édition
    public function editTagAction() {
        $tagId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $userId = $_SESSION['user_id'];
        
        // On récupère le tag pour pré-remplir le champ
        $tag = $this->tagModel->getTagById($tagId, $userId);
        
        require_once 'views/layout/header.php';
        require_once 'views/settings/edit_tag.php';
        require_once 'views/layout/footer.php';
    }

    // Traiter la mise à jour
    public function updateTagAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['tag_name'])) {
            $userId = $_SESSION['user_id'];
            $tagId = (int)$_POST['id'];
            $newName = strip_tags(trim($_POST['tag_name']));
            
            $this->tagModel->updateTag($userId, $tagId, $newName);
        }
        header('Location: index.php?action=manage_tags');
        exit();
    }


    public function manageProfileAction() {

        $userId = $_SESSION['user_id'];

        $user = $this->userModel->getUser($userId);
        $profile = $this->profileModel->getProfile($userId); // On récupère les données morpho (table user_profiles)

        require_once 'views/layout/header.php';
        require_once 'views/settings/profile.php';
        require_once 'views/layout/footer.php';
        exit();
    }

    public function updateProfileAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $userId = $_SESSION['user_id'];

            // 1. Mise à jour du pseudo (table users)
            $this->userModel->updatePseudo($userId, $_POST['pseudo']);

            // 2. Mise à jour du profil (table user_profiles)
            $this->profileModel->saveProfile($userId, $_POST);

            // 3. (Bonus) Si c'est la toute première fois, on ajoute aussi dans weight_history
            // Pour être sûr, tu pourrais vérifier si c'est un nouvel enregistrement ici
            
            header('Location: index.php?action=manage_profile&success=1');
            exit();
        }
    }
}