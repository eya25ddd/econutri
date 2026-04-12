<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Aliment.php';
require_once __DIR__ . '/../controllers/AlimentController.php';

$controller = new AlimentController();
$errors     = [];

$id      = (int) ($_GET['id'] ?? 0);
$aliment = $controller->getById($id);

if (!$aliment) {
    header('Location: listAliment.php?error=notfound');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->update($id, $_POST);
    if ($result['success']) {
        header('Location: listAliment.php?success=updated');
        exit;
    }
    $errors  = $result['errors'];
    // Merge submitted values so form keeps the user's input
    $aliment = Aliment::fromArray(array_merge(
        ['id' => $id, 'image' => $aliment->image],
        $_POST
    ));
}

$pageTitle = 'Modifier ' . $aliment->nom;
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
  .form-card{background:var(--white);border-radius:24px;border:1px solid var(--border);box-shadow:0 8px 32px rgba(45,106,31,.1);width:100%;max-width:700px;overflow:hidden;}
  .form-card-header{background:linear-gradient(135deg,var(--orange-light),#fdd6a8);padding:1.5rem 2rem;border-bottom:1px solid var(--border);}
  .form-card-header h2{font-family:"Playfair Display",serif;font-size:1.3rem;color:#7a3800;}
  .form-card-header p{font-size:.83rem;color:#a05020;margin-top:.2rem;}
  .form-card-body{padding:2rem;}

  .form-group{margin-bottom:1.4rem;}
  .form-group label{display:block;font-size:.83rem;font-weight:600;color:var(--green-dark);margin-bottom:.45rem;}
  .form-group label .req{color:var(--orange);}
  .form-control{width:100%;padding:.75rem 1rem;border:1.5px solid var(--border);border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.92rem;color:var(--black);outline:none;transition:border-color .2s,box-shadow .2s;background:var(--card-bg);}
  .form-control:focus{border-color:var(--green-main);background:var(--white);box-shadow:0 0 0 3px rgba(74,158,48,.12);}
  .form-control.is-invalid{border-color:#e74c3c;background:#fff8f8;}
  .invalid-feedback{color:#e74c3c;font-size:.78rem;margin-top:.3rem;display:flex;align-items:center;gap:.3rem;}

  .grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;}

  .img-upload-zone{border:2px dashed var(--border);border-radius:14px;padding:2rem;text-align:center;cursor:pointer;transition:all .2s;background:var(--card-bg);position:relative;}
  .img-upload-zone:hover{border-color:var(--green-main);background:var(--green-pale);}
  .img-upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;}
  .img-upload-zone .iu-icon{font-size:2.5rem;margin-bottom:.5rem;}
  .img-upload-zone p{font-size:.83rem;color:var(--grey);}
  .current-img{max-width:120px;border-radius:10px;margin-bottom:1rem;border:1px solid var(--border);}
  #imgPreview{max-width:100%;max-height:160px;border-radius:10px;margin-top:1rem;display:none;}

  .form-actions{display:flex;gap:1rem;margin-top:2rem;flex-wrap:wrap;}
  .btn-save{background:linear-gradient(135deg,var(--orange),#d96510);color:var(--white);border:none;padding:.8rem 2rem;border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.95rem;font-weight:700;cursor:pointer;flex:1;transition:transform .2s,box-shadow .2s;}
  .btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(240,124,27,.35);}
  .btn-cancel{background:var(--card-bg);color:var(--grey);border:1.5px solid var(--border);padding:.8rem 1.5rem;border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.95rem;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;transition:all .2s;}
  .btn-cancel:hover{border-color:var(--grey);color:var(--black);}
  .helper-text{font-size:.76rem;color:var(--grey);margin-top:.3rem;}

  @media(max-width:768px){.content-section,.page-hero{padding:2rem 1.5rem;}.grid-4{grid-template-columns:1fr 1fr;}}
</style>

<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb">
        <a href="index.php">Accueil</a> › <a href="listAliment.php">Aliments</a> › Modifier
      </div>
      <h1>✏️ Modifier l'Aliment</h1>
      <p><?= htmlspecialchars($aliment->nom) ?></p>
    </div>
  </div>
</div>

<div class="content-section">
  <div class="form-card">
    <div class="form-card-header">
      <h2>🔄 Modification de l'aliment</h2>
      <p>Les champs marqués d'un <span style="color:var(--orange)">*</span> sont obligatoires.</p>
    </div>
    <div class="form-card-body">
      <form method="POST" enctype="multipart/form-data" id="alimentForm" novalidate>

        <!-- Nom -->
        <div class="form-group">
          <label>Nom de l'aliment <span class="req">*</span></label>
          <input type="text" name="nom" class="form-control <?= isset($errors['nom'])?'is-invalid':'' ?>"
            value="<?= htmlspecialchars($aliment->nom) ?>"
            placeholder="ex : Quinoa, Épinards…" maxlength="100"/>
          <?php if (isset($errors['nom'])): ?>
            <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['nom']) ?></div>
          <?php endif; ?>
        </div>

        <!-- Macros -->
        <div class="form-group">
          <label>Valeurs nutritionnelles <span class="req">*</span></label>
          <div class="grid-4">
            <div>
              <input type="number" name="calories" class="form-control <?= isset($errors['calories'])?'is-invalid':'' ?>"
                placeholder="Calories" min="0" max="9999" step="1"
                value="<?= htmlspecialchars($aliment->calories) ?>"/>
              <div class="helper-text">kcal / 100g</div>
              <?php if (isset($errors['calories'])): ?>
                <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['calories']) ?></div>
              <?php endif; ?>
            </div>
            <div>
              <input type="number" name="proteines" class="form-control <?= isset($errors['proteines'])?'is-invalid':'' ?>"
                placeholder="Protéines" min="0" max="999.99" step="0.1"
                value="<?= htmlspecialchars($aliment->proteines) ?>"/>
              <div class="helper-text">g / 100g</div>
              <?php if (isset($errors['proteines'])): ?>
                <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['proteines']) ?></div>
              <?php endif; ?>
            </div>
            <div>
              <input type="number" name="glucides" class="form-control <?= isset($errors['glucides'])?'is-invalid':'' ?>"
                placeholder="Glucides" min="0" max="999.99" step="0.1"
                value="<?= htmlspecialchars($aliment->glucides) ?>"/>
              <div class="helper-text">g / 100g</div>
              <?php if (isset($errors['glucides'])): ?>
                <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['glucides']) ?></div>
              <?php endif; ?>
            </div>
            <div>
              <input type="number" name="lipides" class="form-control <?= isset($errors['lipides'])?'is-invalid':'' ?>"
                placeholder="Lipides" min="0" max="999.99" step="0.1"
                value="<?= htmlspecialchars($aliment->lipides) ?>"/>
              <div class="helper-text">g / 100g</div>
              <?php if (isset($errors['lipides'])): ?>
                <div class="invalid-feedback">⚠️ <?= htmlspecialchars($errors['lipides']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Image -->
        <div class="form-group">
          <label>Image</label>
          <?php if ($aliment->image && file_exists('../../' . $aliment->image)): ?>
            <div style="margin-bottom:.8rem;">
              <p style="font-size:.8rem;color:var(--grey);margin-bottom:.5rem;">Image actuelle :</p>
              <img src="../../<?= htmlspecialchars($aliment->image) ?>" class="current-img" alt="Image actuelle"/>
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

        <div class="form-actions">
          <a href="listAliment.php" class="btn-cancel">← Annuler</a>
          <button type="submit" class="btn-save">🔄 Mettre à jour</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function previewImg(input) {
  const preview = document.getElementById('imgPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}

document.getElementById('alimentForm').addEventListener('submit', function(e) {
  let valid = true;
  const fields = ['nom','calories','proteines','glucides','lipides'];
  fields.forEach(name => {
    const el = this.elements[name];
    if (!el) return;
    const val = el.value.trim();
    el.classList.remove('is-invalid');
    const prev = el.parentNode.querySelector('.client-err');
    if (prev) prev.remove();
    if (val === '') {
      el.classList.add('is-invalid');
      const err = document.createElement('div');
      err.className = 'invalid-feedback client-err';
      err.textContent = '⚠️ Ce champ est obligatoire.';
      el.parentNode.appendChild(err);
      valid = false;
    }
  });
  if (!valid) e.preventDefault();
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
