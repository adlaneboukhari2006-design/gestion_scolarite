<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}
include 'config.php';
include 'header.php';

$msg = "";

// AJOUT
if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $mat  = trim($_POST['matricule']);
    $nom  = trim($_POST['nom']);
    $pre  = trim($_POST['prenom']);
    $dn   = $_POST['date_naissance'];
    $em   = trim($_POST['email']);
    $niv  = trim($_POST['niveau']);
    $stmt = $pdo->prepare("INSERT INTO etudiants (matricule,nom,prenom,date_naissance,email,niveau) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$mat,$nom,$pre,$dn,$em,$niv]);
    $msg = '<div class="alert alert-success">✅ Étudiant ajouté avec succès.</div>';
}

// MODIFICATION
if (isset($_POST['action']) && $_POST['action'] === 'modifier') {
    $stmt = $pdo->prepare("UPDATE etudiants SET matricule=?,nom=?,prenom=?,date_naissance=?,email=?,niveau=? WHERE id=?");
    $stmt->execute([$_POST['matricule'],$_POST['nom'],$_POST['prenom'],$_POST['date_naissance'],$_POST['email'],$_POST['niveau'],$_POST['id']]);
    $msg = '<div class="alert alert-success">✅ Étudiant modifié.</div>';
}

// SUPPRESSION
if (isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM etudiants WHERE id=?")->execute([(int)$_GET['supprimer']]);
    $msg = '<div class="alert alert-success">✅ Étudiant supprimé.</div>';
}

// Récupérer pour modifier
$edit = null;
if (isset($_GET['modifier'])) {
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id=?");
    $stmt->execute([(int)$_GET['modifier']]);
    $edit = $stmt->fetch();
}

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE nom LIKE ? OR prenom LIKE ? OR matricule LIKE ?");
    $stmt->execute(["%$search%","%$search%","%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM etudiants");
}
$etudiants = $stmt->fetchAll();
?>
<div class="container">
  <div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
      <?= $msg ?>
      <!-- Formulaire -->
      <div class="card">
        <h2><?= $edit ? '✏️ Modifier étudiant' : '➕ Ajouter un étudiant' ?></h2>
        <form method="POST">
          <input type="hidden" name="action" value="<?= $edit ? 'modifier' : 'ajouter' ?>">
          <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
          <div class="form-row">
            <div class="form-group">
              <label>Matricule</label>
              <input type="text" name="matricule" value="<?= htmlspecialchars($edit['matricule'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label>Niveau</label>
              <select name="niveau">
                <?php foreach(['L1','L2','L3','M1','M2'] as $n): ?>
                  <option value="<?=$n?>" <?= ($edit['niveau']??'')==$n?'selected':'' ?>><?=$n?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Nom</label>
              <input type="text" name="nom" value="<?= htmlspecialchars($edit['nom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label>Prénom</label>
              <input type="text" name="prenom" value="<?= htmlspecialchars($edit['prenom'] ?? '') ?>" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Date de naissance</label>
              <input type="date" name="date_naissance" value="<?= $edit['date_naissance'] ?? '' ?>">
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($edit['email'] ?? '') ?>">
            </div>
          </div>
          <button type="submit" class="btn"><?= $edit ? 'Enregistrer' : 'Ajouter' ?></button>
          <?php if ($edit): ?><a href="etudiants.php" class="btn" style="background:#6c757d;margin-left:10px">Annuler</a><?php endif; ?>
        </form>
      </div>

      <!-- Tableau -->
      <div class="card">
        <h2>📋 Liste des étudiants</h2>
        <form method="GET" class="search-bar">
          <input type="text" name="search" placeholder="Rechercher par nom, prénom ou matricule..." value="<?= htmlspecialchars($search) ?>">
          <button type="submit" class="btn btn-sm">🔍 Rechercher</button>
          <?php if ($search): ?><a href="etudiants.php" class="btn btn-sm" style="background:#6c757d">✕ Effacer</a><?php endif; ?>
        </form>
        <table>
          <tr>
            <th>Matricule</th><th>Nom</th><th>Prénom</th><th>Niveau</th><th>Email</th>
            <th>Moy.</th><th>Statut</th><th>Actions</th>
          </tr>
          <?php foreach($etudiants as $e):
            // Calcul moyenne pondérée
            $stmt2 = $pdo->prepare("SELECT SUM(n.note * m.coefficient) / SUM(m.coefficient) as moy FROM notes n JOIN modules m ON n.id_module=m.id WHERE n.id_etudiant=?");
            $stmt2->execute([$e['id']]);
            $moy = $stmt2->fetchColumn();
            $moy_str = $moy !== null ? number_format($moy,2) : 'N/A';
            $statut = ($moy !== null && $moy >= 10) ? '<span class="badge-admis">Admis</span>' : '<span class="badge-echec">Ajourné</span>';
          ?>
          <tr>
            <td><?= htmlspecialchars($e['matricule']) ?></td>
            <td><?= htmlspecialchars($e['nom']) ?></td>
            <td><?= htmlspecialchars($e['prenom']) ?></td>
            <td><?= htmlspecialchars($e['niveau']) ?></td>
            <td><?= htmlspecialchars($e['email']) ?></td>
            <td><b><?= $moy_str ?></b></td>
            <td><?= $statut ?></td>
            <td>
              <a href="etudiants.php?modifier=<?=$e['id']?>" class="btn btn-sm" style="background:#ffc107;color:#000">✏️</a>
              <a href="releve.php?id=<?=$e['id']?>" class="btn btn-sm" style="background:#17a2b8">📄 RN</a>
              <a href="etudiants.php?supprimer=<?=$e['id']?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Supprimer cet étudiant ?')">🗑️</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>