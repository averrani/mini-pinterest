<?php
/*
Page permettant à un utilisateur disposant d'un pseudo et d'un mdp de se connecter.
Si l'opération de connexion fonctionne, l'utilisateur est renvoyé sur index.php
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
	// Si l'utilisateur a tenté de se connecter,
    $pseudo = $_POST["pseudo"];
    $hashMdp = md5($_POST["mdp"]);
    
    // on vérifie que le couple pseudo/mdp existe
    $exist = getUser($pseudo, $hashMdp, $link);
    
    if($exist){
		
		// et on le connecte le cas échéant
        setConnected($pseudo, $link);
        // On crée des variables de session pour faciliter l'écriture du code
        $_SESSION["user"] = $pseudo;
        $_SESSION["link"] = $link;
        $_SESSION["logged"] = $pseudo;
        $_SESSION["time"] = time();
        // On renvoie l'utilisateur sur index.php
		header('Location: index.php');
    }else{
		// Si le couple pseudo/mdp n'existe pas, un message d'erreur est renvoyé
        $stateMsg = "Soit le couple pseudo/mot de passe ne correspond &agrave; aucun utilisateur enregistr&eacute;, soit vous n'&ecirc;tes pas d&eacute;connect&eacute;.";
    }
}

// Si l'utilisateur vient de s'inscrire avec succès, il est renvoyé sur connexion.php
// et un message de réussite s'affiche
if(isset($_GET["subscribe"])){
    $successMsg = "<div class='sucessMsg'>L'inscription a bien &eacute;t&eacute; effectu&eacute;e, vous pouvez vous connecter</div>";
}

if(isset($_POST["retour"])) {
	header('Location: index.php');
}

?>

<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>mini Pinterest</title>
		<link rel="stylesheet" href="styles.css" />
	
	</head>
	<body>
		<div class="loginBanner" >
			<div class="errorMsg"><?php echo $stateMsg; ?></div>
			<?php if(isset($successMsg)){echo $successMsg;} ?>
				<form action="index.php">
					<input type="image" src="logo.png" class="logo" >
				</form>
				<p class="center">Connexion</p>
				<form action="connexion.php" method="POST">
					<table>
						<tr><td class="loginInfo">Pseudo:</td><td><input type="text" name="pseudo"></td></tr>
						<tr><td class="loginInfo">Mot de passe:</td><td><input type="password" name="mdp"></td></tr>
						<br/>
						<tr><td>
							<br/>
						<input class="retour" type="submit" name="valider" value="Se connecter">
					</table>
				</form>
				<form action="index.php" name="retour" method="POST">
					<input class="retour" type="submit" name="login" value="Première connexion ?">
				</form>
		</div>
	</body>
</html>
