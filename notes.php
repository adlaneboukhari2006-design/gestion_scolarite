<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }
include 'config.php';
include 'header.php';

$msg = "";
if (isset($_POST['action']) && $_POST['action'] === 'saisir') {
    $stmt = $pdo->prepare("SELECT id FROM notes WHERE id_etudiant=? AND id_module=?");
    $stmt->execute([$_POST['id_etudiant'],$_POST['id_module']]);
    if ($stmt->rowCount() > 0) {
        $pdo->prepare("UPDATE notes SET note=? WHERE id_etudiant=? AND id_module=?")->execute([$_POST['note'],$_POST['id_etudiant'],$_POST['id_module']]);
    } else {
        $pdo->prepare("INSERT INTO notes (id_etudiant,id_module,note) VALUES (?,?,?)")->execute([$_POST['id_etudiant'],$_POST['id_module'],$_POST['note']]);
    }
    $msg = '<div class="alert alert-success">✅ Note enregistrée.</div>';
}

$etudiants  = $pdo->query("SELECT * FROM etudiants ORDER BY nom")->fetchAll();
$modules    = $pdo->query("SELECT * FROM modules ORDER BY intitule")->fetchAll();
$toutes_notes = $pdo->query("SELECT n.*, e.nom, e.prenom, m.intitule, m.coefficient FROM notes n JOIN etudiants e ON n.id_etudiant=e.id JOIN modules m ON n.id_module=m.id ORDER BY e.nom, m.intitule")->fetchAll();
?>
<div class="container">
  <div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <?= $msg ?>
      <div class="card">
        <h2>📝 Saisir / Modifier une note</h2>
        <form method="POST">
          <input type="hidden" name="action" value="saisir">
          <div class="form-row">
            <div class="form-group">
              <label>Étudiant</label>
              <select name="id_etudiant" required>
                <option value="">-- Choisir --</option>
                <?php foreach($etudiants as $e): ?>
                  <option value="<?=$e['id']?>"><?= htmlspecialchars($e['prenom'].' '.$e['nom'].' ('.$e['matricule'].')') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Module</label>
              <select name="id_module" required>
                <option value="">-- Choisir --</option>
                <?php foreach($modules as $m): ?>
                  <option value="<?=$m['id']?>"><?= htmlspecialchars($m['code'].' – '.$m['intitule']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Note (/20)</label>
              <input type="number" name="note" step="0.25" min="0" max="20" required>
            </div>
          </div>
          <button type="submit" class="btn">Enregistrer</button>
        </form>
      </div>
      <div class="card">
        <h2>📋 Toutes les notes</h2>
        <table>
          <tr><th>Étudiant</th><th>Module</th><th>Coefficient</th><th>Note</th></tr>
          <?php foreach($toutes_notes as $n): ?>
          <tr>
            <td><?= htmlspecialchars($n['prenom'].' '.$n['nom']) ?></td>
            <td><?= htmlspecialchars($n['intitule']) ?></td>
            <td><?= $n['coefficient'] ?></td>
            <td><b><?= number_format($n['note'],2) ?></b></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>