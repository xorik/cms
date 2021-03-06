<?php


class Head
{
	const jquery = "modules/res/jquery.js";
	const noty = "modules/res/jquery.noty.js";
	const fonawesome = "modules/res/font-awesome.css";

	static public $head = array();
	static public $js = array();
	static public $css = array();
	static public $script = array();
	static public $raw_js = array();
	static public $new_js = false;
	static public $title = null;


	static public function js( $file )
	{
		self::$new_js = true;
		self::$js[] = $file;
	}

	static public function css( $file )
	{
		self::$css[] = $file;
	}

	static public function script( $script )
	{
		self::$new_js = true;
		self::$script[] = $script;
	}

	static public function raw_js( $script )
	{
		self::$new_js = true;
		self::$raw_js[] = $script;
	}

	static public function jquery()
	{
		self::$new_js = true;
		self::$js[] = self::jquery;
	}

	static public function noty()
	{
		self::jquery();
		self::$js[] = self::noty;
	}

	static public function fontawesome()
	{
		self::$css[] = self::fonawesome;
	}

	static public function get()
	{
		self::$css = array_unique( self::$css );

		$head = "<!DOCTYPE html>\n<head>\n";

		// If title isn't set - set default title
		if( self::$title === null )
		{
			if( Router::$type == PAGE_TYPE_ADMIN )
				self::$title = "Страница администратора - ". Config::get("title");
			elseif( Router::$type == PAGE_TYPE_CONTENT )
			{
				if( Heap::id() )
					self::$title = Config::get("title") ." - ". Page::title();
				else
					self::$title = Config::get("title") ." - Страница не найдена";
			}
		}

		if( self::$title )
			$head .= "\t<title>". self::$title ."</title>\n";

		foreach( self::$head as $v )
		{
			$head .= "\t$v\n";
		}

		foreach( self::$css as $v )
		{
			if( strpos($v, "//")===false )
				$v = ROOT . $v;
			$head .= "\t<link rel='stylesheet' href='$v'>\n";
		}

		if( self::$new_js )
			$head .= self::scripts();

		$head .= "</head>\n";
		return $head;
	}

	static public function scripts()
	{
		self::$new_js = false;
		self::$js = array_unique( self::$js );
		$script = "";

		foreach( self::$js as $v )
		{
			if( strpos($v, "//")===false )
				$v = ROOT . $v;
			$script .= "\t<script src='$v'></script>\n";
		}

		if( count(self::$script) )
		{
			$script .= "\t<script>\n";
			foreach( self::$script as $v )
			{
				$script .= "$v\n";
			}
			$script .= "\t</script>\n";
		}

		if( count(self::$raw_js) )
		{
			foreach( self::$raw_js as $v )
			{
				$script .= "$v\n";
			}
		}

		return $script;
	}
}
