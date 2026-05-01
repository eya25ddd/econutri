/* ═══════════════════════════════════════════════
   EcoNutri Backoffice — Dark Mode + Translation
═══════════════════════════════════════════════ */

/* ── DARK MODE ── */
const darkCSS = `
  body.bo-dark { --green-dark:#7ec44f; --green-main:#5ab83a; --green-light:#4a9e30;
    --green-pale:#1a2e14; --orange:#f07c1b; --black:#f0f0f0; --grey:#aaa;
    --grey-light:#888; --white:#1e1e1e; --border:#2e3d28; --bg:#141414;
    --card-bg:#1e1e1e; --red:#ff6b6b; --red-light:#2a1515; --blue-light:#0d1f33;
    --sidebar-bg:#0a1a06; }
  body.bo-dark .topbar { background:#1e1e1e; border-color:#2e3d28; }
  body.bo-dark .topbar-search { background:#141414; border-color:#2e3d28; }
  body.bo-dark .topbar-search input { color:#f0f0f0; }
  body.bo-dark .topbar-icon-btn { background:#141414; border-color:#2e3d28; }
  body.bo-dark .content { background:#141414; }
  body.bo-dark .rcard, body.bo-dark .cat-card, body.bo-dark .aliment-card,
  body.bo-dark .chart-card, body.bo-dark .stat-card, body.bo-dark .stat-box { background:#1e1e1e; border-color:#2e3d28; }
  body.bo-dark .rcard-meta span { background:#141414; border-color:#2e3d28; }
  body.bo-dark .rcard-title, body.bo-dark .cat-card-title,
  body.bo-dark .aliment-card-title, body.bo-dark .chart-title { color:#7ec44f; }
  body.bo-dark .rcard-desc, body.bo-dark .rcard-meta { color:#aaa; }
  body.bo-dark .search-input, body.bo-dark .filter-select { background:#1e1e1e; border-color:#2e3d28; color:#f0f0f0; }
  body.bo-dark .count-badge { background:#1a2e14; color:#7ec44f; border-color:#2e3d28; }
  body.bo-dark .btn-edit { background:#1a2e14; color:#7ec44f; border-color:#2e3d28; }
  body.bo-dark .empty-state { background:#1e1e1e; border-color:#2e3d28; }
  body.bo-dark .modal { background:#1e1e1e; }
  body.bo-dark .modal-content, body.bo-dark .modal-foot { color:#f0f0f0; border-color:#2e3d28; }
  body.bo-dark .orders-table { background:#1e1e1e; border-color:#2e3d28; }
  body.bo-dark .orders-table th { background:#1a2e14; }
  body.bo-dark .orders-table td { border-color:#2e3d28; color:#f0f0f0; }
  body.bo-dark .orders-table tr:hover td { background:#242424; }
  body.bo-dark .macro-row { background:#141414; }
  body.bo-dark .stats-overview .stat-card { background:#1e1e1e; }
  body.bo-dark .filter-tabs .tab { background:#1e1e1e; border-color:#2e3d28; color:#aaa; }
`;

(function initDark() {
  const style = document.createElement('style');
  style.textContent = darkCSS;
  document.head.appendChild(style);

  if (localStorage.getItem('bo_dark') === '1') {
    document.body.classList.add('bo-dark');
  }
})();

function boToggleDark() {
  const isDark = document.body.classList.toggle('bo-dark');
  localStorage.setItem('bo_dark', isDark ? '1' : '0');
  const btn = document.getElementById('boDarkBtn');
  if (btn) btn.textContent = isDark ? '☀️' : '🌙';
}

/* ── TRANSLATIONS ── */
const boTranslations = {
  fr: {
    nav_dashboard: 'Tableau de bord', nav_users: 'Utilisateurs',
    nav_recettes: 'Recettes', nav_aliments: 'Aliments',
    nav_categories: 'Catégories', nav_commandes: 'Commandes',
    nav_stats: 'Statistiques', nav_back: 'Retour au site',
    topbar_search_recette: 'Rechercher des recettes…',
    topbar_search_aliment: 'Rechercher des aliments…',
    topbar_search_cat: 'Rechercher…',
    topbar_search_stats: 'Rechercher…',
    title_recettes: 'Gestion des Recettes',
    sub_recettes: 'Administration des recettes EcoNutri',
    title_aliments: 'Gestion des Aliments',
    sub_aliments: 'Administration des aliments EcoNutri',
    title_categories: 'Gestion des Catégories',
    sub_categories: 'Administration des catégories EcoNutri',
    title_stats: 'Statistiques',
    sub_stats: 'Vue d\'ensemble des données EcoNutri',
    btn_add_recette: 'Ajouter une Recette',
    btn_add_aliment: 'Ajouter un Aliment',
    btn_add_cat: 'Ajouter une Catégorie',
    btn_export: 'Exporter PDF',
    search_recette: 'Rechercher une recette…',
    search_aliment: 'Rechercher un aliment…',
    search_cat: 'Rechercher une catégorie…',
    diff_all: 'Toutes difficultés', diff_facile: 'Facile',
    diff_moyen: 'Moyen', diff_difficile: 'Difficile',
    sort_placeholder: 'Trier par...',
    edit: 'Modifier', delete: 'Supprimer',
    stat_recettes: 'Recettes totales', stat_aliments: 'Aliments totaux',
    stat_categories: 'Catégories', stat_avg_time: 'Temps moyen de préparation',
    chart_diff: 'Répartition par difficulté',
    chart_cal: 'Aliments par plage calorique',
    chart_macros: 'Macronutriments moyens',
    chart_top: 'Top 5 aliments utilisés',
    chart_cats: 'Distribution des catégories',
  },
  en: {
    nav_dashboard: 'Dashboard', nav_users: 'Users',
    nav_recettes: 'Recipes', nav_aliments: 'Foods',
    nav_categories: 'Categories', nav_commandes: 'Orders',
    nav_stats: 'Statistics', nav_back: 'Back to site',
    topbar_search_recette: 'Search recipes…',
    topbar_search_aliment: 'Search foods…',
    topbar_search_cat: 'Search…',
    topbar_search_stats: 'Search…',
    title_recettes: 'Recipe Management',
    sub_recettes: 'EcoNutri recipe administration',
    title_aliments: 'Food Management',
    sub_aliments: 'EcoNutri food administration',
    title_categories: 'Category Management',
    sub_categories: 'EcoNutri category administration',
    title_stats: 'Statistics',
    sub_stats: 'EcoNutri data overview',
    btn_add_recette: 'Add a Recipe',
    btn_add_aliment: 'Add a Food',
    btn_add_cat: 'Add a Category',
    btn_export: 'Export PDF',
    search_recette: 'Search a recipe…',
    search_aliment: 'Search a food…',
    search_cat: 'Search a category…',
    diff_all: 'All difficulties', diff_facile: 'Easy',
    diff_moyen: 'Medium', diff_difficile: 'Hard',
    sort_placeholder: 'Sort by...',
    edit: 'Edit', delete: 'Delete',
    stat_recettes: 'Total recipes', stat_aliments: 'Total foods',
    stat_categories: 'Categories', stat_avg_time: 'Avg. prep time',
    chart_diff: 'Difficulty breakdown',
    chart_cal: 'Foods by calorie range',
    chart_macros: 'Average macronutrients',
    chart_top: 'Top 5 used foods',
    chart_cats: 'Category distribution',
  },
  ar: {
    nav_dashboard: 'لوحة التحكم', nav_users: 'المستخدمون',
    nav_recettes: 'الوصفات', nav_aliments: 'الأغذية',
    nav_categories: 'الفئات', nav_commandes: 'الطلبات',
    nav_stats: 'الإحصائيات', nav_back: 'العودة للموقع',
    topbar_search_recette: 'ابحث عن وصفة…',
    topbar_search_aliment: 'ابحث عن غذاء…',
    topbar_search_cat: 'ابحث…',
    topbar_search_stats: 'ابحث…',
    title_recettes: 'إدارة الوصفات',
    sub_recettes: 'إدارة وصفات EcoNutri',
    title_aliments: 'إدارة الأغذية',
    sub_aliments: 'إدارة أغذية EcoNutri',
    title_categories: 'إدارة الفئات',
    sub_categories: 'إدارة فئات EcoNutri',
    title_stats: 'الإحصائيات',
    sub_stats: 'نظرة عامة على بيانات EcoNutri',
    btn_add_recette: 'إضافة وصفة',
    btn_add_aliment: 'إضافة غذاء',
    btn_add_cat: 'إضافة فئة',
    btn_export: 'تصدير PDF',
    search_recette: 'ابحث عن وصفة…',
    search_aliment: 'ابحث عن غذاء…',
    search_cat: 'ابحث عن فئة…',
    diff_all: 'كل المستويات', diff_facile: 'سهل',
    diff_moyen: 'متوسط', diff_difficile: 'صعب',
    sort_placeholder: 'ترتيب حسب...',
    edit: 'تعديل', delete: 'حذف',
    stat_recettes: 'إجمالي الوصفات', stat_aliments: 'إجمالي الأغذية',
    stat_categories: 'الفئات', stat_avg_time: 'متوسط وقت التحضير',
    chart_diff: 'توزيع الصعوبة',
    chart_cal: 'الأغذية حسب السعرات',
    chart_macros: 'متوسط العناصر الغذائية',
    chart_top: 'أكثر 5 أغذية استخداماً',
    chart_cats: 'توزيع الفئات',
  }
};

function boSetLang(lang) {
  localStorage.setItem('bo_lang', lang);
  document.getElementById('boLangLabel').textContent = lang.toUpperCase();
  document.getElementById('boLangMenu').classList.remove('open');
  document.documentElement.dir  = lang === 'ar' ? 'rtl' : 'ltr';
  document.documentElement.lang = lang;
  const t = boTranslations[lang] || boTranslations.fr;

  // Sidebar nav items
  const navMap = {
    'Tableau de bord':'nav_dashboard','Dashboard':'nav_dashboard','لوحة التحكم':'nav_dashboard',
    'Utilisateurs':'nav_users','Users':'nav_users','المستخدمون':'nav_users',
    'Recettes':'nav_recettes','Recipes':'nav_recettes','الوصفات':'nav_recettes',
    'Aliments':'nav_aliments','Foods':'nav_aliments','الأغذية':'nav_aliments',
    'Catégories':'nav_categories','Categories':'nav_categories','الفئات':'nav_categories',
    'Commandes':'nav_commandes','Orders':'nav_commandes','الطلبات':'nav_commandes',
    'Statistiques':'nav_stats','Statistics':'nav_stats','الإحصائيات':'nav_stats',
    'Retour au site':'nav_back','Back to site':'nav_back','العودة للموقع':'nav_back',
  };
  document.querySelectorAll('.nav-item').forEach(item => {
    const icon = item.querySelector('.nav-icon');
    const badge = item.querySelector('.nav-badge');
    if (!icon) return;
    const rawText = item.textContent.replace(icon.textContent, '').replace(badge ? badge.textContent : '', '').trim();
    const key = navMap[rawText];
    if (key && t[key]) {
      item.childNodes.forEach(node => {
        if (node.nodeType === 3 && node.textContent.trim()) {
          node.textContent = ' ' + t[key];
        }
      });
    }
  });

  // Topbar title & subtitle
  const h1 = document.querySelector('.page-title h1');
  const sub = document.querySelector('.page-title span');
  const page = document.body.dataset.boPage;
  if (h1 && page) h1.textContent = t['title_' + page] || h1.textContent;
  if (sub && page) sub.textContent = t['sub_' + page] || sub.textContent;

  // Topbar search placeholder
  const tsearch = document.querySelector('.topbar-search input');
  if (tsearch && page) tsearch.placeholder = t['topbar_search_' + page] || tsearch.placeholder;

  // Crud title
  const crudTitle = document.querySelector('.crud-title');
  if (crudTitle && page) {
    const map = { recettes:'title_recettes', aliments:'title_aliments', categories:'title_categories', stats:'title_stats' };
    if (map[page]) crudTitle.textContent = crudTitle.textContent.replace(/[A-Za-zÀ-ÿ\u0600-\u06FF\s]+/, t[map[page]]);
  }

  // Add buttons
  const addBtn = document.querySelector('.btn-primary[href*="add"]');
  if (addBtn && page) {
    const map = { recettes:'btn_add_recette', aliments:'btn_add_aliment', categories:'btn_add_cat' };
    if (map[page]) addBtn.innerHTML = '<span>+</span> ' + t[map[page]];
  }
  const exportBtn = document.querySelector('.btn-secondary[onclick*="exportPDF"]');
  if (exportBtn) exportBtn.innerHTML = '📄 ' + t.btn_export;

  // Search input placeholder
  const si = document.getElementById('searchInput');
  if (si && page) {
    const map = { recettes:'search_recette', aliments:'search_aliment', categories:'search_cat' };
    if (map[page]) si.placeholder = '🔍 ' + t[map[page]];
  }

  // Difficulty filter
  const diffFilter = document.getElementById('diffFilter');
  if (diffFilter) {
    if (diffFilter.options[0]) diffFilter.options[0].text = t.diff_all;
    if (diffFilter.options[1]) diffFilter.options[1].text = t.diff_facile;
    if (diffFilter.options[2]) diffFilter.options[2].text = t.diff_moyen;
    if (diffFilter.options[3]) diffFilter.options[3].text = t.diff_difficile;
  }

  // Sort select placeholder
  const sortSel = document.getElementById('sortSelect');
  if (sortSel && sortSel.options[0]) sortSel.options[0].text = t.sort_placeholder;

  // Edit/Delete buttons
  document.querySelectorAll('.btn-edit').forEach(b => {
    if (b.textContent.includes('✏️') || b.textContent.includes('Modifier') || b.textContent.includes('Edit') || b.textContent.includes('تعديل'))
      b.innerHTML = '✏️ ' + t.edit;
  });
  document.querySelectorAll('.btn-del').forEach(b => {
    if (b.textContent.includes('🗑️') || b.textContent.includes('Supprimer') || b.textContent.includes('Delete') || b.textContent.includes('حذف'))
      b.innerHTML = '🗑️ ' + t.delete;
  });

  // Stats page chart titles
  if (page === 'stats') {
    const chartTitles = document.querySelectorAll('.chart-title');
    const chartKeys = ['chart_diff','chart_cal','chart_macros','chart_top','chart_cats'];
    chartTitles.forEach((el, i) => { if (chartKeys[i]) el.textContent = (el.textContent.match(/^[^\w]*/)?.[0] || '') + t[chartKeys[i]]; });
    const statLabels = document.querySelectorAll('.stat-label');
    const statKeys = ['stat_recettes','stat_aliments','stat_categories','stat_avg_time'];
    statLabels.forEach((el, i) => { if (statKeys[i]) el.textContent = t[statKeys[i]]; });
  }

  document.dispatchEvent(new CustomEvent('boLangChange', { detail: { lang, t } }));
}

function boToggleLangMenu() {
  document.getElementById('boLangMenu').classList.toggle('open');
}

// Close lang menu on outside click
document.addEventListener('click', function(e) {
  const m = document.getElementById('boLangMenu');
  if (m && !m.contains(e.target)) m.classList.remove('open');
});

// Apply saved settings on load
(function() {
  const savedDark = localStorage.getItem('bo_dark');
  const btn = document.getElementById('boDarkBtn');
  if (savedDark === '1') { document.body.classList.add('bo-dark'); if (btn) btn.textContent = '☀️'; }
  else { if (btn) btn.textContent = '🌙'; }

  const savedLang = localStorage.getItem('bo_lang');
  if (savedLang && savedLang !== 'fr') boSetLang(savedLang);
  else if (document.getElementById('boLangLabel')) document.getElementById('boLangLabel').textContent = 'FR';
})();
