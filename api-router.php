<?php
require_once './libs/Router.php';
require_once "./apps/controllers/ApiMovieController.php";
require_once "./apps/controllers/ApiUserController.php";

$router = new Router();

$router->addRoute('movies', 'GET', 'ApiMovieController', 'obtenerMovies');
$router->addRoute('movies/:ID', 'GET', 'ApiMovieController', 'obtenerMovie');
$router->addRoute('movies/:ID', 'DELETE', 'ApiMovieController', 'deleteMovie');
$router->addRoute('movies', 'POST', 'ApiMovieController', 'createMovie');
$router->addRoute('movies/:ID', 'PUT', 'ApiMovieController', 'updateMovie');
$router->addRoute('auth/token', 'GET', 'ApiUserController', 'getToken');

$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);
