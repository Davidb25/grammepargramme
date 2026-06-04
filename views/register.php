<?php
// views/register.php
// Ce fichier sera inclus entre le header et le footer via le contrôleur
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h2 class="text-center mb-4 font-weight-bold text-dark">Créer un compte</h2>
                
                <!-- Affichage des alertes Bootstrap si elles existent -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=register" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="david@exemple.com">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="6 caractères min.">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-2 shadow-sm">
                        <i class="bi bi-person-plus-fill me-2"></i>S'inscrire
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>