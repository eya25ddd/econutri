<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/RecetteController.php';
require_once __DIR__ . '/../../controllers/AlimentController.php';
require_once __DIR__ . '/../../controllers/CategorieController.php';

$recetteController = new RecetteController();
$alimentController = new AlimentController();
$categorieController = new CategorieController();
$db = Database::getConnection();

// Get all data
$recettes = $recetteController->getAll();
$aliments = $alimentController->getAll();
$categories = $categorieController->getAll();

// Statistics calculations
$totalRecettes = count($recettes);
$totalAliments = count($aliments);
$totalCategories = count($categories);

// Recipes by difficulty
$difficultyStats = ['facile' => 0, 'moyen' => 0, 'difficile' => 0];
foreach ($recettes as $r) {
    if (isset($difficultyStats[$r->difficulte])) {
        $difficultyStats[$r->difficulte]++;
    }
}

// Average preparation time
$avgPrepTime = $totalRecettes > 0 
    ? round(array_sum(array_map(fn($r) => $r->temps_preparation, $recettes)) / $totalRecettes)
    : 0;

// Aliments by calorie ranges
$calorieRanges = [
    '0-100' => 0,
    '101-200' => 0,
    '201-300' => 0,
    '301-500' => 0,
    '500+' => 0
];
foreach ($aliments as $a) {
    $cal = (int)$a->calories;
    if ($cal <= 100) $calorieRanges['0-100']++;
    elseif ($cal <= 200) $calorieRanges['101-200']++;
    elseif ($cal <= 300) $calorieRanges['201-300']++;
    elseif ($cal <= 500) $calorieRanges['301-500']++;
    else $calorieRanges['500+']++;
}

// Average nutritional values
$avgCalories = $totalAliments > 0 
    ? round(array_sum(array_map(fn($a) => $a->calories, $aliments)) / $totalAliments)
    : 0;
$avgProteines = $totalAliments > 0 
    ? round(array_sum(array_map(fn($a) => $a->proteines, $aliments)) / $totalAliments, 1)
    : 0;
$avgGlucides = $totalAliments > 0 
    ? round(array_sum(array_map(fn($a) => $a->glucides, $aliments)) / $totalAliments, 1)
    : 0;
$avgLipides = $totalAliments > 0 
    ? round(array_sum(array_map(fn($a) => $a->lipides, $aliments)) / $totalAliments, 1)
    : 0;

// Top 5 most used aliments in recipes
$stmt = $db->query(
    "SELECT a.nom, COUNT(ra.id) as usage_count
     FROM aliments a
     JOIN recette_aliment ra ON ra.aliment_id = a.id
     GROUP BY a.id
     ORDER BY usage_count DESC
     LIMIT 5"
);
$topAliments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Categories distribution
$categoriesWithRecettes = 0;
$categoriesWithAliments = 0;
foreach ($categories as $cat) {
    $recetteIds = $categorieController->getRecetteIdsForCategory($cat->id);
    $alimentIds = $categorieController->getAlimentIdsForCategory($cat->id);
    if (!empty($recetteIds)) $categoriesWithRecettes++;
    if (!empty($alimentIds)) $categoriesWithAliments++;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoNutri – Statistiques</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
      :root {
        --green-dark: #2d6a1f;
        --green-main: #4a9e30;
        --green-light: #7ec44f;
        --green-pale: #e8f5e1;
        --orange: #f07c1b;
        --orange-light: #fde8d0;
        --black: #111;
        --grey: #666;
        --grey-light: #999;
        --white: #fff;
        --border: #e4eed9;
        --sidebar-bg: #0e2a08;
        --sidebar-w: 260px;
        --topbar-h: 68px;
        --bg: #f2f8ee;
        --card-bg: #fff;
        --red: #e53935;
        --blue: #1565c0;
      }

      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
      html { scroll-behavior: smooth; }
      body {
        font-family: "DM Sans", sans-serif;
        background: var(--bg);
        color: var(--black);
        display: flex;
        min-height: 100vh;
      }

      /* SIDEBAR */
      .sidebar {
        width: var(--sidebar-w);
        background: var(--sidebar-bg);
        min-height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        z-index: 50;
        overflow-y: auto;
      }
      .sidebar-logo {
        padding: 1.4rem 1.6rem;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        display: flex;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
      }
      .logo-icon {
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        display: grid; place-items: center;
        border: 1px solid rgba(255,255,255,0.2);
      }
      .logo-text {
        font-family: "Playfair Display", serif;
        font-size: 1.35rem; color: var(--white);
      }
      .logo-text span { color: var(--orange); }
      .sidebar-admin-tag {
        background: rgba(240,124,27,0.18); color: var(--orange);
        font-size: 0.65rem; font-weight: 700;
        padding: 0.15rem 0.5rem; border-radius: 4px;
        text-transform: uppercase;
        margin-left: auto;
      }
      .sidebar-section { padding: 1.2rem 0.9rem 0.4rem; }
      .sidebar-section-label {
        font-size: 0.65rem; font-weight: 700;
        color: rgba(255,255,255,0.3);
        text-transform: uppercase;
        padding: 0 0.7rem; margin-bottom: 0.5rem;
      }
      .nav-item {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0.9rem; border-radius: 10px;
        cursor: pointer; transition: background 0.2s;
        text-decoration: none; color: rgba(255,255,255,0.6);
        font-size: 0.88rem; font-weight: 500;
        margin-bottom: 0.15rem;
      }
      .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
      .nav-item.active {
        background: linear-gradient(90deg, rgba(74,158,48,0.35), rgba(74,158,48,0.1));
        color: var(--white); border-left: 3px solid var(--green-light);
      }
      .nav-icon { font-size: 1.1rem; width: 22px; text-align: center; }
      .nav-badge {
        margin-left: auto; background: var(--orange); color: var(--white);
        font-size: 0.65rem; font-weight: 700; padding: 0.15rem 0.5rem;
        border-radius: 50px;
      }
      .nav-badge.green { background: var(--green-main); }
      .sidebar-footer {
        margin-top: auto; padding: 1rem 0.9rem;
        border-top: 1px solid rgba(255,255,255,0.07);
      }
      .admin-profile {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.7rem 0.9rem; border-radius: 10px;
      }
      .admin-av {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, var(--green-main), var(--green-dark));
        display: grid; place-items: center;
        font-size: 0.75rem; font-weight: 700; color: var(--white);
        border: 2px solid rgba(126,196,79,0.4);
      }
      .admin-info strong { display: block; font-size: 0.83rem; color: var(--white); font-weight: 600; }
      .admin-info span { font-size: 0.72rem; color: rgba(255,255,255,0.45); }

      /* MAIN AREA */
      .main-area { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; }

      .topbar {
        height: var(--topbar-h); background: var(--white);
        border-bottom: 1px solid var(--border); padding: 0 2rem;
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; z-index: 40;
        box-shadow: 0 2px 12px rgba(45,106,31,0.06);
      }
      .page-title h1 { font-family: "Playfair Display", serif; font-size: 1.3rem; color: var(--green-dark); }
      .page-title span { font-size: 0.78rem; color: var(--grey-light); }
      .topbar-date {
        font-size: 0.8rem; color: var(--grey-light);
        background: var(--bg); border: 1.5px solid var(--border);
        border-radius: 10px; padding: 0.45rem 0.9rem;
      }

      .content { padding: 2rem; flex: 1; }

      /* Stats Grid */
      .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      .stat-card {
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: all 0.3s;
      }
      .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(45,106,31,0.15);
      }
      .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
      }
      .stat-value {
        font-family: "Playfair Display", serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--green-dark);
      }
      .stat-label {
        font-size: 0.9rem;
        color: var(--grey);
        font-weight: 500;
      }

      /* Charts Grid */
      .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      .chart-card {
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
      }
      .chart-title {
        font-family: "Playfair Display", serif;
        font-size: 1.2rem;
        color: var(--green-dark);
        margin-bottom: 1.5rem;
      }
      .chart-container {
        position: relative;
        height: 300px;
      }

      @media (max-width: 820px) {
        .sidebar { transform: translateX(-100%); }
        .main-area { margin-left: 0; }
        .charts-grid { grid-template-columns: 1fr; }
      }
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
        <a class="nav-item" href="listAliment.php"><span class="nav-icon">🥕</span> Aliments<span class="nav-badge">156</span></a>
        <a class="nav-item" href="listCategorie.php"><span class="nav-icon">🏷️</span> Catégories</a>
        <a class="nav-item active" href="statistiques.php"><span class="nav-icon">📈</span> Statistiques</a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-label">Modules</div>
        <a class="nav-item" href="#"><span class="nav-icon">🎯</span> Profils Nutritionnels</a>
        <a class="nav-item" href="#"><span class="nav-icon">📋</span> Suivi Alimentaire</a>
        <a class="nav-item" href="#"><span class="nav-icon">🤖</span> IA &amp; Recommandations</a>
        <a class="nav-item" href="#"><span class="nav-icon">🥕</span> Ingrédients</a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-label">Configuration</div>
        <a class="nav-item" href="#"><span class="nav-icon">⚙️</span> Paramètres</a>
        <a class="nav-item" href="#"><span class="nav-icon">📄</span> Rapports</a>
      </div>

      <div class="sidebar-footer">
        <a class="nav-item" href="../../views/index.php" style="margin-bottom:0.5rem; background:rgba(240,124,27,0.15); color:var(--orange); border:1px solid rgba(240,124,27,0.3);">
          <span class="nav-icon">🏠</span> Retour au site
        </a>
        <div class="admin-profile">
          <div class="admin-av">AD</div>
          <div class="admin-info">
            <strong>Admin EcoNutri</strong>
            <span>Super Administrateur</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- MAIN AREA -->
    <div class="main-area">
      <div class="topbar">
        <div class="page-title">
          <h1>📈 Statistiques</h1>
          <span>Vue d'ensemble des données EcoNutri</span>
        </div>
        <div class="topbar-date">📅 <?php echo date('d F Y'); ?></div>
      </div>

      <div class="content">
        <!-- Overview Stats -->
        <div class="stats-overview">
          <div class="stat-card">
            <div class="stat-icon">🍽️</div>
            <div class="stat-value"><?= $totalRecettes ?></div>
            <div class="stat-label">Recettes totales</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🥕</div>
            <div class="stat-value"><?= $totalAliments ?></div>
            <div class="stat-label">Aliments totaux</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🏷️</div>
            <div class="stat-value"><?= $totalCategories ?></div>
            <div class="stat-label">Catégories</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">⏱️</div>
            <div class="stat-value"><?= $avgPrepTime ?> min</div>
            <div class="stat-label">Temps moyen de préparation</div>
          </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
          <!-- Difficulty Distribution -->
          <div class="chart-card">
            <h3 class="chart-title">📊 Répartition par difficulté</h3>
            <div class="chart-container">
              <canvas id="difficultyChart"></canvas>
            </div>
          </div>

          <!-- Calorie Ranges -->
          <div class="chart-card">
            <h3 class="chart-title">🔥 Aliments par plage calorique</h3>
            <div class="chart-container">
              <canvas id="calorieChart"></canvas>
            </div>
          </div>

          <!-- Average Macros -->
          <div class="chart-card">
            <h3 class="chart-title">💪 Macronutriments moyens</h3>
            <div class="chart-container">
              <canvas id="macrosChart"></canvas>
            </div>
          </div>

          <!-- Top Aliments -->
          <div class="chart-card">
            <h3 class="chart-title">⭐ Top 5 aliments utilisés</h3>
            <div class="chart-container">
              <canvas id="topAlimentsChart"></canvas>
            </div>
          </div>

          <!-- Categories Distribution -->
          <div class="chart-card">
            <h3 class="chart-title">🏷️ Distribution des catégories</h3>
            <div class="chart-container">
              <canvas id="categoriesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Chart.js default config
      Chart.defaults.font.family = "'DM Sans', sans-serif";
      Chart.defaults.color = '#666';

      // Difficulty Distribution Pie Chart
      new Chart(document.getElementById('difficultyChart'), {
        type: 'pie',
        data: {
          labels: ['😊 Facile', '🔥 Moyen', '💪 Difficile'],
          datasets: [{
            data: [<?= $difficultyStats['facile'] ?>, <?= $difficultyStats['moyen'] ?>, <?= $difficultyStats['difficile'] ?>],
            backgroundColor: ['#7ec44f', '#f07c1b', '#e53935'],
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });

      // Calorie Ranges Bar Chart
      new Chart(document.getElementById('calorieChart'), {
        type: 'bar',
        data: {
          labels: ['0-100', '101-200', '201-300', '301-500', '500+'],
          datasets: [{
            label: 'Nombre d\'aliments',
            data: [
              <?= $calorieRanges['0-100'] ?>,
              <?= $calorieRanges['101-200'] ?>,
              <?= $calorieRanges['201-300'] ?>,
              <?= $calorieRanges['301-500'] ?>,
              <?= $calorieRanges['500+'] ?>
            ],
            backgroundColor: '#4a9e30',
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Average Macros Bar Chart
      new Chart(document.getElementById('macrosChart'), {
        type: 'bar',
        data: {
          labels: ['Calories (kcal)', 'Protéines (g)', 'Glucides (g)', 'Lipides (g)'],
          datasets: [{
            label: 'Moyenne',
            data: [<?= $avgCalories ?>, <?= $avgProteines ?>, <?= $avgGlucides ?>, <?= $avgLipides ?>],
            backgroundColor: ['#f07c1b', '#4a9e30', '#1565c0', '#e53935'],
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Top Aliments Horizontal Bar Chart
      new Chart(document.getElementById('topAlimentsChart'), {
        type: 'bar',
        data: {
          labels: [<?php foreach($topAliments as $ta) echo '"' . htmlspecialchars($ta['nom']) . '",'; ?>],
          datasets: [{
            label: 'Utilisations',
            data: [<?php foreach($topAliments as $ta) echo $ta['usage_count'] . ','; ?>],
            backgroundColor: '#7ec44f',
            borderRadius: 8
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            x: { beginAtZero: true }
          }
        }
      });

      // Categories Distribution Doughnut Chart
      new Chart(document.getElementById('categoriesChart'), {
        type: 'doughnut',
        data: {
          labels: ['Catégories avec recettes', 'Catégories avec aliments', 'Catégories vides'],
          datasets: [{
            data: [
              <?= $categoriesWithRecettes ?>,
              <?= $categoriesWithAliments ?>,
              <?= $totalCategories - $categoriesWithRecettes - $categoriesWithAliments ?>
            ],
            backgroundColor: ['#4a9e30', '#f07c1b', '#e4eed9'],
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    </script>
</body>
</html>
