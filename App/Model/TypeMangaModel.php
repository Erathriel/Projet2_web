<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class TypeMangaModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }


    public function getAllTypeMangas() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('t.id', 't.libelle')
            ->from('typeMangas', 't')
            ->addOrderBy('t.libelle', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }
}