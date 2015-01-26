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

		self::$types[base64_encode($title)] = $type;
	}

	static public function get( $title=null )
	{
		if( $title === null )
			$title = Heap::get( "type" );

		$title = base64_encode($title);

		if( isset(self::$types[$title]) )
			return self::$types[$title];
		else
			return self::$types[base64_encode(DEFAULT_PAGE_TYPE)];
	}

	static public function parent_types( $id=null )
	{
		if( !$id )
			$id = Heap::get( "id" );

		// Parent's type
		$res = self::get( Page::type(Page::level($id)-1) );
		$res = $res->sub;
		$res[] = Heap::get( "type" );

		return array_unique( $res );
	}
}
