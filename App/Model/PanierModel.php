<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#join-clauses
    public function getAllPanier() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.nom','p.quantite','p.prix','p.dateAjoutPanier')
            ->from('paniers', 'p')
            ->innerJoin('p', 'users', 'u', 'p.user_id=u.id')
            ->innerJoin('p', 'mangas', 'm', 'p.manga_id=m.id')
            ->innerJoin('p', 'commandes', 'c', 'p.commande_id=c.id')
            ->addOrderBy('m.id', 'ASC');
        return $queryBuilder->execute()->fetchAll();

    }

}