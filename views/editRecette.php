<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Recette.php';
require_once __DIR__ . '/../models/RecetteAliment.php';
require_once __DIR__ . '/../controllers/AlimentController.php';
require_once __DIR__ . '/../controllers/RecetteController.php';

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

$pageTitle = 'Modifier ' . $recette->nom;
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

  .content-section{padding:3rem 5rem;display:flex;justify-content:center;}
  .form-card{background:var(--white);border-radius:24px;border:1px solid var(--border);box-shadow:0 8px 32px rgba(45,106,31,.1);width:100%;max-width:820px;overflow:hidden;}
  .form-card-header{background:linear-gradient(135deg,var(--orange-light),#fdd6a8);padding:1.5rem 2rem;border-bottom:1px solid var(--border);}
  .form-card-header h2{font-family:"Playfair Display",serif;font-size:1.3rem;color:#7a3800;}
  .form-card-header p{font-size:.83rem;color:#a05020;margin-top:.2rem;}
  .form-card-body{padding:2rem;}

  .section-divider{font-family:"Playfair Display",serif;font-size:1rem;color:var(--green-dark);margin:1.8rem 0 1rem;padding-bottom:.5rem;border-bottom:2px solid var(--green-pale);display:flex;align-items:center;gap:.5rem;}

  .form-group{margin-bottom:1.4rem;}
  .form-group label{display:block;font-size:.83rem;font-weight:600;color:var(--green-dark);margin-bottom:.45rem;}
  .form-group label .req{color:var(--orange);}
  .form-control{width:100%;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.92rem;color:var(--black);outline:none;transition:border-color .2s,box-shadow .2s;background:var(--card-bg);}
  .form-control:focus{border-color:var(--green-main);background:var(--white);box-shadow:0 0 0 3px rgba(74,158,48,.12);}
  .form-control.is-invalid{border-color:#e74c3c;background:#fff8f8;}
  .invalid-feedback{color:#e74c3c;font-size:.78rem;margin-top:.3rem;display:flex;align-items:center;gap:.3rem;}
  textarea.form-control{resize:vertical;min-height:100px;}

  .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;}

  .img-upload-zone{border:2px dashed var(--border);border-radius:14px;padding:2rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--card-bg);position:relative;}
  .img-upload-zone:hover{border-color:var(--green-main);background:var(--green-pale);}
  .img-upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;}
  .img-upload-zone .iu-icon{font-size:2.5rem;margin-bottom:.5rem;}
  .img-upload-zone p{font-size:.83rem;color:var(--grey);}
  .current-img{max-width:120px;border-radius:10px;margin-bottom:1rem;border:1px solid var(--border);}
  #imgPreview{max-width:100%;max-height:160px;border-radius:10px;margin-top:1rem;display:none;}

  .ing-list{display:flex;flex-direction:column;gap:.7rem;margin-bottom:.8rem;}
  .ing-row{display:grid;grid-template-columns:1fr auto auto;gap:.7rem;align-items:center;background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:.7rem 1rem;transition:border-color .2s;}
  .ing-row:hover{border-color:var(--green-light);}
  .ing-select{padding:.6rem .8rem;border:1.5px solid var(--border);border-radius:8px;font-family:"DM Sans",sans-serif;font-size:.88rem;outline:none;background:var(--white);min-width:180px;transition:border-color .2s;}
  .ing-select:focus{border-color:var(--green-main);}
  .ing-qty{padding:.6rem .8rem;border:1.5px solid var(--border);border-radius:8px;font-family:"DM Sans",sans-serif;font-size:.88rem;outline:none;width:100px;background:var(--white);transition:border-color .2s;}
  .ing-qty:focus{border-color:var(--green-main);}
  .ing-remove{background:#fff0f0;color:#c0392b;border:1px solid #fcc;width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:1rem;display:grid;place-items:center;transition:all .2s;flex-shrink:0;}
  .ing-remove:hover{background:#c0392b;color:#fff;border-color:#c0392b;}
  .btn-add-ing{background:var(--green-pale);color:var(--green-dark);border:1.5px dashed var(--green-light);padding:.65rem 1.2rem;border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.88rem;font-weight:600;cursor:pointer;width:100%;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.4rem;}
  .btn-add-ing:hover{border-color:var(--green-main);border-style:solid;}
  .ing-error{color:#e74c3c;font-size:.78rem;margin-top:.3rem;display:none;align-items:center;gap:.3rem;}
  .ing-error.show{display:flex;}

  .form-actions{display:flex;gap:1rem;margin-top:2rem;flex-wrap:wrap;}
  .btn-save{background:linear-gradient(135deg,var(--orange),#d96510);color:var(--white);border:none;padding:.8rem 2rem;border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.95rem;font-weight:700;cursor:pointer;flex:1;transition:transform .2s,box-shadow .2s;}
  .btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(240,124,27,.35);}
  .btn-cancel{background:var(--card-bg);color:var(--grey);border:1.5px solid var(--border);padding:.8rem 1.5rem;border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.95rem;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;transition:all .2s;}
  .btn-cancel:hover{border-color:var(--grey);color:var(--black);}

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}.grid-2{grid-template-columns:1fr;}}
</style>

<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb">
        <a href="index.php">Accueil</a> › <a href="listRecette.php">Recettes</a> › Modifier
      </div>
      <h1>✏️ Modifier la Recette</h1>
      <p><?= htmlspecialchars($recette->nom) ?></p>
    </div>
  </div>
</div>

<div class="content-section">
  <div class="form-card">
    <div class="form-card-header">
      <h2>🔄 Modification de la recette</h2>
      <p>Les champs marqués d'un <span style="color:var(--orange)">*</span> sont obligatoires.</p>
    </div>
    <div class="form-card-body">
      <form method="POST" enctype="multipart/form-data" id="recetteForm" novalidate>

        <div class="section-divider">📋 Informations générales</div>

        <div class="form-group">
          <label>Nom de la recette <span class="req">*</span></label>
          <input type="text" name="nom" class="form-control <?= isset($errors['nom'])?'is-invalid':'' ?>"
            value="<?= htmlspecialchars($recette->nom) ?>" maxlength="150"/>
          <?php if (isset($errors['nom'])): ?>
            <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['nom']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Description <span class="req">*</span></label>
          <textarea name="description" class="form-control <?= isset($errors['description'])?'is-invalid':'' ?>"><?= htmlspecialchars($recette->description) ?></textarea>
          <?php if (isset($errors['description'])): ?>
            <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['description']) ?></div>
          <?php endif; ?>
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label>Temps de préparation (min) <span class="req">*</span></label>
            <input type="number" name="temps_preparation"
              class="form-control <?= isset($errors['temps_preparation'])?'is-invalid':'' ?>"
              value="<?= htmlspecialchars($recette->temps_preparation) ?>"
              min="1" max="1440"/>
            <?php if (isset($errors['temps_preparation'])): ?>
              <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['temps_preparation']) ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <label>Difficulté <span class="req">*</span></label>
            <select name="difficulte" class="form-control <?= isset($errors['difficulte'])?'is-invalid':'' ?>">
              <option value="">-- Choisir --</option>
              <?php foreach (['facile'=>'😊 Facile','moyen'=>'🔥 Moyen','difficile'=>'💪 Difficile'] as $val=>$label): ?>
                <option value="<?= $val ?>" <?= $recette->difficulte === $val ? 'selected' : '' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['difficulte'])): ?>
              <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['difficulte']) ?></div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Image -->
        <div class="form-group">
          <label>Image</label>
          <?php if ($recette->image && file_exists('../../' . $recette->image)): ?>
            <div style="margin-bottom:.8rem;">
              <p style="font-size:.8rem;color:var(--grey);margin-bottom:.5rem;">Image actuelle :</p>
              <img src="../../<?= htmlspecialchars($recette->image) ?>" class="current-img" alt=""/>
            </div>
          <?php endif; ?>
          <div class="img-upload-zone" onclick="document.getElementById('imgInput').click()">
            <input type="file" name="image" id="imgInput" accept="image/*" onchange="previewImg(this)"/>
            <div class="iu-icon">📷</div>
            <p><strong>Cliquez pour changer l'image</strong></p>
            <p>JPG, PNG, WebP — max 5 Mo</p>
            <img id="imgPreview" src="" alt="Aperçu"/>
          </div>
        </div>

        <!-- Ingredients -->
        <div class="section-divider">🥗 Ingrédients</div>

        <?php if (isset($errors['ingredients'])): ?>
          <div class="invalid-feedback" style="display:flex;margin-bottom:.8rem;">⚠️ <?= htmlspecialchars($errors['ingredients']) ?></div>
        <?php endif; ?>

        <div class="ing-list" id="ingList">
          <?php
          $dispIngs = !empty($currentIngredients) ? $currentIngredients : [['aliment_id'=>'','quantite'=>'','nom'=>'','calories'=>0]];
          foreach ($dispIngs as $i => $ing): ?>
          <div class="ing-row" id="ing-<?= $i ?>">
            <select name="ing_aliment[]" class="ing-select">
              <option value="">-- Choisir un aliment --</option>
              <?php foreach ($allAliments as $a): ?>
                <option value="<?= $a->id ?>" <?= (string)($ing['aliment_id'] ?? '') === (string)$a->id ? 'selected' : '' ?>>
                  <?= htmlspecialchars($a->nom) ?> (<?= $a->calories ?> kcal)
                </option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="ing_quantite[]" class="ing-qty"
              placeholder="ex: 100g" maxlength="50"
              value="<?= htmlspecialchars($ing['quantite'] ?? '') ?>"/>
            <button type="button" class="ing-remove" onclick="removeIng(this)" title="Retirer">✕</button>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="ing-error" id="ingError">⚠️ Veuillez ajouter au moins un ingrédient avec sa quantité.</div>

        <button type="button" class="btn-add-ing" onclick="addIng()">
          ➕ Ajouter un ingrédient
        </button>

        <div class="form-actions" style="margin-top:2rem;">
          <a href="listRecette.php" class="btn-cancel">← Annuler</a>
          <button type="submit" class="btn-save">🔄 Mettre à jour la recette</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const alimentsData = <?= json_encode(array_map(fn($a) => ['id'=>$a->id,'nom'=>$a->nom,'calories'=>$a->calories], $allAliments)) ?>;
let ingCounter = <?= count($dispIngs) ?>;

function buildSelect(selectedId='') {
  let html = '<option value="">-- Choisir un aliment --</option>';
  alimentsData.forEach(a => {
    const sel = String(a.id) === String(selectedId) ? 'selected' : '';
    html += `<option value="${a.id}" ${sel}>${a.nom} (${a.calories} kcal)</option>`;
  });
  return html;
}

function addIng() {
  const list = document.getElementById('ingList');
  const div  = document.createElement('div');
  div.className = 'ing-row';
  div.id = 'ing-' + ingCounter;
  div.innerHTML = `
    <select name="ing_aliment[]" class="ing-select">${buildSelect()}</select>
    <input type="text" name="ing_quantite[]" class="ing-qty" placeholder="ex: 100g" maxlength="50"/>
    <button type="button" class="ing-remove" onclick="removeIng(this)" title="Retirer">✕</button>`;
  div.style.opacity = '0';
  div.style.transform = 'translateY(-8px)';
  list.appendChild(div);
  requestAnimationFrame(() => {
    div.style.transition = 'opacity .3s,transform .3s';
    div.style.opacity = '1';
    div.style.transform = 'translateY(0)';
  });
  ingCounter++;
}

function removeIng(btn) {
  const row  = btn.closest('.ing-row');
  const list = document.getElementById('ingList');
  if (list.querySelectorAll('.ing-row').length <= 1) {
    showToast('⚠️ Il faut au moins un ingrédient.','error');
    return;
  }
  row.style.transition = 'opacity .3s,transform .3s';
  row.style.opacity = '0';
  row.style.transform = 'scale(.95)';
  setTimeout(() => row.remove(), 300);
}

function previewImg(input) {
  const preview = document.getElementById('imgPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}

document.getElementById('recetteForm').addEventListener('submit', function(e) {
  let valid = true;

  ['nom','description','temps_preparation','difficulte'].forEach(name => {
    const el = this.elements[name];
    if (!el) return;
    el.classList.remove('is-invalid');
    const prev = el.parentNode.querySelector('.client-err');
    if (prev) prev.remove();
    if (!el.value.trim()) {
      el.classList.add('is-invalid');
      const err = document.createElement('div');
      err.className = 'invalid-feedback client-err';
      err.textContent = '⚠️ Ce champ est obligatoire.';
      el.parentNode.appendChild(err);
      valid = false;
    }
  });

  const rows   = document.querySelectorAll('#ingList .ing-row');
  const ingErr = document.getElementById('ingError');
  let ingValid = rows.length > 0;

  rows.forEach(row => {
    const sel = row.querySelector('.ing-select');
    const qty = row.querySelector('.ing-qty');
    if (!sel.value) { ingValid = false; sel.style.borderColor='#e74c3c'; }
    else sel.style.borderColor = '';
    if (!qty.value.trim()) { ingValid = false; qty.style.borderColor='#e74c3c'; }
    else qty.style.borderColor = '';
  });

  if (!ingValid) { ingErr.classList.add('show'); valid = false; }
  else ingErr.classList.remove('show');

  if (!valid) e.preventDefault();
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
