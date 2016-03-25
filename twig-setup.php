<?php

// Load Twig
require_once dirname(__FILE__) . '/Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
// Initiate Twig
if(!isset($_TWIG_LOADER) || !isset($_TWIG)) {
	$_TWIG_LOADER = new Twig_Loader_Array(array());
	$_TWIG = new Twig_Environment($_TWIG_LOADER);
}
