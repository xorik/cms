<?
	// Не тот модуль
	if( $_GET["do"] && $_GET["do"] != "edit" )
		return;
	
	hook_add( "init", "type_init", 1 );
	hook_add( "sub_action", "type_sub_action" );
	hook_add( "sub_new", "type_sub_new" );
	hook_add( "sub_add", "type_sub_add", 10 );
	
	// Узнаем тип страницы
	function type_init()
	{
		$id = (int)$_GET["id"];
		global $TYPE;
		
		$TYPE = get_prop( $id, "type" );
	}
	
	// Написать тип странице в списке разделов
	function type_sub_action( $id )
	{
		echo "<td>" . get_prop( $id, "type" ) . "</td>";
	}
	
	// Выбор типов
	function type_sub_new()
	{
		global $PAGE_TYPE;
		$PAGE_TYPE[] = "Страница";
		// Добавление типов
		hook_run( "page_type" );
		echo "Тип: <select name='type'>\n";
		foreach( $PAGE_TYPE as $v )
			echo "<option>$v</option>\n";
		echo "</select>";
	}
	
	// Добавление страницы
	function type_sub_add( $id )
	{
		add_prop( $id, "type", $_GET["type"] );
	}
	
?>
