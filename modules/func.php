<?php


function clear_post()
{
	header( "Location: {$_SERVER["REQUEST_URI"]}" );
	die;
}


function cur_dir( $file )
{
	$base = str_replace( "index.php", "", $_SERVER["SCRIPT_FILENAME"] );
	$file = preg_replace( "|/[\w\d\._-]+$|", "", $file );

	return str_replace( $base, "", $file );
}


function json( $str, $pretty=0 )
{
	if( is_array($str) )
		return json_encode( $str, $pretty?(256|128):256 );
	elseif( is_string($str) )
		return json_decode( $str, true );
	else
		trigger_error( "json: expect string or array" );
}
