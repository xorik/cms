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

	static public function textarea( $id, $title, $key="text", $rich=1, $cols=80, $rows=20, $long=1, $two_col=0 )
	{
		if( $key == "text" )
			$value = Page::text( $id );
		else
			$value = Page::prop( $id, $key );

		$value = htmlspecialchars( $value );

		echo "<div class='padding'>";
		if( $two_col )
			echo "$title:<br>";
		else
			echo "<div class='descr'>$title:</div><div>";

		$class = $rich ? "rich" : "";
		if( $long ) $class .= " long";

		echo "<textarea name='$key' class='$class' cols='$cols' rows='$rows'>$value</textarea><div>";
	}
}
