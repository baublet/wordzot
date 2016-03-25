<?php
/*
Plugin Name:    WordZot
Plugin URI:     http://www.ryanmpoe.com
Description:    A powerful Zotero manager for WordPress
Version:        0.0
Author:         Ryan Poe
Author URI:     http://www.ryanmpoe.com
*/


define('_WORDZOT_DEBUG', true);
require_once("twig-setup.php");

require_once("include/wordzot.php");
require_once("include/wordzot_admin.php");

// Basic stuff we need to load for this plugin to be installed
$wordzot_base = new WordZot($_TWIG);
// Admin menu features
$wordzot_admin = new WordZotAdmin($_TWIG);
