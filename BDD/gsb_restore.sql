-- Script de restauration de l'application "GSB Frais"

-- Administration de la base de données
CREATE DATABASE IF NOT EXISTS gsb_frais ;
GRANT SHOW DATABASES ON *.* TO userGsb@localhost IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON `gsb_frais`.* TO userGsb@localhost;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
USE gsb_frais ;

-- Création de la structure de la base de données

CREATE TABLE IF NOT EXISTS fraisforfait (
  id char(3) NOT NULL,
  libelle char(20) DEFAULT NULL,
  montant decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS etat (
  id char(2) NOT NULL,
  libelle varchar(30) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS typevisiteur (
  num int(1) NOT NULL,
  libelle varchar(15) NOT NULL,
  PRIMARY KEY (num)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS visiteur (
  id char(4) NOT NULL,
  nom char(30) DEFAULT NULL,
  prenom char(30)  DEFAULT NULL, 
  login char(20) DEFAULT NULL,
  mdp char(255) DEFAULT NULL,
  adresse char(30) DEFAULT NULL,
  cp char(5) DEFAULT NULL,
  ville char(30) DEFAULT NULL,
  dateembauche date DEFAULT NULL,
  statut int(1) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (statut) REFERENCES typevisiteur(num)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fichefrais (
  idVisiteur char(4) NOT NULL,
  mois char(6) NOT NULL,
  nbJustificatifs int(11) DEFAULT NULL,
  montantValide decimal(10,2) DEFAULT NULL,
  dateModif date DEFAULT NULL,
  idEtat char(2) DEFAULT 'CR',
  PRIMARY KEY (idVisiteur,mois),
  FOREIGN KEY (idEtat) REFERENCES etat(id),
  FOREIGN KEY (idVisiteur) REFERENCES visiteur(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lignefraisforfait (
  idvisiteur char(4) NOT NULL,
  mois char(6) NOT NULL,
  idfraisforfait char(3) NOT NULL,
  quantite int(11) DEFAULT NULL,
  PRIMARY KEY (idvisiteur,mois,idfraisforfait),
  FOREIGN KEY (idvisiteur, mois) REFERENCES fichefrais(idvisiteur, mois),
  FOREIGN KEY (idfraisforfait) REFERENCES fraisforfait(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lignefraishorsforfait (
  id int(8) NOT NULL auto_increment,
  idvisiteur char(4) NOT NULL,
  mois char(6) NOT NULL,
  libelle varchar(100) DEFAULT NULL,
  date date DEFAULT NULL,
  montant decimal(10,2) DEFAULT NULL,
  etat char(3) DEFAULT 'ACC',
  PRIMARY KEY (id),
  FOREIGN KEY (idvisiteur, mois) REFERENCES fichefrais(idvisiteur, mois)
) ENGINE=InnoDB;


-- Alimentation des données paramètres
INSERT INTO fraisforfait (id, libelle, montant) VALUES
('ETP', 'Forfait Etape', 110.00),
('KM', 'Frais Kilométrique', 0.62),
('NUI', 'Nuitée Hôtel', 80.00),
('REP', 'Repas Restaurant', 25.00);

INSERT INTO etat (id, libelle) VALUES
('RB', 'Remboursée'),
('CL', 'Saisie clôturée'),
('CR', 'Fiche créée, saisie en cours'),
('VA', 'Validée'),
('MP', 'Mise en paiement');

INSERT INTO typevisiteur (num, libelle) VALUES
(1, 'visiteur'),
(2, 'comptable');

-- Récupération des utilisateurs
INSERT INTO visiteur (id, nom, prenom, login, mdp, adresse, cp, ville, dateembauche, statut) VALUES
('a131', 'Villechalane', 'Louis', 'lvillachane', SHA2('jux7g', 224), '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 2),
('a17', 'Andre', 'David', 'dandre', SHA2('oppg5', 224), '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', 2),
('a55', 'Bedos', 'Christian', 'cbedos', SHA2('gmhxd', 224), '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', 2),
('b13', 'Bentot', 'Pascal', 'pbentot', SHA2('doyw1', 224), '11 allée des Cerises', '46512', 'Bessines', '1992-07-09', 1),
('b16', 'Bioret', 'Luc', 'lbioret', SHA2('hrjfs', 224), '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', 1),
('b50', 'Clepkens', 'Christophe', 'cclepkens', SHA2('bw1us', 224), '12 allée des Anges', '93230', 'Romainville', '2003-08-11', 1),
('c54', 'Debelle', 'Michel', 'mdebelle', SHA2('od5rt', 224), '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', 1),
('d13', 'Debelle', 'Jeanne', 'jdebelle', SHA2('nvwqq', 224), '134 allée des Joncs', '44000', 'Nantes', '2000-05-11', 1),
('d51', 'Debroise', 'Michel', 'mdebroise', SHA2('sghkb', 224), '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', 1),
('e49', 'Duncombe', 'Claude', 'cduncombe', SHA2('qf77j', 224), '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', 1);

-- Ajout de frais tests pour le jury
INSERT INTO fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat) VALUES
('b13', '202104', 0, '0.00', '2021-04-01', 'CR'),
('e49', '202103', 0, '0.00', '2021-03-01', 'CR'),
('b16', '202103', 0, '0.00', '2021-03-01', 'CR');

INSERT INTO lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite) VALUES
('b13', '202104', 'ETP', 1),
('b13', '202104', 'KM', 25),
('b13', '202104', 'NUI', 3),
('b13', '202104', 'REP', 5),

('e49', '202103', 'ETP', 2),
('e49', '202103', 'KM', 78),
('e49', '202103', 'NUI', 6),
('e49', '202103', 'REP', 14),

('b16', '202103', 'ETP', 2),
('b16', '202103', 'KM', 53),
('b16', '202103', 'NUI', 6),
('b16', '202103', 'REP', 12);

INSERT INTO lignefraishorsforfait (idVisiteur, mois, libelle, date, montant) VALUES
('b13', '202104', 'Courses', '2021-02-02', '22.5'),
('b13', '202104', 'Magasin', '2021-03-03', '43'),

('b16', '202103', 'Taxi', '2021-03-02', '40'),
('b16', '202103', 'Essence', '2021-03-03', '43'),
('b16', '202103', 'Café', '2021-03-03', '3'),
('b16', '202103', 'Essence', '2021-03-03', '43');

