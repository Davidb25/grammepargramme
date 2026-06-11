<div class="container py-4">
        <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php?action=settings">Paramètres</a></li>
            <li class="breadcrumb-item active">Mon profil</li>
        </ol>
    </nav>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="bi bi-check-circle-fill me-2"></i> 
                    <strong>Succès !</strong> Le profil a été modifé avec succès.
                </div>
                <a href="index.php?action=dashboard" class="btn btn-sm btn-outline-success ms-3">
                    Retour au Dashboard
                </a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4"><i class="bi bi-person-circle me-2"></i>Mon Profil</h2>
                    <form action="index.php?action=update_profile" method="POST">

                        <h5 class="text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Informations personnelles</h5>
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Prénom ou Pseudo</label>
                                <input type="text" name="pseudo" class="form-control" value="<?= htmlspecialchars($user['pseudo'] ?? '') ?>" placeholder="Prénom ou Pseudo" required>
                            </div>
                        </div>
                        
                        <h5 class="text-primary mb-3"><i class="bi bi-body-text me-2"></i>Morphologie</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sexe *</label>
                                <select name="sexe" class="form-select" required>
                                    <option value="" disabled <?= empty($profile['sexe']) ? 'selected' : '' ?>>Choisissez...</option>
                                    <option value="homme" <?= ($profile['sexe'] ?? '') == 'homme' ? 'selected' : '' ?>>Homme</option>
                                    <option value="femme" <?= ($profile['sexe'] ?? '') == 'femme' ? 'selected' : '' ?>>Femme</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de naissance *</label>
                                <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($profile['date_naissance'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Taille (cm) *</label>
                                <input type="number" name="taille" class="form-control" value="<?= htmlspecialchars($profile['taille'] ?? '') ?>" placeholder="ex: 175" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Poids actuel (kg) *</label>
                                <input type="number" step="0.01" lang="en-US" name="poids" class="form-control" value="<?= htmlspecialchars($profile['poids'] ?? '') ?>" placeholder="ex: 70.5" required>
                            </div>
                        </div>

                        <h5 class="text-primary mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Objectifs & Activité</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Niveau d'activité *</label>
                                <select name="niveau_activite" class="form-select" required>
                                    <option value="" disabled <?= empty($profile['niveau_activite']) ? 'selected' : '' ?>>Sélectionnez votre niveau d'activité</option>
                                    <option value="1.2"   <?= ($profile['niveau_activite'] ?? '') == '1.2'   ? 'selected' : '' ?>>Sédentaire</option>
                                    <option value="1.375" <?= ($profile['niveau_activite'] ?? '') == '1.375' ? 'selected' : '' ?>>Léger</option>
                                    <option value="1.55"  <?= ($profile['niveau_activite'] ?? '') == '1.55'  ? 'selected' : '' ?>>Modéré</option>
                                    <option value="1.725" <?= ($profile['niveau_activite'] ?? '') == '1.725' ? 'selected' : '' ?>>Intense</option>
                                    <option value="1.9"   <?= ($profile['niveau_activite'] ?? '') == '1.9'   ? 'selected' : '' ?>>Très intense</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Objectif de poids (kg)</label>
                                <input type="number" step="0.01" name="objectif_poids" class="form-control" placeholder="ex: 68">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Enregistrer mes informations</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>