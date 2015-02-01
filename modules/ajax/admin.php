<?php

Hook::add( "init", "Auth::init", 200 );
Module::load( "admin" );
Hook::run( "init" );

$id = Heap::id();

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

	$a = array("title"=>$_POST["title"], "hide"=>(int)$_POST["hide"]);

	if( isset($_POST["text"]) )
		$a["text"] = $_POST["text"];
	if( isset($_POST["type"]) )
		$a["type"] = $_POST["type"];

	$res = DB::update( "page", $a, "id=$id" );

	// Page path
	if( isset($_POST["path"]) )
		Page::prop( $id, "path", str_replace(" ", "_", $_POST["path"]) );

	// Run hooks
	Hook::run( "save", $id );

	if( $res === false )
		Noty::err( "Ошибка сохранения страницы!" );
	else
		Noty::success( "Страница сохранена" );

	echo json(Noty::get());
}

// Add new page
elseif( isset($_GET["add"]) )
{
	$type = Heap::type();
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
	Heap::type( $type );

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
	echo json( array() );
}

// Sort pages
elseif( isset($_GET["page_sort"]) )
{
	// Reverse sorting
	// TODO: check in php 5.3
	if( Types::get(Heap::type())->reverse )
		$_GET["p"] = array_reverse( $_GET["p"] );


	// TODO: use POST
	foreach( $_GET["p"] as $k=>$v )
	{
		DB::update( "page", array("pos"=>(int)$k), "id=".(int)$v );
	}
}

// Files sort
elseif( isset($_GET["file_sort"]) )
{
	// TODO: use POST
	foreach( $_GET["p"] as $k=>$v )
	{
		DB::update( "file", array("pos"=>(int)$k), "id=".(int)$v );
	}
}
