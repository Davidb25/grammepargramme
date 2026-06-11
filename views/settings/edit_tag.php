
<?php
//Cela indique à l'éditeur : "Hé, je sais que $tag est un tableau, ne t'inquiète pas, il arrive d'ailleurs."
/** @var array $tag */
?>

<div class="container py-4">
    <h2>Modifier le groupe</h2>
    <form action="index.php?action=update_tag" method="POST">
        <input type="hidden" name="id" value="<?= $tag['id'] ?>">
        <div class="mb-3">
            <input type="text" name="tag_name" class="form-control" value="<?= htmlspecialchars($tag['tag_name']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="index.php?action=manage_tags" class="btn btn-secondary">Annuler</a>
    </form>
</div>