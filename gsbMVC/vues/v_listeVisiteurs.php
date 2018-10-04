<div>
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
		</div>
		<div class="piedForm">
			<p>
				<input id="ok" type="submit" value="Valider" size="20" />
				<input id="annuler" type="reset" value="Annuler" size="20" />
			</p> 
		</div>

	</form>