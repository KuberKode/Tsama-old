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

require_once(dirname(__FILE__).DS."tsama".DS."object.class.php");
require_once(dirname(__FILE__).DS."tsama".DS."html5parser.class.php");
require_once(dirname(__FILE__).DS."tsama".DS."css3parser.class.php");
require_once(dirname(__FILE__).DS."tsama".DS."useragent.class.php");
require_once(dirname(__FILE__).DS."tsama".DS."service.class.php");

class Tsama extends TsamaObject{

	/*Private Variables*/
	private $m_nodes = NULL;
	private $m_coreServices = NULL;

	/*Public Variables*/
	public $loaded = FALSE;

	public function __construct(){
		parent::__construct();

		$this->m_coreServices = array();
		$this->m_nodes = array();

	}

	public static function GetBase(){
		return Tsama::_conf('BASE');
	}

	public static function _conf($key,$value=''){
		global $_TSAMA_CONFIG;

		if(isset($_TSAMA_CONFIG[$key])){
			if(is_string($value) && empty($value)){
				return $_TSAMA_CONFIG[$key];
			}

			$_TSAMA_CONFIG[$key] = $value;

			return $_TSAMA_CONFIG[$key];
		}
		return NULL;
	}

	public static function Debug($value){
		global $_DEBUG;

		if(Tsama::_conf('DEBUG')){
			$_DEBUG[] = $value;
		}

	}

	public function &GetNodes(){
		return $this->m_nodes;
	}

	private function OnLoad(){
		$this->NotifyObservers('OnLoad',$this);
	}

	private function LoadCoreServices(){
		$dom = new DomDocument();

		$coreLocation = Tsama::_conf('BASEDIR').DS."core".DS;

		$xmlFile = "services.xml";
		
		if(file_exists($coreLocation.$xmlFile)){
			if($dom->load($coreLocation.$xmlFile)){
				if($dom->documentElement->hasChildNodes()){
					foreach($dom->documentElement->childNodes as $node){
						$serviceName = '';
						if($node->nodeType != XML_TEXT_NODE && $node->nodeType != XML_COMMENT_NODE){
							if(strtolower($node->nodeName)=='service'){
								if($node->attributes->getNamedItem("name")){
									$serviceName = $node->attributes->getNamedItem("name")->nodeValue;
									$serviceNodeName = $node->attributes->getNamedItem("node")->nodeValue;
									$serviceNode = NULL;
									if($serviceNodeName != 'global'){
										//get node
										$serviceNode = $this->m_nodes->GetFirstChild($serviceNodeName);
										if($serviceNode == NULL){
											$serviceNode = $this->m_nodes->GetFirstChildByAttribute('id',$serviceNodeName);
										}
									}
									$serviceKey = count($this->m_coreServices);
									$this->m_coreServices[] = new TsamaService($serviceName,$serviceNode,CORE_SERVICE);
									if($node->hasChildNodes()){
										foreach($node->childNodes as $child){
											if($child->nodeType != XML_TEXT_NODE && $child->nodeType != XML_COMMENT_NODE){
												//slot or parameter
												if(strtolower($child->nodeName)=='slots'){
													if($child->hasChildNodes()){
														foreach($child->childNodes as $slot){
															if($slot->nodeType != XML_TEXT_NODE && $slot->nodeType != XML_COMMENT_NODE){
																$slotName = '';
																$signalName = '';
																if($slot->attributes->getNamedItem("name")){
																	$slotName = $slot->attributes->getNamedItem("name")->nodeValue;
																	
																	$signalName = $slot->attributes->getNamedItem("signal")->nodeValue;
																	Tsama::Debug( 'Tsama::'.$signalName .'() -->> Tsama'.ucfirst($serviceName).'::'.$slotName.'()');
																	$this->AddObserver($signalName,$this->m_coreServices[$serviceKey]->GetClass(),$slotName);
																}
															}
														}
													}
												}
												if(strtolower($child->nodeName)=='parameters'){
													if($child->hasChildNodes()){
														foreach($child->childNodes as $parameter){
															if($parameter->nodeType != XML_TEXT_NODE && $parameter->nodeType != XML_COMMENT_NODE){
																//core service parameters is global
																if($parameter->attributes->getNamedItem("name")){
																	$param = strtoupper($parameter->attributes->getNamedItem("name")->nodeValue);
																	$paramValue = $parameter->attributes->getNamedItem("value")->nodeValue;
																	if($paramValue != 'default' && $this->_conf($param) == 'default'){
																		$this->_conf($param,$paramValue);
																	}
																}
																
															}
														}
													}
												}

											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	private function CreateInitialNodes(){
		switch(Tsama::_conf('OUTPUT')){
			case 'AJAX': case 'RAW':{
				$this->CreateRawNodes();
			}break;
			case 'CSS3':{
				$this->CreateCSS3Nodes();
			}break;
			case 'HTML5': default:{
				$this->CreateHTML5Nodes();
			}break;
		}
	}
	public function CreateRawNodes(){
		Tsama::_conf('OUTPUT','RAW');
		$this->m_nodes = array();
		$this->m_nodes = new TsamaNode('raw');
	}
	public function CreateCSS3Nodes(){
		Tsama::_conf('OUTPUT','CSS3');
		$this->m_nodes = array();
		$this->m_nodes = new TsamaNode('css3');
	}
	public function CreateHTML5Nodes(){
		Tsama::_conf('OUTPUT','HTML5');
		$this->m_nodes = array();
		$this->m_nodes = HTML5parser::CreateNodes();
	}
	public function Load(){

		$this->CreateInitialNodes();

		//Load Core Services
		$this->LoadCoreServices();

		$this->OnLoad();

		$this->loaded = TRUE;
	}

	public static function UA(){
		return new TsamaUserAgent();
	}

	public static function Redirect($location = ''){
		if(isset($_SESSION['redirect'])){
			if(empty($location)){$location = $_SESSION['redirect'];}
			unset($_SESSION['redirect']);
		}
		if(!empty($location)){ header("Location:".$location); return TRUE;}
		return FALSE;
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
		global $_DEBUG;

		$this->OnRun();

		if(Tsama::Redirect()){ return ;}
		
		if(Tsama::_conf("DEBUG") && count($_DEBUG) > 0){
			$body = &$this->m_nodes->getFirstChild('body');

			if($body){
				$dbg = $body->addChild("pre");
			}else{
				$dbg = $this->m_nodes->addChild("pre");
			}
			$dbg->attr("id","debug");
			foreach($_DEBUG as $item){
				$dbg->setValue($dbg->getValue() . $item . "\r\n");
			}
		}

		switch(Tsama::_conf('OUTPUT')){
			case 'PNG': {
				header("Content-Type: image/png");
				$data = $this->m_nodes->GetFirstChild('data');
				echo (base64_decode($data->GetValue()));
			}break;
			case 'AJAX': case 'RAW':{
				echo HTML5Parser::_out($this->m_nodes,null,TRUE);
			}break;
			case 'CSS3':{
				echo CSS3Parser::_out($this->m_nodes,TRUE);
			}break;
			case 'HTML5': default:{
				echo '<!DOCTYPE html>';
				echo HTML5Parser::_out($this->m_nodes,null,TRUE);
			}break;
		}
		
	}

	public function __destruct(){

	}
}
?>