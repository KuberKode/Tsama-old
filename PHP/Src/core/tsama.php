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

	private $m_nodes = NULL;
	private $m_debug = NULL;

	public function __construct(){
		$this->m_debug = array();

		$this->Load();
	}

	private function OnLoad(){
		$this->NotifyObservers('OnLoad');
	}
	public function Load(){

		$this->m_nodes = HTML5parser::createNodes();
		HTML5Parser::SetLanguage($this->m_nodes,'en');
		HTML5Parser::SetBase($this->m_nodes,'http://'.$_SERVER['SERVER_NAME'].'/');
		HTML5Parser::SetFavIcon($this->m_nodes,'favicon.ico');
		HTML5Parser::SetTitle($this->m_nodes,'Home','Tsama PHP');

		$this->OnLoad();
	}

	private function BeforeAddContent(){
		$this->NotifyObservers('BeforeAddContent');
	}
	private function OnAddContent(){
		$this->NotifyObservers('OnAddContent');
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
		$this->NotifyObservers('AfterAddContent');
	}

	private function OnRun(){
		$this->NotifyObservers('OnRun');
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