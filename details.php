<?php
/*
Page détaillant les caractéristiques d'une photo donnée. Si aucun photoId n'est
passé par GET, l'utilisateur est renvoyé sur index.php.
*/

// On conserve la session sur toutes les pages (par exemple pour garder
// cohérent le temps passé sur le site)
session_start();

// On inclue les fichiers de fonctions et on initialise $link et $pseudo qui sont essentiels
include('fonctions/fonctions.php');
$link = getConnection($dbHost, $dbUser, $dbPwd, $dbName);
if(isset($_SESSION["logged"])){
	$pseudo = $_SESSION["user"];
}
else{
	session_unset();
}

// On effectue toutes les actions liées au (re)chargement de la page
if (!isset($_GET['photoId'])) {
	header('location: index.php');
}

// On initialise $photoId qui va définir la page
$photoId = $_GET['photoId'];

// Si l'utilisateur a essayé de supprimer la photo, il est renvoyé sur index.php
// avec un booléen décrivant la réussite de l'opération
if (isset($_POST["supprimer"])) {
	$confirmation = supprimerPhoto($photoId);
	header('location: index.php?supprimer=$confirmation');
}

// Si l'utilisateur a essayé de modifier la photo,
if (isset($_POST["modifier"])) {
	$description = $_POST["description"];
	$categorie = $_POST["categorie"];
	
	// On vérifie que la description contient au moins un caractère
	if($description == NULL) {
		echo "Echec des modifications : description nécessaire !";
	}
	else {
		// Et on effectue la modification
		$confirmation = modifierPhoto($photoId, $description, $categorie);
		if($confirmation) {
		echo "Modifications apportées avec succès";
		}
	}
}

// Lorsque l'utilisateur vient d'importer une photo qui a été redimensionnée,
// un message le prévient
if (isset($_GET["redimensionnee"])) {
	if ($_GET["redimensionnee"]) {
		echo "Image redimensionnée";
	}
}

?>
<!doctype html>
<!-- details.php -->
<html>
	<head>
		<meta charset="utf-8" />
		<title> mini Pinterest </title>
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
	<div class >
		<form action="index.php">
					<input type="image" src="logo.png" class="logo" >
		</form>
	<?php
	// On récupère les caractéristiques de la photo à partir de son Id
	$caracDeBase = getCaracDeBase($photoId);
	$carac = transfoCarac($caracDeBase);
	// et on les affiche :
	// la photo elle-même
	echo "<img src='Data/$carac[1]' alt='erreur'/>";
	// sa description
	echo "<p id='description'> Description : $carac[0] </p>";
	// son nom
	echo "<p id='nomFic'> Nom du fichier : $carac[1] </p>";
	// sa catégorie, qui est cliquable et renvoie sur index.php
	echo " Catégorie : " . "<a href='index.php?choix=$carac[2]'>$carac[5]</a>";
	// son importateur
	echo "<p id='importateur'> Importateur : $carac[3]</p>";
	// et sa taille convertie en ko
	echo "<p id='taille'> Taille du fichier : $carac[4] ko </p>";
	
	if(isset($pseudo)) {
		if (estAdmin($pseudo) or estImportateur($pseudo, $photoId)) {
		// Si l'utilisateur est administrateur ou l'importateur la photo, on lui propose de la supprimer
		echo "<form method='POST' action='details.php?photoId=$photoId'>
			  <input class ='retour' type ='submit' name='supprimer' value='Supprimer la photo'>
			  </form>";
			  
		// et de la modifier
		echo "<form name='nom' method='POST' action='details.php?photoId=$photoId'>"; ?>
				<p class="description">Nouvelle description de l'image  : <br><br>
					<textarea cols="50" rows="5" wrap="hard" name="description"><?php echo $carac[0] ?></textarea>
				</p>
				<p class="deroulant">Nouvelle catégorie de l'image  :
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
				<input class="retour" type="submit" name="modifier" value="Valider">
			<?php echo "</form>";
				
		}
	}
	?>
	</div>
	</body>
</html>
