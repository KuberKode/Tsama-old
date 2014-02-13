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

class TsamaLocation extends TsamaObject{
	private $m_node = NULL;

	public function __construct(&$parentNode){
		parent::__construct();
		$this->m_node = $parentNode;
	}

	public function Node(){
		return $this->m_node;
	}

	public function Install(){
		require_once(dirname(__FILE__).DS.'location.install.php');
	}

	public function set($params){
		$this->get();
	}

	public function get($params){
		
	}
}
?>