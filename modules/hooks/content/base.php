<?php


Hook::add( "content", "base_content" );
Hook::add( "content", "admin_goto", 900 );


function base_content()
{
	if( $id=Heap::id() )
	{
		$text = Page::text($id);
		if( strpos(Router::$path, "/")!==false )
			$text = preg_replace( '/"(files?\/\d+[_\w]*\.?[\w]*)"/', Router::$root."\\1", $text );

		echo $text;
	}
	else
		echo "<h3>Ошибка 404</h3><p>Страница \"". Router::$path ."\" не найдена</p>";
}


function admin_goto()
{
	if( isset($_COOKIE["sess"]) && Session::admin() && $id=Heap::id() )
		echo "<br><br><a href='". Router::$root ."admin?id=$id'>Перейти к редактированию</a>";
}
