<?php
require_once 'conn/Connection.class.php';
require_once 'conn/Crud.class.php';


$p1 = new Crud;
$data = ["nome" => "HB20"];
$p1->update('carros','where id=:id',$data,"id=8");

$p2 = new Crud;
$data = ["nome" => "palio"];
$p2->create('carros',$data);

$p4 = new Crud;
$p4->delete('carros','where nome = :nome',"nome=palio");

$p3 = new Crud;
$p3->read('carros');
//$p3->fullRead('SELECT * FROM carros WHERE nome = :nome','nome=hb20');
if(!$p3->getError()):
    echo'<pre>';
    var_dump($p3->getResult());
    echo'</pre>';
endif;
