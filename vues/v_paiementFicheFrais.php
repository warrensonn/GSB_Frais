<?php
/** Affichage liste utilisateurs / mois ayant au moins une fiche validée
 *  -------
 *  @file
 *  @brief L'utilisateur comptable peut valider la mise en paiement des fiches de frais déjà validées
 * 
 *  @category  PPE
 *  @package   GSB
 *  @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 *  @version   GIT: <0>
 *  @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<!DOCTYPE html>
<div id="contenu">
	<form class="s12" action="index.php?uc=paiement&action=voirEtatFraisValide" method="post">
		<h2>Mise en paiement des Fiches de Frais</h2> 

		<div class="input-field col s8">
			<label>Visiteur :</label>
			<select id="lstVis" name="lstVis">
				<?php
				if(!empty($lesVisiteurs)){ ?>
					<?php
					foreach ($lesVisiteurs as $unVisiteur)
					{					
						?>
							<option value="<?php echo $unVisiteur['id'] ?>" <?php if($idAutreVisiteur == $unVisiteur['id']) { ?> selected <?php } ?> ><?php echo $unVisiteur['nom']." ".$unVisiteur['prenom']; ?></option>
						<?php 
					}
				}else{
				?>
					<option><?php echo 'Aucun Visiteur ne possède de fiche de frais à mettre en paiement'; ?></option>
				<?php
				}
				?>
			</select>	
		</div>
		
		<div class="input-field col s8">
			<label>Mois :</label>
			<select id="lstMois" name="lstMois">
				<?php
				foreach ($lesMois as $unMois)
				{
					?>
						<option value="<?php echo $unMois['mois'] ?>" <?php if($leMois == $unMois['mois']) { ?> selected <?php } ?> ><?php echo $unMois['numMois']."/".$unMois['numAnnee']; ?></option>
					<?php
				}
				?>
			</select>
		</div> <?php
		if(!empty($lesVisiteurs)){ ?>
		<div class="" class="row col s12">
			<button class="btn btn-info btn-lg" type="submit" name="valider">Search
				<i class="glyphicon glyphicon-search"></i>
			</button>
		</div> <?php } ?>
	</form>
</div> <br> <br>

<?php
if (!empty($messageMiseEnPaiementOk)) { ?>
<div class="panel panel-info"> 
        <div class="panel-heading"> <?php echo $messageMiseEnPaiementOk; ?>
</div> <?php
} ?>