<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrammeParGramme - Suivi Nutritionnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?action=dashboard">
            <i class="bi bi-speedometer2 text-warning me-2"></i>Gramme Par Gramme
        </a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=dashboard"><i class="bi bi-house-door me-1"></i>Journal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=foods"><i class="bi bi-egg-fried me-1"></i>Catalogue Aliments</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3 small">
                        <i class="bi bi-person-circle text-muted me-1"></i><?php echo htmlspecialchars($_SESSION['user_email']); ?>
                    </span>
                    <a href="index.php?action=logout" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>

<div class="container pb-5">