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
define('_WORDZOT_PLUGIN_DIR', dirname(__FILE__));
require_once("twig-setup.php");
require_once("include/phpZot/fsCache.php");
require_once("include/phpZot/phpZot.php");

require_once("include/wordzot.php");
require_once("include/wordzot_admin.php");

// Basic stuff we need to load for this plugin to be installed
$wordzot_base = new WordZot($_TWIG);
// Admin menu features
$wordzot_admin = new WordZotAdmin($_TWIG, $wordzot_base);
$wordzot_admin->admin_dir = dirname(__FILE__) . "/admin/";
