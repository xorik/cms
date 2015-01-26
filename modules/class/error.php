<?php


define( "ERROR_TYPE_DEBUG", "debug" );
define( "ERROR_TYPE_NOTICE", "notice" );
define( "ERROR_TYPE_WARNING", "warning" );
define( "ERROR_TYPE_ERROR", "error" );


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

	static protected function log( $errno, $msg, $file, $line, $trace, $trace_skip=0 )
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

		$errtype = self::error_type($errno);

		$error = array("type"=>$errtype, "msg"=>$msg, "file"=>$file, "line"=>$line, "trace"=>$trace);
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

		$id = null;
		if( DB::$connected )
			$id = Log::add( self::LOG_TYPE, $error, $hash, 1 );

		// If error - show error template
		if( $errtype == ERROR_TYPE_ERROR )
		{
			while( ob_get_level() )
				ob_get_clean();

			Http::header( HTTP_ERROR_INTERNAL );
			Template::show( "modules/templates/error.tpl", 0, array("error"=>$error, "id"=>$id) );
			die;
		}
	}

	static public function error_handler( $errno, $msg, $file, $line )
	{
		self::log( $errno, $msg, $file, $line, debug_backtrace(), 1 );
	}

	static public function exception_handler( Exception $e )
	{
		self::log( E_USER_ERROR, "Exception: ". $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace() );
	}

	static public function shutdown_handler()
	{
		$e = error_get_last();

		// Hasn't fatal error, nothing to do
		if( !$e || !($e["type"] & (E_ERROR|E_PARSE)) )
			return;

		chdir( Router::$fs_root );
		self::log( $e["type"], $e["message"], $e["file"], $e["line"], array() );

		die;
	}

	static protected function usermsg( $errno, $msg )
	{
		$trace = debug_backtrace();
		self::log( $errno, $msg, $trace[1]["file"], $trace[1]["line"], $trace, 1 );
	}

	static public function debug( $msg )
	{
		self::usermsg( E_USER_NOTICE, $msg );
	}

	static public function warning( $msg )
	{
		self::usermsg( E_USER_WARNING, $msg );
	}

	static public function err( $msg )
	{
		self::usermsg( E_USER_ERROR, $msg );
	}

	static protected function error_type( $errno )
	{
		$errors = array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_STRICT, E_RECOVERABLE_ERROR, E_DEPRECATED, E_USER_DEPRECATED );
		$warnings = array( E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING );

		if( in_array($errno, $errors) )
			return ERROR_TYPE_ERROR;
		elseif( in_array($errno, $warnings) )
			return ERROR_TYPE_WARNING;
		elseif( $errno == E_NOTICE )
			return ERROR_TYPE_NOTICE;
		elseif( $errno == E_USER_NOTICE )
			return ERROR_TYPE_DEBUG;
		else
			throw new Exception( "Incorrect errno: $errno" );
	}
}
