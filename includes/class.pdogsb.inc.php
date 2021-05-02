<?php
/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Warren BEVILACQUA <bevilacqua.warren@gmail.com>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb';
    private static $mdp = 'secret';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom, le prénom et le statut sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom, visiteur.statut AS statut '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unNbJustificatifs', $nbJustificatifs, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($dernierMois < $mois) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO fichefrais (idvisiteur, mois, nbjustificatifs, '
                . 'montantvalide, datemodif, idetat) '
                . "VALUES (:unIdVisiteur, :unMois, 0, 0, now(), 'CR')"
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->execute();

            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur, mois, '
                . 'idfraisforfait, quantite) '
                . 'VALUES (:unIdVisiteur, :unMois, "ETP", 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->execute();

            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur, mois, '
                . 'idfraisforfait, quantite) '
                . 'VALUES (:unIdVisiteur, :unMois, "KM", 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->execute();

            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur, mois, '
                . 'idfraisforfait, quantite) '
                . 'VALUES (:unIdVisiteur, :unMois, "NUI", 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->execute();

            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur, mois, '
                . 'idfraisforfait, quantite) '
                . 'VALUES (:unIdVisiteur, :unMois, "REP", 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->execute();
        }       
        $lesIdFrais = $this->getLesIdFrais();
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     * @param String $etat       Etat du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant, $etat) 
    {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant, :unEtat) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idetat = :unEtat, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Permet la sélection des mois pour lesquelles une fiche frais créée existe sous l'id 'CR'
     * afin de les mettre dans une liste pour sélectionner les fiches d'un visiteur suivant le mois
     *
     * @return un tableau avec les mois
     */
	public function getLesMois(){ 
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois as mois '
            . 'FROM  fichefrais '
            . 'WHERE fichefrais.idetat = "CR"'
            . 'ORDER BY fichefrais.mois desc '
        );
        $requetePrepare->execute();
		$lesMois = array();
		while($laLigne = $requetePrepare->fetch())	{
			$mois = $laLigne['mois'];
			$numAnnee =substr($mois,0,4);
			$numMois =substr($mois,4,2);
			$lesMois["$mois"]=array(
					"mois"=>"$mois",
					"numAnnee"  => "$numAnnee",
					"numMois"  => "$numMois"
			);
		}
		return $lesMois;
	}
	
    /**
     * Permet la sélection des mois pour lesquels au moins une fiche frais validée existe
     * pour les mettre dans une liste pour sélectionner les fiche d'un visiteur suivant le mois
     *
     * @return un tableau avec les mois
     */
    public function getLesMoisValides() { 
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois as mois '
            . 'FROM  fichefrais '
            . 'WHERE fichefrais.idetat = "VA"'
            . 'ORDER BY fichefrais.mois desc '
        );
        $requetePrepare->execute();
        $lesMois = array();
        while($laLigne = $requetePrepare->fetch())	{
            $mois = $laLigne['mois'];
            $numAnnee =substr($mois,0,4);
            $numMois =substr($mois,4,2);
            $lesMois["$mois"]=array(
                    "mois"=>"$mois",
                    "numAnnee"  => "$numAnnee",
                    "numMois"  => "$numMois"
            );
        }
        return $lesMois;
    }

    /**
     * Selectionne tous les champs de la table fiche frais suivant la variable mois
     *
     * @param String $mois Mois sous la forme aaaamm
     *
     * @return un tableau contenant toutes les valeurs de la table fiche frais pour le mois donné
     */
	public function getLesFichesFrais($mois){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * '
            . 'FROM fichefrais '
            . 'INNER JOIN visiteur ON idVisiteur=id '
            . 'WHERE mois= :unMois'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
		$requetePrepare->execute();
		$lesFiches = array();
		while($laLigne = $requetePrepare->fetch())	{
			$id = $laLigne['id'];
			$nom = $laLigne['nom'];
			$prenom = $laLigne['prenom'];
			$montant = $laLigne['montantValide'];
			$lesFiches["$id"]=array(
					"id"=>"$id",
					"nom"  => "$nom",
					"prenom"  => "$prenom",
					"montantValide" => "$montant"
			);
		}
		return $lesFiches;
	}

    /**
     * Recupère toutes les fiches d'un visiteurs qui sont mises en paiement (MP)
     *
     * @param String $visiteur qui est id dans la BDD
     *
     * @return un tableau de fiche frais
     */
	
	public function getLesFichesFraisRemboursement($visiteur){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * FROM fichefrais '
            . 'INNER JOIN visiteur ON idVisiteur=id '
            . 'WHERE id= :unIdVisiteur ' 
            . 'AND idEtat="MP"'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $visiteur, PDO::PARAM_STR);
		$requetePrepare->execute();
		return $requetePrepare->fetchAll();
	}
	
    /**
     * Recupère tous les visiteurs ayant une fiche de frais mise en paiement (MP)
     *
     * @return un tableau d'information de la table visiteur 
     */	
	public function getLesVisiteursRemboursement(){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT DISTINCT(id), nom, prenom '
            . 'FROM fichefrais '
            . 'INNER JOIN visiteur ON idVisiteur=id '
            . 'WHERE idEtat="MP" '
        );
		$requetePrepare->execute();
		return $requetePrepare->fetchAll ();
	}
	
    /**
     * Modifie idEtat d'une fiche définie par le visiteur et le mois en paramètre (passage de MP en RB)
     *	
     * @param String $visiteur ID du visiteur
     * @param String $mois sous la forme aaaamm
     */	
	public function modificationRemboursement($idVisiteur, $mois){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET idetat="RB" '
            . 'WHERE idvisiteur= :unIdVisiteur ' 
            . 'AND mois= :unMois '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
		$requetePrepare->execute();
	}
	
    /**
     * retourne les informations des fiches de frais des visiteurs pour lequelles le paiement n'a pas encore été fait
     *
     * @return un tableau d'information de la table visiteur
     *
     */
	public function getlesFichesdeFrais() {
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT idVisiteur, nom, prenom, mois, montantValide '
			. 'FROM visiteur '
            . 'INNER JOIN fichefrais ON visiteur.id = fichefrais.idVisiteur '
            . 'INNER JOIN etat ON etat.id = fichefrais.idEtat '
			. 'WHERE fichefrais.idEtat = "VA" '
        );
		$requetePrepare->execute();
		return $requetePrepare->fetchAll();
	}

    /**
     * Met à jour la FicheFrais identifiée par l'idVisiteur et le mois en modifiant la dernière date de modification et l'état en passant de VA à MP
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       sous la forme aaaamm   
     *
     */
	public function paiementFicheFrais($idVisiteur, $mois) {
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET datemodif = NOW(), idetat = "MP" '
			. 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
		$requetePrepare->execute();
	}

    /**
     * Cette fonction permet de sélectionner tous les visiteurs possédant une fiche frais qui est sous l'id etat (CR)
     *
     * @return un tableau avec les clés de la table visiteur avec sa valeur
     */
	public function getLesVisiteurs(){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * FROM visiteur '
            . 'WHERE visiteur.id IN (SELECT fichefrais.idVisiteur FROM fichefrais WHERE fichefrais.idVisiteur=visiteur.id AND idEtat= "CR")'
        );
		$requetePrepare->execute();
		return $requetePrepare->fetchAll ();
	}
	
    /**
     * Cette fonction permet de sélectionner tous les visiteurs possédant une fiche de frais qui a été validé (VA)
     * 
     * @return un tableau avec les clés de la table visiteur avec sa valeur
     */
    public function getLesVisiteursValides(){
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * FROM visiteur '
            . 'WHERE visiteur.id IN (SELECT fichefrais.idVisiteur FROM fichefrais WHERE fichefrais.idVisiteur=visiteur.id AND idEtat= "VA")'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll ();
    }


    /**
     * Cette fonction permet de mettre à jour la table lignefraisforfait (Kilomètre, Etape, Nuit Hotel et repas)
     * qui récupère toutes les informations des frais suivant le visiteur
     * 
     * @param String  $idVisiteur ID du visiteur
     * @param String  $mois       Sous la forme aaaamm
     * @param Integer $etp        nombre d'étape
     * @param Integer $km         nombre de kilomètre
     * @param Integer $nui        nombre de nuitée à l'hotel
     * @param Integer $rep        nombre de repas
     */
	public function majInfoFraisForfaitise($idVisiteur, $mois, $etp, $km, $nui, $rep){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraisforfait SET quantite= :etp ' 
            . 'WHERE idVisiteur= :unIdVisiteur ' 
            . 'AND idFraisForfait="ETP" '
            . 'AND mois= :unMois');
        $requetePrepare->bindParam(':etp', $etp, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
		$requetePrepare->execute();


		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraisforfait SET quantite= :km ' 
            . 'WHERE idVisiteur= :unIdVisiteur '
            . 'AND idFraisForfait="KM" '
            . 'AND mois= :unMois');
        $requetePrepare->bindParam(':km', $km, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();


		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraisforfait SET quantite= :nui ' 
            . 'WHERE idVisiteur= :unIdVisiteur ' 
            . 'AND idFraisForfait="NUI" ' 
            . 'AND mois= :unMois');
        $requetePrepare->bindParam(':nui', $nui, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();


		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraisforfait SET quantite= :rep ' 
            . 'WHERE idVisiteur= :unIdVisiteur ' 
            . 'AND idFraisForfait="REP" ' 
            . 'AND mois= :unMois');
        $requetePrepare->bindParam(':rep', $rep, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

		//total du tarif avec la valeur des hors forfait
		$total=($etp * 110.00)+($km * 0.62)+($nui * 80.00)+($rep * 25.00);

		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais SET montantValide= :total ' 
            . 'WHERE idVisiteur= :unIdVisiteur '
            . 'AND mois= :unMois');
        $requetePrepare->bindParam(':total', $total, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
		$requetePrepare->execute();
	}
	
    /**
     * Cette fonction a pour but de refuser un frais hors forfait
     * En cliquant sur refuser, le libellé tronquer à le mot REFUSER qui se rajoute devant pour montrer qu'il est bien REFUSER 
     * et l'état du frais change en REF
     *
     * @param Integer $idFrais id lignefraishorsforfait
     */	
	public function refuserFrais($idFrais){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE id=:idFrais '
        );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
		$requetePrepare->execute();
		foreach ($requetePrepare->fetchAll() as $unResultat){
			$libelle=$unResultat['libelle'];
		}
		$libelle='REFUSER '.$libelle;
		$libelle=substr($libelle,0,100);

		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait SET libelle = :libelle, etat="REF" ' 
            . 'WHERE id=:idFrais ' 
            . 'AND etat NOT IN ("REF")'
        );
        $requetePrepare->bindParam(':libelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
		$requetePrepare->execute();
	}
	
    /**
     * Cette fonction valide la fiche de frais pour un visiteur et un mois donné en paramètre 
     * Elle met donc à jour la fiche de frais en VA
     *
     * @param String $idFicheVisiteur id fiche du visiteur
     * @param String $leMois          mois sous la forme aaaamm
     */
	public function validerFiche($idFicheVisiteur, $leMois){
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais SET dateModif = now(), idEtat = "VA" '
            . 'WHERE idVisiteur=:unIdFicheVisiteur '
            . 'AND mois=:leMois'
        );
        $requetePrepare->bindParam(':unIdFicheVisiteur', $idFicheVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':leMois', $leMois, PDO::PARAM_STR);
		$requetePrepare->execute();
	}
	
    /**
     * Cette fonction permet de reporter des frais au mois suivant
     * Selection des informations de la ligne de frais hors forfait suivant l'id du frais hors forfait
     * pour stocker les informations dans une variable 
     * Supprime la ligne hors forfait suivant l'id du frais
     *
     * @param Integer $idFrais ID de lignefraishorsforfait
     *
     * @return le resultat de la requete select ($req executé en premier)
     */
	public function reporterFrais($idFrais){		
		$requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE id=:idFrais '
        );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
		$requetePrepare->execute();

		$requetePrepare1= PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE id=:idFrais'
        );
        $requetePrepare1->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
		$requetePrepare1->execute();

		return $requetePrepare->fetchAll();
	}	

    /** 
     * Cette fonction renvoie le nom et le prénom associé dans la table visiteur à l'idvisiteur en paramètre
     * 
     * @param String $idVisiteur ID du visiteur
     * 
     * @return un tableau contenant champ nom et prenom
     */
    public function selectNomPrenom($idVisiteur) {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT nom, prenom FROM visiteur '
            . 'WHERE id = :idVisiteur'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        
        return $requetePrepare->fetch();
    }


  /**
   * SHA2
   *
   * @param String $mdp   mot de passe saisi
   * 
   * @return mot de passe hashé
   */
  public function SHA2($mdp) {
    $requetePrepare = PdoGSB::$monPdo->prepare(
      'SELECT SHA2(:mdp, 224) '
    );
      $requetePrepare->bindParam(':mdp', $mdp, PDO::PARAM_STR);
  	  $requetePrepare->execute();
      return $requetePrepare->fetch()[0];
  }

}
?>

