<?php


function cur_dir( $file, $filename=0 )
{
	// Real path
	if( strpos($file, "..") )
		$file = realpath( $file );

	// Directory name
	if( !$filename )
		$file =  pathinfo( $file, PATHINFO_DIRNAME );

	// Remove root from path
	return str_replace( FS_ROOT, "", $file );
}


function json( $str, $pretty=0 )
{
	if( is_array($str) )
		// JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE and JSON_PRETTY_PRINT if $pretty
		return json_encode( $str, $pretty?448:320 );
	elseif( is_string($str) )
		return json_decode( $str, true );
	else
		Error::warning( "json: expect string or array" );
}


function mask_search( $dirs, $file_mask )
{
	if( !is_array($dirs) )
		$dirs = array( $dirs );

	$list = array();

	foreach( $dirs as $dir )
	{
		// Search dirs
		if( $x = glob("$dir/*", GLOB_ONLYDIR) )
		{
			if( $x = mask_search($x, $file_mask) )
				$list = array_merge( $list, $x );
		}

		foreach( glob("$dir/$file_mask") as $file )
		{
			if( is_file($file) )
				$list[] = $file;
		}
	}

	return $list;
}
