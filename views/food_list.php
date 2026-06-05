<?php
// views/food_list.php
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-egg-fried me-2 text-success"></i>Catalogue des Aliments</h2>
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addFoodModal">
        <i class="bi bi-plus-lg me-2"></i>Ajouter un aliment
    </button>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom de l'aliment</th>
                        <th>Calories (100g)</th>
                        <th>Protéines (100g)</th>
                        <th>Glucides <small class="text-muted">(dont sucres)</small></th>
                        <th>Lipides <small class="text-muted">(dont saturés)</small></th>
                        <th>Sel (100g)</th>
                        <th>Code-barres</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($foods)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Aucun aliment dans le catalogue pour le moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($foods as $food): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($food['name']); ?></td>
                                <td><span class="badge bg-primary px-2.5 py-1.5"><?php echo $food['kcal_per_100g']; ?> kcal</span></td>
                                <td><?php echo $food['proteins_per_100g']; ?> g</td>
                                <td>
                                    <strong><?php echo $food['carbohydrates_per_100g']; ?> g</strong>
                                    <br><small class="text-muted">dont sucres : <?php echo $food['sugar_per_100g']; ?> g</small>
                                </td>
                                <td>
                                    <strong><?php echo $food['fat_per_100g']; ?> g</strong>
                                    <br><small class="text-muted">dont saturés : <?php echo $food['saturated_fat_per_100g']; ?> g</small>
                                </td>
                                <td><?php echo $food['salt_per_100g']; ?> g</td>
                                <td class="text-muted small"><?php echo htmlspecialchars($food['barcode'] ?? '-'); ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning" title="Ajouter aux favoris">
                                        <i class="bi bi-star"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addFoodModal" tabindex="-1" aria-labelledby="addFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFoodModalLabel">Nouvel Aliment (Valeurs pour 100g)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=foods" method="POST">
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom de l'aliment *</label>
                        <input type="text" name="name" class="form-control" required placeholder="ex: Banane, Miel, Pain complet...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Énergie / Calories (kcal) *</label>
                        <input type="number" name="calories" class="form-control" required placeholder="ex: 89">
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Glucides (g) *</label>
                            <input type="number" step="0.01" name="carbs" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-danger">dont Sucres (g) *</label>
                            <input type="number" step="0.01" name="sugars" class="form-control" required placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Lipides (g) *</label>
                            <input type="number" step="0.01" name="fat" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-danger">dont Saturés (g) *</label>
                            <input type="number" step="0.01" name="saturated_fat" class="form-control" required placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Protéines (g) *</label>
                            <input type="number" step="0.01" name="protein" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-danger">Sel (g) *</label>
                            <input type="number" step="0.01" name="salt" class="form-control" required value="0.00">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Code-barres (Optionnel)</label>
                        <input type="text" name="barcode" class="form-control" placeholder="Saisir ou scanner si disponible">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer au catalogue</button>
                </div>
            </form>
        </div>
    </div>
</div>