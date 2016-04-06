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
            ->select('m.id', 't.libelle', 'm.nom','m.nbEpisode', 'm.prix', 'm.photo', 'm.dispo', 'm.stock')
            ->from('mangas', 'm')
            ->innerJoin('m', 'typeMangas', 't', 'm.typeManga_id=t.id')
            ->addOrderBy('m.id', 'ASC');
        //order by type && nom si id non affichée
        return $queryBuilder->execute()->fetchAll();

    }

    public function insertManga($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('mangas')
            ->values([
                'nom' => '?',
                'typeManga_id' => '?',
                'nbEpisode' =>'?',
                'prix' => '?',
                'photo' => '?',
                'dispo' => '?',
                'stock' => '?'
            ])
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['typeManga_id'])
            ->setParameter(2, $donnees['nbEpisode'])
            ->setParameter(3, $donnees['prix'])
            ->setParameter(4, $donnees['photo'])
            ->setParameter(5, $donnees['dispo'])
            ->setParameter(6, $donnees['stock'])
        ;
        return $queryBuilder->execute();
    }

    function getManga($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id', 'typeManga_id', 'nom','nbEpisode', 'prix', 'photo','dispo','sotck')
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
            ->set('nbEpisode','?')
            ->set('prix','?')
            ->set('photo','?')
            ->set('dispo',' ?')
            ->set('stock', '?')
            ->where('id= ?')
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['typeManga_id'])
            ->setParameter(2, $donnees['nbEpisode'])
            ->setParameter(3, $donnees['prix'])
            ->setParameter(4, $donnees['photo'])
            ->setParameter(5, $donnees['dispo'])
            ->setParameter(6, $donnees['stock'])
            ->setParameter(7, $donnees['id']);
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