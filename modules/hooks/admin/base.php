<?php

Hook::add( "init", "base_init", 300 );

function base_init()
{
	if( !Heap::get("id") )
		return;

	$type = Types::get();
	Hook::add( "show", "Editor::input", 100, "Заголовок" );
	Hook::add( "show", "Editor::input", 150, "Путь", "path" );
	Hook::add( "show", "Editor::input", 170, "Тип", "type" );
	Hook::add( "show", "Editor::hide", 200 );
	Hook::add( "show", "Editor::textarea", 600, "Текст" );
	Hook::add( "content", "Editor::files", 800, "Изображения и файлы" );


	Hook::add( "content", function()
	{
		Template::show( "modules/templates/base.tpl" );
	});
}
