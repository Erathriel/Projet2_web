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
    
    public function insertPanier($manga_id, $user_id){
        $queryBuilder = new QueryBuilder($this->db);
        $prix = (float) $queryBuilder->select('prix')->from('mangas')->where('id=:manga_id')
            ->setParameter('manga_id', $manga_id)->execute()->fetchColumn(0);
        $queryBuilder->insert('paniers')
            ->values([
                'quantite' => '?',
                'prix' =>':prix',
                'user_id' => ':user_id',
                'manga_id' => ':manga_id'
            ])
            ->setParameter('quantite', '1')
            ->setParameter('prix', $prix)
            ->setParameter('user_id', $user_id)
            ->setParameter('manga_id', $manga_id)
        ;
        return $queryBuilder->execute();
    }

    public function countMangaLigne($manga_id, $user_id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('count(manga_id)')->from('paniers')
            -> where('manga_id = :manga_id')->andWhere('user_id = :user_id')
            ->andWhere('commande_id is Null')
            ->setParameter('manga_id',$manga_id)->setParameter('user_id', $user_id);
        return $queryBuilder->execute()->fetchColumn(0);
    }

    public function updateMangaLigneAdd($manga_id, $user_id){
        $queryBuilder = new QueryBuilder($this->db);
        $prix = (float) $queryBuilder->select('prix')->from('mangas')->where('id=:manga_id')
            ->setParameter('manga_id', $manga_id)->execute()->fetchColumn(0);
        $queryBuilder ->update('paniers')
            ->set('quantite','quantite+1')->set('prix',':prix')
            ->where('manga_id = :manga_id')->andWhere('user_id = :user_id')
            ->andWhere('commande_id is Null')
            ->setParameter('prix',$prix)->setParameter('manga_id',$manga_id)
            ->setParameter('user_id',$user_id);
        return $queryBuilder->execute();
    }

}