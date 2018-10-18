<?php
/** 
 * Classe d'accès aux données. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=gsbV2';   		
      	private static $user='root' ;    		
      	private static $mdp='' ;	
		private static $monPdo;
		private static $monPdoGsb=null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct(){
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp); 
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 
 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 
 * @return l'unique objet de la classe PdoGsb
 */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;  
	}
/**
 * Retourne les informations d'un visiteur
 
 * @param $login 
 * @param $mdp
 * @return l'id, le nom et le prénom sous la forme d'un tableau associatif 
*/
	public function getInfosVisiteur($login, $mdp){
		$req = "select Employe.id as id, Employe.nom as nom, Employe.prenom as prenom from Employe 
		where Employe.login = ? and Employe.mdp = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $login);
		$st->bindParam(2, $mdp);
		$st->execute();
		$ligne = $st->fetch();
		return $ligne;
	}

/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
 * concernées par les deux arguments
 
 * La boucle foreach ne peut être utilisée ici car on procède
 * à une modification de la structure itérée - transformation du champ date-
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
*/
	public function getLesFraisHorsForfait($idVisiteur,$mois){
	    $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur = ? 
		and lignefraishorsforfait.mois = ? ";	
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$st->bindParam(2, $mois);
		$st->execute();
		$lesLignes = $st->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
/**
 * Retourne le nombre de justificatif d'un visiteur pour un mois donné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return le nombre entier de justificatifs 
*/
	public function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur = ? and fichefrais.mois = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$st->bindParam(2, $mois);
		$st->execute();
		$laLigne = $st->fetch();
		return $laLigne['nb'];
	}
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
 * concernées par les deux arguments
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
*/
	public function getLesFraisForfait($idVisiteur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur = ? and lignefraisforfait.mois= ? 
		order by lignefraisforfait.idfraisforfait";	
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$st->bindParam(2, $mois);
		$st->execute();
		$lesLignes = $st->fetchAll();
		return $lesLignes; 
	}
/**
 * Retourne tous les id de la table FraisForfait
 
 * @return un tableau associatif 
*/
	public function getLesIdFrais(){
		$req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}
/**
 * Met à jour la table ligneFraisForfait
 
 * Met à jour la table ligneFraisForfait pour un visiteur et
 * un mois donné en enregistrant les nouveaux montants
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
 * @return un tableau associatif 
*/
	public function majFraisForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait set lignefraisforfait.quantite = ?
			where lignefraisforfait.idvisiteur = ? and lignefraisforfait.mois = ?
			and lignefraisforfait.idfraisforfait = ?";
			$st = PdoGsb::$monPdo->prepare($req);
			$st->bindParam(1, $qte);
			$st->bindParam(2, $idVisiteur);
			$st->bindParam(3, $mois);
			$st->bindParam(4, $unIdFrais);
			$st->execute();
		}
		
	}
/**
 * met à jour le nombre de justificatifs de la table ficheFrais
 * pour le mois et le visiteur concerné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
		$req = "update fichefrais set nbjustificatifs = ? 
		where fichefrais.idvisiteur = ? and fichefrais.mois = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $nbJustificatifs);
		$st->bindParam(2, $idVisiteur);
		$st->bindParam(3, $mois);
		$st->execute();	
	}
/**
 * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return vrai ou faux 
*/	
	public function estPremierFraisMois($idVisiteur,$mois)
	{
		$ok = false;
		$req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = ? and fichefrais.idvisiteur = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $mois);
		$st->bindParam(2, $idVisiteur);
		$st->execute();
		$laLigne = $st->fetch();
		if($laLigne['nblignesfrais'] == 0){
			$ok = true;
		}
		return $ok;
	}
/**
 * Retourne le dernier mois en cours d'un visiteur
 
 * @param $idVisiteur 
 * @return le mois sous la forme aaaamm
*/	
	public function dernierMoisSaisi($idVisiteur){
		$req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$st->execute();
		$laLigne = $st->fetch();
		$dernierMois = $laLigne['dernierMois'];
		return $dernierMois;
	}
	
/**
 * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés
 
 * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
 * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function creeNouvellesLignesFrais($idVisiteur,$mois){
		$dernierMois = $this->dernierMoisSaisi($idVisiteur);
		$laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur,$dernierMois);
		if($laDerniereFiche['idEtat']=='CR'){
				$this->majEtatFicheFrais(?, ?,'CL');	
		}
		$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values(?,?,0,0,now(),'CR')";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$st->bindParam(2, $mois);
		$st->execute();
		$lesIdFrais = $this->getLesIdFrais();
		foreach($lesIdFrais as $uneLigneIdFrais){
			$unIdFrais = $uneLigneIdFrais['idfrais'];
			$req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values(?,?,?,0)";
			$st = PdoGsb::$monPdo->prepare($req);
			$st->bindParam(1, $idVisiteur);
			$st->bindParam(2, $mois);
			$st->bindParam(3, $unIdFrais);
			$st->execute();
		 }
	}
/**
 * Crée un nouveau frais hors forfait pour un visiteur un mois donné
 * à partir des informations fournies en paramètre
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $libelle : le libelle du frais
 * @param $date : la date du frais au format français jj//mm/aaaa
 * @param $montant : le montant
*/
	public function creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant){
		$dateFr = dateFrancaisVersAnglais($date);
		$req = "insert into lignefraishorsforfait 
		values('',?,?,?,?,?)";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$st->bindParam(2, $mois);
		$st->bindParam(3, $libelle);
		$st->bindParam(4, $dateFr);
		$st->bindParam(5, $montant);
		$st->execute();
	}
/**
 * Supprime le frais hors forfait dont l'id est passé en argument
 
 * @param $idFrais 
*/
	public function supprimerFraisHorsForfait($idFrais){
		$req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idFrais);
		$st->execute();
	}
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais
 
 * @param $idVisiteur 
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
*/
	public function getLesMoisDisponibles($idVisiteur){
		$req = "SELECT DISTINCT L.mois AS mois
		FROM fichefrais F, lignefraisforfait L
		WHERE F.idvisiteur = L.idvisiteur
		AND F.idvisiteur =  '$idVisiteur'
		AND quantite > 0
		ORDER BY F.mois DESC ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		    "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
	
/**
 * Retourne les mois qui pour lesquels les fiches de frais sont remplies et ne sont pas encore remboursées
 
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
*/
	public function getLesMoisDisponiblesComptable(){
		$req = "SELECT DISTINCT L.mois AS mois
		FROM fichefrais F, lignefraisforfait L
		WHERE F.idvisiteur = L.idvisiteur
		AND L.mois NOT IN (SELECT DISTINCT L.mois 
						FROM fichefrais F, lignefraisforfait L
						WHERE F.idvisiteur = L.idvisiteur
						AND quantite = 0
						OR idEtat = 'RB')
		ORDER BY F.mois DESC";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		    "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}	
/**
 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
*/	
	public function getLesInfosFicheFrais($idVisiteur,$mois){
		$req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs, 
			ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
			where fichefrais.idvisiteur = ? and fichefrais.mois = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$st->bindParam(2, $mois);
		$res = $st->execute();
		$laLigne = $res->fetch();
		return $laLigne;
	}
/**
 * Modifie l'état et la date de modification d'une fiche de frais
 
 * Modifie le champ idEtat et met la date de modif à aujourd'hui
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 */
 
	public function majEtatFicheFrais($idVisiteur,$mois,$etat){
		$req = "update ficheFrais set idEtat = ?, dateModif = now() 
		where fichefrais.idvisiteur = ? and fichefrais.mois = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $etat);
		$st->bindParam(2, $idVisiteur);
		$st->bindParam(3, $mois);
		$st->execute();
	}

	public function getStatut($idVisiteur){
		$req = "SELECT statut FROM Employe WHERE id = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $idVisiteur);
		$laLigne = $st->execute();
		return $laLigne['statut'];
	}


	public function estACloturer($mois) {
		//Annee et mois de la fiche frais
		$moisFichefrais = $mois;
		$numAnneeFF = substr($moisFichefrais, 0, 4);
		$numMoisFF = substr($moisFichefrais, 4, 2);

		//Jour, anne et mois actuel
		$dateActuelle = getJour(date("d/m/Y")).getMois(date("d/m/Y"));
		$numJour = substr($dateActuelle, 0, 2);
		$numAnnee = substr($dateActuelle, 2, 4);
		$numMois = substr($dateActuelle, 6, 2);

		//Si on est avant le 10 du prochain mois : false
		$bool = false;
		if($numMoisFF == 12 && $numAnnee == $numAnneeFF+1) {
			if($numMois > 1) {
				$bool = true;
			}
			else if($numJour > 10) {
				$bool = true;
			}
		}
		else if($numAnnee > $numAnneeFF) {
			$bool = true;
		}
		else if($numMois > $numMoisFF && $numJour > 10) {
			$bool = true;
		}
		return $bool;
	}

	public function autoCloturation() {
		$req = "SELECT idVisiteur as id, mois 
		FROM fichefrais 
		WHERE idEtat = 'CR'";

		foreach(PdoGsb::$monPdo->query($req) as $laLigne) {
			if($this->estACloturer($laLigne['mois'])) {
				$this->majEtatFicheFrais($laLigne['id'], $laLigne['mois'],'CL');
			}
		}
	}
		
	/**
	* Retourne l'id, le nom et le prénom des visiteurs qui ont une fiche de frais pour un mois donné

	* @param $mois sous la forme aaaamm
	* @return un tableau contenant les champs des visiteur
	*/
	
	public function getLesVisiteurs($mois) {
		$req = "SELECT id, nom, prenom FROM Employe, ficheFrais WHERE Employe.id = ficheFrais.idVisiteur AND ficheFrais.mois = ?";
		$st = PdoGsb::$monPdo->prepare($req);
		$st->bindParam(1, $mois);
		$res = $st->query();
		$lesVisiteurs = array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$id = $laLigne['id'];
			$nom = $laLigne['nom'];
			$prenom = $laLigne['prenom'];
			$lesVisiteurs["$id"]=array(
		    "id"=>"$id",
		    "nom"  => "$nom",
			"prenom"  => "$prenom"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesVisiteurs;
	}
	
	public function getLesFichesMoisPrecedent($date) {
		$req =  1;
	}
}

?>