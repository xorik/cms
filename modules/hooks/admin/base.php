<?php

Hook::add( "init", "base_init", 800 );

function base_init()
{
	if( !Router::$type==PAGE_TYPE_AJAX || !isset($_GET["base"]) || !($id=Heap::get("id")) )
		return;

	$type = Types::get();

	// Title
	Hook::add( "show", "Editor::input", 100, "Заголовок" );

	// Path
	if(  !$type->virt && $id!=Config::get("main") )
		Hook::add( "show", "Editor::input", 150, "Путь", "path" );

	// Editable type for developer
	// TODO: enable/disable in config or by button
	if( Session::dev() )
		Hook::add( "show", "Editor::input", 170, "Тип", "type" );
	else
	{
		// Types list
		$bro = Types::parent_types();
		if( count($bro)>1 && !$type->lock_type )
			Hook::add( "show", "Editor::select", 170, "Тип", "type", $bro, Heap::get("type") );
	}

	Hook::add( "show", "Editor::hide", 200 );

	// Editor
	if( $type->editor )
		Hook::add( "show", "Editor::textarea", 600, "Текст" );

	// Files
	if( $type->files )
		Hook::add( "content", "Editor::files", 800, "Изображения и файлы" );


	Hook::add( "content", function()
	{
		Template::show( "modules/templates/base.tpl" );
	});
}
