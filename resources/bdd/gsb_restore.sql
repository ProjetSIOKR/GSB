/* 

    modifs à effectuer:

-script de suppression des fiches de frais tout les mois sur de + 1 an
-créer un comptableGSB, avec des droits limités à ses actions
-créer un adminGSB qui a les droits
-ne plus enregistrer les mdp en clair 

Sécurité mot de passe:
-Nb essai max
-contrôler les formats de mdp mais pas trop
-Question personnel
-Envoie de mail avec code et/ou pour reinistialiser mdp  grace $_SERVER['HTTP_USER_AGENT'] + creation d'une table de device connu si pas connu envoie de code. 




*/
-- Script de restauration de l'application "GSB Frais"

-- Administration de la base de données
CREATE DATABASE gsb_frais_VersionProjet ;
GRANT SHOW DATABASES ON *.* TO visiteur@localhost IDENTIFIED BY 'visiteur';
GRANT ALL PRIVILEGES ON `gsb_frais_VersionProjet`.* TO visiteur@localhost;

GRANT SHOW DATABASES ON *.* TO comptable@localhost IDENTIFIED BY 'comptable';
GRANT ALL PRIVILEGES ON `gsb_frais_VersionProjet`.* TO comptable@localhost;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
USE gsb_frais_VersionProjet ;

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

CREATE TABLE IF NOT EXISTS role (
    id int NOT NULL AUTO_INCREMENT,
    libelle varchar(50) NOT NULL,
    PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS securisationConnexion (
     id int NOT NULL,
    tentative_mdp_id int(5) DEFAULT 0,
    tentative_a2f int(5) DEFAULT 0,
    bloque boolean NOT NULL default false,
    PRIMARY KEY (id),
    FOREIGN KEY (id) REFERENCES utilisateur(id)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS utilisateur (
  id int NOT NULL AUTO_INCREMENT,
  nom char(30) DEFAULT NULL,
  prenom char(30)  DEFAULT NULL, 
  login char(20) DEFAULT NULL,
  mdp char(20) DEFAULT NULL,
  adresse char(30) DEFAULT NULL,
  cp char(5) DEFAULT NULL,
  ville char(30) DEFAULT NULL,
  dateembauche date DEFAULT NULL,
  id_role int NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_role) REFERENCES role(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fichefrais (
  idutilisateur int NOT NULL,
  mois char(6) NOT NULL,
  nbjustificatifs int(11) DEFAULT NULL,
  montantvalide decimal(10,2) DEFAULT NULL,
  datemodif date DEFAULT NULL,
  idetat char(2) DEFAULT 'CR',
  PRIMARY KEY (idutilisateur,mois),
  FOREIGN KEY (idetat) REFERENCES etat(id),
  FOREIGN KEY (idutilisateur) REFERENCES utilisateur(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lignefraisforfait (
  idutilisateur int NOT NULL,
  mois char(6) NOT NULL,
  idfraisforfait char(3) NOT NULL,
  quantite int(11) DEFAULT NULL,
  PRIMARY KEY (idutilisateur,mois,idfraisforfait),
  FOREIGN KEY (idutilisateur, mois) REFERENCES fichefrais(idutilisateur, mois),
  FOREIGN KEY (idfraisforfait) REFERENCES fraisforfait(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lignefraishorsforfait (
  id int(11) NOT NULL auto_increment,
  idutilisateur int NOT NULL,
  mois char(6) NOT NULL,
  libelle varchar(100) DEFAULT NULL,
  date date DEFAULT NULL,
  montant decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (idutilisateur, mois) REFERENCES fichefrais(idutilisateur, mois)
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
('VA', 'Validée et mise en paiement');

INSERT INTO role (id, libelle) VALUES
(0, 'Visiteur'),
(1, 'Comptable');
-- Récupération des utilisateurs
INSERT INTO utilisateur (nom, prenom, login, mdp, adresse, cp, ville, dateembauche, id_role) VALUES
('Villechalane', 'Louis', 'lvillachane', 'jux7g', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21',0),
('Andre', 'David', 'dandre', 'oppg5', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23',0),
('Bedos', 'Christian', 'cbedos', 'gmhxd', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12',0),
('Tusseau', 'Louis', 'ltusseau', 'ktp3s', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01',0),
('Bentot', 'Pascal', 'pbentot', 'doyw1', '11 allée des Cerises', '46512', 'Bessines', '1992-07-09',0),
('Bioret', 'Luc', 'lbioret', 'hrjfs', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11',0),
('Bunisset', 'Francis', 'fbunisset', '4vbnd', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21',0),
('Bunisset', 'Denise', 'dbunisset', 's1y1r', '23 rue Manin', '75019', 'paris', '2010-12-05',0),
('Cacheux', 'Bernard', 'bcacheux', 'uf7r3', '114 rue Blanche', '75017', 'Paris', '2009-11-12',0),
('Cadic', 'Eric', 'ecadic', '6u8dc', '123 avenue de la République', '75011', 'Paris', '2008-09-23',0),
('Charoze', 'Catherine', 'ccharoze', 'u817o', '100 rue Petit', '75019', 'Paris', '2005-11-12',0),
('Clepkens', 'Christophe', 'cclepkens', 'bw1us', '12 allée des Anges', '93230', 'Romainville', '2003-08-11',0),
('Cottin', 'Vincenne', 'vcottin', '2hoh9', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18',1),
('Daburon', 'François', 'fdaburon', '7oqpv', '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11',1),
('De', 'Philippe', 'pde', 'gk9kx', '13 rue Barthes', '94000', 'Créteil', '2010-12-14',1),
('Debelle', 'Michel', 'mdebelle', 'od5rt', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23',1),
('Debelle', 'Jeanne', 'jdebelle', 'nvwqq', '134 allée des Joncs', '44000', 'Nantes', '2000-05-11',1),
('Debroise', 'Michel', 'mdebroise', 'sghkb', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17',1),
('Desmarquest', 'Nathalie', 'ndesmarquest', 'f1fob', '14 Place d Arc', '45000', 'Orléans', '2005-11-12',1),
('Desnost', 'Pierre', 'pdesnost', '4k2o5', '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05',1),
('Dudouit', 'Frédéric', 'fdudouit', '44im8', '18 rue de l église', '23120', 'GrandBourg', '2000-08-01',0),
('Duncombe', 'Claude', 'cduncombe', 'qf77j', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10',0),
('Enault-Pascreau', 'Céline', 'cenault', 'y2qdu', '25 place de la gare', '23200', 'Gueret', '1995-09-01',0),
('Eynde', 'Valérie', 'veynde', 'i7sn3', '3 Grand Place', '13015', 'Marseille', '1999-11-01',0),
('Finck', 'Jacques', 'jfinck', 'mpb3t', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10',0),
('Frémont', 'Fernande', 'ffremont', 'xs5tq', '4 route de la mer', '13012', 'Allauh', '1998-10-01',0),
('Gest', 'Alain', 'agest', 'dywvt', '30 avenue de la mer', '13025', 'Berre', '1985-11-01',0);

ALTER TABLE utilisateur
MODIFY mdp VARCHAR(255) ;
ALTER TABLE utilisateur ADD email TEXT NULL;
UPDATE utilisateur SET email = CONCAT(login,"@swiss-galaxy.com");
ALTER TABLE utilisateur ADD code CHAR(4);