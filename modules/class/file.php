<?php


define( "UPLOAD_STATUS_OK", "ok" );
define( "UPLOAD_STATUS_INCORRECT_URL", "incorrect_url" );
define( "UPLOAD_STATUS_SIZE_EXCEED", "size_exceed" );
define( "UPLOAD_STATUS_INTERNAL", "internal" );
define( "UPLOAD_STATUS_EMPTY", "empty" );
define( "UPLOAD_STATUS_NETWORK_ERROR", "network_error" );


class File
{
	const DEFAULT_CALLBACK="self::default_path";

	static protected $file_id;


	static public function upload($url=null, $gallery=null, $path_callback=self::DEFAULT_CALLBACK )
	{
		if( $path_callback && !is_callable($path_callback) )
			throw new Exception( "Path callback is not callable" );

		Module::load( "upload" );

		$status = array();

		// Load from url
		if( $url )
		{
			if( !$gallery )
				throw new Exception( "Gallery name required when upload from URL" );

			$url = explode( " ", $url );
			foreach( $url as $u )
			{
				if( !$u )
					continue;
				$name = parse_url( $u, PHP_URL_PATH );
				if( !$name || strpos($u, "http")!==0 )
				{
					$status[UPLOAD_STATUS_INCORRECT_URL][] = $u;
					continue;
				}
				$name = pathinfo( $u, PATHINFO_BASENAME );
				$res = self::process( $u, $name, $gallery, $path_callback, 1 );
				$status[$res][] = $name;
			}
		}

		// For every upload files
		foreach( $_FILES as $gallery=>$f )
		{
			// One file
			if( !is_array($f["error"]) )
			{
				if( $f["error"] == UPLOAD_ERR_NO_FILE )
					continue;

				if( $res = self::check($f["error"], $f["size"]) )
				{
					$status[$res][] = $f["name"];
					continue;
				}

				$res = self::process( $f["tmp_name"], $f["name"], $gallery, $path_callback );
				$status[$res][] = $f["name"];
			}
			// Multi-upload
			else
			{
				foreach( $f["error"] as $k=>$v )
				{
					if( $f["error"][$k] == UPLOAD_ERR_NO_FILE )
						continue;

					if( $res = self::check($f["error"][$k], $f["size"][$k]) )
					{
						$status[$res][] = $f["name"][$k];
						continue;
					}

					$res = self::process( $f["tmp_name"][$k], $f["name"][$k], $gallery, $path_callback );
					$status[$res][] = $f["name"][$k];
				}
			}
		}

		return $status;
	}
	
	static protected function check( $error, $size )
	{
		if( $error == UPLOAD_ERR_INI_SIZE )
			return UPLOAD_STATUS_SIZE_EXCEED;
		
		if( $error != UPLOAD_ERR_OK )
			return UPLOAD_STATUS_INTERNAL;

		if( $size == 0 )
			return UPLOAD_STATUS_EMPTY;
		
		return 0;
	}

	static protected function process( $path, $filename, $gallery, $path_callback, $url=0 )
	{
		if( !$url && !is_uploaded_file($path) )
			return UPLOAD_STATUS_INTERNAL;

		$dest = call_user_func( $path_callback, $filename, $gallery );
		if( !$dest )
			return UPLOAD_STATUS_INTERNAL;

		$error = 0;
		do
		{
			// Load from URL
			if( $url )
			{
				$res = @file_get_contents( $path );
				if( !$res )
				{
					$error = UPLOAD_STATUS_NETWORK_ERROR;
					break;
				}

				$res = file_put_contents( $dest, $res );
				if( !$res )
				{
					$error = UPLOAD_STATUS_INTERNAL;
					break;
				}
			}
			// Get file
			else
			{
				if( !move_uploaded_file($path, $dest) )
				{
					$error = UPLOAD_STATUS_INTERNAL;
					break;
				}
			}
		}
		while( 0 );

		if( $error )
		{
			// Delete file from DB
			if( $path_callback == self::DEFAULT_CALLBACK )
				self::delete( self::$file_id );

			return $error;
		}

		chmod( $dest, 0644 );

		$id = $path_callback==self::DEFAULT_CALLBACK ? self::$file_id : null;
		Hook::run( "upload", array("path"=>$dest, "gallery"=>$gallery, "filename"=>$filename, "id"=>$id) );

		return UPLOAD_STATUS_OK;
	}

	static public function default_path( $filename, $gallery )
	{
		if( !Heap::get("id") )
			throw new Exception( "Heap id is not set for upload" );

		$ext = self::ext( $filename );
		$id = self::$file_id = DB::insert( "file", array("gid"=>Heap::get("id"), "filename"=>$filename, "type"=>$ext, "gallery"=>$gallery) );

		return "files/$id". ($ext?".$ext":"");
	}

	static public function delete( $id )
	{
		$path = self::path( $id );
		DB::delete( "file", "id=". DB::escape($id) );
		if( is_file($path) )
			unlink($path);

		// Delete related files
		if( $files = glob("files/{$id}_*", GLOB_NOSORT) )
		{
			foreach( $files as $file )
				unlink( $file );
		}
	}

	static public function ext( $file )
	{
		return pathinfo( $file, PATHINFO_EXTENSION );
	}

	static public function path( $id, $postfix=null, $new_ext=null )
	{
		$row = DB::row( "SELECT id, type FROM file WHERE id=". DB::escape($id) );
		if( !$row )
			return false;

		$ext = $new_ext ? $new_ext : $row["type"];

		return "files/{$row["id"]}". ($postfix===null?"":"_$postfix") . ($ext?".$ext":"");
	}
}
