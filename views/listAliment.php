<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Aliment.php';
require_once __DIR__ . '/../controllers/AlimentController.php';

$controller = new AlimentController();

// Handle delete via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    header('Content-Type: application/json');
    $id     = (int) ($_POST['id'] ?? 0);
    $result = $controller->delete($id);
    echo json_encode($result);
    exit;
}

$aliments = $controller->getAll();

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

  /* ── TABLE SECTION ── */
  .content-section{padding:3rem 5rem;}
  .table-card{background:var(--white);border-radius:20px;border:1px solid var(--border);overflow:hidden;box-shadow:0 4px 24px rgba(45,106,31,.08);}
  .table-toolbar{padding:1.2rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:1rem;flex-wrap:wrap;}
  .search-input{flex:1;min-width:200px;padding:.6rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:"DM Sans",sans-serif;font-size:.88rem;outline:none;transition:border-color .2s;background:var(--card-bg);}
  .search-input:focus{border-color:var(--green-main);}
  .count-badge{background:var(--green-pale);color:var(--green-dark);font-size:.78rem;font-weight:700;padding:.3rem .8rem;border-radius:50px;}

  table{width:100%;border-collapse:collapse;}
  thead tr{background:var(--card-bg);}
  th{padding:.9rem 1.2rem;text-align:left;font-size:.78rem;font-weight:700;color:var(--green-dark);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--border);}
  td{padding:.9rem 1.2rem;font-size:.88rem;border-bottom:1px solid var(--border);color:var(--black);}
  tr:last-child td{border-bottom:none;}
  tbody tr{transition:background .15s;}
  tbody tr:hover{background:var(--green-pale);}

  .aliment-img{width:44px;height:44px;border-radius:10px;object-fit:cover;border:1px solid var(--border);}
  .aliment-img-placeholder{width:44px;height:44px;border-radius:10px;background:var(--green-pale);display:grid;place-items:center;font-size:1.4rem;border:1px solid var(--border);}

  .macro-badge{display:inline-block;padding:.18rem .55rem;border-radius:50px;font-size:.72rem;font-weight:700;}
  .macro-cal{background:#fff3cd;color:#856404;}
  .macro-prot{background:#d1e7dd;color:#0f5132;}
  .macro-gluc{background:#cff4fc;color:#0a4a5f;}
  .macro-lip{background:#f8d7da;color:#842029;}

  .actions-cell{display:flex;gap:.5rem;align-items:center;}
  .btn-edit{background:var(--green-pale);color:var(--green-dark);border:1.5px solid var(--border);padding:.4rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:.3rem;}
  .btn-edit:hover{background:var(--green-main);color:var(--white);border-color:var(--green-main);}
  .btn-del{background:#fff0f0;color:#c0392b;border:1.5px solid #fcc;padding:.4rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;display:inline-flex;align-items:center;gap:.3rem;}
  .btn-del:hover{background:#c0392b;color:var(--white);border-color:#c0392b;}

  .empty-state{padding:4rem;text-align:center;color:var(--grey);}
  .empty-state .ei{font-size:3rem;margin-bottom:1rem;}
  .empty-state p{font-size:.95rem;}

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}th:nth-child(3),th:nth-child(4),th:nth-child(5),td:nth-child(3),td:nth-child(4),td:nth-child(5){display:none;}}
</style>

<!-- Page Hero -->
<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb"><a href="index.php">Accueil</a> › Aliments</div>
      <h1>🥦 Gestion des Aliments</h1>
      <p><?= count($aliments) ?> aliment<?= count($aliments) !== 1 ? 's' : '' ?> dans la base de données</p>
    </div>
    <a href="addAliment.php" class="btn-add">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Ajouter un aliment
    </a>
  </div>
</div>

<!-- Table -->
<div class="content-section">
  <div class="table-card">
    <div class="table-toolbar">
      <input type="text" class="search-input" id="searchInput" placeholder="🔍 Rechercher un aliment…" oninput="filterTable()"/>
      <span class="count-badge" id="countBadge"><?= count($aliments) ?> aliment<?= count($aliments) !== 1 ? 's' : '' ?></span>
    </div>

    <?php if (empty($aliments)): ?>
      <div class="empty-state">
        <div class="ei">🥗</div>
      <p>Aucun aliment enregistré.<br/><a href="addAliment.php" style="color:var(--green-main);font-weight:600;">Ajouter le premier aliment →</a></p>
      </div>
    <?php else: ?>
    <table id="alimentsTable">
      <thead>
        <tr>
          <th>Image</th>
          <th>Nom</th>
          <th>Calories</th>
          <th>Protéines</th>
          <th>Glucides</th>
          <th>Lipides</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($aliments as $a): ?>
        <tr data-name="<?= htmlspecialchars(strtolower($a->nom)) ?>">
          <td>
            <?php if ($a->image && file_exists('../../' . $a->image)): ?>
              <img src="../../<?= htmlspecialchars($a->image) ?>" class="aliment-img" alt="<?= htmlspecialchars($a->nom) ?>"/>
            <?php else: ?>
              <div class="aliment-img-placeholder">🥗</div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($a->nom) ?></strong></td>
          <td><span class="macro-badge macro-cal"><?= $a->calories ?> kcal</span></td>
          <td><span class="macro-badge macro-prot"><?= number_format($a->proteines, 1) ?> g</span></td>
          <td><span class="macro-badge macro-gluc"><?= number_format($a->glucides, 1) ?> g</span></td>
          <td><span class="macro-badge macro-lip"><?= number_format($a->lipides, 1) ?> g</span></td>
          <td>
            <div class="actions-cell">
              <a href="editAliment.php?id=<?= $a->id ?>" class="btn-edit">✏️ Modifier</a>
              <button class="btn-del" onclick="confirmDelete(<?= $a->id ?>, '<?= htmlspecialchars($a->nom, ENT_QUOTES) ?>')">🗑️ Supprimer</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<script>
function filterTable() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#alimentsTable tbody tr');
  let visible = 0;
  rows.forEach(r => {
    const match = r.dataset.name.includes(q);
    r.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  document.getElementById('countBadge').textContent = visible + ' aliment' + (visible !== 1 ? 's' : '');
}

function confirmDelete(id, name) {
  showConfirm(
    'Supprimer l\'aliment',
    `Voulez-vous vraiment supprimer « ${name} » ? Cette action est définitive.`,
    () => deleteAliment(id)
  );
}

function deleteAliment(id) {
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id', id);

  fetch('listAliment.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // Remove row
        const row = document.querySelector(`button[onclick*="confirmDelete(${id},"]`)?.closest('tr');
        if (row) {
          row.style.transition = 'opacity .4s';
          row.style.opacity = '0';
          setTimeout(() => row.remove(), 400);
        }
        showToast('✅ Aliment supprimé avec succès.', 'success');
      } else {
        showToast('❌ ' + (data.message || 'Erreur lors de la suppression.'), 'error');
      }
    })
    .catch(() => showToast('❌ Erreur réseau.', 'error'));
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
