<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\MangaModel;
use App\Model\TypeMangaModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

class MangaController implements ControllerProviderInterface
{
    private $mangaModel;
    private $typeMangaModel;

    public function __construct()
    {
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->mangaModel = new MangaModel($app);
        $mangas = $this->mangaModel->getAllMangas();
        return $app["twig"]->render('backOff/Manga/show.html.twig',['data'=>$mangas]);
    }

    public function add(Application $app) {
        $this->typeMangaModel = new TypeMangaModel($app);
        $typeMangas = $this->typeMangaModel->getAllTypeMangas();
        return $app["twig"]->render('backOff/Manga/add.html.twig',['typeMangas'=>$typeMangas,'path'=>BASE_URL]);
        return "add Manga";
    }

    public function validFormAdd(Application $app, Request $req) {
       // var_dump($app['request']->attributes);
        if (isset($_POST['nom']) && isset($_POST['typeManga_id']) and isset($_POST['nom']) and isset($_POST['photo'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
                'typeManga_id' => htmlspecialchars($app['request']->get('typeManga_id')),
                'prix' => htmlspecialchars($req->get('prix')),
                'photo' => $app->escape($req->get('photo')),  //$req->query->get('photo')
                'dispo' => htmlspecialchars($_POST['dispo']),
                'stock' => htmlspecialchars($_POST['stock'])
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeManga_id']))$erreurs['typeManga_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';
            if(! is_numeric($donnees['dispo']))$erreurs['dispo']='saisir une valeur numérique';
            if(! is_numeric($donnees['stock']))$erreurs['stock']='saisir une valeur numérique';

            if(! empty($erreurs))
            {
                $this->typeMangaModel = new TypeMangaModel($app);
                $typeMangas = $this->typeMangaModel->getAllTypeMangas();
                return $app["twig"]->render('backOff/Manga/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'typeMangas'=>$typeMangas]);
            }
            else
            {
                $this->MangaModel = new MangaModel($app);
                $this->MangaModel->insertManga($donnees);
                return $app->redirect($app["url_generator"]->generate("manga.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb data form Add');

    }

    public function delete(Application $app, $id) {
        $this->typeMangaModel = new TypeMangaModel($app);
        $typeMangas = $this->typeMangaModel->getAllTypeMangas();
        $this->mangaModel = new MangaModel($app);
        $donnees = $this->mangaModel->getManga($id);
        return $app["twig"]->render('backOff/Manga/delete.html.twig',['typeMangas'=>$typeMangas,'donnees'=>$donnees]);
        return "add Manga";
    }

    public function validFormDelete(Application $app, Request $req) {
        $id=$app->escape($req->get('id'));
        if (is_numeric($id)) {
            $this->mangaModel = new MangaModel($app);
            $this->mangaModel->deleteManga($id);
            return $app->redirect($app["url_generator"]->generate("manga.index"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }


    public function edit(Application $app, $id) {
        $this->typeMangaModel = new TypeMangaModel($app);
        $typeMangas = $this->typeMangaModel->getAllTypeMangas();
        $this->mangaModel = new MangaModel($app);
        $donnees = $this->mangaModel->getManga($id);
        return $app["twig"]->render('backOff/Manga/edit.html.twig',['typeMangas'=>$typeMangas,'donnees'=>$donnees]);
        return "add Manga";
    }

    public function validFormEdit(Application $app, Request $req) {
        // var_dump($app['request']->attributes);
        if (isset($_POST['nom']) && isset($_POST['typeManga_id']) and isset($_POST['nom']) and isset($_POST['photo']) and isset($_POST['id'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echaper les entrées
                'typeManga_id' => htmlspecialchars($app['request']->get('typeManga_id')),
                'prix' => htmlspecialchars($req->get('prix')),
                'photo' => $app->escape($req->get('photo')),
                'id' => $app->escape($req->get('id'))//$req->query->get('photo')
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeManga_id']))$erreurs['typeManga_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';
            if(! is_numeric($donnees['id']))$erreurs['id']='saisir une valeur numérique';
           $contraintes = new Assert\Collection(
                [
                    'id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'typeManga_id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'nom' => [
                        new Assert\NotBlank(['message'=>'saisir une valeur']),
                        new Assert\Length(['min'=>2, 'minMessage'=>"Le nom doit faire au moins {{ limit }} caractères."])
                    ],
                    //http://symfony.com/doc/master/reference/constraints/Regex.html
                    'photo' => [
                        new Assert\Length(array('min' => 5)), 
                        new Assert\Regex([ 'pattern' => '/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/',
                        'match'   => true,
                        'message' => 'nom de fichier incorrect (extension jpeg , jpg ou png)' ]),
                    ],
                    'prix' => new Assert\Type(array(
                        'type'    => 'numeric',
                        'message' => 'La valeur {{ value }} n\'est pas valide, le type est {{ type }}.',
                    ))
                ]);
            $errors = $app['validator']->validate($donnees,$contraintes);  // ce n'est pas validateValue

        //    $violationList = $this->get('validator')->validateValue($req->request->all(), $contraintes);
//var_dump($violationList);

          //   die();
            if (count($errors) > 0) {
                // foreach ($errors as $error) {
                //     echo $error->getPropertyPath().' '.$error->getMessage()."\n";
                // }
                // //die();
                //var_dump($erreurs);

            // if(! empty($erreurs))
            // {
                $this->typeMangaModel = new TypeMangaModel($app);
                $typeMangas = $this->typeMangaModel->getAllTypeMangas();
                return $app["twig"]->render('backOff/Manga/edit.html.twig',['donnees'=>$donnees,'errors'=>$errors,'erreurs'=>$erreurs,'typeMangas'=>$typeMangas]);
            }
            else
            {
                $this->MangaModel = new MangaModel($app);
                $this->MangaModel->updateManga($donnees);
                return $app->redirect($app["url_generator"]->generate("manga.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb id form edit');

    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\mangaController::index')->bind('manga.index');
        $controllers->get('/show', 'App\Controller\mangaController::show')->bind('manga.show');

        $controllers->get('/add', 'App\Controller\mangaController::add')->bind('manga.add');
        $controllers->post('/add', 'App\Controller\mangaController::validFormAdd')->bind('manga.validFormAdd');

        $controllers->get('/delete/{id}', 'App\Controller\mangaController::delete')->bind('manga.delete')->assert('id', '\d+');;
        $controllers->delete('/delete', 'App\Controller\mangaController::validFormDelete')->bind('manga.validFormDelete');

        $controllers->get('/edit/{id}', 'App\Controller\mangaController::edit')->bind('manga.edit')->assert('id', '\d+');;
        $controllers->put('/edit', 'App\Controller\mangaController::validFormEdit')->bind('manga.validFormEdit');

        return $controllers;
    }


}
