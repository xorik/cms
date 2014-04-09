<?php
	hook( "init", "files_init", 95 );
	
	// Загрузка галереи аяксом
	function files_init()
	{
		global $id;
		global $TYPE;
		global $PAGE_TYPE;
		
		// Если страница существует
		if( $id && !$PAGE_TYPE[$TYPE]["nofiles"] )
			hook( "content", "files_content", 30 );
		else
			return;
	}
	
	// Показать галерею
	function files_content()
	{
		template( "modules/templates/files.tpl" );
	}
