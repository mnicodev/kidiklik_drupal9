<?php
use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

chdir("../../..");

$autoloader = require_once "autoload.php";
$kernel = DrupalKernel::createFromRequest(Request::createFromGlobals(),$autoloader, 'prod');
$kernel->boot();
echo "ok";
