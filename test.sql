SET foreign_key_check = 0;
DROP TABLE  IF EXISTS paniers,commandes, mangas, users, typeMangas, etats, genre, mangaDuGenre;
SET foreign_key_check = 1;


-- --------------------------------------------------------
-- Structure de la table typeMangas (anciennement typeproduits )
--
CREATE TABLE IF NOT EXISTS typeMangas (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;

-- Contenu de la table typeproduits
INSERT INTO typeMangas (id, libelle) VALUES
(1, 'TV'),
(2, 'OVA'),
(3, 'Movie');

-- --------------------------------------------------------
-- Structure de la table etats

CREATE TABLE IF NOT EXISTS etats (
  id int(11) NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 ;
-- Contenu de la table etats
INSERT INTO etats (id, libelle) VALUES
(1, 'A preparer'),
(2, 'Expedier');

-- --------------------------------------------------------
-- Structure de la table mangas ( anciennement produits )

CREATE TABLE IF NOT EXISTS mangas (
  id int(10) NOT NULL AUTO_INCREMENT,
  typeManga_id int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
--  descr text DEFAULT NULL,
  prix float(6,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  dispo tinyint(4) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (typeManga_id) REFERENCES typeMangas (id)
) DEFAULT CHARSET=utf8 ;



INSERT INTO mangas (id,typeManga_id,nom,prix,photo,dispo) VALUES
(1 ,1, 'Death note','40','imageProduit.jpg',1),
(2 ,1,'Overlord','10','overlord.jpg',1),
(3 ,1, 'Full metal alchemist','60','fma.jpg',1),
(4 ,1,'GREAT TEACHER ONIZUKA','50','gto.jpg',1),
(5 ,1, 'Claymore','20','claymore.jpg',1);



-- --------------------------------------------------------
-- Structure de la table user
-- valide permet de rendre actif le compte (exemple controle par email )
/****************************************
*
*
*
*        GENRE
*
*
*
 ***************************************/


CREATE TABLE IF NOT EXISTS genre (
  id int(10) NO NULL AUTO_INCREMENT,
  libelle varchar(40) NOT NULL,
  PRIMARY KEY(id)
)DEFAULT CHARSET=utf8 ;

/*
INSERT INTO genre (id,libelle) VALUES
(1,'Action'),
(2,'Mystery'),
(3,'Adventure'),
(4,'Fantasy'),
(5,'Magic'),
(6,'Thriller');
*/

CREATE TABLE IF NOT EXISTS mangaDuGenre (
  id int(10) NOT NULL AUTO_INCREMENT,
  id_manga int(10) NOT NULL,
  id_genre int(10) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id_manga) REFERENCES manga(id)
  FOREIGN KEY(id_genre) REFERENCES genre(id)
);
/*
INSERT INTO mangaDuGenre(id,id_manga,id_genre) VALUES
(null,1,6),
(null,1,2);
*/
CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  login varchar(255) NOT NULL,
  nom varchar(255) NOT NULL,
  code_postal varchar(255) NOT NULL,
  ville varchar(255) NOT NULL,
  adresse varchar(255) NOT NULL,
  valide tinyint NOT NULL,
  droit varchar(255) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;

-- Contenu de la table users
INSERT INTO users (id,login,password,email,valide,droit) VALUES
(1, 'admin', 'admin', 'admin@gmail.com',1,'DROITadmin'),
(2, 'vendeur', 'vendeur', 'vendeur@gmail.com',1,'DROITadmin'),
(3, 'client', 'client', 'client@gmail.com',1,'DROITclient'),
(4, 'client2', 'client2', 'client2@gmail.com',1,'DROITclient'),
(5, 'client3', 'client3', 'client3@gmail.com',1,'DROITclient');



-- --------------------------------------------------------
-- Structure de la table commandes
CREATE TABLE IF NOT EXISTS commandes (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  date_achat date NOT NULL,
  etat_id int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_commandes_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_commandes_etats FOREIGN KEY (etat_id) REFERENCES etats (id)
) DEFAULT CHARSET=utf8 ;



-- --------------------------------------------------------
-- Structure de la table paniers
CREATE TABLE IF NOT EXISTS paniers (
  id int(11) NOT NULL AUTO_INCREMENT,
  quantite int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  dateAjoutPanier timestamp default CURRENT_TIMESTAMP,
  user_id int(11) NOT NULL,
  manga_id int(11) NOT NULL,
  commande_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_paniers_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_paniers_mangas FOREIGN KEY (manga_id) REFERENCES mangas (id),
  CONSTRAINT fk_paniers_commandes FOREIGN KEY (commande_id) REFERENCES commandes (id)
) DEFAULT CHARSET=utf8 ;


