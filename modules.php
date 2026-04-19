<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }
include 'config.php';
include 'header.php';

$msg = "";
if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $stmt = $pdo->prepare("INSERT INTO modules (code,intitule,coefficient,id_enseignant) VALUES (?,?,?,?)");
    $stmt->execute([$_POST['code'],$_POST['intitule'],$_POST['coefficient'],$_POST['id_enseignant']]);
    $msg = '<div class="alert alert-success">✅ Module ajouté.</div>';
}
if (isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM modules WHERE id=?")->execute([(int)$_GET['supprimer']]);
    $msg = '<div class="alert alert-success">✅ Module supprimé.</div>';
}
$modules = $pdo->query("SELECT m.*, e.nom as ens_nom, e.prenom as ens_prenom FROM modules m LEFT JOIN enseignants e ON m.id_enseignant=e.id")->fetchAll();
$enseignants = $pdo->query("SELECT * FROM enseignants")->fetchAll();
?>
<div class="container">
  <div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <?= $msg ?>
      <div class="card">
        <h2>➕ Ajouter un module</h2>
        <form method="POST">
          <input type="hidden" name="action" value="ajouter">
          <div class="form-row">
            <div class="form-group">
              <label>Code</label><input type="text" name="code" required>
            </div>
            <div class="form-group">
              <label>Coefficient</label><input type="number" name="coefficient" value="1" min="1" max="6">
            </div>
          </div>
          <div class="form-group">
            <label>Intitulé</label><input type="text" name="intitule" required>
          </div>
          <div class="form-group">
            <label>Enseignant responsable</label>
            <select name="id_enseignant">
              <option value="">-- Aucun --</option>
              <?php foreach($enseignants as $e): ?>
                <option value="<?=$e['id']?>"><?= htmlspecialchars($e['prenom'].' '.$e['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn">Ajouter</button>
        </form>
      </div>
      <div class="card">
        <h2>📚 Liste des modules</h2>
        <table>
          <tr><th>Code</th><th>Intitulé</th><th>Coefficient</th><th>Enseignant</th><th>Actions</th></tr>
          <?php foreach($modules as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['code']) ?></td>
            <td><?= htmlspecialchars($m['intitule']) ?></td>
            <td><?= $m['coefficient'] ?></td>
            <td><?= htmlspecialchars($m['ens_prenom'].' '.$m['ens_nom']) ?></td>
            <td><a href="modules.php?supprimer=<?=$m['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">🗑️</a></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>