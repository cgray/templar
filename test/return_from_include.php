<?php
class B {
    function getInclude(){
        $a = include("test.include.php");
        print_r($a);            
    }

}

$b = new B();
$b->getInclude();

