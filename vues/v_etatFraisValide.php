
<?php
/**
 * Vue État de Frais des fiches validées
 *
 * PHP Version 7
 * 
 * L'utilisateur comptable à la possibilité de valider la mise en paiement des fiches de frais validées
 *
 * @category  PPE
 * @package   GSB
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<hr> <?php
if (empty($lesFraisForfait) || $libEtat == "Fiche créée, saisie en cours" || $libEtat == "Mise en paiement") { 
    // n'affiche que les fiches de frais déjà validée ?>
    <div class="panel panel-info"> 
        <div class="panel-heading">Pas de fiche de frais à mettre en paiement pour ce visiteur ce mois</div>
    </div> <?php
} else { ?>
    <div class="panel panel-primary">
        <div class="panel-heading">Fiche de frais du mois 
            <?php echo $numMois . '-' . $numAnnee ?> : </div>
        <div class="panel-body">
            <strong><u>Etat :</u></strong> <?php echo $libEtat ?>
            depuis le <?php echo $dateModif ?> <br> 
            <strong><u>Montant validé :</u></strong> <?php echo $montantValide ?>
        </div>
    </div>
    <div class="panel panel-info"> 
        <div class="panel-heading">Eléments forfaitisés</div>
        <table class="table table-bordered table-responsive">
            <tr> <?php
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $libelle = $unFraisForfait['libelle']; ?>
                    <th> <?php echo htmlspecialchars($libelle) ?></th> <?php      
                } ?> 
            </tr>
            <tr> <?php    
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $quantite = $unFraisForfait['quantite']; ?>
                    <td><span type="text"> <?php echo $unFraisForfait['quantite']; ?></span></td> <?php               
                }
                ?> 
            </tr>
        </table> 
    </div>  <br> <br><br>

    <?php 
    if (empty($lesFraisHorsForfait)) { ?>
    <div class="panel panel-info"> 
        <div class="panel-heading">Pas de frais hors forfait pour ce visiteur ce mois</div>
    </div><br> <br> <?php
    } else { ?>
    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des éléments hors forfait - 
            <?php echo $nbJustificatifs ?> justificatifs reçus</div>
            <table class="table table-bordered table-responsive">
                <tr> 
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>
                    <th class='montant'>Montant</th>   
                    <th class='etatFrais'></th>             
                </tr> <?php
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $date = $unFraisHorsForfait['date'];
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $montant = $unFraisHorsForfait['montant']; ?>
                    <tr>
                        <td><span type="text"> <?php echo $date ?></span></td>
                        <td><span type="text"> <?php echo $libelle ?></span></td>
                        <td><span type="text"> <?php echo $montant ?></span></td>
                    </tr> <?php
                  } ?>
            </table>
    </div> <?php
    } 
} ?>
<br> 
<?php
if (!empty($lesFraisForfait)) {
    if ($libEtat=="Validée") { ?>
    <div style="text-align: center">
        <a class="btn btn-success btn-lg" role="button"
        href="index.php?uc=paiement&action=mettrePaiement&lstVis=<?php echo $idAutreVisiteur; ?>&lstMois=<?php echo $leMois; ?>"
        onclick="return(confirm('Etes-vous sûr de vouloir mettre en paiement cette fiche de frais?'));">
        <span class="glyphicon glyphicon-ok"></span> Mettre en paiement la fiche de frais</a> 
    </div> <?php
    } 
} ?>
<br><br>