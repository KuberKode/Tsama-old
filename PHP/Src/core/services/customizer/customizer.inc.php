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

class TsamaCustomizer extends TsamaObject{

	private $m_headFirst = null;
	private $m_headLast = null;
	private $m_bodyFirst = null;
	private $m_bodyLast = null;

	public function __construct(){
		parent::__construct();

		$this->m_headFirst = array();
		$this->m_headLast = array();
		$this->m_bodyFirst = array();
		$this->m_bodyLast = array();
	}

	public function OnLoadTheme($tsamaMain,$layout){
		$this->NotifyObservers('OnLoadTheme',$tsamaMain,$layout);
	}

	public function AddHeadFirst($main){

		if(count($this->m_headFirst) > 0){

			$nodes = $main->GetNodes();
			$head = $nodes->GetFirstChild('head');

			for($i = count($this->m_headFirst)-1;$i>=0;$i--){
				$head->AddChildObjectFirst($this->m_headFirst[$i]);
			}
		}

	}
	public function AddHeadLast($main){

		if(count($this->m_headLast) > 0){

			$nodes = $main->GetNodes();
			$head = $nodes->GetFirstChild('head');

			for($i = 0; $i < count($this->m_headLast); $i++){
				$head->AddChildObject($this->m_headLast[$i]);
			}
		}
	}
	public function AddBodyFirst($main){
		if(count($this->m_bodyFirst) > 0){

			$nodes = $main->GetNodes();
			$body = $nodes->GetFirstChild('body');

			for($i = count($this->m_bodyFirst)-1;$i>=0;$i--){
				$body->AddChildObjectFirst($this->m_bodyFirst[$i]);
			}

		}

	}
	public function AddBodyLast($main){

		if(count($this->m_bodyLast) > 0){

			$nodes = $main->GetNodes();
			$body = $nodes->GetFirstChild('body');

			for($i = 0; $i < count($this->m_bodyLast); $i++){
				$body->AddChildObject($this->m_bodyLast[$i]);
			}

		}

	}
	public function LoadTheme($main){
			if($main){
				$nodes = $main->GetNodes();

				$theme = Tsama::_conf('THEME');
				//TODO: Alternatively Load from DB configuration

				$head = $nodes->GetFirstChild('head');

				$style =  $head->GetFirstChildByAttribute('id','main-style');
				if(!$style){
					$style = $head->AddChild('link');
					if(!empty($id)){ $style->attr('id','main-style'); }
				}

				//TODO: if file exists else fallback to default
				$file = 'default';
				
				$style->attr('href',Tsama::_conf('BASE').'themes/'.$theme.'/'.$file.'.css');
				$style->attr('rel','stylesheet');
				$style->attr('type','text/css');
				
				$themeExt = $theme;
		
				if($themeExt != 'default'){
					//custom layouts
					//test for mobile and tablet
					$themeExt .= (Tsama::UA()->IsMobile() ? (Tsama::UA()->IsTablet() ? '-tablet' : '-mobile') : '');
				}else{
					$themeExt = (Tsama::UA()->IsMobile() ? (Tsama::UA()->IsTablet() ? 'tablet' : 'mobile') : 'default');
				}

				//TODO: Theme Ext

				$configLocation = Tsama::_conf('BASEDIR').DS.'themes'.DS.$theme.DS;
				$confFile = $theme.".nfo.xml";
				$themeUrl =  Tsama::_conf('BASE').'themes/'.$theme.'/';
				//check for theme conf in xml or TODO: db
				if(file_exists($configLocation.$confFile)){
					//load config for primary service
					Tsama::Debug("loading theme conf:". $configLocation.$confFile);
					$dom = new DomDocument();
					if($dom->load($configLocation.$confFile)){
						//load theme extensions, e.g. jquery, bootstrap
						if($dom->documentElement->hasChildNodes()){
							foreach($dom->documentElement->childNodes as $parentNode){
								if($parentNode->nodeType != XML_TEXT_NODE && $parentNode->nodeType != XML_COMMENT_NODE){

									$position = 'default';

									if($parentNode->hasAttributes()){
										if($parentNode->attributes->getNamedItem("position")){
											$position = $parentNode->attributes->getNamedItem("position")->nodeValue;
										}
									}

									if($parentNode->hasChildNodes()){
										foreach($parentNode->childNodes as $node){
											if($node->nodeType != XML_TEXT_NODE && $node->nodeType != XML_COMMENT_NODE){
												switch($position){
													case 'first':{
														if($parentNode->nodeName == 'head'){
															$key = count($this->m_headFirst);
															$this->m_headFirst[] = new TsamaNode($node->nodeName);
															$child = $this->m_headFirst[$key];
														}else{
															$key = count($this->m_bodyFirst);
															$this->m_bodyFirst[] = new TsamaNode($node->nodeName);
															$child = $this->m_bodyFirst[$key];
														}
													}break;
													case 'last':{
														if($parentNode->nodeName == 'head'){
															$key = count($this->m_headLast);
															$this->m_headLast[] = new TsamaNode($node->nodeName);
															$child = $this->m_headLast[$key];
														}else{
															$key = count($this->m_bodyLast);
															$this->m_bodyLast[] = new TsamaNode($node->nodeName);
															$child = $this->m_bodyLast[$key];
														}
													}break;
													default:{
														$where = $nodes->GetFirstChild($parentNode->nodeName);
														$child = $where->AddChild($node->nodeName);
													}break;
												}

												if($node->hasAttributes()){
													foreach($node->attributes as $attr){
														if($attr->name == 'src' || $attr->name == 'href'){
															$child->attr($attr->name,$themeUrl . $attr->value);
														}else{
															$child->attr($attr->name,$attr->value);
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

				$main->AddObserver('OnRun',$this,'AddHeadFirst');
				$main->AddObserver('OnRun',$this,'AddBodyFirst');
				$main->AddObserver('AfterRun',$this,'AddHeadLast');
				$main->AddObserver('AfterRun',$this,'AddBodyLast');

				//CSS3Parser Experiment

				//E.g. implementation in future: http://site/theme/name/css

				/*$styles = new TsamaNode('styles');

				$body = $styles->AddChild('body');
				$body->attr('font-family','Arial');
				$body->attr('color','#333333');

				$bi = $styles->AddChild('#container');
				$bi->attr('width','80%');
				$bi->attr('margin','0 auto');
				$bi->attr('padding','12px');
				$bi->attr('moz-box-shadow','0 0 6px #888');
				$bi->attr('-webkit-box-shadow','0 0 6px #888');
				$bi->attr('box-shadow','0 0 6px #888');

				$footer = $styles->AddChild('footer');
				$footer->attr('font-size','90%');

				//We want h4 to have the same styles as footer
				$h4 = $footer->AddChild('h4');

				$header = $styles->AddChild('header');
				$header->attr('font-size','120%');
				
				$ap = $styles->AddChild('article p');
				$ap->attr('text-align','justify');

				HTML5Parser::AddCSS($nodes,CSS3Parser::_out($styles,TRUE));*/

				$this->OnLoadTheme($main,$theme);
				return;
			}
	}

	public function OnLoadLayout($tsamaMain,$layout,$layoutExt){
		$this->NotifyObservers('OnLoadLayout',$tsamaMain,$layout,$layoutExt);
	}
	public function LoadLayout($main){
			if($main){
		
				$dom = new DomDocument();

				$location = Tsama::_conf('BASEDIR').DS.'layouts'.DS;

				$layout = Tsama::_conf('LAYOUT');
				//TODO: Alternatively Load Layout from DB configuration

				$xmlFile = $layout.".xml";
		
				if(!file_exists($location.$xmlFile)){
					return FALSE;
				}
				if(!$dom->load($location.$xmlFile)){
					return FALSE;
				}

				$nodes = $main->GetNodes();

				$body = $nodes->GetFirstChild('body');

				$this->LoadLayoutDOMNodes($dom->documentElement,$body);

				//Get mobile or tablet extensions for layout
				$layoutExt = $layout;
		
				if($layoutExt != 'default'){
					//custom layouts
					//test for mobile and tablet
					$layoutExt .= (Tsama::UA()->IsMobile() ? (Tsama::UA()->IsTablet() ? '-tablet' : '-mobile') : '');
				}else{
					$layoutExt = (Tsama::UA()->IsMobile() ? (Tsama::UA()->IsTablet() ? 'tablet' : 'mobile') : 'default');
				}

				//TODO: layout Ext

				$this->OnLoadLayout($main,$layout,$layoutExt);
				return;
			}
	}

	private function LoadLayoutDOMNodes(&$DOMNode,&$tsamaNode){
		if($DOMNode->hasChildNodes()){
			foreach($DOMNode->childNodes as $node){
				if($node->nodeType != XML_TEXT_NODE && $node->nodeType != XML_COMMENT_NODE){
						$child = $tsamaNode->addChild(strtolower($node->nodeName));
						//Load id attribute
						if($node->attributes->getNamedItem("id")){
							$child->attr("id",$node->attributes->getNamedItem("id")->nodeValue);
						}else{
							//set id to nodename
							$child->attr("id",$node->nodeName);
						}
						//Load className
						if($node->attributes->getNamedItem("class")){
							$child->attr("class",$node->attributes->getNamedItem("class")->nodeValue);
						}
						//Set primary node for service/content
						if($node->attributes->getNamedItem("primary")){
							$child->attr("class",'primary service');
							//$this->m_primary_node = $child;
						}
						$this->LoadLayoutDOMNodes($node,$child);
				}
			}
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