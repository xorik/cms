<?php


define( "RESIZE_METHOD_MAX_SIDE", "self::resize_max_side" );
define( "RESIZE_METHOD_SIMPLE", "self::resize_simple" );
define( "RESIZE_METHOD_COVER", "self::resize_cover" );


class Img
{
	static public $jpeg_quality = 85;
	static public $png_quality = 9;
	static public $bg_fill = false;


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
		if( $w == 0 ) $w = $old_w;
		if( $h == 0 ) $h = $old_h;

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

	static public function is_image_type( $type )
	{
		return in_array( $type, array("jpg", "jpeg", "png", "gif") );
	}

	static public function size( $file )
	{
		return getimagesize( $file );
	}

	static public function resize_simple( $src, $old_w, $old_h, $old_prop, $w, $h, $prop )
	{
		$dest = self::create( $w, $h );
		imagecopyresampled( $dest, $src, 0, 0, 0, 0, $w, $h, $old_w, $old_h );

		return $dest;
	}

	static public function resize_max_side( $src, $old_w, $old_h, $old_prop, $w, $h, $prop )
	{
		if( $prop > $old_prop )
			$w = $h*$old_prop;
		else
			$h = $w/$old_prop;

		$dest = self::create( $w, $h );
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

		$dest = self::create( $w, $h );
		imagecopyresampled($dest, $src, 0, 0, $x, $y, $w, $h, $old_w, $old_h);

		return $dest;
	}

	static public function create( $w, $h )
	{
		$dest = imagecreatetruecolor( $w, $h );

		if( self::$bg_fill === false )
			return $dest;

		if( self::$bg_fill === null )
			$c = array( 0, 0, 0, 127 );
		elseif( self::$bg_fill === true )
			$c = array( 255, 255, 255, 0 );
		else
			$c = self::$bg_fill;

		$color = imagecolorallocatealpha( $dest, $c[0], $c[1], $c[2], isset($c[3])?$c[3]:0 );

		imagefill( $dest, 0, 0, $color );
		imagesavealpha( $dest, true );

		return $dest;
	}

	static protected function save( $dest, $file, $save_func )
	{
		if( $save_func == "imagejpeg" )
			$quality = self::$jpeg_quality;
		elseif( $save_func == "imagepng" )
			$quality = self::$png_quality;
		else
			$quality = null;

		return $save_func( $dest, $file, $quality );
	}
}
