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
	public function __construct(){
		parent::__construct();
	}

	public function OnLoadTheme($tsamaMain,$layout){
		$this->NotifyObservers('OnLoadTheme',$tsamaMain,$layout);
	}
	public function LoadTheme($main){
			if($main){
				$nodes = $main->GetNodes();

				$theme = Tsama::_conf('THEME');
				//TODO: Alternatively Load from DB configuration

				$head = $nodes->GetFirstChild('body');

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
				
				$themeExt = $layout;
		
				if($themeExt != 'default'){
					//custom layouts
					//test for mobile and tablet
					$themeExt .= (Tsama::UA()->IsMobile() ? (Tsama::UA()->IsTablet() ? '-tablet' : '-mobile') : '');
				}else{
					$themeExt = (Tsama::UA()->IsMobile() ? (Tsama::UA()->IsTablet() ? 'tablet' : 'mobile') : 'default');
				}

				//TODO: Theme Ext

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

				$this->OnLoadTheme($main,$layout);
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