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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."collection.class.php");

class TsamaAssociation{
	//Event for the association
	private $m_event = '';
	//Subject object being observed
	private $m_subject = NULL;
	//Observers on subject
	private $m_observers = NULL;
	
	//Creaste an association on a subject and it's event
	public function __construct(&$subject,$event){
		$this->m_subject = $subject;
		$this->SetEvent($event);
	}
	
	//Get & Set Event
	public function GetEvent(){return $this->m_event;}
	public function SetEvent($event){ $this->m_event = $event; }
	
	//Return an array of Observers
	public function &GetObservers(){
		if($this->m_observers){
			return $this->m_observers->Items();
		}
		return 0;
	}
	
	//Add an Observer for the subject
	public function AddObserver(&$observer,$slot){
		if(is_null($this->m_observers)){$this->m_observers = new TsamaCollection();}
		$asObserver = new TsamaAssociation($observer,$slot);
		$this->m_observers->add($asObserver);
	}
	
	//Get & Set Subject
	public function GetObject(){return $this->m_subject;}
	public function SetObject(&$subject){ $this->m_subject = $subject; }
	public function GetSubject(){return $this->m_subject;}
	public function SetSubject(&$subject){ $this->m_subject = $subject; }
	
}
?>