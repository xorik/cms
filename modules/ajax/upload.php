<?php


Hook::add( "init", "Auth::init", 200 );

Hook::add( "init", "upload_init", 900 );
Hook::add( "upload", "gallery_upload" );
Module::load( "upload" );
Hook::run( "init" );


function upload_init()
{
	$url = isset($_POST["url"]) ? $_POST["url"] : "";
	$status = File::upload( $url );

	// File too big
	if( empty($_FILES) )
		Noty::err( "Файл не выбран или размер файла превысил лимит" );
	// No file selected
	elseif( empty($status) )
		Noty::err( "Файл не выбран" );
	// Upload ok
	elseif( count($status)==1 && isset($status[UPLOAD_STATUS_OK]) )
	{
		Noty::success( count($status[UPLOAD_STATUS_OK])>1 ? "Файлы успешно загружены" : "Файл успешно загружен" );
	}
	// Upload with errors
	else
	{
		foreach( $status as $type=>$list )
		{
			$list = implode(",", $list);
			if( $type == UPLOAD_STATUS_OK )
				Noty::success( "Загружен: $list" );
			elseif( $type == UPLOAD_STATUS_SIZE_EXCEED )
				Noty::err( "Размер файла привышен: $list" );
			elseif( $type == UPLOAD_STATUS_INCORRECT_URL )
				Noty::err( "Некорректный URL: $list" );
			elseif( $type == UPLOAD_STATUS_NETWORK_ERROR )
				Noty::err( "Ошибка скачивания файла: $list" );
			elseif( $type == UPLOAD_STATUS_EMPTY )
				Noty::err( "Нулевой размер: $list" );
			else
				Noty::err( "Ошибка загрузки: $list" );
		}
	}

	echo json( Noty::get() );
}


function gallery_upload( $f )
{
	if( !$f["id"] || !($type=Img::type($f["path"])) )
		return;

	if( $type == "png" )
		Img::$bg_fill = array( 245, 245, 245, 0 );

	Img::resize( $f["path"], 300, 64, RESIZE_METHOD_MAX_SIDE, File::path($f["id"], "", "jpg") );

	// Max image size
	$max = Config::get( "files", "max_img" );
	$size = Img::size( $f["path"] );
	if( isset($max[0]) && isset($max[1]) && ($size[0]>$max[0] || $size[1]>$max[1]) )
	{
		if( $type == "png" )
			Img::$bg_fill = null;

		Img::resize( $f["path"], $max[0], $max[1], RESIZE_METHOD_MAX_SIDE, false, $type );
	}
	Img::$bg_fill = false;
}
