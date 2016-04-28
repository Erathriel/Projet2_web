<?php
/**
 * Created by PhpStorm.
 * User: geoffrey
 * Date: 22/04/16
 * Time: 11:22
 */

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class CommandeModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }


    public function getAllCommandes($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id', 'c.user_id', 'prix', 'date_achat', 'etat_id')
            ->from('commandes', 'c')
            ->innerJoin('c', 'users', 'u', 'c.user_id=u.id')
            ->innerJoin('c', 'etats', 'e', 'c.etat_id=e.id')
            ->where('c.user_id = :user_id')
            ->addOrderBy('c.date_achat', 'ASC')
            ->setParameter('user_id', $id);
        return $queryBuilder->execute()->fetchAll();
    }
}