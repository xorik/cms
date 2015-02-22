<?php

Hook::add( "init", "Auth::init", 200 );
Module::load( "admin" );
Hook::run( "init" );

echo "<div id='nav_title'>";
$type = isset(Types::get("root")->sub[0]) ? Types::get("root")->sub[0] : DEFAULT_PAGE_TYPE;
echo "<a href='#' class='add' data-gid='0' data-type='$type'><i class='fa fa-plus'></i></a>";
echo "<a href='". ROOT ."admin' data-id='0'>Разделы</a></div>";

// Pages list
global $list;
$list = array();

$rows = DB::all( "SELECT gid, id, title, type, hide FROM page ORDER BY pos,id" );
foreach( $rows as $row )
{
	$list[$row["gid"]][] = $row;
}
unset( $rows );
nav_level( 0, 1, "root" );



// Show current level
function nav_level( $id, $level, $type )
{
	global $list;

	if( !isset($list[$id]) )
		return;

	// Sorting order
	if( Types::get($type)->reverse )
		$list[$id] = array_reverse( $list[$id] );

	foreach( $list[$id] as $row )
	{
		$type = Types::get( $row["type"] );
		// Hidden page
		$show = $row["hide"] ? "minus" : "check";
		$show_title = $row["hide"] ? "Скрытая страница": "Видимая страница";
		echo "<li><i class='fa fa-sort fa-2x'></i>";
		// Ident
		for( $i=0; $i<$level-1; $i++ )
		{
			echo "<div class='indent'></div>";
		}
		// Removable
		$rm = $type->removable ? "" : " hidden";
			echo "<a href='#' class='del$rm' data-title='Удалить \"{$row["title"]}\" вместе с подразделами?' title='Удалить'><i class='fa fa-times-circle'></i></a>";

		echo "<a class='hide' title='$show_title'><i class='fa fa-$show-circle'></i></a>";
		echo "<a href='?id={$row["id"]}' class='block' data-id='{$row["id"]}' title='{$row["title"]}'>{$row["title"]}";
		// Arrow
		if( !empty( $type->sub) || !empty($list[$row["id"]]) )
			echo "<i class='sub fa fa-caret-right'></i>";

		echo "</a></li>";

		// Sub-pages
		if( !empty( $type->sub) || !empty($list[$row["id"]]) )
		{
			echo "<div class='sub'>";
			// Add button
			if( !empty( $type->sub) )
			{
				$type = $type->sub[0];
				echo "<div class='add'>Добавить подраздел <a href='#' data-gid='{$row["id"]}' data-type='$type'><i class='fa fa-plus'></i></a></div>";
			}
			nav_level( $row["id"], $level+1, $row["type"] );
			echo "<hr></div>";
		}
	}
}
