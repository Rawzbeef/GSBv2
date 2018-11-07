<?php

if(!isset($_REQUEST['action'])){
	$_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];
switch($action){
	case 'demandeConnexion':{
		include("vues/v_connexion.php");
		break;
	}
	case 'valideConnexion':{
		$login = htmlspecialchars($_REQUEST['login']);
		$mdp = md5($_REQUEST['mdp']);
		$visiteur = $pdo->getInfosVisiteur($login,$mdp);
		if(!is_array( $visiteur)){
			ajouterErreur("Login ou mot de passe incorrect");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
		}
		else{
			$_SESSION['connecte'] = 1;
			$id = $visiteur['id'];
			$nom =  $visiteur['nom'];
			$prenom = $visiteur['prenom'];
			connecter($id,$nom,$prenom);
			if($pdo->getStatut($id) == "Visiteur") {
				include("vues/v_sommaire_V.php");
			}
			else {
				include("vues/v_bodyValidation.php");
				include("vues/v_sommaire_C.php");
			}
			$pdo->autoCloturation();
		}
		break;
	}
	case 'deconnexion':{
		session_destroy();
	}
	default :{
		include("vues/v_connexion.php");
		break;
	}
}
?>