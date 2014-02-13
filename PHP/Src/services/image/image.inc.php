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

class ServiceImage extends TsamaObject{
	private $m_node = NULL;

	public function __construct(&$parentNode){
		parent::__construct();
		$this->m_node = $parentNode;
	}

	public function Node(){
		return $this->m_node;
	}

	public function set(){
		if(!isset($_POST)){
			return FALSE;
		}
		$this->get();
		return TRUE;
	}
	public function get($params){
		global $tsama;

		$main = $tsama->GetNodes();

		$main->ClearNodes();

		$route = Tsama::_conf('ROUTE');

		$imgKey = count($route)-1;

		$output = strtoupper(substr($route[$imgKey], strlen($route[$imgKey])-3,3));

		Tsama::_conf('OUTPUT',$output);

		$image = $route[count($route)-1];

		$dir = Tsama::_conf('BASEDIR').DS.'media'.DS.'visual'.DS.'images'.DS.'default.domain';

		for($i = 1; $i<= count($route)-2; $i++){
			$dir .= DS . $route[$i];
		}

		$imgDataFl = $dir . DS . $image;

		if(file_exists($imgDataFl)){
			$imgData = base64_encode(file_get_contents($imgDataFl));
		}

		//get image from site directory under media
		$data = $main->AddChild('data');
		$data->SetValue($imgData);
	}
}
?>