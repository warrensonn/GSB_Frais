<?php
/**
 * Vue État de Frais des fiches mise en paiement
 *
 * PHP Version 7
 * 
 * L'utilisateur comptable à la possibilité de modifier l'état des fiches saisies pour les passer de 'MP' à 'RB'
 *
 * @category  PPE
 * @package   GSB
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<script type="text/javascript">
function checkAllBox(ref, name) {
    var form = ref;
     
    while (form.parentNode && form.nodeName.toLowerCase() != 'form') { form = form.parentNode; }
     
    var elements = form.getElementsByTagName('input');
     
    for (var i = 0; i < elements.length; i++) {
        if (elements[i].type == 'checkbox' && elements[i].name == name) {
            elements[i].checked = ref.checked;
        }
    }
}
</script>

<?php
if (empty($lesVisiteursRemboursement)) { ?>
	<div class="panel panel-info"> 
			<div class="panel-heading">Aucun utilisateur ne possède de fiche de frais mise en paiement
	</div> <?php
} else { ?>
<div id="contenu">
	<h2>Fiche de frais actuellement mises en paiement </h2>
	
	<form class="s12" method='POST' action='index.php?uc=suiviRemboursement&action=validerRemboursement'>
		<div class="panel panel-info">
			<div class="panel-heading">Fiche de frais</div>
				<table class="table table-bordered table-responsive">
					<tr>
						<th class='nom'> Nom </th>    
                		<th class='nom'> Mois </th>    
                		<th class='montant'> Montant </th>     
                		<th class="valide"> Valider </th> 
					</tr> <?php
					$nbFiche=1;
					foreach ( $lesVisiteursRemboursement as $unVisiteur ) { //parcours des visiteur avec fiches MP
						$lesFiches = $pdo->getLesFichesFraisRemboursement($unVisiteur['id']);
						$nom = $unVisiteur['nom'];
						$prenom = $unVisiteur['prenom']; ?>
						<tr>
							<td><?php echo $nom." ".$prenom?></td><!-- Pour avoir un affichage en forme de puce -->
							<td> </td>
							<td> </td> 
							<td> </td>
						</tr> <?php 
						
						foreach ($lesFiches as $uneFiche) {
							$montantValide = $uneFiche['montantValide'];
							$mois = substr( $uneFiche['mois'],4,2)."/".substr( $uneFiche['mois'],0,4); ?>
							<tr>
								<td><!-- Pour l'affichage en forme de puce --></td>
								<td><?php echo $mois ?></td>
								<td><?php echo $montantValide."€" ?></td>
								<td>
							   	 <label>
									<input class="filled-in" type="checkbox" name="choix[]" value="<?php echo $nbFiche;?>" id="<?php echo $nbFiche;?>">
									<span></span>
							  	 </label> <!-- Liste de checkbox pour tous cocher via JS -->
								</td>
							</tr> <?php 
						}
						$nbFiche++;//fiche suiviante
		  	  		} ?>

				</table>
		</div> 
		<!-- Permet de cocher toutes les checkbox -->
		<div class="" class="row s12">
			<div class="btn btn-info btn-lg">
				<input for="checkAll" type="checkbox" class="filled-in" onclick="checkAllBox(this, 'choix[]');" />
				<span>Tous cocher</span>
			</div>
		</div> <br><br>
		<div style="text-align: center" class="row col s12">
			<button class="btn btn-success btn-lg" type="submit" name="valider"
			onclick="return(confirm('Etes-vous sûr de vouloir valider le paiement des fiches cochées?'));">Les fiches sélectionnées sont remboursées
				<i class="glyphicon glyphicon-ok"></i>
			</button>
		</div>
	</form>
</div> <?php
} ?>