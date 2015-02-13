<?php


Configure::add( "security", "Безопасность" );


if( Configure::current() == "security" )
{
	Hook::add( "init", "security_config_init" );
	Hook::add( "content", "security_config_content" );
}


function security_config_init()
{
	if( empty($_POST) )
		return;

	$admin = Config::get( "admin" );
	if( !isset($admin["login"]) )
		$admin["login"] = "";

	// New login or new pass
	if( $_POST["login"]!=$admin["login"] || $_POST["pass1"] )
	{
		if( !$_POST["oldpass"] )
		{
			Noty::err( "Необходимо указать текущий пароль" );
			return;
		}

		if( !Auth::password_check($_POST["oldpass"]) )
		{
			Noty::err( "Неправильный тещущий пароль" );
			return;
		}

		// New login
		if( $_POST["login"] )
			$admin["login"] = $_POST["login"];
		elseif( isset($admin["login"]) )
			unset( $admin["login"] );

		// New password
		if( $_POST["pass1"] )
		{
			if( $_POST["pass1"] != $_POST["pass2"] )
			{
				Noty::err( "Пароли должны совпадать" );
				return;
			}

			if( strlen($_POST["pass1"]) <= 3 )
			{
				Noty::err( "Пароль слишком короткий!" );
				return;
			}

			$admin["hash"] = Auth::password_hash( $_POST["pass1"], $admin["salt"] );
		}
	}

	// Timeout
	$admin["timeout"] = (int)$_POST["timeout"];

	Config::set( "admin", $admin );

	Config::save();
	Http::reload();
}


function security_config_content()
{
	$list = array(
		60*15 => "15 минут",
		60*30 => "30 минут",
		60*60 => "1 час",
		60*60*2 => "2 часа",
		60*60*3 => "3 часа",
		60*60*4 => "4 часа",
		60*60*6 => "6 часов",
		60*60*12 => "12 часов",
		60*60*24 => "24 часа",
	);
	$a = array_merge( Config::get("admin"), array("timeout_list"=>$list) );
	Template::show( "modules/templates/config_security.tpl", 0, $a );
}
