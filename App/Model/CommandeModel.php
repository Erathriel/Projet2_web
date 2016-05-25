<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class CommandeModel {

    private $db;
    private $panierModel;

    public function __construct(Application $app) {
        $this->db = $app['db'];
        $this->panierModel = new PanierModel($app);
    }


    public function getCommandesClient($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id', 'c.prix', 'c.date_achat', 'e.libelle')
            ->from('commandes', 'c')
            ->innerJoin('c', 'users', 'u', 'c.user_id=u.id')
            ->innerJoin('c', 'etats', 'e', 'c.etat_id=e.id')
            ->where('c.user_id = :user_id')
            ->addOrderBy('c.date_achat', 'ASC')
            ->setParameter('user_id', $id);
        return $queryBuilder->execute()->fetchAll();
    }

    public function getAllCommandesClient(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id','c.user_id','c.prix','c.date_achat','e.libelle')
            ->from('commandes','c')
            ->innerJoin('c', 'users', 'u', 'c.user_id=u.id')
            ->innerJoin('c', 'etats', 'e', 'c.etat_id=e.id')
            ->addOrderBy('c.id','ASC');
        return $queryBuilder->execute()->fetchAll();
    }

    public function afficherDetail($commande_id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.id','m.nom', 'p.quantite','p.prix','p.dateAjoutPanier','p.commande_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'mangas', 'm', 'p.manga_id=m.id')
            ->innerJoin('p','users', 'u', 'p.user_id=u.id')
            ->where('p.user_id = u.id')
            ->andWhere('p.commande_id = :commande_id')
            ->addOrderBy('m.typeManga_id', 'ASC')
            ->setParameter('commande_id',$commande_id);
        return $queryBuilder->execute()->fetchAll();
    }



    public function createCommande($user_id){
        $conn=$this->db;
        $conn->beginTransaction();
        $prix=$this->panierModel->calculPrixTotal($user_id);
        $requestSQL=$conn->prepare('insert into commandes(user_id,prix,etat_id) values (?,?,?)');
        $requestSQL->execute([$user_id,$prix,1]);
        $lastInsertid=$conn->lastInsertId();
        $requestSQL=$conn->prepare('update paniers set commande_id=? where user_id=? and commande_id is null');
        $requestSQL->execute([$lastInsertid,$user_id]);
        $conn->commit();

    }
}