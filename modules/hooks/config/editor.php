<?php

Configure::add( "editor", "Редактирование" );

if( Configure::current() == "editor" )
{
	Hook::add( "init", "editor_config_init" );
	Hook::add( "content", "editor_config_content" );
}


function editor_config_init()
{
	if( empty($_POST) )
		return;

	Config::set( "hide_new", (int)$_POST["hide_new"] );
	Config::set( "files", array(
		"max_img"=>array($_POST["max_w"], $_POST["max_h"]),
		"order"=>$_POST["order"],
		"scroll"=>(int)$_POST["scroll"],
		"url"=>isset($_POST["url"]) ? 1 : 0
	));

	Config::save();
	Http::reload();
}


function editor_config_content()
{
	Template::show( "modules/templates/config_editor.tpl", 0, Config::get("files") );
}
