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

	public function LoadTheme($main){
			if($main){
				$nodes = $main->GetNodes();

				HTML5Parser::AddCSS($nodes,'body{ font-family: Arial; color: #333333; } #body-inner{ width: 80%; margin: 0 auto; padding: 12px; -moz-box-shadow: 0 0 6px #888;
-webkit-box-shadow: 0 0 6px #888; box-shadow: 0 0 6px #888; } footer{ font-size: 90%; } header{ font-size: 120%; } article p{ text-align: justify; }');

				return;
			}
	}

	public function LoadLayout($main){
			if($main){
				$nodes = $main->GetNodes();

				$body = $nodes->GetFirstChild('body');

				$bodyInner = $body->AddChild('body-inner');

				$header = $bodyInner->AddChild('header');
				$article = $bodyInner->AddChild('article');
				$footer = $bodyInner->AddChild('footer');
				return;
			}
	}
}
?>