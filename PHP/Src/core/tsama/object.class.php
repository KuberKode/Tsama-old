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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."association.class.php");

class TsamaObject{
	/*Private Variables*/
	private $m_associations = NULL;
	private $m_children = null;
	private $m_parent = null;
	
	/*Public Variables*/
	public $objectId = "";
	
	/*Private Functions*/

	/*Public Functions*/
	public function __construct( $generateId = TRUE ){
		if( $generateId ){ $this->GenerateId(); }
		$this->m_children = array();
	}
	
	public function __destruct(){}
	
	public function AddObserver( $signal, &$observer, $slot ){
		if(is_null($this->m_associations)){$this->m_associations = new TsamaCollection();}
		
		//TODO: See if association does not already exist for this object and signal
		$assoc = new TsamaAssociation( $this, $signal );
		$assoc->AddObserver($observer,$slot);
		
		$this->m_associations->add($assoc);
	}
	
	public function NotifyObservers($signal){ //PHP5 will accept more args if passed through :)
		$args = func_get_args();
		unset($args[0]); //Remove signal from argument list array so as not to be processed since it has already been processed.
		
		$returnValue = FALSE;
		
		if(!is_null($this->m_associations)){
			//Go through events being observed
			foreach($this->m_associations->Items() as $assoc){
				//if event == signal
				if($assoc->GetEvent() == $signal){
					//Get all observers on event
					$asObservers = $assoc->GetObservers();
					foreach($asObservers as $asObserver){
								$observer = $asObserver->GetObject();
								$slot = $asObserver->GetEvent();
								//If get observer event to be called if it exist
								if(method_exists($observer,$slot)){
									//Call Observer event and pass arguments through if any
									if(count($args)==0){
										call_user_func(array($observer, $slot));
									}else{
										call_user_func_array(array($observer, $slot), $args);
									}
									$returnValue = TRUE;
								}
					
					}
				}
			}
		}
		return $returnValue;
	}
	
	//Genereate unique id.
	public function GenerateId($more_entropy  = TRUE){
		$this->objectId = uniqid($more_entropy);
		return($this->objectId);
	}
	
	public function UniqueId(){
		return $this->GenerateId(TRUE);
	}
	
	//Connect this object's signal with an observer
	public function Observe($signal,&$observer,$slot){
		$this->AddObserver($signal,$observer,$slot);
	}
	
	//Set parent object
	public function SetParent(&$parent){ $this->m_parent = $parent; }
	public function &GetParent(){ return $this->m_parent; }
	
	//Handle Child Objects
	public function AddChildObject(&$object){
		if(is_object($object)){
			$key = count($this->m_children);
			$object->SetParent($this);
			$this->m_children[] = clone $object;
			$object = null;
			return $key;
		}
		return null;
	}
	
	//Handle Batch of Child Objects
	public function AddChildObjects(&$objects){
		$newObs = array();
		foreach($objects as $object){
				$object->SetParent($this);
				$newObs[] = clone $object;
				$object = null;
			}
		$this->m_children = array_merge_recursive($this->m_children, $newObs);
		return TRUE;
	}
	
	public function &GetChild($key){
		if(array_key_exists($key,$this->m_children)) {	return $this->m_children[$key]; }
		return NULL; 
	}
	public function &GetChildren(){ return $this->m_children; }
	public function HasChildren(){ 
		if(count($this->m_children)>0){return TRUE;} 
		return FALSE; 
	}
	
	/*Public Static Functions*/
	public static function _observe(&$subject,$signal,&$observer,$slot){
		$subject->AddObserver($signal,$observer,$slot);
	}
}

?>