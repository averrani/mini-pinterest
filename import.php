<?php
/*
Page permettant de téléverser une photo. Si l'opération s'est bien passée, l'utilisateur
est renvoyé sur la page details.php correspondant à la photo 
*/

// On conserve la session sur toutes les pages (par exemple pour garder
// cohérent le temps passé sur le site)
session_start();

// On inclue les fichiers de fonctions et on initialise $link qui est essentiel
include('fonctions/fonctions.php');
$link = getConnection($dbHost, $dbUser, $dbPwd, $dbName);	

// Si la photo et ses caractéristiques ont été transmises, on tente de les enregistrer
if (isset($_POST["valider"])) {
	
	// on récupère les caractéristiques
	$nom = $_FILES["image"]["name"];
	$description = $_POST["description"];
	$cat = $_POST["categorie"];
	// $contenu correspond au nom de la ressource qui a été envoyée au serveur
	$contenu = $_FILES["image"]["tmp_name"];
	$format = $_FILES["image"]["type"];
	$importateur = $_SESSION["user"];
	$taille = $_FILES["image"]["size"];
	
		
	if(estPhotoCorrecte($taille, $description, $cat, $format)) {
		//Si les spécifications demandées par le sujet s'appliquent à la photo,
		// On tente de l'enregistrer
		enregistrerPhoto($nom, $description, $cat, $taille, $importateur, $contenu);
	}
}

?>

<!doctype html>
<!-- import.php -->
<html>
	<head>
		<meta charset="utf-8" />
		<title> mini Pinterest </title>
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
	<div class="loginBanner">
		<form action="index.php">
					<input type="image" src="logo.png" class="logo" >
		</form>
		<p class="import">Importation d'image</p><br>
		<form name="nom" method="POST" action="import.php" enctype="multipart/form-data">
			<?php /* L'input pour la photo */ ?>
			<input type="file" name="image">
			<p class="loginInfo">Description de l'image  :
				<br>
				<br>
				<?php /* L'input pour la description */ ?>
				<textarea cols="50" rows="5" wrap="hard" name="description"></textarea>
			</p>
			<p class="loginInfo">Catégorie de l'image  :
				<?php /* L'input pour la catégorie */ ?>
				<select name="categorie">
					<option value="-1">Catégorie</option>
					<option value="0">animaux</option>
					<option value="1">paysage</option>
					<option value="2">art</option>
					<option value="3">portrait</option>
					<option value="4">salomon</option>
					<option value="5">amogus</option>
				</select>
			</p>
			<?php /* L'input pour la validation */ ?>
			<input class="retour" type="submit" name="valider" value="Importer">
		</form>
	
	</div>
	</body>


</html>
