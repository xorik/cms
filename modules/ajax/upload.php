<?php


Hook::add( "init", "Auth::init", 200 );

Hook::add( "init", "upload_init", 900 );
Hook::add( "upload", "gallery_upload" );
Module::load( "upload" );
Hook::run( "init" );


function upload_init()
{
	$url = isset($_POST["url"]) ? $_POST["url"] : "";
	$status = File::upload( $url );
}


function gallery_upload( $f )
{
	if( !$f["id"] || !($type=Img::type($f["path"])) )
		return;

	if( $type == "png" )
		Img::$bg_fill = array( 245, 245, 245, 0 );
	else
		Img::$bg_fill = false;

	Img::resize( $f["path"], 300, 64, RESIZE_METHOD_MAX_SIDE, File::path($f["id"], "", "jpg") );
}
