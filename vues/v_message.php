<?php
/** Affichage des messages
 *  -------
 *  @file
 *  @brief Affichage des messages
 * 
 *  @category  PPE
 *  @package   GSB
 *  @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 *  @version   GIT: <0>
 *  @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<!DOCTYPE html>
<?php
if (!empty($message)) { ?> <br>
<div class="panel panel-info"> 
        <div class="panel-heading"> <?php echo $message; ?>
</div> <br><?php
} ?>
