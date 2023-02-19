<?php
/*
Fichier contenant toutes les fonctions du site ne se rapportant ni à la connexion,
ni au dialogue avec PHPMyAdmin. 
*/

// On inclue le fichier de fonctions mysqli et on initialise $link qui est essentiel
include('bd.php');
$link = getConnection($dbHost, $dbUser, $dbPwd, $dbName);


// Cette fonction retourne true si le pseudo passé en paramètre est celui d'un
// administrateur, false sinon (utilisateur.admin est un booléen)
function estAdmin($pseudo) {
	GLOBAL $link;
	$query = "SELECT admin FROM utilisateur WHERE pseudo = '".$pseudo."'";
	$res = executeQuery($link, $query);
	$estAdmin = mysqli_fetch_array($res);
	return $estAdmin[0];
}

// Cette fonction retourne true si le pseudo passé en paramètre est celui de
// l'importateur de la photo passé en paramètre (l'importateur est unique car
// c'est une clé étrangère), false sinon
function estImportateur($pseudo, $photoId) {
	GLOBAL $link;
	$query = "SELECT importateur FROM Photo WHERE photoId = '".$photoId."'";
	$res = executeQuery($link, $query);
	$importateur = mysqli_fetch_array($res);
	return $importateur[0] == $pseudo;
}

// Cette fonction retourne vrai si les caractéristiques passées en paramètre
// répondent aux spécifications définie dans l'énoncé, false sinon. Un message
// d'erreur précis est généré en cas de non-conformité des caractéristiques
function estPhotoCorrecte($taille, $description, $cat, $format) {
	if ($taille > 102400) {
		echo "Echec de l'importation : image trop volumineuse (limite de 100ko)" . "<br>";
		return false;
	}
	else if ($description == NULL) {
		echo "Echec de l'importation : description nécessaire" . "<br>";
		return false;
	}
	else if ($cat == -1) {
		echo "Echec de l'importation : choix de categorie nécessaire" . "<br>";
		return false;
	}
	else if ($format != "image/jpeg" and $format != "image/gif" and $format != "image/png") {
		echo "Echec de l'importation : format non conforme (JPEG, GIF et PNG acceptés)" . "<br>";
		return false;
	}
	else {
		return true;
	}
}

// Cette fonction enregistre les données passées en paramètre dans la table Photo
// puis envoie l'utilisateur sur la page details.php correspondant à la photo.
// Pour ce faire, la fonction a besoin de photoId correspondant. Elle le trouve
// en executant la requête renvoyant le plus grand des photoId de la table Photo.
// En effet, photoId est en AUTO_INCREMENT.
function enregistrerPhoto($nom, $description, $cat, $taille, $importateur, $contenu) {
	GLOBAL $link;
	$query = 'insert into Photo(nomfic, description, catId, taille, importateur)
			  values("'. $nom .'", "'. $description .'", "'. $cat .'", "'. $taille .'", "'.$importateur.'")';											   
	$a = executeQuery($link, $query);	
	move_uploaded_file($contenu, "Data/$nom");	
	$query = "SELECT MAX(photoId) FROM Photo";
	$res = executeQuery($link, $query);
	$maxId = mysqli_fetch_array($res)[0];
	if ($a) { header('Location: https://bdw1.univ-lyon1.fr/p1913852/mini-Pinterest/details.php?photoId='.$maxId);
					 exit();
	}
	else { echo "Echec de l'importation : raison indéterminée";};
}

// Cette fonction supprime la photo contenu dans Data/ et correspondant
// à l'Id passé en paramètre. Ensuite, elle supprime le tuple de la table
// photo correspondant à l'Id passé en paramètre. Elle retourne true si
// l'opération a réussi, false sinon.
function supprimerPhoto($photoId) {
	GLOBAL $link;
	$query = "SELECT nomFic FROM Photo WHERE photoId = '".$photoId."'";
	$res = executeQuery($link, $query);
	$nomFic = mysqli_fetch_array($res);
	$dossier = "/var/www/p1913852/mini-Pinterest/Data";
	$confirmation = unlink("$dossier/$nomFic[0]");
	if(!$confirmation) { return false; }
	$query = "DELETE FROM Photo WHERE photoId = '".$photoId."'";
	$res = executeUpdate($link, $query);
	return $res;
}

// Cette fonction vérifie que la description passé en paramètre contient au
// moins un caractère puis met à jour le tuple de la table Photo
// correspondant à l'Id passé en paramètre. Elle retourne true si l'opération
// a réussi, false sinon.
function modifierPhoto($photoId, $description, $categorie) {
	GLOBAL $link;
	if($categorie == -1) {
		$query = 'UPDATE Photo SET description = "'.$description.'" WHERE photoId = "'.$photoId.'"';
	}
	else {
	$query = "UPDATE Photo SET description = '".$description."', catId = '".$categorie."'
			  WHERE photoId = '".$photoId."'";
	}
	$res = executeUpdate($link, $query);
	return $res;	
}

// Cette fonction affiche le temps depuis lequel l'utilisateur
// est connecté en h/min/s grâce à la variable de session "time" initialisée
// à time() lors de la connexion.
function afficherTemps($time) {			
	$heure = floor($time/3600);			
	$minute = floor($time/60) - 60*$heure;
	$seconde = $time - 60*$minute - 3600*$heure;
	if ($heure > 0) { echo $heure . "h" . $minute . "min"; }
	else if ($minute > 0) { echo $minute . "min"; }
	echo $seconde . "s.";
}

// Cette fonction affiche toutes les photos de la base de données de la 
// catégorie passée en paramètre. Les photos sont affichées dans un ordre
// aléatoire et sont cliquables : elles renvoient vers la page details.php
// leur correspondant.
function afficherPhoto($categorie, $pseudo=NULL) {
	GLOBAL $link;
	if ($categorie == -1) {
		$query = "SELECT nomFic, photoId FROM Photo ORDER BY RAND()";
	}
	else if ($categorie == -2) {
		$query = "SELECT nomFic, photoId FROM Photo WHERE importateur = '".$pseudo."' ORDER BY RAND()";
	}
	else {
		$query = "SELECT nomFic, photoId FROM Photo WHERE catId = '". $categorie ."' ORDER BY RAND()";
	}
	
    $res = executeQuery($link, $query);
    while($tabPhoto = mysqli_fetch_array($res)) {
		echo "<div class='image-bord'><a class='image' href='details.php?photoId=$tabPhoto[1]'> 
		<img src='Data/$tabPhoto[0]' class='image' alt='erreur'/></a></div>" . "    ";
	}
}

// Cette fonction retourne le nombre de photos de la catégorie passée en
// paramètre présentes dans la base.
function compterPhotos($categorie, $pseudo) {
	GLOBAL $link;
	if ($categorie == -1) {
		$query = "SELECT COUNT(photoId) FROM Photo";
	}
	else if (($categorie == -2) and !($pseudo == "0")) {
		$query = "SELECT COUNT(photoId) FROM Photo WHERE importateur = '".$pseudo."'";
	}
	else {
		$query = "SELECT COUNT(photoId) FROM Photo WHERE catId = '".$categorie."'";
	}
	$res = executeQuery($link, $query);
	$nbPhotos = mysqli_fetch_array($res);
	return $nbPhotos[0];
}

// Cette fonction affiche un message spécifiant le nombre de photos de la
// catégorie passée en paramètre présentes dans la base.
function afficherNbPhotos($choix, $pseudo="0") {
	$nbPhotos = compterPhotos($choix, $pseudo);
	echo $nbPhotos . " photo";
	if($nbPhotos > 1) { echo "s"; };
	echo " sélectionnée";
	if($nbPhotos > 1) { echo "s"; };
	echo ".";
}

// Cette fonction retourne un tableau correspondant aux caractéristiques de la
// photo passée en paramètre.
function getCaracDeBase($photoId) {
	GLOBAL $link;
	$query = "SELECT description, nomfic, catId, importateur, taille FROM Photo WHERE photoId = '".$photoId."'";
	$res = executeQuery($link, $query);
	$carac = mysqli_fetch_array($res, MYSQLI_NUM);
	return $carac;
}

// Cette fonction retourne un tableau correspondant aux caractéristiques passées
// paramètre modifiées légérement. Elle a pour but principal de faciliter l'affichage
// de ces caractéristiques dans details.php.
function transfoCarac($carac) {
	GLOBAL $link;
	$query = "SELECT nomCat FROM Categorie WHERE catId = '".$carac[2]."'";
	$res = executeQuery($link, $query);
	$tmp = mysqli_fetch_array($res);
	$carac[5] =  $tmp[0];
	$carac[4] = floor($carac[4]/1024);
	return $carac;
}

// Cette fonction affiche des statistiques du site.
function afficherStats() {
	GLOBAL $link;
	$nbUtilisateurs = getNbUtilisateurs();
	$nbImages = compterPhotos(-1, "0");
	$catsRes = getCat();
	$nbCat = getNbCat();
	$adminsRes = getAdmin();
	echo "<a class='nomStat'>⬦ Nombre total d'utilisateurs : </a>" . $nbUtilisateurs . "<br>";
	echo "<a class='nomStat'>⬦ Nombre total d'images : </a>" . $nbImages . "<br>";
	echo "<a class='nomStat'>⬦ Catégories disponibles </a>" . "(" . $nbCat . " différentes)";
	echo "<a class='nomStat'> : </a>";
	while($cat = mysqli_fetch_array($catsRes)) {
		$nbPhotos = compterPhotos($cat[0], "0");
		echo "<br> ⬫ " . $cat[1] . " (" . $nbPhotos . " photo";
		if($nbPhotos > 1) {
			echo "s";
		}
		echo ")";
	}
	echo "<a class='nomStat'><br>⬦ Administrateurs : </a>";
	while($admin = mysqli_fetch_array($adminsRes)) {
		echo $admin[0] . " ";
	}
	echo "<br><a class='nomStat'>⬦ Temps total passé sur le site ";
	echo "<br> par des utilisateurs connectés : </a>";
	afficherTempsTotal();
}

// Cette fonction retourne le nombre d'utilisateurs dans la base.
function getNbUtilisateurs() {
	GLOBAL $link;
	$query = "SELECT COUNT(pseudo) FROM utilisateur";
	$res = executeQuery($link, $query);
	$nbUtilisateurs = mysqli_fetch_array($res);
	return $nbUtilisateurs[0];
}

// Cette fonction retourne une liste des catégories disponibles.
function getCat() {
	GLOBAL $link;
	$query = "SELECT catId, nomCat FROM Categorie";
	$res = executeQuery($link, $query);
	return $res;
}

// Cette fonction retourne le nombre de catégories dans la base.
function getNbCat() {
	GLOBAL $link;
	$query = "SELECT COUNT(catId) FROM Categorie";
	$res = executeQuery($link, $query);
	$nbCat = mysqli_fetch_array($res);
	return $nbCat[0];
}

// Cette fonction retourne une liste des administrateurs du site.
function getAdmin() {
	GLOBAL $link;
	$query = "SELECT pseudo FROM utilisateur WHERE admin = true";
	$res = executeQuery($link, $query);
	return $res;
}

// Cette fonction ajoute le temps passé sur le site par l'utilisateur qui vient
// de se déconnecter au temps total stocké dans la base.
function ajouterTempsTotal($duree) {
	GLOBAL $link;
	$query = "SELECT tempsTotal FROM tempsTotal";
	$res = executeQuery($link, $query);
	$tempsTotal = mysqli_fetch_array($res);
	$tempsTotal = $tempsTotal[0] + $duree;
	$query = "UPDATE tempsTotal SET tempsTotal = '".$tempsTotal."'";
	executeUpdate($link, $query);
}

// Cette fonction affiche le temps total stocké dans la base.
function afficherTempsTotal() {
	GLOBAL $link;
	$query = "SELECT tempsTotal FROM tempsTotal";
	$res = executeQuery($link, $query);
	$tempsTotal = mysqli_fetch_array($res);
	afficherTemps($tempsTotal[0]);	
}

?>
