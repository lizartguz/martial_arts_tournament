<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
Tested working with PHP5.4 and above (including PHP 7 )

 */
require_once './vendor/autoload.php';

use FormGuide\Handlx\FormHandler;


$pp = new FormHandler(); 

$validator = $pp->getValidator();
$validator->fields(['nombre','email'])->areRequired();
$validator->field('email')->isEmail();
$validator->field('mensaje')->maxLength(6000);



$pp->setSubject("Formulario SenvaTec");
$pp->sendEmailTo(['marketing@artguz.net', 'meteorologo@artguz.net']); // ← Your email here

echo $pp->process($_POST);