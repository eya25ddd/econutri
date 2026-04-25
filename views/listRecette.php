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

  /* Cart Button */
  .btn-cart{background:var(--orange);color:var(--white);border:none;padding:.45rem .9rem;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;display:flex;align-items:center;gap:.4rem;justify-content:center;}
  .btn-cart:hover{background:#d66a15;transform:scale(1.05);}
  .btn-cart.in-cart{background:var(--green-main);}

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
    <button class="btn-add" onclick="openMyOrders()" style="background:rgba(255,255,255,0.2);border:2px solid rgba(255,255,255,0.5);">
      📋 Mes Commandes
    </button>
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
        <div style="padding:0 1.2rem 1.2rem;">
          <button class="btn-cart" onclick="event.stopPropagation(); addToCart(<?= $r->id ?>, '<?= htmlspecialchars(addslashes($r->nom)) ?>', '<?= htmlspecialchars($r->image ?? '') ?>', <?= $r->temps_preparation ?>)">
            🛒 Ajouter au panier
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
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
  const badge = document.getElementById('cartBadge');
  const cartBody = document.getElementById('cartBody');
  const cartFooter = document.getElementById('cartFooter');
  const cartTotal = document.getElementById('cartTotal');

  if (cart.length === 0) {
    badge.style.display = 'none';
    cartBody.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛒</div><p>Votre panier est vide</p></div>';
    cartFooter.style.display = 'none';
  } else {
    badge.style.display = 'flex';
    badge.textContent = cart.length;
    cartFooter.style.display = 'block';
    cartTotal.textContent = cart.length + ' recette' + (cart.length !== 1 ? 's' : '');

    cartBody.innerHTML = cart.map(item => `
      <div class="cart-item">
        <img src="../${item.image || 'uploads/placeholder.png'}" alt="${item.name}" class="cart-item-img" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2260%22%3E🍽️%3C/text%3E%3C/svg%3E'">
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-meta">⏱ ${item.time} min</div>
        </div>
        <button class="cart-item-remove" onclick="removeFromCart(${item.id})">×</button>
      </div>
    `).join('');
  }

  // Update button states
  document.querySelectorAll('.rcard').forEach(card => {
    const id = parseInt(card.dataset.id);
    const btn = card.querySelector('.btn-cart');
    if (btn) {
      if (cart.some(item => item.id === id)) {
        btn.classList.add('in-cart');
        btn.innerHTML = '✓ Dans le panier';
      } else {
        btn.classList.remove('in-cart');
        btn.innerHTML = '🛒 Ajouter au panier';
      }
    }
  });
}

function addToCart(id, name, image, time) {
  if (cart.some(item => item.id === id)) {
    removeFromCart(id);
    return;
  }

  cart.push({ id, name, image, time });
  localStorage.setItem('recetteCart', JSON.stringify(cart));
  updateCartUI();
  
  // Show toast
  const toast = document.createElement('div');
  toast.style.cssText = 'position:fixed;bottom:6rem;right:2rem;background:var(--green-dark);color:white;padding:1rem 1.5rem;border-radius:12px;font-size:.9rem;z-index:300;box-shadow:0 8px 24px rgba(0,0,0,0.2);animation:slideIn 0.3s;';
  toast.innerHTML = '✅ Ajouté au panier';
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'slideOut 0.3s';
    setTimeout(() => toast.remove(), 300);
  }, 2000);
}

function removeFromCart(id) {
  cart = cart.filter(item => item.id !== id);
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
    '<strong>Récapitulatif:</strong><br>' + cart.map(i => '🍽️ ' + i.name).join('<br>');
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

<?php include __DIR__ . '/footer.php'; ?>
