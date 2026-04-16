<?php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_scolarite;charset=utf8", "root", "");

if (!$pdo) {
    die("erreur connexion");
}
?>
