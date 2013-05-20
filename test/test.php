<?php

require_once("../code/Templar.php");
require_once("../code/Templar/Exception.php");
require_once("../code/Templar/Template.php");
require_once("../code/Templar/StreamWrapper.php");

$tmpl = new Templar();
$tmpl->addTemplatePath("templates");


// Static Invokation

$tmpl->setTemplatePreprocessor(function($str){return str_replace("Hello", "Hola", $str);});
$tmpl->display("simple_test.phtml", array("fname"=>"Chris"));
