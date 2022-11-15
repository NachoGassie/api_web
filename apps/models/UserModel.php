<?php

require_once "mainModel.php";

class UserModel extends MainModel{

    function getUser($email){
        $query = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $query->execute([$email]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
}    