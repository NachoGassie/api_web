<?php

require_once "MainApiController.php";
require_once "ApiUserController.php";


class ApiMovieController extends MainApiController{
    private $cantPeliculas;

    function __construct(){
        parent::__construct();
        $this->cantPeliculas = $this->movieModel->getCantMovies();
    }

    function obtenerMovies(){
        if ($this->cantPeliculas>0) {

            $pag = $this->getPag();
            $limit = $this->getLimit();
            $campo = $this->getCampo();
            $ord = $this->getOrd();

            if ($pag && $limit && $campo && $ord ) {

                if (isset($_GET['genero'])) 
                    return $this->filter($pag, $limit, $campo, $ord);

                $correctMovieLength = ($pag*$limit) < ($this->cantPeliculas+=$limit);
        
                if ($correctMovieLength) {
                    $movies = $this->movieModel->getPeliculas($campo, $ord, $pag, $limit);  
                    return $this->apiView->response($movies, 200);
                }
                return $this->moviesLengthError();
            }
            return $this->showRequestError($pag, $limit, $campo, $ord);
        }
        return $this->apiView->response("La lista de peliculas se encuentra vacía o no es posible acceder a ella", 404);
    }
    function obtenerMovie($params = null){
        $id = $params[':ID'];
        $movie = $this->movieModel->getPeliculaId($id);
        if ($movie) 
            return $this->apiView->response($movie, 200);
        
        return $this->apiView->response("La pelicula con el id : $id no existe", 404);
    }
    function deleteMovie($params = null){
        $id = $params[':ID'];
        $movie = $this->movieModel->getPeliculaId($id);
        if ($movie) {
            $this->movieModel->deleteMovie($id);
            return $this->apiView->response("La pelicula con el id : $id ha sido borrada con éxito", 200);
        }
        return $this->apiView->response("La pelicula que desea borrar con el id : $id no existe", 404);
    }
    function createMovie(){
        if ($this->authHelper->isLoggedIn()) {
            $body = $this->getData();
        
            if ($this->checkParams($body->id_genero, $body->anio_lanzamiento)) {
    
                $id = $this->movieModel->insertMovie($body->titulo, $body->poster, $body->sinopsis, 
                $body->anio_lanzamiento, $body->id_genero);
    
                return $this->apiView->response("La pelicula se insertó con éxito, con el id : $id", 201);
            }
            return $this->wrongParams();
        }
        return $this->notLoggedIn();
    }
    function updateMovie($params = null){
        if ($this->authHelper->isLoggedIn()) {
            $idMovie = $params[':ID'];
            $movie = $this->movieModel->getPeliculaId($idMovie);
            $body = $this->getData();

            if ($movie) {
                if ($this->checkParams($body->id_genero, $body->anio_lanzamiento)) {

                    $this->movieModel->updateMovie($body->titulo, $body->poster, $body->sinopsis, 
                    $body->anio_lanzamiento, $body->id_genero, $idMovie);
            
                    return $this->apiView->response("La pelicula con el id : $idMovie, se actualizó con éxito", 200);
                }
                return $this->wrongParams();
            }
            return $this->apiView->response("La pelicula que desea modificar con el id : $idMovie, no existe", 404);
        }
        return $this->notLoggedIn();
    }

    //Filtro
    private function filter($pag, $limit, $campo, $ord){
        $gen = $_GET['genero'];
        $cantMatch = $this->genModel->getCountGen($gen);

        if ($cantMatch>0) {
            $correctGenLength = ($pag*$limit) < ($cantMatch+=$limit);
            if ($correctGenLength){
                $movies = $this->genModel->getPeliculaGen($gen, $campo, $ord, $pag, $limit);
                return $this->apiView->response($movies, 200);
            } 
            return $this->moviesLengthError();
        }
        return $this->apiView->response("No se hayan peliculas con el genero : $gen", 404);
    }
    //Private Functions
    private function getOrd(){
        if (isset($_GET['order'])) {
            $tempOrd  = strtoupper($_GET['order']);
            if ($tempOrd === "ASC" || $tempOrd === "DESC") 
                return $tempOrd;
            
            return null;
        }
        return "ASC";  
    }
    private function getCampo(){
        if (isset($_GET['sort'])) {
            $tempCampo = $_GET['sort'];
            if ($tempCampo === "titulo" || $tempCampo === "genero" || $tempCampo === "sinopsis" || 
                $tempCampo === "anio_lanzamiento" || $tempCampo === "id") 
                return $tempCampo;
         
            return null;    
        }
        return "id";
    }
    private function getPag(){
        if (isset($_GET['pag'])){
            $tempPag = $_GET['pag'];
            if (is_numeric($tempPag) && $tempPag>0 && isset($_GET['limit'])) 
                return $tempPag;

            return null;
        } 
        return 1; 
    }
    private function getLimit(){
        if (isset($_GET['limit'])) {
            $tempLimit = $_GET['limit'];
            if (is_numeric($tempLimit) && $tempLimit>0 && isset($_GET['pag'])) 
                return $tempLimit;

            return null;
        }
        return $this->cantPeliculas; 
    }
    private function showRequestError($pag, $limit, $campo, $ord){
        $error = "";

        if (!$pag || !$limit) 
            $error .= "La paginación espera una peticion númerica mayor a 0 y ser hecha de forma conjunta entre sus dos parametros. "; 
        if (!$campo) 
            $error .=  ucfirst($_GET['sort']) ." no es un campo perteneciente a la tabla de peliculas. ";
        if (!$ord) 
            $error .= "Asegurese de escribir un orden asc o desc."; 

        return $this->apiView->response($error, 400);     
    }
    private function checkParams($genId, $anio){
        $genres = $this->genModel->getAllGen();  
        $correctGen = false;
        
        foreach ($genres as $g) {
            if ($g->id_genero === $genId) 
                $correctGen = true;
        } 
        $correctAnio = is_numeric($anio) && $anio>1900;

        return $correctGen && $correctAnio;
    }
    //Mensajes Repet
    private function moviesLengthError(){
        return $this->apiView->response("Excedió la cantidad de peliculas disponibles", 400);
    }
    private function wrongParams(){
        return $this->apiView->response("Asegurese de que su petición posea un año mayor a 1900 y un genero perteneciente a la tabla", 400);
    }
    private function notLoggedIn(){
        return $this->apiView->response("Debe tener el nivel de sesión necesario para acceder a esta función", 401);
    }
}