<?php


// TODO: store error level (notice, warning, etc)
// TODO: internal error/warning function
// TODO: template for user, debug for developer + 500 header in case of fatal error


class Error
{
	const LOG_TYPE = "debug";

	static protected $errors_hash = array();
	static public $ingonre_notices = 0;

	static public function init()
	{
		// Register handlers
		set_error_handler( __CLASS__ ."::error_handler" );
		set_exception_handler( __CLASS__ ."::exception_handler" );
		register_shutdown_function( __CLASS__ ."::shutdown_handler" );

		// Hide errors
		error_reporting( 0 );
		ini_set( "display_errors", 0 );
	}

	static public function log( $errno, $msg, $file, $line, $trace, $trace_skip=0 )
	{
		if( $errno==E_NOTICE && self::$ingonre_notices )
			return;

		$file = cur_dir( $file, 1 );

		// Better trace
		if( $trace_skip )
			$trace = array_slice( $trace, $trace_skip );

		$trace = array_reverse( $trace );
		foreach( $trace as $k=>&$t )
		{
			if( !isset($t["file"]) && $k>0 )
			{
				$t["file"] = $trace[$k-1][0];
				$t["line"] = $trace[$k-1][1];
			}

			$t["file"] = isset($t["file"]) ? $t["file"]: null;
			$t["line"] = isset($t["line"]) ? $t["line"]: null;
			$t["args"] = isset($t["args"]) ? $t["args"]: null;

			$class = isset($t["class"]) ? $t["class"].$t["type"] : "";
			$t = array( cur_dir($t["file"], 1), $t["line"], $class.$t["function"] , $t["args"]);
		}

		$error = array("msg"=>$msg, "file"=>$file, "line"=>$line, "trace"=>$trace);
		$hash = md5( json($error) );

		// Error already logged
		if( in_array($hash, self::$errors_hash) )
			return;

		self::$errors_hash[] = $hash;

		// Store last url, GET and POST
		$error["url"] = Router::$path;
		if( !empty($_GET) )
			$error["get"] = $_GET;
		if( !empty($_POST) )
			$error["post"] = $_POST;

		// Workaround, if log class isn't loaded
		if( !class_exists("Log") )
			require( "modules/class/log.php" );

		Log::add( self::LOG_TYPE, $error, $hash, 1 );
	}

	static public function error_handler( $errno, $msg, $file, $line )
	{
		self::log( $errno, $msg, $file, $line, debug_backtrace(), 1 );
	}

	static public function exception_handler( Exception $e )
	{
		self::log( E_ERROR, "Exception: ". $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace() );
	}

	static public function shutdown_handler()
	{
		$e = error_get_last();

		// Hasn't fatal error, nothing to do
		if( !$e || !($e["type"] & (E_ERROR|E_PARSE)) )
			return;

		self::log( $e["type"], $e["message"], $e["file"], $e["line"], array() );

		die;
	}
}
