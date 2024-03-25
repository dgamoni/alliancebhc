<?php

define('CORE_PATH', get_stylesheet_directory() . '/core');
define('CORE_URL', get_stylesheet_directory_uri() . '/core');
define( 'CORE_PLUGINS_PATH', CORE_PATH . '/plugins/' );
define( 'CORE_PLUGINS_URL', CORE_URL . '/plugins/' );
define( 'CORE_MODULES_PATH', CORE_PATH . '/modules/' );
define( 'CORE_MODULES_URL', CORE_URL . '/modules/' );


//lib
require_once CORE_PATH.'/lib/BFI_Thumb.php';

// autoload functions
$dirs = array(
    //CORE_PATH . '/widgets/',
    //CORE_PATH . '/post_types/',
    //CORE_PATH . '/sidebars/',
    //CORE_PATH . '/shortcodes/',
    //CORE_PATH . '/functions/',

);
foreach ($dirs as $dir) {
    $other_inits = array();
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (false !== ($file = readdir($dh))) {
                if ($file != '.' && $file != '..' && stristr($file, '.php') !== false) {
                    list($nam, $ext) = explode('.', $file);
                    if ($ext == 'php')
                        $other_inits[] = $file;
                }
            }
            closedir($dh);
        }
    }
    asort($other_inits);
    foreach ($other_inits as $other_init) {
        if (file_exists($dir . $other_init))
            include_once $dir . $other_init;
    }
}




