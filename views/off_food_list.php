<?php
// views/off_food_list.php

$isAdmin = isset($_SESSION['user_role']) && strtoupper($_SESSION['user_role']) === 'ADMIN';
$currentUserId = $_SESSION['user_id'] ?? null;

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-egg-fried me-2 text-success"></i>Produits OpenFoodFacts</h2>
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addFoodModal" onclick="setupAddMode()">
        <i class="bi bi-plus-lg me-2"></i>Ajouter un aliment
    </button>
</div>

<div id="ajaxAlertContainer"></div>

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
<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="index.php?action=off_foods" 
       class="btn btn-sm <?php echo (!isset($_GET['filter_custom']) && !isset($_GET['filter_tag'])) ? 'btn-dark' : 'btn-outline-dark'; ?>">
        <i class="bi bi-grid-fill me-1"></i> Tout le catalogue
    </a>
    
    <a href="index.php?action=off_foods&filter_custom=1" 
       class="btn btn-sm <?php echo (isset($_GET['filter_custom']) && $_GET['filter_custom'] == '1') ? 'btn-info text-dark' : 'btn-outline-info'; ?>">
        <i class="bi bi-person-fill me-1"></i> Mes aliments Perso
    </a>

    <div class="btn-group">
        <button type="button" 
                class="btn btn-sm dropdown-toggle <?php echo isset($_GET['filter_tag']) ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; ?>" 
                data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-star-fill text-warning me-1"></i> 
            <?php 
                if (isset($_GET['filter_tag'])) {
                    if ($_GET['filter_tag'] === 'all') {
                        echo "Tous mes favoris";
                    } else {
                        $selectedTagName = "Favoris";
                        if (!empty($userTags)) {
                            foreach ($userTags as $tag) {
                                if ($tag['id'] == $_GET['filter_tag']) {
                                    $selectedTagName = $tag['tag_name'];
                                    break;
                                }
                            }
                        }
                        echo htmlspecialchars($selectedTagName);
                    }
                } else {
                    echo "Filtrer par Favoris";
                }
            ?>
        </button>
        <ul class="dropdown-menu shadow">
            <li>
                <a class="dropdown-item <?php echo (isset($_GET['filter_tag']) && $_GET['filter_tag'] === 'all') ? 'active' : ''; ?>" 
                   href="index.php?action=off_foods&filter_tag=all">
                    <i class="bi bi-stars me-2 text-warning"></i>Tous mes favoris
                </a>
            </li>
            <?php if (!empty($userTags)): ?>
                <li><hr class="dropdown-divider"></li>
                <?php foreach ($userTags as $tag): ?>
                        <?php if ($tag['id'] == 1) continue; // On ignore l'ID 1 ?>
                    <li>
                        <a class="dropdown-item <?php echo (isset($_GET['filter_tag']) && $_GET['filter_tag'] == $tag['id']) ? 'active' : ''; ?>" 
                           href="index.php?action=off_foods&filter_tag=<?php echo $tag['id']; ?>">
                            <i class="bi bi-bookmark-star me-2 text-muted"></i><?php echo htmlspecialchars($tag['tag_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
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
                        <th style="width: 60px;">Visuel</th>
                        <th>Nom de l'aliment</th>
                        <th>Catégorie</th>
                        <th>Portion (100g/ml)</th>
                        <th>Protéines</th>
                        <th>Glucides <small>(sucres)</small></th>
                        <th>Lipides <small>(saturés)</small></th>
                        <th>Fibres</th>
                        <th>Sel</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($foods)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Aucun aliment dans le catalogue.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($foods as $food): ?>
                            <?php 
                            // 💡 Sécurité : On a le droit de gérer si on est ADMIN OU si on est le créateur de l'aliment
                            $canManage = $isAdmin || (!empty($food['user_id']) && $food['user_id'] == $currentUserId);
                            ?>
                            <tr>
                                <td class="align-middle text-center" style="width: 60px;">
                                    <?php if (!empty($food['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($food['image_path']); ?>" 
                                            class="img-thumbnail img-zoom-click" 
                                            style="width: 45px; height: 45px; object-fit: cover; cursor: pointer;" 
                                            alt="Visuel"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#imagePreviewModal"
                                            onclick="document.getElementById('modalLargeImage').src = this.src;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-middle justify-content-center m-auto border" style="width: 45px; height: 45px;">
                                            <i class="bi bi-camera text-muted m-auto"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-dark">
                                    <?php if (!empty($food['custom_name'])): ?>
                                        <span class="fw-bold text-primary"><i class="bi bi-tag-fill me-1 small"></i><?php echo htmlspecialchars($food['custom_name']); ?></span>
                                        <br><small class="text-muted fw-normal">Nom d'origine : <?php echo htmlspecialchars($food['name']); ?></small>
                                    <?php else: ?>
                                        <span class="fw-bold"><?php echo htmlspecialchars($food['name']); ?></span>
                                    <?php endif; ?>

                                    <?php if (!empty($food['barcode'])): ?>
                                        <br><small class="text-muted fw-normal"><i class="bi bi-upc-scan me-1"></i><?php echo htmlspecialchars($food['barcode']); ?></small>
                                    <?php endif; ?>

                                    <?php if (!empty($food['user_id'])): ?>
                                        <span class="badge bg-info text-dark ms-1" title="Aliment ajouté manuellement">
                                            <i class="bi bi-person-fill"></i> Perso
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo htmlspecialchars($food['category_name'] ?? 'Non classé'); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo $food['kcal_per_100g']; ?> kcal 
                                        <small>/ 100<?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></small>
                                    </span>
                                </td>
                                
                                <td><?php echo $food['proteins_per_100g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></td>
                                
                                <td>
                                    <strong><?php echo $food['carbohydrates_per_100g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></strong>
                                    <br><small class="text-muted">dont : <?php echo $food['sugar_per_100g']; ?> g</small>
                                </td>
                                
                                <td>
                                    <strong><?php echo $food['fat_per_100g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></strong>
                                    <br><small class="text-muted">dont : <?php echo $food['saturated_fat_per_100g']; ?> g</small>
                                </td>
                                
                                <td><?php echo $food['fibers_per_100g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></td>
                                
                                <td><?php echo $food['salt_per_100g']; ?> <?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?></td>

                                <td class="align-middle text-end" style="width: 140px;">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if (!empty($food['off_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($food['off_url']); ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Voir sur Open Food Facts">
                                                <i class="bi bi-globe"></i>
                                            </a>
                                        <?php endif; ?>

                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning btn-action" 
                                                title="Gérer les favoris et étiquettes"
                                                onclick="openFavoriteManager(<?php echo $food['id']; ?>, '<?php echo htmlspecialchars($food['name'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <i class="bi <?php echo !empty($food['food_tags']) ? 'bi-star-fill text-warning' : 'bi-star'; ?>" 
                                            id="star-icon-<?php echo $food['id']; ?>" 
                                            style="font-size: 1rem;"></i>
                                        </button>

                                        <?php if ($canManage): ?>
                                            <button class="btn btn-sm btn-outline-primary" title="Modifier"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#addFoodModal"
                                                    onclick="setupEditMode(this)"
                                                    data-custom-name="<?php echo htmlspecialchars($food['custom_name'] ?? ''); ?>"
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
                                                    data-barcode="<?php echo htmlspecialchars($food['barcode'] ?? ''); ?>"
                                                    data-image="<?php echo htmlspecialchars($food['image_path'] ?? ''); ?>"
                                                    data-url="<?php echo htmlspecialchars($food['off_url'] ?? ''); ?>"
                                                    data-category="<?php echo $food['category_id'] ?? ''; ?>"
                                                    data-user-id="<?php echo $food['user_id'] ?? ''; ?>"
                                                    data-unit="<?php echo htmlspecialchars($food['food_unit'] ?? 'g'); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger btn-delete-food" 
                                                    data-id="<?= $food['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($food['name'], ENT_QUOTES, 'UTF-8') ?>"
                                                    title="Supprimer l'aliment">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
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

<div class="modal fade" id="addFoodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalTitle">Saisie Nutritionnelle</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="foodForm" action="index.php?action=off_foods" method="POST" onsubmit="document.getElementById('foodCategoryId').disabled = false;">
                <div class="modal-body">
                    <input type="hidden" name="id" id="foodId">
                    
                    <div class="mb-3 p-3 bg-light rounded border border-warning-subtle">
                        <label class="form-label fw-bold text-dark"><i class="bi bi-upc-scan me-2 text-warning"></i>Scanner ou saisir un Code-barres (Recherche Auto)</label>
                        <div class="input-group">
                            <input type="text" name="barcode" id="foodBarcode" class="form-control" placeholder="Scanner ou saisir manuellement..." oninput="checkBarcodeLength(this.value)">
                            <button class="btn btn-primary" type="button" id="btnScanCamera" onclick="startCameraScanner()" title="Scanner avec l'appareil photo">
                                <i class="bi bi-camera"></i>
                            </button>
                        </div>
                        <div id="apiLoader" class="text-primary small mt-1 d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Recherche sur Open Food Facts...
                        </div>
                        <div id="apiFeedback" class="small mt-1 d-none"></div>
                    </div>

                    <div id="foodImagePreviewContainer" class="mb-3 text-center d-none">
                        <label class="form-label d-block fw-semibold text-muted">Visuel de l'aliment</label>
                        <img id="foodImagePreview" src="" alt="Aperçu" class="img-thumbnail shadow-sm" style="max-height: 120px; max-width: 100px; object-fit: cover;">
                    </div>

                    <div id="cameraScannerArea" class="mb-3 d-none text-center bg-dark rounded p-2 position-relative">
                        <div id="interactive" class="viewport" style="width: 100%; max-height: 250px; overflow: hidden;"></div>
                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="stopCameraScanner()">Arrêter la caméra</button>
                    </div>

                    <hr class="text-muted">

                    <div class="mb-3 p-3 bg-light rounded border border-primary-subtle">
                        <label class="form-label fw-bold text-primary"><i class="bi bi-person-heart me-2"></i>Mon Surnom Perso (Désignation privée)</label>
                        <input type="text" name="custom_name" id="foodCustomName" class="form-control border-primary-subtle" placeholder="ex: Mon Fromage Blanc du Matin, Petit-déjeuner...">
                        <div class="form-text text-muted small">Ce nom n'est visible que par toi pour tes recherches rapides.</div>
                    </div>

                    <div class="row">
                        <div class="col-8 mb-3">
                            <label class="form-label fw-bold">Nom de l'aliment *</label>
                            <input type="text" name="name" id="foodName" class="form-control" required placeholder="ex: Skyr, Miel...">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label fw-bold">Unité de base *</label>
                            <select name="food_unit" id="foodUnit" class="form-select fw-bold border-dark" onchange="updateDynamicLabels(this.value)">
                                <option value="g" selected>Grammes (g)</option>
                                <option value="ml">Millilitres (ml)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Catégorie *</label>
                        <select name="category_id" id="foodCategoryId" class="form-control" required>
                            <option value="" disabled selected>-- Choisir une catégorie --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Calories (<span class="dyn-unit">kcal</span>) (pour 100<span class="lbl-unit">g</span>) *</label>
                        <input type="number" step="any" name="calories" id="foodCalories" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Glucides (pour 100<span class="lbl-unit">g</span>) *</label>
                            <input type="number" step="0.01" name="carbs" id="foodCarbs" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">dont Sucres (g) *</label>
                            <input type="number" step="0.01" name="sugars" id="foodSugars" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Lipides (pour 100<span class="lbl-unit">g</span>) *</label>
                            <input type="number" step="0.01" name="fat" id="foodFat" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">dont Saturés (g) *</label>
                            <input type="number" step="0.01" name="saturated_fat" id="foodSaturatedFat" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Protéines (pour 100<span class="lbl-unit">g</span>) *</label>
                            <input type="number" step="0.01" name="protein" id="foodProtein" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Fibres (g) *</label>
                            <input type="number" step="0.01" name="fibers" id="foodFibers" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3 col-6">
                        <label class="form-label fw-semibold">Sel (g) *</label>
                        <input type="number" step="0.01" name="salt" id="foodSalt" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lien URL de la Photo (Open Food Facts ou Web)</label>
                        <input type="text" name="image_path" id="foodImage" class="form-control" placeholder="https://images.openfoodfacts.org/...jpg">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lien de la Fiche Produit (En savoir plus)</label>
                        <input type="text" name="off_url" id="foodUrl" class="form-control" placeholder="https://fr.openfoodfacts.org/produit/...">
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" id="btnDeleteFromModal" style="display: none;" onclick="deleteCurrentFoodFromModal()">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                    <div>
                        <button type="button" id="btnReset" class="btn btn-outline-secondary" onclick="clearApiFeedback()">Vider</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" id="btnSubmit" class="btn btn-success">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg bg-white p-0 position-relative rounded"  style="border: solid 4px rgb(255, 255, 255);">
            <button type="button" 
                    class="btn-close position-absolute top-0 end-0 m-3 z-3 bg-white p-2 rounded-circle border shadow-sm" 
                    data-bs-dismiss="modal" 
                    aria-label="Close"
                    style="opacity: 0.8;"></button>
            <div class="modal-body p-0 text-center overflow-hidden rounded">
                <img src="" id="modalLargeImage" class="img-fluid d-block w-100" alt="Plein écran" style="max-height: 75vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=off_foods" method="POST">
                <div class="modal-body">
                    <p>Es-tu sûr de vouloir supprimer l'aliment <strong id="deleteFoodName"></strong> ?</p>
                    <p class="text-muted small mb-0">Cette action est irréversible.</p>
                    <input type="hidden" name="delete_id" id="deleteFoodId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="favoriteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-2.5">
                <h6 class="modal-title fw-bold"><i class="bi bi-star-fill me-2"></i>Organiser les Favoris</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <p class="small text-muted mb-2 text-truncate fw-semibold" id="favModalFoodName"></p>
                <input type="hidden" id="favModalFoodId">

                <div class="form-check form-switch p-2 bg-light rounded border mb-3">
                    <input class="form-check-input ms-0 me-2" type="checkbox" id="checkFavGeneral" onchange="toggleGeneralFavorite()">
                    <label class="form-check-label fw-bold text-dark" for="checkFavGeneral">Ajouter aux favoris</label>
                </div>

                <div id="sectionSubTags" class="d-none">
                    <label class="form-label small fw-bold text-secondary mb-1">Classer dans un sous-groupe :</label>
                    <div class="border rounded p-2 bg-white" style="max-height: 150px; overflow-y: auto;" id="favTagsListContainer">
                        <?php if (!empty($userTags)): ?>
                            <?php foreach ($userTags as $tag): ?>
                                <?php if ($tag['id'] == 1) continue; // On ignore l'ID 1 ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input fav-tag-checkbox" 
                                           type="checkbox" 
                                           value="<?php echo $tag['id']; ?>" 
                                           id="modalTag_<?php echo $tag['id']; ?>"
                                           onchange="updateFoodTagsJson()">
                                    <label class="form-check-label small" for="modalTag_<?php echo $tag['id']; ?>">
                                        <?php echo htmlspecialchars($tag['tag_name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted small text-center py-2">Aucun sous-groupe créé.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
const userIsAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
const currentUserId = <?php echo json_encode($currentUserId); ?>;

function updateDynamicLabels(unit) {
    document.querySelectorAll('.lbl-unit').forEach(span => {
        span.innerText = unit;
    });
}

function setupAddMode() {
    document.getElementById('foodForm').reset();
    document.getElementById('foodId').value = '';
    document.getElementById('modalTitle').innerText = "Nouvel Aliment (Valeurs pour 100g/ml)";
    document.getElementById('btnSubmit').innerText = "Enregistrer au catalogue";
    document.getElementById('btnSubmit').className = "btn btn-success";
    document.getElementById('btnReset').style.display = "inline-block";
    document.getElementById('btnDeleteFromModal').style.display = "none";
    document.getElementById('foodCustomName').value = '';
    
    document.getElementById('foodUnit').value = 'g';
    updateDynamicLabels('g');

    document.getElementById('foodImagePreviewContainer').classList.add('d-none');
    document.getElementById('foodImagePreview').src = '';
    clearApiFeedback();
    setFoodFieldsReadOnly(false);

    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        setTimeout(function() {
            startCameraScanner();
        }, 150);
    }
    document.getElementById('foodBarcode').removeAttribute('inputmode');
}

function setupEditMode(button) {
    document.getElementById('modalTitle').innerText = "Modifier l'aliment";
    document.getElementById('btnSubmit').innerText = "Sauvegarder les modifications";
    document.getElementById('btnSubmit').className = "btn btn-primary";
    document.getElementById('btnReset').style.display = "none";
    
    // Vérification dynamique du droit de suppression depuis le modal d'édition
    const foodOwnerId = button.getAttribute('data-user-id');
    const canManageThisFood = userIsAdmin || (foodOwnerId && foodOwnerId == currentUserId);
    document.getElementById('btnDeleteFromModal').style.display = canManageThisFood ? "inline-block" : "none";
    
    clearApiFeedback();

    document.getElementById('foodId').value = button.getAttribute('data-id');
    document.getElementById('foodName').value = button.getAttribute('data-name');
    document.getElementById('foodCalories').value = button.getAttribute('data-calories');
    document.getElementById('foodProtein').value = button.getAttribute('data-protein');
    document.getElementById('foodCarbs').value = button.getAttribute('data-carbs');
    document.getElementById('foodSugars').value = button.getAttribute('data-sugars');
    document.getElementById('foodFat').value = button.getAttribute('data-fat');
    document.getElementById('foodSaturatedFat').value = button.getAttribute('data-saturated_fat');
    document.getElementById('foodFibers').value = button.getAttribute('data-fibers');
    document.getElementById('foodSalt').value = button.getAttribute('data-salt');
    document.getElementById('foodBarcode').value = button.getAttribute('data-barcode');
    document.getElementById('foodCustomName').value = button.getAttribute('data-custom-name') || '';
    
    const unit = button.getAttribute('data-unit') || 'g';
    document.getElementById('foodUnit').value = unit;
    updateDynamicLabels(unit);
    
    const imgPath = button.getAttribute('data-image');
    document.getElementById('foodImage').value = imgPath || '';
    document.getElementById('foodUrl').value = button.getAttribute('data-url');
    document.getElementById('foodCategoryId').value = button.getAttribute('data-category');

    const previewContainer = document.getElementById('foodImagePreviewContainer');
    const previewImg = document.getElementById('foodImagePreview');
    if (imgPath && imgPath.trim() !== '') {
        previewImg.src = imgPath;
        previewContainer.classList.remove('d-none');
    } else {
        previewContainer.classList.add('d-none');
        previewImg.src = '';
    }

    document.getElementById('foodBarcode').removeAttribute('inputmode');
    
    const currentUrl = button.getAttribute('data-url') || '';
    if (currentUrl.includes('openfoodfacts.org')) {
        setFoodFieldsReadOnly(true);
    } else {
        setFoodFieldsReadOnly(false);
    }
}

function clearApiFeedback() {
    const loader = document.getElementById('apiLoader');
    const feedback = document.getElementById('apiFeedback');
    if(loader) loader.classList.add('d-none');
    if(feedback) {
        feedback.classList.add('d-none');
        feedback.className = "small mt-1 d-none";
    }
}

function checkBarcodeLength(barcode) {
    const cleanedBarcode = barcode.trim();
    if (cleanedBarcode.length === 13 || cleanedBarcode.length === 8) {
        fetchOFFData(cleanedBarcode);
    } else {
        clearApiFeedback();
        document.getElementById('foodName').value = '';
        document.getElementById('foodCalories').value = '';
        document.getElementById('foodCarbs').value = '';
        document.getElementById('foodSugars').value = '';
        document.getElementById('foodFat').value = '';
        document.getElementById('foodSaturatedFat').value = '';
        document.getElementById('foodProtein').value = '';
        document.getElementById('foodFibers').value = '';
        document.getElementById('foodSalt').value = '';
        document.getElementById('foodImage').value = '';
        document.getElementById('foodUrl').value = '';
        document.getElementById('foodCategoryId').value = '';
        document.getElementById('foodUnit').value = 'g';
        updateDynamicLabels('g');
        
        const previewContainer = document.getElementById('foodImagePreviewContainer');
        const previewImg = document.getElementById('foodImagePreview');
        if (previewContainer) previewContainer.classList.add('d-none');
        if (previewImg) previewImg.src = '';
    }
}

// ÉCOUTEUR COMPLÉMENTAIRE POUR LE CAS DE LA SAISIE MANUELLE HORS LONGUEURS FIXES (ex: Copier-coller)
document.addEventListener("DOMContentLoaded", function () {
    const barcodeInput = document.getElementById('foodBarcode');
    if(barcodeInput) {
        barcodeInput.addEventListener('change', function() {
            const val = this.value.trim();
            if(val.length > 3) {
                fetchOFFData(val);
            }
        });
    }
});

function fetchOFFData(barcode) {
    if (!barcode) return;

    const loader = document.getElementById('apiLoader');
    const feedback = document.getElementById('apiFeedback');
    
    loader.classList.remove('d-none');
    feedback.classList.add('d-none');

    const url = `https://fr.openfoodfacts.org/api/v2/product/${barcode}.json`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            loader.classList.add('d-none');
            feedback.classList.remove('d-none');

            if (data.status === 1 && data.product) {
                const product = data.product;
                const nutrients = product.nutriments || {};

                let rawName = product.product_name_fr || product.product_name || '';
                const brand = product.brands || '';

                let finalName = rawName;
                if (brand && !rawName.toLowerCase().includes(brand.toLowerCase())) {
                    finalName = brand + " - " + rawName;
                }

                document.getElementById('foodName').value = finalName;

                let detectedUnit = 'g';
                if (product.volume_unit || (product.quantity_unit && ['ml', 'cl', 'l'].includes(product.quantity_unit.toLowerCase()))) {
                    detectedUnit = 'ml';
                }
                document.getElementById('foodUnit').value = detectedUnit;
                updateDynamicLabels(detectedUnit);

                const kcal = nutrients['energy-kcal_100g'] || nutrients['energy-kcal_value'] || nutrients['energy-kcal'] || '';
                const carbs = nutrients['carbohydrates_100g'];
                const sugars = nutrients['sugars_100g'];
                const fat = nutrients['fat_100g'];
                const satFat = nutrients['saturated-fat_100g'];
                const protein = nutrients['proteins_100g'];
                const fibers = nutrients['fiber_100g'];
                const salt = nutrients['salt_100g'];

                document.getElementById('foodCalories').value = kcal ? Math.round(kcal) : '';
                const formatMacro = (val) => (val !== undefined && val !== '') ? parseFloat(val).toFixed(2) : '';

                document.getElementById('foodCarbs').value = formatMacro(carbs);
                document.getElementById('foodSugars').value = formatMacro(sugars) || '0.00';
                document.getElementById('foodFat').value = formatMacro(fat);
                document.getElementById('foodSaturatedFat').value = formatMacro(satFat) || '0.00';
                document.getElementById('foodProtein').value = formatMacro(protein);
                document.getElementById('foodFibers').value = formatMacro(fibers) || '0.00';
                document.getElementById('foodSalt').value = formatMacro(salt) || '0.00';
                
                const imgUrl = product.image_url || product.image_front_url || product.image_thumb_url || '';
                document.getElementById('foodImage').value = imgUrl;
                document.getElementById('foodUrl').value = `https://fr.openfoodfacts.org/produit/${barcode}`;

                const previewContainer = document.getElementById('foodImagePreviewContainer');
                const previewImg = document.getElementById('foodImagePreview');
                if (imgUrl && imgUrl.trim() !== '') {
                    previewImg.src = imgUrl;
                    previewContainer.classList.remove('d-none');
                } else {
                    previewContainer.classList.add('d-none');
                    previewImg.src = '';
                }

                let detectedCategoryId = "";
                const offCategories = (product.categories || "").toLowerCase();
                
                if (offCategories.includes("boisson") || offCategories.includes("beverage") || offCategories.includes("jus de") || offCategories.includes("soda") || offCategories.includes("café") || offCategories.includes("thé")) {
                    detectedCategoryId = "7"; 
                } else if (offCategories.includes("lait") || offCategories.includes("yaourt") || offCategories.includes("fromage") || offCategories.includes("skyr") || offCategories.includes("dairy") || offCategories.includes("crème fraîche")) {
                    detectedCategoryId = "2"; 
                } else if (offCategories.includes("viande") || offCategories.includes("poulet") || offCategories.includes("bœuf") || offCategories.includes("poisson") || offCategories.includes("thon") || offCategories.includes("œuf") || offCategories.includes("meat") || offCategories.includes("seafood")) {
                    detectedCategoryId = "3"; 
                } else if (offCategories.includes("huile") || offCategories.includes("beurre") || offCategories.includes("margarine") || offCategories.includes("amande") || offCategories.includes("noix") || offCategories.includes("avocat") || offCategories.includes("fats")) {
                    detectedCategoryId = "4"; 
                } else if (offCategories.includes("fruit") || offCategories.includes("légume") || offCategories.includes("tomate") || offCategories.includes("salade") || offCategories.includes("plant-based foods")) {
                    detectedCategoryId = "5"; 
                } else if (offCategories.includes("chocolat") || offCategories.includes("biscuit") || offCategories.includes("bonbon") || offCategories.includes("miel") || offCategories.includes("confiture") || offCategories.includes("sucre") || offCategories.includes("snack")) {
                    detectedCategoryId = "6"; 
                } else if (offCategories.includes("riz") || offCategories.includes("pâte") || offCategories.includes("pain") || offCategories.includes("avoine") || offCategories.includes("céréale") || offCategories.includes("lentille") || offCategories.includes("féculent") || offCategories.includes("cereals") || offCategories.includes("blé")) {
                    detectedCategoryId = "1"; 
                } else if (offCategories.includes("plat préparé") || offCategories.includes("pizza") || offCategories.includes("sandwich") || offCategories.includes("meals")) {
                    detectedCategoryId = "8"; 
                }

                if (detectedCategoryId !== "") {
                    document.getElementById('foodCategoryId').value = detectedCategoryId;
                } else {
                    document.getElementById('foodCategoryId').value = "8";
                }

                if (!kcal || carbs === undefined || fat === undefined || protein === undefined) {
                    feedback.className = "alert alert-warning mt-2 mb-0 py-2 d-block";
                    feedback.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Produit incomplet !</strong><br> Certaines macros manquent. Remplis les manuellement.
                            </div>
                        </div>
                    `;
                    setFoodFieldsReadOnly(false);
                } else {
                    feedback.className = "alert alert-success mt-2 mb-0 py-2 d-block";
                    feedback.innerHTML = "<i class='bi bi-check-circle-fill me-2'></i> Produit importé avec succès !";
                    setFoodFieldsReadOnly(true);
                }
            } else {
                feedback.className = "alert alert-danger mt-2 mb-0 py-2 d-block";
                feedback.innerHTML = "<i class='bi bi-exclamation-circle-fill me-2'></i> Produit inconnu. Saisie 100% manuelle.";
                document.getElementById('foodCategoryId').value = "8"; 
                setFoodFieldsReadOnly(false);
            }
        })
        .catch(error => {
            console.error('Erreur OFF:', error);
            loader.classList.add('d-none');
            feedback.classList.remove('d-none');
            feedback.className = "alert alert-danger mt-2 mb-0 py-2 d-block";
            feedback.innerHTML = "<i class='bi bi-wifi-off me-2'></i> Erreur réseau.";
        });
}

let html5QrCode = null;

function startCameraScanner() {
    const scannerArea = document.getElementById('cameraScannerArea');
    scannerArea.classList.remove('d-none');

    html5QrCode = new Html5Qrcode("interactive");

    const config = { 
        fps: 15,
        qrbox: { width: 250, height: 150 },
        aspectRatio: 1.777778
    };

    html5QrCode.start(
        { facingMode: "environment" }, 
        config,
        (decodedText, decodedResult) => {
            document.getElementById('foodBarcode').value = decodedText;
            if (navigator.vibrate) navigator.vibrate(100);
            stopCameraScanner();
            fetchOFFData(decodedText);
        },
        (errorMessage) => {
            console.log("Recherche scanner...");
        }
    ).catch((err) => {
        console.error("Erreur caméra : ", err);
        alert("Impossible de démarrer la caméra.");
    });
}

function stopCameraScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            document.getElementById('cameraScannerArea').classList.add('d-none');
        }).catch((err) => console.error(err));
    } else {
        document.getElementById('cameraScannerArea').classList.add('d-none');
    }
}

let deleteModalBootstrap = null;

function confirmDelete(id, name) {
    document.getElementById('deleteFoodId').value = id;
    document.getElementById('deleteFoodName').innerText = name;

    if(!deleteModalBootstrap) {
        deleteModalBootstrap = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    }
    deleteModalBootstrap.show();
}

document.addEventListener('click', function(event) {
    const button = event.target.closest('.btn-delete-food');
    if (button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        confirmDelete(id, name);
    }
});

function deleteCurrentFoodFromModal() {
    const id = document.getElementById('foodId').value; 
    const name = document.getElementById('foodName').value; 
    
    const editModalEl = document.getElementById('addFoodModal');
    const editModal = bootstrap.Modal.getInstance(editModalEl);
    if(editModal) editModal.hide();
    
    confirmDelete(id, name);
}

document.addEventListener("DOMContentLoaded", function () {
    const modalEl = document.getElementById('addFoodModal');
    modalEl.addEventListener('shown.bs.modal', function () {
        const barcodeInput = document.getElementById('foodBarcode');
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            barcodeInput.setAttribute('inputmode', 'none');
        } else {
            barcodeInput.removeAttribute('inputmode');
        }
        barcodeInput.focus();
    });

    document.getElementById('foodBarcode').addEventListener('click', function() {
        this.removeAttribute('inputmode');
        this.focus();
    });

    const inputs = document.querySelectorAll('#foodForm input[type="number"]');
    inputs.forEach(input => {
        input.addEventListener("focus", function() {
            this.type = "text";
        });

        input.addEventListener("keydown", function(e) {
            if (e.key === ',' || e.key === 'Decimal') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const val = this.value;
                this.value = val.slice(0, start) + '.' + val.slice(end);
                this.selectionStart = this.selectionEnd = start + 1;
                this.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        input.addEventListener("beforeinput", function(e) {
            if (e.data === ',') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const val = this.value;
                this.value = val.slice(0, start) + '.' + val.slice(end);
                this.selectionStart = this.selectionEnd = start + 1;
            }
        });

        input.addEventListener("blur", function() {
            let txtValue = this.value.toString().replace(',', '.');
            if (txtValue !== '') {
                let num = parseFloat(txtValue);
                if (!isNaN(num)) {
                    if (this.id === 'foodCalories') {
                        this.value = Math.round(num);
                    } else {
                        this.value = num.toFixed(2);
                    }
                }
            }
            this.type = "number";
        });
    });

    const imageInput = document.getElementById('foodImage');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const previewContainer = document.getElementById('foodImagePreviewContainer');
            const previewImg = document.getElementById('foodImagePreview');
            if (this.value && this.value.trim() !== '') {
                previewImg.src = this.value;
                previewContainer.classList.remove('d-none');
            } else {
                previewContainer.classList.add('d-none');
                previewImg.src = '';
            }
        });
    }
});

function filterFoodTable() {
    const input = document.getElementById('globalSearchInput');
    const filter = input.value.toLowerCase().trim();
    const table = document.querySelector('.table tbody');
    const rows = table.getElementsByTagName('tr');

    if (rows.length === 1 && rows[0].cells.length === 1) return;

    for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName('td')[1];
        if (nameCell) {
            const textValue = nameCell.textContent || nameCell.innerText;
            if (textValue.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}

function clearSearch() {
    const input = document.getElementById('globalSearchInput');
    input.value = '';
    filterFoodTable();
    input.focus();
}

function setFoodFieldsReadOnly(isReadOnly) {
    const fields = [
        'foodName', 'foodCategoryId', 'foodUnit', 'foodCalories', 
        'foodCarbs', 'foodSugars', 'foodFat', 'foodSaturatedFat', 
        'foodProtein', 'foodFibers', 'foodSalt', 'foodImage', 'foodUrl'
    ];
    
    fields.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input) {
            if (input.tagName === 'SELECT') {
                input.disabled = isReadOnly;
            } else {
                input.readOnly = isReadOnly;
            }

            if (isReadOnly) {
                input.classList.add('bg-light');
            } else {
                input.classList.remove('bg-light');
            }
        }
    });

    const barcodeInput = document.getElementById('foodBarcode');
    if (barcodeInput) {
        barcodeInput.readOnly = isReadOnly;
        if (isReadOnly) {
            barcodeInput.classList.add('bg-light');
        } else {
            barcodeInput.classList.remove('bg-light');
        }
    }

    const btnSubmit = document.getElementById('btnSubmit');
    if (btnSubmit) {
        btnSubmit.classList.remove('d-none');
    }
}

// =======================================================
//   GESTION DES FAVORIS ET ÉTIQUETTES (AJAX)
// =======================================================
let favoriteModalBootstrap = null;

// Ouvre le gestionnaire et pré-remplit l'état actuel des favoris pour l'aliment
function openFavoriteManager(foodId, foodName) {
    document.getElementById('favModalFoodId').value = foodId;
    document.getElementById('favModalFoodName').innerText = foodName;

    // Réinitialise l'affichage des cases du modal
    const generalSwitch = document.getElementById('checkFavGeneral');
    generalSwitch.checked = false;
    document.getElementById('sectionSubTags').classList.add('d-none');
    
    // Décoche toutes les étiquettes du modal
    document.querySelectorAll('.fav-tag-checkbox').forEach(cb => cb.checked = false);

    // Initialisation propre du modal Bootstrap s'il n'existe pas encore
    if (!favoriteModalBootstrap) {
        const modalEl = document.getElementById('favoriteModal');
        if (modalEl) {
            favoriteModalBootstrap = new bootstrap.Modal(modalEl);
        }
    }

    // Récupération du statut actuel via AJAX
    fetch(`index.php?action=off_foods&subaction=get_food_fav_status&fav_id=${foodId}`)
        .then(res => res.json())
        .then(data => {
            if (data.is_favorite) {
                generalSwitch.checked = true;
                document.getElementById('sectionSubTags').classList.remove('d-none');
                
                if (data.tags && Array.isArray(data.tags)) {
                    data.tags.forEach(tagId => {
                        const cb = document.getElementById(`modalTag_${tagId}`);
                        if (cb) cb.checked = true;
                    });
                }
            }
            if (favoriteModalBootstrap) {
                favoriteModalBootstrap.show();
            }
        })
        .catch(err => {
            console.error("Erreur lors de la récupération du statut favori:", err);
            // Sécurité : on affiche quand même le modal si le réseau rame
            if (favoriteModalBootstrap) {
                favoriteModalBootstrap.show();
            }
        });
}

// Gère l'activation / désactivation globale du favori (Interrupteur)
function toggleGeneralFavorite() {
    const foodId = document.getElementById('favModalFoodId').value;
    const isChecked = document.getElementById('checkFavGeneral').checked;
    const sectionSubTags = document.getElementById('sectionSubTags');
    const starIcon = document.getElementById(`star-icon-${foodId}`);

    // FORCE L'ALLUMAGE IMMÉDIAT VISUEL
    if (isChecked) {
        if (sectionSubTags) sectionSubTags.classList.remove('d-none');
        if (starIcon) {
            starIcon.className = "bi bi-star-fill text-warning";
        }
    } else {
        if (sectionSubTags) sectionSubTags.classList.add('d-none');
        if (starIcon) {
            starIcon.className = "bi bi-star";
        }
    }
    
    // Envoi de la sauvegarde en arrière-plan
    updateFoodTagsJson();
}

// Envoie l'état complet au serveur en tâche de fond (Sauvegarde)
function updateFoodTagsJson() {
    const foodId = document.getElementById('favModalFoodId').value;
    const isFavorite = document.getElementById('checkFavGeneral').checked;
    
    const selectedTags = [];
    if (isFavorite) {
        document.querySelectorAll('.fav-tag-checkbox:checked').forEach(cb => {
            selectedTags.push(parseInt(cb.value));
        });
    }

    const tagsParam = encodeURIComponent(JSON.stringify(selectedTags));
    const url = `index.php?action=off_foods&subaction=save_food_favorites&fav_id=${foodId}&is_favorite=${isFavorite ? '1' : '0'}&tags=${tagsParam}`;

    fetch(url)
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error("Erreur de sauvegarde des favoris:", data.message);
        } else {
            console.log("Enregistré avec succès !");
        }
    })
    .catch(err => console.error("Erreur réseau favoris:", err));
}




</script>

<style>
#cameraScannerArea {
    background-color: #1a1a1a;
    border: 2px solid #333;
    overflow: hidden;
    position: relative;
}
#interactive.viewport {
    position: relative;
    width: 100%;
    height: auto; 
    display: flex;
    justify-content: center;
    align-items: center;
}
#interactive.viewport video {
    width: 100% !important;
    height: auto !important;
    display: block;
}
#interactive div:has(video), 
#interactive [id*="html5-qrcode"] {
    border-color: transparent !important;
}
#interactive div div,
#interactive div span,
#interactive canvas {
    border: none !important;
    outline: none !important;
    display: none !important;
    opacity: 0 !important;
}
#interactive.viewport::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 250px;  
    height: 150px; 
    border: 2px solid #ffc107; 
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.4);
    z-index: 999; 
    pointer-events: none;
}
#interactive.viewport::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 180px;
    height: 2px;
    background: #d6d4d4;
    box-shadow: 0 0 8px #d6d4d4;
    opacity: 0.5;
    z-index: 1000;
    pointer-events: none;
}
</style>