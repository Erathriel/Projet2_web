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
    private $mangaModel;

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
        //$prixTotal = $this->panierModel;
        return $app["twig"]->render('frontOff/Panier/show.html.twig',['data'=>$panier]);
    }

    public function add(Application $app, Request $req){
        $this->mangaModel = new MangaModel($app);
        $this->panierModel = new PanierModel($app);
        $manga_id = $app->escape($req->get('id'));
        $user_id = $app['session']->get('user_id');
        if($this->panierModel->countMangaLigne($manga_id,$user_id)>0){
            $this->panierModel->updateMangaLigneAdd($manga_id,$user_id);
        }
        else {
            $this->panierModel->insertPanier($manga_id,$user_id);
        }
        return $app->redirect($app["url_generator"]->generate("manga.index"));
        return "add Panier";
    }

    public function delete(Application $app,Request $req){
        $this->mangaModel = new MangaModel($app);
        $this->panierModel = new PanierModel($app);
        $manga_id = $app->escape($req->get('id'));
        $user_id = $app['session']->get('user_id');
        if($this->panierModel->countMangaLigne($manga_id,$user_id)!=1){
            $this->panierModel->updateMangaLigneAddDec($manga_id,$user_id);
        }
        else {
            $this->panierModel->deletePanier($manga_id,$user_id);
        }
        return $app->redirect($app["url_generator"]->generate("manga.index"));
        return "delete Panier";
    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\panierController::index')->bind('panier.index');
        $controllers->get('/show', 'App\Controller\panierController::show')->bind('panier.show');

        $controllers->get('/add/{id}', 'App\Controller\panierController::add')->bind('panier.add')->assert('id', '\d+');;
//        $controllers->post('/add', 'App\Controller\panierController::validFormAdd')->bind('panier.validFormAdd');

        $controllers->get('/delete/{id}', 'App\Controller\panierController::delete')->bind('panier.delete')->assert('id', '\d+');;

        return $controllers;
    }


}
