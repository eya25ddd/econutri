<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Recette.php';
require_once __DIR__ . '/../models/RecetteAliment.php';
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../controllers/RecetteController.php';
require_once __DIR__ . '/../controllers/CategorieController.php';

$controller = new RecetteController();
$categorieController = new CategorieController();

// Handle AJAX delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    header('Content-Type: application/json');
    $id     = (int) ($_POST['id'] ?? 0);
    $result = $controller->delete($id);
    echo json_encode($result);
    exit;
}

// Get filter parameters
$selectedCategorieId = (int) ($_GET['categorie'] ?? 0);

// Get all recettes
$allRecettes = $controller->getAll();

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

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}.recipes-grid{grid-template-columns:1fr;}}
</style>

<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb"><a href="index.php">Accueil</a> › Recettes</div>
      <h1>🍽️ Gestion des Recettes</h1>
      <p><?= count($recettes) ?> recette<?= count($recettes) !== 1 ? 's' : '' ?> enregistrée<?= count($recettes) !== 1 ? 's' : '' ?></p>
    </div>
    <a href="addRecette.php" class="btn-add">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Ajouter une recette
    </a>
  </div>
</div>

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
      <a href="viewRecette.php?id=<?= $r->id ?>" class="rcard"
           data-name="<?= htmlspecialchars(strtolower($r->nom)) ?>"
           data-diff="<?= htmlspecialchars($r->difficulte) ?>"
           style="text-decoration:none;color:inherit;">
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
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
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

<?php include __DIR__ . '/footer.php'; ?>
