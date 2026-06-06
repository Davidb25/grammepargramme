<?php
// views/food_list.php
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-egg-fried me-2 text-success"></i>Catalogue des Aliments</h2>
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

<div class="card shadow border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Visuel</th>
                        <th>Nom de l'aliment</th>
                        <th>Calories (100g)</th>
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
                            <td colspan="9" class="text-center py-4 text-muted">Aucun aliment dans le catalogue.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($foods as $food): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($food['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($food['image_path']); ?>" alt="Aliment" class="img-thumbnail" style="width: 45px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded border" style="width: 45px; height: 45px;">
                                            <i class="bi bi-camera small"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="fw-bold text-dark">
                                    <?php echo htmlspecialchars($food['name']); ?>
                                    <?php if (!empty($food['barcode'])): ?>
                                        <br><small class="text-muted fw-normal"><i class="bi bi-upc-scan me-1"></i><?php echo htmlspecialchars($food['barcode']); ?></small>
                                    <?php endif; ?>
                                </td>
                                
                                <td><span class="badge bg-primary"><?php echo $food['kcal_per_100g']; ?> kcal</span></td>
                                <td><?php echo $food['proteins_per_100g']; ?> g</td>
                                <td>
                                    <strong><?php echo $food['carbohydrates_per_100g']; ?> g</strong>
                                    <br><small class="text-muted">dont : <?php echo $food['sugar_per_100g']; ?> g</small>
                                </td>
                                <td>
                                    <strong><?php echo $food['fat_per_100g']; ?> g</strong>
                                    <br><small class="text-muted">dont : <?php echo $food['saturated_fat_per_100g']; ?> g</small>
                                </td>
                                <td><?php echo $food['fibers_per_100g']; ?> g</td>
                                <td><?php echo $food['salt_per_100g']; ?> g</td>
                                
                                <td class="text-end">
                                    <?php if (!empty($food['off_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($food['off_url']); ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Voir sur Open Food Facts">
                                            <i class="bi bi-globe"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-warning me-1" title="Favoris">
                                        <i class="bi bi-star"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary me-1" title="Modifier"
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
                                            data-barcode="<?php echo htmlspecialchars($food['barcode'] ?? ''); ?>"
                                            data-image="<?php echo htmlspecialchars($food['image_path'] ?? ''); ?>"
                                            data-url="<?php echo htmlspecialchars($food['off_url'] ?? ''); ?>"
                                            data-category="<?php echo $food['category_id'] ?? ''; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $food['id'] ?>, '<?= htmlspecialchars($food['name'], ENT_QUOTES) ?>')" title="Supprimer l'aliment">
                                        <i class="bi bi-trash"></i>
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
                <h5 class="modal-title" id="modalTitle">Saisie Nutritionnelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="foodForm" action="index.php?action=foods" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="foodId">
                    
                    <div class="mb-3 p-3 bg-light rounded border border-warning-subtle">
                        <label class="form-label fw-bold text-dark"><i class="bi bi-upc-scan me-2 text-warning"></i>Scanner ou saisir un Code-barres</label>
                        <div class="input-group">
                            <input type="text" name="barcode" id="foodBarcode" class="form-control" placeholder="Scanner en premier pour pré-remplir" oninput="checkBarcodeLength(this.value)">
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
                        <img id="foodImagePreview" src="" alt="Aperçu" class="img-thumbnail shadow-sm" style="max-height: 120px; object-fit: cover;">
                    </div>

                    <div id="cameraScannerArea" class="mb-3 d-none text-center bg-dark rounded p-2 position-relative">
                        <div id="interactive" class="viewport" style="width: 100%; max-height: 250px; overflow: hidden;"></div>
                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="stopCameraScanner()">Arrêter la caméra</button>
                    </div>

                    <hr class="text-muted">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de l'aliment *</label>
                        <input type="text" name="name" id="foodName" class="form-control" required placeholder="ex: Skyr, Miel...">
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
                        <label class="form-label fw-bold">Calories (kcal) *</label>
                        <input type="number" step="any" name="calories" id="foodCalories" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Glucides (g) *</label>
                            <input type="number" step="0.01" name="carbs" id="foodCarbs" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">dont Sucres (g) *</label>
                            <input type="number" step="0.01" name="sugars" id="foodSugars" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Lipides (g) *</label>
                            <input type="number" step="0.01" name="fat" id="foodFat" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">dont Saturés (g) *</label>
                            <input type="number" step="0.01" name="saturated_fat" id="foodSaturatedFat" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Protéines (g) *</label>
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
                        <label class="form-label fw-semibold text-primary">Lien URL de la Photo (Open Food Facts ou Web)</label>
                        <input type="text" name="image_path" id="foodImage" class="form-control" placeholder="https://images.openfoodfacts.org/...jpg">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-info">Lien de la Fiche Produit (En savoir plus)</label>
                        <input type="text" name="off_url" id="foodUrl" class="form-control" placeholder="https://fr.openfoodfacts.org/produit/...">
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" id="btnDeleteFromModal" onclick="deleteCurrentFoodFromModal()">
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

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=foods" method="POST">
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

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
// --- LOGIQUE DU FORMULAIRE (AJOUT / MODIFICATION) ---
function setupAddMode() {
    document.getElementById('foodForm').reset();
    document.getElementById('foodId').value = '';
    document.getElementById('modalTitle').innerText = "Nouvel Aliment (Valeurs pour 100g)";
    document.getElementById('btnSubmit').innerText = "Enregistrer au catalogue";
    document.getElementById('btnSubmit').className = "btn btn-success";
    document.getElementById('btnReset').style.display = "inline-block";
    document.getElementById('btnDeleteFromModal').style.display = "none";
    
    // On cache l'aperçu de l'image en mode ajout pur
    document.getElementById('foodImagePreviewContainer').classList.add('d-none');
    document.getElementById('foodImagePreview').src = '';
    clearApiFeedback();

    // --- DETECTION MOBILE POUR OUVERTURE AUTO DE LA CAMERA ---
    // On vérifie si l'appareil est un smartphone/tablette (iOS ou Android)
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // Si c'est un mobile, on attend un tout petit instant (150ms) que la modale 
        // finisse de s'ouvrir graphiquement, puis on lance l'appareil photo
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
    document.getElementById('btnDeleteFromModal').style.display = "inline-block";
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
    
    const imgPath = button.getAttribute('data-image');
    document.getElementById('foodImage').value = imgPath || '';
    document.getElementById('foodUrl').value = button.getAttribute('data-url');
    document.getElementById('foodCategoryId').value = button.getAttribute('data-category');

    // Gestion de l'affichage de l'image en édition
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
}

// --- LOGIQUE D'INTERROGATION OPEN FOOD FACTS ---
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
    
    // Si le code-barres fait la bonne taille (8 ou 13 caractères), on lance la recherche API
    if (cleanedBarcode.length === 13 || cleanedBarcode.length === 8) {
        fetchOFFData(cleanedBarcode);
    } 
    // Sinon, dès que l'utilisateur modifie le code (ex: il supprime un chiffre), on nettoie tout
    else {
        clearApiFeedback(); // Efface les messages de succès / erreur
        
        // On réinitialise les champs liés aux macros et au produit, sans toucher au code-barres lui-même
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
        document.getElementById('foodCategoryId').value = ''; // Remet la catégorie par défaut
        
        // NOUVEAU : On vide et on masque proprement le visuel de l'image
        const previewContainer = document.getElementById('foodImagePreviewContainer');
        const previewImg = document.getElementById('foodImagePreview');
        if (previewContainer) previewContainer.classList.add('d-none');
        if (previewImg) previewImg.src = '';
    }
}

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

                const name = product.product_name_fr || product.product_name || '';
                const kcal = nutrients['energy-kcal_100g'] || nutrients['energy-kcal_value'] || '';
                const carbs = nutrients['carbohydrates_100g'];
                const sugars = nutrients['sugars_100g'];
                const fat = nutrients['fat_100g'];
                const satFat = nutrients['saturated-fat_100g'];
                const protein = nutrients['proteins_100g'];
                const fibers = nutrients['fiber_100g'];
                const salt = nutrients['salt_100g'];

                document.getElementById('foodName').value = name;
                document.getElementById('foodCalories').value = kcal ? Math.round(kcal) : '';

                const formatMacro = (val) => (val !== undefined && val !== '') ? parseFloat(val).toFixed(2) : '';

                document.getElementById('foodCarbs').value = formatMacro(carbs);
                document.getElementById('foodSugars').value = formatMacro(sugars) || '0.00';
                document.getElementById('foodFat').value = formatMacro(fat);
                document.getElementById('foodSaturatedFat').value = formatMacro(satFat) || '0.00';
                document.getElementById('foodProtein').value = formatMacro(protein);
                document.getElementById('foodFibers').value = formatMacro(fibers) || '0.00';
                document.getElementById('foodSalt').value = formatMacro(salt) || '0.00';
                
                // Extraction de la photo
                const imgUrl = product.image_url || product.image_front_url || product.image_thumb_url || '';
                document.getElementById('foodImage').value = imgUrl;
                document.getElementById('foodUrl').value = `https://fr.openfoodfacts.org/produit/${barcode}`;

                // Affichage en direct de l'image scannée
                const previewContainer = document.getElementById('foodImagePreviewContainer');
                const previewImg = document.getElementById('foodImagePreview');
                if (imgUrl && imgUrl.trim() !== '') {
                    previewImg.src = imgUrl;
                    previewContainer.classList.remove('d-none');
                } else {
                    previewContainer.classList.add('d-none');
                    previewImg.src = '';
                }

                if (!kcal || carbs === undefined || fat === undefined || protein === undefined) {
                    feedback.className = "alert alert-warning mt-2 mb-0 py-2 d-block";
                    feedback.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Produit incomplet !</strong><br>
                                Certaines macros manquent sur Open Food Facts. Remplis les manuellement.
                            </div>
                        </div>
                    `;
                } else {
                    feedback.className = "alert alert-success mt-2 mb-0 py-2 d-block";
                    feedback.innerHTML = "<i class='bi bi-check-circle-fill me-2'></i> Produit importé avec succès !";
                }
            } else {
                feedback.className = "alert alert-danger mt-2 mb-0 py-2 d-block";
                feedback.innerHTML = "<i class='bi bi-exclamation-circle-fill me-2'></i> Produit inconnu. Saisie 100% manuelle.";
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

// --- LOGIQUE DU SCANNER PHOTO ---
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

// --- SUPPRESSION ALIMENT ---
let deleteModalBootstrap = null;

function confirmDelete(id, name) {
    document.getElementById('deleteFoodId').value = id;
    document.getElementById('deleteFoodName').innerText = name;
    
    if(!deleteModalBootstrap) {
        deleteModalBootstrap = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    }
    deleteModalBootstrap.show();
}

function deleteCurrentFoodFromModal() {
    const id = document.getElementById('foodId').value; 
    const name = document.getElementById('foodName').value; 
    
    const editModalEl = document.getElementById('addFoodModal');
    const editModal = bootstrap.Modal.getInstance(editModalEl);
    if(editModal) editModal.hide();
    
    confirmDelete(id, name);
}

// --- AUTO-FOCUS & REFORMATAGE INTELLIGENT DES SAISIES NUMÉRIQUES ---
document.addEventListener("DOMContentLoaded", function () {
    
    // Auto-focus intelligent sur le champ Code-barres dès l'ouverture de la modale
    const modalEl = document.getElementById('addFoodModal');
    modalEl.addEventListener('shown.bs.modal', function () {
        const barcodeInput = document.getElementById('foodBarcode');
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            // Sur mobile, on bloque le clavier virtuel AVANT de faire le focus
            barcodeInput.setAttribute('inputmode', 'none');
        } else {
            // Sur PC, on s'assure que l'inputmode reste normal
            barcodeInput.removeAttribute('inputmode');
        }
        
        barcodeInput.focus();
    });

    // Si l'utilisateur clique manuellement dans le champ code-barres, on lui réactive le clavier
    document.getElementById('foodBarcode').addEventListener('click', function() {
        this.removeAttribute('inputmode');
        this.focus();
    });

    // Gestion propre des décimales (Solution ultime type=text temporaire)
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

    // Écouteur manuel sur le champ URL de la photo pour mettre à jour l'aperçu si collé à la main
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