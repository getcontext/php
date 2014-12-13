<?php
require_once('framework/application/GooglePlacesApplication.php');

use Framework\Request;
use Framework\Application\GooglePlacesApplication;

$request = new Request();

if ($request->getController() == 'googleplaces') {

    $app = GooglePlacesApplication::get();
    $app->execute($request);

}


