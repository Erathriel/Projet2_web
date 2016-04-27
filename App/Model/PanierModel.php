<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    public function getAllPanier($data) {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.nom', 'p.quantite','p.prix','p.dateAjoutPanier')
            ->from('paniers', 'p')
            ->innerJoin('p', 'users', 'u', 'p.user_id=u.id')
            ->innerJoin('p', 'mangas', 'm', 'p.manga_id=m.id')
            ->innerJoin('p', 'commandes', 'c', 'p.commande_id=c.id')
            ->where('p.user_id = :user_id')
            ->addOrderBy('m.typeManga_id', 'ASC')
            ->setParameter('user_id', $data);
        return $queryBuilder->execute()->fetchAll();

    }
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#join-clauses
    
    public function insertPanier($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('paniers')
            ->values([
                'id' => '?',
                'quantite' => '?',
                'prix' =>'?',
                'dateAjoutPanier' => '?',
                'user_id' => '?',
                'manga_id' => '?',
                'commande_id' => '?'
            ])
            ->setParameter(0, $donnees['id'])
            ->setParameter(1, $donnees['quantite'])
            ->setParameter(2, $donnees['prix'])
            ->setParameter(3, $donnees['dateAjoutPanier'])
            ->setParameter(4, $donnees['user_id'])
            ->setParameter(5, $donnees['manga_id'])
            ->setParameter(6, $donnees['commande_id'])
        ;
        return $queryBuilder->execute();
    }

}