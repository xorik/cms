<?php


define( "DEFAULT_LOGIN", "admin" );
define( "DEVELOPER_LOGIN", "dev" );


class Auth
{
	static public $admin = false;
	static public $dev = false;


	static public function init()
	{
		Session::init();

		// Logon
		if( isset($_POST["admin_login"]) && isset($_POST["admin_pass"]) )
		{
			do
			{
				// Incorrect login
				if( $_POST["admin_login"]!=Config::get("admin", "login") && $_POST["admin_login"]!=DEVELOPER_LOGIN )
					break;


				// Incorrect password
				if( !self::password_check($_POST["admin_pass"]) )
					break;

				Session::set( "hash", Config::get("admin", "hash") );
				Session::set( "ipua", Http::ipua() );
				Session::set( "admin", 1 );
				if( $_POST["admin_login"] == DEVELOPER_LOGIN )
					Session::set( "dev", 1 );

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
		$hash = Session::get( "hash" );
		if( !$hash || $hash != Config::get("admin", "hash") || Http::ipua()!=Session::get("ipua") )
		{
			// Reset admin and dev if password changed
			if( $hash )
				self::reset_admin();

			Http::header( HTTP_ERROR_FORBIDDEN );
			Head::$title = Config::get("title") ." - вход в страницу администратора";

			if( Router::$type == PAGE_TYPE_AJAX )
				die( "Authentication required" );

			// Return logon formlogin
			return "modules/templates/login.tpl";
		}
	}

	static public function reset_admin()
	{
		Session::delete( "hash" );
		Session::delete( "ipua" );
		Session::delete( "admin" );
		Session::delete( "dev" );
	}

	static public function password_check( $pass )
	{
		return sha1( $pass . Config::get("admin", "salt") ) == Config::get("admin", "hash");
	}

	static public function password_set( $pass )
	{
		$admin = Config::get( "admin" );
		$salt = base64_encode( crc32(time()) );;
		$admin["salt"] = $salt;
		$admin["hash"] = sha1( $pass . $salt );
		Config::set( "admin", $admin );
		Config::save();
	}
}
