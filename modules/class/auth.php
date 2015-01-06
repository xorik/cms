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
				Session::set( "admin", 1 );
				if( $_POST["admin_login"] == DEVELOPER_LOGIN )
					Session::set( "dev", 1 );

				clear_post();
			}
			while( 0 );
		}

		// Logout
		if( isset($_GET["logout"]) )
		{
			Session::set( "hash", null );
			Session::set( "admin", null );
			Session::set( "dev", null );
			Session::save();

			header( "Location: ". Router::$root );
			die;
		}

		// Check session
		$hash = Session::get( "hash" );
		if( $hash != Config::get("admin", "hash") )
		{
			// Reset admin and dev if password changed
			if( $hash )
			{
				Session::set( "hash", null );
				Session::set( "admin", null );
				Session::set( "dev", null );
			}

			// Show logon form and exit
			Head::css( "modules/res/login.css" );
			echo Head::get();
			Template::show( "modules/templates/login.tpl" );

			Hook::run( "shutdown" );
			die;
		}
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
