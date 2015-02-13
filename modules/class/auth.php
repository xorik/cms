<?php


define( "DEFAULT_LOGIN", "admin" );
define( "DEVELOPER_LOGIN", "dev" );


class Auth
{
	static public $admin = false;
	static public $dev = false;


	static public function init()
	{
		// Logon
		if( isset($_POST["admin_login"]) && isset($_POST["admin_pass"]) )
		{
			do
			{
				// Incorrect login
				$login = Config::get("admin", "login");
				if( !$login )
					$login = DEFAULT_LOGIN;

				if( $_POST["admin_login"]!=$login && $_POST["admin_login"]!=DEVELOPER_LOGIN )
					break;


				// Incorrect password
				if( !self::password_check($_POST["admin_pass"]) )
					break;

				Session::hash( Config::get("admin", "hash") );
				Session::ipua( Http::ipua() );
				Session::admin( 1 );
				if( $_POST["admin_login"] == DEVELOPER_LOGIN )
					Session::dev( 1 );

				Http::reload();
			}
			while( 0 );
		}

		// Logout
		if( isset($_GET["logout"]) )
		{
			self::reset_admin();
			Http::redirect( Router::$root );
		}

		// Check session
		$hash = Session::hash();
		$timeout = Config::get( "admin", "timeout" );
		// get from config or 15 min if isn't set
		$max_time = Session::$mtime + ($timeout?$timeout:900);
		if( !$hash || $hash != Config::get("admin", "hash") || Http::ipua()!=Session::ipua() || time()>$max_time )
		{
			// Reset admin and dev if password changed
			if( $hash )
				self::reset_admin();

			Http::header( HTTP_ERROR_FORBIDDEN );
			Head::$title = Config::get("title") ." - вход в страницу администратора";

			if( Router::$type == PAGE_TYPE_AJAX )
				die( "Authentication required" );

			// Return logon form
			return "modules/templates/login.tpl";
		}

		// Update session if half of max time expired
		if( time() > Session::$mtime + ($timeout?$timeout:900)/2 )
			Session::touch();
	}

	static public function reset_admin()
	{
		Session::delete( "hash" );
		Session::delete( "ipua" );
		Session::delete( "admin" );
		Session::delete( "dev" );
	}

	static public function password_check( $pass, $salt=null, $hash=null )
	{
		if( !$salt )
			$salt = Config::get( "admin", "salt" );
		if( !$hash )
			$hash = Config::get( "admin", "hash" );

		return sha1( $pass . $salt ) == $hash;
	}

	static public function password_hash( $pass, &$salt=null )
	{
		$salt = base64_encode( crc32(time()) );

		return sha1( $pass . $salt );
	}
}
