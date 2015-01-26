<?php


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
