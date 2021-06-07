<?php
/** Vue Liste des frais au forfait
 *  -------
 *  @file
 *  @brief Affichage des frais forfaits de l'utilisateur 
 * 
 *  @category  PPE
 *  @package   GSB
 *  @author    Réseau CERTA <contact@reseaucerta.org>
 *  @author    José GIL <jgil@ac-nice.fr>
 *  @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com>
 *  @copyright 2017 Réseau CERTA
 *  @license   Réseau CERTA
 *  @version   GIT: <0>
 *  @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<!DOCTYPE html>
<div class="row">    
    <h2>Renseigner ma fiche de frais du mois 
        <?php echo $numMois . '-' . $numAnnee ?>
    </h2>
    <h3>Eléments forfaitisés</h3>
    <div class="col-md-4">
        <form method="post" 
              action="index.php?uc=gererFrais&action=validerMajFraisForfait" 
              role="form">
            <fieldset>       
                <?php
                $total=0;
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; 
                    $total+=$quantite;?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                    </div> <?php               
                }
                ?>
                <button class="btn btn-success" type="submit"> <?php 
                if (!$total==0) {
                    echo 'Modifier'; ?> </button> <?php
                } else {
                    echo 'Ajouter'; ?> </button> <?php
                    } ?>
                <button class="btn btn-danger" type="reset">Revenir</button>
            </fieldset>
        </form>
    </div>
</div>
