<?php
session_start();
include 'config.php';

$id = (int)($_GET['id'] ?? $_SESSION['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id=?");
$stmt->execute([$id]);
$etu = $stmt->fetch();
if (!$etu) { echo "Étudiant introuvable."; exit; }

$stmt = $pdo->prepare("SELECT n.note, m.intitule, m.code, m.coefficient FROM notes n JOIN modules m ON n.id_module=m.id WHERE n.id_etudiant=? ORDER BY m.intitule");
$stmt->execute([$id]);
$notes = $stmt->fetchAll();

$moy_num = 0; $moy_den = 0;
foreach ($notes as $n) { $moy_num += $n['note'] * $n['coefficient']; $moy_den += $n['coefficient']; }
$moyenne = $moy_den > 0 ? $moy_num / $moy_den : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Relevé de Notes – <?= htmlspecialchars($etu['nom'].' '.$etu['prenom']) ?></title>
  <style>
    body { font-family:Arial,sans-serif; max-width:700px; margin:40px auto; padding:20px; }
    .entete { text-align:center; border-bottom:2px solid #1a3c6e; padding-bottom:15px; margin-bottom:20px; }
    .entete h2 { color:#1a3c6e; }
    .infos { display:flex; gap:30px; margin-bottom:20px; }
    .infos div { flex:1; }
    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    th { background:#1a3c6e; color:white; padding:10px; text-align:left; }
    td { padding:10px; border-bottom:1px solid #ddd; }
    .moy-box { background:#1a3c6e; color:white; padding:15px; border-radius:8px; text-align:center; font-size:1.3em; }
    .btn-print { background:#1a3c6e; color:white; padding:10px 25px; border:none; border-radius:6px; cursor:pointer; font-size:1em; }
    @media print { .no-print { display:none; } }
  </style>
</head>
<body>
<div class="entete">
  <h2>🎓 USTHB – Faculté d'Informatique</h2>
  <h3>Relevé de Notes – Année 2025/2026</h3>
</div>
<div class="infos">
  <div><b>Nom :</b> <?= htmlspecialchars($etu['nom']) ?><br>
       <b>Prénom :</b> <?= htmlspecialchars($etu['prenom']) ?></div>
  <div><b>Matricule :</b> <?= htmlspecialchars($etu['matricule']) ?><br>
       <b>Niveau :</b> <?= htmlspecialchars($etu['niveau']) ?></div>
</div>
<table>
  <tr><th>Module</th><th>Code</th><th>Coefficient</th><th>Note /20</th><th>Résultat</th></tr>
  <?php foreach($notes as $n): ?>
  <tr>
    <td><?= htmlspecialchars($n['intitule']) ?></td>
    <td><?= htmlspecialchars($n['code']) ?></td>
    <td><?= $n['coefficient'] ?></td>
    <td><b><?= number_format($n['note'],2) ?></b></td>
    <td><?= $n['note'] >= 10 ? '✅ Validé' : '❌ Ajourné' ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<div class="moy-box">
  Moyenne Générale : <b><?= number_format($moyenne,2) ?>/20</b>
  — <?= $moyenne >= 10 ? '✅ ADMIS(E)' : '❌ AJOURNÉ(E)' ?>
</div>
<br>
<div class="no-print">
  <button class="btn-print" onclick="window.print()">🖨️ Imprimer / Télécharger PDF</button>
  <a href="javascript:history.back()" style="margin-left:15px">← Retour</a>
</div>
</body>
</html>