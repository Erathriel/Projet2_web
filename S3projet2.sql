DROP TABLE  IF EXISTS paniers,commandes, mangas, users, typeMangas, etats;

-- --------------------------------------------------------
-- Structure de la table typeMangas (anciennement typeproduits )
--
CREATE TABLE IF NOT EXISTS typeMangas (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;

-- Contenu de la table typeproduits
-- INSERT INTO typeProduits (id, libelle) VALUES
-- (1, 'type 1'),
-- (2, 'type 2'),
-- (3, 'type 3');

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
-- Structure de la table mangas ( anciennement produits ) !

CREATE TABLE IF NOT EXISTS mangas (
  id int(10) NOT NULL AUTO_INCREMENT,
  typeManga_id int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
--  descr text DEFAULT NULL,
  nbEpisode int(10) DEFAULT NULL,
  prix float(6,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  dispo tinyint(4) NOT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_mangas_typeMangas FOREIGN KEY (typeManga_id) REFERENCES typeMangas (id)
) DEFAULT CHARSET=utf8 ;

-- INSERT INTO produits (id,typeProduit_id,nom,prix,photo,dispo,stock) VALUES
-- (1,1, 'produit 1','100','imageProduit.jpeg',1,5),
-- (2,1, 'produit 2','5.5','imageProduit.jpeg',1,4),
-- (3,2, 'produit 3','8.5','imageProduit.jpeg',1,10);

INSERT INTO mangas (id,typeManga_id,nom,nbEpisode,prix,photo,dispo,stock) VALUES
(1 ,1, 'Death note','37','40','dn2.jpg',3,10),
(2 ,1,'Overlord','12','10','overlord.jpg',3,5),
(3 ,1, 'Full metal alchemist','50','60','fma.jpg',3,2),
(4 ,1,'GREAT TEACHER ONIZUKA','72','50','gto.jpg',3,10),
(5 ,1, 'Claymore','24','20','claymore.jpg',3,10),
(6,1,'Nisekoi','24','30','nisekoi.jpg',3,10),
(7,2,'Nisekoi OVA','3','2','nisekoiOVA.jpg',3,10),
(8,2,'Nodame cantabile','3','5','nc.jpg',3,10),
(9,2,'Ajin','2','10','ajin.jpg',3,10),
(10,3,'Ghost in the shell','1','15','gits.jpg',3,10),
(11,3,'Ghost in the shell 2','1','15','gits2.jpg',3,10),
(12,3,'The empire of corpse','1','10','cempire.jpg',3,10);

-- --------------------------------------------------------
-- Structure de la table user
-- valide permet de rendre actif le compte (exemple controle par email )

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

INSERT INTO commandes (id, user_id, prix, date_achat, etat_id ) VALUES
(1, 3, '40','2014-05-05', 1),
(2, 4, '10','2014-06-06', 2);


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

INSERT INTO paniers (id, quantite, prix, dateAjoutPanier, user_id, manga_id, commande_id) VALUES
(1,'1','40','2014-05-05',3,1,1),
(2,'1','10','2014-06-06',4,2,2);

/****************************************
*
*
*
*        GENRE
*
*
*
 ***************************************/


/*
CREATE TABLE IF NOT EXISTS genre (
  id int(10) NO NULL AUTO_INCREMENT,
  libelle varchar(40) NOT NULL,
  PRIMARY KEY(id)
)DEFAULT CHARSET=utf8 ;


INSERT INTO genre (id,libelle) VALUES
(1,'Action'),
(2,'Mystery'),
(3,'Adventure'),
(4,'Fantasy'),
(5,'Magic'),
(6,'Thriller');


CREATE TABLE IF NOT EXISTS mangaDuGenre (
  id int(10) NOT NULL AUTO_INCREMENT,
  id_manga int(10) NOT NULL,
  id_genre int(10) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id_manga) REFERENCES manga(id)
  FOREIGN KEY(id_genre) REFERENCES genre(id)
);

INSERT INTO mangaDuGenre(id,id_manga,id_genre) VALUES
(null,1,6),
(null,1,2);
*/



