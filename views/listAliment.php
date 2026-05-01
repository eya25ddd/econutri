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

// ── Statistics ──
$totalA     = count($allAliments);
$avgCal     = $totalA > 0 ? round(array_sum(array_map(fn($a) => $a->calories,  $allAliments)) / $totalA) : 0;
$avgProt    = $totalA > 0 ? round(array_sum(array_map(fn($a) => $a->proteines, $allAliments)) / $totalA, 1) : 0;
$avgGluc    = $totalA > 0 ? round(array_sum(array_map(fn($a) => $a->glucides,  $allAliments)) / $totalA, 1) : 0;
$avgLip     = $totalA > 0 ? round(array_sum(array_map(fn($a) => $a->lipides,   $allAliments)) / $totalA, 1) : 0;
$maxCal     = $totalA > 0 ? max(array_map(fn($a) => $a->calories, $allAliments)) : 0;
$minCal     = $totalA > 0 ? min(array_map(fn($a) => $a->calories, $allAliments)) : 0;
// Calorie ranges
$calLow     = count(array_filter($allAliments, fn($a) => $a->calories <= 100));
$calMid     = count(array_filter($allAliments, fn($a) => $a->calories > 100 && $a->calories <= 300));
$calHigh    = count(array_filter($allAliments, fn($a) => $a->calories > 300));

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
  .btn-add{background:var(--orange);color:var(--white);border:none;padding:.7rem 1.6rem;border-radius:50px;font-family:"DM Sans",sans-serif;font-size:.92rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.5rem;text-decoration:none;transition:transform .2s,box-shadow .2s;}
  .btn-add:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(240,124,27,.4);}
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

  /* ── STATS ── */
  .stats-section{padding:1.5rem 5rem 0;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;}
  .stat-card{background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:1rem 1.2rem;display:flex;align-items:center;gap:.9rem;box-shadow:0 2px 8px rgba(45,106,31,.06);}
  .stat-card-icon{font-size:1.8rem;flex-shrink:0;}
  .stat-card-val{font-family:"Playfair Display",serif;font-size:1.4rem;font-weight:700;color:var(--green-dark);line-height:1;}
  .stat-card-label{font-size:.72rem;color:var(--grey);margin-top:.2rem;}
  .stat-bar-section{padding:1.2rem 5rem 0;}
  .stat-bar-title{font-size:.8rem;font-weight:700;color:var(--grey);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.8rem;}
  .stat-bar-row{display:flex;align-items:center;gap:.8rem;margin-bottom:.5rem;}
  .stat-bar-label{font-size:.8rem;font-weight:600;width:90px;flex-shrink:0;}
  .stat-bar-track{flex:1;height:10px;background:var(--border);border-radius:50px;overflow:hidden;}
  .stat-bar-fill{height:100%;border-radius:50px;}
  .stat-bar-count{font-size:.78rem;font-weight:700;color:var(--grey);width:30px;text-align:right;flex-shrink:0;}

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}.aliments-grid{grid-template-columns:1fr;}.stats-section,.stat-bar-section,.smart-panel-wrap{padding:1rem 1.5rem 0;}}

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
  .reco-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.8rem;margin-top:.8rem;}
  .reco-card{background:var(--green-pale);border:1.5px solid var(--border);border-radius:12px;padding:.9rem;transition:all .2s;}
  .reco-card:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(45,106,31,.15);border-color:var(--green-main);}
  .reco-card-name{font-weight:700;font-size:.88rem;color:var(--green-dark);margin-bottom:.4rem;}
  .reco-badge{background:var(--white);border-radius:50px;padding:.15rem .5rem;font-size:.72rem;font-weight:600;display:inline-block;margin:.1rem;}
  .reco-score{font-size:.72rem;font-weight:700;color:var(--orange);margin-top:.4rem;}
  .reco-highlight{border-color:var(--orange)!important;background:linear-gradient(135deg,#fff8f0,var(--green-pale))!important;}
  .reco-rank{font-size:1.1rem;margin-bottom:.3rem;}
  .reco-empty{text-align:center;padding:1.5rem;color:var(--grey);font-size:.88rem;}
  @media(max-width:768px){.smart-panel-wrap{padding:1.5rem;}}
</style>

<!-- Page Hero -->
<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb"><a href="index.php">Accueil</a> › Aliments</div>
      <h1>🥦 Nos Aliments</h1>
      <p><?= count($aliments) ?> aliment<?= count($aliments) !== 1 ? 's' : '' ?> dans la base de données</p>
    </div>
    <button class="btn-add" onclick="exportPDF()" style="background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.4);">
      📄 Exporter PDF
    </button>
  </div>
</div>

<!-- ── STATISTICS ── -->
<div class="stats-section">
  <div class="stat-card">
    <div class="stat-card-icon">🥗</div>
    <div><div class="stat-card-val"><?= $totalA ?></div><div class="stat-card-label">Aliments au total</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon">🔥</div>
    <div><div class="stat-card-val"><?= $avgCal ?> kcal</div><div class="stat-card-label">Calories moyennes</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon">💪</div>
    <div><div class="stat-card-val"><?= $avgProt ?>g</div><div class="stat-card-label">Protéines moy.</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon">🌾</div>
    <div><div class="stat-card-val"><?= $avgGluc ?>g</div><div class="stat-card-label">Glucides moy.</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon">🧈</div>
    <div><div class="stat-card-val"><?= $avgLip ?>g</div><div class="stat-card-label">Lipides moy.</div></div>
  </div>
</div>

<?php if ($totalA > 0): ?>
<div class="stat-bar-section">
  <div class="stat-bar-title">Répartition calorique</div>
  <div class="stat-bar-row">
    <span class="stat-bar-label" style="color:#0f5132;">≤ 100 kcal</span>
    <div class="stat-bar-track"><div class="stat-bar-fill" style="width:<?= round($calLow/$totalA*100) ?>%;background:#4a9e30;"></div></div>
    <span class="stat-bar-count"><?= $calLow ?></span>
  </div>
  <div class="stat-bar-row">
    <span class="stat-bar-label" style="color:#856404;">101–300 kcal</span>
    <div class="stat-bar-track"><div class="stat-bar-fill" style="width:<?= round($calMid/$totalA*100) ?>%;background:#f07c1b;"></div></div>
    <span class="stat-bar-count"><?= $calMid ?></span>
  </div>
  <div class="stat-bar-row">
    <span class="stat-bar-label" style="color:#842029;">&gt; 300 kcal</span>
    <div class="stat-bar-track"><div class="stat-bar-fill" style="width:<?= round($calHigh/$totalA*100) ?>%;background:#e53935;"></div></div>
    <span class="stat-bar-count"><?= $calHigh ?></span>
  </div>
</div>
<?php endif; ?>

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
      <div class="aliment-card" data-name="<?= htmlspecialchars(strtolower($a->nom)) ?>" data-id="<?= $a->id ?>">
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
<!-- ══════════════════════════════════════════
   FILTRAGE INTELLIGENT + RECOMMANDATIONS
══════════════════════════════════════════ -->
<div class="smart-panel-wrap">

  <!-- FILTER -->
  <div class="smart-panel">
    <div class="sp-header" onclick="togglePanel('sfPanel')">
      <span>🔍 Filtrage intelligent des aliments</span>
      <span class="sp-arrow" id="arrow-sfPanel">▼</span>
    </div>
    <div class="sp-body" id="body-sfPanel">

      <div class="sp-section-label">🎯 Profil nutritionnel</div>
      <div class="chip-row" id="sfProfileChips">
        <button class="chip active" data-val="" onclick="chipSelect(this,'sfProfileChips'); applyAlimentFilter()">Tous</button>
        <button class="chip" data-val="low_cal" onclick="chipSelect(this,'sfProfileChips'); applyAlimentFilter()">⚖️ Faible calorie</button>
        <button class="chip" data-val="high_prot" onclick="chipSelect(this,'sfProfileChips'); applyAlimentFilter()">💪 Riche en protéines</button>
        <button class="chip" data-val="low_carb" onclick="chipSelect(this,'sfProfileChips'); applyAlimentFilter()">🌿 Low-carb</button>
        <button class="chip" data-val="low_fat" onclick="chipSelect(this,'sfProfileChips'); applyAlimentFilter()">🥗 Faible en graisses</button>
      </div>

      <div class="sp-section-label" style="margin-top:.9rem;">🔥 Plage calorique</div>
      <div class="chip-row" id="sfCalChips">
        <button class="chip active" data-val="" onclick="chipSelect(this,'sfCalChips'); applyAlimentFilter()">Toutes</button>
        <button class="chip" data-val="low" onclick="chipSelect(this,'sfCalChips'); applyAlimentFilter()">≤ 100 kcal</button>
        <button class="chip" data-val="mid" onclick="chipSelect(this,'sfCalChips'); applyAlimentFilter()">101–300 kcal</button>
        <button class="chip" data-val="high" onclick="chipSelect(this,'sfCalChips'); applyAlimentFilter()">&gt; 300 kcal</button>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:1rem;flex-wrap:wrap;gap:.5rem;">
        <span id="sfResultCount" style="font-size:.82rem;font-weight:600;color:var(--green-dark);"></span>
        <button onclick="resetAlimentFilter()" style="background:none;border:1.5px solid var(--border);border-radius:8px;padding:.4rem 1rem;font-size:.82rem;font-weight:600;cursor:pointer;color:var(--grey);font-family:'DM Sans',sans-serif;">↺ Réinitialiser</button>
      </div>
    </div>
  </div>

  <!-- RECOMMENDATIONS -->
  <div class="smart-panel">
    <div class="sp-header" onclick="togglePanel('recoPanel')">
      <span>✨ Recommandations nutritionnelles</span>
      <span class="sp-arrow" id="arrow-recoPanel">▼</span>
    </div>
    <div class="sp-body collapsed" id="body-recoPanel">
      <p style="font-size:.85rem;color:var(--grey);margin-bottom:1rem;">Choisissez votre objectif — on sélectionne les aliments les plus adaptés.</p>

      <div class="chip-row" id="recoGoalChips" style="margin-bottom:.8rem;">
        <button class="chip active" data-val="" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">Tous</button>
        <button class="chip" data-val="perte_poids" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">⚖️ Perte de poids</button>
        <button class="chip" data-val="muscle" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">💪 Muscle</button>
        <button class="chip" data-val="low_carb" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">🌿 Low-carb</button>
        <button class="chip" data-val="energie" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">⚡ Énergie</button>
        <button class="chip" data-val="leger" onclick="chipSelect(this,'recoGoalChips'); generateRecommendations()">🥗 Léger</button>
      </div>

      <div id="recoResults" style="margin-top:1.2rem;"></div>
    </div>
  </div>

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

/* ══════════════════════════════════════════
   SMART FILTER + RECOMMENDATIONS (ALIMENTS)
══════════════════════════════════════════ */
const ALL_ALIMENTS = <?= json_encode(array_values(array_map(fn($a) => [
    'id'        => $a->id,
    'nom'       => $a->nom,
    'calories'  => $a->calories,
    'proteines' => round($a->proteines, 1),
    'glucides'  => round($a->glucides, 1),
    'lipides'   => round($a->lipides, 1),
], $allAliments))) ?>;

const PROFILE_RULES = {
  low_cal:   a => a.calories < 100,
  high_prot: a => a.proteines > 15,
  low_carb:  a => a.glucides < 10,
  low_fat:   a => a.lipides < 5,
};

function applyAlimentFilter() {
  const profile  = document.querySelector('#sfProfileChips .chip.active')?.dataset.val || '';
  const calRange = document.querySelector('#sfCalChips .chip.active')?.dataset.val || '';

  const filtered = ALL_ALIMENTS.filter(a => {
    if (profile && PROFILE_RULES[profile] && !PROFILE_RULES[profile](a)) return false;
    if (calRange === 'low'  && a.calories > 100) return false;
    if (calRange === 'mid'  && (a.calories <= 100 || a.calories > 300)) return false;
    if (calRange === 'high' && a.calories <= 300) return false;
    return true;
  });

  const filteredIds = new Set(filtered.map(a => a.id));

  document.querySelectorAll('#alimentsGrid .aliment-card').forEach(card => {
    card.style.display = filteredIds.has(parseInt(card.dataset.id)) ? '' : 'none';
  });

  const el = document.getElementById('sfResultCount');
  if (el) el.textContent = filtered.length + ' aliment' + (filtered.length !== 1 ? 's' : '') + ' trouvé' + (filtered.length !== 1 ? 's' : '');
  const badge = document.getElementById('countBadge');
  if (badge) badge.textContent = filtered.length + ' aliment' + (filtered.length !== 1 ? 's' : '');
}

function resetAlimentFilter() {
  ['sfProfileChips','sfCalChips'].forEach(id => {
    const group = document.getElementById(id);
    if (!group) return;
    group.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    group.querySelector('.chip').classList.add('active');
  });
  document.querySelectorAll('#alimentsGrid .aliment-card').forEach(c => c.style.display = '');
  const el = document.getElementById('sfResultCount');
  if (el) el.textContent = ALL_ALIMENTS.length + ' aliment' + (ALL_ALIMENTS.length !== 1 ? 's' : '') + ' trouvé' + (ALL_ALIMENTS.length !== 1 ? 's' : '');
}

function generateRecommendations() {
  const goal = document.querySelector('#recoGoalChips .chip.active')?.dataset.val || '';
  const container = document.getElementById('recoResults');
  if (!goal) { container.innerHTML = '<div class="reco-empty">Choisissez un objectif pour voir les recommandations.</div>'; return; }

  const GOAL_SCORE = {
    perte_poids: a => Math.max(0, 500 - a.calories) / 5 + Math.max(0, 20 - a.lipides),
    muscle:      a => a.proteines * 4,
    low_carb:    a => Math.max(0, 50 - a.glucides) * 2 + a.proteines,
    energie:     a => a.glucides * 1.5 + a.calories / 20,
    leger:       a => Math.max(0, 15 - a.lipides) * 3 + Math.max(0, 200 - a.calories) / 10,
  };

  const scored = ALL_ALIMENTS
    .map(a => ({ ...a, score: GOAL_SCORE[goal](a) }))
    .sort((a, b) => b.score - a.score)
    .slice(0, 8);

  const medals = ['🥇','🥈','🥉','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣'];
  const goalLabels = { perte_poids:'⚖️ Perte de poids', muscle:'💪 Muscle', low_carb:'🌿 Low-carb', energie:'⚡ Énergie', leger:'🥗 Léger' };

  container.innerHTML = `
    <div style="font-size:.82rem;color:var(--grey);margin-bottom:.8rem;">
      Top aliments pour <strong>${goalLabels[goal]}</strong>
    </div>
    <div class="reco-grid">
      ${scored.map((a, i) => `
        <div class="reco-card ${i === 0 ? 'reco-highlight' : ''}">
          <div class="reco-rank">${medals[i]}</div>
          <div class="reco-card-name">${a.nom}</div>
          <div>
            <span class="reco-badge" style="color:#856404;background:#fff3cd;">🔥 ${a.calories} kcal</span>
            <span class="reco-badge" style="color:#0f5132;background:#d1e7dd;">💪 ${a.proteines}g</span>
            <span class="reco-badge" style="color:#0a4a5f;background:#cff4fc;">🌾 ${a.glucides}g</span>
            <span class="reco-badge" style="color:#842029;background:#f8d7da;">🧈 ${a.lipides}g</span>
          </div>
          <div class="reco-score">Score: ${Math.round(a.score)} pts</div>
        </div>`).join('')}
    </div>`;
}

function togglePanel(id) {
  const body  = document.getElementById('body-' + id);
  const arrow = document.getElementById('arrow-' + id);
  body.classList.toggle('collapsed');
  arrow.style.transform = body.classList.contains('collapsed') ? 'rotate(-90deg)' : '';
}

function chipSelect(el, groupId) {
  document.getElementById(groupId).querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
}

// Init count
document.addEventListener('DOMContentLoaded', function() {
  const el = document.getElementById('sfResultCount');
  if (el) el.textContent = ALL_ALIMENTS.length + ' aliment' + (ALL_ALIMENTS.length !== 1 ? 's' : '') + ' trouvé' + (ALL_ALIMENTS.length !== 1 ? 's' : '');
});
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
  doc.text('Liste des Aliments', 14, 20);
  doc.setFontSize(9);
  doc.text('Exporte le ' + dateStr, 283, 13, { align: 'right' });

  // Stats boxes
  const statsData = [
    { label: 'Total',        val: '<?= $totalA ?>',    x: 44  },
    { label: 'Cal. moy.',    val: '<?= $avgCal ?> kcal', x: 110 },
    { label: 'Prot. moy.',   val: '<?= $avgProt ?>g',  x: 176 },
    { label: 'Gluc. moy.',   val: '<?= $avgGluc ?>g',  x: 242 },
  ];
  [14, 80, 146, 212].forEach(x => {
    doc.setFillColor(232, 245, 225);
    doc.roundedRect(x, 32, 60, 16, 3, 3, 'F');
  });
  statsData.forEach(s => {
    doc.setFont('helvetica', 'bold'); doc.setFontSize(12); doc.setTextColor(45, 106, 31);
    doc.text(s.val, s.x, 41, { align: 'center' });
    doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(100, 100, 100);
    doc.text(s.label, s.x, 46, { align: 'center' });
  });

  // Collect visible cards
  const cards = [...document.querySelectorAll('#alimentsGrid .aliment-card')].filter(c => c.style.display !== 'none');
  const rows = cards.map((card, i) => {
    const name = stripText(card.querySelector('.aliment-card-title')?.textContent || '');
    const macros = [...card.querySelectorAll('.macro-value')];
    const cal  = macros[0]?.textContent.replace(/[^\d\.]/g,'').trim() || '-';
    const prot = macros[1]?.textContent.replace(/[^\d\.]/g,'').trim() || '-';
    const gluc = macros[2]?.textContent.replace(/[^\d\.]/g,'').trim() || '-';
    const lip  = macros[3]?.textContent.replace(/[^\d\.]/g,'').trim() || '-';
    return [i+1, name, cal + ' kcal', prot + ' g', gluc + ' g', lip + ' g'];
  });

  doc.autoTable({
    startY: 53,
    head: [['#', 'Nom', 'Calories', 'Proteines', 'Glucides', 'Lipides']],
    body: rows,
    styles: { font:'helvetica', fontSize:10, cellPadding:5, valign:'middle' },
    headStyles: { fillColor:[45,106,31], textColor:255, fontStyle:'bold', fontSize:10 },
    alternateRowStyles: { fillColor:[245,252,240] },
    columnStyles: {
      0: { cellWidth:12, halign:'center' },
      1: { cellWidth:80, fontStyle:'bold' },
      2: { cellWidth:40, halign:'center', textColor:[133,100,4], fillColor:[255,243,205] },
      3: { cellWidth:40, halign:'center', textColor:[15,81,50],  fillColor:[209,231,221] },
      4: { cellWidth:40, halign:'center', textColor:[10,74,95],  fillColor:[207,244,252] },
      5: { cellWidth:40, halign:'center', textColor:[132,32,41], fillColor:[248,215,218] },
    },
    didDrawPage(data) {
      const p = doc.internal.getNumberOfPages();
      doc.setFontSize(8); doc.setTextColor(150);
      doc.text('EcoNutri - Aliments  |  Page ' + data.pageNumber + ' / ' + p, 148.5, 205, { align:'center' });
    }
  });

  doc.save('EcoNutri_Aliments_' + now.toISOString().slice(0,10) + '.pdf');
}

/* ── PAGE-LEVEL TRANSLATIONS ── */
const pageTranslations = {
  fr: {
    hero_title: 'Nos Aliments',
    btn_export: 'Exporter PDF',
    stat_total: 'Aliments au total',
    stat_avg_cal: 'Calories moyennes',
    stat_avg_prot: 'Protéines moy.',
    stat_avg_gluc: 'Glucides moy.',
    stat_avg_lip: 'Lipides moy.',
    stat_bar_title: 'Répartition calorique',
    bar_low: '≤ 100 kcal',
    bar_mid: '101–300 kcal',
    bar_high: '> 300 kcal',
    search_ph: 'Rechercher un aliment…',
    filter_all_cat: 'Toutes catégories',
    empty: 'Aucun aliment enregistré.',
    macro_cal: 'Calories',
    macro_prot: 'Protéines',
    macro_gluc: 'Glucides',
    macro_lip: 'Lipides',
  },
  en: {
    hero_title: 'Our Foods',
    btn_export: 'Export PDF',
    stat_total: 'Total foods',
    stat_avg_cal: 'Avg. calories',
    stat_avg_prot: 'Avg. protein',
    stat_avg_gluc: 'Avg. carbs',
    stat_avg_lip: 'Avg. fat',
    stat_bar_title: 'Calorie breakdown',
    bar_low: '≤ 100 kcal',
    bar_mid: '101–300 kcal',
    bar_high: '> 300 kcal',
    search_ph: 'Search a food…',
    filter_all_cat: 'All categories',
    empty: 'No foods registered.',
    macro_cal: 'Calories',
    macro_prot: 'Protein',
    macro_gluc: 'Carbs',
    macro_lip: 'Fat',
  },
  ar: {
    hero_title: 'أغذيتنا',
    btn_export: 'تصدير PDF',
    stat_total: 'إجمالي الأغذية',
    stat_avg_cal: 'متوسط السعرات',
    stat_avg_prot: 'متوسط البروتين',
    stat_avg_gluc: 'متوسط الكربوهيدرات',
    stat_avg_lip: 'متوسط الدهون',
    stat_bar_title: 'توزيع السعرات الحرارية',
    bar_low: '≤ 100 سعرة',
    bar_mid: '101–300 سعرة',
    bar_high: '> 300 سعرة',
    search_ph: 'ابحث عن غذاء…',
    filter_all_cat: 'كل الفئات',
    empty: 'لا توجد أغذية مسجلة.',
    macro_cal: 'سعرات',
    macro_prot: 'بروتين',
    macro_gluc: 'كربوهيدرات',
    macro_lip: 'دهون',
  }
};

function applyPageLang(lang) {
  const t = pageTranslations[lang] || pageTranslations.fr;

  // Hero title
  const h1 = document.querySelector('.page-hero h1');
  if (h1) h1.textContent = '🥦 ' + t.hero_title;

  // Export button
  const exportBtn = document.querySelector('.page-hero .btn-add');
  if (exportBtn) exportBtn.textContent = '📄 ' + t.btn_export;

  // Stat card labels
  const statLabels = document.querySelectorAll('.stat-card-label');
  const statKeys = ['stat_total','stat_avg_cal','stat_avg_prot','stat_avg_gluc','stat_avg_lip'];
  statLabels.forEach((el, i) => { if (statKeys[i]) el.textContent = t[statKeys[i]]; });

  // Stat bar title
  const barTitle = document.querySelector('.stat-bar-title');
  if (barTitle) barTitle.textContent = t.stat_bar_title;

  // Calorie bar labels
  const barLabels = document.querySelectorAll('.stat-bar-label');
  if (barLabels[0]) barLabels[0].textContent = t.bar_low;
  if (barLabels[1]) barLabels[1].textContent = t.bar_mid;
  if (barLabels[2]) barLabels[2].textContent = t.bar_high;

  // Search placeholder
  const si = document.getElementById('searchInput');
  if (si) si.placeholder = '🔍 ' + t.search_ph;

  // Category filter first option
  const catFilter = document.getElementById('categorieFilter');
  if (catFilter && catFilter.options[0]) catFilter.options[0].text = t.filter_all_cat;

  // Empty state
  const empty = document.querySelector('.empty-state p');
  if (empty) empty.textContent = t.empty;

  // Macro labels on all cards
  document.querySelectorAll('.macro-label').forEach(el => {
    const txt = el.textContent.trim();
    if (txt === 'Calories' || txt === 'Calories' || txt === 'سعرات') el.textContent = t.macro_cal;
    else if (/Prot|Protein|بروتين/i.test(txt)) el.textContent = t.macro_prot;
    else if (/Gluc|Carb|كربو/i.test(txt)) el.textContent = t.macro_gluc;
    else if (/Lipid|Fat|دهون/i.test(txt)) el.textContent = t.macro_lip;
  });
}

document.addEventListener('langChange', function(e) { applyPageLang(e.detail.lang); });
// Apply saved language on load
(function() {
  const saved = localStorage.getItem('econutri_lang');
  if (saved && saved !== 'fr') applyPageLang(saved);
})();
</script>

<?php include __DIR__ . '/footer.php'; ?>
