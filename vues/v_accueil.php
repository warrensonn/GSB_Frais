<?php
/**
 * Vue Accueil
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<!DOCTYPE html>
<div id="accueil">
    <h2>
        Gestion des frais<small> - <?php echo $_SESSION['type'] ?> : 
            <?php 
            echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']
            ?></small>
    </h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12"> <?php

                        if ($_SESSION['type']=='visiteur') { // Si l'utilisateur est un visiteur ?>   
                        <a href="index.php?uc=gererFrais&action=saisirFrais"
                           class="btn btn-success btn-lg" role="button">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <br>Renseigner la fiche de frais</a>
                        <a href="index.php?uc=etatFrais&action=selectionnerMois"
                           class="btn btn-primary btn-lg" role="button">
                            <span class="glyphicon glyphicon-list-alt"></span>
                            <br>Afficher mes fiches de frais</a> <?php

                        } else { // sinon l'utilisateur est un comptable ?>
                        <a href="index.php?uc=validerFrais&action=selectionnerVisiteur"
                           class="btn btn-success btn-lg" role="button">
                            <span class="glyphicon glyphicon-ok"></span>
                            <br>Valider fiche de frais</a>
                        <a href="index.php?uc=paiement&action=affichageVisiteur"
                           class="btn btn-primary btn-lg" role="button">
                            <span class="glyphicon glyphicon-eur"></span>
                            <br>Mettre en paiement</a> 
                        <a href="index.php?uc=suiviRemboursement&action=affichage"
                           class="btn btn-info btn-lg" role="button"> 
                           <span class="glyphicon glyphicon-list"></span>
                           <br>Suivi remboursement</a> <?php
                        } ?>                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>