<?php

Hook::add( "init", "Auth::init", 200 );
Module::load( "admin" );
Hook::run( "init" );

// Show current level
function nav_level( $nid, $level, $type )
{
	// Sorting order
	if( Types::get($type)->reverse )
		$order = "pos DESC, id DESC";
	else
		$order = "pos, id";

	$rows = DB::all( "SELECT id, title, type, hide FROM page WHERE gid=$nid ORDER BY $order" );
	foreach( $rows as $row )
	{
		$type = Types::get( $row["type"] );
		// Hidden page
		$show = $row["hide"] ? "hide" : "show";
		echo "<li><i class='i-sort'></i>";
		// Ident
		for( $i=0; $i<$level-1; $i++ )
		{
			echo "<div class='indent'></div>";
		}
		// Removable
		if( $type->removable )
		{
			echo "<a href='#' class='round del' data-title='Удалить \"{$row["title"]}\" вместе с подразделами?'><i class='i-del'></i></a>";
		}
		// Non-removable
		else
		{
			echo "<div class='round lock'></div>";
		}
		echo "<div class='round $show'><i class='i-'></i></div>";
		echo "<a href='?id={$row["id"]}' class='block' data-id='{$row["id"]}' title='{$row["title"]}'>{$row["title"]}";
		// Arrow
		if( !empty($PAGE_TYPE[$row["type"]]["sub"]) )
			echo "<i class='i-arrow'></i>";

		echo "</a></li>";

		// Sub-pages
		if( !empty( $type->sub) )
		{
			echo "<div class='sub'>";
			// Add button
			$type = $type->sub[0];
			echo "<div class='add'>Добавить подраздел <a href='#' data-gid='{$row["id"]}' data-type='$type'><i class='i-plus'></i></a></div>";
			nav_level( $row["id"], $level+1, $row["type"] );
			echo "<hr></div>";
		}
	}
}

echo "<div id='nav_title'>";
$type = isset(Types::get("root")->sub[0]) ? Types::get("root")->sub[0] : DEFAULT_PAGE_TYPE;
echo "<a href='#' class='add' data-gid='0' data-type='$type'><i class='i-plus'></i></a>";
echo "<a href='". Router::$root ."admin' data-id='0'>Разделы</a></div>";

// Root list
nav_level( 0, 1, "" );

