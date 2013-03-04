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

	public function OnLoadTheme(){
		$this->NotifyObservers('OnLoadTheme',$this);
	}
	public function LoadTheme($main){
			if($main){
				$nodes = $main->GetNodes();

				//HTML5Parser::AddCSS($nodes,'body{ font-family: Arial; color: #333333; } #body-inner{ width: 80%; margin: 0 auto; padding: 12px; -moz-box-shadow: 0 0 6px #888; -webkit-box-shadow: 0 0 6px #888; box-shadow: 0 0 6px #888; } footer{ font-size: 90%; } header{ font-size: 120%; } article p{ text-align: justify; }');

				//CSS3Parser Experiment

				$styles = new TsamaNode('styles');

				$body = $styles->AddChild('body');
				$body->attr('font-family','Arial');
				$body->attr('color','#333333');

				$bi = $styles->AddChild('#body-inner');
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

				HTML5Parser::AddCSS($nodes,CSS3Parser::_out($styles,TRUE));

				$this->OnLoadTheme();
				return;
			}
	}

	public function OnLoadLayout(){
		$this->NotifyObservers('OnLoadLayout',$this);
	}
	public function LoadLayout($main){
			if($main){
				$nodes = $main->GetNodes();

				$body = $nodes->GetFirstChild('body');

				$bodyInner = $body->AddChild('body-inner');

				$header = $bodyInner->AddChild('header');
				$article = $bodyInner->AddChild('article');
				$footer = $bodyInner->AddChild('footer');

				$this->OnLoadLayout();
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