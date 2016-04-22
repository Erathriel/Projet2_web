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

}