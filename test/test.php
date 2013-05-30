<?php

require_once("../code/Templar.php");
require_once("../code/Templar/Exception.php");
require_once("../code/Templar/Template.php");
require_once("../code/Templar/StreamWrapper.php");

$tmpl = new Templar();
$tmpl->addTemplatePath("templates");


// Static Invokation

//$tmpl->displayTemplate("simple_test.phtml", array("fname"=>"Chris"));
$tmpl->defineViewHelper("formText", "formInput.phtml", array("name","value", "attr"));
$tmpl->defineViewHelper("htmlAttributes","html_attributes.phtml", array("attributes"));
$tmpl->displayTemplate("simple_test.phtml", array("fname"=>"Chris"));