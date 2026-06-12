<?php
// views/ciqual_food_list.php

$isAdmin = isset($_SESSION['user_role']) && strtoupper($_SESSION['user_role']) === 'ADMIN';
$currentUserId = $_SESSION['user_id'] ?? null;

?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-egg-fried me-2 text-success"></i>Aliments Ciqual</h2>
</div>

<div class="card shadow border-0">
    <div class="mb-3">
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" 
                id="globalSearchInput" 
                class="form-control border-start-0 ps-0" 
                placeholder="Rechercher un aliment par son nom, son code-barres ou ton surnom perso..." 
                onkeyup="filterFoodTable()">
            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code de l'aliment</th>
                        <th>Nom de l'aliment</th>
                        <th>Portion (100g/ml)</th>
                        <th>Protéines</th>
                        <th>Glucides <small>(sucres)</small></th>
                        <th>Lipides <small>(saturés)</small></th>
                        <th>Fibres</th>
                        <th>Eau</th>
                        <th>Sel</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ref_ciqual)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Aucun aliment dans le catalogue.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ref_ciqual as $food): ?>
                            <tr>
                                <td><?php echo $food['alim_code']; ?></td>
                                <td style="width: 200px;"><?php echo $food['alim_nom_fr']; ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo $food['energie_kcal']; ?> kcal 
                                        <small>/ 100<?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></small>
                                    </span>
                                </td>
                                
                                <td><?php echo $food['proteines_g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></td>
                                
                                <td>
                                    <strong><?php echo $food['glucides_g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></strong>
                                    <br><small class="text-muted">dont : <?php echo $food['sucres_g']; ?> g</small>
                                </td>
                                
                                <td>
                                    <strong><?php echo $food['lipides_g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></strong>
                                    <br><small class="text-muted">dont : <?php echo $food['acides_gras_satures_g']; ?> g</small>
                                </td>
                                
                                <td><?php echo $food['fibres_g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></td>
                                <td><?php echo $food['eau_g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></td>
                                
                                <td><?php echo $food['sodium_mg']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></td>

                                <td class="align-middle text-end" style="width: 60px;">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning btn-action" 
                                                title="Gérer les favoris et étiquettes"
                                                onclick="openFavoriteManager(<?php echo $food['id']; ?>, '<?php echo htmlspecialchars($food['alim_nom_fr'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <i class="bi <?php echo !empty($food['food_tags']) ? 'bi-star-fill text-warning' : 'bi-star'; ?>" 
                                            id="star-icon-<?php echo $food['id']; ?>" 
                                            style="font-size: 1rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>