<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Recette.php';
require_once __DIR__ . '/../../models/RecetteAliment.php';
require_once __DIR__ . '/../../controllers/AlimentController.php';
require_once __DIR__ . '/../../controllers/RecetteController.php';

$alimentController = new AlimentController();
$recetteController = new RecetteController();
$errors            = [];

$id      = (int) ($_GET['id'] ?? 0);
$recette = $recetteController->getById($id);

if (!$recette) {
    header('Location: listRecette.php?error=notfound');
    exit;
}

$allAliments     = $alimentController->getAll();
$currentIngredients = $recetteController->getAliments($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingredients = [];
    $ingAliments = $_POST['ing_aliment'] ?? [];
    $ingQtts     = $_POST['ing_quantite'] ?? [];

    foreach ($ingAliments as $idx => $alimentId) {
        if (!empty($alimentId)) {
            $ingredients[] = [
                'aliment_id' => (int) $alimentId,
                'quantite'   => $ingQtts[$idx] ?? '',
            ];
        }
    }

    $result = $recetteController->update($id, $_POST, $ingredients);

    if ($result['success']) {
        header('Location: listRecette.php?success=updated');
        exit;
    }
    $errors = $result['errors'];

    // Rebuild recette from POST for re-display
    $recette = Recette::fromArray(array_merge(
        ['id' => $id, 'image' => $recette->image, 'date_creation' => $recette->date_creation],
        $_POST
    ));
    // Rebuild ingredients list from POST
    $currentIngredients = [];
    foreach ($ingAliments as $idx => $alimentId) {
        if (!empty($alimentId)) {
            $currentIngredients[] = [
                'aliment_id' => $alimentId,
                'quantite'   => $ingQtts[$idx] ?? '',
                'nom'        => '',
                'calories'   => 0,
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoNutri – Administration</title>
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
        --blue: #1565c0;
        --blue-light: #e3f0ff;
      }

      *,
      *::before,
      *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }
      html {
        scroll-behavior: smooth;
      }

      body {
        font-family: "DM Sans", sans-serif;
        background: var(--bg);
        color: var(--black);
        display: flex;
        min-height: 100vh;
        overflow-x: hidden;
      }

      /* ══════════════════════════════════════
       SIDEBAR
    ══════════════════════════════════════ */
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
      .logo-text span {
        color: var(--orange);
      }
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

      .sidebar-section {
        padding: 1.2rem 0.9rem 0.4rem;
      }
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
      .nav-item.active .nav-icon {
        color: var(--green-light);
      }

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
      .nav-badge.green {
        background: var(--green-main);
      }

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
      .admin-profile:hover {
        background: rgba(255, 255, 255, 0.07);
      }
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
      .admin-profile:hover .logout-icon {
        color: var(--orange);
      }

      /* ══════════════════════════════════════
       MAIN AREA
    ══════════════════════════════════════ */
      .main-area {
        margin-left: var(--sidebar-w);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      /* ── Top Bar ── */
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

      .topbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
      }
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
      .topbar-search svg {
        color: var(--grey-light);
        flex-shrink: 0;
      }

      .topbar-right {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

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

      /* ── Content ── */
      .content {
        padding: 2rem;
        flex: 1;
      }

      /* Custom styles for CRUD pages */
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

      .crud-actions {
        display: flex;
        gap: 1rem;
      }

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
        background: linear-gradient(135deg, var(--orange), #d96510);
        color: var(--white);
      }

      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(240, 124, 27, 0.3);
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

      .form-container {
        max-width: 800px;
        margin: 0 auto;
      }

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

      .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
      }

      .form-group {
        margin-bottom: 1.5rem;
      }

      .form-group.full-width {
        grid-column: 1 / -1;
      }

      .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--green-dark);
        margin-bottom: 0.5rem;
      }

      .form-input,
      .form-select,
      .form-textarea {
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
      .form-select:focus,
      .form-textarea:focus {
        border-color: var(--green-main);
        background: var(--white);
      }

      .form-textarea {
        resize: vertical;
        min-height: 100px;
      }

      .form-error {
        color: var(--red);
        font-size: 0.8rem;
        margin-top: 0.25rem;
        display: block;
      }

      .ingredients-section {
        margin-top: 2rem;
      }

      .ingredients-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
      }

      .add-ingredient-btn {
        background: var(--orange);
        color: var(--white);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
      }

      .add-ingredient-btn:hover {
        background: #e55a0a;
        transform: translateY(-1px);
      }

      .ingredient-row {
        display: grid;
        grid-template-columns: 2fr 1fr auto;
        gap: 1rem;
        align-items: end;
        margin-bottom: 1rem;
        padding: 1rem;
        background: var(--bg);
        border-radius: 10px;
        border: 1px solid var(--border);
      }

      .remove-ingredient-btn {
        background: var(--red);
        color: var(--white);
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        cursor: pointer;
        display: grid;
        place-items: center;
        font-size: 1rem;
        transition: all 0.2s;
      }

      .remove-ingredient-btn:hover {
        background: #d32f2f;
        transform: scale(1.1);
      }

      .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border);
      }

      .file-input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
      }

      .file-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
      }

      .file-input-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        border: 2px dashed var(--border);
        border-radius: 10px;
        background: var(--bg);
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
      }

      .file-input-label:hover {
        border-color: var(--green-main);
        background: var(--green-pale);
      }

      .file-input-label i {
        font-size: 2rem;
        color: var(--grey);
        margin-bottom: 0.5rem;
      }

      .file-input-label span {
        font-size: 0.9rem;
        color: var(--grey);
      }

      .image-preview {
        margin-top: 1rem;
        max-width: 200px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border);
      }

      .image-preview img {
        width: 100%;
        height: auto;
        display: block;
      }

      .current-image {
        margin-bottom: 1rem;
        padding: 1rem;
        background: var(--bg);
        border-radius: 10px;
        border: 1px solid var(--border);
        text-align: center;
      }

      .current-image img {
        max-width: 150px;
        max-height: 100px;
        border-radius: 8px;
        margin-bottom: 0.5rem;
      }

      .current-image p {
        font-size: 0.8rem;
        color: var(--grey);
        margin: 0;
      }

      /* Responsive */
      @media (max-width: 1100px) {
        :root {
          --sidebar-w: 220px;
        }
      }
      @media (max-width: 820px) {
        .sidebar {
          transform: translateX(-100%);
        }
        .sidebar.open {
          transform: translateX(0);
        }
        .main-area {
          margin-left: 0;
        }
        .crud-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 1rem;
        }
        .crud-actions {
          width: 100%;
          justify-content: flex-end;
        }
        .form-grid {
          grid-template-columns: 1fr;
        }
        .ingredient-row {
          grid-template-columns: 1fr;
          gap: 0.5rem;
        }
        .form-actions {
          flex-direction: column;
        }
        .btn {
          justify-content: center;
        }
      }
    </style>
  </head>
  <body>
    <!-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
      <a class="sidebar-logo" href="index.php">
        <div class="logo-icon">
          <svg viewBox="0 0 32 32" fill="none" width="24" height="24">
            <path
              d="M16 4C10 4 5 8 4 14c4-2 9-1 12 3 3-4 8-5 12-3-1-6-6-10-12-10z"
              fill="#7ec44f"
            />
            <path
              d="M4 14c-1 5 2 10 7 12l5-8-5-4c-3 0-6 0-7 0z"
              fill="#4a9e30"
            />
            <path
              d="M28 14c1 5-2 10-7 12l-5-8 5-4c3 0 6 0 7 0z"
              fill="#2d6a1f"
            />
            <circle cx="16" cy="22" r="3" fill="#f07c1b" />
          </svg>
        </div>
        <span class="logo-text">Eco<span>Nutri</span></span>
        <span class="sidebar-admin-tag">Admin</span>
      </a>

      <!-- Main -->
      <div class="sidebar-section">
        <div class="sidebar-section-label">Principal</div>
        <a class="nav-item" href="index.php">
          <span class="nav-icon">📊</span> Tableau de bord
        </a>
        <a class="nav-item" href="#">
          <span class="nav-icon">👥</span> Utilisateurs
          <span class="nav-badge">1 248</span>
        </a>
        <a class="nav-item active" href="listRecette.php">
          <span class="nav-icon">🍽️</span> Recettes
          <span class="nav-badge green">240</span>
        </a>
        <a class="nav-item" href="listAliment.php">
          <span class="nav-icon">🥕</span> Aliments
          <span class="nav-badge orange">156</span>
        </a>
        <a class="nav-item" href="statistiques.php">
          <span class="nav-icon">📈</span> Statistiques
        </a>
      </div>

      <!-- Modules -->
      <div class="sidebar-section">
        <div class="sidebar-section-label">Modules</div>
        <a class="nav-item" href="#">
          <span class="nav-icon">🎯</span> Profils Nutritionnels
        </a>
        <a class="nav-item" href="#">
          <span class="nav-icon">📋</span> Suivi Alimentaire
        </a>
        <a class="nav-item" href="#">
          <span class="nav-icon">🤖</span> IA &amp; Recommandations
        </a>
        <a class="nav-item" href="#">
          <span class="nav-icon">🥕</span> Ingrédients
        </a>
      </div>

      <!-- Config -->
      <div class="sidebar-section">
        <div class="sidebar-section-label">Configuration</div>
        <a class="nav-item" href="#">
          <span class="nav-icon">⚙️</span> Paramètres
        </a>
        <a class="nav-item" href="#">
          <span class="nav-icon">📄</span> Rapports
        </a>
      </div>

      <div class="sidebar-footer">
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

    <!-- ══════════════════════════════════════════
     MAIN AREA
══════════════════════════════════════════ -->
    <div class="main-area">
      <!-- TOP BAR -->
      <div class="topbar">
        <div class="topbar-left">
          <div class="page-title">
            <h1>Modifier une Recette</h1>
            <span><?php echo htmlspecialchars($recette->nom); ?></span>
          </div>
        </div>

        <div class="topbar-search">
          <svg
            width="15"
            height="15"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
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
          <h1 class="crud-title">🔄 Modifier une Recette</h1>
          <div class="crud-actions">
            <a href="listRecette.php" class="btn btn-secondary">
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
          <form method="POST" enctype="multipart/form-data" id="recetteForm">
            <div class="form-section">
              <h2>📝 Informations générales</h2>
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="nom">Nom de la recette *</label>
                  <input type="text" id="nom" name="nom" class="form-input" value="<?php echo htmlspecialchars($recette->nom); ?>" required maxlength="150">
                  <?php if (isset($errors['nom'])): ?>
                    <span class="form-error">⚠️ <?php echo htmlspecialchars($errors['nom']); ?></span>
                  <?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label" for="categorie">Catégorie</label>
                  <select id="categorie" name="categorie" class="form-select">
                    <option value="plat" <?php echo ($recette->categorie ?? '') === 'plat' ? 'selected' : ''; ?>>Plat principal</option>
                    <option value="entree" <?php echo ($recette->categorie ?? '') === 'entree' ? 'selected' : ''; ?>>Entrée</option>
                    <option value="dessert" <?php echo ($recette->categorie ?? '') === 'dessert' ? 'selected' : ''; ?>>Dessert</option>
                    <option value="boisson" <?php echo ($recette->categorie ?? '') === 'boisson' ? 'selected' : ''; ?>>Boisson</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label" for="temps_preparation">Temps de préparation (min) *</label>
                  <input type="number" id="temps_preparation" name="temps_preparation" class="form-input" value="<?php echo htmlspecialchars($recette->temps_preparation); ?>" required min="1" max="1440">
                  <?php if (isset($errors['temps_preparation'])): ?>
                    <span class="form-error">⚠️ <?php echo htmlspecialchars($errors['temps_preparation']); ?></span>
                  <?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label" for="difficulte">Difficulté *</label>
                  <select id="difficulte" name="difficulte" class="form-select" required>
                    <option value="facile" <?php echo $recette->difficulte === 'facile' ? 'selected' : ''; ?>>😊 Facile</option>
                    <option value="moyen" <?php echo $recette->difficulte === 'moyen' ? 'selected' : ''; ?>>🔥 Moyen</option>
                    <option value="difficile" <?php echo $recette->difficulte === 'difficile' ? 'selected' : ''; ?>>💪 Difficile</option>
                  </select>
                  <?php if (isset($errors['difficulte'])): ?>
                    <span class="form-error">⚠️ <?php echo htmlspecialchars($errors['difficulte']); ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="form-group full-width">
                <label class="form-label" for="description">Description *</label>
                <textarea id="description" name="description" class="form-textarea" required><?php echo htmlspecialchars($recette->description); ?></textarea>
                <?php if (isset($errors['description'])): ?>
                  <span class="form-error">⚠️ <?php echo htmlspecialchars($errors['description']); ?></span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-section">
              <h2>🥕 Ingrédients</h2>
              <?php if (isset($errors['ingredients'])): ?>
                <div class="alert alert-error" style="margin-bottom: 1rem;">
                  <span>⚠️</span>
                  <span><?php echo htmlspecialchars($errors['ingredients']); ?></span>
                </div>
              <?php endif; ?>
              <div id="ingredientsContainer">
                <?php
                $displayIngredients = !empty($currentIngredients) ? $currentIngredients : [['aliment_id' => '', 'quantite' => '', 'nom' => '', 'calories' => 0]];
                foreach ($displayIngredients as $index => $ing):
                ?>
                <div class="ingredient-row">
                  <div class="form-group">
                    <label class="form-label">Aliment</label>
                    <select name="ing_aliment[]" class="form-select" required>
                      <option value="">Sélectionner un aliment</option>
                      <?php foreach ($allAliments as $aliment): ?>
                        <option value="<?= $aliment->id ?>" <?= ((string)($ing['aliment_id'] ?? '')) === (string)$aliment->id ? 'selected' : '' ?>>
                          <?= htmlspecialchars($aliment->nom) ?> (<?= $aliment->calories ?> kcal)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Quantité</label>
                    <input type="text" name="ing_quantite[]" class="form-input" placeholder="ex: 100g" maxlength="50" value="<?php echo htmlspecialchars($ing['quantite'] ?? ''); ?>" required>
                  </div>
                  <button type="button" class="remove-ingredient-btn" onclick="removeIngredient(this)" <?php echo count($displayIngredients) <= 1 ? 'style="display: none;"' : ''; ?>>×</button>
                </div>
                <?php endforeach; ?>
              </div>
              <div class="ingredients-header">
                <button type="button" class="add-ingredient-btn" onclick="addIngredient()">
                  <span>+</span> Ajouter un ingrédient
                </button>
              </div>
            </div>

            <div class="form-section">
              <h2>📸 Image de la recette</h2>
              <?php if ($recette->image && file_exists('../' . $recette->image)): ?>
              <div class="current-image">
                <img src="../<?php echo htmlspecialchars($recette->image); ?>" alt="Image actuelle">
                <p>Image actuelle</p>
              </div>
              <?php endif; ?>
              <div class="form-group full-width">
                <div class="file-input-wrapper">
                  <input type="file" id="image" name="image" class="file-input" accept="image/*" onchange="previewImage(this)">
                  <label for="image" class="file-input-label">
                    <i>📷</i>
                    <span><?php echo $recette->image ? 'Changer l\'image' : 'Ajouter une image'; ?></span>
                    <div style="font-size: 0.8rem; margin-top: 0.25rem; color: var(--grey);">JPG, PNG, WebP — max 5 Mo</div>
                  </label>
                </div>
                <div id="imagePreview" class="image-preview" style="display: none;"></div>
              </div>
            </div>

            <div class="form-actions">
              <a href="listRecette.php" class="btn btn-secondary">Annuler</a>
              <button type="submit" class="btn btn-primary">
                <span>✓</span> Mettre à jour la recette
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      function addIngredient() {
        const container = document.getElementById('ingredientsContainer');
        const rows = container.querySelectorAll('.ingredient-row');
        const newRow = rows[0].cloneNode(true);

        // Reset values
        newRow.querySelector('select').value = '';
        newRow.querySelector('input').value = '';
        newRow.querySelector('.remove-ingredient-btn').style.display = 'grid';

        container.appendChild(newRow);
      }

      function removeIngredient(button) {
        const row = button.closest('.ingredient-row');
        const container = document.getElementById('ingredientsContainer');
        const rows = container.querySelectorAll('.ingredient-row');

        if (rows.length > 1) {
          row.remove();
        }
      }

      function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const label = document.querySelector('.file-input-label');

        if (input.files && input.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            preview.style.display = 'block';
            label.style.display = 'none';
          };
          reader.readAsDataURL(input.files[0]);
        } else {
          preview.style.display = 'none';
          label.style.display = 'flex';
        }
      }

      // Client-side validation
      document.getElementById('recetteForm').addEventListener('submit', function(e) {
        let valid = true;
        const requiredFields = ['nom', 'description', 'temps_preparation', 'difficulte'];

        requiredFields.forEach(fieldName => {
          const field = this.elements[fieldName];
          const value = field.value.trim();

          // Remove existing error styling
          field.classList.remove('is-invalid');
          const existingError = field.parentNode.querySelector('.client-error');
          if (existingError) {
            existingError.remove();
          }

          if (value === '') {
            field.classList.add('is-invalid');
            const errorDiv = document.createElement('span');
            errorDiv.className = 'form-error client-error';
            errorDiv.textContent = '⚠️ Ce champ est obligatoire.';
            field.parentNode.appendChild(errorDiv);
            valid = false;
          }
        });

        // Check ingredients
        const ingredientRows = document.querySelectorAll('#ingredientsContainer .ingredient-row');
        let ingredientsValid = ingredientRows.length > 0;

        ingredientRows.forEach(row => {
          const select = row.querySelector('select');
          const input = row.querySelector('input');

          select.style.borderColor = '';
          input.style.borderColor = '';

          if (!select.value) {
            select.style.borderColor = 'var(--red)';
            ingredientsValid = false;
          }
          if (!input.value.trim()) {
            input.style.borderColor = 'var(--red)';
            ingredientsValid = false;
          }
        });

        if (!ingredientsValid) {
          // Show error for ingredients
          const existingError = document.querySelector('.alert-error span:last-child');
          if (existingError && existingError.textContent.includes('ingrédient')) {
            // Error already shown
          } else {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-error';
            alertDiv.innerHTML = '<span>⚠️</span><span>Veuillez ajouter au moins un ingrédient avec sa quantité.</span>';
            const formContainer = document.querySelector('.form-container');
            formContainer.insertBefore(alertDiv, formContainer.firstChild);
          }
          valid = false;
        }

        if (!valid) {
          e.preventDefault();
        }
      });
    </script>
  </body>
</html>
