<?php
// views/food_list.php
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-egg-fried me-2 text-success"></i>Catalogue des Aliments</h2>
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addFoodModal" onclick="setupAddMode()">
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
                        <th>Protéines</th>
                        <th>Glucides <small>(dont sucres)</small></th>
                        <th>Lipides <small>(dont saturés)</small></th>
                        <th>Fibres</th> <th>Sel (100g)</th>
                        <th>Code-barres</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($foods)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Aucun aliment dans le catalogue pour le moment.</td>
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
                                <td><?php echo $food['fibers_per_100g']; ?> g</td> <td><?php echo $food['salt_per_100g']; ?> g</td>
                                <td class="text-muted small"><?php echo htmlspecialchars($food['barcode'] ?? '-'); ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning me-1" title="Ajouter aux favoris (Bientôt disponible)">
                                        <i class="bi bi-star"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Modifier"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#addFoodModal"
                                            onclick="setupEditMode(this)"
                                            data-id="<?php echo $food['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($food['name']); ?>"
                                            data-calories="<?php echo $food['kcal_per_100g']; ?>"
                                            data-protein="<?php echo $food['proteins_per_100g']; ?>"
                                            data-carbs="<?php echo $food['carbohydrates_per_100g']; ?>"
                                            data-sugars="<?php echo $food['sugar_per_100g']; ?>"
                                            data-fat="<?php echo $food['fat_per_100g']; ?>"
                                            data-saturated_fat="<?php echo $food['saturated_fat_per_100g']; ?>"
                                            data-fibers="<?php echo $food['fibers_per_100g']; ?>"
                                            data-salt="<?php echo $food['salt_per_100g']; ?>"
                                            data-barcode="<?php echo htmlspecialchars($food['barcode'] ?? ''); ?>">
                                        <i class="bi bi-pencil"></i>
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

<div class="modal fade" id="addFoodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalTitle">Saisie Nutritionnelle (pour 100g / 100ml)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="foodForm" action="index.php?action=foods" method="POST">
                <div class="modal-body">
                    
                    <input type="hidden" name="id" id="foodId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de l'aliment *</label>
                        <input type="text" name="name" id="foodName" class="form-control" required placeholder="ex: Skyr, Miel...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Calories (kcal) *</label>
                        <input type="number" name="calories" id="foodCalories" class="form-control" required placeholder="ex: 64">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Glucides (g) *</label>
                            <input type="number" step="0.01" name="carbs" id="foodCarbs" class="form-control" required placeholder="ex: 4.9">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">dont Sucres (g) *</label>
                            <input type="number" step="0.01" name="sugars" id="foodSugars" class="form-control" required placeholder="ex: 4.9">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Lipides (g) *</label>
                            <input type="number" step="0.01" name="fat" id="foodFat" class="form-control" required placeholder="ex: 0.3">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">dont Saturés (g) *</label>
                            <input type="number" step="0.01" name="saturated_fat" id="foodSaturatedFat" class="form-control" required placeholder="ex: 0.2">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Protéines (g) *</label>
                            <input type="number" step="0.01" name="protein" id="foodProtein" class="form-control" required placeholder="ex: 10.0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Fibres (g) *</label>
                            <input type="number" step="0.01" name="fibers" id="foodFibers" class="form-control" required placeholder="ex: 0.0" value="0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sel (g) *</label>
                        <input type="number" step="0.01" name="salt" id="foodSalt" class="form-control" required placeholder="ex: 0.11" value="0.00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Code-barres (Optionnel)</label>
                        <input type="text" name="barcode" id="foodBarcode" class="form-control" placeholder="ex: 4016223004351">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="reset" id="btnReset" class="btn btn-outline-secondary me-auto">Vider</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" id="btnSubmit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setupAddMode() {
    document.getElementById('foodForm').reset();
    document.getElementById('foodId').value = '';
    document.getElementById('modalTitle').innerText = "Nouvel Aliment (Valeurs pour 100g)";
    document.getElementById('btnSubmit').innerText = "Enregistrer au catalogue";
    document.getElementById('btnSubmit').className = "btn btn-success";
    document.getElementById('btnReset').style.display = "inline-block";
}

function setupEditMode(button) {
    document.getElementById('modalTitle').innerText = "Modifier l'aliment";
    document.getElementById('btnSubmit').innerText = "Sauvegarder les modifications";
    document.getElementById('btnSubmit').className = "btn btn-primary";
    document.getElementById('btnReset').style.display = "none";

    document.getElementById('foodId').value = button.getAttribute('data-id');
    document.getElementById('foodName').value = button.getAttribute('data-name');
    document.getElementById('foodCalories').value = button.getAttribute('data-calories');
    document.getElementById('foodProtein').value = button.getAttribute('data-protein');
    document.getElementById('foodCarbs').value = button.getAttribute('data-carbs');
    document.getElementById('foodSugars').value = button.getAttribute('data-sugars');
    document.getElementById('foodFat').value = button.getAttribute('data-fat');
    document.getElementById('foodSaturatedFat').value = button.getAttribute('data-saturated_fat');
    document.getElementById('foodFibers').value = button.getAttribute('data-fibers'); // AJOUT
    document.getElementById('foodSalt').value = button.getAttribute('data-salt');
    document.getElementById('foodBarcode').value = button.getAttribute('data-barcode');
}

function resetFoodForm() {
    setupAddMode();
}
</script>