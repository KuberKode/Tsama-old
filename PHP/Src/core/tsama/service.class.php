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

require_once(dirname(__FILE__).DS."object.class.php");

define("CORE_SERVICE",1);
define("PRIMARY_SERVICE",2);
define("PRIMARY_EXTENSION",3);
define("SECONDARY_SERVICE",4);

class TsamaCommand{
	public $name = '';
	public $signal = FALSE;
	public $slot = FALSE;
}

class TsamaQuery{
	public $name = '';
	public $title = '';
	public $signal = FALSE;
	public $slot = FALSE;
}

class TsamaServiceInfo{
	public $name = '';
	public $title = '';
	public $author = '';
	public $license = '';
	public $description = '';
	public $copyright = '';
	public $aliases = null;
	public $command = null;
	public $commands = null;
	public $queries = null;
	public $query = '';
	public $signals = null;
	public $slots = null;
	public $isValid = FALSE;

	public function __construct(&$name,$location='services'){

		$this->name = $name;
		$this->aliases = Array();
		$this->commands = Array();
		$this->queries = Array();
		$this->signals = Array();
		$this->slots = Array();

		$serviceName = $name;

		$serviceLocation = Tsama::_conf('BASEDIR').DS.$location.DS.$serviceName.DS;

		//load nfo file
		$dom = new DomDocument();

		$xmlFile = $serviceName.".nfo.xml";
		
		if(file_exists($serviceLocation.$xmlFile)){
			$this->isValid = TRUE;
			if($dom->load($serviceLocation.$xmlFile)){
											//get title
				if($dom->documentElement->attributes->getNamedItem("title")){
					$this->title = $dom->documentElement->attributes->getNamedItem("title")->nodeValue;
				}
				if($dom->documentElement->hasChildNodes()){
					foreach($dom->documentElement->childNodes as $node){
						if($node->nodeType != XML_TEXT_NODE && $node->nodeType != XML_COMMENT_NODE){
							switch(strtolower($node->nodeName)){
								//get author
								case 'author':{
									$this->author = $node->nodeValue;
								}break;
								//get license
								case 'license':{
									$this->license = $node->nodeValue;
								}break;
								//get description
								case 'description':{
									$this->description = $node->nodeValue;
								}break;
								//get copyright
								case 'copyright':{
									$this->copyright = $node->nodeValue;
								}break;
								//aliases
								case 'aliases':{
									if($node->hasChildNodes()){
										foreach($node->childNodes as $alias){
											if($alias->nodeType != XML_TEXT_NODE && $alias->nodeType != XML_COMMENT_NODE){
												if($alias->attributes->getNamedItem("name")){
													$newKey = count($this->aliases);
													$this->aliases[] = $alias->attributes->getNamedItem("name")->nodeValue;
												}
											}
										}
									}
								}break;
								//commands
								case 'commands':{
									if($node->hasChildNodes()){
										foreach($node->childNodes as $command){
											if($command->nodeType != XML_TEXT_NODE && $command->nodeType != XML_COMMENT_NODE){
												if($command->attributes->getNamedItem("name")){
													$newKey = count($this->commands);
													$this->commands[] = new TsamaCommand();
													$this->commands[$newKey]->name = $command->attributes->getNamedItem("name")->nodeValue;
												}

												if($command->attributes->getNamedItem("default")){
													$this->command = $this->commands[$newKey];
												}
											}
										}
									}
								}break;
								//queries
								case 'queries':{
									if($node->hasChildNodes()){
										foreach($node->childNodes as $query){
											if($query->nodeType != XML_TEXT_NODE && $query->nodeType != XML_COMMENT_NODE){
												if($query->attributes->getNamedItem("name")){
													$newKey = count($this->queries);
													$this->queries[] = new TsamaQuery();
													$this->queries[$newKey]->name = $query->attributes->getNamedItem("name")->nodeValue;

													if($query->attributes->getNamedItem("title")){
														$this->queries[$newKey]->title = $query->attributes->getNamedItem("title")->nodeValue;
													}
												}

												if($query->attributes->getNamedItem("default")){
													$this->query = $this->queries[$newKey];
												}
											}
										}
									}

								}break;
								//signals
								case 'signals':{
									if($node->hasChildNodes()){
										foreach($node->childNodes as $signal){
											if($signal->nodeType != XML_TEXT_NODE && $signal->nodeType != XML_COMMENT_NODE){
												if($signal->attributes->getNamedItem("name")){
													$this->signals[] = $signal->attributes->getNamedItem("name")->nodeValue;

												}
											}
										}
									}
								}break;
								//slots
								case 'slots':{
									if($node->hasChildNodes()){
										foreach($node->childNodes as $slot){
											if($slot->nodeType != XML_TEXT_NODE && $slot->nodeType != XML_COMMENT_NODE){
												if($slot->attributes->getNamedItem("name")){
													$this->slots[] = $slot->attributes->getNamedItem("name")->nodeValue;

												}
											}
										}
									}
								}break;
								//?layouts?
								default: break;
							}
						}
					}
				}
			}
		}
	}
}

class TsamaService extends TsamaObject {

	private $m_class = null;
	private $m_info = null;
	
	public $name = '';
	public $command = '';
	public $query = '';
	public $type = 0;
	public $parameters = null;
	public $node = null;

	private $m_signals = null;
	private $m_slots = null;
	
	public function __construct($name,&$node,$type = SECONDARY_SERVICE){

		parent::__construct();

		$this->name = $name;
		$this->type = $type;
		$this->parameters = array();
		$this->node = $node;

		$classPrefix = 'Service';
		$location = 'services';

		if($type == CORE_SERVICE){
			$location = "core".DS."services";
			$classPrefix = 'Tsama';
		}

		$this->m_info = new TsamaServiceInfo($name,$location);

		if($this->m_info->isValid){

			$this->m_signals = $this->m_info->signals;
			$this->m_slots = $this->m_info->slots;
			$this->query = $this->m_info->query->name;
			$this->command = $this->m_info->command->name;

			$serviceLocation = Tsama::_conf('BASEDIR').DS.$location.DS.$name.DS;
			$fl = $name.".inc.php";

			if(file_exists($serviceLocation . $fl)){ require_once($serviceLocation.$fl); }
			$className = $classPrefix.ucfirst($name);

			if(class_exists($className)){
				$this->m_class = new $className($node);
			}

		}
	}

	public function &Info(){
		return $this->m_info;
	}
	//get information about a service from the service's nfo xml file
	public static function GetInfo($name){
		$nfo = new TsamaServiceInfo($name);
		return $nfo;
	}

	public function AddSignal($signalName){
		if(!in_array($signalName,$this->m_signals)){
			$this->m_signals[] = $signalName;
		}
	}
	public function HasSignal($signalName){
		return in_array($signalName,$this->m_signals);
	}

	public function AddSlot($slotName){
		if(!in_array($slotName,$this->m_slots)){
			$this->m_slots[] = $slotName;
		}
	}
	public function HasSlot($slotName){
		return in_array($slotName,$this->m_slots);
	}


	public function &GetClass(){
		return $this->m_class;
	}

	public function Execute(){
		//query is always default if commmand is not specified
		//if comand then command executes first, query second, always

		//http://site/service/command/parameters/?POST
		//http://site/service/query/parameters/?GET

		$what = $this->query;
		//retriev command or query if set

		$route = Tsama::_conf('ROUTE');

		if(isset($route[1])){
			$what = $route[1];
			$warr = explode('-', $what);
			if(count($warr)>1){
				$what = '';
				foreach ($warr as $value) {
					if(!empty($value)){
						$what .= ucfirst($value);
					}
				}
			}
			Tsama::Debug('Service: '.ucfirst($route[0]).'::'.$what.'()');
		}

		//TODO: Getting Parameters
		//TODO: Setting Observers
		//TODO: Call the Before function if exist
		if(method_exists($this->m_class, $what)){
 			$this->m_class->$what($this->parameters);
 		}else{
 			$qry = $this->query;
 			if(method_exists($this->m_class, $qry)){
 				$this->m_class->$qry($this->parameters);
 			}
 		}
 		//Call the After function if exist

		/*Random Thoughts To Be Reviewed*/

		//command/query/signal

		/*
		******Command query example*********

		- command: update person
		then
		- query: get latest details for that person

		************************************
		*/
 		
		//TODO: have authentication as a command/query?
		//Test member-session (id in db?)
			//Start anon-session
			//Create guest person
			//Try to identify person
				//If identified, try to authenticate person
					//If authenticate fails, up the login limits
						//insert message, redirect to login
					//If authenticated, get person info
						//Start member-session
		//Create Member Person

		//E.g. tsama/item/list/person/johan ... and tsama/people/list?output=ajax where service people is an alias of person :)
		//test for command with output ajax (e.g. tsama/command/paramter1/parameter2/parameter3/?output=ajax... or query tsama/query/paramter1/parameter2/parameter3/...
		//e.g. tsama/form/sign-on tsama/authenticate/person .... tsama/list/people/strydom/johannes ... tsama/save/person .... tsama/save/item/market
		/*End Random Thoughts*/
	}
	
}
?>