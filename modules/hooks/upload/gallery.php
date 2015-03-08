<?php


Hook::add( "upload", "gallery_upload" );


function gallery_upload( $f )
{
	if( !$f["id"] || !($type=Img::type($f["path"])) )
		return;

	if( $type == "png" )
		Img::$bg_fill = array( 245, 245, 245, 0 );

	Img::resize( $f["path"], 300, 64, RESIZE_METHOD_MAX_SIDE, File::path($f["id"], ".jpg") );

	// Max image size
	$max = Config::get( "files", "max_img" );
	$size = Img::size( $f["path"] );
	if( isset($max[0]) && isset($max[1]) && ($size[0]>$max[0] || $size[1]>$max[1]) )
	{
		if( $type == "png" )
			Img::$bg_fill = null;

		Img::resize( $f["path"], $max[0], $max[1], RESIZE_METHOD_MAX_SIDE, false, $type );
	}
	Img::$bg_fill = false;
}
