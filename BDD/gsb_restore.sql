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
  mdp char(20) DEFAULT NULL,
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
('a131', 'Villechalane', 'Louis', 'lvillachane', 'jux7g', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 2),
('a17', 'Andre', 'David', 'dandre', 'oppg5', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', 2),
('a55', 'Bedos', 'Christian', 'cbedos', 'gmhxd', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', 2),
('a93', 'Tusseau', 'Louis', 'ltusseau', 'ktp3s', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', 1),
('b13', 'Bentot', 'Pascal', 'pbentot', 'doyw1', '11 allée des Cerises', '46512', 'Bessines', '1992-07-09', 1),
('b16', 'Bioret', 'Luc', 'lbioret', 'hrjfs', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', 1),
('b19', 'Bunisset', 'Francis', 'fbunisset', '4vbnd', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', 1),
('b25', 'Bunisset', 'Denise', 'dbunisset', 's1y1r', '23 rue Manin', '75019', 'paris', '2010-12-05', 1),
('b28', 'Cacheux', 'Bernard', 'bcacheux', 'uf7r3', '114 rue Blanche', '75017', 'Paris', '2009-11-12', 1),
('b34', 'Cadic', 'Eric', 'ecadic', '6u8dc', '123 avenue de la République', '75011', 'Paris', '2008-09-23', 1),
('b4', 'Charoze', 'Catherine', 'ccharoze', 'u817o', '100 rue Petit', '75019', 'Paris', '2005-11-12', 1),
('b50', 'Clepkens', 'Christophe', 'cclepkens', 'bw1us', '12 allée des Anges', '93230', 'Romainville', '2003-08-11', 1),
('b59', 'Cottin', 'Vincenne', 'vcottin', '2hoh9', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', 1),
('c14', 'Daburon', 'François', 'fdaburon', '7oqpv', '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11', 1),
('c3', 'De', 'Philippe', 'pde', 'gk9kx', '13 rue Barthes', '94000', 'Créteil', '2010-12-14', 1),
('c54', 'Debelle', 'Michel', 'mdebelle', 'od5rt', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', 1),
('d13', 'Debelle', 'Jeanne', 'jdebelle', 'nvwqq', '134 allée des Joncs', '44000', 'Nantes', '2000-05-11', 1),
('d51', 'Debroise', 'Michel', 'mdebroise', 'sghkb', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', 1),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', 'f1fob', '14 Place d Arc', '45000', 'Orléans', '2005-11-12', 1),
('e24', 'Desnost', 'Pierre', 'pdesnost', '4k2o5', '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05', 1),
('e39', 'Dudouit', 'Frédéric', 'fdudouit', '44im8', '18 rue de l église', '23120', 'GrandBourg', '2000-08-01', 1),
('e49', 'Duncombe', 'Claude', 'cduncombe', 'qf77j', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', 1),
('e5', 'Enault-Pascreau', 'Céline', 'cenault', 'y2qdu', '25 place de la gare', '23200', 'Gueret', '1995-09-01', 1),
('e52', 'Eynde', 'Valérie', 'veynde', 'i7sn3', '3 Grand Place', '13015', 'Marseille', '1999-11-01', 1),
('f21', 'Finck', 'Jacques', 'jfinck', 'mpb3t', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', 1),
('f39', 'Frémont', 'Fernande', 'ffremont', 'xs5tq', '4 route de la mer', '13012', 'Allauh', '1998-10-01', 1),
('f4', 'Gest', 'Alain', 'agest', 'dywvt', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', 1);

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

