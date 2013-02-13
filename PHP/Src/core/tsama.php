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

class Tsama extends TsamaObject{

	public function __construct(){
		$this->Load();
	}

	private function OnLoad(){
		$this->NotifyObservers('OnLoad');
	}
	public function Load(){
		$this->OnLoad();
	}

	private function OnRun(){
		$this->NotifyObservers('OnRun');
	}
	public function Run(){
		$this->OnRun();
	}

	public function __destruct(){

	}
}
?>