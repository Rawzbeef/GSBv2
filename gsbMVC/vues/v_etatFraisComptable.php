
	<h3>Fiche de frais du mois <?php echo $numMois."-".$numAnnee?> : </h3>
    <div class="encadre">
		<form action="index.php?uc=validerFrais&action=validerFicheFrais" method="post">
			<p>
				Etat actuel : <?php echo $libEtat?> depuis le <?php echo $dateModif?> <br> Montant validé : <?php echo $montantValide?>         
							 
			</p>
			<table class="listeLegere">
			   <caption>Eléments forfaitisés </caption>
				<tr>
				<?php
				foreach ( $lesFraisForfait as $unFraisForfait ) {
					$libelle = $unFraisForfait['libelle'];
				?>	
					<th> <?php echo $libelle?></th>
				<?php
				}
				?>
					<th>Situation</th>
				</tr>
				<tr>
				<?php
				foreach ($lesFraisForfait as $unFraisForfait) {
					$idFrais = $unFraisForfait["idfrais"];
					$quantite = $unFraisForfait['quantite'];
				?>
					<td class="qteForfait">
						<input class="tailleInput" type="text" name="<?php echo $idFrais;?>" value="<?php echo $quantite;?>"> 
					</td>
				<?php
				  }
				?>
					<td>
						<select id="lstEtat" name="lstEtat">
							<option value="CL" selected>Saisie clôturée</option> 
							<option value="RB">Remboursée</option>
							<option value="VA">Validée et mise en paiement</option>   	
						</select>
					</td>
				</tr>
			</table>
			<table class="listeLegere">
			   <caption>Descriptif des éléments hors forfait -<?php echo $nbJustificatifs ?> justificatifs reçus -
			   </caption>
					 <tr>
						<th class="date">Date</th>
						<th class="libelle">Libellé</th>
						<th class='montant'>Montant</th> 
						<th>Situation</th>
					 </tr>
				<?php
				  $i = 0;
				  foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
				  {
					$i++;
					$date = $unFraisHorsForfait['date'];
					$libelle = $unFraisHorsForfait['libelle'];
					$montant = $unFraisHorsForfait['montant'];
				?>
					 <tr>
						<td><?php echo $date ?></td>
						<td><?php echo $libelle ?></td>
						<td><?php echo $montant ?></td>
						<td>
							<select id="lstSituation<?php echo$i;?>" name="lstSituation<?php echo$i;?>">
								<option value="VL" selected>Validée</option> 
								<option value="RF">Refusée</option>   	
							</select>
						</td>
					 </tr>
				<?php 
				  }
				?>
			</table>
		</div>
		<div class="piedForm">
			<p>
				<input id="ok" type="submit" value="Valider la fiche de frais" size="20" />
			</p> 
		</div>
	</form>
