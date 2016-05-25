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
    private $panierModel;

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

    public function showAdmin(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $user_id = $app['session']->get('user_id');
        $commande = $this->commandeModel->getAllCommandesClient();
        return $app["twig"]->render('backOff/Commandes/showAdmin.html.twig',['data'=>$commande]);
    }

    public function add(Application $app){
        $this->commandeModel = new CommandeModel($app);
        
        $user_id = $app['session']->get('user_id');
        $commande = $this->commandeModel->createCommande($user_id);
        return $app["twig"]->render('frontOff/Commandes/show.html.twig',['data'=>$commande]);

    }

    
    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\commandeController::index')->bind('commande.index');
        $controllers->get('/show', 'App\Controller\commandeController::show')->bind('commande.show');
        $controllers->get('/showAdmin', 'App\Controller\commandeController::showAdmin')->bind('commande.showAdmin');

        $controllers->get('/add', 'App\Controller\commandeController::add')->bind('commande.add');

        return $controllers;
    }

}