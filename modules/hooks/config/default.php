<?php


Hook::add( "nav", "default_nav" );


function default_nav()
{
	$a = array( "root"=>Router::$root, "list"=>Configure::$list, "current"=>Configure::current() );
	Template::show( "modules/templates/config_nav.tpl", 0, $a );
}
