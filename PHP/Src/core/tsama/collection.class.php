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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."object.class.php");

//Collection class for objects
class TsamaCollection extends TsamaObject{
	private $m_array = NULL;
	
	public function __construct() {
	
		parent::__construct();
		$this->m_array = array();
	}
	
	public function __destruct() {
	
		parent::__destruct();
		$this->m_array = array();
		unset($this->m_array);
	}
	
	//Add object to Collection.
	public function Add($object = NULL,$key = -1){
	
		if(!$object){ $object = new TsamaObject(); }
		if($key > -1){
			$this->m_array[$key] = $object;
		}else{
			$this->m_array[] = $object;
		}
		
		//unset($object); //Object is not a clone/copy but a reference. Do not uncomment.
	}
	
	//Clear collection of all objects
	public function Clear(){
	
		unset($this->m_array);
		$this->m_array = array();
	}
	//Get size of collection
	public function Count(){ return $this->Size();}
	
	//Get specific Object in collection as per key
	public function &Get($key)	{ return $this->Item($key); }
	
	public function &Item($key){
	
		if(array_key_exists($key,$this->m_array)) {	return $this->m_array[$key]; }
		$n = NULL; return $n;
	}
	//Return Object collection's array
	public function &Items(){
		if($this->count()>0){ return $this->m_array;}
		return NULL;
	}
	
	public function Length(){ return $this->Size();}
	
	//Remove speicific object as per key
	public function Remove($key)
	{
		if(array_key_exists($key,$this->m_array)) { unset($this->m_array[$key]); }
	}
	
	//Set specific object at key position or add if key does not exits.
	public function Set($object = NULL,$key = 0){
	
		if(!$object){ $object = new TsamaObject(); }
		
		if(!array_key_exists($key,$this->m_array)) 
		{ $this->Add($object); }
		else
		{ $this->m_array[$key] = $object; }
		
		//unset($object); //Object is not a clone/copy but a reference. Do not uncomment.
	}
	
	public function Size(){
	
		return count($this->m_array);
	}
}

?>