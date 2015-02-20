<?php


// Paths
define( "ROOT", str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]) );
define( "FS_ROOT", str_replace("index.php", "", $_SERVER["SCRIPT_FILENAME"]) );
define( "HTTP_ROOT", "http". (isset($_SERVER["HTTPS"])?"s":"") ."://". $_SERVER["HTTP_HOST"] . ROOT );


header( "Content-type: text/html; charset=utf-8" );
ob_start();

// Load and init basic modules
require( "modules/func.php" );
require( "modules/class/localcache.php" );
LocalCache::init();

// Error handler
Error::init();

Config::init();

if( Config::get("cache") === CACHE_LEVEL_OFF )
	LocalCache::scan();


Module::load( "all" );
Router::init();

Hook::run( "shutdown" );
