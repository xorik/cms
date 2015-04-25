<?php


Hook::add( "init", "Auth::init", 200 );

Hook::add( "init", "files_init", 900 );
Hook::add( "file_show", "default_file_show" );
Hook::add( "files_action", "select_files_action", 100 );
Hook::add( "files_action", "del_files_action", 900 );

Module::load( "files" );
Hook::run( "init" );


function files_init()
{
	$id = Heap::id();
	if( !$id || !isset($_GET["gallery"]) )
	{
		Http::header( HTTP_ERROR_NOT_FOUND );
		return;
	}

	// Delete selected files
	if( isset($_POST["del"]) )
	{
		foreach( $_POST as $id => $v )
			if( $v == "on" )
				File::delete( $id );

		if( count($_POST) == 1 )
			Noty::err( "Файлы не выбраны" );
		else
			Noty::success( "Выбранные файлы удалены", 3 );

		echo json( Noty::get() );
		return;
	}

	$order = Config::get("files", "order")=="desc" ? "pos DESC, id DESC" : "pos, id";
	$files = DB::all( "SELECT id, type, gallery, filename FROM file WHERE gid=$id AND gallery=". DB::escape($_GET["gallery"]) ." ORDER BY $order" );
	if( !$files )
		return;

	Template::show( "modules/templates/files.tpl", 0, array("files"=>$files, "id"=>$id) );
}


function default_file_show( $f )
{
	if( Img::is_image_type( $f["type"] && is_file($prev=File::path($f["id"], ".jpg")) ) )
	{
		$text = "<img src='". File::path($f["id"]) ."'>";
		$html = "<img src='$prev' class='pic'>";
	}
	else
	{
		$text = "<a href='file/{$f["id"]}'>{$f["filename"]}</a>";
		$html = "<i class='fa fa-file-text-o'></i> {$f["filename"]}";
	}

	echo "<a href='#' data-text=\"$text\">$html</a>";
}


function select_files_action()
{
	echo "<label><input type='checkbox' class='files_sel'> <small>Выделить все</small> </label>";
}


function del_files_action()
{
	echo "<input type='submit' name='del' value='Удалить выбранные' data-title='Удалить выбранные файлы?'>";
}
