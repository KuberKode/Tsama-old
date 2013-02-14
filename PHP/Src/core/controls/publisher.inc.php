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
	public function __construct(){
		parent::__construct();
	}

	public function LoadSiteInfo($main){
			if($main){
				$nodes = $main->GetNodes();

				HTML5Parser::SetLanguage($nodes,'en');
				HTML5Parser::SetBase($nodes,'http://'.$_SERVER['SERVER_NAME'].'/');
				HTML5Parser::SetFavIcon($nodes,'favicon.ico');
				HTML5Parser::SetTitle($nodes,'Home','Tsama PHP');
			}
	}

	public function LoadContent($main){
			if($main){

				$main->AddContent('header','h1','Hello world!');
				$main->AddContent('article','p','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vulputate hendrerit est. Cras velit diam, gravida sit amet sagittis eget, eleifend vel felis. Praesent in velit non odio tempus lobortis sed sed ligula. Donec pellentesque accumsan ligula et venenatis. Maecenas et sem nunc, ac commodo nisl. Vivamus quis urna enim. Phasellus lacus velit, accumsan id interdum eget, pellentesque eu quam. Integer venenatis libero sit amet tortor interdum accumsan. Proin at quam non mauris ullamcorper dapibus. Pellentesque semper, ipsum ut cursus porta, dui leo consequat libero, eu placerat eros urna eu ipsum. Nullam elit metus, mollis ut posuere at, blandit sit amet lacus. Donec varius fermentum gravida. Maecenas venenatis euismod condimentum.');
				$main->AddContent('footer','p','Copyright &copy; '. date('Y') . " - Tsama PHP &amp; &reg; or &trade; as indicated. - All rights reserved.");
				return;
			}
	}
}
?>