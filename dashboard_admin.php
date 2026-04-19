<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}
include 'config.php';
include 'header.php';

$nb_etu = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();
$nb_mod = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
$nb_ens = $pdo->query("SELECT COUNT(*) FROM enseignants")->fetchColumn();
?>
<div class="container">
  <div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <div class="card">
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