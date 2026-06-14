<div id="containerFiltersAndTableOffProduct" class="d-none">

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

    <div class="mb-3">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="globalSearchInput" class="form-control border-start-0 ps-0" placeholder="Rechercher un aliment par son nom, son code-barres ou ton surnom perso..." onkeyup="filterFoodTable()">
                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
    </div>
    <table id="tableOffProduct" class="table">
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

        </tbody>
    </table>

</div>
<script>

function renderTableOffProducts(products) {
    const containerFiltersAndTableOffProduct = document.getElementById('containerFiltersAndTableOffProduct');
    const tableBody = document.querySelector('#tableOffProduct tbody');
    const table = document.getElementById('tableOffProduct');

    // 1. On vide le contenu actuel pour éviter les doublons
    tableBody.innerHTML = '';
    
    // 2. On boucle sur les données
    products.forEach(product => {
        const row = document.createElement('tr');
        
        // On gère l'affichage de l'image (si null, mettre une image par défaut ou icone)
        const imgDisplay = product.image_path 
            ? `<img src="${product.image_path}" width="40" class="img-thumbnail">` 
            : '<i class="bi bi-image-alt text-muted fs-4"></i>';


        // 1. On prépare le bloc code-barres séparément
        let barcodeHtml = '';
        if (product.barcode)
            barcodeHtml = `<br><small class="text-muted fw-normal"><i class="bi bi-upc-scan me-1"></i>${escapeHtml(product.barcode)}</small>`;
        else
            barcodeHtml = `<br><small class="text-muted fw-normal"><i class="bi bi-upc-scan me-1"></i></small>`;

        row.innerHTML = `
            <td>${imgDisplay}</td>
            <td class="fw-bold">
                ${escapeHtml(product.name)}
               ${barcodeHtml}
            </td>
            <td><span class="badge bg-light text-dark border">${escapeHtml(product.category_name)}</span></td>
            <td><span class="badge bg-primary">${escapeHtml(product.kcal_per_100g)} kcal<small>/ 100g</small></td>
            <td>${escapeHtml(product.proteins_per_100g)}g</td>
            <td>${escapeHtml(product.carbohydrates_per_100g)}g <small class="text-muted">(${escapeHtml(product.sugar_per_100g)}g)</small></td>
            <td>${escapeHtml(product.fat_per_100g)}g <small class="text-muted">(${escapeHtml(product.saturated_fat_per_100g)}g)</small></td>
            <td>${escapeHtml(product.fibers_per_100g)}g</td>
            <td>${escapeHtml(product.salt_per_100g)}g</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" onclick="addToMeal(${escapeHtml(product.id)})">
                    <i class="bi bi-plus-circle"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);

    // 3. On affiche tout le container de la table si il était caché
    containerFiltersAndTableOffProduct.classList.remove('d-none');
    });
}

function clearSearch() {
    const input = document.getElementById('globalSearchInput');
    input.value = '';
    filterFoodTable();
    input.focus();
}

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

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}



</script>