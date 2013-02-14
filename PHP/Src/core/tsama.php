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

define("TSAMA",TRUE);

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."tsama".DIRECTORY_SEPARATOR."object.class.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."tsama".DIRECTORY_SEPARATOR."html5parser.class.php");

class Tsama extends TsamaObject{

	/*Private Variables*/
	private $m_nodes = NULL;
	private $m_debug = NULL;

	/*Public Variables*/
	public $loaded = FALSE;

	public function __construct(){
		parent::__construct();

		$this->m_debug = array();

	}

	public function &GetNodes(){
		return $this->m_nodes;
	}

	private function OnLoad(){
		$this->NotifyObservers('OnLoad',$this);
	}
	public function Load(){

		$this->m_nodes = HTML5parser::createNodes();

		$this->OnLoad();

		$this->loaded = TRUE;
	}

	private function BeforeAddContent(){
		$this->NotifyObservers('BeforeAddContent',$this);
	}
	private function OnAddContent(){
		$this->NotifyObservers('OnAddContent',$this);
	}
	public function AddContent($parent,$tag,$content){
		$this->BeforeAddContent();
		$container = $this->m_nodes->GetFirstChild($parent);

		$child = $container->AddChild($tag);
		$child->SetValue($content);

		$this->OnAddContent();
		$this->AfterAddContent();
	}
	private function AfterAddContent(){
		$this->NotifyObservers('AfterAddContent',$this);
	}

	private function OnRun(){
		if(!$this->loaded){ $this->Load();}

		$this->NotifyObservers('OnRun',$this);

	}
	public function Run(){
		$this->OnRun();

		echo '<!DOCTYPE html>';
		echo HTML5Parser::_out($this->m_nodes,null,TRUE);
	}

	public function __destruct(){

	}
}
?>