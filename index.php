<?php
require_once 'conn/Connection.class.php';
require_once 'conn/Crud.class.php';

$p1 = new Crud;
//$p1->fullRead("SELECT * FROM usuarios");

$p1->read('carros');
if(!$p1->getError()):
    echo'<pre>';
    var_dump($p1->getResult());
    echo'</pre>';
endif;
echo '<hr>';
$p2 = new Crud;
$data = ["nome" => "I30"];
//$p2->create('carros',$data);
if(!$p2->getError()):
    echo'<pre>';
    var_dump($p2->getResult());
    echo'</pre>';
endif;