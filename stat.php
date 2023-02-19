<?php
/*
Page permettant à un administrateur de visualiser certaines statistiques du site.
*/

// On conserve la session sur toutes les pages (par exemple pour garder
// cohérent le temps passé sur le site)
session_start();

// On inclue les fichiers de fonctions et on initialise $link qui est essentiel
include('fonctions/fonctions.php');
$link = getConnection($dbHost, $dbUser, $dbPwd, $dbName);
?>


<!doctype html>
<!-- stat.php -->
<html>
	<head>
		<meta charset="utf-8" />
		<title> mini Pinterest </title>
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
	<div class="loginBanner" >
	<form action="index.php">
					<input type="image" src="logo.png" class="logo" >
	</form>
	<p class="stats"><?php
	// Appel à la fonction qui affiche toutes les statistiques
	afficherStats($link);
	?>
	</p>
	</div>
	</body>
</html>
