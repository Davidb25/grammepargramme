<?php
// views/dashboard.php
// Ce fichier est inclus dans ton indexAction() après le header
?>

<div class="container py-4">

    <!-- HEADER DU DASHBOARD : Logo + Titre -->
    <div class="d-flex flex-column align-items-center mb-4 pb-3 border-bottom">
        <!-- TON LOGO : Remplace la source ci-dessous par ton lien final -->
        <img src="assets/images/logo.png" 
             alt="Logo Gramme par Gramme" 
             class="img-fluid me-3" 
             style="max-height: 180px; width: auto; object-fit: contain;">
             
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Gramme par Gramme</h1>
            <p class="text-muted mb-0 small text-uppercase tracking-wider fw-semibold">Tableau de bord Nutrition</p>
        </div>
    </div>

    <!-- BLOC TRANSPARENCE : Alerte info Open Food Facts -->
    <div class="alert alert-dismissible fade show border-0 bg-light shadow-sm p-4 mb-4" role="alert">
        <div class="d-flex align-items-start">
            <div class="bg-success-subtle text-success p-3 rounded-circle me-3 d-none d-sm-block">
                <i class="bi bi-info-circle-fill fs-4"></i>
            </div>
            <div class="pe-4">
                <h5 class="fw-bold text-dark mb-2">Transparence & Précision de vos données</h5>
                <p class="text-muted mb-2" style="font-size: 0.95rem; line-height: 1.5;">
                    Notre catalogue d'aliments est connecté directement à la base de données mondiale et collaborative <strong>Open Food Facts</strong> (le Wikipédia de l'alimentation).
                </p>
                <p class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.5;">
                    <i class="bi bi-lightbulb text-warning me-1"></i> <strong>Le saviez-vous ?</strong> 
                    Pour certains produits liquides (comme les crèmes, soupes ou laits), l'API internationale standardise parfois l'affichage en <strong>grammes (g)</strong> plutôt qu'en <strong>millilitres (ml)</strong>. 
                    L'écart sur le calcul final de vos calories reste dérisoire et n'impacte en rien le suivi de vos objectifs !
                </p>
            </div>
        </div>
        <!-- Bouton pour fermer la note informative -->
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 1.25rem; right: 1.25rem;"></button>
    </div>

    <!-- RESTE DE TON DASHBOARD (Tu peux mettre tes graphiques, repas ou résumés ici) -->
    <div class="row">
        <div class="col-12">
            <!-- Tes composants actuels du dashboard -->
        </div>
    </div>

</div>