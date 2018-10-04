<div id="contenu">
	<h2>Mes fiches de frais</h2>
	<h3>Visiteur à sélectionner : </h3>
	<form action="index.php?uc=validerFrais&action=voirFicheAValider" method="post">
		<div class="corpsForm">

			<p>

				<label for="lstVisiteur" accesskey="n">Visiteur: </label>
				<select id="lstVisiteur" name="lstVisiteur">
					<?php
						foreach($lesVisiteurs as $unVisiteur)
						{
							$idV = $unVisiteur['id'];
							$prenomV = $unVisiteur['prenom'];
							$nomV =  $unVisiteur['nom'];
							if($idV == $visiteurASelectionner){
							?>
								<option selected value="<?php echo $idV ?>"><?php echo  $prenomV." ".$nomV ?> </option>
							<?php 
							}
							else{ ?>
								<option value="<?php echo $idV ?>"><?php echo  $prenomV." ".$nomV ?> </option>
							<?php 
							}
						}
					?>    
				</select>
			</p>
			<p>
				<label for="leMois">Mois : </label>
				<input type="text" value="<?php echo $numMois."/".$numAnnee; ?>" disabled="disabled"></input>
			</p>
		</div>
		<div class="piedForm">
			<p>
				<input id="ok" type="submit" value="Valider" size="20" />
				<input id="annuler" type="reset" value="Effacer" size="20" />
			</p> 
		</div>

	</form>