<?php 
/*
** 12 February 2013
**
** The author disclaims copyright to this source code.  In place of
** a legal notice, here is a quote:
**
** If you’re willing to restrict the flexibility of your approach, 
** you can almost always do something better.
** - John Carmack
**
*************************************************************************/

if(!defined('TSAMA'))exit;

require_once(dirname(__FILE__).DS."node.class.php");

class CSS3Parser{

	public function __construct(){}

	static public function Start(&$node, $compress){

		$css = '';

		$NL = '';
		if(!$compress){ $NL = "\r\n"; }

		$css .= $node->GetName();

		return '{' . $NL. $css;

	}

	public static function Stop(&$node, $compress){

		$css = '';

		$NL = '';
		if(!$compress){ $NL = "\r\n"; }

		$css .= '}' . $NL;

		return $css;

	}

	public static function _out(&$node, $compress=false){

		$NL = '';
		if(!$compress){ $NL = "\r\n"; }

		$out = HTML5Parser::Start($node, $compress);

		$out .= HTML5Parser::Stop($node, $compress);

		return $out;

	}
}


?>