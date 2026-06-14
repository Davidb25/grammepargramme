console.log("fichier js app.js");


// Appel API à OpenFoodFAct
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
                const nutriments = product.nutriments || {};

                console.log(product);

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

                const kcal = nutriments['energy-kcal_100g'] || nutriments['energy-kcal_value'] || nutriments['energy-kcal'] || '';
                const carbs = nutriments['carbohydrates_100g'];
                const sugars = nutriments['sugars_100g'];
                const fat = nutriments['fat_100g'];
                const satFat = nutriments['saturated-fat_100g'];
                const protein = nutriments['proteins_100g'];
                const fibers = nutriments['fiber_100g'];
                const salt = nutriments['salt_100g'];

                document.getElementById('foodCalories').value = kcal ? Math.round(kcal) : '';
                const formatMacro = (val) => (val !== undefined && val !== '') ? parseFloat(val).toFixed(2) : '';

                document.getElementById('foodCode').value = product.code;

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
                    feedback.innerHTML = "<i class='bi bi-check-circle-fill me-2'></i> Produit trouvé avec succès !";
                    setFoodFieldsReadOnly(true);
                }

            document.getElementById('formProductOff').classList.remove('d-none');
            document.getElementById('foodBarcode').value='';

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

