<?php
session_start();
session_destroy();//supprime info d'utilisateur
header("Location: index.php");//Redirection index.php
exit;
?>