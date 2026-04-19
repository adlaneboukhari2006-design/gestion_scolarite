<?php
include 'header.php';
include 'config.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $mdp   = md5(trim($_POST['mdp']));

    // Vérif admin
    $stmt = $pdo->prepare("SELECT * FROM administrateur WHERE login=? AND mdp=?");
    $stmt->execute([$login, $mdp]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['role'] = 'admin';
        $_SESSION['nom']  = 'Administrateur';
        header("Location: dashboard_admin.php"); exit;
    }

    // Vérif enseignant
    $stmt = $pdo->prepare("SELECT * FROM enseignants WHERE email=? AND mdp=?");
    $stmt->execute([$login, $mdp]);
    $ens = $stmt->fetch();
    if ($ens) {
        $_SESSION['role'] = 'enseignant';
        $_SESSION['id']   = $ens['id'];
        $_SESSION['nom']  = $ens['prenom'].' '.$ens['nom'];
        header("Location: dashboard_enseignant.php"); exit;
    }

    // Vérif étudiant
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE matricule=? AND email=?");
    $stmt->execute([$login, $login]);
    // Connexion étudiant par matricule uniquement (simplifié)
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE matricule=?");
    $stmt->execute([$login]);
    $etu = $stmt->fetch();
    if ($etu) {
        $_SESSION['role'] = 'etudiant';
        $_SESSION['id']   = $etu['id'];
        $_SESSION['nom']  = $etu['prenom'].' '.$etu['nom'];
        header("Location: dashboard_etudiant.php"); exit;
    }

    $erreur = "Identifiants incorrects. Veuillez réessayer.";
}
?>
<div class="login-box">
  <h2>🔐 Connexion</h2>
  <?php if ($erreur): ?>
    <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Identifiant (login / matricule / email)</label>
      <input type="text" name="login" placeholder="admin / matricule" required>
    </div>
    <div class="form-group">
      <label>Mot de passe</label>
      <input type="password" name="mdp" placeholder="••••••••" required>
    </div>
    <button type="submit" class="btn" style="width:100%">Se connecter</button>
  </form>
  <p style="margin-top:15px;font-size:0.82em;color:#888;text-align:center">
    Admin : login=<b>admin</b> / mdp=<b>admin123</b><br>
    Étudiant : matricule=<b>2024001</b>
  </p>
</div>
<?php include 'footer.php'; ?>