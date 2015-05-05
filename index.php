<?php


// Paths
define( "ROOT", str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]) );
define( "FS_ROOT", str_replace("index.php", "", $_SERVER["SCRIPT_FILENAME"]) );
define( "HTTP_ROOT", "http". (empty($_SERVER["HTTPS"])?"":"s") ."://". $_SERVER["HTTP_HOST"] . ROOT );


header( "Content-type: text/html; charset=utf-8" );
ob_start();

// Load and init basic modules
require( "modules/func.php" );
require( "modules/localcache.php" );
LocalCache::init();

// Error handler
Error::init();

// Rescan cache, if developer
if( isset($_COOKIE["sess"]) && Session::dev() )
	LocalCache::scan();


Module::load( "all" );
Router::init();

Hook::run( "shutdown" );
