<?php

Hook::add( "init", "Auth::init", 200 );
Module::load( "admin" );
Hook::run( "init" );

$id = Heap::get("id");

// Show page content
if( isset($_GET["base"]) )
{
	if( isset($_GET["id"]) && $_GET["id"]!=="0" && !$id )
	{
		echo "Страница не найдена";
		return;
	}
	Hook::run( "content", $id );
}

// Save the page
elseif( isset($_GET["save"]) )
{
	if( !$id )
		throw new Exception( "Id isn't set or page isn't exists" );

	$res = DB::update( "page", array("title"=>$_POST["title"], "text"=>$_POST["text"], "type"=>$_POST["type"], "hide"=>(int)$_POST["hide"]), "id=$id" );

	// Page path
	Page::prop( $id, "path", str_replace(" ", "_", $_POST["path"]) );

	// Run hooks
	Hook::run( "save", $id );

	if( $res === false )
		echo '{"error": "Ошибка сохранения страницы!"}';
	else
		echo '{"success": "Страница сохранена"}';
}

// Add new page
elseif( isset($_GET["add"]) )
{
	$type = Heap::get( "type" );
	// Root
	if( !$type )
	{
		$type = "root";
		$id = 0;
	}

	// Sub-page type
	$type = isset(Types::get($type)->sub[0]) ? Types::get($type)->sub[0] : DEFAULT_PAGE_TYPE;

	$id = DB::insert( "page", array("gid"=>$id, "title"=>$_POST["title"], "text"=>"", "type"=>$type) );
	if( !$id )
		throw new Exception( "Can't add page" );
	Heap::set( "type", $type );

	// Run add page hooks
	Hook::run( "add", $id );
	echo json( array("id"=>$id) );
}
elseif( isset($_GET["del"]) )
{
	$id = (int)$_POST["del"];
	if( !Page::get($id) )
		throw new Exception( "Page $id isn't exists" );

	Page::delete( $id );

	// TODO: return new id, if current removed
	echo "{}";
}

// Sort pages
elseif( isset($_GET["page_sort"]) )
{
	// Reverse sorting
	if( Types::get(Heap::get("type"))->reverse )
		$_GET["p"] = array_reverse( $_GET["p"] );


	// TODO: use POST
	foreach( $_GET["p"] as $k=>$v )
	{
		DB::update( "page", array("pos"=>(int)$k), "id=".(int)$v );
	}
}

// Files sort
// TODO: test
elseif( isset($_GET["file_sort"]) )
{
	// TODO: use POST
	foreach( $_GET["p"] as $k=>$v )
	{
		DB::update( "file", array("pos"=>(int)$k), "id=".(int)$v );
	}
}
