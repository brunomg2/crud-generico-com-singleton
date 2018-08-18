<?php
require_once 'conn/Conexion.class.php';
require_once 'conn/Crud.class.php';

$p1 = new Crud;
//$p1->fullRead("SELECT * FROM usuarios");

$p1->read('usuarios','where id = :id','id=1');
if(!$p1->getError()):
    echo'<pre>';
    var_dump($p1->getResult());
    echo'</pre>';
endif;