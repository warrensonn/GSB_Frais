<?php
/** Vue erreurs
 *  -------
 *  @file
 *  @brief Affichage des erreurs
 * 
 *  @category  PPE
 *  @package   GSB
 *  @author    Réseau CERTA <contact@reseaucerta.org>
 *  @author    José GIL <jgil@ac-nice.fr>
 *  @copyright 2017 Réseau CERTA
 *  @license   Réseau CERTA
 *  @version   GIT: <0>
 *  @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<!DOCTYPE html>
<div class="alert alert-danger" role="alert">
    <?php
    foreach ($_REQUEST['erreurs'] as $erreur) {
        echo '<p>' . htmlspecialchars($erreur) . '</p>';
    }
    ?>
</div>