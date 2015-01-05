<?php


header( "Content-type: text/html; charset=utf-8" );
error_reporting( E_ALL );
ini_set( "display_errors", 1 );

// Load and init basic modules
require( "modules/func.php" );
require( "modules/class/localcache.php" );
LocalCache::init();
Config::init();

if( Config::get("cache") === CACHE_LEVEL_OFF )
	LocalCache::scan();

// Connect to database
Hook::add( "init", "DB::init", 50 );

// Load session, if session cookie is set
if( isset($_COOKIE[Session::SESSION_COOKIE]) )
	Hook::add( "init", "Session::init", 100 );

Module::load( "all" );
Router::init();

Hook::run( "shutdown" );
