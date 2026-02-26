<?php

require_once 'Usuario.php'

class Cocinero extends Usuario{

    private function __construct($nombreUsuario, $email, $nombre, $apellidos, $hash, $rol, $avatar){
        parent::__construct($nombreUsuario, $email, $nombre, $apellidos, $hash, $rol, $avatar);

    }
    
}




