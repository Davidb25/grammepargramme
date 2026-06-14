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
