<?php
/*
Page permettant de s'inscrire en créant un tuple dans la table Utilisateur.
Si l'inscription est réussie, l'utilisateur est renvoyé sur connexion.php
*/

// On conserve la session sur toutes les pages (par exemple pour garder
// cohérent le temps passé sur le site)
session_start();

// On inclue les fichiers de fonctions et on initialise $link qui est essentiel
include('fonctions/fonctions.php');
include('fonctions/utilisateur.php');
$link = getConnection($dbHost, $dbUser, $dbPwd, $dbName);
$stateMsg = "";

if(isset($_POST["valider"])){
	// Si l'utilisateur a tenté de s'inscrire,
    $pseudo = $_POST["pseudo"];
    $hashMdp = md5($_POST["mdp"]);
    $hashConfirmMdp = md5($_POST["confirmMdp"]);
    
    // on vérifie que le pseudo qu'il a entré est disponible (car pseudo
    // est la clé primaire de la table Utilisateur)
    $available = checkAvailability($pseudo, $link);
    
    if($hashMdp == $hashConfirmMdp){
		// On vérifie que les deux champs de mdp correspondent
        if($available){
			// On enregistre le nouvel utilisateur et on l'envoie sur connexion.php
            register($pseudo, $hashMdp, $link);
            header('Location: connexion.php?subscribe=yes');
        }else{
            $stateMsg = "Le pseudo demand&eacute; est d&eacute;j&agrave; utilis&eacute;";
        }
    }else{
        $stateMsg = "Les mots de passe ne correspondent pas, veuillez r&eacute;essayer";
    }
}

?>

<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Premi&egrave;re inscription</title>
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
		<div class="loginBanner">
			<?php /* On affiche un message d'erreur si l'inscription n'a pas fonctionné */ ?>
			<div class="errorMsg"><?php echo $stateMsg; ?></div>
			<form action="index.php">
					<input type="image" src="logo.png" class="logo" >
			</form>
			<p class="center">Inscription</p>
			<form action="inscription.php" method="POST">
				<table>
					<tr><td class="loginInfo">Nom d'utilisateur:</td><td><input type="text" name="pseudo"></td></tr>
					<tr><td class="loginInfo">Mot de passe:</td><td><input type="password" name="mdp"></td></tr>
					<tr><td class="loginInfo">Confirmer mot de passe:</td><td><input type="password" name="confirmMdp"></td></tr>       
					<br/>
					<tr><td>
						<br/><input class="retour" type="submit" name="valider" value="S'inscrire">
				</table>
			</form>
			
			<form action="index.php" name="retour" method="POST">
				<?php /* On propose à l'utilisateur de se rendre sur connexion.php */ ?>
				<input class="retour" type="submit" name="connect" value="Déjà inscrit ?">
			</form>
		</div>
	</body>
</html>
