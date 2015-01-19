<?php


class Editor
{
	static public function input( $id, $title, $key="title" )
	{
		if( $key == "title" )
			$value = Page::title( 0, $id );
		elseif( $key == "type" )
			$value = Page::type();
		else
			$value = Page::prop( $id, $key );

		echo "<div class='descr'>$title:</div><div><input type='text' name='$key' value='$value'></div>";
	}

	static public function hide( $id )
	{
		$hide = DB::one( "SELECT hide FROM page WHERE id=$id" );
		if( $hide )
		{
			$c1 = ""; $c2 = "checked";
		}
		else
		{
			$c1 = "checked"; $c2 = "";
		}
		echo "<div>
				<div class='descr'></div>
				<label>
					<input type='radio' name='hide' value='0' $c1> Страница видна всем
					(<div class='round show'><i class='i-'></i></div>)
				</label>
				<label>
					<input type='radio' name='hide' value='1' $c2> Скрывать страницу в меню
					(<div class='round hide'><i class='i-'></i></div>)
				</label>
			</div>";
	}

	static public function textarea( $id, $title, $key="text", $rich=1, $cols=80, $rows=20, $long=1 )
	{
		if( $key == "text" )
			$value = Page::text( $id );
		else
			$value = Page::prop( $id, $key );

		$value = htmlspecialchars( $value );


		$class = $rich ? "rich" : "";
		if( $long ) $class .= " long";

		echo "<div class='padding'><div class='descr'>$title:</div>";
		echo "<div><textarea name='$key' class='$class' cols='$cols' rows='$rows'>$value</textarea></div></div>";
	}

	static public function select( $id, $title, $name, $list, $current="" )
	{
		// Convert array to assoc
		if( isset($list[0]) )
			$list = array_combine( $list, $list );

		echo "<div><div class='descr'>$title:</div><select name='$name'>";
		foreach( $list as $k=>$v )
		{
			$sel = $k==$current ? "selected" : "";
			echo "<option value='$k' $sel>$v</option>";
		}
		echo "</select></div>";
	}

	static public function hidden( $id, $name, $value )
	{
		echo "<input type='hidden' name='$name' value='$value'>";
	}

	static public function files( $id, $title, $gallery="gallery" )
	{
		if( Config::get("files", "url") )
			$url = "или по ссылке: <input type='text' name='url' placeholder='Ссылки на файлы в Интернете (через пробел)'>";
		else
			$url = "";

		echo "<div class='fields'>
				<div class='padding'>
					<div class='descr'>$title:</div>
					<div>
						<form action='". Router::$root ."ajax/upload?id=$id' method='post' enctype='multipart/form-data' target='upload-$gallery'>
							<input type='file' name='{$gallery}[]' multiple='true'>
							$url
							<input type='submit' class='btn' value='Загрузить'>
							<input type='hidden' name='gallery' value='gallery'>
							<small>(Максимум: ". ini_get("upload_max_filesize") ."b)</small>
						</form>
						<div class='files' data-id='$id' data-gallery='$gallery'></div>
						<iframe name='upload-$gallery' src='#' style='display:none'></iframe>
					</div>
				</div>
			</div>";
	}
}
