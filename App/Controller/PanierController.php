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

    public function add(Application $app, $id){
        $mangaModel = new MangaModel($app);
        $manga = $mangaModel->getManga($id);
        $user_id = $app['session']->get('user_id');
        $commandeModel = new CommandeModel($app);
        $commande = $commandeModel->getAllCommandes($user_id);
        return $app["twig"]->render('frontOff/Panier/add.html.twig',['data'=>$manga, 'id'=>$commande]);
        return "add Panier";


    }

    public function validFormAdd(Application $app, Request $req){
        $dataManga = $app->escape($req->get('id','prix'));
        $idCommande = $app->escape($req->get('id'));
        $user_id = $app['session']->get('user_id');
        $donnees = [
            'quantite'=>htmlspecialchars($_POST['quantite']),
            'dateAjoutPanier'=>htmlspecialchars($_POST['dateAjoutPanier'])
        ];

        if(! is_numeric($donnees['quantite']))$erreurs['quantite']='saisir une valeur numérique';
        if (! preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$donnees['dateAjoutPanier'])) $erreurs['dateAjoutPanier']='Date incorrect';

        if(! empty($erreurs))
        {
            $mangaModel = new MangaModel($app);
            $manga = $mangaModel->getManga($dataManga[0]);
            $user_id = $app['session']->get('user_id');
            $commandeModel = new CommandeModel($app);
            $commande = $commandeModel->getAllCommandes($user_id);
            return $app["twig"]->render('frontOff/Panier/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'data'=>$manga, 'id'=>$commande]);
        }
        else
        {
            $this->panierModel = new PanierModel($app);
            $this->panierModel->insertPanier($donnees, $dataManga, $idCommande, $user_id);
            return $app->redirect($app["url_generator"]->generate("manga.index"));
        }
    }


    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\panierController::index')->bind('panier.index');
        $controllers->get('/show', 'App\Controller\panierController::show')->bind('panier.show');

        $controllers->get('/add/{id}', 'App\Controller\panierController::add')->bind('panier.add')->assert('id', '\d+');;
        $controllers->post('/add', 'App\Controller\panierController::validFormAdd')->bind('panier.validFormAdd');

        return $controllers;
    }


}
