<?php
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
if($pdo->getStatut($idVisiteur) == "Visiteur") {
	include("vues/v_sommaire_V.php");
}
else {
	include("vues/v_bodyValidation.php");
	include("vues/v_sommaire_C.php");
}

$mois = getMois(date("d/m/Y"));
$numAnnee =substr( $mois,0,4);
$numMois =substr( $mois,4,2);

switch($action){
	case 'selectionnerMois':{
		$lesMois=$pdo->getLesMoisDisponiblesComptable();
		$lesCles = array_keys($lesMois);
		$moisASelectionner = $lesCles[0];
		include("vues/v_listeMoisComptable.php");
		break;
	}
	case 'choixVisiteur':{
		$leMois = $_REQUEST['lstMois'];
		$_SESSION['mois'] = $leMois;
		$lesMois=$pdo->getLesMoisDisponiblesComptable();
		$moisASelectionner = $leMois;
		include("vues/v_listeMoisComptable.php");
		$lesVisiteurs=$pdo->getLesVisiteurs($leMois);
		$lesCles = array_keys($lesVisiteurs);
		$visiteurASelectionner = $lesCles[0];
		include("vues/v_listeVisiteurs.php");
		break;
	}
	
	case 'voirFicheAValider':{
		$leMois = $_SESSION['mois'];
		$lesMois=$pdo->getLesMoisDisponiblesComptable();
		$moisASelectionner = $leMois;
		include("vues/v_listeMoisComptable.php");
		$leVisiteur = $_REQUEST['lstVisiteur'];
		$lesVisiteurs=$pdo->getLesVisiteurs($leMois);
		$visiteurASelectionner = $leVisiteur;
		include("vues/v_listeVisiteurs.php");
		
		break;
		
	}
	
}


?>