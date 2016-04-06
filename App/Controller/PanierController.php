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
        $panier = $this->panierModel->getAllPanier();
        return $app["twig"]->render('backOff/Panier/show.html.twig',['data'=>$panier]);
    }


    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\panierController::index')->bind('panier.index');
        $controllers->get('/show', 'App\Controller\panierController::show')->bind('panier.show');


        return $controllers;
    }


}
