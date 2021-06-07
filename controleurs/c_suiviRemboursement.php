<?php
/** Mettre à jour les fiches mise en paiement
 *  -------
 *  @file
 *  @brief L'utilisateur comptable peut modifier l'état des fiches de frais déjà mise en paiement en les cochant.
 *  Les fiches sont alors notées comme 'remboursées'
 * 
 * @category  PPE
 * @package   GSB
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = $_REQUEST ['action'];
switch ($action) {
	case 'affichage': { // Affiche chaque personne ayant au moins une fiche de frais en état mise en paiement avec l'ensemble de ses fiches de frais mise en paiement
		$lesVisiteursRemboursement = $pdo->getLesVisiteursRemboursement(); //visiteur avec des fiches MP
		if(!$lesVisiteursRemboursement){  // Si aucune fiche est actuellement en mise en paiement
			$message = "Aucune fiche de frais mise en paiement";
			include ("vues/v_message.php");
		} else{
			include("vues/v_remboursementFrais.php");
		}
		break;
	}
	
	case 'validerRemboursement': { // Valide le remboursement des fiches de frais cochées
		$i=1; // i=1 car la checkbox commence a 1, representant le numero de la fiche
		$lesVisiteursRemboursement = $pdo->getLesVisiteursRemboursement();
		foreach ( $lesVisiteursRemboursement as $unVisiteur ) { // on parcours les visiteur avec une fiche MP
			$lesFiches = $pdo->getLesFichesFraisRemboursement($unVisiteur['id']);
			foreach ($lesFiches as $uneFiche){ //on recupere toutes les fiche du visiteur
				if(isset($_POST['choix'])) { //seulement si on a cocher une checkbox
					foreach($_POST['choix'] as $case){ //parcour des checkbox
						if($case==$i) { //il faut que le numero de la fiche et de la checkbox soit egaux
							$pdo->modificationRemboursement($uneFiche['idVisiteur'], $uneFiche['mois']);
						}
					}
				}
			$i++; // fiche suivante
			}
		}
		if(!isset($_POST['choix'])){
			$message = "Vous n'avez rien coché";
			include ("vues/v_message.php");
		} else { // renvoie l'affichage mis à jour, c'est à dire sans les fiches de frais déjà notées comme remboursées
			$lesVisiteursRemboursement = $pdo->getLesVisiteursRemboursement();
			$message = "Les fiches sélectionnées ont bien été enregistréés comme remboursées";
			include("vues/v_message.php");
			include("vues/v_remboursementFrais.php");
		}
	}
}
?>