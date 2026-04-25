<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Categorie.php';
require_once __DIR__ . '/../../models/Recette.php';
require_once __DIR__ . '/../../models/Aliment.php';
require_once __DIR__ . '/../../controllers/CategorieController.php';
require_once __DIR__ . '/../../controllers/RecetteController.php';
require_once __DIR__ . '/../../controllers/AlimentController.php';

$categorieController = new CategorieController();
$recetteController = new RecetteController();
$alimentController = new AlimentController();

$id = (int) ($_GET['id'] ?? 0);
$categorie = $categorieController->getById($id);

if (!$categorie) {
    header('Location: listCategorie.php?error=Catégorie introuvable');
    exit;
}

$errors = [];
$old = [];

// Get all recettes and aliments for selection
$allRecettes = $recetteController->getAll();
$allAliments = $alimentController->getAll();

// Get currently assigned items
$assignedRecettes = $categorieController->getCategoriesForRecette($id);
$assignedAliments = $categorieController->getCategoriesForAliment($id);

// Determine current type based on what's assigned
$currentType = '';
$selectedRecetteIds = [];
$selectedAlimentIds = [];

// Check which items are currently in this category
$selectedRecetteIds = $categorieController->getRecetteIdsForCategory($id);
$selectedAlimentIds = $categorieController->getAlimentIdsForCategory($id);

if (!empty($selectedRecetteIds)) {
    $currentType = 'recette';
} elseif (!empty($selectedAlimentIds)) {
    $currentType = 'aliment';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    
    // Update the category
    $result = $categorieController->update($id, $_POST);
    
    if ($result['success']) {
        // Determine which type was selected
        $type = $_POST['type'] ?? '';
        
        if ($type === 'recette') {
            $selectedIds = $_POST['recette_ids'] ?? [];
            $categorieController->assignRecettesToCategory($id, $selectedIds);
            // Clear aliment associations
            $categorieController->assignAlimentsToCategory($id, []);
        } elseif ($type === 'aliment') {
            $selectedIds = $_POST['aliment_ids'] ?? [];
            $categorieController->assignAlimentsToCategory($id, $selectedIds);
            // Clear recette associations
            $categorieController->assignRecettesToCategory($id, []);
        }
        
        header('Location: listCategorie.php?success=Catégorie modifiée avec succès');
        exit;
    }
    $errors = $result['errors'];
} else {
    // Pre-fill form with existing data
    $old['nom'] = $categorie->nom;
    $old['type'] = $currentType;
    $old['recette_ids'] = $selectedRecetteIds;
    $old['aliment_ids'] = $selectedAlimentIds;
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoNutri – Modifier Catégorie</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <style>
      :root {
        --green-dark: #2d6a1f;
        --green-main: #4a9e30;
        --green-light: #7ec44f;
        --green-pale: #e8f5e1;
        --orange: #f07c1b;
        --orange-light: #fde8d0;
        --black: #111;
        --grey: #666;
        --grey-light: #999;
        --white: #fff;
        --border: #e4eed9;
        --sidebar-bg: #0e2a08;
        --sidebar-w: 260px;
        --topbar-h: 68px;
        --bg: #f2f8ee;
        --card-bg: #fff;
        --red: #e53935;
        --red-light: #fdecea;
      }

      *,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }
      html { scroll-behavior: smooth; }
      body {
        font-family: "DM Sans", sans-serif;
        background: var(--bg);
        color: var(--black);
        display: flex;
        min-height: 100vh;
        overflow-x: hidden;
      }

      .sidebar {
        width: var(--sidebar-w);
        background: var(--sidebar-bg);
        min-height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        z-index: 50;
        transition: transform 0.3s;
        overflow-y: auto;
        overflow-x: hidden;
      }
      .sidebar-logo {
        padding: 1.4rem 1.6rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        display: flex;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
      }
      .logo-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
      }
      .logo-text {
        font-family: "Playfair Display", serif;
        font-size: 1.35rem;
        color: var(--white);
        letter-spacing: -0.4px;
      }
      .logo-text span { color: var(--orange); }
      .sidebar-admin-tag {
        background: rgba(240, 124, 27, 0.18);
        color: var(--orange);
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-left: auto;
      }

      .sidebar-section { padding: 1.2rem 0.9rem 0.4rem; }
      .sidebar-section-label {
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 0 0.7rem;
        margin-bottom: 0.5rem;
      }

      .nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 0.9rem;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.88rem;
        font-weight: 500;
        margin-bottom: 0.15rem;
        position: relative;
      }
      .nav-item:hover {
        background: rgba(255, 255, 255, 0.07);
        color: rgba(255, 255, 255, 0.9);
      }
      .nav-item.active {
        background: linear-gradient(
          90deg,
          rgba(74, 158, 48, 0.35),
          rgba(74, 158, 48, 0.1)
        );
        color: var(--white);
        border-left: 3px solid var(--green-light);
      }
      .nav-item.active .nav-icon { color: var(--green-light); }

      .nav-icon {
        font-size: 1.1rem;
        width: 22px;
        text-align: center;
        flex-shrink: 0;
      }

      .nav-badge {
        margin-left: auto;
        background: var(--orange);
        color: var(--white);
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 50px;
        min-width: 20px;
        text-align: center;
      }
      .nav-badge.green { background: var(--green-main); }
      .nav-badge.orange { background: var(--orange); }

      .sidebar-footer {
        margin-top: auto;
        padding: 1rem 0.9rem;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
      }
      .admin-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 0.9rem;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s;
      }
      .admin-profile:hover { background: rgba(255, 255, 255, 0.07); }
      .admin-av {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(
          135deg,
          var(--green-main),
          var(--green-dark)
        );
        display: grid;
        place-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--white);
        flex-shrink: 0;
        border: 2px solid rgba(126, 196, 79, 0.4);
      }
      .admin-info strong {
        display: block;
        font-size: 0.83rem;
        color: var(--white);
        font-weight: 600;
      }
      .admin-info span {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.45);
      }
      .admin-profile .logout-icon {
        margin-left: auto;
        color: rgba(255, 255, 255, 0.3);
        font-size: 0.9rem;
        transition: color 0.2s;
      }
      .admin-profile:hover .logout-icon { color: var(--orange); }

      .main-area {
        margin-left: var(--sidebar-w);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      .topbar {
        height: var(--topbar-h);
        background: var(--white);
        border-bottom: 1px solid var(--border);
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 40;
        box-shadow: 0 2px 12px rgba(45, 106, 31, 0.06);
      }

      .topbar-left { display: flex; align-items: center; gap: 1rem; }
      .page-title h1 {
        font-family: "Playfair Display", serif;
        font-size: 1.3rem;
        color: var(--green-dark);
        line-height: 1.1;
      }
      .page-title span {
        font-size: 0.78rem;
        color: var(--grey-light);
        font-weight: 400;
      }

      .topbar-search {
        display: flex;
        align-items: center;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.45rem 0.9rem;
        gap: 0.5rem;
        width: 260px;
      }
      .topbar-search input {
        border: none;
        outline: none;
        background: transparent;
        font-family: "DM Sans", sans-serif;
        font-size: 0.85rem;
        color: var(--black);
        width: 100%;
      }
      .topbar-search svg { color: var(--grey-light); flex-shrink: 0; }

      .topbar-right { display: flex; align-items: center; gap: 1rem; }

      .topbar-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--bg);
        border: 1.5px solid var(--border);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
      }
      .topbar-icon-btn:hover {
        background: var(--green-pale);
        border-color: var(--green-main);
      }
      .topbar-icon-btn .notif-dot {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--orange);
        border: 2px solid var(--white);
      }

      .topbar-date {
        font-size: 0.8rem;
        color: var(--grey-light);
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.45rem 0.9rem;
        white-space: nowrap;
      }

      .content { padding: 2rem; flex: 1; }

      .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
      }

      .crud-title {
        font-family: "Playfair Display", serif;
        font-size: 1.8rem;
        color: var(--green-dark);
        margin: 0;
      }

      .crud-actions { display: flex; gap: 1rem; }

      .btn {
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 10px;
        font-family: "DM Sans", sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
      }

      .btn-primary {
        background: linear-gradient(135deg, var(--green-main), var(--green-dark));
        color: var(--white);
      }

      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(45, 106, 31, 0.3);
      }

      .btn-secondary {
        background: var(--bg);
        color: var(--green-dark);
        border: 1.5px solid var(--border);
      }

      .btn-secondary:hover {
        background: var(--green-pale);
        border-color: var(--green-main);
      }

      .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
      }

      .alert-error {
        background: var(--red-light);
        color: var(--red);
        border: 1px solid #ffcdd2;
      }

      .form-container { max-width: 800px; margin: 0 auto; }

      .form-section {
        background: var(--card-bg);
        border: 1.5px solid var(--border);
        border-radius: 18px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 16px rgba(45, 106, 31, 0.08);
      }

      .form-section h2 {
        font-family: "Playfair Display", serif;
        font-size: 1.4rem;
        color: var(--green-dark);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .form-group { margin-bottom: 1.5rem; }
      .form-group.full-width { grid-column: 1 / -1; }

      .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--green-dark);
        margin-bottom: 0.5rem;
      }

      .form-input,
      .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: "DM Sans", sans-serif;
        font-size: 0.9rem;
        background: var(--bg);
        outline: none;
        transition: border-color 0.2s;
      }

      .form-input:focus,
      .form-select:focus {
        border-color: var(--green-main);
        background: var(--white);
      }

      .form-error {
        color: var(--red);
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: block;
      }

      .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border);
      }

      .type-selector {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
      }

      .type-option {
        flex: 1;
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        background: var(--bg);
      }

      .type-option:hover {
        border-color: var(--green-main);
        background: var(--green-pale);
      }

      .type-option.selected {
        border-color: var(--green-main);
        background: var(--green-pale);
        box-shadow: 0 4px 12px rgba(74, 158, 48, 0.2);
      }

      .type-option input[type="radio"] {
        display: none;
      }

      .type-option-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--green-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
      }

      .selection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
        background: var(--bg);
        border-radius: 10px;
      }

      .selection-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
      }

      .selection-item:hover {
        border-color: var(--green-main);
        background: var(--green-pale);
      }

      .selection-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
      }

      .selection-item label {
        flex: 1;
        cursor: pointer;
        font-size: 0.9rem;
        color: var(--black);
      }

      .selection-section {
        display: none;
      }

      .selection-section.active {
        display: block;
      }

      @media (max-width: 820px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .main-area { margin-left: 0; }
        .crud-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 1rem;
        }
        .crud-actions {
          width: 100%;
          justify-content: flex-end;
        }
        .form-actions {
          flex-direction: column;
        }
        .btn { justify-content: center; }
        .type-selector { flex-direction: column; }
        .selection-grid { grid-template-columns: 1fr; }
      }
    </style>
  </head>
  <body>
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <a class="sidebar-logo" href="index.php">
        <div class="logo-icon">
          <svg viewBox="0 0 32 32" fill="none" width="24" height="24">
            <path d="M16 4C10 4 5 8 4 14c4-2 9-1 12 3 3-4 8-5 12-3-1-6-6-10-12-10z" fill="#7ec44f"/>
            <path d="M4 14c-1 5 2 10 7 12l5-8-5-4c-3 0-6 0-7 0z" fill="#4a9e30"/>
            <path d="M28 14c1 5-2 10-7 12l-5-8 5-4c3 0 6 0 7 0z" fill="#2d6a1f"/>
            <circle cx="16" cy="22" r="3" fill="#f07c1b"/>
          </svg>
        </div>
        <span class="logo-text">Eco<span>Nutri</span></span>
        <span class="sidebar-admin-tag">Admin</span>
      </a>

      <div class="sidebar-section">
        <div class="sidebar-section-label">Principal</div>
        <a class="nav-item" href="index.php"><span class="nav-icon">📊</span> Tableau de bord</a>
        <a class="nav-item" href="#"><span class="nav-icon">👥</span> Utilisateurs<span class="nav-badge">1 248</span></a>
        <a class="nav-item" href="listRecette.php"><span class="nav-icon">🍽️</span> Recettes<span class="nav-badge green">240</span></a>
        <a class="nav-item" href="listAliment.php"><span class="nav-icon">🥕</span> Aliments<span class="nav-badge orange">156</span></a>
        <a class="nav-item active" href="listCategorie.php"><span class="nav-icon">🏷️</span> Catégories</a>
        <a class="nav-item" href="statistiques.php"><span class="nav-icon">📈</span> Statistiques</a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-label">Modules</div>
        <a class="nav-item" href="#"><span class="nav-icon">🎯</span> Profils Nutritionnels</a>
        <a class="nav-item" href="#"><span class="nav-icon">📋</span> Suivi Alimentaire</a>
        <a class="nav-item" href="#"><span class="nav-icon">🤖</span> IA &amp; Recommandations</a>
        <a class="nav-item" href="#"><span class="nav-icon">🥕</span> Ingrédients</a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-label">Configuration</div>
        <a class="nav-item" href="#"><span class="nav-icon">⚙️</span> Paramètres</a>
        <a class="nav-item" href="#"><span class="nav-icon">📄</span> Rapports</a>
      </div>

      <div class="sidebar-footer">
        <a class="nav-item" href="../../views/index.php" style="margin-bottom:0.5rem; background:rgba(240,124,27,0.15); color:var(--orange); border:1px solid rgba(240,124,27,0.3);">
          <span class="nav-icon">🏠</span> Retour au site
        </a>
        <div class="admin-profile">
          <div class="admin-av">AD</div>
          <div class="admin-info">
            <strong>Admin EcoNutri</strong>
            <span>Super Administrateur</span>
          </div>
          <span class="logout-icon">⏻</span>
        </div>
      </div>
    </aside>

    <!-- MAIN AREA -->
    <div class="main-area">
      <div class="topbar">
        <div class="topbar-left">
          <div class="page-title">
            <h1>Modifier une Catégorie</h1>
            <span>Modification de la catégorie</span>
          </div>
        </div>

        <div class="topbar-search">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input type="text" placeholder="Rechercher…" />
        </div>

        <div class="topbar-right">
          <div class="topbar-date">📅 <?php echo date('d F Y'); ?></div>
          <div class="topbar-icon-btn" title="Notifications">
            🔔
            <span class="notif-dot"></span>
          </div>
          <div class="topbar-icon-btn" title="Messages">💬</div>
        </div>
      </div>

      <!-- CONTENT -->
      <div class="content">
        <div class="crud-header">
          <h1 class="crud-title">🏷️ Modifier une Catégorie</h1>
          <div class="crud-actions">
            <a href="listCategorie.php" class="btn btn-secondary">
              ← Retour à la liste
            </a>
          </div>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <span>❌</span>
          <div>
            <?php foreach ($errors as $error): ?>
              <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="form-container">
          <form method="POST" id="categorieForm">
            <div class="form-section">
              <h2>🏷️ Informations de la catégorie</h2>
              <div class="form-group full-width">
                <label class="form-label" for="nom">Nom de la catégorie *</label>
                <input type="text" id="nom" name="nom" class="form-input" 
                       value="<?php echo htmlspecialchars($old['nom'] ?? ''); ?>" 
                       required maxlength="100" 
                       placeholder="ex : Végétarien, Sans gluten, Protéiné…">
                <?php if (isset($errors['nom'])): ?>
                  <span class="form-error">⚠️ <?php echo htmlspecialchars($errors['nom']); ?></span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-section">
              <h2>📋 Type de contenu</h2>
              <p style="color: var(--grey); font-size: 0.9rem; margin-bottom: 1rem;">
                Sélectionnez le type d'éléments à associer à cette catégorie (recettes ou aliments).
              </p>
              
              <div class="type-selector">
                <label class="type-option" id="type-recette-option">
                  <input type="radio" name="type" value="recette" id="type-recette" 
                         <?php echo ($old['type'] ?? '') === 'recette' ? 'checked' : ''; ?>>
                  <div class="type-option-label">
                    <span>🍽️</span>
                    <span>Recettes</span>
                  </div>
                </label>
                
                <label class="type-option" id="type-aliment-option">
                  <input type="radio" name="type" value="aliment" id="type-aliment"
                         <?php echo ($old['type'] ?? '') === 'aliment' ? 'checked' : ''; ?>>
                  <div class="type-option-label">
                    <span>🥕</span>
                    <span>Aliments</span>
                  </div>
                </label>
              </div>

              <!-- Recettes Selection -->
              <div id="recettes-section" class="selection-section">
                <h3 style="font-size: 1rem; color: var(--green-dark); margin-bottom: 1rem;">
                  Sélectionner les recettes
                </h3>
                <div class="selection-grid">
                  <?php foreach ($allRecettes as $recette): ?>
                  <div class="selection-item">
                    <input type="checkbox" name="recette_ids[]" value="<?= $recette->id ?>" 
                           id="recette-<?= $recette->id ?>"
                           <?php echo in_array($recette->id, $old['recette_ids'] ?? []) ? 'checked' : ''; ?>>
                    <label for="recette-<?= $recette->id ?>"><?= htmlspecialchars($recette->nom) ?></label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Aliments Selection -->
              <div id="aliments-section" class="selection-section">
                <h3 style="font-size: 1rem; color: var(--green-dark); margin-bottom: 1rem;">
                  Sélectionner les aliments
                </h3>
                <div class="selection-grid">
                  <?php foreach ($allAliments as $aliment): ?>
                  <div class="selection-item">
                    <input type="checkbox" name="aliment_ids[]" value="<?= $aliment->id ?>" 
                           id="aliment-<?= $aliment->id ?>"
                           <?php echo in_array($aliment->id, $old['aliment_ids'] ?? []) ? 'checked' : ''; ?>>
                    <label for="aliment-<?= $aliment->id ?>"><?= htmlspecialchars($aliment->nom) ?></label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="form-actions">
              <a href="listCategorie.php" class="btn btn-secondary">Annuler</a>
              <button type="submit" class="btn btn-primary">
                <span>✓</span> Mettre à jour la catégorie
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      // Type selector logic
      const typeRecette = document.getElementById('type-recette');
      const typeAliment = document.getElementById('type-aliment');
      const typeRecetteOption = document.getElementById('type-recette-option');
      const typeAlimentOption = document.getElementById('type-aliment-option');
      const recettesSection = document.getElementById('recettes-section');
      const alimentsSection = document.getElementById('aliments-section');

      function updateTypeSelection() {
        if (typeRecette.checked) {
          typeRecetteOption.classList.add('selected');
          typeAlimentOption.classList.remove('selected');
          recettesSection.classList.add('active');
          alimentsSection.classList.remove('active');
          
          // Uncheck all aliment checkboxes
          document.querySelectorAll('input[name="aliment_ids[]"]').forEach(cb => cb.checked = false);
        } else if (typeAliment.checked) {
          typeAlimentOption.classList.add('selected');
          typeRecetteOption.classList.remove('selected');
          alimentsSection.classList.add('active');
          recettesSection.classList.remove('active');
          
          // Uncheck all recette checkboxes
          document.querySelectorAll('input[name="recette_ids[]"]').forEach(cb => cb.checked = false);
        }
      }

      typeRecette.addEventListener('change', updateTypeSelection);
      typeAliment.addEventListener('change', updateTypeSelection);

      // Initialize on page load
      updateTypeSelection();

      // Form validation
      document.getElementById('categorieForm').addEventListener('submit', function(e) {
        const nom = document.getElementById('nom').value.trim();
        const typeSelected = typeRecette.checked || typeAliment.checked;

        if (!nom) {
          e.preventDefault();
          alert('Veuillez saisir un nom pour la catégorie.');
          return;
        }

        if (!typeSelected) {
          e.preventDefault();
          alert('Veuillez sélectionner un type (Recettes ou Aliments).');
          return;
        }
      });
    </script>
  </body>
</html>
