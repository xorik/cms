<?php


function cur_dir( $file, $filename=0 )
{
	// Path of root directory
	$root = str_replace( "index.php", "", $_SERVER["SCRIPT_FILENAME"] );
	// Full path
	$file = $filename ? realpath($file) : pathinfo( realpath($file), PATHINFO_DIRNAME );

	// Remove root from path
	return str_replace( $root, "", $file );
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
