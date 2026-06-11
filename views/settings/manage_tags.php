
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?action=settings">Paramètres</a></li>
            <li class="breadcrumb-item active">Gestion des tags</li>
        </ol>
    </nav>
    
    <h2>Gestion des groupes de favori</h2>
    <hr>
    
    <form action="index.php?action=add_tag" method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="tag_name" class="form-control" placeholder="Nom du nouveau groupe" required>
            <button type="submit" class="btn btn-success">Ajouter</button>
        </div>
    </form>

<ul class="list-group">
    <?php if (!empty($userTags)): ?>
        <?php foreach ($userTags as $tag): ?>
            <?php if($tag['id'] == 1) continue; // On masque le tag système ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($tag['tag_name']) ?>
                
                <div>
                    <a href="index.php?action=edit_tag&id=<?= $tag['id'] ?>" class="btn btn-sm btn-outline-primary me-2">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $tag['id'] ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-muted small text-center py-2">Aucun sous-groupe créé.</div>
    <?php endif; ?>
</ul>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmer la suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Êtes-vous sûr de vouloir supprimer ce groupe ? 
        <br><br>
        <span class="text-danger fw-bold">Attention :</span> Les aliments associés à ce nom ne seront plus liés à aucune catégorie.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Supprimer</a>
      </div>
    </div>
  </div>
</div>

<script>
// Script pour passer l'ID à la modale
var deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var tagId = button.getAttribute('data-id');
  var deleteLink = document.getElementById('confirmDeleteBtn');
  deleteLink.href = 'index.php?action=delete_tag&id=' + tagId;
});
</script>