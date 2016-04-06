<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class MangaModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#join-clauses
    public function getAllMangas() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.id', 't.libelle', 'm.nom', 'm.prix', 'm.photo', 'm.dispo', 'm.stock')
            ->from('mangas', 'm')
            ->innerJoin('m', 'typeMangas', 't', 'm.typeManga_id=t.id')
            ->addOrderBy('m.nom', 'ASC');
        return $queryBuilder->execute()->fetchAll();

    }

    public function insertManga($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('mangas')
            ->values([
                'nom' => '?',
                'typeManga_id' => '?',
                'prix' => '?',
                'photo' => '?',
                'dispo' => '?',
                'stock' => '?'
            ])
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['typeManga_id'])
            ->setParameter(2, $donnees['prix'])
            ->setParameter(3, $donnees['photo'])
            ->setParameter(4, $donnees['dispo'])
            ->setParameter(5, $donnees['stock'])
        ;
        return $queryBuilder->execute();
    }

    function getManga($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id', 'typeManga_id', 'nom', 'prix', 'photo','dispo','sotck')
            ->from('mangas')
            ->where('id= :id')
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    public function updateManga($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('mangas')
            ->set('nom', '?')
            ->set('typeManga_id','?')
            ->set('prix','?')
            ->set('photo','?')
            ->set('dispo',' ?')
            ->set('stock', '?')
            ->where('id= ?')
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['typeManga_id'])
            ->setParameter(2, $donnees['prix'])
            ->setParameter(3, $donnees['photo'])
            ->setParameter(4, $donnees['dispo'])
            ->setParameter(5, $donnees['stock'])
            ->setParameter(6, $donnees['id']);
        return $queryBuilder->execute();
    }

    public function deleteManga($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('mangas')
            ->where('id = :id')
            ->setParameter('id',(int)$id)
        ;
        return $queryBuilder->execute();
    }



}