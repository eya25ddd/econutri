<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Categorie.php';
require_once __DIR__ . '/../../controllers/CategorieController.php';

$controller = new CategorieController();

// Handle AJAX delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    header('Content-Type: application/json');
    $id     = (int) ($_POST['id'] ?? 0);
    $result = $controller->delete($id);
    echo json_encode($result);
    exit;
}

$categories = $controller->getAll();

// Flash messages
$success = $_GET['success'] ?? '';
$error   = $_GET['error']   ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoNutri – Catégories</title>
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
        border-bottom: 1px solid rgba(255,255,255,0.07);
        display: flex;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
      }
      .logo-icon {
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        display: grid; place-items: center;
        border: 1px solid rgba(255,255,255,0.2);
        flex-shrink: 0;
      }
      .logo-text {
        font-family: "Playfair Display", serif;
        font-size: 1.35rem; color: var(--white);
        letter-spacing: -0.4px;
      }
      .logo-text span { color: var(--orange); }
      .sidebar-admin-tag {
        background: rgba(240,124,27,0.18); color: var(--orange);
        font-size: 0.65rem; font-weight: 700;
        padding: 0.15rem 0.5rem; border-radius: 4px;
        text-transform: uppercase; letter-spacing: 0.6px;
        margin-left: auto;
      }
      .sidebar-section { padding: 1.2rem 0.9rem 0.4rem; }
      .sidebar-section-label {
        font-size: 0.65rem; font-weight: 700;
        color: rgba(255,255,255,0.3);
        text-transform: uppercase; letter-spacing: 0.8px;
        padding: 0 0.7rem; margin-bottom: 0.5rem;
      }
      .nav-item {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0.9rem; border-radius: 10px;
        cursor: pointer; transition: background 0.2s;
        text-decoration: none; color: rgba(255,255,255,0.6);
        font-size: 0.88rem; font-weight: 500;
        margin-bottom: 0.15rem; position: relative;
      }
      .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
      .nav-item.active {
        background: linear-gradient(90deg, rgba(74,158,48,0.35), rgba(74,158,48,0.1));
        color: var(--white); border-left: 3px solid var(--green-light);
      }
      .nav-icon { font-size: 1.1rem; width: 22px; text-align: center; flex-shrink: 0; }
      .nav-badge {
        margin-left: auto; background: var(--orange); color: var(--white);
        font-size: 0.65rem; font-weight: 700; padding: 0.15rem 0.5rem;
        border-radius: 50px; min-width: 20px; text-align: center;
      }
      .nav-badge.green { background: var(--green-main); }
      .nav-badge.orange { background: var(--orange); }
      .sidebar-footer {
        margin-top: auto; padding: 1rem 0.9rem;
        border-top: 1px solid rgba(255,255,255,0.07);
      }
      .admin-profile {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.7rem 0.9rem; border-radius: 10px;
        cursor: pointer; transition: background 0.2s;
      }
      .admin-profile:hover { background: rgba(255,255,255,0.07); }
      .admin-av {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, var(--green-main), var(--green-dark));
        display: grid; place-items: center;
        font-size: 0.75rem; font-weight: 700; color: var(--white);
        flex-shrink: 0; border: 2px solid rgba(126,196,79,0.4);
      }
      .admin-info strong { display: block; font-size: 0.83rem; color: var(--white); font-weight: 600; }
      .admin-info span { font-size: 0.72rem; color: rgba(255,255,255,0.45); }
      .admin-profile .logout-icon { margin-left: auto; color: rgba(255,255,255,0.3); font-size: 0.9rem; transition: color 0.2s; }
      .admin-profile:hover .logout-icon { color: var(--orange); }

      .main-area { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

      .topbar {
        height: var(--topbar-h); background: var(--white);
        border-bottom: 1px solid var(--border); padding: 0 2rem;
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; z-index: 40;
        box-shadow: 0 2px 12px rgba(45,106,31,0.06);
      }
      .topbar-left { display: flex; align-items: center; gap: 1rem; }
      .page-title h1 { font-family: "Playfair Display", serif; font-size: 1.3rem; color: var(--green-dark); line-height: 1.1; }
      .page-title span { font-size: 0.78rem; color: var(--grey-light); font-weight: 400; }
      .topbar-search {
        display: flex; align-items: center; background: var(--bg);
        border: 1.5px solid var(--border); border-radius: 10px;
        padding: 0.45rem 0.9rem; gap: 0.5rem; width: 260px;
      }
      .topbar-search input {
        border: none; outline: none; background: transparent;
        font-family: "DM Sans", sans-serif; font-size: 0.85rem; color: var(--black); width: 100%;
      }
      .topbar-search svg { color: var(--grey-light); flex-shrink: 0; }
      .topbar-right { display: flex; align-items: center; gap: 1rem; }
      .topbar-icon-btn {
        width: 38px; height: 38px; border-radius: 10px;
        background: var(--bg); border: 1.5px solid var(--border);
        display: grid; place-items: center; cursor: pointer;
        transition: all 0.2s; position: relative;
      }
      .topbar-icon-btn:hover { background: var(--green-pale); border-color: var(--green-main); }
      .topbar-icon-btn .notif-dot {
        position: absolute; top: 7px; right: 7px;
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--orange); border: 2px solid var(--white);
      }
      .topbar-date {
        font-size: 0.8rem; color: var(--grey-light);
        background: var(--bg); border: 1.5px solid var(--border);
        border-radius: 10px; padding: 0.45rem 0.9rem; white-space: nowrap;
      }

      .content { padding: 2rem; flex: 1; }

      .crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
      .crud-title { font-family: "Playfair Display", serif; font-size: 1.8rem; color: var(--green-dark); margin: 0; }
      .crud-actions { display: flex; gap: 1rem; }

      .btn {
        padding: 0.6rem 1.2rem; border: none; border-radius: 10px;
        font-family: "DM Sans", sans-serif; font-size: 0.9rem; font-weight: 600;
        cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s;
      }
      .btn-primary { background: linear-gradient(135deg, var(--green-main), var(--green-dark)); color: var(--white); }
      .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(45,106,31,0.3); }

      .alert {
        padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 0.75rem; font-weight: 500;
      }
      .alert-success { background: var(--green-pale); color: var(--green-dark); border: 1px solid var(--green-light); }
      .alert-error { background: var(--red-light); color: var(--red); border: 1px solid #ffcdd2; }

      .table-toolbar { display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center; flex-wrap: wrap; }
      .search-input {
        flex: 1; min-width: 200px;
        padding: 0.75rem 1rem; border: 1.5px solid var(--border);
        border-radius: 10px; font-family: "DM Sans", sans-serif;
        font-size: 0.9rem; background: var(--bg); outline: none; transition: border-color 0.2s;
      }
      .search-input:focus { border-color: var(--green-main); background: var(--white); }
      .filter-select {
        padding: 0.75rem 1rem; border: 1.5px solid var(--border);
        border-radius: 10px; font-family: "DM Sans", sans-serif;
        font-size: 0.9rem; background: var(--bg); outline: none; transition: border-color 0.2s; cursor: pointer;
      }
      .filter-select:focus { border-color: var(--green-main); background: var(--white); }
      .count-badge {
        background: var(--green-pale); color: var(--green-dark);
        border: 1px solid var(--border); border-radius: 50px;
        padding: 0.35rem 0.9rem; font-size: 0.82rem; font-weight: 600; white-space: nowrap;
      }

      .categories-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }

      .cat-card {
        background: var(--card-bg); border: 1.5px solid var(--border);
        border-radius: 16px; overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(45,106,31,0.06);
        display: flex; flex-direction: column;
      }
      .cat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(45,106,31,0.15);
        border-color: var(--green-light);
      }

      .cat-card-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; gap: 1rem; }
      .cat-card-title { font-family: "Playfair Display", serif; font-size: 1.2rem; color: var(--green-dark); margin: 0; }
      .cat-card-actions { display: flex; gap: 0.75rem; justify-content: flex-end; flex-wrap: wrap; margin-top: auto; }

      .btn-edit, .btn-del {
        padding: 0.55rem 1rem; border-radius: 10px; border: 1.5px solid transparent;
        font-family: "DM Sans", sans-serif; font-size: 0.85rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.4rem;
        text-decoration: none; cursor: pointer; transition: all 0.2s ease;
      }
      .btn-edit { background: var(--green-pale); color: var(--green-dark); border-color: var(--border); }
      .btn-edit:hover { background: var(--green-light); color: var(--white); border-color: var(--green-light); }
      .btn-del { background: #fff0f0; color: #c0392b; border-color: #f7c6c6; }
      .btn-del:hover { background: #f8d7da; color: #842029); }

      .empty-state {
        text-align: center; padding: 4rem 2rem; color: var(--grey);
        background: var(--card-bg); border: 1.5px dashed var(--border); border-radius: 18px;
      }
      .empty-state .empty-icon { font-size: 4rem; margin-bottom: 1rem; display: block; }
      .empty-state h3 { font-family: "Playfair Display", serif; font-size: 1.3rem; color: var(--green-dark); margin-bottom: 0.5rem; }
      .empty-state p { font-size: 0.9rem; color: var(--grey-light); }

      .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.5);
        z-index: 200; display: grid; place-items: center;
        opacity: 0; pointer-events: none; transition: opacity 0.3s; backdrop-filter: blur(4px);
      }
      .modal-overlay.open { opacity: 1; pointer-events: all; }
      .modal {
        background: var(--white); border-radius: 22px;
        width: min(420px, 94vw); max-height: 80vh; overflow-y: auto;
        transform: translateY(28px) scale(0.97); transition: transform 0.3s;
        box-shadow: 0 20px 60px rgba(0,0,0,0.22);
      }
      .modal-overlay.open .modal { transform: none; }
      .modal-head {
        background: linear-gradient(135deg, var(--green-dark), var(--green-main));
        padding: 1.4rem 1.8rem; display: flex; align-items: center;
        justify-content: space-between; border-radius: 22px 22px 0 0;
      }
      .modal-head h2 { font-family: "Playfair Display", serif; font-size: 1.15rem; color: var(--white); }
      .modal-head p { font-size: 0.78rem; color: rgba(255,255,255,0.7); margin-top: 0.15rem; }
      .modal-x {
        background: rgba(255,255,255,0.2); border: none; color: var(--white);
        width: 34px; height: 34px; border-radius: 50%; cursor: pointer;
        display: grid; place-items: center; font-size: 1rem;
      }
      .modal-x:hover { background: rgba(255,255,255,0.3); }
      .modal-content { padding: 1.6rem; font-size: 0.95rem; color: var(--grey); line-height: 1.5; }
      .modal-foot {
        padding: 1rem 1.6rem; border-top: 1px solid var(--border);
        display: flex; justify-content: flex-end; gap: 0.7rem;
      }
      .btn-cancel {
        padding: 0.6rem 1.3rem; border: 1.5px solid var(--border);
        border-radius: 50px; background: transparent;
        font-family: "DM Sans", sans-serif; font-size: 0.87rem; color: var(--grey); cursor: pointer;
      }
      .btn-confirm {
        padding: 0.6rem 1.6rem; border: none; border-radius: 50px;
        background: linear-gradient(135deg, var(--red), #d32f2f);
        color: var(--white); font-family: "DM Sans", sans-serif;
        font-size: 0.87rem; font-weight: 700; cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(229,57,53,0.3); }

      .toast {
        position: fixed; bottom: 1.8rem; right: 2rem;
        background: var(--green-dark); color: var(--white);
        padding: 0.9rem 1.4rem; border-radius: 14px;
        font-size: 0.88rem; font-weight: 500; z-index: 300;
        box-shadow: 0 8px 28px rgba(0,0,0,0.2);
        transform: translateY(70px); opacity: 0;
        transition: transform 0.35s, opacity 0.35s;
        display: flex; align-items: center; gap: 0.5rem;
      }
      .toast.show { transform: translateY(0); opacity: 1; }
      .toast.error { background: var(--red); }

      @media (max-width: 1100px) {
        :root { --sidebar-w: 220px; }
        .categories-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
      }
      @media (max-width: 820px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .main-area { margin-left: 0; }
        .crud-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .crud-actions { width: 100%; justify-content: flex-end; }
        .table-toolbar { flex-direction: column; align-items: stretch; }
        .categories-grid { grid-template-columns: 1fr; }
      }
    </style>
  </head>
  <body data-bo-page="categories">
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
            <h1>Gestion des Catégories</h1>
            <span>Administration des catégories EcoNutri</span>
          </div>
        </div>
        <div class="topbar-search">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" placeholder="Rechercher…" />
        </div>
        <div class="topbar-right">
          <div class="topbar-date">📅 <?php echo date('d F Y'); ?></div>
          <button id="boDarkBtn" onclick="boToggleDark()" title="Mode sombre/clair" style="background:var(--bg);border:1.5px solid var(--border);border-radius:10px;width:38px;height:38px;cursor:pointer;font-size:1.1rem;display:grid;place-items:center;">🌙</button>
          <div id="boLangMenu" style="position:relative;">
            <button onclick="boToggleLangMenu()" style="background:var(--bg);border:1.5px solid var(--border);border-radius:10px;padding:.4rem .8rem;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.82rem;font-weight:700;color:var(--green-dark);display:flex;align-items:center;gap:.3rem;">🌐 <span id="boLangLabel">FR</span> ▾</button>
            <div style="position:absolute;top:calc(100% + 6px);right:0;background:var(--white);border:1.5px solid var(--border);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);overflow:hidden;display:none;min-width:120px;z-index:200;" id="boLangDropdown">
              <button onclick="boSetLang('fr')" style="display:flex;align-items:center;gap:.5rem;width:100%;padding:.55rem 1rem;background:none;border:none;font-family:'DM Sans',sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;color:var(--green-dark);">🇫🇷 Français</button>
              <button onclick="boSetLang('en')" style="display:flex;align-items:center;gap:.5rem;width:100%;padding:.55rem 1rem;background:none;border:none;font-family:'DM Sans',sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;color:var(--green-dark);">🇸🇦 العربية</button>
              <button onclick="boSetLang('ar')" style="display:flex;align-items:center;gap:.5rem;width:100%;padding:.55rem 1rem;background:none;border:none;font-family:'DM Sans',sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;color:var(--green-dark);">🇬🇧 English</button>
            </div>
          </div>
          <div class="topbar-icon-btn" title="Notifications">🔔<span class="notif-dot"></span></div>
          <div class="topbar-icon-btn" title="Messages">💬</div>
        </div>
      </div>

      <div class="content">
        <?php if ($success): ?>
        <div class="alert alert-success"><span>✅</span><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-error"><span>❌</span><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="crud-header">
          <h1 class="crud-title">🏷️ Gestion des Catégories</h1>
          <div class="crud-actions">
            <a href="addCategorie.php" class="btn btn-primary"><span>+</span> Ajouter une Catégorie</a>
          </div>
        </div>

        <div class="table-toolbar">
          <input type="text" class="search-input" id="searchInput" placeholder="🔍 Rechercher une catégorie…" oninput="filterCards()">
          <select class="filter-select" id="sortSelect" onchange="sortCategories()">
            <option value="">Trier par...</option>
            <option value="asc">🔤 A → Z</option>
            <option value="desc">🔤 Z → A</option>
          </select>
          <span class="count-badge" id="countBadge"><?= count($categories) ?> catégorie<?= count($categories) !== 1 ? 's' : '' ?></span>
        </div>

        <?php if (empty($categories)): ?>
        <div class="empty-state">
          <span class="empty-icon">🏷️</span>
          <h3>Aucune catégorie trouvée</h3>
          <p>Il n'y a encore aucune catégorie dans le système. Commencez par en ajouter une !</p>
          <a href="addCategorie.php" class="btn btn-primary" style="margin-top:1.5rem;display:inline-flex;">
            <span>+</span> Ajouter la première catégorie
          </a>
        </div>
        <?php else: ?>
        <div class="categories-grid" id="categoriesGrid">
          <?php foreach ($categories as $c): ?>
          <div class="cat-card" data-name="<?= htmlspecialchars(strtolower($c->nom)) ?>">
            <div class="cat-card-body">
              <h3 class="cat-card-title">🏷️ <?= htmlspecialchars($c->nom) ?></h3>
              <div class="cat-card-actions">
                <a href="editCategorie.php?id=<?= $c->id ?>" class="btn-edit">✏️ Modifier</a>
                <button class="btn-del" onclick="confirmDelete(<?= $c->id ?>, '<?= htmlspecialchars(addslashes($c->nom)) ?>')">
                  🗑️ Supprimer
                </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
      <div class="modal">
        <div class="modal-head">
          <div>
            <h2>Confirmer la suppression</h2>
            <p>Cette action est irréversible.</p>
          </div>
          <button class="modal-x" onclick="closeModal()">×</button>
        </div>
        <div class="modal-content">
          <p id="deleteMessage">Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
        </div>
        <div class="modal-foot">
          <button class="btn-cancel" onclick="closeModal()">Annuler</button>
          <button class="btn-confirm" id="confirmDeleteBtn">🗑️ Supprimer</button>
        </div>
      </div>
    </div>

    <script>
      let deleteId = null;

      function confirmDelete(id, name) {
        deleteId = id;
        document.getElementById('deleteMessage').textContent =
          `Êtes-vous sûr de vouloir supprimer "${name}" ? Cette action est irréversible.`;
        document.getElementById('deleteModal').classList.add('open');
      }

      function closeModal() {
        document.getElementById('deleteModal').classList.remove('open');
        deleteId = null;
      }

      document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (!deleteId) return;
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', deleteId);

        fetch('listCategorie.php', { method: 'POST', body: formData })
          .then(r => r.json())
          .then(data => {
            closeModal();
            if (data.success) {
              showToast('Catégorie supprimée avec succès', 'success');
              setTimeout(() => location.reload(), 1500);
            } else {
              showToast('Erreur : ' + (data.message || 'Erreur inconnue'), 'error');
            }
          })
          .catch(() => {
            closeModal();
            showToast('Erreur réseau lors de la suppression', 'error');
          });
      });

      function filterCards() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('#categoriesGrid .cat-card');
        let visible = 0;
        cards.forEach(c => {
          const show = c.dataset.name.includes(q);
          c.style.display = show ? '' : 'none';
          if (show) visible++;
        });
        document.getElementById('countBadge').textContent =
          visible + ' catégorie' + (visible !== 1 ? 's' : '');
      }

      function sortCategories() {
        const sortValue = document.getElementById('sortSelect').value;
        if (!sortValue) return;

        const grid = document.getElementById('categoriesGrid');
        const cards = Array.from(grid.querySelectorAll('.cat-card'));

        cards.sort((a, b) => {
          const nameA = a.dataset.name;
          const nameB = b.dataset.name;
          
          if (sortValue === 'asc') {
            return nameA.localeCompare(nameB);
          } else {
            return nameB.localeCompare(nameA);
          }
        });

        // Re-append cards in sorted order
        cards.forEach(card => grid.appendChild(card));
      }

      function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast' + (type === 'error' ? ' error' : '');
        toast.innerHTML = `<span>${type === 'success' ? '✅' : '❌'}</span>${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
          toast.classList.remove('show');
          setTimeout(() => toast.remove(), 350);
        }, 3000);
      }

      document.getElementById('deleteModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
      });
    </script>
    <script src="../../assets/backoffice-utils.js"></script>
  </body>
</html>
