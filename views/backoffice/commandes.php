
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/CommandeController.php';

$controller = new CommandeController();

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $msg    = trim($_POST['message'] ?? '');

    if ($action === 'accept') {
        echo json_encode($controller->accept($id, $msg));
    } elseif ($action === 'reject') {
        echo json_encode($controller->reject($id, $msg));
    } elseif ($action === 'delete') {
        echo json_encode($controller->delete($id));
    } else {
        echo json_encode(['success' => false, 'message' => 'Action inconnue.']);
    }
    exit;
}

$commandes  = $controller->getAll();
$nbPending  = $controller->countByStatus('pending');
$nbAccepted = $controller->countByStatus('accepted');
$nbRejected = $controller->countByStatus('rejected');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoNutri – Commandes</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--green-dark:#2d6a1f;--green-main:#4a9e30;--green-light:#7ec44f;--green-pale:#e8f5e1;--orange:#f07c1b;--black:#111;--grey:#666;--grey-light:#999;--white:#fff;--border:#e4eed9;--sidebar-bg:#0e2a08;--sidebar-w:260px;--topbar-h:68px;--bg:#f2f8ee;--card-bg:#fff;--red:#e53935;--red-light:#fdecea;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:"DM Sans",sans-serif;background:var(--bg);color:var(--black);display:flex;min-height:100vh;}

    /* SIDEBAR */
    .sidebar{width:var(--sidebar-w);background:var(--sidebar-bg);min-height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;z-index:50;overflow-y:auto;}
    .sidebar-logo{padding:1.4rem 1.6rem;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:.7rem;text-decoration:none;}
    .logo-icon{width:40px;height:40px;background:rgba(255,255,255,.1);border-radius:10px;display:grid;place-items:center;border:1px solid rgba(255,255,255,.2);}
    .logo-text{font-family:"Playfair Display",serif;font-size:1.35rem;color:var(--white);}
    .logo-text span{color:var(--orange);}
    .sidebar-admin-tag{background:rgba(240,124,27,.18);color:var(--orange);font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:4px;text-transform:uppercase;margin-left:auto;}
    .sidebar-section{padding:1.2rem .9rem .4rem;}
    .sidebar-section-label{font-size:.65rem;font-weight:700;color:rgba(255,255,255,.3);text-transform:uppercase;padding:0 .7rem;margin-bottom:.5rem;}
    .nav-item{display:flex;align-items:center;gap:.75rem;padding:.65rem .9rem;border-radius:10px;cursor:pointer;transition:background .2s;text-decoration:none;color:rgba(255,255,255,.6);font-size:.88rem;font-weight:500;margin-bottom:.15rem;}
    .nav-item:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.9);}
    .nav-item.active{background:linear-gradient(90deg,rgba(74,158,48,.35),rgba(74,158,48,.1));color:var(--white);border-left:3px solid var(--green-light);}
    .nav-icon{font-size:1.1rem;width:22px;text-align:center;}
    .nav-badge{margin-left:auto;background:var(--orange);color:var(--white);font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:50px;}
    .nav-badge.green{background:var(--green-main);}
    .nav-badge.orange{background:var(--orange);}
    .sidebar-footer{margin-top:auto;padding:1rem .9rem;border-top:1px solid rgba(255,255,255,.07);}
    .admin-profile{display:flex;align-items:center;gap:.75rem;padding:.7rem .9rem;border-radius:10px;}
    .admin-av{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--green-main),var(--green-dark));display:grid;place-items:center;font-size:.75rem;font-weight:700;color:var(--white);border:2px solid rgba(126,196,79,.4);}
    .admin-info strong{display:block;font-size:.83rem;color:var(--white);font-weight:600;}
    .admin-info span{font-size:.72rem;color:rgba(255,255,255,.45);}

    /* MAIN */
    .main-area{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;}
    .topbar{height:var(--topbar-h);background:var(--white);border-bottom:1px solid var(--border);padding:0 2rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:40;box-shadow:0 2px 12px rgba(45,106,31,.06);}
    .page-title h1{font-family:"Playfair Display",serif;font-size:1.3rem;color:var(--green-dark);}
    .page-title span{font-size:.78rem;color:var(--grey-light);}
    .topbar-date{font-size:.8rem;color:var(--grey-light);background:var(--bg);border:1.5px solid var(--border);border-radius:10px;padding:.45rem .9rem;}
    .content{padding:2rem;flex:1;}

    /* Stats row */
    .stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:1.2rem;margin-bottom:2rem;}
    .stat-box{background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:1.2rem 1.5rem;display:flex;align-items:center;gap:1rem;}
    .stat-box-icon{font-size:2rem;}
    .stat-box-val{font-family:"Playfair Display",serif;font-size:1.8rem;font-weight:700;}
    .stat-box-label{font-size:.8rem;color:var(--grey);}
    .pending-box .stat-box-val{color:var(--orange);}
    .accepted-box .stat-box-val{color:var(--green-main);}
    .rejected-box .stat-box-val{color:var(--red);}

    /* Filter tabs */
    .filter-tabs{display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;}
    .tab{padding:.5rem 1.2rem;border-radius:50px;border:1.5px solid var(--border);background:var(--white);font-family:"DM Sans",sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .2s;color:var(--grey);}
    .tab:hover{border-color:var(--green-main);color:var(--green-main);}
    .tab.active{background:var(--green-main);color:var(--white);border-color:var(--green-main);}

    /* Orders table */
    .orders-table{width:100%;border-collapse:collapse;background:var(--white);border-radius:16px;overflow:hidden;border:1.5px solid var(--border);}
    .orders-table th{background:var(--green-pale);color:var(--green-dark);font-size:.8rem;font-weight:700;text-transform:uppercase;padding:.9rem 1.2rem;text-align:left;}
    .orders-table td{padding:1rem 1.2rem;border-bottom:1px solid var(--border);font-size:.88rem;vertical-align:middle;}
    .orders-table tr:last-child td{border-bottom:none;}
    .orders-table tr:hover td{background:#fafff8;}

    .status-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .8rem;border-radius:50px;font-size:.75rem;font-weight:700;}
    .status-pending{background:#fff3cd;color:#856404;}
    .status-accepted{background:#d1e7dd;color:#0f5132;}
    .status-rejected{background:#f8d7da;color:#842029;}

    .recettes-list{font-size:.8rem;color:var(--grey);line-height:1.6;}

    .btn-accept{background:var(--green-pale);color:var(--green-dark);border:1.5px solid var(--green-light);padding:.4rem .9rem;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;}
    .btn-accept:hover{background:var(--green-main);color:var(--white);}
    .btn-reject{background:var(--red-light);color:var(--red);border:1.5px solid #ffcdd2;padding:.4rem .9rem;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;}
    .btn-reject:hover{background:var(--red);color:var(--white);}
    .btn-delete{background:#f5f5f5;color:var(--grey);border:1.5px solid var(--border);padding:.4rem .7rem;border-radius:8px;font-size:.8rem;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;}
    .btn-delete:hover{background:#eee;}

    .empty-state{text-align:center;padding:4rem;color:var(--grey);}
    .empty-state .ei{font-size:3.5rem;margin-bottom:1rem;}

    /* Modal */
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
    .modal-overlay.open{display:flex;}
    .modal-box{background:var(--white);border-radius:20px;width:min(480px,92vw);box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;}
    .modal-head{padding:1.4rem 1.8rem;display:flex;align-items:center;justify-content:space-between;}
    .modal-head.accept{background:linear-gradient(135deg,var(--green-dark),var(--green-main));}
    .modal-head.reject{background:linear-gradient(135deg,#c0392b,var(--red));}
    .modal-head h2{font-family:"Playfair Display",serif;font-size:1.1rem;color:var(--white);}
    .modal-head p{font-size:.78rem;color:rgba(255,255,255,.7);margin-top:.15rem;}
    .modal-x{background:rgba(255,255,255,.2);border:none;color:var(--white);width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;display:grid;place-items:center;}
    .modal-body{padding:1.5rem;}
    .modal-body label{font-size:.85rem;font-weight:600;color:var(--green-dark);display:block;margin-bottom:.5rem;}
    .modal-body .order-info{background:var(--green-pale);border-radius:10px;padding:1rem;margin-bottom:1rem;font-size:.85rem;line-height:1.7;}
    .modal-body textarea{width:100%;padding:.8rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:"DM Sans",sans-serif;font-size:.88rem;resize:vertical;min-height:100px;outline:none;}
    .modal-body textarea:focus{border-color:var(--green-main);}
    .modal-foot{padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:.7rem;}
    .btn-cancel{padding:.6rem 1.3rem;border:1.5px solid var(--border);border-radius:50px;background:transparent;font-family:"DM Sans",sans-serif;font-size:.87rem;color:var(--grey);cursor:pointer;}
    .btn-confirm-accept{padding:.6rem 1.6rem;border:none;border-radius:50px;background:linear-gradient(135deg,var(--green-main),var(--green-dark));color:var(--white);font-family:"DM Sans",sans-serif;font-size:.87rem;font-weight:700;cursor:pointer;}
    .btn-confirm-reject{padding:.6rem 1.6rem;border:none;border-radius:50px;background:linear-gradient(135deg,var(--red),#c0392b);color:var(--white);font-family:"DM Sans",sans-serif;font-size:.87rem;font-weight:700;cursor:pointer;}

    .toast{position:fixed;bottom:1.8rem;right:2rem;background:var(--green-dark);color:var(--white);padding:.9rem 1.4rem;border-radius:14px;font-size:.88rem;font-weight:500;z-index:300;box-shadow:0 8px 28px rgba(0,0,0,.2);transform:translateY(70px);opacity:0;transition:transform .35s,opacity .35s;display:flex;align-items:center;gap:.5rem;}
    .toast.show{transform:translateY(0);opacity:1;}
    .toast.error{background:var(--red);}
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <aside class="sidebar">
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
      <a class="nav-item" href="listCategorie.php"><span class="nav-icon">🏷️</span> Catégories</a>
      <a class="nav-item active" href="commandes.php"><span class="nav-icon">📦</span> Commandes<span class="nav-badge orange"><?= $nbPending ?></span></a>
      <a class="nav-item" href="statistiques.php"><span class="nav-icon">📈</span> Statistiques</a>
    </div>
    <div class="sidebar-section">
      <div class="sidebar-section-label">Modules</div>
      <a class="nav-item" href="#"><span class="nav-icon">🎯</span> Profils Nutritionnels</a>
      <a class="nav-item" href="#"><span class="nav-icon">📋</span> Suivi Alimentaire</a>
      <a class="nav-item" href="#"><span class="nav-icon">🤖</span> IA &amp; Recommandations</a>
    </div>
    <div class="sidebar-section">
      <div class="sidebar-section-label">Configuration</div>
      <a class="nav-item" href="#"><span class="nav-icon">⚙️</span> Paramètres</a>
      <a class="nav-item" href="#"><span class="nav-icon">📄</span> Rapports</a>
    </div>
    <div class="sidebar-footer">
      <a class="nav-item" href="../../views/index.php" style="margin-bottom:.5rem;background:rgba(240,124,27,.15);color:var(--orange);border:1px solid rgba(240,124,27,.3);">
        <span class="nav-icon">🏠</span> Retour au site
      </a>
      <div class="admin-profile">
        <div class="admin-av">AD</div>
        <div class="admin-info"><strong>Admin EcoNutri</strong><span>Super Administrateur</span></div>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main-area">
    <div class="topbar">
      <div class="page-title">
        <h1>📦 Gestion des Commandes</h1>
        <span>Commandes reçues des utilisateurs</span>
      </div>
      <div class="topbar-date">📅 <?= date('d F Y') ?></div>
    </div>

    <div class="content">
      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-box pending-box">
          <div class="stat-box-icon">⏳</div>
          <div><div class="stat-box-val"><?= $nbPending ?></div><div class="stat-box-label">En attente</div></div>
        </div>
        <div class="stat-box accepted-box">
          <div class="stat-box-icon">✅</div>
          <div><div class="stat-box-val"><?= $nbAccepted ?></div><div class="stat-box-label">Acceptées</div></div>
        </div>
        <div class="stat-box rejected-box">
          <div class="stat-box-icon">❌</div>
          <div><div class="stat-box-val"><?= $nbRejected ?></div><div class="stat-box-label">Refusées</div></div>
        </div>
      </div>

      <!-- Filter tabs -->
      <div class="filter-tabs">
        <button class="tab active" onclick="filterTab('all', this)">Toutes (<?= count($commandes) ?>)</button>
        <button class="tab" onclick="filterTab('pending', this)">⏳ En attente (<?= $nbPending ?>)</button>
        <button class="tab" onclick="filterTab('accepted', this)">✅ Acceptées (<?= $nbAccepted ?>)</button>
        <button class="tab" onclick="filterTab('rejected', this)">❌ Refusées (<?= $nbRejected ?>)</button>
      </div>

      <?php if (empty($commandes)): ?>
        <div class="empty-state">
          <div class="ei">📦</div>
          <p>Aucune commande reçue pour le moment.</p>
        </div>
      <?php else: ?>
      <table class="orders-table" id="ordersTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Client</th>
            <th>Recettes commandées</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Message admin</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($commandes as $c):
            $recettes = json_decode($c['recettes'], true) ?? [];
          ?>
          <tr data-status="<?= $c['status'] ?>">
            <td><strong>#<?= $c['id'] ?></strong></td>
            <td>
              <strong><?= htmlspecialchars($c['user_name']) ?></strong><br>
              <span style="font-size:.78rem;color:var(--grey);">📧 <?= htmlspecialchars($c['user_email']) ?></span>
              <?php if ($c['user_phone']): ?>
                <br><span style="font-size:.78rem;color:var(--grey);">📞 <?= htmlspecialchars($c['user_phone']) ?></span>
              <?php endif; ?>
            </td>
            <td>
              <div class="recettes-list">
                <?php foreach ($recettes as $r): ?>
                  🍽️ <?= htmlspecialchars(is_array($r) ? ($r['name'] ?? '') : $r) ?><br>
                <?php endforeach; ?>
              </div>
            </td>
            <td style="white-space:nowrap;font-size:.8rem;color:var(--grey);">
              <?= date('d/m/Y', strtotime($c['date_commande'])) ?><br>
              <?= date('H:i', strtotime($c['date_commande'])) ?>
            </td>
            <td>
              <?php if ($c['status'] === 'pending'): ?>
                <span class="status-badge status-pending">⏳ En attente</span>
              <?php elseif ($c['status'] === 'accepted'): ?>
                <span class="status-badge status-accepted">✅ Acceptée</span>
              <?php else: ?>
                <span class="status-badge status-rejected">❌ Refusée</span>
              <?php endif; ?>
            </td>
            <td style="font-size:.8rem;color:var(--grey);max-width:180px;">
              <?= $c['admin_message'] ? htmlspecialchars($c['admin_message']) : '<em>—</em>' ?>
            </td>
            <td>
              <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                <?php if ($c['status'] === 'pending'): ?>
                  <button class="btn-accept" onclick="openModal('accept', <?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['user_name'])) ?>', '<?= htmlspecialchars(addslashes($c['user_email'])) ?>')">✅ Accepter</button>
                  <button class="btn-reject" onclick="openModal('reject', <?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['user_name'])) ?>', '<?= htmlspecialchars(addslashes($c['user_email'])) ?>')">❌ Refuser</button>
                <?php endif; ?>
                <button class="btn-delete" onclick="deleteCommande(<?= $c['id'] ?>)">🗑️</button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Accept / Reject Modal -->
  <div class="modal-overlay" id="actionModal" onclick="if(event.target===this)closeModal()">
    <div class="modal-box">
      <div class="modal-head" id="modalHead">
        <div><h2 id="modalTitle">Accepter la commande</h2><p id="modalSub"></p></div>
        <button class="modal-x" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="order-info" id="modalInfo"></div>
        <label>Message de confirmation pour le client *</label>
        <textarea id="adminMessage" placeholder="Ex: Votre commande a été acceptée. Nous vous contacterons sous 24h..."></textarea>
      </div>
      <div class="modal-foot">
        <button class="btn-cancel" onclick="closeModal()">Annuler</button>
        <button id="modalConfirmBtn" onclick="submitAction()">Confirmer</button>
      </div>
    </div>
  </div>

  <script>
    let currentAction = null;
    let currentId = null;

    function openModal(action, id, name, email) {
      currentAction = action;
      currentId = id;
      const head = document.getElementById('modalHead');
      const title = document.getElementById('modalTitle');
      const sub = document.getElementById('modalSub');
      const info = document.getElementById('modalInfo');
      const btn = document.getElementById('modalConfirmBtn');
      const ta = document.getElementById('adminMessage');

      if (action === 'accept') {
        head.className = 'modal-head accept';
        title.textContent = '✅ Accepter la commande #' + id;
        sub.textContent = 'Un message sera envoyé au client.';
        btn.className = 'btn-confirm-accept';
        btn.textContent = '✅ Accepter & Envoyer';
        ta.placeholder = 'Ex: Votre commande a été acceptée ! Nous vous contacterons sous 24h pour les détails de livraison.';
        ta.value = 'Bonjour ' + name + ',\n\nVotre commande a bien été acceptée ! Nous vous contacterons très prochainement.\n\nCordialement,\nL\'équipe EcoNutri';
      } else {
        head.className = 'modal-head reject';
        title.textContent = '❌ Refuser la commande #' + id;
        sub.textContent = 'Un message sera envoyé au client.';
        btn.className = 'btn-confirm-reject';
        btn.textContent = '❌ Refuser & Envoyer';
        ta.placeholder = 'Ex: Nous sommes désolés, votre commande ne peut pas être traitée pour le moment.';
        ta.value = 'Bonjour ' + name + ',\n\nNous sommes désolés, votre commande n\'a pas pu être acceptée.\n\nCordialement,\nL\'équipe EcoNutri';
      }

      info.innerHTML = '<strong>Client :</strong> ' + name + '<br><strong>Email :</strong> ' + email;
      document.getElementById('actionModal').classList.add('open');
    }

    function closeModal() {
      document.getElementById('actionModal').classList.remove('open');
      currentAction = null;
      currentId = null;
    }

    function submitAction() {
      const msg = document.getElementById('adminMessage').value.trim();
      if (!msg) { showToast('Veuillez écrire un message.', 'error'); return; }

      const fd = new FormData();
      fd.append('action', currentAction);
      fd.append('id', currentId);
      fd.append('message', msg);

      fetch('commandes.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            closeModal();
            showToast(currentAction === 'accept' ? '✅ Commande acceptée !' : '❌ Commande refusée.', currentAction === 'accept' ? 'success' : 'error');
            setTimeout(() => location.reload(), 1500);
          } else {
            showToast('Erreur : ' + (data.message || 'Inconnue'), 'error');
          }
        })
        .catch(() => showToast('Erreur réseau.', 'error'));
    }

    function deleteCommande(id) {
      if (!confirm('Supprimer cette commande définitivement ?')) return;
      const fd = new FormData();
      fd.append('action', 'delete');
      fd.append('id', id);
      fetch('commandes.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showToast('Commande supprimée.', 'success');
            setTimeout(() => location.reload(), 1200);
          }
        });
    }

    function filterTab(status, el) {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      el.classList.add('active');
      document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
      });
    }

    function showToast(msg, type = 'success') {
      const t = document.createElement('div');
      t.className = 'toast' + (type === 'error' ? ' error' : '');
      t.textContent = msg;
      document.body.appendChild(t);
      setTimeout(() => t.classList.add('show'), 100);
      setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 350); }, 3000);
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
  </script>
</body>
</html>