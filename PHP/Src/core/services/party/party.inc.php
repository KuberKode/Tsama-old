<?php
/*
** 08 July 2013
**
** The author disclaims copyright to this source code. 
**
*************************************************************************/

if(!defined('TSAMA'))exit;

class TsamaParty extends TsamaObject{
	private $m_node = NULL;

	public function __construct(&$parentNode){
		parent::__construct();
		$this->m_node = $parentNode;
	}

	public function Node(){
		return $this->m_node;
	}

	public function set($params){
		$this->get();
	}


	public function get($params){
		
	}
}
?>