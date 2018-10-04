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
	case 'choixVisiteur':{
		$lesVisiteurs=$pdo->getLesVisiteurs($mois);
		$lesCles = array_keys($lesVisiteurs);
		$visiteurASelectionner = $lesCles[0];
		include("vues/v_listeVisiteurs.php");
		break;
	}
	
	case 'voirFicheAValider':{
		$leVisiteur = $_REQUEST['lstVisiteur'];
		$lesVisiteurs=$pdo->getLesVisiteurs($mois);
		$visiteurASelectionner = $leVisiteur;
		include("vues/v_listeVisiteurs.php");
		
	}
	
}


?>