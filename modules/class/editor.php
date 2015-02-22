<?php


// TODO: rich editor for config


class Editor
{
	static public function input( $title, $key, $value, $size=0, $placeholder="", $postfix="", $type="text" )
	{
		if( $placeholder )
			$placeholder = "placeholder='$placeholder'";

		if( $size === "small" )
		{
			$class = "class='small'"; $size = "";
		}
		elseif( $size )
		{
			$class = "class='auto'"; $size = "size='$size'";
		}
		else
			$class = $size = "";

		echo "<div class='descr'>$title:</div><div><input type='$type' name='$key' value='$value' $class $size $placeholder>$postfix</div>";
	}

	static public function password( $title, $key, $saved=0, $size=0 )
	{
		self::input( $title, $key, "", $size, $saved?"Пароль сохранён":"", "", "password" );
	}

	static public function textarea( $title, $key, $value, $rich=0, $cols=80, $rows=20, $long=1 )
	{
		$value = htmlspecialchars( $value );

		$class = $rich ? "rich" : "";
		if( $long ) $class .= " long";

		echo "<div class='padding'><div class='descr'>$title:</div>";
		echo "<div><textarea name='$key' class='$class' cols='$cols' rows='$rows'>$value</textarea></div></div>";
	}

	static public function select( $title, $name, $list, $current="", $size=0, $postfix="" )
	{
		// Convert array to assoc
		if( isset($list[0]) )
			$list = array_combine( $list, $list );

		if( $size === "small" )
		{
			$class = "class='small'"; $size = "";
		}
		elseif( $size )
		{
			$class = "class='auto'"; $size = "style='width: {$size}em'";
		}
		else
			$class = $size = "";

		echo "<div><div class='descr'>$title:</div><select name='$name' $class $size>";
		foreach( $list as $k=>$v )
		{
			$sel = $k==$current ? "selected" : "";
			echo "<option value='$k' $sel>$v</option>";
		}
		echo "</select>$postfix</div>";
	}

	static public function checkbox( $title, $key, $value, $label, $checked=false )
	{
		$title = $title ? "$title:" : "";
		$checked = $checked ? "checked" : "";
		echo "<div class='descr'>$title</div><div><label><input type='checkbox' name='$key' value='$value' $checked> $label</label></div>";
	}

	static public function hidden( $name, $value )
	{
		echo "<input type='hidden' name='$name' value='$value'>";
	}

	static public function admin_input( $id, $title, $key="title", $size=0, $placeholder=""  )
	{
		if( $key == "title" )
			$value = Page::title( 0, $id );
		elseif( $key == "type" )
			$value = Page::type();
		else
			$value = Page::prop( $id, $key );

		self::input( $title, $key, $value, $size, $placeholder );
	}

	static public function admin_hide( $id )
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
					<input type='radio' name='hide' value='0' $c1>
					<i class='fa fa-check-circle fa-lg'></i>
					Страница видна всем
				</label>
				<label>
					<input type='radio' name='hide' value='1' $c2>
					<i class='fa fa-minus-circle fa-lg'></i>
					Скрывать страницу в меню
				</label>
			</div>";
	}

	static public function admin_textarea( $id, $title, $key="text", $rich=1, $cols=80, $rows=20, $long=1 )
	{
		if( $key == "text" )
			$value = Page::text( $id );
		else
			$value = Page::prop( $id, $key );

		self::textarea( $title, $key, $value, $rich, $cols, $rows, $long );
	}

	static public function admin_select( $id, $title, $name, $list, $current="" )
	{
		self::select( $title, $name, $list, $current );
	}

	static public function admin_files( $id, $title, $gallery="gallery" )
	{
		if( Config::get("files", "url") )
			$url = "или по ссылке: <input type='text' name='url' placeholder='Ссылки на файлы в Интернете (через пробел)'>";
		else
			$url = "";

		echo "<div class='fields'>
				<div class='padding'>
					<div class='descr'>$title:</div>
					<div>
						<form method='post' enctype='multipart/form-data'>
							<input type='file' name='{$gallery}[]' multiple>
							$url
							<button class='btn'><i class='fa fa-upload'></i> Загрузить</button>
							<small>(Максимум: ". ini_get("upload_max_filesize") ."b)</small>
							<div class='progress'>
								<div>
									<progress value='0'></progress>
								</div>
								<a href='#'>Отменить загрузку</a>
							</div>
						</form>
						<div class='files' data-id='$id' data-gallery='$gallery'></div>
					</div>
				</div>
			</div>";
	}
}
