<?php


define( "RESIZE_METHOD_MAX_SIDE", "self::resize_max_side" );
define( "RESIZE_METHOD_SIMPLE", "self::resize_simple" );
define( "RESIZE_METHOD_COVER", "self::resize_cover" );


class Img
{
	static public $jpeg_quality = 85;


	static public function resize( $file, $w, $h, $resize_method=RESIZE_METHOD_MAX_SIDE, $output=false, $format="jpeg" )
	{
		if( !is_file($file) )
			return false;

		$type = self::type( $file );
		if( !$type )
			return false;

		if( !is_callable($resize_method) )
			throw new Exception( "Resize method is not callable" );

		if( !function_exists($save_func="image$format") )
			throw new Exception( "Can't save image to $format format" );

		// Original and new image dimensions and proportion
		list( $old_w, $old_h ) = self::size( $file );
		$old_prop = (float)$old_w / (float)$old_h;
		$prop = (float)$w / (float)$h;

		// Load original image
		$func = "imagecreatefrom$type";
		$src = $func( $file );

		$dest = call_user_func( $resize_method, $src, $old_w, $old_h, $old_prop, $w, $h, $prop );
		imagedestroy( $src );

		$res = self::save( $dest, $output===false?$file:$output, $save_func );
		if( !$res )
			return false;

		imagedestroy( $dest );

		if( $output )
			chmod( $output, 0644 );

		return true;
	}

	static public function type( $file )
	{
		if( !is_file($file) )
			return false;

		static $finfo = null;
		if( !$finfo )
			$finfo = new finfo( FILEINFO_MIME_TYPE );

		$mime = $finfo->file( $file );
		if( preg_match("/^image\/(\w+)$/", $mime, $m) )
		{
			if( function_exists("imagecreatefrom".$m[1]) )
				return $m[1];
			else
				return false;
		}

		return false;
	}

	static public function size( $file )
	{
		return getimagesize( $file );
	}

	static public function resize_simple( $src, $old_w, $old_h, $old_prop, $w, $h, $prop )
	{
		$dest = imagecreatetruecolor( $w, $h );
		imagecopyresampled( $dest, $src, 0, 0, 0, 0, $w, $h, $old_w, $old_h );

		return $dest;
	}

	static public function resize_max_side( $src, $old_w, $old_h, $old_prop, $w, $h, $prop )
	{
		if( $prop > $old_prop )
			$w = $h*$old_prop;
		else
			$h = $w/$old_prop;

		$dest = imagecreatetruecolor( $w, $h );
		imagecopyresampled( $dest, $src, 0, 0, 0, 0, $w, $h, $old_w, $old_h );

		return $dest;
	}

	static public function resize_cover( $src, $old_w, $old_h, $old_prop, $w, $h, $prop )
	{
		$x = $y = 0;
		if( $prop > $old_prop )
		{
			$y = ($old_h-$old_w/$prop)/2;
			$old_h = $old_w/$prop;
		}
		else
		{
			$x = ($old_w-$old_h*$prop)/2;
			$old_w = $old_h*$prop;
		}

		$dest = imagecreatetruecolor( $w, $h );
		imagecopyresampled($dest, $src, 0, 0, $x, $y, $w, $h, $old_w, $old_h);

		return $dest;
	}

	static protected function save( $dest, $file, $save_func )
	{
		$quality = ($save_func=="imagejpeg") ? self::$jpeg_quality : null;
		return $save_func( $dest, $file, $quality );
	}
}
