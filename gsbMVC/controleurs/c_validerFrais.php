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
		if(sizeof($lesMois) == 0) {
			include("vues/v_validationAucuneFiche.php");
		}
		else {
			$lesCles = array_keys($lesMois);
			$moisASelectionner = $lesCles[0];
			include("vues/v_listeMoisComptable.php");
		}
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
		$idVisiteur = $leVisiteur;
		$_SESSION['visiteur'] = $idVisiteur;
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesFraisForfait= $pdo->getLesFraisForfait($idVisiteur,$leMois);
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
	
	case 'validerFicheFrais':{
		$leMois = $_SESSION['mois'];
		$lesMois=$pdo->getLesMoisDisponiblesComptable();
		$moisASelectionner = $leMois;
		include("vues/v_listeMoisComptable.php");
		$leVisiteur = $_SESSION['visiteur'];
		$lesVisiteurs=$pdo->getLesVisiteurs($leMois);
		$visiteurASelectionner = $leVisiteur;
		include("vues/v_listeVisiteurs.php");
		// Mise à jour état fiche frais
		$idEtat = $_REQUEST['lstEtat'];
		$pdo->majEtatFicheFrais($leVisiteur,$leMois,$idEtat);
		// Mise à jour refus hors forfait si existant
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur,$leMois);
		$nbHorsForfait = sizeof($lesFraisHorsForfait);
		$i = 0;
		foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) {
			$i++;
			$liste = "lstSituation".$i;
			$idHF = $unFraisHorsForfait['id'];
			$libHF = $unFraisHorsForfait['libelle'];
			echo $idHF;
			echo $libHF;
			echo $_REQUEST[$liste];
			if ($_REQUEST[$liste] == "RF") {
				$pdo->majHorsForfait($idHF, $libHF);
			}
		}
		// Affichage
		$lesFraisForfait= $pdo->getLesFraisForfait($leVisiteur,$leMois);
		$lesInfosFicheFrais = $pdo->getLesFichesMoisPrecedent($leVisiteur,$leMois);
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