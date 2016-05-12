<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\PanierModel;
use App\Model\MangaModel;
use App\Model\CommandeModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;



class CommandeController implements ControllerProviderInterface{

    private $commandeModel;


    public function __construct()
    {

    }


    public function index(Application $app) {
        return $this->show($app);
    }


    public function show(Application $app) {
        $this->commandeModel = new CommandeModel($app);
        $user_id = $app['session']->get('user_id');
        $commande = $this->commandeModel->getCommandesClient($user_id);
        return $app["twig"]->render('frontOff/Commandes/show.html.twig',['data'=>$commande]);
    }




    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\commandeController::index')->bind('commande.index');
        $controllers->get('/show', 'App\Controller\commandeController::show')->bind('commande.show');


        return $controllers;
    }

}