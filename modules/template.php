<?php


// Выполнить шаблон
function template( $file, $check_dir=0 )
{
	// Название файла кеша
	$cache_file = $file[0]=="/" ? cur_dir( $file ) : $file;
	$cache_file = "cache/". str_replace( "/", ".", $cache_file ) .".php";
	// Время модификации
	$cache_time = is_file( $cache_file ) ? filemtime( $cache_file ) : 0;
	$file_time = filemtime( $file );
	
	// Один файл
	if( !$check_dir )
	{
		// Нет файла-кеша или дата изменилась
		if( $file_time!=$cache_time )
		{
			template_make_cache( $file, $cache_file, $file_time );
		}
	}
	// Нужна проверка всех файлов
	else
	{
		$max_time = 0;
		foreach( glob(dirname($file) ."/*.tpl") as $f )
		{
			$max_time = max( $max_time, filemtime($f) );
		}
		
		if( $cache_time != $max_time )
		{
			template_make_cache( $file, $cache_file, $max_time );
		}
	}
	
	// Подставляем глобальные переменные
	extract( $GLOBALS, EXTR_REFS );
	
	require( $cache_file );
}


function template_make_cache( $file, $cache_file, $mtime )
{
	// Базовый каталог для шаблона
	global $BASEDIR;
	$BASEDIR = dirname( $file );
	
	$res = file_put_contents( $cache_file, template_parse(file_get_contents($file)) );
	if( !$res )
		die( "Can't create cache file: $file" );

	// Сдлать одинаковый modify time
	touch( $cache_file, $mtime );
}


function template_parse( $text )
{
	$regex = array(
		"/{IF ([^}]+)}/i", // IF, ELSE, ELSEIF
		"/{ELSEIF ([^}]+)}/i",
		"/{ELSE}/i",
		"/{\/IF}/i",
		"/{EACH ([^}]+)}/i", // FOREACH
		"/{\/EACH}/i",
		"/{(\\$[\d\w_\\[\\]\'\"]+)}/", // $var или $var[key]
		"/{([\d\w_]+)\\(([^)]*)\\)}/", // func()
		"/{{/", // PHP код
		"/}}/",
		"/\/\*.*?\*\//s" // Коммент
	);
	$replace = array(
		"<?php if(\\1): ?>",
		"<?php elseif(\\1): ?>",
		"<?php else: ?>",
		"<?php endif ?>",
		"<?php foreach(\\1): ?>",
		"<?php endforeach ?>",
		"<?php echo \\1 ?>",
		"<?php echo \\1(\\2) ?>",
		"<?php ",
		"?>",
		""
	);
	
	$text = preg_replace( $regex, $replace, $text );
	
	// Include
	return preg_replace_callback( "/{include ([\d\w_-]+)}/i", "template_include_callback", $text );
}


function template_include_callback( $m )
{
	global $BASEDIR;
	// Детектим рекурсию
	static $inc = array();
	if( $inc[$m[1]] < 30 )
	{
		$inc[$m[1]]++;
	}
	else
	{
		die( "Recursion detected!" );
	}
	
	return template_parse( file_get_contents($BASEDIR ."/". $m[1] .".tpl") );
}
