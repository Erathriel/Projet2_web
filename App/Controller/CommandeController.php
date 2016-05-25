<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\PanierModel;
use App\Model\MangaModel;
use App\Model\CommandeModel;
use App\Model\EtatModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;



class CommandeController implements ControllerProviderInterface{

    private $commandeModel;
    private $panierModel;
    private $etatModel;

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

    public function showDetails(Application $app, Request $req){
        $this->commandeModel = new CommandeModel($app);
        $id=$app->escape($req->get('id'));
        $commande = $this->commandeModel->afficherDetail($id);
        return $app["twig"]->render('backOff/Commandes/showDetails.html.twig',['data'=>$commande]);
    }

    public function add(Application $app){
        $this->commandeModel = new CommandeModel($app);
        $user_id = $app['session']->get('user_id');
        $commande = $this->commandeModel->createCommande($user_id);
        return $app["twig"]->render('frontOff/Commandes/show.html.twig',['data'=>$commande]);

    }

    public function edit(Application $app, $id){
        $this->commandeModel = new CommandeModel($app);
        $this->etatModel = new EtatModel($app);
        $etats = $this->etatModel->getAllEtat();
        $donnees = $this->commandeModel->getCommande($id);
        return $app["twig"]->render('backOff/Commandes/edit.html.twig',['etats'=>$etats,'donnees'=>$donnees]);
        return "edit User";
    }

    public function validFormEdit(Application $app, Request $req){
        if(isset($_POST['id']) and isset($_POST['etat_id'])){
            $donnees = [
                'etat_id' => htmlspecialchars($app['request']->get('etat_id')),
                'id' => $app->escape($req->get('id'))
            ];
            if(! is_numeric($donnees['etat_id']))$erreurs['etat_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['id']))$erreurs['id']='saisir une valeur numérique';
            if (! empty($erreurs)) {
                $this->etatModel = new EtatModel($app);
                $etats = $this->etatModel->getAllEtat();
                return $app["twig"]->render('backOff/Commandes/edit.html.twig',['donnees'=>$donnees, 'erreurs'=>$erreurs,'etats'=>$etats]);
            }
            else
            {
                $this->commandeModel = new CommandeModel($app);
                $this->commandeModel->updateEtat($donnees);
                return $app->redirect($app["url_generator"]->generate("commande.showAdmin"));
            }
        }
        else{
            return $app->abort(404, 'error Pb id form edit');
        }
    }

    
    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\commandeController::index')->bind('commande.index');
        $controllers->get('/show', 'App\Controller\commandeController::show')->bind('commande.show');
        $controllers->get('/showAdmin', 'App\Controller\commandeController::showAdmin')->bind('commande.showAdmin');
        $controllers->get('/showDetails/{id}', 'App\Controller\commandeController::showDetails')->bind('commande.showDetails')->assert('id', '\d+');

        $controllers->get('/add', 'App\Controller\commandeController::add')->bind('commande.add');

        $controllers->get('/edit/{id}', 'App\Controller\commandeController::edit')->bind('commande.edit')->assert('id', '\d+');;
        $controllers->put('/edit', 'App\Controller\commandeController::validFormEdit')->bind('commande.validFormEdit');

        return $controllers;
    }

}