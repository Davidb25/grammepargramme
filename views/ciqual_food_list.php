<?php
// views/ciqual_food_list.php

$isAdmin = isset($_SESSION['user_role']) && strtoupper($_SESSION['user_role']) === 'ADMIN';
$currentUserId = $_SESSION['user_id'] ?? null;

?>