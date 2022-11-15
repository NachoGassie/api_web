<?php

require_once "./apps/views/ApiView.php";
require_once "./apps/models/MovieModel.php";
require_once "./apps/models/genModel.php";
require_once "./apps/helpers/authHelper.php";

class MainApiController{
    protected $apiView; 
    protected $movieModel;
    protected $genModel;
    protected $authHelper;

    private $data;

    function __construct(){
        $this->apiView = new ApiView();
        $this->movieModel = new MovieModel();
        $this->genModel = new GenModel();
        $this->authHelper = new AuthHelper();
        $this->data = file_get_contents("php://input"); 
    }

    protected function getData(){ 
        return json_decode($this->data); 
    }  
}