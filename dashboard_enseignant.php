<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
  header("Location: login.php");
  exit;
}
include 'config.php';
include 'header.php';
$id = $_SESSION['id']; //id de prof pour connaitres le modules
$modules = $pdo->prepare("SELECT * FROM modules WHERE id_enseignant=?"); //prepare:recherch sur bdd et protège contre les injections SQL
$modules->execute([$id]);
$mods = $modules->fetchAll(); //mods:liste des module de l'enseignant
?>
<div class="container">
  <div class="card" style="margin-bottom:20px">
    <h2>👨‍🏫 Bonjour, <?= htmlspecialchars($_SESSION['nom']) ?></h2>
  </div>
  <?php foreach ($mods as $m): ?><!--//Pour chaque module nous affichons ses étudiants-->
  <div class="card">        
    <h2>📚 <?= htmlspecialchars($m['intitule']) ?> (<?= htmlspecialchars($m['code']) ?>)</h2>  <!--$m['intitule']:module $m['code']):abreviation-->
    <?php
    $stmt = $pdo->prepare("SELECT e.nom, e.prenom, e.matricule, n.note, e.id FROM etudiants e LEFT JOIN notes n ON n.id_etudiant=e.id AND n.id_module=? ORDER BY e.nom");
    $stmt->execute([$m['id']]);
    $rows = $stmt->fetchAll();//donne tout les valeur qui sont dans bdd
    ?>
    <form method="POST" action="notes.php"><!--donnees transferer a "notes.php",method="POST":URL--> 
      <table>
        <tr>
          <th>Matricule</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Note actuelle</th>
        </tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['matricule']) ?></td>
            <td><?= htmlspecialchars($r['nom']) ?></td>
            <td><?= htmlspecialchars($r['prenom']) ?></td>
            <td><?= $r['note'] !== null ? number_format($r['note'], 2) : '<span style="color:#999">Non saisi</span>' ?></td><!--//2lettre apres virgule,sinon:Non saisi-->
          </tr>
        <?php endforeach; ?>
      </table>
    </form>
  </div>
<?php endforeach; ?>
<a href="logout.php" class="btn" style="background:#dc3545">🚪 Déconnexion</a>
</div>
<?php include 'footer.php'; ?>