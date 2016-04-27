<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use App\Model\PanierModel;

class PanierController implements ControllerProviderInterface
{
    private $panierModel;

    public function __construct()
    {
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->panierModel = new PanierModel($app);
        $user_id = $app['session']->get('user_id');
        $panier = $this->panierModel->getAllPanier($user_id);
        return $app["twig"]->render('frontOff/Panier/show.html.twig',['data'=>$panier]);
    }

    public function add(Application $app){
        $donnees = [
            'id' => htmlspecialchars($_POST['id']),                    // echapper les entrées
            'quantite' => htmlspecialchars($_POST('quantite')),
            'prix' => htmlspecialchars($_POST['prix']),
            'dateAjoutPanier' => htmlspecialchars($_POST('dateAjoutPanier')),
            'user_id' => htmlspecialchars($_POST('user_id')),  //$req->query->get('photo')
            'manga_id' => htmlspecialchars($_POST['manga_id']),
            'commande_id' => htmlspecialchars($_POST['commande_id'])
        ];
        $this->panierModel = new panierModel($app);
        $this->panierModel->insertPanier($donnees);
        return $app->redirect($app["url_generator"]->generate("manga.index"));
    }


    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\panierController::index')->bind('panier.index');
        $controllers->get('/show', 'App\Controller\panierController::show')->bind('panier.show');

        $controllers->post('/add', 'App\Controller\panierController::add')->bind('panier.add');


        return $controllers;
    }


}
