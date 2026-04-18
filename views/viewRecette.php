<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Recette.php';
require_once __DIR__ . '/../controllers/RecetteController.php';

$controller = new RecetteController();
$id = (int) ($_GET['id'] ?? 0);
$recette = $controller->getById($id);

if (!$recette) {
    header('Location: dashboard.php');
    exit;
}

$aliments = $controller->getAliments($id);

$pageTitle = $recette->nom;
$activeNav = 'dashboard';
include __DIR__ . '/header.php';
?>

<style>
  .page-hero {
    background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-main) 60%, var(--green-light) 100%);
    padding: 3rem 5rem 2.5rem;
    color: var(--white);
  }
  .page-hero-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .page-hero h1 {
    font-family: "Playfair Display", serif;
    font-size: 2.2rem;
    margin-bottom: .3rem;
  }
  .page-hero p {
    font-size: .95rem;
    opacity: .85;
  }
  .breadcrumb {
    font-size: .8rem;
    opacity: .7;
    margin-bottom: .8rem;
  }
  .breadcrumb a {
    color: var(--white);
    text-decoration: none;
  }
  .breadcrumb a:hover {
    text-decoration: underline;
  }

  .content-section {
    padding: 3rem 5rem;
    display: flex;
    justify-content: center;
  }
  .recipe-detail {
    background: var(--white);
    border-radius: 24px;
    border: 1px solid var(--border);
    box-shadow: 0 8px 32px rgba(45,106,31,.1);
    width: 100%;
    max-width: 900px;
    overflow: hidden;
  }
  .recipe-header {
    position: relative;
    height: 300px;
    overflow: hidden;
    background: var(--green-pale);
  }
  .recipe-header img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .recipe-header-placeholder {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    font-size: 5rem;
  }
  .recipe-diff {
    position: absolute;
    top: 1rem;
    left: 1rem;
    font-size: .75rem;
    font-weight: 700;
    padding: .3rem .8rem;
    border-radius: 50px;
    text-transform: uppercase;
  }
  .diff-facile {
    background: #d1e7dd;
    color: #0f5132;
  }
  .diff-moyen {
    background: #fff3cd;
    color: #856404;
  }
  .diff-difficile {
    background: #f8d7da;
    color: #842029;
  }
  .recipe-body {
    padding: 2rem;
  }
  .recipe-title {
    font-family: "Playfair Display", serif;
    font-size: 1.8rem;
    color: var(--black);
    margin-bottom: .5rem;
  }
  .recipe-meta {
    display: flex;
    gap: 1.5rem;
    font-size: .9rem;
    color: var(--grey);
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }
  .recipe-meta span {
    display: flex;
    align-items: center;
    gap: .4rem;
  }
  .recipe-desc {
    font-size: 1rem;
    line-height: 1.6;
    color: var(--black);
    margin-bottom: 2rem;
  }
  .ingredients-section {
    margin-bottom: 2rem;
  }
  .section-title {
    font-family: "Playfair Display", serif;
    font-size: 1.2rem;
    color: var(--green-dark);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
  }
  .ingredients-list {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
  }
  .ingredient-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .5rem 0;
    border-bottom: 1px solid var(--border);
  }
  .ingredient-item:last-child {
    border-bottom: none;
  }
  .ingredient-name {
    font-weight: 600;
    color: var(--black);
  }
  .ingredient-qty {
    color: var(--green-dark);
    font-weight: 500;
  }
  .recipe-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
  }
  .btn-back {
    background: var(--card-bg);
    color: var(--grey);
    border: 1.5px solid var(--border);
    padding: .8rem 1.5rem;
    border-radius: 12px;
    font-family: "DM Sans", sans-serif;
    font-size: .95rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
  }
  .btn-back:hover {
    border-color: var(--grey);
    color: var(--black);
  }
  .btn-favorite {
    background: linear-gradient(135deg, var(--orange), #e66a0a);
    color: var(--white);
    border: none;
    padding: .8rem 1.5rem;
    border-radius: 12px;
    font-family: "DM Sans", sans-serif;
    font-size: .95rem;
    font-weight: 700;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
  }
  .btn-favorite:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(240,124,27,.4);
  }

  @media(max-width:768px) {
    .content-section, .page-hero {
      padding: 2rem 1.5rem;
    }
    .recipe-body {
      padding: 1.5rem;
    }
    .recipe-meta {
      flex-direction: column;
      gap: .5rem;
    }
  }
</style>

<div class="page-hero">
  <div class="page-hero-inner">
    <div>
      <div class="breadcrumb">
        <a href="index.php">Accueil</a> › <a href="backoffice/dashboard.php">Recettes</a> › <?= htmlspecialchars($recette->nom) ?>
      </div>
      <h1>🍽️ <?= htmlspecialchars($recette->nom) ?></h1>
      <p>Découvrez cette recette délicieuse</p>
    </div>
  </div>
</div>

<div class="content-section">
  <div class="recipe-detail">
    <div class="recipe-header">
      <?php if (!empty($recette->image)): ?>
        <img src="../<?= htmlspecialchars($recette->image) ?>" alt="<?= htmlspecialchars($recette->nom) ?>"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
        <div class="recipe-header-placeholder" style="display:none;">🍽️</div>
      <?php else: ?>
        <div class="recipe-header-placeholder">🍽️</div>
      <?php endif; ?>
      <div class="recipe-diff diff-<?= strtolower($recette->difficulte) ?>">
        <?= htmlspecialchars($recette->difficulte) ?>
      </div>
    </div>
    <div class="recipe-body">
      <div class="recipe-title"><?= htmlspecialchars($recette->nom) ?></div>
      <div class="recipe-meta">
        <span>⏱️ <?= $recette->temps_preparation ?> minutes</span>
        <span>🥕 <?= count($aliments) ?> ingrédients</span>
        <span>📅 Créée le <?= date('d/m/Y', strtotime($recette->date_creation)) ?></span>
      </div>
      <div class="recipe-desc">
        <?= nl2br(htmlspecialchars($recette->description)) ?>
      </div>
      <div class="ingredients-section">
        <div class="section-title">
          🥕 Ingrédients
        </div>
        <div class="ingredients-list">
          <?php foreach ($aliments as $aliment): ?>
            <div class="ingredient-item">
              <div class="ingredient-name"><?= htmlspecialchars($aliment['nom']) ?></div>
              <div class="ingredient-qty"><?= $aliment['quantite'] ?> g</div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="recipe-actions">
        <a href="listRecette.php" class="btn-back">← Retour aux recettes</a>
        <button class="btn-favorite">❤️ Ajouter aux favoris</button>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>