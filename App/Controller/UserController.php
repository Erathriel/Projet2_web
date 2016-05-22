<?php
namespace App\Controller;

use App\Model\UserModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security;

class UserController implements ControllerProviderInterface {

	private $userModel;

	public function index(Application $app) {
		return $this->connexionUser($app);
	}

	public function connexionUser(Application $app)
	{
		return $app["twig"]->render('v_session_connexion.html.twig');
	}

	public function validFormConnexionUser(Application $app)
	{

		$app['session']->clear();
		$donnees['login']=$app['request']->request->get('login');
		$donnees['password']=$app['request']->request->get('password');

		$this->userModel = new UserModel($app);
		$data=$this->userModel->verif_login_mdp_Utilisateur($donnees['login'],$donnees['password']);

		if($data != NULL)
		{
			$app['session']->set('droit', $data['droit']);  //dans twig {{ app.session.get('droit') }}
			$app['session']->set('login', $data['login']);
			$app['session']->set('user_id', $data['id']);
			$app['session']->set('logged', 1);
			return $app->redirect($app["url_generator"]->generate("manga.index"));
		}
		else
		{
			$app['session']->set('erreur','mot de passe ou login incorrect');
			return $app["twig"]->render('v_session_connexion.html.twig');
		}
	}
	public function deconnexionSession(Application $app)
	{
		$app['session']->clear();
		$app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');
		return $app->redirect($app["url_generator"]->generate("manga.index"));
	}

	public function edit(Application $app){
		$this->userModel = new UserModel($app);
		$user_id = $app['session']->get('user_id');
		$donnees = $this->userModel->getUsers($user_id);
		return $app["twig"]->render('frontOff/User/edit.html.twig',['donnees'=>$donnees]);
		return "edit User";
	}

	public function validFormEdit(Application $app, Request $req){
		$this->userModel = new UserModel($app);
		$user_id = $app['session']->get('user_id');
		$donnees = $this->userModel->getUsers($user_id);
		if(isset($_POST['login']) and isset($_POST['password']) and isset($_POST['email']) and isset($_POST['nom']) and isset($_POST['code_postal']) and isset($_POST['ville']) and isset($_POST['adresse'])){
			$donnees =[
					'login'=>htmlspecialchars($_POST['login']),
					'password'=>htmlspecialchars($_POST['password']),
					'email'=>htmlspecialchars($_POST['email']),
					'nom'=>htmlspecialchars($_POST['nom']),
					'code_postal'=>htmlspecialchars($_POST['code_postal']),
					'ville'=>htmlspecialchars($_POST['ville']),
					'adresse'=>htmlspecialchars($_POST['adresse'])
			];

			if ((! preg_match("/^[A-Za-z0-9]{2,}/",$donnees['login']))) $erreurs['login']='login composé de 2 lettres minimum';
			if ((! preg_match("/^[A-Za-z0-9]{2,}/",$donnees['password']))) $erreurs['password']='mot de passe composé de 2 lettres minimum';
			if ((! preg_match('#^([\w\.-]+)@([\w\.-]+)(\.[a-z]{2,4})$#',$donnees['email']))) $erreurs['email']='email incorrect';
			if ((! preg_match("/^[A-Za-z0-9]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
			if ((! preg_match("/^[0-9]{5,}/",$donnees['code_postal']))) $erreurs['code_postal']='code postal incorrect';
			if ((! preg_match("/^[A-Za-z0-9]{2,}/",$donnees['ville']))) $erreurs['ville']='ville inconnu';
			if ((! preg_match("/^[A-Za-z0-9]{2,}/",$donnees['adresse']))) $erreurs['adresse']='adresse incorrect';
			if(! is_numeric($user_id))$erreurs['id']='saisir une valeur numérique';
			if (! empty($erreurs)) {
				$this->userModel = new UserModel($app);
				return $app["twig"]->render('frontOff/User/edit.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
			}
			else
			{
				$this->userModel = new UserModel($app);
				$this->userModel->updateUser($donnees,$user_id);
				return $app->redirect($app["url_generator"]->generate("manga.index"));
			}
		}
		else{
			return $app->abort(404, 'error Pb id form edit');
		}
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];
		$controllers->match('/', 'App\Controller\UserController::index')->bind('user.index');
		$controllers->get('/login', 'App\Controller\UserController::connexionUser')->bind('user.login');
		$controllers->post('/login', 'App\Controller\UserController::validFormConnexionUser')->bind('user.validFormlogin');
		$controllers->get('/logout', 'App\Controller\UserController::deconnexionSession')->bind('user.logout');
		$controllers->get('/edit', 'App\Controller\UserController::edit')->bind('user.edit')->bind('user.edit');
		$controllers->put('/edit', 'App\Controller\UserController::validFormEdit')->bind('user.validFormEdit');
		return $controllers;
	}
}