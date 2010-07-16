<?
	hook_add( "content", "sub_content", 60 );
	hook_add( "init", "sub_init" );
	
	// Добавление/удаление страниц
	function sub_init()
	{
		// Добавление страницы
		if( isset($_GET["page_add"]) )
		{
			$gid = (int)$_GET["page_add"];
			$query = "INSERT INTO page (gid, title, text, type) VALUES ($gid, '{$_GET["title"]}', '', '{$_GET["type"]}')";
			mysql_query( $query );
			$id = mysql_insert_id();
			
			// Другие действия при добавлении
			hook_run( "sub_add", $id );
			
			// Переход на созданную страницу
			header( "Location: admin.php?id=$id" );
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
			hook_run( "sub_del", $id );
		}
		
		// Удаление страниц
		if( isset($_GET["page_del"]) )
		{
			// Удаление
			$del = (int)$_GET["page_del"];
			page_del( $del );
			
			// Переход обратно
			$id = (int)$_GET["id"];
			header( "Location: admin.php?id=$id" );
			die;
		}
		
		if( isset($_GET["page_sort"]) )
		{
			$id = (int)$_GET["id"];
			$i = 0;
			
			foreach( $_GET["p"] as $k => $v )
			{
				$k = (int)$k;
				$v = (int)$v;
				$query = "UPDATE page SET pos=$k WHERE id=$v";
				mysql_query( $query );
			}
			
			// Переход обратно
			header( "Location: admin.php?id=$id" );
			die;
		}
	}
	
	function sub_content()
	{
		global $id;
		global $PAGE_TYPE;
		
		if( $id )
			echo "<h3 id='sub_toggle'>Подразделы</h3>\n";
		else
			echo "<h3 id='sub_toggle'>Разделы</h3>\n";
		
		// Список поразделов
		?>
			<table class='noth'>
		<?
		$query = "SELECT id, title, type, hide FROM page WHERE gid=$id ORDER BY pos";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			echo "<tr id='{$row["id"]}'><td><a href='admin.php?id={$row["id"]}'>";
			if( $row["hide"] )
				echo "<img src='modules/img/hide.png'>";
			else
				echo "<img src='modules/img/edit.png'>";
			
			echo " {$row["title"]}</a></td>";
			
			echo "<td>{$row["type"]}</td>";
			
			// Другие операции над страницей
			hook_run( "sub_action", $row["id"] );
			
			echo "<td class='sort'></td>";
			
			echo "<td><a href='admin.php?id=$id&page_del={$row["id"]}' onclick='if(confirm(\"Удалить {$row["title"]} вместе с подразделами?\")) return true; return false;'><img src='modules/img/del.png'> Удалить</a></td></tr>\n";
		}
		?>
			<tr><td colspan='9'>
				<form>
					<input type='hidden' name='page_add' value='<?= $id ?>'>
					<img src='modules/img/add.png'> Добавить:
					<input type='text' name='title'>
					<select name='type'>
					<?
						foreach( $PAGE_TYPE as $v )
							echo "<option>$v</option>\n";
					?>
					</select>
					<?
						// Другие поля для добавления
						hook_run( "sub_new" );
					?>
					<input type='submit' value='Добавить'>
				</form>
			</td></tr>
			</table>
		<?
	}
?>
