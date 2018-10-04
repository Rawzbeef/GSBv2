<?php
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
if($pdo->getStatut($idVisiteur) == "Visiteur") {
	include("vues/v_sommaire_V.php");
}
else {
	include("vues/v_sommaire_C.php");
}

$mois = getMois(date("d/m/Y"));
$numAnnee =substr( $mois,0,4);
$numMois =substr( $mois,4,2);
switch($action){
	case 'choixVisiteur':{
		
		break;
	}
	
}


?>