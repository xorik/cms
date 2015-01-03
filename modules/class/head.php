<?php


class Head
{
	const jquery = "modules/res/jquery.js";
	const noty = "modules/res/jquery.noty.js";

	static public $head = array();
	static public $js = array();
	static public $css = array();
	static public $script = array();
	static public $new_js = false;


	static public function js( $file )
	{
		self::$js[] = $file;
	}

	static public function css( $file )
	{
		self::$css[] = $file;
	}

	static public function script( $file )
	{
		self::$new_js = true;
		self::$script[] = $file;
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

	static public function get()
	{
		self::$css = array_unique( self::$css );

		$head = "<!DOCTYPE html>\n<head>\n";
		foreach( self::$head as $v )
		{
			$head .= "\t$v\n";
		}

		foreach( self::$css as $v )
		{
			if( strpos($v, "//")===false )
				$v = Router::$root . $v;
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
				$v = Router::$root . $v;
			$script .= "\t<script src='$v'></script>\n";
		}

		if( count(self::$script) )
		{
			$script .= "\t<script>\n";
			foreach( self::$script as $v )
				$script .= "$v\n";
			$script .= "\t</script>\n";
		}

		return $script;
	}
}
