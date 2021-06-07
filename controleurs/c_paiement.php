<?php
/** Mise en paiement des fiches validées
 *  -------
 *  @file
 *  @brief L'utilisateur comptable peut consulter les fiches de frais validées et valider leur mise en paiement
 * 
 * @category  PPE
 * @package   GSB
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = $_REQUEST ['action'];
switch ($action) {
	case 'affichageVisiteur' :	  // Affiche chaque visiteur ayant au moins une fiche de frais en état 'validée' avec l'ensemble de ses fiches de frais validées
		{
			$leMois=-1;
			$idAutreVisiteur=-1;

			$lesMois = $pdo->getLesMoisValides();
			$lesVisiteurs = $pdo->getLesVisiteursValides();

			include ("vues/v_paiementFicheFrais.php");

			break;
		}
	
	case 'voirEtatFraisValide' :	// Affichage des informations de la fiche de frais sélectionnées (frais forfait et hors forfait) et mise en paiement possible
		{	
			// On récupère le visiteur et le mois choisi par le comptable afin d'afficher la fiche de frais associée
			$leMois = $_REQUEST ['lstMois'];
			$idAutreVisiteur = $_REQUEST ['lstVis'];

			$lesMois = $pdo->getLesMoisValides();
			$lesVisiteurs = $pdo->getLesVisiteursValides();

			include ("vues/v_paiementFicheFrais.php");	 // Le comptable a toujours l'option de rechercher une autre fiche de frais

			$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait ( $idAutreVisiteur, $leMois );
			$lesFraisForfait = $pdo->getLesFraisForfait ( $idAutreVisiteur, $leMois );
			
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais ( $idAutreVisiteur, $leMois );	// Récupération des informations de la fiche de frais sélectionnée
			
			$numAnnee = substr ( $leMois, 0, 4 );
			$numMois = substr ( $leMois, 4, 2 );
			
			if (!empty($lesInfosFicheFrais)) {	 // on récupère les informations uniquement si une fiche existe
				$libEtat = $lesInfosFicheFrais ['libEtat'];
				$montantValide = $lesInfosFicheFrais ['montantValide'];
				$nbJustificatifs = $lesInfosFicheFrais ['nbJustificatifs'];
				$dateModif = $lesInfosFicheFrais ['dateModif'];
				$dateModif = dateAnglaisVersFrancais ( $dateModif );
			}

			include ("vues/v_etatFraisValide.php");   
			break;
		}

	case 'mettrePaiement' :	   // modifie l'état de la fiche de frais pour passer de VA à MP
		{		
			$leMois = $_REQUEST ['lstMois'];
			$idAutreVisiteur = $_REQUEST ['lstVis'];

			$lesFraisForfait = $pdo->getLesFraisForfait ( $idAutreVisiteur, $leMois );	 // On met à jour la liste à afficher

			$pdo->paiementFicheFrais($idAutreVisiteur, $leMois);   // L'état de la fiche de frais est modifié

			$lesMois = $pdo->getLesMoisValides();
			$lesVisiteurs = $pdo->getLesVisiteursValides();
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais ($idAutreVisiteur, $leMois);

			if (!empty($lesInfosFicheFrais)) {
				$libEtat = $lesInfosFicheFrais ['libEtat'];
				$montantValide = $lesInfosFicheFrais ['montantValide'];
				$nbJustificatifs = $lesInfosFicheFrais ['nbJustificatifs'];
				$dateModif = $lesInfosFicheFrais ['dateModif'];
				$dateModif = dateAnglaisVersFrancais ( $dateModif );
			}

			$messageMiseEnPaiementOk = "La fiche a bien été validée";
			include ("vues/v_paiementFicheFrais.php");
			break;
		}
}
?>