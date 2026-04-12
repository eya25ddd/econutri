<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoNutri – Administration</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
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
        --red-light: #fdecea;
        --blue: #1565c0;
        --blue-light: #e3f0ff;
      }

      *,
      *::before,
      *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }
      html {
        scroll-behavior: smooth;
      }

      body {
        font-family: "DM Sans", sans-serif;
        background: var(--bg);
        color: var(--black);
        display: flex;
        min-height: 100vh;
        overflow-x: hidden;
      }

      /* ══════════════════════════════════════
       SIDEBAR
    ══════════════════════════════════════ */
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
        transition: transform 0.3s;
      }

      .sidebar-logo {
        padding: 1.4rem 1.6rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        display: flex;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
      }
      .logo-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
      }
      .logo-text {
        font-family: "Playfair Display", serif;
        font-size: 1.35rem;
        color: var(--white);
        letter-spacing: -0.4px;
      }
      .logo-text span {
        color: var(--orange);
      }

      .sidebar-admin-tag {
        background: rgba(240, 124, 27, 0.18);
        color: var(--orange);
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-left: auto;
      }

      .sidebar-section {
        padding: 1.2rem 0.9rem 0.4rem;
      }
      .sidebar-section-label {
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 0 0.7rem;
        margin-bottom: 0.5rem;
      }

      .nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 0.9rem;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.88rem;
        font-weight: 500;
        margin-bottom: 0.15rem;
        position: relative;
      }
      .nav-item:hover {
        background: rgba(255, 255, 255, 0.07);
        color: rgba(255, 255, 255, 0.9);
      }
      .nav-item.active {
        background: linear-gradient(
          90deg,
          rgba(74, 158, 48, 0.35),
          rgba(74, 158, 48, 0.1)
        );
        color: var(--white);
        border-left: 3px solid var(--green-light);
      }
      .nav-item.active .nav-icon {
        color: var(--green-light);
      }

      .nav-icon {
        font-size: 1.1rem;
        width: 22px;
        text-align: center;
        flex-shrink: 0;
      }

      .nav-badge {
        margin-left: auto;
        background: var(--orange);
        color: var(--white);
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 50px;
        min-width: 20px;
        text-align: center;
      }
      .nav-badge.green {
        background: var(--green-main);
      }

      .sidebar-footer {
        margin-top: auto;
        padding: 1rem 0.9rem;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
      }
      .admin-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 0.9rem;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.2s;
      }
      .admin-profile:hover {
        background: rgba(255, 255, 255, 0.07);
      }
      .admin-av {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(
          135deg,
          var(--green-main),
          var(--green-dark)
        );
        display: grid;
        place-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--white);
        flex-shrink: 0;
        border: 2px solid rgba(126, 196, 79, 0.4);
      }
      .admin-info strong {
        display: block;
        font-size: 0.83rem;
        color: var(--white);
        font-weight: 600;
      }
      .admin-info span {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.45);
      }
      .admin-profile .logout-icon {
        margin-left: auto;
        color: rgba(255, 255, 255, 0.3);
        font-size: 0.9rem;
        transition: color 0.2s;
      }
      .admin-profile:hover .logout-icon {
        color: var(--orange);
      }

      /* ══════════════════════════════════════
       MAIN AREA
    ══════════════════════════════════════ */
      .main-area {
        margin-left: var(--sidebar-w);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      /* ── Top Bar ── */
      .topbar {
        height: var(--topbar-h);
        background: var(--white);
        border-bottom: 1px solid var(--border);
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 40;
        box-shadow: 0 2px 12px rgba(45, 106, 31, 0.06);
      }

      .topbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
      }
      .page-title h1 {
        font-family: "Playfair Display", serif;
        font-size: 1.3rem;
        color: var(--green-dark);
        line-height: 1.1;
      }
      .page-title span {
        font-size: 0.78rem;
        color: var(--grey-light);
        font-weight: 400;
      }

      .topbar-search {
        display: flex;
        align-items: center;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.45rem 0.9rem;
        gap: 0.5rem;
        width: 260px;
      }
      .topbar-search input {
        border: none;
        outline: none;
        background: transparent;
        font-family: "DM Sans", sans-serif;
        font-size: 0.85rem;
        color: var(--black);
        width: 100%;
      }
      .topbar-search svg {
        color: var(--grey-light);
        flex-shrink: 0;
      }

      .topbar-right {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

      .topbar-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--bg);
        border: 1.5px solid var(--border);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
      }
      .topbar-icon-btn:hover {
        background: var(--green-pale);
        border-color: var(--green-main);
      }
      .topbar-icon-btn .notif-dot {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--orange);
        border: 2px solid var(--white);
      }

      .topbar-date {
        font-size: 0.8rem;
        color: var(--grey-light);
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.45rem 0.9rem;
        white-space: nowrap;
      }

      /* ── Content ── */
      .content {
        padding: 2rem;
        flex: 1;
      }

      /* ── Welcome Banner ── */
      .welcome-banner {
        background: linear-gradient(
          135deg,
          var(--green-dark) 0%,
          var(--green-main) 60%,
          var(--green-light) 100%
        );
        border-radius: 20px;
        padding: 1.8rem 2.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
      }
      .welcome-banner::before {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
      }
      .welcome-banner::after {
        content: "";
        position: absolute;
        right: 80px;
        bottom: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(240, 124, 27, 0.12);
      }
      .welcome-text h2 {
        font-family: "Playfair Display", serif;
        font-size: 1.4rem;
        color: var(--white);
        margin-bottom: 0.25rem;
      }
      .welcome-text p {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.85rem;
      }
      .welcome-actions {
        display: flex;
        gap: 0.7rem;
        position: relative;
        z-index: 1;
      }
      .wb-btn {
        padding: 0.55rem 1.2rem;
        border-radius: 50px;
        font-family: "DM Sans", sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: transform 0.2s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
      }
      .wb-btn:hover {
        transform: translateY(-2px);
      }
      .wb-btn.primary {
        background: var(--orange);
        color: var(--white);
      }
      .wb-btn.secondary {
        background: rgba(255, 255, 255, 0.15);
        color: var(--white);
        border: 1.5px solid rgba(255, 255, 255, 0.3);
      }

      /* ══════════════════════════════════════
       STAT CARDS
    ══════════════════════════════════════ */
      .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.2rem;
        margin-bottom: 2rem;
      }

      .stat-card {
        background: var(--card-bg);
        border: 1.5px solid var(--border);
        border-radius: 18px;
        padding: 1.4rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        transition: transform 0.2s, box-shadow 0.2s;
        animation: fadeUp 0.4s ease both;
      }
      .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 28px rgba(45, 106, 31, 0.1);
      }
      .stat-card:nth-child(1) {
        animation-delay: 0.05s;
      }
      .stat-card:nth-child(2) {
        animation-delay: 0.12s;
      }
      .stat-card:nth-child(3) {
        animation-delay: 0.19s;
      }
      .stat-card:nth-child(4) {
        animation-delay: 0.26s;
      }

      .stat-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        flex-shrink: 0;
      }
      .si-green {
        background: var(--green-pale);
      }
      .si-orange {
        background: var(--orange-light);
      }
      .si-blue {
        background: var(--blue-light);
      }
      .si-red {
        background: var(--red-light);
      }

      .stat-body {
        flex: 1;
      }
      .stat-body .stat-val {
        font-family: "Playfair Display", serif;
        font-size: 1.7rem;
        color: var(--black);
        line-height: 1;
        margin-bottom: 0.2rem;
      }
      .stat-body .stat-label {
        font-size: 0.78rem;
        color: var(--grey);
        font-weight: 500;
      }

      .stat-trend {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 0.2rem;
        white-space: nowrap;
        align-self: flex-start;
        margin-top: 0.2rem;
      }
      .trend-up {
        background: var(--green-pale);
        color: var(--green-dark);
      }
      .trend-down {
        background: var(--red-light);
        color: var(--red);
      }
      .trend-neutral {
        background: #f5f5f5;
        color: var(--grey);
      }

      /* ══════════════════════════════════════
       GRID 2 COLS
    ══════════════════════════════════════ */
      .dash-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
      }
      .dash-grid.three {
        grid-template-columns: 2fr 1fr;
      }

      .panel {
        background: var(--card-bg);
        border: 1.5px solid var(--border);
        border-radius: 18px;
        overflow: hidden;
        animation: fadeUp 0.5s ease both;
      }

      .panel-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .panel-header h3 {
        font-family: "Playfair Display", serif;
        font-size: 1rem;
        color: var(--black);
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .panel-header .ph-icon {
        font-size: 1.1rem;
      }

      .panel-action {
        font-size: 0.78rem;
        color: var(--green-main);
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: color 0.2s;
        background: none;
        border: none;
        font-family: "DM Sans", sans-serif;
      }
      .panel-action:hover {
        color: var(--green-dark);
      }

      .panel-body {
        padding: 1.5rem;
      }

      /* ── Chart Placeholder ── */
      .chart-wrap {
        height: 200px;
        position: relative;
        display: flex;
        align-items: flex-end;
        gap: 8px;
        padding-bottom: 28px;
      }
      .chart-bar-group {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        height: 100%;
        justify-content: flex-end;
      }
      .chart-bars {
        display: flex;
        gap: 3px;
        align-items: flex-end;
        width: 100%;
        flex: 1;
      }
      .cbar {
        flex: 1;
        border-radius: 6px 6px 0 0;
        transition: opacity 0.2s;
        cursor: pointer;
      }
      .cbar:hover {
        opacity: 0.8;
      }
      .cbar.green {
        background: linear-gradient(
          180deg,
          var(--green-light),
          var(--green-main)
        );
      }
      .cbar.orange {
        background: linear-gradient(180deg, #ffa852, var(--orange));
      }
      .chart-label {
        font-size: 0.68rem;
        color: var(--grey-light);
        white-space: nowrap;
      }

      .chart-legend {
        display: flex;
        gap: 1.2rem;
        margin-top: 1rem;
      }
      .legend-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        color: var(--grey);
      }
      .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
      }

      /* ── Donut Chart ── */
      .donut-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
      }
      .donut-svg-wrap {
        position: relative;
      }
      .donut-center {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }
      .donut-center strong {
        font-family: "Playfair Display", serif;
        font-size: 1.5rem;
        color: var(--black);
      }
      .donut-center span {
        font-size: 0.68rem;
        color: var(--grey-light);
      }
      .donut-legend {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
      }
      .dl-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.8rem;
      }
      .dl-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
      }
      .dl-label {
        color: var(--grey);
        flex: 1;
      }
      .dl-val {
        font-weight: 700;
        color: var(--black);
      }
      .dl-pct {
        color: var(--grey-light);
        font-size: 0.72rem;
      }

      /* ══════════════════════════════════════
       TABLES
    ══════════════════════════════════════ */
      .table-wrap {
        overflow-x: auto;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.83rem;
      }
      thead tr {
        border-bottom: 2px solid var(--border);
      }
      thead th {
        text-align: left;
        padding: 0.7rem 1rem;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--grey-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
      }
      tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
        cursor: pointer;
      }
      tbody tr:last-child {
        border-bottom: none;
      }
      tbody tr:hover {
        background: var(--bg);
      }
      tbody td {
        padding: 0.85rem 1rem;
        color: var(--black);
        vertical-align: middle;
      }

      .td-user {
        display: flex;
        align-items: center;
        gap: 0.65rem;
      }
      .td-av {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--white);
        flex-shrink: 0;
      }
      .td-av.g {
        background: var(--green-main);
      }
      .td-av.o {
        background: var(--orange);
      }
      .td-av.d {
        background: var(--green-dark);
      }
      .td-av.b {
        background: var(--blue);
      }
      .td-name strong {
        display: block;
        font-size: 0.83rem;
        color: var(--black);
      }
      .td-name span {
        font-size: 0.72rem;
        color: var(--grey-light);
      }

      .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
      }
      .badge-green {
        background: var(--green-pale);
        color: var(--green-dark);
      }
      .badge-orange {
        background: var(--orange-light);
        color: #b85a00;
      }
      .badge-red {
        background: var(--red-light);
        color: var(--red);
      }
      .badge-blue {
        background: var(--blue-light);
        color: var(--blue);
      }
      .badge-grey {
        background: #f0f0f0;
        color: var(--grey);
      }

      .action-btns {
        display: flex;
        gap: 0.4rem;
      }
      .action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: var(--white);
        cursor: pointer;
        display: grid;
        place-items: center;
        font-size: 0.85rem;
        transition: all 0.2s;
      }
      .action-btn:hover {
        border-color: var(--green-main);
        background: var(--green-pale);
      }
      .action-btn.danger:hover {
        border-color: var(--red);
        background: var(--red-light);
      }

      /* ══════════════════════════════════════
       ACTIVITY FEED
    ══════════════════════════════════════ */
      .activity-list {
        display: flex;
        flex-direction: column;
        gap: 0;
      }
      .activity-item {
        display: flex;
        gap: 1rem;
        padding: 0.9rem 0;
        border-bottom: 1px solid var(--border);
        position: relative;
      }
      .activity-item:last-child {
        border-bottom: none;
      }
      .act-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 1rem;
        flex-shrink: 0;
      }
      .act-body {
        flex: 1;
      }
      .act-body strong {
        font-size: 0.83rem;
        color: var(--black);
        display: block;
        margin-bottom: 0.15rem;
      }
      .act-body span {
        font-size: 0.75rem;
        color: var(--grey-light);
      }
      .act-time {
        font-size: 0.72rem;
        color: var(--grey-light);
        white-space: nowrap;
        align-self: center;
      }

      /* ══════════════════════════════════════
       MINI PANELS
    ══════════════════════════════════════ */
      .mini-panels {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.2rem;
        margin-bottom: 1.5rem;
      }

      .mini-panel {
        background: var(--card-bg);
        border: 1.5px solid var(--border);
        border-radius: 18px;
        padding: 1.3rem 1.4rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .mini-panel:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(45, 106, 31, 0.1);
      }
      .mp-icon {
        font-size: 1.8rem;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
      }
      .mp-body strong {
        display: block;
        font-family: "Playfair Display", serif;
        font-size: 1.3rem;
        color: var(--black);
      }
      .mp-body span {
        font-size: 0.75rem;
        color: var(--grey);
      }
      .mp-trend {
        margin-left: auto;
        font-size: 0.75rem;
        font-weight: 700;
      }
      .mp-trend.up {
        color: var(--green-main);
      }
      .mp-trend.down {
        color: var(--red);
      }

      /* ══════════════════════════════════════
       TOP RECIPES PANEL
    ══════════════════════════════════════ */
      .recipe-rank-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
      .recipe-rank-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.65rem 0.8rem;
        border-radius: 12px;
        transition: background 0.15s;
        cursor: pointer;
      }
      .recipe-rank-item:hover {
        background: var(--bg);
      }
      .rank-num {
        font-family: "Playfair Display", serif;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--grey-light);
        width: 18px;
        text-align: center;
        flex-shrink: 0;
      }
      .rank-num.top {
        color: var(--orange);
      }
      .rank-img {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        background: var(--bg);
        display: grid;
        place-items: center;
        font-size: 1.3rem;
      }
      .rank-info {
        flex: 1;
        min-width: 0;
      }
      .rank-info strong {
        display: block;
        font-size: 0.83rem;
        color: var(--black);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .rank-info span {
        font-size: 0.72rem;
        color: var(--grey-light);
      }
      .rank-views {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--green-dark);
        background: var(--green-pale);
        padding: 0.2rem 0.55rem;
        border-radius: 50px;
        white-space: nowrap;
      }

      /* ══════════════════════════════════════
       MODAL (Add Recette / Add User)
    ══════════════════════════════════════ */
      .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 200;
        display: grid;
        place-items: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
        backdrop-filter: blur(4px);
      }
      .modal-overlay.open {
        opacity: 1;
        pointer-events: all;
      }
      .modal {
        background: var(--white);
        border-radius: 22px;
        width: min(520px, 94vw);
        max-height: 88vh;
        overflow-y: auto;
        transform: translateY(28px) scale(0.97);
        transition: transform 0.3s;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.22);
      }
      .modal-overlay.open .modal {
        transform: none;
      }
      .modal-head {
        background: linear-gradient(
          135deg,
          var(--green-dark),
          var(--green-main)
        );
        padding: 1.4rem 1.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 22px 22px 0 0;
      }
      .modal-head h2 {
        font-family: "Playfair Display", serif;
        font-size: 1.15rem;
        color: var(--white);
      }
      .modal-head p {
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 0.15rem;
      }
      .modal-x {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: var(--white);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        cursor: pointer;
        display: grid;
        place-items: center;
        font-size: 1rem;
      }
      .modal-x:hover {
        background: rgba(255, 255, 255, 0.3);
      }
      .modal-content {
        padding: 1.6rem;
      }
      .fg {
        margin-bottom: 1.1rem;
      }
      .fg label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--green-dark);
        margin-bottom: 0.38rem;
      }
      .fg label em {
        color: var(--orange);
        font-style: normal;
      }
      .fc {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: "DM Sans", sans-serif;
        font-size: 0.88rem;
        color: var(--black);
        outline: none;
        transition: border-color 0.2s;
        background: var(--bg);
      }
      .fc:focus {
        border-color: var(--green-main);
        background: var(--white);
      }
      textarea.fc {
        resize: vertical;
        min-height: 80px;
      }
      .fg-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.9rem;
      }
      .modal-foot {
        padding: 1rem 1.6rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
      }
      .btn-cancel {
        padding: 0.6rem 1.3rem;
        border: 1.5px solid var(--border);
        border-radius: 50px;
        background: transparent;
        font-family: "DM Sans", sans-serif;
        font-size: 0.87rem;
        color: var(--grey);
        cursor: pointer;
      }
      .btn-save {
        padding: 0.6rem 1.6rem;
        border: none;
        border-radius: 50px;
        background: linear-gradient(
          135deg,
          var(--green-main),
          var(--green-dark)
        );
        color: var(--white);
        font-family: "DM Sans", sans-serif;
        font-size: 0.87rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
      }
      .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(45, 106, 31, 0.3);
      }

      /* Toast */
      .toast {
        position: fixed;
        bottom: 1.8rem;
        right: 2rem;
        background: var(--green-dark);
        color: var(--white);
        padding: 0.9rem 1.4rem;
        border-radius: 14px;
        font-size: 0.88rem;
        font-weight: 500;
        z-index: 300;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.2);
        transform: translateY(70px);
        opacity: 0;
        transition: transform 0.35s, opacity 0.35s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .toast.show {
        transform: translateY(0);
        opacity: 1;
      }

      /* Animations */
      @keyframes fadeUp {
        from {
          opacity: 0;
          transform: translateY(18px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      /* Responsive */
      @media (max-width: 1100px) {
        :root {
          --sidebar-w: 220px;
        }
        .stats-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        .mini-panels {
          grid-template-columns: 1fr 1fr;
        }
      }
      @media (max-width: 820px) {
        .sidebar {
          transform: translateX(-100%);
        }
        .sidebar.open {
          transform: translateX(0);
        }
        .main-area {
          margin-left: 0;
        }
        .dash-grid,
        .dash-grid.three {
          grid-template-columns: 1fr;
        }
        .stats-grid {
          grid-template-columns: 1fr 1fr;
        }
      }
    </style>
  </head>
  <body>
    <!-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
      <a class="sidebar-logo" href="#">
        <div class="logo-icon">
          <svg viewBox="0 0 32 32" fill="none" width="24" height="24">
            <path
              d="M16 4C10 4 5 8 4 14c4-2 9-1 12 3 3-4 8-5 12-3-1-6-6-10-12-10z"
              fill="#7ec44f"
            />
            <path
              d="M4 14c-1 5 2 10 7 12l5-8-5-4c-3 0-6 0-7 0z"
              fill="#4a9e30"
            />
            <path
              d="M28 14c1 5-2 10-7 12l-5-8 5-4c3 0 6 0 7 0z"
              fill="#2d6a1f"
            />
            <circle cx="16" cy="22" r="3" fill="#f07c1b" />
          </svg>
        </div>
        <span class="logo-text">Eco<span>Nutri</span></span>
        <span class="sidebar-admin-tag">Admin</span>
      </a>

      <!-- Main -->
      <div class="sidebar-section">
        <div class="sidebar-section-label">Principal</div>
        <a class="nav-item active" href="#" onclick="setPage('dashboard',this)">
          <span class="nav-icon">📊</span> Tableau de bord
        </a>
        <a class="nav-item" href="#" onclick="setPage('users',this)">
          <span class="nav-icon">👥</span> Utilisateurs
          <span class="nav-badge">1 248</span>
        </a>
        <a class="nav-item" href="#" onclick="setPage('recettes',this)">
          <span class="nav-icon">🍽️</span> Recettes
          <span class="nav-badge green">240</span>
        </a>
        <a class="nav-item" href="#" onclick="setPage('stats',this)">
          <span class="nav-icon">📈</span> Statistiques
        </a>
      </div>

      <!-- Modules -->
      <div class="sidebar-section">
        <div class="sidebar-section-label">Modules</div>
        <a class="nav-item" href="#" onclick="setPage('profils',this)">
          <span class="nav-icon">🎯</span> Profils Nutritionnels
        </a>
        <a class="nav-item" href="#" onclick="setPage('suivi',this)">
          <span class="nav-icon">📋</span> Suivi Alimentaire
        </a>
        <a class="nav-item" href="#" onclick="setPage('ia',this)">
          <span class="nav-icon">🤖</span> IA &amp; Recommandations
        </a>
        <a class="nav-item" href="#" onclick="setPage('ingredients',this)">
          <span class="nav-icon">🥕</span> Ingrédients
        </a>
      </div>

      <!-- Config -->
      <div class="sidebar-section">
        <div class="sidebar-section-label">Configuration</div>
        <a class="nav-item" href="#" onclick="setPage('settings',this)">
          <span class="nav-icon">⚙️</span> Paramètres
        </a>
        <a class="nav-item" href="#" onclick="setPage('reports',this)">
          <span class="nav-icon">📄</span> Rapports
        </a>
      </div>

      <div class="sidebar-footer">
        <div class="admin-profile">
          <div class="admin-av">AD</div>
          <div class="admin-info">
            <strong>Admin EcoNutri</strong>
            <span>Super Administrateur</span>
          </div>
          <span class="logout-icon">⏻</span>
        </div>
      </div>
    </aside>

    <!-- ══════════════════════════════════════════
     MAIN AREA
══════════════════════════════════════════ -->
    <div class="main-area">
      <!-- TOP BAR -->
      <div class="topbar">
        <div class="topbar-left">
          <div class="page-title">
            <h1 id="pageTitle">Tableau de bord</h1>
            <span id="pageSub">Vue d'ensemble de la plateforme</span>
          </div>
        </div>

        <div class="topbar-search">
          <svg
            width="15"
            height="15"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          <input type="text" placeholder="Rechercher…" />
        </div>

        <div class="topbar-right">
          <div class="topbar-date">📅 Mardi 7 avril 2026</div>
          <div class="topbar-icon-btn" title="Notifications">
            🔔
            <span class="notif-dot"></span>
          </div>
          <div class="topbar-icon-btn" title="Messages">💬</div>
        </div>
      </div>

      <!-- CONTENT -->
      <div class="content" id="mainContent">
        <!-- ── WELCOME BANNER ── -->
        <div class="welcome-banner">
          <div class="welcome-text">
            <h2>👋 Bonjour, Administrateur !</h2>
            <p>
              Bienvenue sur votre espace de gestion EcoNutri. Voici le résumé
              d'aujourd'hui.
            </p>
          </div>
          <div class="welcome-actions">
            <button class="wb-btn primary" onclick="openModal('addRecette')">
              + Ajouter Recette
            </button>
            <button class="wb-btn secondary" onclick="openModal('addUser')">
              + Nouvel Utilisateur
            </button>
          </div>
        </div>

        <!-- ── STAT CARDS ── -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon-wrap si-green">👥</div>
            <div class="stat-body">
              <div class="stat-val">1 248</div>
              <div class="stat-label">Utilisateurs inscrits</div>
            </div>
            <div class="stat-trend trend-up">↑ +12%</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon-wrap si-orange">🍽️</div>
            <div class="stat-body">
              <div class="stat-val">240</div>
              <div class="stat-label">Recettes publiées</div>
            </div>
            <div class="stat-trend trend-up">↑ +8%</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon-wrap si-blue">📋</div>
            <div class="stat-body">
              <div class="stat-val">3 842</div>
              <div class="stat-label">Repas suivis ce mois</div>
            </div>
            <div class="stat-trend trend-up">↑ +22%</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon-wrap si-red">♻️</div>
            <div class="stat-body">
              <div class="stat-val">-35%</div>
              <div class="stat-label">Gaspillage réduit moy.</div>
            </div>
            <div class="stat-trend trend-up">↑ +5%</div>
          </div>
        </div>

        <!-- ── MINI PANELS ── -->
        <div class="mini-panels">
          <div class="mini-panel">
            <div class="mp-icon si-green">🟢</div>
            <div class="mp-body">
              <strong>198</strong>
              <span>Utilisateurs actifs aujourd'hui</span>
            </div>
            <span class="mp-trend up">↑ 14%</span>
          </div>
          <div class="mini-panel">
            <div class="mp-icon si-orange">⭐</div>
            <div class="mp-body">
              <strong>4.8 / 5</strong>
              <span>Note moyenne des recettes</span>
            </div>
            <span class="mp-trend up">↑ 0.2</span>
          </div>
          <div class="mini-panel">
            <div class="mp-icon si-blue">🤖</div>
            <div class="mp-body">
              <strong>6 420</strong>
              <span>Recommandations IA générées</span>
            </div>
            <span class="mp-trend up">↑ 18%</span>
          </div>
        </div>

        <!-- ── CHART + DONUT ── -->
        <div class="dash-grid">
          <!-- Bar Chart -->
          <div class="panel" style="animation-delay: 0.1s">
            <div class="panel-header">
              <h3><span class="ph-icon">📈</span> Activité mensuelle</h3>
              <select
                class="fc"
                style="width: auto; padding: 0.3rem 0.7rem; font-size: 0.78rem"
              >
                <option>2026</option>
                <option>2025</option>
              </select>
            </div>
            <div class="panel-body">
              <div class="chart-wrap" id="barChart"></div>
              <div class="chart-legend">
                <div class="legend-item">
                  <span
                    class="legend-dot"
                    style="background: var(--green-main)"
                  ></span
                  >Inscriptions
                </div>
                <div class="legend-item">
                  <span
                    class="legend-dot"
                    style="background: var(--orange)"
                  ></span
                  >Recettes ajoutées
                </div>
              </div>
            </div>
          </div>

          <!-- Donut Chart -->
          <div class="panel" style="animation-delay: 0.18s">
            <div class="panel-header">
              <h3><span class="ph-icon">🥗</span> Catégories recettes</h3>
            </div>
            <div class="panel-body">
              <div class="donut-wrap">
                <div class="donut-svg-wrap">
                  <svg
                    width="150"
                    height="150"
                    viewBox="0 0 150 150"
                    id="donutSvg"
                  ></svg>
                  <div class="donut-center">
                    <strong>240</strong>
                    <span>Recettes</span>
                  </div>
                </div>
                <div class="donut-legend" id="donutLegend"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── USERS TABLE + TOP RECIPES ── -->
        <div class="dash-grid three">
          <!-- Recent Users -->
          <div class="panel" style="animation-delay: 0.22s">
            <div class="panel-header">
              <h3>
                <span class="ph-icon">👥</span> Derniers utilisateurs inscrits
              </h3>
              <button
                class="panel-action"
                onclick="setPage('users', document.querySelector('.nav-item:nth-child(2)'))"
              >
                Voir tout →
              </button>
            </div>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Utilisateur</th>
                    <th>Objectif</th>
                    <th>Statut</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-av g">SM</div>
                        <div class="td-name">
                          <strong>Samira Mansouri</strong
                          ><span>samira@email.com</span>
                        </div>
                      </div>
                    </td>
                    <td>Équilibre</td>
                    <td><span class="badge badge-green">✓ Actif</span></td>
                    <td>05 avr. 2026</td>
                    <td>
                      <div class="action-btns">
                        <button class="action-btn" title="Voir">👁</button
                        ><button class="action-btn" title="Modifier">✏️</button
                        ><button class="action-btn danger" title="Supprimer">
                          🗑
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-av o">AK</div>
                        <div class="td-name">
                          <strong>Ahmed Khelil</strong
                          ><span>ahmed@email.com</span>
                        </div>
                      </div>
                    </td>
                    <td>Prise de masse</td>
                    <td><span class="badge badge-green">✓ Actif</span></td>
                    <td>03 avr. 2026</td>
                    <td>
                      <div class="action-btns">
                        <button class="action-btn">👁</button
                        ><button class="action-btn">✏️</button
                        ><button class="action-btn danger">🗑</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-av d">LB</div>
                        <div class="td-name">
                          <strong>Lina Bensalem</strong
                          ><span>lina@email.com</span>
                        </div>
                      </div>
                    </td>
                    <td>Perte de poids</td>
                    <td><span class="badge badge-orange">⏳ Inactif</span></td>
                    <td>01 avr. 2026</td>
                    <td>
                      <div class="action-btns">
                        <button class="action-btn">👁</button
                        ><button class="action-btn">✏️</button
                        ><button class="action-btn danger">🗑</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-av b">YT</div>
                        <div class="td-name">
                          <strong>Youssef Touati</strong
                          ><span>youssef@email.com</span>
                        </div>
                      </div>
                    </td>
                    <td>Vegan</td>
                    <td><span class="badge badge-green">✓ Actif</span></td>
                    <td>29 mars 2026</td>
                    <td>
                      <div class="action-btns">
                        <button class="action-btn">👁</button
                        ><button class="action-btn">✏️</button
                        ><button class="action-btn danger">🗑</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="td-user">
                        <div class="td-av g">ND</div>
                        <div class="td-name">
                          <strong>Nadia Dridi</strong
                          ><span>nadia@email.com</span>
                        </div>
                      </div>
                    </td>
                    <td>Végétarien</td>
                    <td><span class="badge badge-red">✗ Banni</span></td>
                    <td>25 mars 2026</td>
                    <td>
                      <div class="action-btns">
                        <button class="action-btn">👁</button
                        ><button class="action-btn">✏️</button
                        ><button class="action-btn danger">🗑</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Top Recipes -->
          <div class="panel" style="animation-delay: 0.28s">
            <div class="panel-header">
              <h3><span class="ph-icon">🏆</span> Top Recettes</h3>
              <button
                class="panel-action"
                onclick="setPage('recettes',document.querySelectorAll('.nav-item')[2])"
              >
                Voir tout →
              </button>
            </div>
            <div class="panel-body">
              <div class="recipe-rank-list">
                <div class="recipe-rank-item">
                  <span class="rank-num top">1</span>
                  <div class="rank-img">🥗</div>
                  <div class="rank-info">
                    <strong>Bowl Quinoa &amp; Avocat</strong
                    ><span>Samira M. · Salade</span>
                  </div>
                  <span class="rank-views">4.9 ★</span>
                </div>
                <div class="recipe-rank-item">
                  <span class="rank-num top">2</span>
                  <div class="rank-img">🍗</div>
                  <div class="rank-info">
                    <strong>Poulet Citron &amp; Herbes</strong
                    ><span>Lina B. · Plat chaud</span>
                  </div>
                  <span class="rank-views">4.8 ★</span>
                </div>
                <div class="recipe-rank-item">
                  <span class="rank-num top">3</span>
                  <div class="rank-img">🥤</div>
                  <div class="rank-info">
                    <strong>Smoothie Vert Énergisant</strong
                    ><span>Ahmed K. · Smoothie</span>
                  </div>
                  <span class="rank-views">4.7 ★</span>
                </div>
                <div class="recipe-rank-item">
                  <span class="rank-num">4</span>
                  <div class="rank-img">🍲</div>
                  <div class="rank-info">
                    <strong>Soupe Lentilles Épicée</strong
                    ><span>Youssef T. · Soupe</span>
                  </div>
                  <span class="rank-views">4.6 ★</span>
                </div>
                <div class="recipe-rank-item">
                  <span class="rank-num">5</span>
                  <div class="rank-img">🥣</div>
                  <div class="rank-info">
                    <strong>Porridge Fruits Rouges</strong
                    ><span>Nadia D. · Petit-déj.</span>
                  </div>
                  <span class="rank-views">4.5 ★</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── RECENT RECIPES TABLE ── -->
        <div class="panel" style="margin-bottom: 1.5rem; animation-delay: 0.3s">
          <div class="panel-header">
            <h3><span class="ph-icon">🍽️</span> Recettes récentes</h3>
            <button class="panel-action" onclick="openModal('addRecette')">
              + Ajouter
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Recette</th>
                  <th>Auteur</th>
                  <th>Catégorie</th>
                  <th>Temps</th>
                  <th>Calories</th>
                  <th>Note</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="td-user">
                      <div
                        style="
                          font-size: 1.3rem;
                          width: 32px;
                          text-align: center;
                        "
                      >
                        🥗
                      </div>
                      <div class="td-name">
                        <strong>Bowl Quinoa &amp; Avocat</strong
                        ><span>05 avr. 2026</span>
                      </div>
                    </div>
                  </td>
                  <td>Samira M.</td>
                  <td><span class="badge badge-green">Salade</span></td>
                  <td>15 min</td>
                  <td>320 kcal</td>
                  <td>⭐ 4.9</td>
                  <td><span class="badge badge-green">✓ Publié</span></td>
                  <td>
                    <div class="action-btns">
                      <button class="action-btn">👁</button
                      ><button class="action-btn">✏️</button
                      ><button class="action-btn danger">🗑</button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="td-user">
                      <div
                        style="
                          font-size: 1.3rem;
                          width: 32px;
                          text-align: center;
                        "
                      >
                        🍗
                      </div>
                      <div class="td-name">
                        <strong>Poulet Citron &amp; Herbes</strong
                        ><span>03 avr. 2026</span>
                      </div>
                    </div>
                  </td>
                  <td>Lina B.</td>
                  <td><span class="badge badge-orange">Plat chaud</span></td>
                  <td>30 min</td>
                  <td>420 kcal</td>
                  <td>⭐ 4.8</td>
                  <td><span class="badge badge-green">✓ Publié</span></td>
                  <td>
                    <div class="action-btns">
                      <button class="action-btn">👁</button
                      ><button class="action-btn">✏️</button
                      ><button class="action-btn danger">🗑</button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="td-user">
                      <div
                        style="
                          font-size: 1.3rem;
                          width: 32px;
                          text-align: center;
                        "
                      >
                        🥤
                      </div>
                      <div class="td-name">
                        <strong>Smoothie Vert Énergisant</strong
                        ><span>01 avr. 2026</span>
                      </div>
                    </div>
                  </td>
                  <td>Ahmed K.</td>
                  <td><span class="badge badge-blue">Smoothie</span></td>
                  <td>5 min</td>
                  <td>180 kcal</td>
                  <td>⭐ 4.7</td>
                  <td><span class="badge badge-orange">⏳ En attente</span></td>
                  <td>
                    <div class="action-btns">
                      <button class="action-btn">👁</button
                      ><button class="action-btn">✏️</button
                      ><button class="action-btn danger">🗑</button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="td-user">
                      <div
                        style="
                          font-size: 1.3rem;
                          width: 32px;
                          text-align: center;
                        "
                      >
                        🍲
                      </div>
                      <div class="td-name">
                        <strong>Soupe Lentilles Épicée</strong
                        ><span>29 mars 2026</span>
                      </div>
                    </div>
                  </td>
                  <td>Youssef T.</td>
                  <td><span class="badge badge-blue">Soupe</span></td>
                  <td>25 min</td>
                  <td>290 kcal</td>
                  <td>⭐ 4.6</td>
                  <td><span class="badge badge-green">✓ Publié</span></td>
                  <td>
                    <div class="action-btns">
                      <button class="action-btn">👁</button
                      ><button class="action-btn">✏️</button
                      ><button class="action-btn danger">🗑</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ── ACTIVITY FEED ── -->
        <div class="dash-grid">
          <div class="panel" style="animation-delay: 0.35s">
            <div class="panel-header">
              <h3><span class="ph-icon">⚡</span> Activité récente</h3>
              <span style="font-size: 0.75rem; color: var(--grey-light)"
                >Temps réel</span
              >
            </div>
            <div class="panel-body">
              <div class="activity-list">
                <div class="activity-item">
                  <div class="act-icon si-green">👤</div>
                  <div class="act-body">
                    <strong>Nouvelle inscription — Samira Mansouri</strong
                    ><span
                      >A rejoint EcoNutri et complété son profil
                      nutritionnel</span
                    >
                  </div>
                  <span class="act-time">il y a 5 min</span>
                </div>
                <div class="activity-item">
                  <div class="act-icon si-orange">🍽️</div>
                  <div class="act-body">
                    <strong>Recette soumise — "Tabboulé Libanais"</strong
                    ><span
                      >Maya H. a partagé une nouvelle recette, en attente de
                      validation</span
                    >
                  </div>
                  <span class="act-time">il y a 23 min</span>
                </div>
                <div class="activity-item">
                  <div class="act-icon si-green">🤖</div>
                  <div class="act-body">
                    <strong>IA — 42 recommandations générées</strong
                    ><span
                      >Le moteur de recommandations a traité 42 nouveaux
                      profils</span
                    >
                  </div>
                  <span class="act-time">il y a 1h</span>
                </div>
                <div class="activity-item">
                  <div class="act-icon" style="background: var(--blue-light)">
                    ♻️
                  </div>
                  <div class="act-body">
                    <strong>Rapport gaspillage — Mise à jour</strong
                    ><span
                      >Les statistiques de réduction du gaspillage ont été
                      recalculées</span
                    >
                  </div>
                  <span class="act-time">il y a 2h</span>
                </div>
                <div class="activity-item">
                  <div class="act-icon si-orange">⭐</div>
                  <div class="act-body">
                    <strong>Nouvel avis — "Bowl Quinoa" · 5 étoiles</strong
                    ><span
                      >Ahmed K. a laissé un avis positif sur la recette
                      vedette</span
                    >
                  </div>
                  <span class="act-time">il y a 3h</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats Panel -->
          <div class="panel" style="animation-delay: 0.4s">
            <div class="panel-header">
              <h3><span class="ph-icon">🎯</span> Objectifs nutritionnels</h3>
            </div>
            <div class="panel-body">
              <div style="display: flex; flex-direction: column; gap: 1.1rem">
                <div>
                  <div
                    style="
                      display: flex;
                      justify-content: space-between;
                      font-size: 0.82rem;
                      margin-bottom: 0.4rem;
                    "
                  >
                    <span style="color: var(--grey)">Perte de poids</span>
                    <strong style="color: var(--green-dark)">38%</strong>
                  </div>
                  <div
                    style="
                      background: var(--border);
                      border-radius: 6px;
                      height: 8px;
                    "
                  >
                    <div
                      style="
                        background: linear-gradient(
                          90deg,
                          var(--green-light),
                          var(--green-main)
                        );
                        height: 100%;
                        border-radius: 6px;
                        width: 38%;
                      "
                    ></div>
                  </div>
                </div>

                <div>
                  <div
                    style="
                      display: flex;
                      justify-content: space-between;
                      font-size: 0.82rem;
                      margin-bottom: 0.4rem;
                    "
                  >
                    <span style="color: var(--grey)"
                      >Alimentation équilibrée</span
                    >
                    <strong style="color: var(--green-dark)">29%</strong>
                  </div>
                  <div
                    style="
                      background: var(--border);
                      border-radius: 6px;
                      height: 8px;
                    "
                  >
                    <div
                      style="
                        background: linear-gradient(
                          90deg,
                          #ffa852,
                          var(--orange)
                        );
                        height: 100%;
                        border-radius: 6px;
                        width: 29%;
                      "
                    ></div>
                  </div>
                </div>

                <div>
                  <div
                    style="
                      display: flex;
                      justify-content: space-between;
                      font-size: 0.82rem;
                      margin-bottom: 0.4rem;
                    "
                  >
                    <span style="color: var(--grey)">Prise de masse</span>
                    <strong style="color: var(--green-dark)">18%</strong>
                  </div>
                  <div
                    style="
                      background: var(--border);
                      border-radius: 6px;
                      height: 8px;
                    "
                  >
                    <div
                      style="
                        background: linear-gradient(
                          90deg,
                          #64b5f6,
                          var(--blue)
                        );
                        height: 100%;
                        border-radius: 6px;
                        width: 18%;
                      "
                    ></div>
                  </div>
                </div>

                <div>
                  <div
                    style="
                      display: flex;
                      justify-content: space-between;
                      font-size: 0.82rem;
                      margin-bottom: 0.4rem;
                    "
                  >
                    <span style="color: var(--grey)">Vegan / Végétarien</span>
                    <strong style="color: var(--green-dark)">15%</strong>
                  </div>
                  <div
                    style="
                      background: var(--border);
                      border-radius: 6px;
                      height: 8px;
                    "
                  >
                    <div
                      style="
                        background: linear-gradient(
                          90deg,
                          var(--green-pale),
                          var(--green-light)
                        );
                        height: 100%;
                        border-radius: 6px;
                        width: 15%;
                      "
                    ></div>
                  </div>
                </div>

                <div
                  style="
                    margin-top: 0.5rem;
                    padding-top: 1rem;
                    border-top: 1px solid var(--border);
                  "
                >
                  <div
                    style="
                      display: flex;
                      align-items: center;
                      justify-content: space-between;
                    "
                  >
                    <span style="font-size: 0.8rem; color: var(--grey)"
                      >Taux de complétion des profils</span
                    >
                    <span class="badge badge-green">76%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /content -->
    </div>
    <!-- /main-area -->

    <!-- ══════════════════════════════════════════
     MODAL — ADD RECETTE
══════════════════════════════════════════ -->
    <div
      class="modal-overlay"
      id="addRecetteModal"
      onclick="closeOut(event,'addRecetteModal')"
    >
      <div class="modal">
        <div class="modal-head">
          <div>
            <h2>🍽️ Ajouter une Recette</h2>
            <p>Publiez une nouvelle recette sur la plateforme</p>
          </div>
          <button class="modal-x" onclick="closeModal('addRecetteModal')">
            ✕
          </button>
        </div>
        <div class="modal-content">
          <div class="fg">
            <label>Nom de la recette <em>*</em></label>
            <input
              type="text"
              class="fc"
              placeholder="Ex : Salade Quinoa et Avocat"
            />
          </div>
          <div class="fg-row">
            <div class="fg">
              <label>Catégorie <em>*</em></label>
              <select class="fc">
                <option>-- Choisir --</option>
                <option>Salade</option>
                <option>Plat chaud</option>
                <option>Smoothie</option>
                <option>Soupe</option>
                <option>Petit-déjeuner</option>
                <option>Dessert</option>
              </select>
            </div>
            <div class="fg">
              <label>Temps de préparation <em>*</em></label>
              <input type="text" class="fc" placeholder="20 min" />
            </div>
          </div>
          <div class="fg-row">
            <div class="fg">
              <label>Calories (kcal)</label>
              <input type="number" class="fc" placeholder="350" />
            </div>
            <div class="fg">
              <label>Nombre de personnes</label>
              <input type="number" class="fc" placeholder="2" />
            </div>
          </div>
          <div class="fg">
            <label>Description <em>*</em></label>
            <textarea class="fc" placeholder="Décrivez la recette…"></textarea>
          </div>
          <div class="fg">
            <label>Ingrédients principaux</label>
            <input
              type="text"
              class="fc"
              placeholder="Ex : quinoa, avocat, tomates cerises…"
            />
          </div>
          <div class="fg-row">
            <div class="fg">
              <label>Statut de publication</label>
              <select class="fc">
                <option>Publié</option>
                <option>Brouillon</option>
                <option>En attente</option>
              </select>
            </div>
            <div class="fg">
              <label>Tags</label>
              <input
                type="text"
                class="fc"
                placeholder="vegan, rapide, sans gluten…"
              />
            </div>
          </div>
        </div>
        <div class="modal-foot">
          <button class="btn-cancel" onclick="closeModal('addRecetteModal')">
            Annuler
          </button>
          <button
            class="btn-save"
            onclick="saveAction('addRecetteModal','✅ Recette ajoutée avec succès !')"
          >
            ✓ Publier la recette
          </button>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════
     MODAL — ADD USER
══════════════════════════════════════════ -->
    <div
      class="modal-overlay"
      id="addUserModal"
      onclick="closeOut(event,'addUserModal')"
    >
      <div class="modal">
        <div class="modal-head">
          <div>
            <h2>👤 Ajouter un Utilisateur</h2>
            <p>Créer un nouveau compte utilisateur</p>
          </div>
          <button class="modal-x" onclick="closeModal('addUserModal')">
            ✕
          </button>
        </div>
        <div class="modal-content">
          <div class="fg-row">
            <div class="fg">
              <label>Prénom <em>*</em></label>
              <input type="text" class="fc" placeholder="Prénom" />
            </div>
            <div class="fg">
              <label>Nom <em>*</em></label>
              <input type="text" class="fc" placeholder="Nom de famille" />
            </div>
          </div>
          <div class="fg">
            <label>Adresse email <em>*</em></label>
            <input
              type="email"
              class="fc"
              placeholder="utilisateur@email.com"
            />
          </div>
          <div class="fg-row">
            <div class="fg">
              <label>Mot de passe <em>*</em></label>
              <input
                type="password"
                class="fc"
                placeholder="Minimum 8 caractères"
              />
            </div>
            <div class="fg">
              <label>Rôle</label>
              <select class="fc">
                <option>Utilisateur</option>
                <option>Administrateur</option>
              </select>
            </div>
          </div>
          <div class="fg-row">
            <div class="fg">
              <label>Âge</label>
              <input type="number" class="fc" placeholder="25" />
            </div>
            <div class="fg">
              <label>Objectif nutritionnel</label>
              <select class="fc">
                <option>-- Choisir --</option>
                <option>Perte de poids</option>
                <option>Prise de masse</option>
                <option>Alimentation équilibrée</option>
                <option>Végétarien / Vegan</option>
              </select>
            </div>
          </div>
          <div class="fg">
            <label>Statut du compte</label>
            <select class="fc">
              <option>Actif</option>
              <option>Inactif</option>
              <option>Banni</option>
            </select>
          </div>
        </div>
        <div class="modal-foot">
          <button class="btn-cancel" onclick="closeModal('addUserModal')">
            Annuler
          </button>
          <button
            class="btn-save"
            onclick="saveAction('addUserModal','✅ Utilisateur créé avec succès !')"
          >
            ✓ Créer l'utilisateur
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"></div>

    <script>
      /* ── Navigation ── */
      const pageMeta = {
        dashboard: {
          title: "Tableau de bord",
          sub: "Vue d'ensemble de la plateforme",
        },
        users: {
          title: "Gestion Utilisateurs",
          sub: "Gérez tous les comptes utilisateurs",
        },
        recettes: {
          title: "Gestion Recettes",
          sub: "Modérez et gérez les recettes publiées",
        },
        stats: { title: "Statistiques", sub: "Analyses et rapports détaillés" },
        profils: {
          title: "Profils Nutritionnels",
          sub: "Gérez les profils et objectifs des utilisateurs",
        },
        suivi: {
          title: "Suivi Alimentaire",
          sub: "Consultez les journaux alimentaires",
        },
        ia: {
          title: "IA & Recommandations",
          sub: "Paramètres du moteur de recommandation IA",
        },
        ingredients: {
          title: "Ingrédients",
          sub: "Base de données des ingrédients",
        },
        settings: {
          title: "Paramètres",
          sub: "Configuration de la plateforme",
        },
        reports: { title: "Rapports", sub: "Générez et exportez des rapports" },
      };

      function setPage(key, el) {
        document
          .querySelectorAll(".nav-item")
          .forEach((n) => n.classList.remove("active"));
        if (el) el.classList.add("active");
        const meta = pageMeta[key] || pageMeta.dashboard;
        document.getElementById("pageTitle").textContent = meta.title;
        document.getElementById("pageSub").textContent = meta.sub;
      }

      /* ── Modals ── */
      function openModal(type) {
        const id = type === "addRecette" ? "addRecetteModal" : "addUserModal";
        document.getElementById(id).classList.add("open");
        document.body.style.overflow = "hidden";
      }
      function closeModal(id) {
        document.getElementById(id).classList.remove("open");
        document.body.style.overflow = "";
      }
      function closeOut(e, id) {
        if (e.target === document.getElementById(id)) closeModal(id);
      }
      function saveAction(id, msg) {
        closeModal(id);
        toast(msg);
      }

      /* ── Toast ── */
      function toast(msg) {
        const t = document.getElementById("toast");
        t.textContent = msg;
        t.classList.add("show");
        setTimeout(() => t.classList.remove("show"), 3500);
      }

      /* ── Bar Chart ── */
      const months = [
        "Jan",
        "Fév",
        "Mar",
        "Avr",
        "Mai",
        "Jun",
        "Jul",
        "Aoû",
        "Sep",
        "Oct",
        "Nov",
        "Déc",
      ];
      const inscriptions = [
        42, 58, 71, 95, 88, 112, 130, 118, 145, 162, 178, 198,
      ];
      const recettes = [12, 18, 22, 31, 28, 38, 45, 42, 52, 58, 67, 72];
      const maxVal = Math.max(...inscriptions);

      const bc = document.getElementById("barChart");
      months.forEach((m, i) => {
        const g = document.createElement("div");
        g.className = "chart-bar-group";
        const bars = document.createElement("div");
        bars.className = "chart-bars";

        const b1 = document.createElement("div");
        b1.className = "cbar green";
        b1.style.height = Math.round((inscriptions[i] / maxVal) * 145) + "px";
        b1.title = `Inscriptions: ${inscriptions[i]}`;

        const b2 = document.createElement("div");
        b2.className = "cbar orange";
        b2.style.height = Math.round((recettes[i] / maxVal) * 145) + "px";
        b2.title = `Recettes: ${recettes[i]}`;

        bars.appendChild(b1);
        bars.appendChild(b2);
        const lbl = document.createElement("span");
        lbl.className = "chart-label";
        lbl.textContent = m;
        g.appendChild(bars);
        g.appendChild(lbl);
        bc.appendChild(g);
      });

      /* ── Donut Chart ── */
      const donutData = [
        { label: "Salades", val: 68, color: "#4a9e30" },
        { label: "Plats chauds", val: 54, color: "#f07c1b" },
        { label: "Smoothies", val: 42, color: "#7ec44f" },
        { label: "Soupes", val: 36, color: "#2d6a1f" },
        { label: "Autres", val: 40, color: "#ffa852" },
      ];
      const total = donutData.reduce((s, d) => s + d.val, 0);
      const cx = 75,
        cy = 75,
        r = 55,
        innerR = 35;
      let angle = -Math.PI / 2;
      const svgEl = document.getElementById("donutSvg");

      donutData.forEach((d) => {
        const sweep = (d.val / total) * 2 * Math.PI;
        const x1 = cx + r * Math.cos(angle),
          y1 = cy + r * Math.sin(angle);
        angle += sweep;
        const x2 = cx + r * Math.cos(angle),
          y2 = cy + r * Math.sin(angle);
        const lf = sweep > Math.PI ? 1 : 0;
        const ix1 = cx + innerR * Math.cos(angle - sweep),
          iy1 = cy + innerR * Math.sin(angle - sweep);
        const ix2 = cx + innerR * Math.cos(angle),
          iy2 = cy + innerR * Math.sin(angle);
        const path = document.createElementNS(
          "http://www.w3.org/2000/svg",
          "path"
        );
        path.setAttribute(
          "d",
          `M${x1} ${y1} A${r} ${r} 0 ${lf} 1 ${x2} ${y2} L${ix2} ${iy2} A${innerR} ${innerR} 0 ${lf} 0 ${ix1} ${iy1} Z`
        );
        path.setAttribute("fill", d.color);
        path.style.cursor = "pointer";
        path.style.transition = "opacity .2s";
        path.onmouseenter = () => {
          path.style.opacity = ".8";
        };
        path.onmouseleave = () => {
          path.style.opacity = "1";
        };
        svgEl.appendChild(path);
      });

      const legend = document.getElementById("donutLegend");
      donutData.forEach((d) => {
        const row = document.createElement("div");
        row.className = "dl-row";
        row.innerHTML = `<span class="dl-dot" style="background:${
          d.color
        }"></span><span class="dl-label">${
          d.label
        }</span><span class="dl-val">${
          d.val
        }</span><span class="dl-pct">${Math.round(
          (d.val / total) * 100
        )}%</span>`;
        legend.appendChild(row);
      });
    </script>
  </body>
</html>
