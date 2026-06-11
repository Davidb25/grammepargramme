<?php
// controllers/SettingsController.php

require_once 'config/database.php';
require_once 'models/TagModel.php'; // On importe le nouveau modèle

class SettingsController {
    private $db;
    private $tagModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->tagModel = new TagModel($this->db);
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



}