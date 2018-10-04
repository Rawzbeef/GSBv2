<!-- Division pour le sommaire -->
<div id="menuGauche">
	<div id="infosUtil">

		<h2>

		</h2>

	</div>  
	<ul id="menuList">
		<li>
			Comptable :<br>
			<?php echo $_SESSION['prenom']."  ".$_SESSION['nom']  ?>
		</li>
		<li class="smenu">
			<a href="index.php?uc=validerFrais&action=choixVisiteur" title="Validation des fiches de frais">Validation des fiches de frais</a>
		</li>
		<li class="smenu">
			<a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter">Déconnexion</a>
		</li>
	</ul>

</div>
