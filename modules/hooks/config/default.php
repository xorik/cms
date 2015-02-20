<?php


Hook::add( "nav", "default_nav" );


if( Configure::current() == "base" )
{
	Hook::add( "init", "base_config_init" );
	Hook::add( "content", "base_config_content" );
}


function base_config_init()
{
	if( empty($_POST) )
		return;

	Config::set( "title", $_POST["title"] );
	Config::save();

	// Logo upload and resize
	Hook::add( "upload", function( $f )
	{
		// TODO: noty
		if( !($type = Img::type($f["path"])) )
			return;

		if( $type == "png" )
			Img::$bg_fill = null;

		$size = Img::size( $f["path"] );
		if( $size[0]>360 || $size[1]>133 )
			Img::resize( $f["path"], 360, 133, RESIZE_METHOD_MAX_SIDE, false, "png" );
	});

	if( isset($_POST["rm_logo"]) && $_POST["rm_logo"] )
		unlink( "files/logo.png" );
	else
	{
		File::upload( null, "", function()
		{
			return "files/logo.png";
		});
	}

	Http::reload();
}


function base_config_content()
{
	Heap::logo( is_file($file="files/logo.png") ? $file : "" );
	Template::show( "modules/templates/config_base.tpl" );
}


function default_nav()
{
	$a = array( "list"=>Configure::$list, "current"=>Configure::current() );
	Template::show( "modules/templates/config_nav.tpl", 0, $a );
}
