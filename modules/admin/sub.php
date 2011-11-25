<?
	hook( "init", "sub_init", 97 );
	
	// Добавление/удаление страниц
	function sub_init()
	{
		global $id;
		
		// Добавление страницы
		if( isset($_GET["page_add"]) )
		{
			$gid = (int)$_GET["page_add"];
			$query = "INSERT INTO page (gid, title, text, type) VALUES ($gid, '{$_POST["title"]}', '', '{$_POST["type"]}')";
			mysql_query( $query );
			$id = mysql_insert_id();
			
			// Другие действия при добавлении
			run( "sub_add", $id );
			
			// Переход на созданную страницу
			header( "Location: ".ADMIN."id=$id" );
			die;
		}
		
		// Рекурсивное удаление страницы
		function page_del( $id )
		{
			// Потомки
			$query = "SELECT id FROM page WHERE gid=$id";
			$res = mysql_query( $query );
			while( $row = mysql_fetch_array($res) )
				page_del( $row["id"] );
			
			// Сама страница
			$query = "DELETE FROM page WHERE id=$id";
			mysql_query( $query );
			
			// Доп. поля
			$query = "DELETE FROM prop WHERE id=$id";
			mysql_query( $query );
			
			// Другие действия при удалении
			run( "sub_del", $id );
		}
		
		// Удаление страниц
		if( isset($_GET["page_del"]) )
		{
			// Удаление
			$del = (int)$_GET["page_del"];
			page_del( $del );
			
			// Переход обратно
			header( "Location: ".ADMIN."id=$id" );
			die;
		}
		
		if( isset($_GET["page_sort"]) )
		{
			$i = 0;
			
			foreach( $_GET["p"] as $k => $v )
			{
				$k = (int)$k;
				$v = (int)$v;
				$query = "UPDATE page SET pos=$k WHERE id=$v";
				mysql_query( $query );
			}
			
			// Переход обратно
			header( "Location: ".ADMIN."id=$id" );
			die;
		}
		
		// Нужны ли подразделы
		global $TYPE;
		global $PAGE_TYPE;
		
		if( isset($id) && !$PAGE_TYPE[$TYPE]["nosub"] )
			hook( "content", "sub_content", 60 );
	}
	
	function sub_content()
	{
		global $id;
		global $TYPE;
		global $PAGE_TYPE;
		
		if( $id )
			echo "<h3>Подразделы</h3>\n";
		else
			echo "<h3>Разделы</h3>\n";
		
		// Список поразделов
		?>
			<table id='sub' class='noth'>
		<?
		$query = "SELECT id, title, type, hide FROM page WHERE gid=$id ORDER BY pos";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			echo "<tr id='{$row["id"]}'><td><a href='".ADMIN."id={$row["id"]}'>";
			if( $row["hide"] )
				echo "<img src='modules/img/hide.png'>";
			else
				echo "<img src='modules/img/edit.png'>";
			
			echo " {$row["title"]}</a></td>";
			
			echo "<td>{$row["type"]}</td>";
			
			// Другие операции над страницей
			run( "sub_action", $row["id"] );
			
			echo "<td><a href='".ADMIN."id=$id&page_del={$row["id"]}' onclick='if(confirm(\"Удалить {$row["title"]} вместе с подразделами?\")) return true; return false;'><img src='modules/img/del.png'> Удалить</a></td></tr>\n";
		}
		?>
			<tr><td colspan='9'>
				<form method='post' action='<?= ADMIN ?>page_add=<?= $id ?>'>
					<img src='modules/img/add.png'> Добавить:
					<input type='text' name='title'>
					<select name='type'>
					<?
						if( isset($PAGE_TYPE[$TYPE]["sub"]) )
							foreach( $PAGE_TYPE[$TYPE]["sub"] as $v )
								echo "<option>$v</option>\n";
						else
							foreach( $PAGE_TYPE as $k=>$v )
								echo "<option>$k</option>\n";
					?>
					</select>
					<?
						// Другие поля для добавления
						run( "sub_new" );
					?>
					<input type='submit' value='Добавить'>
				</form>
			</td></tr>
			</table>
		<?
	}
?>
