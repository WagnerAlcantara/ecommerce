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

require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");
$app->run();
