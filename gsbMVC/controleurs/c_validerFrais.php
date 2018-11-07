<?php
$action = htmlspecialchars($_REQUEST['action']);
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
			include("vues/v_listeMoisComptable.php");
		}
		break;
	}
	case 'choixVisiteur':{
		$leMois = htmlspecialchars($_REQUEST['lstMois']);
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
		$leVisiteur = htmlspecialchars($_REQUEST['lstVisiteur']);
		$lesVisiteurs=$pdo->getLesVisiteurs($leMois);
		$visiteurASelectionner = $leVisiteur;
		include("vues/v_listeVisiteurs.php");
		$idVisiteur = $leVisiteur;
		$_SESSION['visiteur'] = $idVisiteur;
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitValides($idVisiteur,$leMois);
		$lesFraisForfait= $pdo->getLesFraisForfait($idVisiteur,$leMois);
		$lesInfosFicheFrais = $pdo->getLesFichesMoisPrecedent($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
		include("vues/v_validerFraisComptable.php");
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
		$idEtat = htmlspecialchars($_REQUEST['lstEtat']);
		$pdo->majEtatFicheFrais($leVisiteur,$leMois,$idEtat);
		// Mise à jour des quantités des frais forfait
		$lesFraisForfait= $pdo->getLesFraisForfait($leVisiteur,$leMois);
		foreach ($lesFraisForfait as $unFraisForfait) {
			$idF = $unFraisForfait["idfrais"];
			$qte = htmlspecialchars($_REQUEST[$idF]);
			$pdo->majQteFraisForfait($leVisiteur, $leMois, $idF, $qte);
		}
		// Mise à jour refus hors forfait si existant
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitValides($leVisiteur,$leMois);
		$nbHorsForfait = sizeof($lesFraisHorsForfait);
		$i = 0;
		foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) {
			$i++;
			$liste = "lstSituation".$i;
			$idHF = $unFraisHorsForfait['id'];
			$libHF = $unFraisHorsForfait['libelle'];
			if ($_REQUEST[$liste] == "RF") {
				$pdo->majHorsForfait($idHF, $libHF);
			}
		}
		// Mise à jour du montant validé
		$lEtat = htmlspecialchars($_REQUEST["lstEtat"]);
		if($lEtat == "RB" || $lEtat == "VA") {
			$lesMontantsFraisForfait = $pdo->getMontantFraisForfait();
			$lesFraisForfait= $pdo->getLesFraisForfait($leVisiteur,$leMois);
			$lesMontantsHorsForfait = $pdo->getLesMontantsHorsForfaitValides($leVisiteur,$leMois);
			$leMontant = calculMontantValide($lesMontantsFraisForfait, $lesFraisForfait, $lesMontantsHorsForfait);
			$pdo->majMontantValide($leVisiteur, $leMois, $leMontant);
		}
		// Affichage
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitValides($leVisiteur,$leMois);
		$lesFraisForfait= $pdo->getLesFraisForfait($leVisiteur,$leMois);
		$lesInfosFicheFrais = $pdo->getLesFichesMoisPrecedent($leVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		$dateModif =  dateAnglaisVersFrancais($dateModif);
		include("vues/v_validerFraisComptable.php");
		break;
	}
}


?>