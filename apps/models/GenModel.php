<?php

require_once "MainModel.php";

class GenModel extends MainModel{
     
    function getPeliculaGen($gen, $campo, $ord, $pag, $limit){
        $pag = ($pag-1)*$limit;
        $query = $this->db->prepare("SELECT * FROM peliculas p INNER JOIN generos g
        ON p.id_genero = g.id_genero WHERE g.genero=? ORDER BY ". $campo." ".$ord.", p.id " .$ord. " LIMIT " . $pag .','. $limit); 
        $query->execute([$gen]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    //Funciones Auxiliares
    function getCountGen($gen){
        $query = $this->db->prepare("SELECT * FROM peliculas p INNER JOIN generos g
        ON p.id_genero = g.id_genero WHERE g.genero=?"); 
        $query->execute([$gen]); 
        return count($query->fetchAll(PDO::FETCH_OBJ));
    }
    function getAllGen(){
        $query = $this->db->prepare("SELECT * FROM generos g"); 
        $query->execute(); 
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}
