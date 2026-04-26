<?php
//local database,database name,username pardefault,no motdepass
$host = "localhost"; 
$dbname = "gestion_scolarite";
$user = "root";
$pass = "";

//try:try to connect else:catch
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);//charset=utf8:code ascii en clavier
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//utilisateur voir l'erreur dans des exceptions
} catch (PDOException $e) {//si erreur de try connect cache dans $e
    die("Erreur de connexion : " . $e->getMessage());//die:stop le programme,getMessage:cause d'erreur
}
?>  
