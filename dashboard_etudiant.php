<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') { header("Location: login.php"); exit; }
include 'config.php';
include 'header.php';
$id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id=?");
$stmt->execute([$id]); $etu = $stmt->fetch();
$stmt = $pdo->prepare("SELECT n.note, m.intitule, m.code, m.coefficient FROM notes n JOIN modules m ON n.id_module=m.id WHERE n.id_etudiant=?");
$stmt->execute([$id]); $notes = $stmt->fetchAll();
$moy_num=0; $moy_den=0;
foreach($notes as $n){ $moy_num+=$n['note']*$n['coefficient']; $moy_den+=$n['coefficient']; }
$moy = $moy_den > 0 ? $moy_num/$moy_den : 0;
?>
<div class="container">
  <div class="card" style="margin-bottom:20px">
    <h2>👨‍🎓 Bonjour, <?= htmlspecialchars($etu['prenom'].' '.$etu['nom']) ?></h2>
    <p>Matricule : <?= htmlspecialchars($etu['matricule']) ?> | Niveau : <?= htmlspecialchars($etu['niveau']) ?></p>
  </div>
  <div class="card">
    <h2>📊 Mes notes</h2>
    <table>
      <tr><th>Module</th><th>Coefficient</th><th>Note /20</th></tr>
      <?php foreach($notes as $n): ?>
      <tr>
        <td><?= htmlspecialchars($n['intitule']) ?></td>
        <td><?= $n['coefficient'] ?></td>
        <td><b><?= number_format($n['note'],2) ?></b></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <div style="margin-top:15px;padding:15px;background:#1a3c6e;color:white;border-radius:8px;text-align:center;font-size:1.2em">
      Moyenne générale : <b><?= number_format($moy,2) ?>/20</b>
      — <?= $moy >= 10 ? '✅ ADMIS(E)' : '❌ AJOURNÉ(E)' ?>
    </div>
    <br>
    <a href="releve.php" class="btn">📄 Télécharger mon relevé de notes</a>
    &nbsp;<a href="logout.php" class="btn" style="background:#dc3545">🚪 Déconnexion</a>
  </div>
</div>
<?php include 'footer.php'; ?>