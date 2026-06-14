<?php
// views/build_meals.php
?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-egg-fried me-2 text-success"></i>Définir votre repas</h2>
</div>
<div class="mb-3 d-flex flex-wrap gap-2" style="height:80px; width:100%; border: solid 1px blue">
    Calcul calorique et nutritionnel de mon repas
    <div id="containerCalculRepas" style="width: 100%; min-height:40px; background-color: rgb(180,180,180)"></div>
</div>
<div class="mb-3 d-flex flex-wrap gap-2">
    <button class="btn btn-sm <?php echo (!isset($_GET['filter_custom']) && !isset($_GET['filter_tag'])) ? 'btn-dark' : 'btn-outline-dark'; ?>" onclick="loadCatalogOffproducts()">
        <i class="bi bi-grid-fill me-1"></i> Catalogue OpenFoodFacts
    </button>
</div>
    
<?php
// On appelle la vue du catalogue de produit OpenFoodFacts. 

    require 'partials/_table_products_off.php';

?>

<!-- Concerne ajout produit ------------------------------->
<!-- Concerne ajout produit ------------------------------->
<!-- Concerne ajout produit ------------------------------
<div class="mb-3 p-3 bg-light rounded border border-warning-subtle">
    <label class="form-label fw-bold text-dark"><i class="bi bi-upc-scan me-2 text-warning"></i>Scanner ou saisir un Code-barres</label>
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

-->


<div id="formProductOff" class="border p-3 rounded shadow-sm d-none">
    <div id="foodImagePreviewContainer" class="mb-3 text-center d-none">
        <label class="form-label d-block fw-semibold text-muted">Visuel de l'aliment</label>
        <img id="foodImagePreview" src="" alt="Aperçu" class="img-thumbnail shadow-sm" style="max-height: 120px; object-fit: cover;">
    </div>

    <div class="row">
        <div class="col-12 col-md-7 mb-3">
            <label class="form-label fw-bold">Nom de l'aliment *</label>
            <input type="text" name="name" id="foodName" class="form-control" required placeholder="ex: Skyr, Miel...">
        </div>
        <div class="col-12 col-md-3 mb-3">
            <label class="form-label fw-bold">Code-barre *</label>
            <input type="text" name="name" id="foodCode" class="form-control">
        </div>
        <div class="col-12 col-md-2 mb-3">
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
        <div class="col-12 col-md-6 mb-3">
            <label class="form-label fw-semibold">Glucides (pour 100<span class="lbl-unit">g</span>) *</label>
            <input type="number" step="0.01" name="carbs" id="foodCarbs" class="form-control" required>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <label class="form-label text-muted">dont Sucres (g) *</label>
            <input type="number" step="0.01" name="sugars" id="foodSugars" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <label class="form-label fw-semibold">Lipides (pour 100<span class="lbl-unit">g</span>) *</label>
            <input type="number" step="0.01" name="fat" id="foodFat" class="form-control" required>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <label class="form-label text-muted">dont Saturés (g) *</label>
            <input type="number" step="0.01" name="saturated_fat" id="foodSaturatedFat" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <label class="form-label fw-semibold">Protéines (pour 100<span class="lbl-unit">g</span>) *</label>
            <input type="number" step="0.01" name="protein" id="foodProtein" class="form-control" required>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <label class="form-label fw-semibold">Fibres (g) *</label>
            <input type="number" step="0.01" name="fibers" id="foodFibers" class="form-control" required>
        </div>
    </div>

    <div class="mb-3 col-12 col-md-6">
        <label class="form-label fw-semibold">Sel (g) *</label>
        <input type="number" step="0.01" name="salt" id="foodSalt" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Lien URL de la Photo</label>
        <input type="text" name="image_path" id="foodImage" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Lien de la Fiche Produit</label>
        <input type="text" name="off_url" id="foodUrl" class="form-control">
    </div>
</div>


<?php
// Modal de gestion de favori d'un aliment. 

    require 'partials/_modal_favorite_food.php';

?>



<script>


function loadCatalogOffproducts() {
    console.log('je charge le catalogue');

    fetch('index.php?action=ajax_load_catalog_off_products') // Appelle la méthode AJAX
        .then(res => res.json())
        .then(data => {
            console.log(data); // Ici tu as ton tableau de produits !
            // Maintenant, tu peux boucler sur 'data' pour créer tes éléments HTML
            // data.forEach(product => { ... });

            renderTableOffProducts(data); // On passe la réponse JSON à notre fonction d'affichage dans la vue

        })
        .catch(err => {
            console.error("Erreur lors de la récupération du catalogue:", err);
        });
}



function checkBarcodeLength(barcode) {
    const cleanedBarcode = barcode.trim();
    if (cleanedBarcode.length === 13 || cleanedBarcode.length === 8) {
        fetchOFFData(cleanedBarcode);

    } else {

        clearApiFeedback();
        document.getElementById('foodCode').value = '';
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


function clearApiFeedback() { // permet d'effacer le message d'information concernant l'appel
    const loader = document.getElementById('apiLoader');
    const feedback = document.getElementById('apiFeedback');
    if(loader) loader.classList.add('d-none');
    if(feedback) {
        feedback.classList.add('d-none');
        feedback.className = "small mt-1 d-none";
    }
}

function updateDynamicLabels(unit) {
    document.querySelectorAll('.lbl-unit').forEach(span => {
        span.innerText = unit;
    });
}

function setFoodFieldsReadOnly(isReadOnly) {
    const fields = [
        'foodName', 'foodCode', 'foodUnit', 'foodCategoryId', 'foodCalories', 
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

    const btnSubmit = document.getElementById('btnSubmit');
    if (btnSubmit) {
        btnSubmit.classList.remove('d-none');
    }
}


</script>