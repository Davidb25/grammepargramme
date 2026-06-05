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
                                            data-barcode="<?php echo htmlspecialchars($food['barcode'] ?? ''); ?>"
                                            data-image="<?php echo htmlspecialchars($food['image_path'] ?? ''); ?>"
                                            data-url="<?php echo htmlspecialchars($food['off_url'] ?? ''); ?>">
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
                <h5 class="modal-title" id="modalTitle">Saisie Nutritionnelle</h5>
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
                            <label class="form-label fw-semibold text-success">Fibres (g) *</label>
                            <input type="number" step="0.01" name="fibers" id="foodFibers" class="form-control" required value="0.00">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Sel (g) *</label>
                            <input type="number" step="0.01" name="salt" id="foodSalt" class="form-control" required value="0.00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Code-barres</label>
                            <div class="input-group">
                                <input type="text" name="barcode" id="foodBarcode" class="form-control" placeholder="Optionnel">
                                <button class="btn btn-outline-primary" type="button" id="btnScanCamera" onclick="startCameraScanner()" title="Scanner avec l'appareil photo">
                                    <i class="bi bi-camera"></i>
                                </button>
                            </div>
                        </div>
                        <div id="cameraScannerArea" class="mb-3 d-none text-center bg-dark rounded p-2 position-relative">
                            <div id="interactive" class="viewport" style="width: 100%; max-height: 250px; overflow: hidden;"></div>
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="stopCameraScanner()">Arrêter la caméra</button>
                        </div>
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
                <div class="modal-footer">
                    <button type="reset" id="btnReset" class="btn btn-outline-secondary me-auto">Vider</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" id="btnSubmit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
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
    document.getElementById('foodFibers').value = button.getAttribute('data-fibers');
    document.getElementById('foodSalt').value = button.getAttribute('data-salt');
    document.getElementById('foodBarcode').value = button.getAttribute('data-barcode');
    document.getElementById('foodImage').value = button.getAttribute('data-image'); // NOUVEAU
    document.getElementById('foodUrl').value = button.getAttribute('data-url');     // NOUVEAU
}

function resetFoodForm() {
    setupAddMode();
}

// --- LOGIQUE DU SCANNER PHOTO ---
let html5QrCode = null;

function startCameraScanner() {
    const scannerArea = document.getElementById('cameraScannerArea');
    scannerArea.classList.remove('d-none');

    html5QrCode = new Html5Qrcode("interactive");

    const config = { 
        fps: 15,
        qrbox: { width: 250, height: 150 }, // On remet le rectangle natif pour éviter le bug
        aspectRatio: 1.777778
    };

    html5QrCode.start(
        { facingMode: "environment" }, 
        config,
        (decodedText, decodedResult) => {
            document.getElementById('foodBarcode').value = decodedText;
            if (navigator.vibrate) navigator.vibrate(100);
            stopCameraScanner();
            alert("Produit scanné : " + decodedText);
        },
        (errorMessage) => {
            console.log("Recherche en cours...");
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

// Modifie aussi ta fonction setupAddMode existante pour couper la caméra si elle était ouverte
const originalSetupAddMode = setupAddMode;
setupAddMode = function() {
    originalSetupAddMode();
    stopCameraScanner();
}
</script>
<style>
/* Conteneur principal de la caméra */
#cameraScannerArea {
    background-color: #1a1a1a;
    border: 2px solid #333;
    overflow: hidden;
    position: relative;
}

/* Ajustement de la zone vidéo */
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

/* 🧼 L'ARME SUPRÊME : On cible spécifiquement les calques de bordures de Html5-QRCode */
#interactive div:has(video), 
#interactive [id*="html5-qrcode"] {
    /* Cette astuce force l'effacement des tracés superposés de la bibliothèque */
    border-color: transparent !important;
}

/* On force TOUS les éléments enfants générés (les fameux coins blancs) à devenir invisibles */
#interactive div div,
#interactive div span,
#interactive canvas {
    border: none !important;
    outline: none !important;
    display: none !important;
    opacity: 0 !important;
}

/* 🟡 TON RECTANGLE JAUNE PARFAIT ET SUR-MESURE */
#interactive.viewport::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 250px;  
    height: 150px; 
    border: 2px solid #ffc107; /* Ton joli jaune */
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.4);
    z-index: 999; /* On le passe au premier plan pour qu'il soit bien visible */
    pointer-events: none;
}

/* 🔴 LA LIGNE ROUGE DE VISÉE */
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
    opacity:0.5;
    z-index: 1000;
    pointer-events: none;
}
</style>