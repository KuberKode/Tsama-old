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

class TsamaPublisher extends TsamaObject{

	private $m_services = null;

	public function __construct(){
		$this->m_services = array();

		parent::__construct();
	}

	public function OnLoadSiteInfo(){
		$this->NotifyObservers('OnLoadSiteInfo',$this);
	}
	public function LoadSiteInfo($main){
		if($main){
			$nodes = $main->GetNodes();

			HTML5Parser::SetLanguage($nodes,'en');
			HTML5Parser::SetBase($nodes,Tsama::GetBase());
			HTML5Parser::SetFavIcon($nodes,'favicon.ico');
			HTML5Parser::SetTitle($nodes,'Home',Tsama::_conf('NAME'));

			$this->OnLoadSiteInfo();
		}
	}

	public function OnLoadServices(){
		$this->NotifyObservers('OnLoadServices',$this);
	}
	public function LoadServices($main){
		if($main){
			$nodes = $main->GetNodes();
			$serviceNode = null;

			$this->LoadSiteInfo($main);

			$route = Tsama::_conf('ROUTE');

			Tsama::Debug(print_r($route,TRUE));

			//primary service first
			$serviceName = 'default';
			$configName = 'home';

			if(isset($route[0])){$serviceName = $route[0]; $configName = $serviceName;}

			Tsama::Debug('Service => '.$serviceName);

			$serviceLocation = Tsama::_conf('BASEDIR').DS.'services'.DS.$serviceName.DS;

			$nfoFile = $serviceName.".nfo.xml";
			
			if(!file_exists($serviceLocation.$nfoFile)){
				Tsama::Debug('Service '.$serviceName.' not found. Using home configuration');
				$configName = 'home';
			}

			$configLocation =  Tsama::_conf('BASEDIR').DS.'conf'.DS;
			$confFile = $configName.".conf.xml";

			Tsama::Debug('Config: '.$configLocation.$confFile);

			//check for service conf in xml or TODO: db
			if(file_exists($configLocation.$confFile)){
				//load config for primary service
				$dom = new DomDocument();
				if($dom->load($configLocation.$confFile)){
					//load service
					if($dom->documentElement->hasChildNodes()){
						foreach($dom->documentElement->childNodes as $service){
							$primary = FALSE;
							$primaryKey = 0;
							if($service->nodeType != XML_TEXT_NODE && $service->nodeType != XML_COMMENT_NODE){
								if(strtolower($service->nodeName) == 'service'){
									if($service->attributes->getNamedItem("name")){
										//get real service name
										$serviceName = $service->attributes->getNamedItem("name")->nodeValue;

										//check for primary
										if(!$primary && $service->attributes->getNamedItem("primary")){
											if(strtolower($service->attributes->getNamedItem("primary")->nodeValue) == 'true' ){
												$primary = true;
											}
										}
										$title = '';
										$description = '';
										$params = null;
										$slots = null;
										$layoutNode = null;
										//retrieve service configurations
										foreach($service->childNodes as $conf){

											//if primary get custom title and description
											if(strtolower($conf->nodeName) == 'title'){ $title = $conf->nodeValue; }
											if(strtolower($conf->nodeName) == 'description'){ $description = $conf->nodeValue; }
											//get node for service from layout
											if(strtolower($conf->nodeName) == 'layout'){ 
												$id='';
												$node='';

												if($conf->attributes->getNamedItem("id")){
													$id = $conf->attributes->getNamedItem("id")->nodeValue;
													$layoutNode = $nodes->GetFirstChildByAttribute('id',$id);
												}
												if($layoutNode==null){
													if($conf->attributes->getNamedItem("node")){
														$node = $conf->attributes->getNamedItem("node")->nodeValue;
														$layoutNode = $nodes->GetFirstChild($node);
													}
												}

												if(!empty($title)){ $params['title']=$title; }
												if(!empty($description)){ $params['description']=$description; }
												
											}
											//load parameters
											if(strtolower($conf->nodeName) == 'parameters'){
												foreach($conf->childNodes as $param){
													if($param->nodeType != XML_TEXT_NODE && $param->nodeType != XML_COMMENT_NODE){
														$pName = $param->attributes->getNamedItem("name")->nodeValue;
														$pValue = $param->attributes->getNamedItem("value")->nodeValue;
														$params[$pName]=$pValue;
													}
												}
											} 
											//get observer info
											if(strtolower($conf->nodeName) == 'slots'){
												//get what to observe
												$slots  = $conf;
											}

										}
										//Add the service
										$serviceKey = count($this->m_services);
										if($primary){
											$this->m_services[] = new TsamaService($serviceName,$layoutNode,PRIMARY_SERVICE);
											$primaryKey = $serviceKey;
										}else{
											$this->m_services[] = new TsamaService($serviceName,$layoutNode,SECONDARY_SERVICE);
										}
										$currentService = $this->m_services[$serviceKey];
										$currentService->parameters = $params;
										//Check for slots
										if($slots){

											$observe = 'this';
											if($slots->attributes->getNamedItem("observe")){
												$observe = strtolower($slots->attributes->getNamedItem("observe")->nodeValue);
											}

											if($slots->hasChildNodes()){
												foreach ($slots->childNodes as $slot) {
													if($slot->nodeType != XML_TEXT_NODE && $slot->nodeType != XML_COMMENT_NODE){
														$signalName = $slot->attributes->getNamedItem("signal")->nodeValue;

														$activeService = $currentService;

														if($observe=='primary'){
															$activeService = $this->m_services[$primaryKey];
														}

														if($activeService->HasSignal($signalName)){
															if($slot->attributes->getNamedItem("name")){
																$slotName = $slot->attributes->getNamedItem("name")->nodeValue;
															}

															if(method_exists($activeService->GetClass(),"addObserver")){
																$activeService->GetClass()->addObserver($signalName,$currentService->GetClass(),$slotName);
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
			}else{
				Tsama::Debug('Config '.$serviceName.' not found. Using service as is.');
				//use first article node
				$serviceNode = $nodes->GetFirstChild('article');
				$this->m_services[] = new TsamaService($serviceName,$serviceNode,PRIMARY_SERVICE);
			}
			$this->OnLoadServices();
		}
	}

	public function OnLoadContent(){
		$this->NotifyObservers('OnLoadContent',$this);
	}
	public function LoadContent($main){
		if($main){
			foreach($this->m_services as $service){
				if(method_exists($service, 'Execute')){
					$service->Execute();
				}
			}
			$this->OnLoadContent();
			return;
		}
	}

	public function set(){
		if(!isset($_POST)){
			return FALSE;
		}
		$this->get();
		return TRUE;
	}
	public function get(){}
}
?>