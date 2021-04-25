<?php
/**
 * Validation des fiches de frais
 *
 * PHP Version 7
 *
 * L'utilisateur comptable à la possibilité de valider des fiches de frais en les ayant préalablement si besoin modifiées
 * au niveau des frais forfaitisés ou bien en supprimant / reportant des frais hors forfait.
 * 
 * @category  PPE
 * @package   GSB
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = $_REQUEST ['action'];
$idVisiteur = $_SESSION ['idVisiteur'];

switch ($action) {
	case 'selectionnerVisiteur' :	// Affiche chaque visiteur et mois ayant au moins une fiche de frais en état 'validée'
		{
			$leMois=-1;
			$idAutreVisiteur=-1;
			
			$lesVisiteurs = $pdo->getLesVisiteurs ();	// récupère les utilisateurs dans fichefrais ayant une fiche frais en état 'CR'
			$lesMois=$pdo->getLesMois();				// récupère les mois dans fichefrais ayant une fiche frais en état 'CR'
			include ("vues/v_validationFrais.php");
			break;
		}
	
	case 'voirEtatFrais' :		
		{
			// On récupère le visiteur et le mois choisi par le comptable afin d'afficher la fiche de frais associée
			$leMois = $_REQUEST ['lstMois'];
			$idAutreVisiteur = $_REQUEST ['lstVis'];
			
			$lesMois = $pdo->getLesMois ();
			$lesVisiteurs = $pdo->getLesVisiteurs ();
			
			include ("vues/v_validationFrais.php");
			
			// On récupère les frais forfait et hors forfait de la fiche frais sélectionnée
			$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait ( $idAutreVisiteur, $leMois );		
			$lesFraisForfait = $pdo->getLesFraisForfait ( $idAutreVisiteur, $leMois );
			
			// On récupère les informations de la fiche frais
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais ( $idAutreVisiteur, $leMois );
			
			$numAnnee = substr ( $leMois, 0, 4 );
			$numMois = substr ( $leMois, 4, 2 );
			
			if (!empty($lesInfosFicheFrais)) {	 // on récupère les informations uniquement si une fiche existe
				$libEtat = $lesInfosFicheFrais ['libEtat'];
				$montantValide = $lesInfosFicheFrais ['montantValide'];
				$nbJustificatifs = $lesInfosFicheFrais ['nbJustificatifs'];
				$dateModif = $lesInfosFicheFrais ['dateModif'];
				$dateModif = dateAnglaisVersFrancais ( $dateModif );
			}

			include ("vues/v_etatFraisComptable.php");
			break;
		}
	
	case 'valideEtatFrais' :	// Si l'utilisateur modifie les quantités des frais forfait
		{
			$etp = $_POST['ETP'];
			$km = $_POST['KM'];
			$nui = $_POST['NUI'];
			$rep = $_POST['REP'];
			$idAutreVisiteur = $_REQUEST['lstVis'];
			$leMois = $_REQUEST['lstMois'];

			$pdo->majInfoFraisForfaitise ( $idAutreVisiteur, $leMois, $etp, $km, $nui, $rep );	  // On modifie les données de la fiche de frais
			
			$lesVisiteurs = $pdo->getLesVisiteurs ();				
			$lesMois = $pdo->getLesMois (); 
			
			include ("vues/v_validationFrais.php");
			
			$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait ( $idAutreVisiteur, $leMois );
			$lesFraisForfait = $pdo->getLesFraisForfait ( $idAutreVisiteur, $leMois );	 // On récupère les nouvelles informations
			
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais ( $idAutreVisiteur, $leMois );
			
			$numAnnee = substr ( $leMois, 0, 4 );
			$numMois = substr ( $leMois, 4, 2 );
			
			$libEtat = $lesInfosFicheFrais ['libEtat'];
			$montantValide = $lesInfosFicheFrais ['montantValide'];
			$nbJustificatifs = $lesInfosFicheFrais ['nbJustificatifs'];
			$dateModif = $lesInfosFicheFrais ['dateModif'];
			$dateModif = dateAnglaisVersFrancais ( $dateModif );
			
			include ("vues/v_etatFraisComptable.php");
			
			break;
		}
	
	case 'refuserFraisHorsForfait' :	// Permet le refus d'un frais hors forfait, le visiteur pourra voir le libellé avec 'REFUSE' devant, l'état de la ligne hors forfait sera modifié en REF
		{
			$idFrais=$_REQUEST['id'];
			$pdo->refuserFrais($idFrais);	// Fonction qui applique le changement de libellé et d'état
			
			$lesVisiteurs = $pdo->getLesVisiteurs ();
			$lesMois = $pdo->getLesMois ();	

			$idAutreVisiteur = $_REQUEST ['lstVis'];
			$leMois = $_REQUEST ['lstMois'];
				
			include ("vues/v_validationFrais.php");
				
			$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait ( $idAutreVisiteur, $leMois );	 // On récupère les nouvelles informations des frais hors forfait de la fiche	
			$lesFraisForfait = $pdo->getLesFraisForfait ( $idAutreVisiteur, $leMois );
				
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais ( $idAutreVisiteur, $leMois );
				
			$numAnnee = substr ( $leMois, 0, 4 );
			$numMois = substr ( $leMois, 4, 2 );
				
			$libEtat = $lesInfosFicheFrais ['libEtat'];
			$montantValide = $lesInfosFicheFrais ['montantValide'];
			$nbJustificatifs = $lesInfosFicheFrais ['nbJustificatifs'];
			$dateModif = $lesInfosFicheFrais ['dateModif'];
			$dateModif = dateAnglaisVersFrancais ( $dateModif );
				
			include ("vues/v_etatFraisComptable.php");
			
			break;
		}
		
	case 'validerFicheFrais' :	 // L'utilisateur valide la fiche de frais, elle passe de l'idetat 'CR' à 'VA' et on enregistre la date de modification
		{
			$idAutreVisiteur = $_REQUEST ['lstVis'];
			$leMois = $_REQUEST ['lstMois'];

			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais ( $idAutreVisiteur, $leMois );
			$libEtat = $lesInfosFicheFrais ['libEtat'];
			$messageValidationOk = "La fiche a bien été validée";

			$pdo->validerFiche($idAutreVisiteur,$leMois);	// Fonction qui applique le changement d'idetat et la date de modification
			
			$lesVisiteurs = $pdo->getLesVisiteurs ();	
			$lesMois = $pdo->getLesMois ();
				
			include ("vues/v_validationFrais.php");
			
			break;
		}
	
	case 'reporterFraisHorsForfait':	// L'utilisateur reporte un frais hors forfait, celui-ci est placé dans la fiche de frais du mois suivant. Si elle n'existe pas encore, elle est crée
		{
			$idFrais=$_REQUEST['id'];	
			$idAutreVisiteur = $_REQUEST ['lstVis'];
			$leMois = $_REQUEST ['lstMois'];

			$annee=substr($leMois,0,4);
			$mois=substr($leMois,4,2)+1;	// Pour obtenir le numéro du mois suivant
			
			if($mois==13){	 // cas particulier où le frais hors forfait était sur une fiche de frais du mois de décembre
				$mois=01;
				$annee=$annee+1;
			}
			
			$anneeMois=$annee."0".$mois;
		
			$resultat=$pdo->reporterFrais($idFrais);	// Fonction qui supprime le frais hors forfait et retourne les informations du frais hors forfait supprimée
			
			$test=$pdo->creeNouvellesLignesFrais($idAutreVisiteur,$anneeMois);	  // Fonction qui créée une fiche de frais si il n'en existe pas encore pour ce mois là ainsi que les lignes de frais forfait

			foreach($resultat as $unResultat){	  // On récupère les informations du frais hors forfait supprimée dans des variables
				$mois=$unResultat['mois'];
				$libelle=$unResultat['libelle'];
				$date=$unResultat['date'];
				$montant=$unResultat['montant'];
				$etat=$unResultat['etat'];
			}
			
			$anneeDate=substr($date,0,4);
			$moisDate=substr($date,5,2);
			$jourDate=substr($date,8,2);
			
			// On recrée la ligne de frais hors forfait dans la fiche de frais du mois suivant
			$pdo->creeNouveauFraisHorsForfait($idAutreVisiteur,$anneeMois,$libelle,$jourDate."/".$moisDate."/".$anneeDate,$montant, $etat);	

			$lesVisiteurs = $pdo->getLesVisiteurs ();
			$lesMois = $pdo->getLesMois ();
				
			include ("vues/v_validationFrais.php");
				
			$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait ( $idAutreVisiteur, $leMois );	  // On récupère les frais hors forfait mis à jour du mois 
				
			$lesFraisForfait = $pdo->getLesFraisForfait ( $idAutreVisiteur, $leMois );	 
				
			$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais ( $idAutreVisiteur, $leMois );	// On récupère les informations de la fiche de frais mis à jour du mois 
				
			$numAnnee = substr ( $leMois, 0, 4 );
			$numMois = substr ( $leMois, 4, 2 );
				
			$libEtat = $lesInfosFicheFrais ['libEtat'];
			$montantValide = $lesInfosFicheFrais ['montantValide'];
			$nbJustificatifs = $lesInfosFicheFrais ['nbJustificatifs'];
			$dateModif = $lesInfosFicheFrais ['dateModif'];
			$dateModif = dateAnglaisVersFrancais ( $dateModif );
				
			include ("vues/v_etatFraisComptable.php");
			
			break;
			
		}
}
?>