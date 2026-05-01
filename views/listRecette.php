<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Recette.php';
require_once __DIR__ . '/../models/RecetteAliment.php';
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../controllers/RecetteController.php';
require_once __DIR__ . '/../controllers/CategorieController.php';
require_once __DIR__ . '/../controllers/CommandeController.php';

$controller = new RecetteController();
$categorieController = new CategorieController();
$commandeController = new CommandeController();

// Handle AJAX delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    header('Content-Type: application/json');
    $id     = (int) ($_POST['id'] ?? 0);
    $result = $controller->delete($id);
    echo json_encode($result);
    exit;
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'order') {
    header('Content-Type: application/json');
    $result = $commandeController->create([
        'user_name'  => $_POST['user_name'] ?? '',
        'user_email' => $_POST['user_email'] ?? '',
        'user_phone' => $_POST['user_phone'] ?? '',
        'recettes'   => $_POST['recettes'] ?? '',
    ]);
    echo json_encode($result);
    exit;
}

// Handle check orders by email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'check_orders') {
    header('Content-Type: application/json');
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide.']);
        exit;
    }
    $orders = $commandeController->getByEmail($email);
    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

// Get filter parameters
$selectedCategorieId = (int) ($_GET['categorie'] ?? 0);

// Get all recettes
$allRecettes = $controller->getAllWithNutrition();

// Get only categories that have recettes assigned
$allCategories = [];
foreach ($categorieController->getAll() as $cat) {
    $recetteIds = $categorieController->getRecetteIdsForCategory($cat->id);
    if (!empty($recetteIds)) {
        $allCategories[] = $cat;
    }
}

// Filter recettes by category if selected
if ($selectedCategorieId > 0) {
    $recetteIds = $categorieController->getRecetteIdsForCategory($selectedCategorieId);
    $recettes = array_filter($allRecettes, fn($r) => in_array($r->id, $recetteIds));
} else {
    $recettes = $allRecettes;
}

// ── Statistics ──
$total      = count($allRecettes);
$facile     = count(array_filter($allRecettes, fn($r) => $r->difficulte === 'facile'));
$moyen      = count(array_filter($allRecettes, fn($r) => $r->difficulte === 'moyen'));
$difficile  = count(array_filter($allRecettes, fn($r) => $r->difficulte === 'difficile'));
$avgTime    = $total > 0 ? round(array_sum(array_map(fn($r) => $r->temps_preparation, $allRecettes)) / $total) : 0;
$maxTime    = $total > 0 ? max(array_map(fn($r) => $r->temps_preparation, $allRecettes)) : 0;
$minTime    = $total > 0 ? min(array_map(fn($r) => $r->temps_preparation, $allRecettes)) : 0;

// ── Build JSON for JS smart filter & recommendations ──
$recettesJson = json_encode(array_values(array_map(fn($r) => [
    'id'          => $r->id,
    'nom'         => $r->nom,
    'description' => $r->description,
    'difficulte'  => $r->difficulte,
    'temps'       => $r->temps_preparation,
    'calories'    => $r->total_calories,
    'proteines'   => round($r->total_proteines, 1),
    'glucides'    => round($r->total_glucides, 1),
    'lipides'     => round($r->total_lipides, 1),
    'ingredients' => $r->ingredient_names,
    'image'       => $r->image ?? '',
    'nb_aliments' => $r->nb_aliments,
    'date'        => $r->date_creation,
], $allRecettes)));

// Flash messages
$success = $_GET['success'] ?? '';
$error   = $_GET['error']   ?? '';

$pageTitle = 'Recettes';
$activeNav = 'recettes';
include __DIR__ . '/header.php';
?>

<style>
  .page-hero{background:linear-gradient(135deg,var(--green-dark) 0%,var(--green-main) 60%,var(--green-light) 100%);padding:3rem 5rem 2.5rem;color:var(--white);}
  .page-hero-inner{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;}
  .page-hero h1{font-family:"Playfair Display",serif;font-size:2.2rem;margin-bottom:.3rem;}
  .page-hero p{font-size:.95rem;opacity:.85;}
  .breadcrumb{font-size:.8rem;opacity:.7;margin-bottom:.8rem;}
  .breadcrumb a{color:var(--white);text-decoration:none;}
  .breadcrumb a:hover{text-decoration:underline;}

  .btn-add{background:var(--orange);color:var(--white);border:none;padding:.7rem 1.6rem;border-radius:50px;font-family:"DM Sans",sans-serif;font-size:.92rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.5rem;text-decoration:none;transition:transform .2s,box-shadow .2s;}
  .btn-add:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(240,124,27,.4);}

  .content-section{padding:3rem 5rem;}

  /* Flash */
  .flash{padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;font-size:.9rem;font-weight:500;display:flex;align-items:center;gap:.7rem;}
  .flash-success{background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;}
  .flash-error{background:#f8d7da;color:#842029;border:1px solid #f5c2c7;}

  /* Grid of recipe cards */
  .recipes-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.6rem;}

  .rcard{background:var(--white);border-radius:20px;overflow:hidden;border:1px solid var(--border);transition:transform .25s,box-shadow .25s;display:flex;flex-direction:column;}
  .rcard:hover{transform:translateY(-4px);box-shadow:0 14px 36px rgba(45,106,31,.16);}
  .rcard-img{position:relative;height:180px;overflow:hidden;background:var(--green-pale);}
  .rcard-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
  .rcard:hover .rcard-img img{transform:scale(1.06);}
  .rcard-img-placeholder{width:100%;height:100%;display:grid;place-items:center;font-size:4rem;}
  .rcard-diff{position:absolute;top:.8rem;left:.8rem;font-size:.7rem;font-weight:700;padding:.25rem .7rem;border-radius:50px;text-transform:uppercase;}
  .diff-facile{background:#d1e7dd;color:#0f5132;}
  .diff-moyen{background:#fff3cd;color:#856404;}
  .diff-difficile{background:#f8d7da;color:#842029;}
  .rcard-body{padding:1.2rem;flex:1;display:flex;flex-direction:column;}
  .rcard-title{font-family:"Playfair Display",serif;font-size:1rem;margin-bottom:.5rem;color:var(--black);}
  .rcard-desc{font-size:.82rem;color:var(--grey);line-height:1.5;flex:1;margin-bottom:.8rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
  .rcard-meta{display:flex;gap:.8rem;font-size:.75rem;color:var(--grey);margin-bottom:1rem;flex-wrap:wrap;}
  .rcard-meta span{display:flex;align-items:center;gap:.25rem;}
  .rcard-actions{display:flex;gap:.5rem;}
  .btn-edit{background:var(--green-pale);color:var(--green-dark);border:1.5px solid var(--border);padding:.45rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;text-decoration:none;transition:all .2s;flex:1;text-align:center;}
  .btn-edit:hover{background:var(--green-main);color:var(--white);border-color:var(--green-main);}
  .btn-del{background:#fff0f0;color:#c0392b;border:1.5px solid #fcc;padding:.45rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;flex:1;text-align:center;}
  .btn-del:hover{background:#c0392b;color:var(--white);border-color:#c0392b;}

  /* Toolbar */
  .table-toolbar{display:flex;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;}
  .search-input{flex:1;min-width:200px;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:"DM Sans",sans-serif;font-size:.88rem;outline:none;transition:border-color .2s;background:var(--white);}
  .search-input:focus{border-color:var(--green-main);}
  .count-badge{background:var(--green-pale);color:var(--green-dark);font-size:.78rem;font-weight:700;padding:.3rem .8rem;border-radius:50px;white-space:nowrap;}
  .filter-select{padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:"DM Sans",sans-serif;font-size:.88rem;outline:none;background:var(--white);cursor:pointer;}
  .filter-select:focus{border-color:var(--green-main);}

  .empty-state{padding:4rem;text-align:center;color:var(--grey);}
  .empty-state .ei{font-size:3.5rem;margin-bottom:1rem;}

  /* ── STATS SECTION ── */
  .stats-section{padding:1.5rem 5rem 0;display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;}

  /* ── SMART PANELS ── */
  .smart-panel-wrap{padding:2rem 5rem;display:flex;flex-direction:column;gap:1rem;}
  .smart-panel{background:var(--white);border:1.5px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(45,106,31,.06);}
  .sp-header{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.4rem;cursor:pointer;font-weight:700;font-size:.95rem;color:var(--green-dark);user-select:none;transition:background .2s;}
  .sp-header:hover{background:var(--green-pale);}
  .sp-arrow{font-size:.8rem;transition:transform .3s;}
  .sp-body{padding:1.2rem 1.4rem;border-top:1px solid var(--border);}
  .sp-body.collapsed{display:none;}
  .sp-section-label{font-size:.8rem;font-weight:700;color:var(--green-dark);margin-bottom:.5rem;}
  .chip-row{display:flex;flex-wrap:wrap;gap:.5rem;}
  .chip{padding:.4rem .9rem;border-radius:50px;border:1.5px solid var(--border);background:var(--white);font-size:.82rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;color:var(--grey);transition:all .2s;}
  .chip:hover{border-color:var(--green-main);color:var(--green-main);}
  .chip.active{background:var(--green-main);color:var(--white);border-color:var(--green-main);}
  /* Recommendation cards */
  .reco-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.8rem;margin-top:.8rem;}
  .reco-card{background:var(--green-pale);border:1.5px solid var(--border);border-radius:12px;padding:.9rem;cursor:pointer;transition:all .2s;}
  .reco-card:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(45,106,31,.15);border-color:var(--green-main);}
  .reco-card-name{font-weight:700;font-size:.88rem;color:var(--green-dark);margin-bottom:.4rem;}
  .reco-card-meta{font-size:.75rem;color:var(--grey);display:flex;flex-wrap:wrap;gap:.4rem;}
  .reco-badge{background:var(--white);border-radius:50px;padding:.15rem .5rem;font-size:.72rem;font-weight:600;}
  .reco-score{font-size:.72rem;font-weight:700;color:var(--orange);margin-top:.4rem;}
  .reco-empty{text-align:center;padding:1.5rem;color:var(--grey);font-size:.88rem;}
  .reco-highlight{border-color:var(--orange)!important;background:linear-gradient(135deg,#fff8f0,var(--green-pale))!important;}
  .reco-rank{font-size:1.1rem;margin-bottom:.3rem;}
  .stat-card{background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:1rem 1.2rem;display:flex;align-items:center;gap:.9rem;box-shadow:0 2px 8px rgba(45,106,31,.06);}
  .stat-card-icon{font-size:1.8rem;flex-shrink:0;}
  .stat-card-val{font-family:"Playfair Display",serif;font-size:1.5rem;font-weight:700;color:var(--green-dark);line-height:1;}
  .stat-card-label{font-size:.75rem;color:var(--grey);margin-top:.2rem;}
  .stat-bar-section{padding:1.2rem 5rem 0;}
  .stat-bar-title{font-size:.8rem;font-weight:700;color:var(--grey);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.8rem;}
  .stat-bar-row{display:flex;align-items:center;gap:.8rem;margin-bottom:.5rem;}
  .stat-bar-label{font-size:.8rem;font-weight:600;width:70px;flex-shrink:0;}
  .stat-bar-track{flex:1;height:10px;background:var(--border);border-radius:50px;overflow:hidden;}
  .stat-bar-fill{height:100%;border-radius:50px;transition:width .8s ease;}
  .stat-bar-count{font-size:.78rem;font-weight:700;color:var(--grey);width:30px;text-align:right;flex-shrink:0;}

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}.recipes-grid{grid-template-columns:1fr;}.floating-cart{bottom:1.5rem;right:1.5rem;}.stats-section,.stat-bar-section{padding:1rem 1.5rem 0;}}
  .btn-cart{background:var(--orange);color:var(--white);border:none;padding:.45rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;display:flex;align-items:center;gap:.4rem;justify-content:center;}
  .btn-cart:hover{background:#d66a15;transform:scale(1.05);}
  .btn-cart.in-cart{background:var(--green-main);}

  /* Quantity controls */
  .qty-controls{display:none;align-items:center;justify-content:space-between;gap:.5rem;background:var(--green-pale);border-radius:8px;padding:.3rem .5rem;}
  .qty-controls.visible{display:flex;}
  .qty-btn{width:28px;height:28px;border-radius:6px;border:none;font-size:1rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0;}
  .qty-minus{background:#fff0f0;color:#c0392b;}
  .qty-minus:hover{background:#c0392b;color:var(--white);}
  .qty-plus{background:var(--green-pale);color:var(--green-dark);border:1.5px solid var(--green-light);}
  .qty-plus:hover{background:var(--green-main);color:var(--white);}
  .qty-num{font-weight:700;font-size:.95rem;color:var(--green-dark);min-width:24px;text-align:center;}
  .qty-label{font-size:.75rem;color:var(--grey);flex:1;text-align:center;}

  /* Floating Cart Icon */
  .floating-cart{position:fixed;bottom:2rem;right:2rem;width:60px;height:60px;background:var(--orange);color:var(--white);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;cursor:pointer;box-shadow:0 8px 24px rgba(240,124,27,.4);transition:all .3s;z-index:100;}
  .floating-cart:hover{transform:scale(1.1);box-shadow:0 12px 32px rgba(240,124,27,.6);}
  .cart-badge{position:absolute;top:-5px;right:-5px;background:var(--red);color:var(--white);width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;border:2px solid var(--white);}

  /* Cart Modal */
  .cart-modal{position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
  .cart-modal.open{display:flex;}
  .cart-content{background:var(--white);border-radius:20px;width:min(500px,90vw);max-height:80vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
  .cart-header{background:linear-gradient(135deg,var(--orange),#d66a15);padding:1.5rem;color:var(--white);display:flex;align-items:center;justify-content:space-between;}
  .cart-header h2{font-family:"Playfair Display",serif;font-size:1.4rem;margin:0;}
  .cart-close{background:rgba(255,255,255,0.2);border:none;color:var(--white);width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.2rem;display:flex;align-items:center;justify-content:center;}
  .cart-close:hover{background:rgba(255,255,255,0.3);}
  .cart-body{padding:1.5rem;overflow-y:auto;flex:1;}
  .cart-item{display:flex;gap:1rem;padding:1rem;background:var(--green-pale);border-radius:12px;margin-bottom:1rem;align-items:center;}
  .cart-item-img{width:60px;height:60px;border-radius:8px;object-fit:cover;background:var(--white);flex-shrink:0;}
  .cart-item-info{flex:1;}
  .cart-item-name{font-weight:600;font-size:.9rem;color:var(--green-dark);margin-bottom:.3rem;}
  .cart-item-meta{font-size:.75rem;color:var(--grey);}
  .cart-item-remove{background:var(--red);color:var(--white);border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;}
  .cart-item-remove:hover{background:#c0392b;}
  .cart-empty{text-align:center;padding:3rem 1rem;color:var(--grey);}
  .cart-empty-icon{font-size:3rem;margin-bottom:1rem;}
  .cart-footer{padding:1.5rem;border-top:1px solid var(--border);}
  .cart-total{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;font-size:1.1rem;font-weight:700;color:var(--green-dark);}
  .btn-order{background:linear-gradient(135deg,var(--green-main),var(--green-dark));color:var(--white);border:none;padding:.8rem 1.5rem;border-radius:50px;font-family:"DM Sans",sans-serif;font-size:.9rem;font-weight:700;cursor:pointer;width:100%;transition:all .2s;}
  .btn-order:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(45,106,31,.3);}

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}.recipes-grid{grid-template-columns:1fr;}.floating-cart{bottom:1.5rem;right:1.5rem;}}
</style>

<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb"><a href="index.php">Accueil</a> › Recettes</div>
      <h1>🍽️ Nos Recettes</h1>
      <p><?= count($recettes) ?> recette<?= count($recettes) !== 1 ? 's' : '' ?> disponible<?= count($recettes) !== 1 ? 's' : '' ?></p>
    </div>
    <div style="display:flex;gap:.8rem;flex-wrap:wrap;align-items:center;">
      <button class="btn-add" onclick="exportPDF()" style="background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.4);">
        📄 Exporter PDF
      </button>
      <button class="btn-add" onclick="openMyOrders()" style="background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.5);">
        📋 Mes Commandes
      </button>
    </div>
  </div>
</div>

<!-- ── STATISTICS ── -->
<div class="stats-section">
  <div class="stat-card">
    <div class="stat-card-icon">🍽️</div>
    <div><div class="stat-card-val"><?= $total ?></div><div class="stat-card-label">Recettes au total</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon">⏱️</div>
    <div><div class="stat-card-val"><?= $avgTime ?> min</div><div class="stat-card-label">Temps moyen</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon">🚀</div>
    <div><div class="stat-card-val"><?= $minTime ?> min</div><div class="stat-card-label">Plus rapide</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon">👨‍🍳</div>
    <div><div class="stat-card-val"><?= $maxTime ?> min</div><div class="stat-card-label">Plus longue</div></div>
  </div>
</div>

<?php if ($total > 0): ?>
<div class="stat-bar-section">
  <div class="stat-bar-title">Répartition par difficulté</div>
  <div class="stat-bar-row">
    <span class="stat-bar-label" style="color:#0f5132;">😊 Facile</span>
    <div class="stat-bar-track"><div class="stat-bar-fill" style="width:<?= $total > 0 ? round($facile/$total*100) : 0 ?>%;background:#4a9e30;"></div></div>
    <span class="stat-bar-count"><?= $facile ?></span>
  </div>
  <div class="stat-bar-row">
    <span class="stat-bar-label" style="color:#856404;">🔥 Moyen</span>
    <div class="stat-bar-track"><div class="stat-bar-fill" style="width:<?= $total > 0 ? round($moyen/$total*100) : 0 ?>%;background:#f07c1b;"></div></div>
    <span class="stat-bar-count"><?= $moyen ?></span>
  </div>
  <div class="stat-bar-row">
    <span class="stat-bar-label" style="color:#842029;">💪 Difficile</span>
    <div class="stat-bar-track"><div class="stat-bar-fill" style="width:<?= $total > 0 ? round($difficile/$total*100) : 0 ?>%;background:#e53935;"></div></div>
    <span class="stat-bar-count"><?= $difficile ?></span>
  </div>
</div>
<?php endif; ?>

<div class="content-section">
  <?php if ($success === 'added'): ?>
    <div class="flash flash-success">✅ Recette ajoutée avec succès !</div>
  <?php elseif ($success === 'updated'): ?>
    <div class="flash flash-success">✅ Recette mise à jour avec succès !</div>
  <?php elseif ($error === 'notfound'): ?>
    <div class="flash flash-error">❌ Recette introuvable.</div>
  <?php endif; ?>

  <div class="table-toolbar">
    <input type="text" class="search-input" id="searchInput" placeholder="🔍 Rechercher une recette…" oninput="filterCards()"/>
    <select class="filter-select" id="categorieFilter" onchange="filterByCategorie()">
      <option value="">Toutes catégories</option>
      <?php foreach ($allCategories as $cat): ?>
        <option value="<?= $cat->id ?>" <?= $selectedCategorieId === $cat->id ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat->nom) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <select class="filter-select" id="diffFilter" onchange="filterCards()">
      <option value="">Toutes difficultés</option>
      <option value="facile">Facile</option>
      <option value="moyen">Moyen</option>
      <option value="difficile">Difficile</option>
    </select>
    <span class="count-badge" id="countBadge"><?= count($recettes) ?> recette<?= count($recettes) !== 1 ? 's' : '' ?></span>
  </div>

  <?php if (empty($recettes)): ?>
    <div class="empty-state">
      <div class="ei">🍳</div>
      <p>Aucune recette disponible pour le moment.</p>
    </div>
  <?php else: ?>
    <div class="recipes-grid" id="recipesGrid">
      <?php foreach ($recettes as $r): ?>
      <div class="rcard"
           data-name="<?= htmlspecialchars(strtolower($r->nom)) ?>"
           data-diff="<?= htmlspecialchars($r->difficulte) ?>"
           data-id="<?= $r->id ?>">
        <a href="viewRecette.php?id=<?= $r->id ?>" style="text-decoration:none;color:inherit;display:block;">
          <div class="rcard-img">
            <?php if (!empty($r->image)): ?>
              <img src="../<?= htmlspecialchars($r->image) ?>" alt="<?= htmlspecialchars($r->nom) ?>"
                   onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"/>
              <div class="rcard-img-placeholder" style="display:none;">🍽️</div>
            <?php else: ?>
              <div class="rcard-img-placeholder">🍽️</div>
            <?php endif; ?>
            <span class="rcard-diff diff-<?= htmlspecialchars($r->difficulte) ?>">
              <?= ['facile'=>'😊 Facile','moyen'=>'🔥 Moyen','difficile'=>'💪 Difficile'][$r->difficulte] ?? $r->difficulte ?>
            </span>
          </div>
          <div class="rcard-body">
            <h3 class="rcard-title"><?= htmlspecialchars($r->nom) ?></h3>
            <p class="rcard-desc"><?= htmlspecialchars($r->description) ?></p>
            <div class="rcard-meta">
              <span>⏱ <?= $r->temps_preparation ?> min</span>
              <span>🥗 <?= $r->nb_aliments ?? 0 ?> ingrédient<?= ($r->nb_aliments ?? 0) !== 1 ? 's' : '' ?></span>
              <?php if ($r->date_creation): ?>
                <span>📅 <?= date('d/m/Y', strtotime($r->date_creation)) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </a>
        <div style="padding:0 1.2rem 1.2rem;display:flex;flex-direction:column;gap:.5rem;">
          <button class="btn-cart" id="btn-cart-<?= $r->id ?>" onclick="event.stopPropagation(); addToCart(<?= $r->id ?>, '<?= htmlspecialchars(addslashes($r->nom)) ?>', '<?= htmlspecialchars($r->image ?? '') ?>', <?= $r->temps_preparation ?>)">
            🛒 Ajouter au panier
          </button>
          <div class="qty-controls" id="qty-<?= $r->id ?>">
            <button class="qty-btn qty-minus" onclick="event.stopPropagation(); changeQty(<?= $r->id ?>, -1)">−</button>
            <span class="qty-label">Quantité</span>
            <span class="qty-num" id="qty-num-<?= $r->id ?>">1</span>
            <button class="qty-btn qty-plus" onclick="event.stopPropagation(); changeQty(<?= $r->id ?>, 1)">+</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<!-- ══════════════════════════════════════════
   FILTRAGE INTELLIGENT + RECOMMANDATIONS
══════════════════════════════════════════ -->
<div class="smart-panel-wrap">

  <!-- FILTER -->
  <div class="smart-panel">
    <div class="sp-header" onclick="togglePanel('sfPanel')">
      <span>🔍 Filtrage intelligent</span>
      <span class="sp-arrow" id="arrow-sfPanel">▼</span>
    </div>
    <div class="sp-body" id="body-sfPanel">

      <!-- Quick goal chips -->
      <div class="sp-section-label">🎯 Objectif santé</div>
      <div class="chip-row" id="sfGoalChips">
        <button class="chip active" data-val="" onclick="chipSelect(this,'sfGoalChips'); applySmartFilter()">Tous</button>
        <button class="chip" data-val="perte_poids" onclick="chipSelect(this,'sfGoalChips'); applySmartFilter()">⚖️ Perte de poids</button>
        <button class="chip" data-val="muscle" onclick="chipSelect(this,'sfGoalChips'); applySmartFilter()">💪 Muscle</button>
        <button class="chip" data-val="equilibre" onclick="chipSelect(this,'sfGoalChips'); applySmartFilter()">🥗 Équilibre</button>
        <button class="chip" data-val="energie" onclick="chipSelect(this,'sfGoalChips'); applySmartFilter()">⚡ Énergie</button>
        <button class="chip" data-val="leger" onclick="chipSelect(this,'sfGoalChips'); applySmartFilter()">🌿 Léger</button>
      </div>

      <!-- Quick time chips -->
      <div class="sp-section-label" style="margin-top:.9rem;">⏱️ Temps de préparation</div>
      <div class="chip-row" id="sfTimeChips">
        <button class="chip active" data-val="999" onclick="chipSelect(this,'sfTimeChips'); applySmartFilter()">Tous</button>
        <button class="chip" data-val="15" onclick="chipSelect(this,'sfTimeChips'); applySmartFilter()">≤ 15 min</button>
        <button class="chip" data-val="30" onclick="chipSelect(this,'sfTimeChips'); applySmartFilter()">≤ 30 min</button>
        <button class="chip" data-val="60" onclick="chipSelect(this,'sfTimeChips'); applySmartFilter()">≤ 60 min</button>
      </div>

      <!-- Quick difficulty chips -->
      <div class="sp-section-label" style="margin-top:.9rem;">💪 Difficulté</div>
      <div class="chip-row" id="sfDiffChips">
        <button class="chip active" data-val="" onclick="chipSelect(this,'sfDiffChips'); applySmartFilter()">Toutes</button>
        <button class="chip" data-val="facile" onclick="chipSelect(this,'sfDiffChips'); applySmartFilter()">😊 Facile</button>
        <button class="chip" data-val="moyen" onclick="chipSelect(this,'sfDiffChips'); applySmartFilter()">🔥 Moyen</button>
        <button class="chip" data-val="difficile" onclick="chipSelect(this,'sfDiffChips'); applySmartFilter()">💪 Difficile</button>
      </div>

      <!-- Ingredient search -->
      <div class="sp-section-label" style="margin-top:.9rem;">🥕 Contient l'ingrédient</div>
      <input type="text" id="sfIngredient" placeholder="Ex: poulet, tomate, riz…" oninput="applySmartFilter()"
        style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.88rem;outline:none;background:var(--white);color:var(--black);margin-top:.4rem;">

      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:1rem;flex-wrap:wrap;gap:.5rem;">
        <span id="sfResultCount" style="font-size:.82rem;font-weight:600;color:var(--green-dark);"></span>
        <button onclick="resetSmartFilter()" style="background:none;border:1.5px solid var(--border);border-radius:8px;padding:.4rem 1rem;font-size:.82rem;font-weight:600;cursor:pointer;color:var(--grey);font-family:'DM Sans',sans-serif;">↺ Réinitialiser</button>
      </div>
    </div>
  </div>

  <!-- RECOMMENDATIONS -->
  <div class="smart-panel">
    <div class="sp-header" onclick="togglePanel('recoPanel')">
      <span>✨ Recommandations personnalisées</span>
      <span class="sp-arrow" id="arrow-recoPanel">▼</span>
    </div>
    <div class="sp-body collapsed" id="body-recoPanel">
      <p style="font-size:.85rem;color:var(--grey);margin-bottom:1rem;">Choisissez votre objectif et votre temps disponible — on sélectionne les meilleures recettes pour vous.</p>

      <div class="chip-row" id="recoGoalChips" style="margin-bottom:.8rem;">
        <button class="chip active" data-val="" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">Tous</button>
        <button class="chip" data-val="perte_poids" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">⚖️ Perte de poids</button>
        <button class="chip" data-val="muscle" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">💪 Muscle</button>
        <button class="chip" data-val="equilibre" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">🥗 Équilibre</button>
        <button class="chip" data-val="energie" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">⚡ Énergie</button>
        <button class="chip" data-val="leger" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">🌿 Léger</button>
      </div>

      <div class="chip-row" id="recoTimeChips">
        <button class="chip active" data-val="999" onclick="chipSelect(this,'recoTimeChips'); generateRecommendations()">Tout temps</button>
        <button class="chip" data-val="15" onclick="chipSelect(this,'recoTimeChips'); generateRecommendations()">≤ 15 min</button>
        <button class="chip" data-val="30" onclick="chipSelect(this,'recoTimeChips'); generateRecommendations()">≤ 30 min</button>
        <button class="chip" data-val="60" onclick="chipSelect(this,'recoTimeChips'); generateRecommendations()">≤ 60 min</button>
      </div>

      <div id="recoResults" style="margin-top:1.2rem;"></div>
    </div>
  </div>

</div>

<!-- Floating Cart Button -->
<div class="floating-cart" onclick="openCart()">
  🛒
  <span class="cart-badge" id="cartBadge" style="display:none;">0</span>
</div>

<!-- Cart Modal -->
<div class="cart-modal" id="cartModal" onclick="if(event.target === this) closeCart()">
  <div class="cart-content">
    <div class="cart-header">
      <h2>🛒 Mon Panier</h2>
      <button class="cart-close" onclick="closeCart()">×</button>
    </div>
    <div class="cart-body" id="cartBody">
      <div class="cart-empty">
        <div class="cart-empty-icon">🛒</div>
        <p>Votre panier est vide</p>
      </div>
    </div>
    <div class="cart-footer" id="cartFooter" style="display:none;">
      <div class="cart-total">
        <span>Total:</span>
        <span id="cartTotal">0 recette(s)</span>
      </div>
      <button class="btn-order" onclick="showOrderForm()">📦 Commander</button>
    </div>
  </div>
</div>

<!-- Order Form Modal -->
<div class="cart-modal" id="orderModal" onclick="if(event.target === this) closeOrderForm()">
  <div class="cart-content">
    <div class="cart-header">
      <h2>📋 Finaliser la commande</h2>
      <button class="cart-close" onclick="closeOrderForm()">×</button>
    </div>
    <div class="cart-body">
      <div id="orderError" style="display:none;background:#f8d7da;color:#842029;padding:.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.88rem;"></div>
      <div style="display:flex;flex-direction:column;gap:1rem;">
        <div>
          <label style="font-size:.85rem;font-weight:600;color:var(--green-dark);display:block;margin-bottom:.4rem;">Votre nom *</label>
          <input type="text" id="orderName" placeholder="Ex: Jean Dupont" style="width:100%;padding:.7rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;">
        </div>
        <div>
          <label style="font-size:.85rem;font-weight:600;color:var(--green-dark);display:block;margin-bottom:.4rem;">Email *</label>
          <input type="email" id="orderEmail" placeholder="Ex: jean@email.com" style="width:100%;padding:.7rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;">
        </div>
        <div>
          <label style="font-size:.85rem;font-weight:600;color:var(--green-dark);display:block;margin-bottom:.4rem;">Téléphone</label>
          <input type="tel" id="orderPhone" placeholder="Ex: 06 12 34 56 78" style="width:100%;padding:.7rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;">
        </div>
        <div id="orderSummary" style="background:var(--green-pale);border-radius:10px;padding:1rem;font-size:.85rem;color:var(--green-dark);"></div>
      </div>
    </div>
    <div class="cart-footer" style="display:block;">
      <button class="btn-order" onclick="submitOrder()" id="submitBtn">📦 Envoyer la commande</button>
    </div>
  </div>
</div>

<script>
let cart = JSON.parse(localStorage.getItem('recetteCart') || '[]');

function updateCartUI() {
  const badge      = document.getElementById('cartBadge');
  const cartBody   = document.getElementById('cartBody');
  const cartFooter = document.getElementById('cartFooter');
  const cartTotal  = document.getElementById('cartTotal');

  const totalQty = cart.reduce((s, i) => s + i.qty, 0);

  if (cart.length === 0) {
    badge.style.display = 'none';
    cartBody.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛒</div><p>Votre panier est vide</p></div>';
    cartFooter.style.display = 'none';
  } else {
    badge.style.display = 'flex';
    badge.textContent = totalQty;
    cartFooter.style.display = 'block';
    cartTotal.textContent = totalQty + ' portion' + (totalQty !== 1 ? 's' : '');

    cartBody.innerHTML = cart.map(item => `
      <div class="cart-item">
        <img src="../${item.image || ''}" alt="${item.name}" class="cart-item-img"
             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2260%22%3E🍽️%3C/text%3E%3C/svg%3E'">
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-meta">⏱ ${item.time} min</div>
        </div>
        <div style="display:flex;align-items:center;gap:.4rem;flex-shrink:0;">
          <button onclick="changeQty(${item.id}, -1)"
            style="width:26px;height:26px;border-radius:6px;border:none;background:#fff0f0;color:#c0392b;font-weight:700;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">−</button>
          <span style="font-weight:700;font-size:.95rem;color:var(--green-dark);min-width:20px;text-align:center;">${item.qty}</span>
          <button onclick="changeQty(${item.id}, 1)"
            style="width:26px;height:26px;border-radius:6px;border:1.5px solid var(--green-light);background:var(--green-pale);color:var(--green-dark);font-weight:700;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">+</button>
        </div>
        <button class="cart-item-remove" onclick="removeFromCart(${item.id})" style="margin-left:.3rem;">×</button>
      </div>
    `).join('');
  }

  // Sync card buttons & qty controls
  document.querySelectorAll('.rcard').forEach(card => {
    const id      = parseInt(card.dataset.id);
    const btn     = document.getElementById('btn-cart-' + id);
    const qtyBox  = document.getElementById('qty-' + id);
    const qtyNum  = document.getElementById('qty-num-' + id);
    const item    = cart.find(i => i.id === id);

    if (btn && qtyBox && qtyNum) {
      if (item) {
        btn.classList.add('in-cart');
        btn.innerHTML = '✓ Dans le panier';
        qtyBox.classList.add('visible');
        qtyNum.textContent = item.qty;
      } else {
        btn.classList.remove('in-cart');
        btn.innerHTML = '🛒 Ajouter au panier';
        qtyBox.classList.remove('visible');
        qtyNum.textContent = 1;
      }
    }
  });
}

function addToCart(id, name, image, time) {
  const existing = cart.find(i => i.id === id);
  if (existing) {
    removeFromCart(id);
    return;
  }
  cart.push({ id, name, image, time, qty: 1 });
  localStorage.setItem('recetteCart', JSON.stringify(cart));
  updateCartUI();

  const toast = document.createElement('div');
  toast.style.cssText = 'position:fixed;bottom:6rem;right:2rem;background:var(--green-dark);color:white;padding:1rem 1.5rem;border-radius:12px;font-size:.9rem;z-index:300;box-shadow:0 8px 24px rgba(0,0,0,0.2);transition:opacity .3s;';
  toast.innerHTML = '✅ Ajouté au panier';
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2000);
}

function changeQty(id, delta) {
  const item = cart.find(i => i.id === id);
  if (!item) return;
  item.qty = Math.max(1, item.qty + delta);
  localStorage.setItem('recetteCart', JSON.stringify(cart));
  updateCartUI();
}

function removeFromCart(id) {
  cart = cart.filter(i => i.id !== id);
  localStorage.setItem('recetteCart', JSON.stringify(cart));
  updateCartUI();
}

function openCart() {
  document.getElementById('cartModal').classList.add('open');
}

function closeCart() {
  document.getElementById('cartModal').classList.remove('open');
}

function showOrderForm() {
  if (cart.length === 0) return;
  closeCart();
  document.getElementById('orderSummary').innerHTML =
    '<strong>Récapitulatif:</strong><br>' +
    cart.map(i => `🍽️ ${i.name} <strong>x${i.qty}</strong>`).join('<br>');
  document.getElementById('orderModal').classList.add('open');
}

function closeOrderForm() {
  document.getElementById('orderModal').classList.remove('open');
}

function submitOrder() {
  const name  = document.getElementById('orderName').value.trim();
  const email = document.getElementById('orderEmail').value.trim();
  const phone = document.getElementById('orderPhone').value.trim();
  const errEl = document.getElementById('orderError');

  if (!name) { errEl.textContent = 'Le nom est obligatoire.'; errEl.style.display='block'; return; }
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { errEl.textContent = 'Email invalide.'; errEl.style.display='block'; return; }
  errEl.style.display = 'none';

  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = '⏳ Envoi en cours...';

  const fd = new FormData();
  fd.append('action', 'order');
  fd.append('user_name', name);
  fd.append('user_email', email);
  fd.append('user_phone', phone);
  fd.append('recettes', JSON.stringify(cart));

  fetch('listRecette.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        cart = [];
        localStorage.setItem('recetteCart', JSON.stringify(cart));
        updateCartUI();
        closeOrderForm();
        // Show success message
        const msg = document.createElement('div');
        msg.style.cssText = 'position:fixed;top:2rem;left:50%;transform:translateX(-50%);background:var(--green-dark);color:white;padding:1.2rem 2rem;border-radius:14px;font-size:.95rem;font-weight:600;z-index:400;box-shadow:0 8px 24px rgba(0,0,0,0.2);text-align:center;';
        msg.innerHTML = '✅ Commande envoyée !<br><small style="opacity:.8">L\'admin vous contactera bientôt.</small>';
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 4000);
      } else {
        errEl.textContent = data.message || 'Erreur lors de l\'envoi.';
        errEl.style.display = 'block';
      }
    })
    .catch(() => {
      errEl.textContent = 'Erreur réseau. Réessayez.';
      errEl.style.display = 'block';
    })
    .finally(() => {
      btn.disabled = false;
      btn.textContent = '📦 Envoyer la commande';
    });
}

// Initialize cart UI on page load
updateCartUI();

/* ══════════════════════════════════════════
   SMART FILTER + RECOMMENDATIONS
══════════════════════════════════════════ */
const ALL_RECETTES = <?= $recettesJson ?>;

/* ── Panel toggle ── */
function togglePanel(id) {
  const body  = document.getElementById('body-' + id);
  const arrow = document.getElementById('arrow-' + id);
  body.classList.toggle('collapsed');
  arrow.style.transform = body.classList.contains('collapsed') ? 'rotate(-90deg)' : '';
}

// ── Chip selector ──
function chipSelect(el, groupId) {
  document.getElementById(groupId).querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
}

// ── Goal → nutritional logic ──
const GOAL_RULES = {
  perte_poids: r => r.calories < 400 && r.lipides < 15,
  muscle:      r => r.proteines > 20,
  equilibre:   r => r.proteines > 5 && r.glucides > 5 && r.lipides > 2,
  energie:     r => r.glucides > 30,
  leger:       r => r.lipides < 8 && r.calories < 300,
};

// ── Smart Filter ──
function applySmartFilter() {
  const timeMax    = parseInt(document.querySelector('#sfTimeChips .chip.active')?.dataset.val || '999');
  const diff       = document.querySelector('#sfDiffChips .chip.active')?.dataset.val || '';
  const goalTag    = document.querySelector('#sfGoalChips .chip.active')?.dataset.val || '';
  const ing        = (document.getElementById('sfIngredient')?.value || '').toLowerCase().trim();

  const filtered = ALL_RECETTES.filter(r => {
    if (r.temps > timeMax) return false;
    if (diff && r.difficulte !== diff) return false;
    if (goalTag && GOAL_RULES[goalTag] && !GOAL_RULES[goalTag](r)) return false;
    if (ing && !(r.ingredients || '').includes(ing) && !r.nom.toLowerCase().includes(ing)) return false;
    return true;
  });

  document.querySelectorAll('#recipesGrid .rcard').forEach(card => {
    const id = parseInt(card.dataset.id);
    card.style.display = filtered.some(r => r.id === id) ? '' : 'none';
  });

  const el = document.getElementById('sfResultCount');
  if (el) el.textContent = filtered.length + ' recette' + (filtered.length !== 1 ? 's' : '') + ' trouvée' + (filtered.length !== 1 ? 's' : '');
  const badge = document.getElementById('countBadge');
  if (badge) badge.textContent = filtered.length + ' recette' + (filtered.length !== 1 ? 's' : '');
}

function resetSmartFilter() {
  ['sfGoalChips','sfTimeChips','sfDiffChips'].forEach(id => {
    const group = document.getElementById(id);
    if (!group) return;
    group.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    group.querySelector('.chip').classList.add('active');
  });
  const ing = document.getElementById('sfIngredient');
  if (ing) ing.value = '';
  document.querySelectorAll('#recipesGrid .rcard').forEach(c => c.style.display = '');
  const el = document.getElementById('sfResultCount');
  if (el) el.textContent = ALL_RECETTES.length + ' recette' + (ALL_RECETTES.length !== 1 ? 's' : '') + ' trouvée' + (ALL_RECETTES.length !== 1 ? 's' : '');
}

// ── Recommendations ──
function scoreRecette(r, goal, maxTime, level) {
  let score = 0;
  if (maxTime < 999 && r.temps > maxTime) return -1;
  if (level && r.difficulte !== level) return -1;
  if (goal === 'perte_poids') {
    score += Math.max(0, 500 - r.calories) / 10;
    score += Math.max(0, 20 - r.lipides);
  } else if (goal === 'muscle') {
    score += r.proteines * 3;
    score += Math.max(0, 30 - r.lipides);
  } else if (goal === 'equilibre') {
    const balance = Math.min(r.proteines, r.glucides, r.lipides);
    score += balance * 2;
  } else if (goal === 'energie') {
    score += r.glucides * 2;
    score += r.calories / 20;
  } else if (goal === 'leger') {
    score += Math.max(0, 15 - r.lipides) * 3;
    score += Math.max(0, 300 - r.calories) / 10;
  }
  // Bonus for quick prep
  score += Math.max(0, 60 - r.temps) / 5;
  return score;
}

function generateRecommendations() {
  const goal    = document.querySelector('#recoGoalChips .chip.active')?.dataset.val || '';
  const maxTime = parseInt(document.querySelector('#recoTimeChips .chip.active')?.dataset.val || '999');
  const container = document.getElementById('recoResults');

  if (!goal) {
    container.innerHTML = '<div class="reco-empty">Choisissez un objectif pour voir les recommandations.</div>';
    return;
  }

  const scored = ALL_RECETTES
    .map(r => ({ ...r, score: scoreRecette(r, goal, maxTime, '') }))
    .filter(r => r.score >= 0)
    .sort((a, b) => b.score - a.score)
    .slice(0, 6);

  if (scored.length === 0) {
    container.innerHTML = '<div class="reco-empty">Aucune recette ne correspond. Essayez d\'élargir les filtres.</div>';
    return;
  }

  const medals = ['🥇','🥈','🥉','4️⃣','5️⃣','6️⃣'];
  const goalLabels = { perte_poids:'⚖️ Perte de poids', muscle:'💪 Muscle', equilibre:'🥗 Équilibre', energie:'⚡ Énergie', leger:'🌿 Léger' };

  container.innerHTML = `
    <div style="font-size:.82rem;color:var(--grey);margin-bottom:.8rem;">
      Top recommandations pour <strong>${goalLabels[goal]}</strong>
    </div>
    <div class="reco-grid">
      ${scored.map((r, i) => `
        <div class="reco-card ${i === 0 ? 'reco-highlight' : ''}" onclick="window.location='viewRecette.php?id=${r.id}'" style="cursor:pointer;">
          <div class="reco-rank">${medals[i]}</div>
          <div class="reco-card-name">${r.nom}</div>
          <div class="reco-card-meta">
            <span class="reco-badge">🔥 ${r.calories} kcal</span>
            <span class="reco-badge">💪 ${r.proteines}g</span>
            <span class="reco-badge">⏱ ${r.temps} min</span>
          </div>
          <div class="reco-score">Score: ${Math.round(r.score)} pts</div>
        </div>
      `).join('')}
    </div>`;
}

// Init result count
(function() {
  const el = document.getElementById('sfResultCount');
  if (el) el.textContent = ALL_RECETTES.length + ' recette' + (ALL_RECETTES.length !== 1 ? 's' : '') + ' trouvée' + (ALL_RECETTES.length !== 1 ? 's' : '');
})();

// Page-level translations
const pageTranslations = {
  fr: {
    hero_title:'Nos Recettes', hero_sub:'disponible', hero_subs:'disponibles',
    btn_orders:'Mes Commandes', btn_export:'Exporter PDF',
    stat_total:'Recettes au total', stat_avg:'Temps moyen', stat_fast:'Plus rapide', stat_long:'Plus longue',
    stat_bar_title:'Répartition par difficulté',
    diff_facile:'Facile', diff_moyen:'Moyen', diff_difficile:'Difficile',
    search_ph:'Rechercher une recette…',
    filter_all_cat:'Toutes catégories', filter_all_diff:'Toutes difficultés',
    filter_facile:'Facile', filter_moyen:'Moyen', filter_difficile:'Difficile',
    empty:'Aucune recette disponible pour le moment.',
    btn_add_cart:'Ajouter au panier', btn_in_cart:'Dans le panier',
    cart_title:'Mon Panier', cart_empty:'Votre panier est vide',
    cart_order:'Commander', cart_total:'portion', cart_totals:'portions',
    order_title:'Finaliser la commande',
    order_name:'Votre nom', order_email:'Email', order_phone:'Téléphone',
    order_recap:'Récapitulatif', order_send:'Envoyer la commande',
    my_orders:'Mes Commandes', orders_email_ph:'votre@email.com',
    orders_search:'Voir mes commandes', orders_back:'Changer d\'email',
    orders_empty:'Aucune commande trouvée pour cet email.',
    status_pending:'En attente', status_accepted:'Acceptée', status_rejected:'Refusée',
  },
  en: {
    hero_title:'Our Recipes', hero_sub:'available', hero_subs:'available',
    btn_orders:'My Orders', btn_export:'Export PDF',
    stat_total:'Total recipes', stat_avg:'Avg. time', stat_fast:'Fastest', stat_long:'Longest',
    stat_bar_title:'Difficulty breakdown',
    diff_facile:'Easy', diff_moyen:'Medium', diff_difficile:'Hard',
    search_ph:'Search a recipe…',
    filter_all_cat:'All categories', filter_all_diff:'All difficulties',
    filter_facile:'Easy', filter_moyen:'Medium', filter_difficile:'Hard',
    empty:'No recipes available at the moment.',
    btn_add_cart:'Add to cart', btn_in_cart:'In cart',
    cart_title:'My Cart', cart_empty:'Your cart is empty',
    cart_order:'Order', cart_total:'portion', cart_totals:'portions',
    order_title:'Finalize order',
    order_name:'Your name', order_email:'Email', order_phone:'Phone',
    order_recap:'Summary', order_send:'Send order',
    my_orders:'My Orders', orders_email_ph:'your@email.com',
    orders_search:'View my orders', orders_back:'Change email',
    orders_empty:'No orders found for this email.',
    status_pending:'Pending', status_accepted:'Accepted', status_rejected:'Rejected',
  },
  ar: {
    hero_title:'وصفاتنا', hero_sub:'متاحة', hero_subs:'متاحة',
    btn_orders:'طلباتي', btn_export:'تصدير PDF',
    stat_total:'إجمالي الوصفات', stat_avg:'متوسط الوقت', stat_fast:'الأسرع', stat_long:'الأطول',
    stat_bar_title:'توزيع حسب الصعوبة',
    diff_facile:'سهل', diff_moyen:'متوسط', diff_difficile:'صعب',
    search_ph:'ابحث عن وصفة…',
    filter_all_cat:'كل الفئات', filter_all_diff:'كل المستويات',
    filter_facile:'سهل', filter_moyen:'متوسط', filter_difficile:'صعب',
    empty:'لا توجد وصفات متاحة حالياً.',
    btn_add_cart:'أضف إلى السلة', btn_in_cart:'في السلة',
    cart_title:'سلتي', cart_empty:'سلتك فارغة',
    cart_order:'اطلب', cart_total:'حصة', cart_totals:'حصص',
    order_title:'إتمام الطلب',
    order_name:'اسمك', order_email:'البريد الإلكتروني', order_phone:'الهاتف',
    order_recap:'ملخص', order_send:'إرسال الطلب',
    my_orders:'طلباتي', orders_email_ph:'بريدك@example.com',
    orders_search:'عرض طلباتي', orders_back:'تغيير البريد',
    orders_empty:'لا توجد طلبات لهذا البريد.',
    status_pending:'قيد الانتظار', status_accepted:'مقبول', status_rejected:'مرفوض',
  }
};

function applyPageLang(lang) {
  const t = pageTranslations[lang] || pageTranslations.fr;
  // Hero
  const h1 = document.querySelector('.page-hero h1');
  if(h1) h1.textContent = '🍽️ ' + t.hero_title;
  // Buttons in hero
  const btns = document.querySelectorAll('.page-hero .btn-add');
  if(btns[0]) btns[0].innerHTML = '📄 ' + t.btn_export;
  if(btns[1]) btns[1].innerHTML = '📋 ' + t.btn_orders;
  // Stat labels
  const statLabels = document.querySelectorAll('.stat-card-label');
  const statKeys = ['stat_total','stat_avg','stat_fast','stat_long'];
  statLabels.forEach((el,i) => { if(statKeys[i]) el.textContent = t[statKeys[i]]; });
  // Stat bar title
  const barTitle = document.querySelector('.stat-bar-title');
  if(barTitle) barTitle.textContent = t.stat_bar_title;
  // Difficulty bar labels
  const barLabels = document.querySelectorAll('.stat-bar-label');
  if(barLabels[0]) barLabels[0].textContent = '😊 ' + t.diff_facile;
  if(barLabels[1]) barLabels[1].textContent = '🔥 ' + t.diff_moyen;
  if(barLabels[2]) barLabels[2].textContent = '💪 ' + t.diff_difficile;
  // Search
  const si = document.getElementById('searchInput');
  if(si) si.placeholder = '🔍 ' + t.search_ph;
  // Filters
  const catFilter = document.getElementById('categorieFilter');
  if(catFilter && catFilter.options[0]) catFilter.options[0].text = t.filter_all_cat;
  const diffFilter = document.getElementById('diffFilter');
  if(diffFilter) {
    if(diffFilter.options[0]) diffFilter.options[0].text = t.filter_all_diff;
    if(diffFilter.options[1]) diffFilter.options[1].text = t.filter_facile;
    if(diffFilter.options[2]) diffFilter.options[2].text = t.filter_moyen;
    if(diffFilter.options[3]) diffFilter.options[3].text = t.filter_difficile;
  }
  // Empty state
  const empty = document.querySelector('.empty-state p');
  if(empty) empty.textContent = t.empty;
  // Cart buttons on cards
  document.querySelectorAll('.btn-cart').forEach(btn => {
    if(!btn.classList.contains('in-cart')) btn.innerHTML = '🛒 ' + t.btn_add_cart;
    else btn.innerHTML = '✓ ' + t.btn_in_cart;
  });
  // Cart modal title
  const cartTitle = document.querySelector('#cartModal .cart-header h2');
  if(cartTitle) cartTitle.textContent = '🛒 ' + t.cart_title;
  // My orders button
  const myOrdersBtn = document.querySelector('#myOrdersModal .cart-header h2');
  if(myOrdersBtn) myOrdersBtn.textContent = '📋 ' + t.my_orders;
  // Check orders button
  const checkBtn = document.getElementById('checkOrdersBtn');
  if(checkBtn) checkBtn.textContent = '🔍 ' + t.orders_search;
  const emailInput = document.getElementById('ordersEmailInput');
  if(emailInput) emailInput.placeholder = t.orders_email_ph;
}

document.addEventListener('langChange', function(e){ applyPageLang(e.detail.lang); });
// Apply on load if saved
(function(){ const s = localStorage.getItem('econutri_lang'); if(s && s !== 'fr') applyPageLang(s); })();

function filterCards() {
  const q    = document.getElementById('searchInput').value.toLowerCase();
  const diff = document.getElementById('diffFilter').value;
  const cards = document.querySelectorAll('#recipesGrid .rcard');
  let visible = 0;
  cards.forEach(c => {
    const nameMatch = c.dataset.name.includes(q);
    const diffMatch = !diff || c.dataset.diff === diff;
    const show = nameMatch && diffMatch;
    c.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  document.getElementById('countBadge').textContent = visible + ' recette' + (visible !== 1 ? 's' : '');
}

function filterByCategorie() {
  const categorieId = document.getElementById('categorieFilter').value;
  const url = new URL(window.location.href);
  if (categorieId) {
    url.searchParams.set('categorie', categorieId);
  } else {
    url.searchParams.delete('categorie');
  }
  window.location.href = url.toString();
}

function confirmDelete(id, name) {
  showConfirm(
    'Supprimer la recette',
    `Voulez-vous vraiment supprimer « ${name} » ? Les ingrédients associés seront également supprimés.`,
    () => deleteRecette(id)
  );
}

function deleteRecette(id) {
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id', id);

  fetch('listRecette.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const card = document.querySelector(`button[onclick*="confirmDelete(${id},"]`)?.closest('.rcard');
        if (card) {
          card.style.transition = 'opacity .4s,transform .4s';
          card.style.opacity = '0';
          card.style.transform = 'scale(.95)';
          setTimeout(() => card.remove(), 400);
        }
        showToast('✅ Recette supprimée avec succès.', 'success');
      } else {
        showToast('❌ ' + (data.message || 'Erreur lors de la suppression.'), 'error');
      }
    })
    .catch(() => showToast('❌ Erreur réseau.', 'error'));
}
</script>

<!-- My Orders Modal -->
<div class="cart-modal" id="myOrdersModal" onclick="if(event.target===this)closeMyOrders()">
  <div class="cart-content" style="width:min(560px,92vw);">
    <div class="cart-header" style="background:linear-gradient(135deg,var(--green-dark),var(--green-main));">
      <h2>📋 Mes Commandes</h2>
      <button class="cart-close" onclick="closeMyOrders()">×</button>
    </div>
    <div class="cart-body" id="myOrdersBody">
      <!-- Step 1: email input -->
      <div id="ordersStep1">
        <p style="font-size:.9rem;color:var(--grey);margin-bottom:1.2rem;">Entrez votre email pour voir l'état de vos commandes.</p>
        <div id="ordersEmailError" style="display:none;background:#f8d7da;color:#842029;padding:.7rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.85rem;"></div>
        <input type="email" id="ordersEmailInput" placeholder="votre@email.com"
          style="width:100%;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;margin-bottom:1rem;"
          onkeydown="if(event.key==='Enter') checkOrders()">
        <button onclick="checkOrders()" id="checkOrdersBtn"
          style="width:100%;background:linear-gradient(135deg,var(--green-main),var(--green-dark));color:var(--white);border:none;padding:.8rem;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:700;cursor:pointer;">
          🔍 Voir mes commandes
        </button>
      </div>
      <!-- Step 2: results -->
      <div id="ordersStep2" style="display:none;">
        <button onclick="backToEmail()"
          style="background:none;border:none;color:var(--green-main);font-size:.85rem;font-weight:600;cursor:pointer;margin-bottom:1rem;padding:0;">
          ← Changer d'email
        </button>
        <div id="ordersList"></div>
      </div>
    </div>
  </div>
</div>

<style>
  .order-card{border:1.5px solid var(--border);border-radius:14px;padding:1.2rem;margin-bottom:1rem;background:var(--white);}
  .order-card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:.8rem;}
  .order-card-id{font-weight:700;color:var(--green-dark);font-size:.9rem;}
  .order-card-date{font-size:.75rem;color:var(--grey);}
  .order-card-recettes{font-size:.82rem;color:var(--grey);margin-bottom:.8rem;line-height:1.7;}
  .order-status-pending{background:#fff3cd;color:#856404;padding:.35rem .9rem;border-radius:50px;font-size:.78rem;font-weight:700;display:inline-block;}
  .order-status-accepted{background:#d1e7dd;color:#0f5132;padding:.35rem .9rem;border-radius:50px;font-size:.78rem;font-weight:700;display:inline-block;}
  .order-status-rejected{background:#f8d7da;color:#842029;padding:.35rem .9rem;border-radius:50px;font-size:.78rem;font-weight:700;display:inline-block;}
  .order-msg-box{margin-top:.8rem;padding:.9rem 1rem;border-radius:10px;font-size:.85rem;line-height:1.6;}
  .order-msg-accepted{background:#d1e7dd;color:#0f5132;border-left:4px solid var(--green-main);}
  .order-msg-rejected{background:#f8d7da;color:#842029;border-left:4px solid var(--red);}
  .order-msg-pending{background:#fff3cd;color:#856404;border-left:4px solid #ffc107;}
</style>

<script>
function openMyOrders() {
  document.getElementById('myOrdersModal').classList.add('open');
}
function closeMyOrders() {
  document.getElementById('myOrdersModal').classList.remove('open');
}
function backToEmail() {
  document.getElementById('ordersStep1').style.display = 'block';
  document.getElementById('ordersStep2').style.display = 'none';
}

function checkOrders() {
  const email = document.getElementById('ordersEmailInput').value.trim();
  const errEl = document.getElementById('ordersEmailError');
  const btn   = document.getElementById('checkOrdersBtn');

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    errEl.textContent = 'Veuillez entrer un email valide.';
    errEl.style.display = 'block';
    return;
  }
  errEl.style.display = 'none';
  btn.disabled = true;
  btn.textContent = '⏳ Recherche...';

  const fd = new FormData();
  fd.append('action', 'check_orders');
  fd.append('email', email);

  fetch('listRecette.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = '🔍 Voir mes commandes';

      if (!data.success) {
        errEl.textContent = data.message;
        errEl.style.display = 'block';
        return;
      }

      document.getElementById('ordersStep1').style.display = 'none';
      document.getElementById('ordersStep2').style.display = 'block';

      const list = document.getElementById('ordersList');
      if (data.orders.length === 0) {
        list.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--grey);"><div style="font-size:2.5rem;margin-bottom:.8rem;">📭</div><p>Aucune commande trouvée pour cet email.</p></div>';
        return;
      }

      list.innerHTML = data.orders.map(o => {
        const recettes = JSON.parse(o.recettes || '[]');
        const recettesList = recettes.map(r => '🍽️ ' + (typeof r === 'object' ? r.name : r)).join('<br>');
        const date = new Date(o.date_commande).toLocaleDateString('fr-FR', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });

        let statusBadge, msgBox = '';
        if (o.status === 'pending') {
          statusBadge = '<span class="order-status-pending">⏳ En attente</span>';
          msgBox = '<div class="order-msg-box order-msg-pending">⏳ Votre commande est en cours de traitement. Vous recevrez une réponse bientôt.</div>';
        } else if (o.status === 'accepted') {
          statusBadge = '<span class="order-status-accepted">✅ Acceptée</span>';
          msgBox = o.admin_message ? `<div class="order-msg-box order-msg-accepted"><strong>✅ Message de l'équipe EcoNutri :</strong><br>${o.admin_message.replace(/\n/g,'<br>')}</div>` : '';
        } else {
          statusBadge = '<span class="order-status-rejected">❌ Refusée</span>';
          msgBox = o.admin_message ? `<div class="order-msg-box order-msg-rejected"><strong>❌ Message de l'équipe EcoNutri :</strong><br>${o.admin_message.replace(/\n/g,'<br>')}</div>` : '';
        }

        return `
          <div class="order-card">
            <div class="order-card-header">
              <span class="order-card-id">Commande #${o.id}</span>
              <span class="order-card-date">📅 ${date}</span>
            </div>
            <div class="order-card-recettes">${recettesList}</div>
            ${statusBadge}
            ${msgBox}
          </div>`;
      }).join('');
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = '🔍 Voir mes commandes';
      errEl.textContent = 'Erreur réseau. Réessayez.';
      errEl.style.display = 'block';
    });
}
</script>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
function stripText(str) {
  return (str || '').replace(/[\u{1F000}-\u{1FFFF}|\u{2600}-\u{27FF}|\u{2300}-\u{23FF}|\u{FE00}-\u{FEFF}]/gu, '').replace(/[^\x00-\x7FÀ-ÿ\s\-\/\:\.,!'()]/g, '').trim();
}

function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
  const now = new Date();
  const dateStr = now.toLocaleDateString('fr-FR', { day:'2-digit', month:'long', year:'numeric' });

  // Header
  doc.setFillColor(45, 106, 31);
  doc.rect(0, 0, 297, 28, 'F');
  doc.setFillColor(74, 158, 48);
  doc.rect(0, 22, 297, 6, 'F');
  doc.setTextColor(255, 255, 255);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(20);
  doc.text('EcoNutri', 14, 13);
  doc.setFontSize(11);
  doc.setFont('helvetica', 'normal');
  doc.text('Liste des Recettes', 14, 20);
  doc.setFontSize(9);
  doc.text('Exporte le ' + dateStr, 283, 13, { align: 'right' });

  // Stats boxes
  const stats = [
    { label: 'Total', val: '<?= $total ?>', x: 44 },
    { label: 'Facile',    val: '<?= $facile ?>',    x: 110 },
    { label: 'Moyen',     val: '<?= $moyen ?>',     x: 176 },
    { label: 'Difficile', val: '<?= $difficile ?>', x: 242 },
  ];
  [14, 80, 146, 212].forEach(x => {
    doc.setFillColor(232, 245, 225);
    doc.roundedRect(x, 32, 60, 16, 3, 3, 'F');
  });
  stats.forEach(s => {
    doc.setFont('helvetica', 'bold'); doc.setFontSize(14); doc.setTextColor(45, 106, 31);
    doc.text(s.val, s.x, 41, { align: 'center' });
    doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(100, 100, 100);
    doc.text(s.label, s.x, 46, { align: 'center' });
  });

  // Collect visible cards
  const cards = [...document.querySelectorAll('#recipesGrid .rcard')].filter(c => c.style.display !== 'none');
  const rows = cards.map((card, i) => {
    const name = stripText(card.querySelector('.rcard-title')?.textContent || '');
    const desc = stripText(card.querySelector('.rcard-desc')?.textContent || '');
    const metaSpans = [...card.querySelectorAll('.rcard-meta span')];
    const timeSpan = metaSpans.find(s => s.textContent.includes('min'));
    const ingSpan  = metaSpans.find(s => s.textContent.includes('ingr'));
    const dateSpan = metaSpans.find(s => /\d{2}\/\d{2}\/\d{4}/.test(s.textContent));
    const time = timeSpan ? timeSpan.textContent.replace(/[^\d\s\w]/g,'').trim() : '-';
    const ing  = ingSpan  ? ingSpan.textContent.replace(/[^\d\s\w]/g,'').trim()  : '-';
    const date = dateSpan ? dateSpan.textContent.replace(/[^\d\/]/g,'').trim()   : '-';
    const diff = card.dataset.diff || '';
    const diffLabel = { facile:'Facile', moyen:'Moyen', difficile:'Difficile' }[diff] || diff;
    return [i+1, name, desc.length > 55 ? desc.slice(0,52)+'...' : desc, diffLabel, time, ing, date];
  });

  doc.autoTable({
    startY: 53,
    head: [['#', 'Nom', 'Description', 'Difficulte', 'Temps', 'Ingredients', 'Date']],
    body: rows,
    styles: { font:'helvetica', fontSize:9, cellPadding:4, valign:'middle', overflow:'linebreak' },
    headStyles: { fillColor:[45,106,31], textColor:255, fontStyle:'bold', fontSize:9 },
    alternateRowStyles: { fillColor:[245,252,240] },
    columnStyles: {
      0: { cellWidth:10, halign:'center' },
      1: { cellWidth:48, fontStyle:'bold' },
      2: { cellWidth:78 },
      3: { cellWidth:25, halign:'center' },
      4: { cellWidth:25, halign:'center' },
      5: { cellWidth:35, halign:'center' },
      6: { cellWidth:30, halign:'center' },
    },
    didParseCell(data) {
      if (data.section === 'body' && data.column.index === 3) {
        const v = data.cell.raw;
        if (v === 'Facile')    { data.cell.styles.textColor=[15,81,50];  data.cell.styles.fillColor=[209,231,221]; }
        if (v === 'Moyen')     { data.cell.styles.textColor=[133,100,4]; data.cell.styles.fillColor=[255,243,205]; }
        if (v === 'Difficile') { data.cell.styles.textColor=[132,32,41]; data.cell.styles.fillColor=[248,215,218]; }
      }
    },
    didDrawPage(data) {
      const p = doc.internal.getNumberOfPages();
      doc.setFontSize(8); doc.setTextColor(150);
      doc.text('EcoNutri - Recettes  |  Page ' + data.pageNumber + ' / ' + p, 148.5, 205, { align:'center' });
    }
  });

  doc.save('EcoNutri_Recettes_' + now.toISOString().slice(0,10) + '.pdf');
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
