<?php
/**
 * Affichage des informations d'une fiche de frais saisies
 *
 * PHP Version 7
 * 
 * L'utilisateur comptable peut modifier les frais forfaits / refuser ou reporter les frais hors forfait de la fiche saisie et finallement valider la fiche de frais
 *
 * @category  PPE
 * @package   GSB
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */ 
?>
<hr> <?php
if (empty($lesFraisForfait) || $libEtat == "Validée" || $libEtat == "Mise en paiement") { 
    // N'affiche que les fiches frais non validée ?>
    <div class="panel panel-info"> 
        <div class="panel-heading">Pas de fiche de frais à valider pour ce visiteur ce mois</div>
    </div> <?php
} else { ?>
<form action="index.php?uc=validerFrais&action=valideEtatFrais&lstVis=<?php echo $idAutreVisiteur; ?>&lstMois=<?php echo $leMois; ?>" method="post">
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
                    <td><input type="text" name="<?php echo $unFraisForfait['idfrais']; ?>" value="<?php echo $unFraisForfait['quantite']; ?>"/></td> <?php               
                }
                ?>
            </tr>
        </table> 
    </div> 
    <input class="btn btn-success btn-lg" type="submit" value="Corriger"
    onclick="return(confirm('Etes-vous sûr de vouloir corriger cette fiche de frais?'));" >
</form> <?php 
} ?>
<br> <br><br>

<?php 
if (empty($lesFraisHorsForfait)) { ?>
    <div class="panel panel-info"> 
        <div class="panel-heading">Pas de frais hors forfait pour ce visiteur ce mois</div>
    </div><br> <br> <?php
} else { 
    if ($libEtat=="Fiche créée, saisie en cours") { ?>
<div class="panel panel-info">
    <div class="panel-heading">Descriptif des éléments hors forfait - 
        <?php echo $nbJustificatifs ?> justificatifs reçus</div>
    <table class="table table-bordered table-responsive">
        <tr> 
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class='montant'>Montant</th>   
            <th class='etatFrais'></th>             
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant']; ?>
            <tr>
                <td><input type="text" name="date"
                value="<?php echo $date ?>"/></td>
                <td><input type="text" name="libelle"
                value="<?php echo $libelle ?>"/></td>
                <td><input type="text" name="supprimer"
                value="<?php echo $montant ?>" /></td>
                <td><a class="btn btn-warning btn-lg" role="button" 
                    href="index.php?uc=validerFrais&action=reporterFraisHorsForfait&id=<?php echo $unFraisHorsForfait['id']; ?>&lstVis=<?php echo $idAutreVisiteur; ?>&lstMois=<?php echo $leMois; ?>"
                    onclick="return(confirm('Etes-vous sûr de vouloir reporter cette entrée?'));">
                    <span class="glyphicon glyphicon-hourglass"></span> Reporter</a> 

                    <a class="btn btn-danger btn-lg" role="button"
                    href="index.php?uc=validerFrais&action=refuserFraisHorsForfait&id=<?php echo $unFraisHorsForfait['id']; ?>&lstVis=<?php echo $idAutreVisiteur; ?>&lstMois=<?php echo $leMois; ?>"
                    onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette entrée?'));">
                    <span class="glyphicon glyphicon-trash"></span> Refuser</a> 
	            </td>
            </tr>
            <?php
        }
        ?>
    </table>
</div> <?php
    }
} ?>
<br> 
<?php
if (!empty($lesFraisForfait)) {
    if ($libEtat=="Fiche créée, saisie en cours") { ?>
    <div style="text-align: center">
        <a class="btn btn-success btn-lg" role="button"
        href="index.php?uc=validerFrais&action=validerFicheFrais&lstVis=<?php echo $idAutreVisiteur; ?>&lstMois=<?php echo $leMois; ?>"
        onclick="return(confirm('Etes-vous sûr de vouloir valider cette fiche de frais?'));">
        <span class="glyphicon glyphicon-ok"></span> Valider fiche de frais</a> 
    </div> <?php
    }
} ?>
<br> <br>