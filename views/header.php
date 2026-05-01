<?php
// includes/header.php
// Usage: include at the top of every page.
// $pageTitle  — optional, sets <title>
// $activeNav  — optional: 'accueil' | 'recettes' | 'fonctionnalites' | 'comment' | 'contact'
$pageTitle  = $pageTitle  ?? 'EcoNutri';
$activeNav  = $activeNav  ?? '';

// Detect depth from root so asset/link paths stay correct
$path = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
$views_pos = strpos($path, 'views/');
if ($views_pos === false) {
    $root = 'views/';
} else {
    $subpath = substr($path, $views_pos + 6); // after 'views/'
    $depth = substr_count($subpath, '/');
    $root = str_repeat('../', $depth);
}
?>
<!DOCTYPE html>
<html lang="fr"> 
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle) ?> – EcoNutri</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --green-dark:#2d6a1f;--green-main:#4a9e30;--green-light:#7ec44f;
      --green-pale:#e8f5e1;--orange:#f07c1b;--orange-light:#fde8d0;
      --black:#111;--grey:#555;--white:#fff;--card-bg:#f9fdf6;--border:#d9eed0;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html{scroll-behavior:smooth;}
    body{font-family:"DM Sans",sans-serif;background:var(--white);color:var(--black);overflow-x:hidden;}

    /* ── HEADER ── */
    header{background:linear-gradient(135deg,var(--green-dark) 0%,var(--green-main) 60%,var(--green-light) 100%);padding:0 2.5rem;display:flex;align-items:center;justify-content:space-between;height:68px;position:sticky;top:0;z-index:100;box-shadow:0 4px 20px rgba(45,106,31,.35);}
    .logo{display:flex;align-items:center;gap:.65rem;text-decoration:none;}
    .logo-icon{width:42px;height:42px;background:rgba(255,255,255,.15);border-radius:10px;display:grid;place-items:center;backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.3);}
    .logo-text{font-family:"Playfair Display",serif;font-size:1.5rem;color:var(--white);letter-spacing:-.5px;}
    .logo-text span{color:var(--orange);}
    nav{display:flex;align-items:center;gap:2rem;}
    nav a{color:rgba(255,255,255,.88);text-decoration:none;font-size:.92rem;font-weight:500;letter-spacing:.3px;transition:color .2s;}
    nav a:hover{color:var(--white);}
    nav a.active{color:var(--white);border-bottom:2px solid var(--orange);padding-bottom:2px;}
    .nav-dropdown{position:relative;}
    .nav-dropdown .dropdown-toggle{color:rgba(255,255,255,.88);text-decoration:none;font-size:.92rem;font-weight:500;letter-spacing:.3px;cursor:pointer;background:transparent;border:none;padding:0;font-family:"DM Sans",sans-serif;}
    .nav-dropdown .dropdown-toggle:hover{color:var(--white);}
    .nav-dropdown .dropdown-menu{position:absolute;top:calc(100% + 8px);left:0;background:var(--white);color:var(--black);min-width:160px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.12);border:1px solid var(--border);overflow:hidden;display:none;z-index:150;}
    .nav-dropdown.open .dropdown-menu{display:block;}
    .nav-dropdown .dropdown-menu a{display:block;padding:.6rem 1rem;color:var(--green-dark);text-decoration:none;font-weight:600;font-size:.88rem;}
    .nav-dropdown .dropdown-menu a:hover{background:var(--card-bg);}
    .header-actions{display:flex;align-items:center;gap:.8rem;}
    .btn-login{background:rgba(255,255,255,.15);color:var(--white);border:1.5px solid rgba(255,255,255,.4);padding:.5rem 1.2rem;border-radius:50px;font-size:.88rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:background .2s;}
    .btn-login:hover{background:rgba(255,255,255,.25);}
    .btn-register{background:var(--orange);color:var(--white);border:none;padding:.5rem 1.3rem;border-radius:50px;font-size:.88rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:transform .2s,box-shadow .2s;}
    .btn-register:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(240,124,27,.45);}

    /* ── MODAL SYSTEM ── */
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:200;display:grid;place-items:center;opacity:0;pointer-events:none;transition:opacity .3s;backdrop-filter:blur(4px);}
    .modal-overlay.open{opacity:1;pointer-events:all;}
    .modal{background:var(--white);border-radius:24px;width:min(480px,94vw);transform:translateY(30px) scale(.97);transition:transform .3s;box-shadow:0 24px 64px rgba(0,0,0,.25);overflow:hidden;}
    .modal-overlay.open .modal{transform:translateY(0) scale(1);}
    .modal-header{background:linear-gradient(135deg,var(--green-dark),var(--green-main));padding:1.8rem 2rem;display:flex;align-items:center;justify-content:space-between;}
    .modal-header-left{display:flex;align-items:center;gap:.8rem;}
    .modal-header-left .mh-icon{font-size:1.6rem;background:rgba(255,255,255,.15);width:44px;height:44px;border-radius:12px;display:grid;place-items:center;}
    .modal-header h2{font-family:"Playfair Display",serif;font-size:1.2rem;color:var(--white);}
    .modal-header p{font-size:.8rem;color:rgba(255,255,255,.75);margin-top:.2rem;}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:var(--white);width:36px;height:36px;border-radius:50%;cursor:pointer;display:grid;place-items:center;font-size:1.1rem;transition:background .2s;}
    .modal-close:hover{background:rgba(255,255,255,.35);}
    .modal-body{padding:2rem;}
    .form-group{margin-bottom:1.2rem;}
    .form-group label{display:block;font-size:.83rem;font-weight:600;color:var(--green-dark);margin-bottom:.4rem;}
    .form-group label span{color:var(--orange);}
    .form-control{width:100%;padding:.7rem .95rem;border:1.5px solid var(--border);border-radius:12px;font-family:"DM Sans",sans-serif;font-size:.9rem;color:var(--black);outline:none;transition:border-color .2s;background:var(--card-bg);}
    .form-control:focus{border-color:var(--green-main);background:var(--white);}
    .form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
    .forgot{text-align:right;margin-top:-.7rem;margin-bottom:.8rem;}
    .forgot a{font-size:.8rem;color:var(--green-main);text-decoration:none;}
    .divider{display:flex;align-items:center;gap:.8rem;margin:1.2rem 0;font-size:.78rem;color:var(--grey);}
    .divider::before,.divider::after{content:"";flex:1;height:1px;background:var(--border);}
    .btn-submit-full{width:100%;padding:.8rem;border:none;border-radius:12px;background:linear-gradient(135deg,var(--green-main),var(--green-dark));color:var(--white);font-family:"DM Sans",sans-serif;font-size:.95rem;font-weight:700;cursor:pointer;transition:transform .2s,box-shadow .2s;}
    .btn-submit-full:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(45,106,31,.35);}
    .modal-switch{text-align:center;margin-top:1.2rem;font-size:.83rem;color:var(--grey);}
    .modal-switch a{color:var(--orange);font-weight:700;cursor:pointer;text-decoration:none;}
    .nutrition-tags{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.5rem;}
    .ntag{padding:.35rem .85rem;border-radius:50px;border:1.5px solid var(--border);font-size:.78rem;cursor:pointer;transition:all .2s;font-family:"DM Sans",sans-serif;color:var(--grey);background:var(--card-bg);}
    .ntag.selected{background:var(--green-pale);border-color:var(--green-main);color:var(--green-dark);font-weight:600;}

    /* ── TOAST ── */
    .toast{position:fixed;bottom:2rem;right:2rem;background:var(--green-dark);color:var(--white);padding:1rem 1.5rem;border-radius:14px;font-size:.9rem;font-weight:500;z-index:300;box-shadow:0 8px 28px rgba(0,0,0,.25);transform:translateY(80px);opacity:0;transition:transform .35s,opacity .35s;display:flex;align-items:center;gap:.6rem;}
    .toast.show{transform:translateY(0);opacity:1;}
    .toast.toast-error{background:#c0392b;}
    .toast.toast-success{background:var(--green-dark);}

    /* ── CUSTOM CONFIRM DIALOG ── */
    .confirm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:500;display:grid;place-items:center;opacity:0;pointer-events:none;transition:opacity .3s;backdrop-filter:blur(4px);}
    .confirm-overlay.open{opacity:1;pointer-events:all;}
    .confirm-box{background:var(--white);border-radius:20px;width:min(420px,92vw);padding:0;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.25);transform:scale(.95);transition:transform .3s;}
    .confirm-overlay.open .confirm-box{transform:scale(1);}
    .confirm-icon-wrap{background:linear-gradient(135deg,#fff3e0,#fde8d0);padding:2rem;text-align:center;}
    .confirm-icon-wrap .ci{font-size:3rem;}
    .confirm-content{padding:1.5rem 2rem 2rem;}
    .confirm-content h3{font-family:"Playfair Display",serif;font-size:1.15rem;color:var(--black);margin-bottom:.5rem;}
    .confirm-content p{font-size:.88rem;color:var(--grey);line-height:1.6;}
    .confirm-actions{display:flex;gap:.8rem;margin-top:1.5rem;}
    .confirm-cancel{flex:1;padding:.7rem;border:1.5px solid var(--border);border-radius:12px;background:var(--card-bg);color:var(--grey);font-family:"DM Sans",sans-serif;font-size:.9rem;font-weight:600;cursor:pointer;transition:all .2s;}
    .confirm-cancel:hover{border-color:var(--grey);color:var(--black);}
    .confirm-delete{flex:1;padding:.7rem;border:none;border-radius:12px;background:linear-gradient(135deg,#e74c3c,#c0392b);color:var(--white);font-family:"DM Sans",sans-serif;font-size:.9rem;font-weight:700;cursor:pointer;transition:transform .2s,box-shadow .2s;}
    .confirm-delete:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(231,76,60,.4);}

    @media(max-width:680px){
      header{padding:0 1rem;}
      nav{display:none;}
    }

    /* ── DARK MODE ── */
    body.dark{--green-dark:#7ec44f;--green-main:#5ab83a;--green-light:#4a9e30;--green-pale:#1a2e14;--orange:#f07c1b;--black:#f0f0f0;--grey:#aaa;--white:#1a1a1a;--card-bg:#242424;--border:#2e3d28;}
    body.dark header{background:linear-gradient(135deg,#0e2a08 0%,#1a3d10 60%,#2d6a1f 100%);}
    body.dark footer{background:#0a1a06;}
    body.dark .modal,.body.dark .confirm-box{background:#242424;color:#f0f0f0;}
    body.dark .form-control{background:#2e2e2e;border-color:#3a4a30;color:#f0f0f0;}
    body.dark .nav-dropdown .dropdown-menu{background:#1e2e18;border-color:#2e3d28;}
    body.dark .nav-dropdown .dropdown-menu a{color:#c8e6b0;}

    /* ── TOGGLE BUTTONS ── */
    .header-toggles{display:flex;align-items:center;gap:.5rem;}
    .toggle-btn{background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.35);color:var(--white);padding:.38rem .75rem;border-radius:50px;font-size:.8rem;font-weight:600;cursor:pointer;font-family:"DM Sans",sans-serif;transition:all .2s;white-space:nowrap;}
    .toggle-btn:hover{background:rgba(255,255,255,.28);}
    .lang-menu{position:relative;}
    .lang-dropdown{position:absolute;top:calc(100% + 6px);right:0;background:var(--white);border:1.5px solid var(--border);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);overflow:hidden;display:none;min-width:110px;z-index:200;}
    .lang-menu.open .lang-dropdown{display:block;}
    .lang-dropdown button{display:flex;align-items:center;gap:.5rem;width:100%;padding:.55rem 1rem;background:none;border:none;font-family:"DM Sans",sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;color:var(--green-dark);transition:background .15s;}
    .lang-dropdown button:hover{background:var(--card-bg);}
    body.dark .lang-dropdown{background:#1e2e18;border-color:#2e3d28;}
    body.dark .lang-dropdown button{color:#c8e6b0;}
    body.dark .lang-dropdown button:hover{background:#2a3e22;}
  </style>
</head>
<body>

<header>
  <a class="logo" href="<?= $root ?>index.php">
    <div class="logo-icon">
      <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" width="26" height="26">
        <path d="M16 4C10 4 5 8 4 14c4-2 9-1 12 3 3-4 8-5 12-3-1-6-6-10-12-10z" fill="#7ec44f"/>
        <path d="M4 14c-1 5 2 10 7 12l5-8-5-4c-3 0-6 0-7 0z" fill="#4a9e30"/>
        <path d="M28 14c1 5-2 10-7 12l-5-8 5-4c3 0 6 0 7 0z" fill="#2d6a1f"/>
        <circle cx="16" cy="22" r="3" fill="#f07c1b"/>
      </svg>
    </div>
    <span class="logo-text">Eco<span>Nutri</span></span>
  </a>

  <nav>
    <a href="<?= $root ?>index.php" class="<?= $activeNav==='accueil'?'active':'' ?>">Accueil</a>
    <div class="nav-dropdown" id="nav-recettes">
      <button class="dropdown-toggle" onclick="toggleRecetteDropdown(event)">Nos Recettes ▾</button>
      <div class="dropdown-menu">
        <a href="<?= $root ?>backoffice/dashboard.php">Voir les recettes</a>
        <a href="<?= $root ?>listAliment.php">Aliments</a>
        <a href="<?= $root ?>listRecette.php">Recettes</a>
      </div>
    </div>
    <a href="<?= $root ?>index.php#fonctionnalites" class="<?= $activeNav==='fonctionnalites'?'active':'' ?>">Fonctionnalités</a>
    <a href="<?= $root ?>index.php#comment" class="<?= $activeNav==='comment'?'active':'' ?>">Comment ça marche</a>
    <a href="<?= $root ?>index.php#contact" class="<?= $activeNav==='contact'?'active':'' ?>">Contact</a>
  </nav>

  <div class="header-actions">
    <!-- Dark mode + Language toggles -->
    <div class="header-toggles">
      <button class="toggle-btn" id="darkToggle" onclick="toggleDark()" title="Mode sombre/clair">🌙</button>
      <div class="lang-menu" id="langMenu">
        <button class="toggle-btn" onclick="toggleLangMenu()">🌐 <span id="langLabel">FR</span> ▾</button>
        <div class="lang-dropdown">
          <button onclick="setLang('fr')">🇫🇷 Français</button>
          <button onclick="setLang('en')">🇬🇧 English</button>
          <button onclick="setLang('ar')">🇸🇦 العربية</button>
        </div>
      </div>
    </div>
    <button class="btn-login" onclick="openModal('login')">Se connecter</button>
    <button class="btn-register" onclick="openModal('register')">S'inscrire</button>
  </div>
</header>

<!-- Login Modal -->
<div class="modal-overlay" id="loginModal" onclick="closeModalOutside(event,'loginModal')">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-header-left"><div class="mh-icon">🔑</div><div><h2>Connexion</h2><p>Bienvenue de retour sur EcoNutri</p></div></div>
      <button class="modal-close" onclick="closeModal('loginModal')">✕</button>
    </div>
    <div class="modal-body">
      <form id="loginForm" onsubmit="handleLogin(); return false;">
        <div class="form-group"><label>Adresse email <span>*</span></label><input type="email" id="adminEmail" class="form-control" placeholder="votre@email.com"/></div>
        <div class="form-group"><label>Mot de passe <span>*</span></label><input type="password" id="adminPassword" class="form-control" placeholder="••••••••"/></div>
        <div class="forgot"><a href="#">Mot de passe oublié ?</a></div>
        <button type="submit" class="btn-submit-full">Se connecter</button>
      </form>
      <div class="divider">ou</div>
      <div class="modal-switch">Pas encore de compte ? <a onclick="switchModal('loginModal','registerModal')">S'inscrire gratuitement →</a></div>
    </div>
  </div>
</div>

<!-- Register Modal -->
<div class="modal-overlay" id="registerModal" onclick="closeModalOutside(event,'registerModal')">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-header-left"><div class="mh-icon">🌿</div><div><h2>Créer un compte</h2><p>Rejoignez la communauté EcoNutri</p></div></div>
      <button class="modal-close" onclick="closeModal('registerModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row-2">
        <div class="form-group"><label>Prénom <span>*</span></label><input type="text" class="form-control" placeholder="Votre prénom"/></div>
        <div class="form-group"><label>Nom <span>*</span></label><input type="text" class="form-control" placeholder="Votre nom"/></div>
      </div>
      <div class="form-group"><label>Adresse email <span>*</span></label><input type="email" class="form-control" placeholder="votre@email.com"/></div>
      <div class="form-group"><label>Mot de passe <span>*</span></label><input type="password" class="form-control" placeholder="Minimum 8 caractères"/></div>
      <div class="form-row-2">
        <div class="form-group"><label>Âge</label><input type="number" class="form-control" placeholder="25" min="13" max="100"/></div>
        <div class="form-group"><label>Objectif principal</label>
          <select class="form-control"><option value="">-- Choisir --</option><option>Perte de poids</option><option>Prise de masse</option><option>Alimentation équilibrée</option><option>Végétarisme / Véganisme</option></select>
        </div>
      </div>
      <div class="form-group"><label>Préférences alimentaires</label>
        <div class="nutrition-tags">
          <button class="ntag" onclick="toggleTag(this)">🥗 Végétarien</button>
          <button class="ntag" onclick="toggleTag(this)">🌱 Vegan</button>
          <button class="ntag" onclick="toggleTag(this)">🌾 Sans gluten</button>
          <button class="ntag" onclick="toggleTag(this)">🥛 Sans lactose</button>
          <button class="ntag" onclick="toggleTag(this)">🥩 Riche en protéines</button>
          <button class="ntag" onclick="toggleTag(this)">⚡ Faible en glucides</button>
        </div>
      </div>
      <button class="btn-submit-full" onclick="handleRegister()">Créer mon compte</button>
      <div class="divider">ou</div>
      <div class="modal-switch">Déjà membre ? <a onclick="switchModal('registerModal','loginModal')">Se connecter →</a></div>
    </div>
  </div>
</div>

<!-- Custom Confirm Dialog -->
<div class="confirm-overlay" id="confirmOverlay">
  <div class="confirm-box">
    <div class="confirm-icon-wrap"><div class="ci">🗑️</div></div>
    <div class="confirm-content">
      <h3 id="confirmTitle">Confirmer la suppression</h3>
      <p id="confirmMessage">Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>
      <div class="confirm-actions">
        <button class="confirm-cancel" onclick="closeConfirm()">Annuler</button>
        <button class="confirm-delete" id="confirmBtn">Supprimer</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
function openModal(type){const id=type==='login'?'loginModal':'registerModal';document.getElementById(id).classList.add('open');document.body.style.overflow='hidden';}
function closeModal(id){document.getElementById(id).classList.remove('open');document.body.style.overflow='';}
function closeModalOutside(e,id){if(e.target===document.getElementById(id))closeModal(id);}
function switchModal(from,to){closeModal(from);setTimeout(()=>{document.getElementById(to).classList.add('open');},250);}
function toggleTag(el){el.classList.toggle('selected');}
function handleLogin(){
  const email=document.getElementById('adminEmail').value.trim();
  const password=document.getElementById('adminPassword').value.trim();
  if(email==='admin@econutri.com'&&password==='admin123'){closeModal('loginModal');showToast('🎉 Connexion admin réussie !');setTimeout(()=>{window.location.href='/EcoNutri/views/backoffice/index.php';},800);}else{showToast('❌ Email ou mot de passe incorrect','error');}}

function handleRegister(){closeModal('registerModal');showToast('🌿 Compte créé avec succès ! Bienvenue dans la communauté EcoNutri !');}

function showToast(msg,type='success'){
  const t=document.getElementById('toast');
  t.textContent=msg;
  t.className='toast toast-'+type+' show';
  setTimeout(()=>t.classList.remove('show'),3800);
}

let _confirmCallback=null;
function showConfirm(title,message,callback){
  document.getElementById('confirmTitle').textContent=title;
  document.getElementById('confirmMessage').textContent=message;
  _confirmCallback=callback;
  document.getElementById('confirmOverlay').classList.add('open');
  document.body.style.overflow='hidden';
}
function closeConfirm(){document.getElementById('confirmOverlay').classList.remove('open');document.body.style.overflow='';_confirmCallback=null;}
document.getElementById('confirmBtn').addEventListener('click',function(){if(_confirmCallback)_confirmCallback();closeConfirm();});

function toggleRecetteDropdown(e){e.stopPropagation();document.getElementById('nav-recettes').classList.toggle('open');}
document.addEventListener('click',function(e){const n=document.getElementById('nav-recettes');if(n&&!n.contains(e.target))n.classList.remove('open');});

/* ── DARK MODE ── */
function toggleDark(){
  const isDark = document.body.classList.toggle('dark');
  localStorage.setItem('econutri_dark', isDark ? '1' : '0');
  document.getElementById('darkToggle').textContent = isDark ? '☀️' : '🌙';
}
(function(){
  if(localStorage.getItem('econutri_dark') === '1'){
    document.body.classList.add('dark');
    const btn = document.getElementById('darkToggle');
    if(btn) btn.textContent = '☀️';
  }
})();

/* ── LANGUAGE ── */
const translations = {
  fr: {
    nav_accueil:'Accueil', nav_recettes:'Nos Recettes', nav_fonc:'Fonctionnalités',
    nav_comment:'Comment ça marche', nav_contact:'Contact',
    btn_login:'Se connecter', btn_register:"S'inscrire",
    footer_nav:'Navigation', footer_compte:'Compte', footer_about:'À propos',
  },
  en: {
    nav_accueil:'Home', nav_recettes:'Our Recipes', nav_fonc:'Features',
    nav_comment:'How it works', nav_contact:'Contact',
    btn_login:'Log in', btn_register:'Sign up',
    footer_nav:'Navigation', footer_compte:'Account', footer_about:'About',
  },
  ar: {
    nav_accueil:'الرئيسية', nav_recettes:'وصفاتنا', nav_fonc:'المميزات',
    nav_comment:'كيف يعمل', nav_contact:'اتصل بنا',
    btn_login:'تسجيل الدخول', btn_register:'إنشاء حساب',
    footer_nav:'التنقل', footer_compte:'الحساب', footer_about:'حول',
  }
};

function setLang(lang){
  localStorage.setItem('econutri_lang', lang);
  document.getElementById('langLabel').textContent = lang.toUpperCase();
  document.getElementById('langMenu').classList.remove('open');
  const t = translations[lang] || translations.fr;
  // Nav
  const navLinks = document.querySelectorAll('nav a');
  const keys = ['nav_accueil','nav_fonc','nav_comment','nav_contact'];
  let ki = 0;
  navLinks.forEach(a => { if(keys[ki]) { a.textContent = t[keys[ki]]; ki++; } });
  const toggle = document.querySelector('.dropdown-toggle');
  if(toggle) toggle.textContent = t.nav_recettes + ' ▾';
  // Buttons
  const loginBtn = document.querySelector('.btn-login');
  const regBtn   = document.querySelector('.btn-register');
  if(loginBtn) loginBtn.textContent = t.btn_login;
  if(regBtn)   regBtn.textContent   = t.btn_register;
  // RTL for Arabic
  document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
  document.documentElement.lang = lang;
  // Store for page-level translations
  document.dispatchEvent(new CustomEvent('langChange', { detail: { lang, t } }));
}

function toggleLangMenu(){
  document.getElementById('langMenu').classList.toggle('open');
}
document.addEventListener('click', function(e){
  const m = document.getElementById('langMenu');
  if(m && !m.contains(e.target)) m.classList.remove('open');
});

// Apply saved language on load
(function(){
  const saved = localStorage.getItem('econutri_lang');
  if(saved && saved !== 'fr') setLang(saved);
})();
</script>
