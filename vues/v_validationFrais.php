<?php
/**
 * Vue État de Frais des fiches de frais créées
 *
 * PHP Version 7
 * 
 * L'utilisateur peut saisir le visiteur de son choix dans une liste déroulante ainsi qu'un mois 
 * et cliquer sur search pour demander d'afficher les informations de le fiche de frais demandées
 *
 * @category  PPE
 * @package   GSB
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<!DOCTYPE html>
	<form class="s12" action="index.php?uc=validerFrais&action=voirEtatFrais" method="post">
		<h2>Les fiches de frais</h2>
			
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
					<option><?php echo 'Aucun Visiteur ne possède de fiche de frais'; ?></option>
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
		</div>
		
		<div class="" class="row col s12">
			<button class="btn btn-info btn-lg" type="submit" name="valider">Search
				<i class="glyphicon glyphicon-search"></i>
			</button>
		</div>
	</form>
<br><br>

<?php
if (!empty($messageValidationOk)) { ?>
<div class="panel panel-info"> 
        <div class="panel-heading"> <?php echo $messageValidationOk; ?>
</div> <?php
} ?>