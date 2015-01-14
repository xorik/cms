<?php

// Root and simple page type
define( "DEFAULT_PAGE_TYPE", "Страница" );
Types::set( DEFAULT_PAGE_TYPE, "Обычная страница с текстом и картинками" );


class Type
{
	public $descr;
	public $sub = array();
	public $editor;
	public $files;
	public $removable;
	public $lock_type;
	public $virt;
	public $reverse;
}


class Types {
	static public $types = array();


	static public function set( $title, $descr="", $sub=array(), $editor=1, $files=1, $removable=1, $lock_type=0, $virt=0, $reverse=0 )
	{
		$type = new Type;
		$type->descr = $descr;
		$type->sub = $sub;
		$type->editor = $editor;
		$type->files = $files;
		$type->removable = $removable;
		$type->lock_type = $lock_type;
		$type->virt = $virt;
		$type->reverse = $reverse;

		self::$types[$title] = $type;
	}

	static public function get( $title=null )
	{
		if( !$title )
			$title = Heap::get( "type" );

		if( isset(self::$types[$title]) )
			return self::$types[$title];
		elseif( isset(self::$types[DEFAULT_PAGE_TYPE]) )
			return self::$types[DEFAULT_PAGE_TYPE];
		else
			return false;
	}
}
