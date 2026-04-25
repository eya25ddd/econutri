<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Aliment.php';
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../controllers/AlimentController.php';
require_once __DIR__ . '/../controllers/CategorieController.php';

$controller = new AlimentController();
$categorieController = new CategorieController();

// Handle delete via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    header('Content-Type: application/json');
    $id     = (int) ($_POST['id'] ?? 0);
    $result = $controller->delete($id);
    echo json_encode($result);
    exit;
}

// Get filter parameters
$selectedCategorieId = (int) ($_GET['categorie'] ?? 0);

// Get all aliments
$allAliments = $controller->getAll();

// Get only categories that have aliments assigned
$allCategories = [];
foreach ($categorieController->getAll() as $cat) {
    $alimentIds = $categorieController->getAlimentIdsForCategory($cat->id);
    if (!empty($alimentIds)) {
        $allCategories[] = $cat;
    }
}

// Filter aliments by category if selected
if ($selectedCategorieId > 0) {
    $alimentIds = $categorieController->getAlimentIdsForCategory($selectedCategorieId);
    $aliments = array_filter($allAliments, fn($a) => in_array($a->id, $alimentIds));
} else {
    $aliments = $allAliments;
}

$pageTitle = 'Aliments';
$activeNav = 'recettes';
include __DIR__ . '/header.php';
?>

<style>
  /* ── PAGE LAYOUT ── */
  .page-hero{background:linear-gradient(135deg,var(--green-dark) 0%,var(--green-main) 60%,var(--green-light) 100%);padding:3rem 5rem 2.5rem;color:var(--white);}
  .page-hero-inner{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;}
  .page-hero h1{font-family:"Playfair Display",serif;font-size:2.2rem;margin-bottom:.3rem;}
  .page-hero p{font-size:.95rem;opacity:.85;}
  .breadcrumb{font-size:.8rem;opacity:.7;margin-bottom:.8rem;}
  .breadcrumb a{color:var(--white);text-decoration:none;}
  .breadcrumb a:hover{text-decoration:underline;}

  /* ── CONTENT SECTION ── */
  .content-section{padding:3rem 5rem;}
  
  /* ── TOOLBAR ── */
  .table-toolbar{display:flex;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:2rem;}
  .search-input{flex:1;min-width:200px;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:"DM Sans",sans-serif;font-size:.88rem;outline:none;transition:border-color .2s;background:var(--white);}
  .search-input:focus{border-color:var(--green-main);}
  .filter-select{padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:"DM Sans",sans-serif;font-size:.88rem;outline:none;background:var(--white);cursor:pointer;}
  .filter-select:focus{border-color:var(--green-main);}
  .count-badge{background:var(--green-pale);color:var(--green-dark);font-size:.78rem;font-weight:700;padding:.3rem .8rem;border-radius:50px;white-space:nowrap;}

  /* ── ALIMENTS GRID ── */
  .aliments-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.8rem;}

  .aliment-card{background:var(--white);border-radius:16px;border:1px solid var(--border);overflow:hidden;transition:transform .25s,box-shadow .25s;display:flex;flex-direction:column;box-shadow:0 2px 12px rgba(45,106,31,.08);}
  .aliment-card:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(45,106,31,.15);}
  
  .aliment-card-img{position:relative;width:100%;height:200px;background:var(--green-pale);overflow:hidden;}
  .aliment-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
  .aliment-card:hover .aliment-card-img img{transform:scale(1.08);}
  .aliment-card-img-placeholder{width:100%;height:100%;display:grid;place-items:center;font-size:3rem;}

  .aliment-card-body{padding:1.4rem;flex:1;display:flex;flex-direction:column;}
  .aliment-card-title{font-family:"Playfair Display",serif;font-size:1.2rem;color:var(--green-dark);margin-bottom:.8rem;font-weight:700;}

  .macro-badges{display:flex;flex-direction:column;gap:.6rem;flex:1;}
  .macro-row{display:flex;align-items:center;justify-content:space-between;padding:.6rem;background:var(--card-bg);border-radius:10px;}
  .macro-label{font-size:.8rem;font-weight:600;color:var(--grey);}
  .macro-value{display:inline-block;padding:.2rem .6rem;border-radius:50px;font-size:.78rem;font-weight:700;}
  
  .macro-cal{background:#fff3cd;color:#856404;}
  .macro-prot{background:#d1e7dd;color:#0f5132;}
  .macro-gluc{background:#cff4fc;color:#0a4a5f;}
  .macro-lip{background:#f8d7da;color:#842029;}

  .empty-state{padding:4rem;text-align:center;color:var(--grey);}
  .empty-state .ei{font-size:3.5rem;margin-bottom:1rem;}
  .empty-state p{font-size:.95rem;}

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}.aliments-grid{grid-template-columns:1fr;}}
</style>

<!-- Page Hero -->
<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb"><a href="index.php">Accueil</a> › Aliments</div>
      <h1>🥦 Gestion des Aliments</h1>
      <p><?= count($aliments) ?> aliment<?= count($aliments) !== 1 ? 's' : '' ?> dans la base de données</p>
    </div>
  </div>
</div>

<!-- Content -->
<div class="content-section">
  <div class="table-toolbar">
    <input type="text" class="search-input" id="searchInput" placeholder="🔍 Rechercher un aliment…" oninput="filterCards()"/>
    <select class="filter-select" id="categorieFilter" onchange="filterByCategorie()">
      <option value="">Toutes catégories</option>
      <?php foreach ($allCategories as $cat): ?>
        <option value="<?= $cat->id ?>" <?= $selectedCategorieId === $cat->id ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat->nom) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <span class="count-badge" id="countBadge"><?= count($aliments) ?> aliment<?= count($aliments) !== 1 ? 's' : '' ?></span>
  </div>

  <?php if (empty($aliments)): ?>
    <div class="empty-state">
      <div class="ei">🥗</div>
      <p>Aucun aliment enregistré.</p>
    </div>
  <?php else: ?>
    <div class="aliments-grid" id="alimentsGrid">
      <?php foreach ($aliments as $a): ?>
      <div class="aliment-card" data-name="<?= htmlspecialchars(strtolower($a->nom)) ?>">
        <div class="aliment-card-img">
          <?php if (!empty($a->image)): ?>
            <img src="../<?= htmlspecialchars($a->image) ?>" alt="<?= htmlspecialchars($a->nom) ?>"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"/>
            <div class="aliment-card-img-placeholder" style="display:none;">🥗</div>
          <?php else: ?>
            <div class="aliment-card-img-placeholder">🥗</div>
          <?php endif; ?>
        </div>
        <div class="aliment-card-body">
          <div class="aliment-card-title"><?= htmlspecialchars($a->nom) ?></div>
          <div class="macro-badges">
            <div class="macro-row">
              <span class="macro-label">Calories</span>
              <span class="macro-value macro-cal"><?= $a->calories ?> kcal</span>
            </div>
            <div class="macro-row">
              <span class="macro-label">Protéines</span>
              <span class="macro-value macro-prot"><?= number_format($a->proteines, 1) ?> g</span>
            </div>
            <div class="macro-row">
              <span class="macro-label">Glucides</span>
              <span class="macro-value macro-gluc"><?= number_format($a->glucides, 1) ?> g</span>
            </div>
            <div class="macro-row">
              <span class="macro-label">Lipides</span>
              <span class="macro-value macro-lip"><?= number_format($a->lipides, 1) ?> g</span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
function filterCards() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  const cards = document.querySelectorAll('.aliment-card');
  let visible = 0;
  cards.forEach(card => {
    const match = card.dataset.name.includes(q);
    card.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  document.getElementById('countBadge').textContent = visible + ' aliment' + (visible !== 1 ? 's' : '');
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
</script>

<?php include __DIR__ . '/footer.php'; ?>

<?php include __DIR__ . '/footer.php'; ?>
