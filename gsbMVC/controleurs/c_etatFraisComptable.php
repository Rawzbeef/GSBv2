<?php
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
if(!isset($_SESSION['connecte'])) {
	include("vues/v_connexion.php");
}

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
		if(sizeof($lesMois) == 0) {
			include("vues/v_validationAucuneFiche.php");
		}
		else {
			$lesCles = array_keys($lesMois);
			$moisASelectionner = $lesCles[0];
			include("vues/v_listeMoisComptableConsult.php");
		}
		break;
	}
	case 'choixVisiteur':{
		$leMois = $_REQUEST['lstMois'];
		$_SESSION['mois'] = $leMois;
		$lesMois=$pdo->getLesMoisDisponiblesComptable();
		$moisASelectionner = $leMois;
		include("vues/v_listeMoisComptableConsult.php");
		$lesVisiteurs=$pdo->getLesVisiteurs($leMois);
		$lesCles = array_keys($lesVisiteurs);
		$visiteurASelectionner = $lesCles[0];
		include("vues/v_listeVisiteursConsult.php");
		break;
	}
	case 'voirFicheFrais':{
		$leMois = $_SESSION['mois'];
		$lesMois=$pdo->getLesMoisDisponiblesComptable();
		$moisASelectionner = $leMois;
		include("vues/v_listeMoisComptableConsult.php");
		$leVisiteur = $_REQUEST['lstVisiteur'];
		$lesVisiteurs=$pdo->getLesVisiteurs($leMois);
		$visiteurASelectionner = $leVisiteur;
		include("vues/v_listeVisiteursConsult.php");
		$idVisiteur = $leVisiteur;
		$employe = $pdo->getleVisiteur($idVisiteur);
		$nomVisiteur = $employe['nom'];
		$prenomVisiteur = $employe['prenom'];
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur,$leMois);
		$lesInfosFicheFrais = $pdo->getLesFichesMoisPrecedent($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
		include("vues/v_etatFraisComptable.php");
		break;
	}
}
?>