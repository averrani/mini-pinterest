<?php
/* 
Page principale du site. C'est ici que les photos sont affichées.
On peut accéder à toutes les autres pages du site directement depuis index.php.
*/
 
// On conserve la session sur toutes les pages (par exemple pour garder
// cohérent le temps passé sur le site)
session_start();
					
// On inclue les fichiers de fonctions et on initialise $link et $user qui sont essentiels
include('fonctions/utilisateur.php');
include('fonctions/fonctions.php');
$link = getConnection($dbHost, $dbUser, $dbPwd, $dbName);
if(isset($_SESSION["logged"])){
	$user = $_SESSION["user"];
}
else{
	session_unset();
}

// On effectue toutes les actions liées au (re)chargement de la page
if (isset($_POST["choix"])) {
	$choix = $_POST["choix"];
}
else if (isset($_GET["choix"])) {
	$choix = $_GET["choix"];
}

if (isset($_GET["supprimer"])) {
	// Si l'utilisateur a essayé de supprimer une photo, il est renvoyé sur
	// index.php et un message indiquant la réussite ou l'échec de l'opération
	// de suppression est affiché
	$supprimer = $_GET["supprimer"];
	if ($supprimer) {
		echo "Suppression réussie";
	}
	else {
		echo "Suppression échouée";
	}
}

if(isset($_POST["connect"])){
	// On utilise un header pour envoyer l'utilisateur sur la page de connexion
		header('Location: connexion.php');
}

if(isset($_POST["login"])){
	// Si l'utilisateur veut se rendre sur la page d'inscription,
	if(isset($_SESSION["user"])) {
		// on ajoute son temps de connexion au temps total de connexion,
		$time = time() - $_SESSION["time"];
		ajouterTempsTotal($time);
		// on le déconnecte,
		setDisconnected($user, $link);
		session_destroy();
		session_unset();
	}
	// puis on utilise un header pour l'envoyer sur la page d'inscription
	header('Location: inscription.php');
}

if(isset($_POST["disconnect"])){
	// Quand un utilisateur se déconnecte, on ajoute son temps de connexion
	// au temps total de connexion et on détruit la session
	$time = time() - $_SESSION["time"];
	ajouterTempsTotal($time);
	setDisconnected($user, $link);
	session_destroy();
	session_unset();
	header('Location: index.php');
}

if(isset($_POST["ajouter"])){
	// On utilise un header pour envoyer l'utilisateur sur la page d'importation
	header('Location: import.php');
}

if(isset($_POST["stat"])){
	// On utilise un header pour envoyer un administrateur sur la page de statistiques
	header('Location: stat.php');
}
?>
<!doctype html>
<!-- index.php -->
<html>
	<head>
		<meta charset="utf-8" />
		<title> mini Pinterest </title>
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>	
		
		<?php /* on affiche les boutons pour s'inscrire, se connecter ou se
				 déconnecter */ ?>
		
		<form class="p2" method="post">
		<input type="image" src="logo.png" align=left class="logo" >
		<?php
		if(!isset($_SESSION["user"]) && !isset($_SESSION["link"])){
		?>
		<input class="login" type="submit" name="login" value="Créer un compte">
		<input class="connexion" type="submit" name="connect" value="Se connecter">
		<?php } ?>
		
		<?php
			if(isset($_SESSION["user"]) && isset($_SESSION["link"])){
		?>
			<input class="deconnexion" type="submit" name="disconnect" value="Se déconnecter">
		<?php } ?>
		
		</form>
		<br>
		<br>
		<br>
		<br>
		<br>
		<?php
			if(isset($_SESSION["user"]) && isset($_SESSION["link"])){
			// Si l'utilisateur est connecté
		?>
		<?php /* On affiche le bouton pour se rendre sur la page d'importation */ ?>
		<form name="nom" method="POST" action="index.php" class="p3">
		<input class ="ajout" type="submit" value="Ajouter une photo" name="ajouter">
		<?php 
		if (estAdmin($_SESSION["user"])) {
			?>
			<?php /* Si, en plus, c'est un administrateur, on affiche le bouton
					 pour se rendre sur la page des statistiques */ ?>
			<input class="ajout" type="submit" value="Statistiques du site" name="stat">
			<?php
		}
		?>
		</form>
		<?php } ?>
		
		<?php /* on affiche le nombre de photos */ ?>
		<p class="p"> <?php
		if(!isset($choix)) { $choix = -1; }
		if(!isset($user)) {
			afficherNbPhotos($choix);
		}
		else {
			afficherNbPhotos($choix, $user);
		}
		?>
		</p>
		
				
		<form name="nom" method="POST" action="index.php">
        <p class="p1"> 
			<?php
			if(isset($_SESSION["logged"])){
				// Si l'utilisateur est connecté, on affiche son pseudo et 
				// depuis combien de temps il l'est
				echo "Bonjour " . $user . ". ";
				$duree = time() - $_SESSION["time"];
				echo "Vous êtes connecté(e) depuis ";
				afficherTemps($duree);
			}
			?>
			<?php /* On affiche le menu déroulant de catégories de photos */ ?>
			Quelles photos souhaitez-vous afficher ? 
			<select name="choix">
				<option value="-1">Toutes les photos</option>
				<?php
				if(isset($user)) {
					// Si l'utilisateur est connecté, on lui propose d'afficher les photos
					// qu'il a importé
					?>
					<option value="-2">Mes photos</option>
					<?php } ?>
				<option value="0">animaux</option>
				<option value="1">paysages</option>
				<option value="2">art</option>
				<option value="3">portraits</option>
				<option value="4">salomon</option>
				<option value="5">amogus</option>
			</select>
			<input class="ajout" type="submit" name="action" value="Valider"></input>
        </p>
        </form>
        <?php /* On affiche les photos de la catégorie choisie */ ?>
		<div class="site">
			<?php
				// On regarde si une catégorie est choisie. On vérifie pour $_GET
				// et $_POST car le menu déroulant de catégories envoie la catégorie
				// par POST et details.php renvoie sur index.php avec la catégorie
				// cliquée par GET
				if (isset($_POST["choix"]) or isset($_GET['choix'])) {
					if (isset($_POST["choix"])) { $choix = $_POST["choix"]; }
					else { $choix = $_GET['choix']; }
					if ($choix == -1) {
						// Si l'utilisateur a choisi "Toutes les photos"
						?>
						<h1 class="h1"> Toutes les photos </h1>
						<?php
						afficherPhoto(-1);
					}
					else if ($choix == -2) {
						// Si l'utilisateur a choisi "Mes photos"
						?>
						<h1 class="h1"> Mes photos </h1>
						<?php
						afficherPhoto(-2, $user);
					}
					else {
						// Si l'utilisateuer a choisi une catégorie précise
						$query = "SELECT nomCat FROM Categorie WHERE catId = '".$choix."'";
						$res = executeQuery($link, $query);
						$nomCat = mysqli_fetch_array($res);
						// On affiche le nom de la catégorie
						?><h1 class="h1">Photos de la catégorie <?php echo $nomCat[0] ?></h1><?php
						// puis les photos
						afficherPhoto($choix);				
					}
				}
				else {
					// Si aucune catégorie n'est choisie (par exemple quand on arrive sur le site),
					// toutes les photos sont affichées
					?>
					<h1 class="h1"> Toutes les photos </h1>
					<?php
					afficherPhoto(-1, $link);
				}
			?>
        </div>
         
			
		</body>
</html>	

