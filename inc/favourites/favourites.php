<?php
/***************************
* constants
***************************/

if(!defined('LI_BASE_DIR')) {
	define('LI_BASE_DIR', dirname(__FILE__));
}
if(!defined('LI_BASE_URL')) {
	define('LI_BASE_URL', plugin_dir_url(__FILE__));
}

/***************************
* includes
***************************/
include(LI_BASE_DIR . '/includes/fav-functions.php');
include(LI_BASE_DIR . '/includes/scripts.php');