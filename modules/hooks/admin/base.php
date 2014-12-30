<?php


hook( "init", "type_init", 10 );
hook( "init", "base_init", 90 );


// Дефолтный тип
function type_init()
{
	global $PAGE_TYPE;
	$PAGE_TYPE["Страница"] = array("descr"=>"Обычная страница с текстами и картинками");
}


function base_init()
{
	global $id, $TYPE, $PAGE_TYPE, $GID_TYPE, $LEVEL, $CONFIG, $BASE;

	// Нет страницы или главная -> выход
	if( !$id )
	{
		// Нет страницы
		if( !isset($id) )
			hook( "content", "not_found_content" );
		return;
	}

	// Данные
	$BASE = db_select_one( "SELECT title, text, hide FROM page WHERE id=$id" );

	hook( "content", "crumb_content", 5 );
	hook( "content", "base_content", 10 );
	hook( "base_show", "base_tpl", 10, "title" );
	hook( "base_show", "base_tpl", 15, "type" );

	// Описание типа
	if( $PAGE_TYPE[$TYPE]["descr"] )
	{
		hook( "base_show", "base_tpl", 16, "descr" );
		$BASE["descr"] = $PAGE_TYPE[$TYPE]["descr"];
	}
	hook( "base_show", "base_tpl", 80, "hide" );

	// Нужен текст
	if( !$PAGE_TYPE[$TYPE]["notext"] )
		hook( "base_show", "base_tpl", 90, "text" );

	// Путь, если не главная и не виртуальная
	if( $id!=$CONFIG["main"] && !$PAGE_TYPE[$TYPE]["virt"] )
	{
		hook( "base_show", "base_tpl", 20, "path" );
		$BASE["path"] = get_prop( $id, "path" );
	}

	// Типы
	$types = array( $TYPE );
	if( $PAGE_TYPE[$GID_TYPE[$LEVEL-1]]["sub"] )
		$types = array_merge( $types, $PAGE_TYPE[$GID_TYPE[$LEVEL-1]]["sub"] );

	// Убираем повторы
	$types = array_unique( $types );
	$BASE['types'] = $types;
}


// Крошки
function crumb_content()
{
	echo "<div id='crumb'>";
	run( "crumb" );
	echo "</div>";
}

// Редактирование
function base_content()
{
	template( "modules/templates/base.tpl" );
}

// Элемент базового редактирования
function base_tpl( $id, $data )
{
	global $DATA;

	$DATA = $data;
	template( "modules/templates/base_content.tpl" );
}


function not_found_content()
{
	echo "<h3>Страница не найдена!</h3>";
}
