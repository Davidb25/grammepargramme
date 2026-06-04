<?php
// index.php

// 1. On inclut le fichier de configuration
require_once 'config/database.php';

echo "<h1>Welcome to GrammeParGramme !</h1>";

// 2. On instancie la classe Database (on crée l'objet)
$database = new Database();
$db = $database->getConnection();

// 3. Si $db n'est pas vide, c'est que la connexion a réussi !
if ($db) {
    echo "<p style='color: green; font-weight: bold;'>✔ Connexion à MySQL réussie avec succès !</p>";
    
    // Petite requête bonus pour lister tes moments de repas dans la page !
    $query = "SELECT label FROM meal_moments";
    $stmt = $db->query($query);
    $moments = $stmt->fetchAll();
    
    echo "<h3>Moments configurés en BDD :</h3><ul>";
    foreach ($moments as $moment) {
        echo "<li>" . htmlspecialchars($moment['label']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Échec de la connexion.</p>";
}