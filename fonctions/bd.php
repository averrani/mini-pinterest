<?php

/* Initialisation des variables de connexion à la base de données */
$dbHost = "localhost";
$dbUser = "p1913852";
$dbPwd = "Cabana86Gusto";
$dbName = "p1913852";

/*Cette fonction prend en entrée l'identifiant de la machine hôte de la base de données, les identifiants (login, mot de passe) d'un utilisateur autorisé 
sur la base de données contenant les tables pour le chat et renvoie une connexion active sur cette base de donnée. Sinon, un message d'erreur est affiché.*/
function getConnection($dbHost, $dbUser, $dbPwd, $dbName)
{
	$link = mysqli_connect($dbHost, $dbUser, $dbPwd, $dbName);
	if (!$link) {
		echo "Echec lors de la connexion à la base de données : (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
	}
	return $link;
}

/*Cette fonction prend en entrée une connexion vers la base de données du chat ainsi 
qu'une requête SQL SELECT et renvoie les résultats de la requête. Si le résultat est faux, un message d'erreur est affiché*/
function executeQuery($link, $query)
{
	$result = mysqli_query($link, $query);
	if(!$result){
		echo "La requete ".$query." n'a pas pu etre executee a cause d'une erreur de syntaxe";
	}
	return $result;
}

/*Cette fonction prend en entrée une connexion vers la base de données du chat ainsi 
qu'une requête SQL INSERT/UPDATE/DELETE et renvoie true/false selon la réussite de la mise à jour*/
function executeUpdate($link, $query)
{
	$result = mysqli_query($link, $query);
	if(!$result){
		echo "La requête de mise à jour ".$query." n'a pas pu être exécutée a cause d'une erreur de syntaxe";
	}
	return $result;
}

/*Cette fonction ferme la connexion active $link passée en entrée*/
function closeConnexion($link)
{
	mysqli_close($link);
}
?>
