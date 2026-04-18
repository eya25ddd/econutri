<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EcoNutri – Accueil</title>
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
        --grey: #555;
        --white: #fff;
        --card-bg: #f9fdf6;
        --border: #d9eed0;
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
        background: var(--white);
        color: var(--black);
        overflow-x: hidden;
      }

      /* ═══════════════════════════════════════════════
       HEADER
    ══════════════════════════════════════════════ */
      header {
        background: linear-gradient(
          135deg,
          var(--green-dark) 0%,
          var(--green-main) 60%,
          var(--green-light) 100%
        );
        padding: 0 2.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 68px;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 4px 20px rgba(45, 106, 31, 0.35);
      }

      .logo {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        text-decoration: none;
      }
      .logo-icon {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        display: grid;
        place-items: center;
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, 0.3);
      }
      .logo-text {
        font-family: "Playfair Display", serif;
        font-size: 1.5rem;
        color: var(--white);
        letter-spacing: -0.5px;
      }
      .logo-text span {
        color: var(--orange);
      }

      nav {
        display: flex;
        align-items: center;
        gap: 2rem;
      }
      nav a {
        color: rgba(255, 255, 255, 0.88);
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: 500;
        letter-spacing: 0.3px;
        transition: color 0.2s;
      }
      nav a:hover {
        color: var(--white);
      }
      nav a.active {
        color: var(--white);
        border-bottom: 2px solid var(--orange);
        padding-bottom: 2px;
      }

      /* Nav dropdown for "Nos Recettes" */
      .nav-dropdown {
        position: relative;
      }
      .nav-dropdown .dropdown-toggle {
        color: rgba(255, 255, 255, 0.88);
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: 500;
        letter-spacing: 0.3px;
        cursor: pointer;
        background: transparent;
        border: none;
        padding: 0;
      }
      .nav-dropdown .dropdown-toggle:hover {
        color: var(--white);
      }
      .nav-dropdown .dropdown-menu {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        background: var(--white);
        color: var(--black);
        min-width: 160px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border: 1px solid var(--border);
        overflow: hidden;
        display: none;
        z-index: 150;
      }
      .nav-dropdown.open .dropdown-menu {
        display: block;
      }
      .nav-dropdown .dropdown-menu a {
        display: block;
        padding: 0.6rem 1rem;
        color: var(--green-dark);
        text-decoration: none;
        font-weight: 600;
      }
      .nav-dropdown .dropdown-menu a:hover {
        background: var(--card-bg);
      }

      .header-actions {
        display: flex;
        align-items: center;
        gap: 0.8rem;
      }

      .btn-login {
        background: rgba(255, 255, 255, 0.15);
        color: var(--white);
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        font-family: "DM Sans", sans-serif;
        transition: background 0.2s;
      }
      .btn-login:hover {
        background: rgba(255, 255, 255, 0.25);
      }

      .btn-register {
        background: var(--orange);
        color: var(--white);
        border: none;
        padding: 0.5rem 1.3rem;
        border-radius: 50px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        font-family: "DM Sans", sans-serif;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(240, 124, 27, 0.45);
      }

      /* ═══════════════════════════════════════════════
       HERO SECTION
    ══════════════════════════════════════════════ */
      .hero {
        min-height: 92vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        position: relative;
        overflow: hidden;
        background: linear-gradient(
          160deg,
          var(--green-pale) 0%,
          #d4edbc 55%,
          #f5fff0 100%
        );
      }

      /* Decorative blobs */
      .hero::before {
        content: "";
        position: absolute;
        right: -100px;
        top: -100px;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(
          circle,
          rgba(126, 196, 79, 0.2) 0%,
          transparent 70%
        );
        pointer-events: none;
      }
      .hero::after {
        content: "";
        position: absolute;
        left: -80px;
        bottom: -80px;
        width: 380px;
        height: 380px;
        border-radius: 50%;
        background: radial-gradient(
          circle,
          rgba(240, 124, 27, 0.08) 0%,
          transparent 70%
        );
        pointer-events: none;
      }

      .hero-left {
        padding: 4rem 3rem 4rem 5rem;
        position: relative;
        z-index: 1;
        animation: fadeUp 0.7s ease both;
      }

      .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--orange-light);
        color: var(--orange);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(240, 124, 27, 0.2);
      }

      .hero h1 {
        font-family: "Playfair Display", serif;
        font-size: 3.2rem;
        line-height: 1.12;
        color: var(--green-dark);
        margin-bottom: 1.2rem;
      }
      .hero h1 .highlight {
        color: var(--orange);
        position: relative;
      }
      .hero h1 .highlight::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -4px;
        width: 100%;
        height: 3px;
        background: var(--orange);
        border-radius: 2px;
        opacity: 0.4;
      }

      .hero-desc {
        font-size: 1.05rem;
        color: var(--grey);
        line-height: 1.7;
        max-width: 460px;
        margin-bottom: 2rem;
      }

      .hero-cta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 3rem;
      }

      .cta-primary {
        background: linear-gradient(
          135deg,
          var(--green-main),
          var(--green-dark)
        );
        color: var(--white);
        border: none;
        padding: 0.85rem 2rem;
        border-radius: 50px;
        font-family: "DM Sans", sans-serif;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
      }
      .cta-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(45, 106, 31, 0.35);
      }

      .cta-secondary {
        background: transparent;
        color: var(--green-dark);
        border: 2px solid var(--green-main);
        padding: 0.85rem 2rem;
        border-radius: 50px;
        font-family: "DM Sans", sans-serif;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        text-decoration: none;
      }
      .cta-secondary:hover {
        background: var(--green-pale);
        transform: translateY(-2px);
      }

      .hero-trust {
        display: flex;
        align-items: center;
        gap: 1.5rem;
      }
      .trust-avatars {
        display: flex;
      }
      .trust-avatars .av {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2.5px solid var(--white);
        background: var(--green-light);
        display: grid;
        place-items: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--white);
        margin-left: -10px;
      }
      .trust-avatars .av:first-child {
        margin-left: 0;
      }
      .av.a1 {
        background: var(--green-dark);
      }
      .av.a2 {
        background: var(--green-main);
      }
      .av.a3 {
        background: var(--orange);
      }
      .av.a4 {
        background: #c0532e;
      }

      .trust-text {
        font-size: 0.82rem;
        color: var(--grey);
        line-height: 1.4;
      }
      .trust-text strong {
        color: var(--green-dark);
      }

      /* Hero right - visual cards */
      .hero-right {
        position: relative;
        padding: 3rem 4rem 3rem 2rem;
        display: grid;
        place-items: center;
        z-index: 1;
        animation: fadeUp 0.7s 0.2s ease both;
      }

      .hero-cards-stack {
        position: relative;
        width: 340px;
        height: 400px;
      }

      .hcard {
        position: absolute;
        background: var(--white);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(45, 106, 31, 0.15);
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.3s;
      }
      .hcard:hover {
        transform: scale(1.02);
      }

      .hcard-main {
        width: 300px;
        top: 40px;
        left: 20px;
        z-index: 3;
      }
      .hcard-main img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        display: block;
      }
      .hcard-body {
        padding: 1rem 1.1rem 1.2rem;
      }
      .hcard-tag {
        background: var(--orange);
        color: var(--white);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        display: inline-block;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
      }
      .hcard-title {
        font-family: "Playfair Display", serif;
        font-size: 0.98rem;
        color: var(--black);
        margin-bottom: 0.3rem;
      }
      .hcard-meta {
        font-size: 0.78rem;
        color: var(--grey);
        display: flex;
        gap: 0.8rem;
      }

      .hcard-back1 {
        width: 260px;
        top: 10px;
        left: 60px;
        z-index: 2;
        transform: rotate(4deg);
        height: 80px;
        background: linear-gradient(135deg, var(--green-pale), #d4edbc);
      }
      .hcard-back2 {
        width: 240px;
        top: 0;
        left: 80px;
        z-index: 1;
        transform: rotate(-3deg);
        height: 70px;
        background: var(--orange-light);
      }

      /* Floating widgets */
      .float-widget {
        position: absolute;
        background: var(--white);
        border-radius: 14px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
        padding: 0.7rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.82rem;
        border: 1px solid var(--border);
        animation: float 3s ease-in-out infinite;
      }
      .float-widget.w1 {
        top: 10px;
        right: 20px;
        animation-delay: 0s;
      }
      .float-widget.w2 {
        bottom: 60px;
        left: 0;
        animation-delay: 1.5s;
      }
      .float-widget .wi {
        font-size: 1.4rem;
      }
      .float-widget strong {
        display: block;
        font-size: 0.85rem;
        color: var(--green-dark);
      }
      .float-widget span {
        color: var(--grey);
      }

      @keyframes float {
        0%,
        100% {
          transform: translateY(0);
        }
        50% {
          transform: translateY(-8px);
        }
      }

      /* ═══════════════════════════════════════════════
       STATS STRIP
    ══════════════════════════════════════════════ */
      .stats-strip {
        background: linear-gradient(
          135deg,
          var(--green-dark),
          var(--green-main)
        );
        padding: 2rem 5rem;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        text-align: center;
      }
      .strip-stat strong {
        display: block;
        font-family: "Playfair Display", serif;
        font-size: 2rem;
        color: var(--white);
      }
      .strip-stat span {
        font-size: 0.83rem;
        color: rgba(255, 255, 255, 0.72);
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .strip-stat .icon {
        font-size: 1.5rem;
        margin-bottom: 0.3rem;
      }

      /* ═══════════════════════════════════════════════
       FEATURES SECTION
    ══════════════════════════════════════════════ */
      .section {
        padding: 5rem 5rem;
      }
      .section-center {
        text-align: center;
      }

      .section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--green-pale);
        color: var(--green-dark);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.35rem 0.9rem;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 1rem;
      }

      .section-title {
        font-family: "Playfair Display", serif;
        font-size: 2.2rem;
        color: var(--black);
        margin-bottom: 0.8rem;
        line-height: 1.2;
      }
      .section-title span {
        color: var(--orange);
      }

      .section-sub {
        font-size: 1rem;
        color: var(--grey);
        max-width: 520px;
        margin: 0 auto 3rem;
        line-height: 1.65;
      }

      /* Feature cards */
      .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.6rem;
      }

      .feature-card {
        background: var(--card-bg);
        border: 1.5px solid var(--border);
        border-radius: 20px;
        padding: 2rem;
        transition: transform 0.25s, box-shadow 0.25s;
        position: relative;
        overflow: hidden;
      }
      .feature-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(
          90deg,
          var(--green-main),
          var(--green-light)
        );
        opacity: 0;
        transition: opacity 0.3s;
      }
      .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(45, 106, 31, 0.14);
      }
      .feature-card:hover::before {
        opacity: 1;
      }

      .feature-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.6rem;
        margin-bottom: 1.2rem;
      }
      .fi-green {
        background: var(--green-pale);
      }
      .fi-orange {
        background: var(--orange-light);
      }

      .feature-card h3 {
        font-family: "Playfair Display", serif;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--black);
      }
      .feature-card p {
        font-size: 0.88rem;
        color: var(--grey);
        line-height: 1.6;
      }

      /* ═══════════════════════════════════════════════
       HOW IT WORKS
    ══════════════════════════════════════════════ */
      .how-section {
        background: linear-gradient(160deg, var(--green-pale) 0%, #f0f9e8 100%);
        padding: 5rem;
      }

      .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        position: relative;
        margin-top: 1rem;
      }
      .steps-grid::before {
        content: "";
        position: absolute;
        top: 36px;
        left: 12%;
        right: 12%;
        height: 2px;
        background: linear-gradient(90deg, var(--green-light), var(--orange));
        z-index: 0;
      }

      .step {
        text-align: center;
        position: relative;
        z-index: 1;
      }
      .step-num {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--white);
        border: 3px solid var(--green-main);
        display: grid;
        place-items: center;
        font-family: "Playfair Display", serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--green-dark);
        margin: 0 auto 1rem;
        position: relative;
        box-shadow: 0 4px 20px rgba(45, 106, 31, 0.15);
      }
      .step-num .step-icon {
        font-size: 1.6rem;
      }
      .step h4 {
        font-family: "Playfair Display", serif;
        font-size: 1rem;
        color: var(--green-dark);
        margin-bottom: 0.4rem;
      }
      .step p {
        font-size: 0.82rem;
        color: var(--grey);
        line-height: 1.5;
      }

      /* ═══════════════════════════════════════════════
       AI SPOTLIGHT
    ══════════════════════════════════════════════ */
      .ai-section {
        padding: 5rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
      }

      .ai-visual {
        background: linear-gradient(135deg, var(--green-dark), #1a4010);
        border-radius: 24px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        min-height: 380px;
        display: flex;
        flex-direction: column;
        justify-content: center;
      }
      .ai-visual::before {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(126, 196, 79, 0.15);
      }

      .ai-chip {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.78rem;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        display: inline-block;
        margin-bottom: 1.2rem;
        backdrop-filter: blur(6px);
      }

      .ai-title {
        font-family: "Playfair Display", serif;
        font-size: 1.6rem;
        color: var(--white);
        margin-bottom: 0.8rem;
        line-height: 1.3;
      }

      .ai-rec-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
        margin-top: 1.2rem;
      }
      .ai-rec-list li {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 0.7rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(4px);
      }
      .ai-rec-list li .rec-icon {
        font-size: 1.3rem;
      }
      .ai-rec-list li .rec-match {
        margin-left: auto;
        background: var(--orange);
        color: var(--white);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.18rem 0.55rem;
        border-radius: 50px;
      }

      .ai-text .section-kicker {
        display: inline-flex;
      }
      .ai-text .section-title {
        text-align: left;
      }
      .ai-text .section-sub {
        text-align: left;
        margin: 0 0 1.5rem;
      }

      .ai-benefits {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
      }
      .benefit-row {
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
      }
      .benefit-dot {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--green-pale);
        display: grid;
        place-items: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        margin-top: 2px;
      }
      .benefit-row p {
        font-size: 0.88rem;
        color: var(--grey);
        line-height: 1.55;
      }
      .benefit-row p strong {
        color: var(--green-dark);
      }

      /* ═══════════════════════════════════════════════
       RECIPE PREVIEW
    ══════════════════════════════════════════════ */
      .recipes-section {
        background: #f2f8ee;
        padding: 5rem;
      }

      .recipes-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 2.2rem;
      }

      .see-all {
        font-size: 0.9rem;
        color: var(--green-main);
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        transition: gap 0.2s;
      }
      .see-all:hover {
        gap: 0.6rem;
      }

      .recipes-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.6rem;
      }

      .rcard {
        background: var(--white);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border);
        transition: transform 0.25s, box-shadow 0.25s;
      }
      .rcard:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 36px rgba(45, 106, 31, 0.16);
      }
      .rcard-img {
        position: relative;
        height: 190px;
        overflow: hidden;
      }
      .rcard-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s;
      }
      .rcard:hover .rcard-img img {
        transform: scale(1.06);
      }
      .rcard-cat {
        position: absolute;
        top: 0.8rem;
        left: 0.8rem;
        background: var(--orange);
        color: var(--white);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.7rem;
        border-radius: 50px;
        text-transform: uppercase;
      }
      .rcard-body {
        padding: 1.1rem 1.2rem 1.3rem;
      }
      .rcard-title {
        font-family: "Playfair Display", serif;
        font-size: 1.05rem;
        margin-bottom: 0.65rem;
        color: var(--black);
      }
      .rcard-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.78rem;
        color: var(--grey);
      }
      .rcard-time {
        display: flex;
        align-items: center;
        gap: 0.3rem;
      }
      .rcard-rating {
        color: var(--orange);
        font-weight: 700;
      }

      /* ═══════════════════════════════════════════════
       ECO COMMITMENT
    ══════════════════════════════════════════════ */
      .eco-section {
        padding: 5rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
      }

      .eco-text .section-title {
        text-align: left;
      }
      .eco-text .section-sub {
        text-align: left;
        margin: 0 0 2rem;
      }

      .eco-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
      }
      .eco-list li {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        font-size: 0.92rem;
        color: var(--black);
      }
      .eco-check {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--green-pale);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        color: var(--green-main);
        font-size: 1rem;
        font-weight: 700;
      }

      .eco-visual {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
      }
      .eco-card {
        background: var(--card-bg);
        border: 1.5px solid var(--border);
        border-radius: 18px;
        padding: 1.5rem;
        text-align: center;
      }
      .eco-card:first-child {
        grid-column: span 2;
        background: linear-gradient(135deg, var(--green-pale), #d4edbc);
      }
      .eco-card .eco-num {
        font-family: "Playfair Display", serif;
        font-size: 2rem;
        color: var(--green-dark);
      }
      .eco-card .eco-label {
        font-size: 0.8rem;
        color: var(--grey);
        margin-top: 0.2rem;
      }

      /* ═══════════════════════════════════════════════
       TESTIMONIALS
    ══════════════════════════════════════════════ */
      .testi-section {
        background: linear-gradient(160deg, var(--green-pale), #f0f9e8);
        padding: 5rem;
      }

      .testi-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 1rem;
      }

      .testi-card {
        background: var(--white);
        border-radius: 20px;
        padding: 1.8rem;
        border: 1px solid var(--border);
        position: relative;
      }
      .testi-card::before {
        content: '"';
        position: absolute;
        top: 1rem;
        right: 1.5rem;
        font-family: "Playfair Display", serif;
        font-size: 4rem;
        color: var(--green-pale);
        line-height: 1;
      }
      .testi-stars {
        color: var(--orange);
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
        letter-spacing: 0.1rem;
      }
      .testi-text {
        font-size: 0.88rem;
        color: var(--grey);
        line-height: 1.65;
        margin-bottom: 1.2rem;
        font-style: italic;
      }
      .testi-author {
        display: flex;
        align-items: center;
        gap: 0.7rem;
      }
      .tav {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--white);
        flex-shrink: 0;
      }
      .tav.g {
        background: var(--green-dark);
      }
      .tav.o {
        background: var(--orange);
      }
      .tav.l {
        background: var(--green-light);
      }
      .testi-name {
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--black);
      }
      .testi-role {
        font-size: 0.76rem;
        color: var(--grey);
      }

      /* ═══════════════════════════════════════════════
       CTA BANNER
    ══════════════════════════════════════════════ */
      .cta-banner {
        background: linear-gradient(
          135deg,
          var(--green-dark) 0%,
          var(--green-main) 100%
        );
        padding: 4rem 5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
      }
      .cta-banner::before {
        content: "";
        position: absolute;
        top: -80px;
        left: -80px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
      }
      .cta-banner::after {
        content: "";
        position: absolute;
        bottom: -60px;
        right: -60px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(240, 124, 27, 0.1);
      }
      .cta-banner h2 {
        font-family: "Playfair Display", serif;
        font-size: 2.4rem;
        color: var(--white);
        margin-bottom: 0.8rem;
      }
      .cta-banner p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        margin-bottom: 2rem;
      }
      .cta-banner-btns {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
      }

      .banner-btn-primary {
        background: var(--orange);
        color: var(--white);
        border: none;
        padding: 0.85rem 2.2rem;
        border-radius: 50px;
        font-family: "DM Sans", sans-serif;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .banner-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(240, 124, 27, 0.4);
      }

      .banner-btn-secondary {
        background: rgba(255, 255, 255, 0.12);
        color: var(--white);
        border: 2px solid rgba(255, 255, 255, 0.4);
        padding: 0.85rem 2.2rem;
        border-radius: 50px;
        font-family: "DM Sans", sans-serif;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
      }
      .banner-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.22);
      }

      /* ═══════════════════════════════════════════════
       FOOTER
    ══════════════════════════════════════════════ */
      footer {
        background: #0e2a08;
        color: rgba(255, 255, 255, 0.65);
        padding: 3rem 5rem 2rem;
      }
      .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 2.5rem;
        padding-bottom: 2.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }
      .footer-brand .logo-text {
        color: var(--white);
        font-size: 1.3rem;
      }
      .footer-brand p {
        font-size: 0.83rem;
        line-height: 1.6;
        margin-top: 0.7rem;
      }
      .footer-tagline {
        color: var(--orange);
        font-size: 0.78rem;
        font-style: italic;
        margin-top: 0.4rem;
      }
      .footer-col h4 {
        color: var(--white);
        font-size: 0.88rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .footer-col ul {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
      .footer-col ul li a {
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        font-size: 0.83rem;
        transition: color 0.2s;
      }
      .footer-col ul li a:hover {
        color: var(--green-light);
      }
      .footer-bottom {
        padding-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
      }
      .footer-bottom span {
        color: var(--orange);
      }

      /* ═══════════════════════════════════════════════
       MODALS : LOGIN + REGISTER
    ══════════════════════════════════════════════ */
      .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
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
        border-radius: 24px;
        width: min(480px, 94vw);
        transform: translateY(30px) scale(0.97);
        transition: transform 0.3s;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.25);
        overflow: hidden;
      }
      .modal-overlay.open .modal {
        transform: translateY(0) scale(1);
      }

      .modal-header {
        background: linear-gradient(
          135deg,
          var(--green-dark),
          var(--green-main)
        );
        padding: 1.8rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .modal-header-left {
        display: flex;
        align-items: center;
        gap: 0.8rem;
      }
      .modal-header-left .mh-icon {
        font-size: 1.6rem;
        background: rgba(255, 255, 255, 0.15);
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
      }
      .modal-header h2 {
        font-family: "Playfair Display", serif;
        font-size: 1.2rem;
        color: var(--white);
      }
      .modal-header p {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.75);
        margin-top: 0.2rem;
      }
      .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: var(--white);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: grid;
        place-items: center;
        font-size: 1.1rem;
        transition: background 0.2s;
      }
      .modal-close:hover {
        background: rgba(255, 255, 255, 0.35);
      }

      .modal-body {
        padding: 2rem;
      }

      .form-group {
        margin-bottom: 1.2rem;
      }
      .form-group label {
        display: block;
        font-size: 0.83rem;
        font-weight: 600;
        color: var(--green-dark);
        margin-bottom: 0.4rem;
      }
      .form-group label span {
        color: var(--orange);
      }
      .form-control {
        width: 100%;
        padding: 0.7rem 0.95rem;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-family: "DM Sans", sans-serif;
        font-size: 0.9rem;
        color: var(--black);
        outline: none;
        transition: border-color 0.2s;
        background: var(--card-bg);
      }
      .form-control:focus {
        border-color: var(--green-main);
        background: var(--white);
      }

      .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
      }

      .forgot {
        text-align: right;
        margin-top: -0.7rem;
        margin-bottom: 0.8rem;
      }
      .forgot a {
        font-size: 0.8rem;
        color: var(--green-main);
        text-decoration: none;
      }

      .divider {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin: 1.2rem 0;
        font-size: 0.78rem;
        color: var(--grey);
      }
      .divider::before,
      .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--border);
      }

      .btn-submit-full {
        width: 100%;
        padding: 0.8rem;
        border: none;
        border-radius: 12px;
        background: linear-gradient(
          135deg,
          var(--green-main),
          var(--green-dark)
        );
        color: var(--white);
        font-family: "DM Sans", sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .btn-submit-full:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(45, 106, 31, 0.35);
      }

      .modal-switch {
        text-align: center;
        margin-top: 1.2rem;
        font-size: 0.83rem;
        color: var(--grey);
      }
      .modal-switch a {
        color: var(--orange);
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
      }

      /* nutrition tags (register) */
      .nutrition-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
      }
      .ntag {
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        border: 1.5px solid var(--border);
        font-size: 0.78rem;
        cursor: pointer;
        transition: all 0.2s;
        font-family: "DM Sans", sans-serif;
        color: var(--grey);
        background: var(--card-bg);
      }
      .ntag.selected {
        background: var(--green-pale);
        border-color: var(--green-main);
        color: var(--green-dark);
        font-weight: 600;
      }

      /* Toast */
      .toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: var(--green-dark);
        color: var(--white);
        padding: 1rem 1.5rem;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 500;
        z-index: 300;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.25);
        transform: translateY(80px);
        opacity: 0;
        transition: transform 0.35s, opacity 0.35s;
        display: flex;
        align-items: center;
        gap: 0.6rem;
      }
      .toast.show {
        transform: translateY(0);
        opacity: 1;
      }

      /* ═══════════════════════════════════════════════
       ANIMATIONS
    ══════════════════════════════════════════════ */
      @keyframes fadeUp {
        from {
          opacity: 0;
          transform: translateY(24px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      .feature-card {
        animation: fadeUp 0.5s ease both;
      }
      .feature-card:nth-child(1) {
        animation-delay: 0.05s;
      }
      .feature-card:nth-child(2) {
        animation-delay: 0.13s;
      }
      .feature-card:nth-child(3) {
        animation-delay: 0.21s;
      }
      .feature-card:nth-child(4) {
        animation-delay: 0.29s;
      }
      .feature-card:nth-child(5) {
        animation-delay: 0.37s;
      }
      .feature-card:nth-child(6) {
        animation-delay: 0.45s;
      }

      /* ═══════════════════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════════════════ */
      @media (max-width: 1024px) {
        .hero {
          grid-template-columns: 1fr;
          min-height: auto;
        }
        .hero-right {
          display: none;
        }
        .hero-left {
          padding: 4rem 3rem;
        }
        .features-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        .steps-grid {
          grid-template-columns: repeat(2, 1fr);
        }
        .steps-grid::before {
          display: none;
        }
        .ai-section,
        .eco-section {
          grid-template-columns: 1fr;
        }
        .testi-grid {
          grid-template-columns: 1fr 1fr;
        }
        .footer-grid {
          grid-template-columns: 1fr 1fr;
        }
        .recipes-row {
          grid-template-columns: 1fr 1fr;
        }
      }

      @media (max-width: 680px) {
        .hero-left {
          padding: 3rem 1.5rem;
        }
        .hero h1 {
          font-size: 2.2rem;
        }
        .section,
        .how-section,
        .ai-section,
        .eco-section,
        .testi-section,
        .cta-banner,
        .recipes-section {
          padding: 3rem 1.5rem;
        }
        .stats-strip {
          grid-template-columns: repeat(2, 1fr);
          padding: 2rem 1.5rem;
        }
        .features-grid,
        .steps-grid,
        .testi-grid,
        .recipes-row {
          grid-template-columns: 1fr;
        }
        .footer-grid {
          grid-template-columns: 1fr;
          padding: 0;
        }
        footer {
          padding: 2rem 1.5rem;
        }
        header {
          padding: 0 1rem;
        }
        nav {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <!-- ─── HEADER ─────────────────────────────────── -->
    <header>
      <a class="logo" href="#">
        <div class="logo-icon">
          <svg
            viewBox="0 0 32 32"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            width="26"
            height="26"
          >
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
      </a>

      <nav>
        <a href="#" class="active">Accueil</a>
        <div class="nav-dropdown" id="nav-recettes">
          <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleRecetteDropdown(event)">Nos Recettes ▾</a>
          <div class="dropdown-menu" id="recette-menu" role="menu" aria-label="Nos Recettes">
            <a href="listAliment.php">Aliments</a>
            <a href="listRecette.php">Recettes</a>
          </div>
        </div>
        <a href="#fonctionnalites">Fonctionnalités</a>
        <a href="#comment">Comment ça marche</a>
        <a href="#contact">Contact</a>
      </nav>

      <div class="header-actions">
        <button class="btn-login" onclick="openModal('login')">
          Se connecter
        </button>
        <button class="btn-register" onclick="openModal('register')">
          S'inscrire
        </button>
      </div>
    </header>

    <!-- ─── HERO ────────────────────────────────────── -->
    <section class="hero">
      <div class="hero-left">
        <div class="hero-badge">🌿 Alimentation saine &amp; durable</div>
        <h1>
          Mangez <span class="highlight">Mieux</span>,<br />Vivez Durablement
        </h1>
        <p class="hero-desc">
          EcoNutri vous aide à adopter une alimentation saine grâce à des
          recommandations personnalisées par IA, tout en réduisant le gaspillage
          alimentaire pour un impact positif sur la planète.
        </p>

        <div class="hero-cta">
          <a href="#" class="cta-primary" onclick="openModal('register')">
            Commencer gratuitement
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <line x1="5" y1="12" x2="19" y2="12" />
              <polyline points="12 5 19 12 12 19" />
            </svg>
          </a>
          <a href="listRecette.php" class="cta-secondary">
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="12" cy="12" r="10" />
              <polygon points="10 8 16 12 10 16 10 8" />
            </svg>
            Voir les recettes
          </a>
        </div>

        <div class="hero-trust">
          <div class="trust-avatars">
            <div class="av a1">SM</div>
            <div class="av a2">AK</div>
            <div class="av a3">LB</div>
            <div class="av a4">YT</div>
          </div>
          <div class="trust-text">
            <strong>+1 200 utilisateurs</strong><br />
            nous font déjà confiance
          </div>
        </div>
      </div>

      <div class="hero-right">
        <div class="hero-cards-stack">
          <div class="hcard hcard-back2"></div>
          <div class="hcard hcard-back1"></div>
          <div class="hcard hcard-main">
            <img
              src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&q=80"
              alt="Recette saine"
            />
            <div class="hcard-body">
              <span class="hcard-tag">🤖 Recommandé par IA</span>
              <h4 class="hcard-title">Bowl Méditerranéen &amp; Quinoa</h4>
              <div class="hcard-meta">
                <span>⏱ 20 min</span>
                <span>🔥 380 kcal</span>
                <span>⭐ 4.9</span>
              </div>
            </div>
          </div>
        </div>

        <div class="float-widget w1">
          <span class="wi">🎯</span>
          <div>
            <strong>Profil nutritionnel</strong>
            <span>Personnalisé pour vous</span>
          </div>
        </div>
        <div class="float-widget w2">
          <span class="wi">♻️</span>
          <div>
            <strong>-30% de gaspillage</strong>
            <span>Ce mois-ci</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ─── STATS STRIP ────────────────────────────── -->
    <div class="stats-strip">
      <div class="strip-stat">
        <div class="icon">🥗</div>
        <strong>240+</strong>
        <span>Recettes saines</span>
      </div>
      <div class="strip-stat">
        <div class="icon">👥</div>
        <strong>1 200+</strong>
        <span>Utilisateurs actifs</span>
      </div>
      <div class="strip-stat">
        <div class="icon">🤖</div>
        <strong>98%</strong>
        <span>Précision IA</span>
      </div>
      <div class="strip-stat">
        <div class="icon">♻️</div>
        <strong>-35%</strong>
        <span>Gaspillage réduit</span>
      </div>
    </div>

    <!-- ─── FEATURES ──────────────────────────────── -->
    <section class="section section-center" id="fonctionnalites">
      <div class="section-kicker">✨ Fonctionnalités</div>
      <h2 class="section-title">Tout ce dont vous avez <span>besoin</span></h2>
      <p class="section-sub">
        Une plateforme complète pour transformer votre façon de manger, de
        cuisiner et de gérer vos aliments.
      </p>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon fi-green">👤</div>
          <h3>Inscription &amp; Profil</h3>
          <p>
            Créez votre compte en quelques secondes et configurez votre profil
            nutritionnel selon vos objectifs, allergies et préférences
            alimentaires.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-orange">🎯</div>
          <h3>Profil Nutritionnel</h3>
          <p>
            Renseignez votre âge, poids, activité physique et objectifs pour
            recevoir des recommandations parfaitement adaptées à votre
            métabolisme.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-green">🤖</div>
          <h3>Recommandations IA</h3>
          <p>
            Notre intelligence artificielle analyse votre profil et vous propose
            des repas personnalisés, optimisés pour votre santé et vos goûts.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-orange">📊</div>
          <h3>Suivi Alimentaire</h3>
          <p>
            Suivez votre consommation quotidienne, visualisez vos apports
            nutritionnels et progressez vers vos objectifs santé.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-green">🍽️</div>
          <h3>Partage de Recettes</h3>
          <p>
            Découvrez des centaines de recettes partagées par la communauté, et
            publiez les vôtres pour inspirer d'autres utilisateurs.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon fi-orange">♻️</div>
          <h3>Anti-Gaspillage</h3>
          <p>
            Entrez les ingrédients disponibles dans votre frigo et recevez des
            suggestions de recettes pour ne rien jeter.
          </p>
        </div>
      </div>
    </section>

    <!-- ─── HOW IT WORKS ───────────────────────────── -->
    <section class="how-section section-center" id="comment">
      <div class="section-kicker">🚀 Comment ça marche</div>
      <h2 class="section-title">Démarrez en <span>4 étapes</span></h2>
      <p class="section-sub">
        Simple, rapide et efficace. Rejoignez EcoNutri et transformez votre
        alimentation dès aujourd'hui.
      </p>

      <div class="steps-grid">
        <div class="step">
          <div class="step-num"><span class="step-icon">📝</span></div>
          <h4>Inscription</h4>
          <p>Créez votre compte gratuitement en moins de 2 minutes.</p>
        </div>
        <div class="step">
          <div class="step-num"><span class="step-icon">👤</span></div>
          <h4>Profil Nutritionnel</h4>
          <p>
            Complétez votre profil avec vos objectifs et préférences
            alimentaires.
          </p>
        </div>
        <div class="step">
          <div class="step-num"><span class="step-icon">🤖</span></div>
          <h4>L'IA Analyse</h4>
          <p>
            Notre IA traite vos données et génère des recommandations
            sur-mesure.
          </p>
        </div>
        <div class="step">
          <div class="step-num"><span class="step-icon">🥗</span></div>
          <h4>Cuisinez &amp; Progressez</h4>
          <p>Suivez vos repas, suivez vos progrès, vivez mieux.</p>
        </div>
      </div>
    </section>

    <!-- ─── AI SECTION ────────────────────────────── -->
    <section class="ai-section">
      <div class="ai-visual">
        <div class="ai-chip">🤖 Intelligence Artificielle</div>
        <h3 class="ai-title">
          Votre assistant nutrition<br />personnel, 24h/24
        </h3>
        <ul class="ai-rec-list">
          <li>
            <span class="rec-icon">🥗</span>
            Bowl Quinoa &amp; Légumes Rôtis
            <span class="rec-match">98% match</span>
          </li>
          <li>
            <span class="rec-icon">🍲</span>
            Soupe de Lentilles aux Épices
            <span class="rec-match">94% match</span>
          </li>
          <li>
            <span class="rec-icon">🥤</span>
            Smoothie Vert Épinards &amp; Kiwi
            <span class="rec-match">91% match</span>
          </li>
          <li>
            <span class="rec-icon">🍗</span>
            Poulet Grillé au Citron &amp; Thym
            <span class="rec-match">89% match</span>
          </li>
        </ul>
      </div>

      <div class="ai-text">
        <div class="section-kicker">🧠 IA &amp; Personnalisation</div>
        <h2 class="section-title">
          Des recommandations<br /><span>vraiment</span> personnalisées
        </h2>
        <p class="section-sub">
          Notre système d'IA apprend de vos habitudes pour vous proposer les
          meilleures recettes selon votre profil nutritionnel et les ingrédients
          que vous avez à disposition.
        </p>

        <div class="ai-benefits">
          <div class="benefit-row">
            <div class="benefit-dot">🎯</div>
            <p>
              <strong>Recommandations sur-mesure</strong> — L'IA analyse votre
              historique alimentaire, vos carences et vos préférences pour une
              expérience unique.
            </p>
          </div>
          <div class="benefit-row">
            <div class="benefit-dot">🥕</div>
            <p>
              <strong>Suggestions selon vos ingrédients</strong> — Entrez ce que
              vous avez dans votre frigo et recevez des idées de recettes
              anti-gaspillage.
            </p>
          </div>
          <div class="benefit-row">
            <div class="benefit-dot">📈</div>
            <p>
              <strong>Prédiction du gaspillage</strong> — L'IA prédit quels
              aliments risquent d'expirer et vous suggère comment les utiliser.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ─── RECIPE PREVIEW ────────────────────────── -->
    <section class="recipes-section">
      <div class="recipes-header">
        <div>
          <div class="section-kicker" style="display: inline-flex">
            🍽️ Nos Recettes
          </div>
          <h2 class="section-title" style="margin-top: 0.5rem">
            Recettes <span>populaires</span>
          </h2>
        </div>
        <a href="index.html" class="see-all">Voir toutes les recettes →</a>
      </div>

      <div class="recipes-row">
        <div class="rcard">
          <div class="rcard-img">
            <img
              src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&q=80"
              alt="Salade Quinoa"
            />
            <span class="rcard-cat">Salade</span>
          </div>
          <div class="rcard-body">
            <h3 class="rcard-title">Salade Quinoa &amp; Avocat</h3>
            <div class="rcard-meta">
              <span class="rcard-time">⏱ 15 min · 320 kcal</span>
              <span class="rcard-rating">★ 4.8</span>
            </div>
          </div>
        </div>

        <div class="rcard">
          <div class="rcard-img">
            <img
              src="https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=600&q=80"
              alt="Poulet Citron"
            />
            <span class="rcard-cat">Plat chaud</span>
          </div>
          <div class="rcard-body">
            <h3 class="rcard-title">Poulet au Citron &amp; Herbes</h3>
            <div class="rcard-meta">
              <span class="rcard-time">⏱ 30 min · 420 kcal</span>
              <span class="rcard-rating">★ 4.9</span>
            </div>
          </div>
        </div>

        <div class="rcard">
          <div class="rcard-img">
            <img
              src="https://images.unsplash.com/photo-1547592180-85f173990554?w=600&q=80"
              alt="Soupe Lentilles"
            />
            <span class="rcard-cat">Soupe</span>
          </div>
          <div class="rcard-body">
            <h3 class="rcard-title">Soupe de Lentilles aux Épices</h3>
            <div class="rcard-meta">
              <span class="rcard-time">⏱ 25 min · 290 kcal</span>
              <span class="rcard-rating">★ 4.6</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ─── ECO COMMITMENT ────────────────────────── -->
    <section class="eco-section">
      <div class="eco-text">
        <div class="section-kicker">🌍 Développement Durable</div>
        <h2 class="section-title">
          Manger <span>responsable</span>,<br />agir pour la planète
        </h2>
        <p class="section-sub">
          EcoNutri intègre les principes du développement durable à chaque
          recommandation pour réduire votre empreinte carbone alimentaire.
        </p>

        <ul class="eco-list">
          <li>
            <span class="eco-check">✓</span>Réduction du gaspillage alimentaire
            grâce à l'IA
          </li>
          <li>
            <span class="eco-check">✓</span>Mise en avant des produits locaux et
            de saison
          </li>
          <li>
            <span class="eco-check">✓</span>Recettes à faible empreinte carbone
            priorisées
          </li>
          <li>
            <span class="eco-check">✓</span>Suivi de votre impact
            environnemental personnel
          </li>
          <li>
            <span class="eco-check">✓</span>Communauté engagée pour une
            alimentation durable
          </li>
        </ul>
      </div>

      <div class="eco-visual">
        <div class="eco-card">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem">🌱</div>
          <div class="eco-num">35%</div>
          <div class="eco-label">
            de gaspillage alimentaire réduit en moyenne par nos utilisateurs
          </div>
        </div>
        <div class="eco-card">
          <div style="font-size: 1.8rem; margin-bottom: 0.4rem">♻️</div>
          <div class="eco-num">12kg</div>
          <div class="eco-label">d'aliments sauvés par mois</div>
        </div>
        <div class="eco-card">
          <div style="font-size: 1.8rem; margin-bottom: 0.4rem">🌿</div>
          <div class="eco-num">-28%</div>
          <div class="eco-label">d'empreinte carbone alimentaire</div>
        </div>
      </div>
    </section>

    <!-- ─── TESTIMONIALS ──────────────────────────── -->
    <section class="testi-section section-center">
      <div class="section-kicker">💬 Témoignages</div>
      <h2 class="section-title">Ils nous font <span>confiance</span></h2>
      <p class="section-sub">
        Découvrez ce que nos utilisateurs disent d'EcoNutri.
      </p>

      <div class="testi-grid">
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text">
            EcoNutri a complètement transformé mon rapport à la nourriture. Les
            recommandations IA sont bluffantes de précision ! Je mange mieux et
            je gaspille beaucoup moins.
          </p>
          <div class="testi-author">
            <div class="tav g">SM</div>
            <div>
              <div class="testi-name">Samira M.</div>
              <div class="testi-role">Utilisatrice depuis 6 mois</div>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text">
            Grâce au suivi alimentaire, j'ai atteint mes objectifs nutritionnels
            en 3 mois. L'interface est belle et très intuitive. Je recommande
            vivement !
          </p>
          <div class="testi-author">
            <div class="tav o">AK</div>
            <div>
              <div class="testi-name">Ahmed K.</div>
              <div class="testi-role">Sportif amateur</div>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★☆</div>
          <p class="testi-text">
            La fonctionnalité anti-gaspillage est géniale ! J'entre mes
            ingrédients et je reçois des idées de recettes créatives. Fini les
            aliments jetés à la poubelle.
          </p>
          <div class="testi-author">
            <div class="tav l">LB</div>
            <div>
              <div class="testi-name">Lina B.</div>
              <div class="testi-role">Mère de famille</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ─── CTA BANNER ────────────────────────────── -->
    <section class="cta-banner" id="contact">
      <h2>Prêt à manger mieux,<br />durablement ?</h2>
      <p>
        Rejoignez plus de 1 200 utilisateurs et commencez votre transformation
        alimentaire dès aujourd'hui. C'est gratuit.
      </p>
      <div class="cta-banner-btns">
        <button class="banner-btn-primary" onclick="openModal('register')">
          Créer mon compte gratuitement
        </button>
        <button class="banner-btn-secondary" onclick="openModal('login')">
          Déjà membre ? Se connecter
        </button>
      </div>
    </section>

    <!-- ─── FOOTER ────────────────────────────────── -->
    <footer>
      <div class="footer-grid">
        <div class="footer-brand">
          <a
            class="logo"
            href="#"
            style="margin-bottom: 0.6rem; display: inline-flex"
          >
            <span class="logo-text">Eco<span>Nutri</span></span>
          </a>
          <p>
            Une application d'alimentation saine et durable propulsée par
            l'intelligence artificielle pour vous aider à mieux manger et
            réduire le gaspillage.
          </p>
          <div class="footer-tagline">"Mangez mieux, vivez durablement"</div>
        </div>
        <div class="footer-col">
          <h4>Navigation</h4>
          <ul>
            <li><a href="#">Accueil</a></li>
            <li><a href="index.html">Nos Recettes</a></li>
            <li><a href="#fonctionnalites">Fonctionnalités</a></li>
            <li><a href="#comment">Comment ça marche</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Compte</h4>
          <ul>
            <li><a href="#" onclick="openModal('register')">S'inscrire</a></li>
            <li><a href="#" onclick="openModal('login')">Se connecter</a></li>
            <li><a href="#">Mon profil</a></li>
            <li><a href="#">Suivi alimentaire</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>À propos</h4>
          <ul>
            <li><a href="#">Notre mission</a></li>
            <li><a href="#">L'équipe</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">Politique de confidentialité</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>© 2026 <span>EcoNutri</span> — Tous droits réservés.</p>
        <p>Fait avec 💚 pour une planète en meilleure santé</p>
      </div>
    </footer>

    <!-- ─── LOGIN MODAL ────────────────────────────── -->
    <div
      class="modal-overlay"
      id="loginModal"
      onclick="closeModalOutside(event,'loginModal')"
    >
      <div class="modal">
        <div class="modal-header">
          <div class="modal-header-left">
            <div class="mh-icon">🔑</div>
            <div>
              <h2>Connexion</h2>
              <p>Bienvenue de retour sur EcoNutri</p>
            </div>
          </div>
          <button class="modal-close" onclick="closeModal('loginModal')">
            ✕
          </button>
        </div>
        <div class="modal-body">
          <form id="loginForm" onsubmit="handleLogin(); return false;">
            <div class="form-group">
              <label>Adresse email <span>*</span></label>
              <input
                id="adminEmail"
                type="email"
                class="form-control"
                placeholder="votre@email.com"
              />
            </div>
            <div class="form-group">
              <label>Mot de passe <span>*</span></label>
              <input
                id="adminPassword"
                type="password"
                class="form-control"
                placeholder="••••••••"
              />
            </div>
            <div class="forgot"><a href="#">Mot de passe oublié ?</a></div>
            <button type="submit" class="btn-submit-full">
              Se connecter
            </button>
          </form>
          <div class="divider">ou</div>
          <div class="modal-switch">
            Pas encore de compte ?
            <a onclick="switchModal('loginModal','registerModal')"
              >S'inscrire gratuitement →</a
            >
          </div>
        </div>
      </div>
    </div>

    <!-- ─── REGISTER MODAL ─────────────────────────── -->
    <div
      class="modal-overlay"
      id="registerModal"
      onclick="closeModalOutside(event,'registerModal')"
    >
      <div class="modal">
        <div class="modal-header">
          <div class="modal-header-left">
            <div class="mh-icon">🌿</div>
            <div>
              <h2>Créer un compte</h2>
              <p>Rejoignez la communauté EcoNutri</p>
            </div>
          </div>
          <button class="modal-close" onclick="closeModal('registerModal')">
            ✕
          </button>
        </div>
        <div class="modal-body">
          <div class="form-row-2">
            <div class="form-group">
              <label>Prénom <span>*</span></label>
              <input
                type="text"
                class="form-control"
                placeholder="Votre prénom"
              />
            </div>
            <div class="form-group">
              <label>Nom <span>*</span></label>
              <input type="text" class="form-control" placeholder="Votre nom" />
            </div>
          </div>
          <div class="form-group">
            <label>Adresse email <span>*</span></label>
            <input
              type="email"
              class="form-control"
              placeholder="votre@email.com"
            />
          </div>
          <div class="form-group">
            <label>Mot de passe <span>*</span></label>
            <input
              type="password"
              class="form-control"
              placeholder="Minimum 8 caractères"
            />
          </div>
          <div class="form-row-2">
            <div class="form-group">
              <label>Âge</label>
              <input
                type="number"
                class="form-control"
                placeholder="25"
                min="13"
                max="100"
              />
            </div>
            <div class="form-group">
              <label>Objectif principal</label>
              <select class="form-control">
                <option value="">-- Choisir --</option>
                <option>Perte de poids</option>
                <option>Prise de masse</option>
                <option>Alimentation équilibrée</option>
                <option>Végétarisme / Véganisme</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Préférences alimentaires</label>
            <div class="nutrition-tags">
              <button class="ntag" onclick="toggleTag(this)">
                🥗 Végétarien
              </button>
              <button class="ntag" onclick="toggleTag(this)">🌱 Vegan</button>
              <button class="ntag" onclick="toggleTag(this)">
                🌾 Sans gluten
              </button>
              <button class="ntag" onclick="toggleTag(this)">
                🥛 Sans lactose
              </button>
              <button class="ntag" onclick="toggleTag(this)">
                🥩 Riche en protéines
              </button>
              <button class="ntag" onclick="toggleTag(this)">
                ⚡ Faible en glucides
              </button>
            </div>
          </div>
          <button class="btn-submit-full" onclick="handleRegister()">
            Créer mon compte
          </button>
          <div class="divider">ou</div>
          <div class="modal-switch">
            Déjà membre ?
            <a onclick="switchModal('registerModal','loginModal')"
              >Se connecter →</a
            >
          </div>
        </div>
      </div>
    </div>

    <!-- ─── TOAST ───────────────────────────────────── -->
    <div class="toast" id="toast"></div>

    <script>
      // Modal system
      function openModal(type) {
        const id = type === "login" ? "loginModal" : "registerModal";
        document.getElementById(id).classList.add("open");
        document.body.style.overflow = "hidden";
      }
      function closeModal(id) {
        document.getElementById(id).classList.remove("open");
        document.body.style.overflow = "";
      }
      function closeModalOutside(e, id) {
        if (e.target === document.getElementById(id)) closeModal(id);
      }
      function switchModal(from, to) {
        closeModal(from);
        setTimeout(() => {
          document.getElementById(to).classList.add("open");
        }, 250);
      }

      // Tag toggle
      function toggleTag(el) {
        el.classList.toggle("selected");
      }

      // Fake auth handlers
      function handleLogin() {
        const email = document.getElementById('adminEmail').value.trim();
        const password = document.getElementById('adminPassword').value.trim();

        if (email === 'admin@econutri.com' && password === 'admin123') {
          closeModal('loginModal');
          showToast('🎉 Connexion admin réussie !');
          setTimeout(() => {
            window.location.href = '/EcoNutri/views/backoffice/index.php';
          }, 800);
          return;
        }

        showToast('❌ Email ou mot de passe incorrect', 'error');
      }
      function handleRegister() {
        closeModal("registerModal");
        showToast(
          "🌿 Compte créé avec succès ! Bienvenue dans la communauté EcoNutri !"
        );
      }

      // Toast
      function showToast(msg) {
        const t = document.getElementById("toast");
        t.textContent = msg;
        t.classList.add("show");
        setTimeout(() => t.classList.remove("show"), 3800);
      }

      // Smooth scroll for nav links
      document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener("click", (e) => {
          const target = document.querySelector(a.getAttribute("href"));
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: "smooth", block: "start" });
          }
        });
      });

      // Dropdown for "Nos Recettes"
      function toggleRecetteDropdown(e) {
        e.stopPropagation();
        const nav = document.getElementById("nav-recettes");
        if (!nav) return;
        nav.classList.toggle("open");
      }
      // Close dropdown when clicking outside
      document.addEventListener("click", function (e) {
        const nav = document.getElementById("nav-recettes");
        if (!nav) return;
        if (!nav.contains(e.target)) {
          nav.classList.remove("open");
        }
      });
    </script>
  </body>
</html>
