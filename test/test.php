<?php

require_once("../code/Templar.php");
require_once("../code/Templar/Exception.php");

$tmpl = Templar::getInstance();
$tmpl->addTemplatePath("templates");

$func = $tmpl->getTemplateFunction("simple_test.phtml");

$func("Chris");

// Static Invokation

Templar::display("simple_test.phtml", "Chris");
