<?php
session_start();//stocker les informations d'utilisateurs,$_SESSION POUVOIR D'UTILISATION
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {//Vérification des autorisations:si role exist et si il est admin
    header("Location: login.php"); exit;//sinon aller à la page admin ,quitter le programme
}
//Rappeler des fichiers
include 'config.php';
include 'header.php';
//Récupérer les statistiques de la base de données
$nb_etu = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();//nbr des etudiants
$nb_mod = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();//nbr des modules
$nb_ens = $pdo->query("SELECT COUNT(*) FROM enseignants")->fetchColumn();//nbr des enseignant
?>
<div class="container">
  <div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <div class="card">     <!-- '//htmlspecialchars:Protection contre les attaques codes malveillant'-->
        <h2>👋 Bonjour, <?= htmlspecialchars($_SESSION['nom']) ?></h2> 
        <p style="color:#666">Tableau de bord administrateur</p>  
      </div>
      <div class="stats">
        <div class="stat-box">
          <div class="number"><?= $nb_etu ?></div>
          <div class="label">Étudiants</div>
        </div>
        <div class="stat-box">
          <div class="number"><?= $nb_mod ?></div>
          <div class="label">Modules</div>
        </div>
        <div class="stat-box">
          <div class="number"><?= $nb_ens ?></div>
          <div class="label">Enseignants</div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>