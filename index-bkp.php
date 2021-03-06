<?php
session_start();
require_once("vendor/autoload.php");

use Hcode\Model\Category;
use \Slim\Slim;
use \Hcode\Page;
use Hcode\PageAdmin;
use \Hcode\Model\User;


$app = new \Slim\Slim();

$app->config('debug', true);

/**Arquivo responsável pelas rotas da aplicação */

/** Rota  default */
$app->get('/', function () {
	$page = new Page();
	$page->setTpl("index");
});

/** Rota que direciona para área admin */
$app->get('/admin/', function () {
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("index");
});

/** Rota que  return o login */
$app->get('/admin/login', function () {
	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);
	$page->setTpl("login");
});

/** Rota que direciona para o login */
$app->post('/admin/login', function () {
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

/**Rota responsável para deslogar o usuário */
$app->get('/admin/logout', function () {
	User::logout();
	header("Location: /admin/login");
	exit;
});

/** Rota responsável por retornar todos os usuários */
$app->get('/admin/users', function () {
	User::verifyLogin();
	$users = User::listAll();

	$page = new PageAdmin();
	$page->setTpl("users", array("users" => $users));
});

/**Rota responsável  por trazer os dados do usuário*/
$app->get('/admin/users/create', function () {
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("users-create");
});

/** Rota responsável por deletar usuário */
$app->get('/admin/users/:iduser/delete', function ($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int) $iduser);
	$user->delete();
	header("Location: /admin/users");
	exit;
});

/** Rota responsável por retornar os dados do usuário para edição */
$app->get('/admin/users/:iduser', function ($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$page = new PageAdmin();
	$page->setTpl("users-update", array(
		"user" => $user->getValues()
	));
});

/**Rota responsável pela criação de usuário */
$app->post('/admin/users/create', function () {
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

		"cost" => 12

	]);

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;
});



/**Rota responsável por setar o id do usuários */
$app->post('/admin/users/:iduser', function ($iduser) {
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]) ? 1 : 0);
	$user->get((int) $iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;
});


$app->get("/admin/forgot", function () {
	$page = new PageAdmin([
		"header" => false,
		"footer" => false,
	]);
	$page->setTpl("forgot");
});


$app->post("/admin/forgot", function () {
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent", function () {

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("forgot-sent");
});


$app->get("/admin/forgot/reset", function () {

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("forgot-reset", array(
		"name" => $user["desperson"],
		"code" => $_GET["code"]
	));
});

$app->post("/admin/forgot/reset", function () {

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setFogotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = User::getPasswordHash($_POST["password"]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("forgot-reset-success");
});



$app->get("/admin/categories", function () {
	User::verifyLogin();
	$categories = Category::listAll();
	$page = new PageAdmin([]);

	$page->setTpl("categories", array(
		"categories" => $categories
	));
});

$app->get("/admin/categories/create", function () {
	User::verifyLogin();
	$page = new PageAdmin([]);
	$page->setTpl("categories-create");
});

$app->post("/admin/categories/create", function () {
	User::verifyLogin();
	$category = new Category();
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;
});


/**Rota responsável por setar o id do categorias */
$app->get('/admin/categories/:idcategory/delete', function ($idcategory) {
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->delete();
	header("Location: /admin/categories");
	exit;
});

$app->get('/admin/categories/:idcategory', function ($idcategory) {
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new PageAdmin([]);
	$page->setTpl("categories-update", array(
		'category' => $category->getValues()
	));
});
$app->post('/admin/categories/:idcategory', function ($idcategory) {
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;
});


$app->get("/categories/:category", function ($idcategory) {
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new Page();
	$page->setTpl("category", [
		'category' => $category->getValues(),
		'products' => []
	]);
});








$app->run();
