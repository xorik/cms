<?php


function json( $str, $pretty=0 )
{
	if( is_array($str) )
		return json_encode( $str, $pretty?(256|128):256 );
	elseif( is_string($str) )
		return json_decode( $str, true );
	else
		trigger_error( "json: expect string or array" );
}