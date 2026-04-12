<?php
// includes/footer.php
// $root is set by header.php — fallback if footer used standalone
$root = $root ?? '';
?>
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <a class="logo" href="<?= $root ?>index.php" style="margin-bottom:.6rem;display:inline-flex;">
        <span class="logo-text">Eco<span>Nutri</span></span>
      </a>
      <p>Une application d'alimentation saine et durable propulsée par l'intelligence artificielle pour vous aider à mieux manger et réduire le gaspillage.</p>
      <div class="footer-tagline">"Mangez mieux, vivez durablement"</div>
    </div>
    <div class="footer-col">
      <h4>Navigation</h4>
      <ul>
        <li><a href="<?= $root ?>index.php">Accueil</a></li>
        <li><a href="<?= $root ?>listAliment.php">Aliments</a></li>
        <li><a href="<?= $root ?>listRecette.php">Recettes</a></li>
        <li><a href="<?= $root ?>index.php#fonctionnalites">Fonctionnalités</a></li>
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
        <li><a href="<?= $root ?>index.php#contact">Contact</a></li>
        <li><a href="#">Politique de confidentialité</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?= date('Y') ?> <span>EcoNutri</span> — Tous droits réservés.</p>
    <p>Fait avec 💚 pour une planète en meilleure santé</p>
  </div>
</footer>
</body>
</html>

<style>
footer{background:#0e2a08;color:rgba(255,255,255,.65);padding:3rem 5rem 2rem;}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:2.5rem;padding-bottom:2.5rem;border-bottom:1px solid rgba(255,255,255,.1);}
.footer-brand .logo-text{color:var(--white);font-size:1.3rem;}
.footer-brand p{font-size:.83rem;line-height:1.6;margin-top:.7rem;}
.footer-tagline{color:var(--orange);font-size:.78rem;font-style:italic;margin-top:.4rem;}
.footer-col h4{color:var(--white);font-size:.88rem;font-weight:700;margin-bottom:1rem;text-transform:uppercase;letter-spacing:.5px;}
.footer-col ul{list-style:none;display:flex;flex-direction:column;gap:.5rem;}
.footer-col ul li a{color:rgba(255,255,255,.6);text-decoration:none;font-size:.83rem;transition:color .2s;}
.footer-col ul li a:hover{color:var(--green-light);}
.footer-bottom{padding-top:1.5rem;display:flex;justify-content:space-between;align-items:center;font-size:.8rem;}
.footer-bottom span{color:var(--orange);}
@media(max-width:1024px){.footer-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:680px){.footer-grid{grid-template-columns:1fr;}footer{padding:2rem 1.5rem;}}
</style>
