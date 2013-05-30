<?php
ini_set("display_errors", "on");
error_reporting(E_ALL);

require_once ("../code/Templar/StreamWrapper.php");

stream_register_wrapper("templar.template", "Templar_StreamWrapper") || die ("Problem Registering Stream");

//$dat = file_get_contents("templar.template:///var/projects/templar/test/templates/simple_test.phtml");

$a = include("templar.template:///var/projects/templar/test/templates/simple_test.phtml");
$a(array("fname"=>"Chris"));
?>