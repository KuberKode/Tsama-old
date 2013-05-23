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

class ServiceThing extends TsamaObject{
	private $m_node = NULL;
	public $description = '';
	public $image = '';
	public $name = '';
	public $url = '';
	public function __construct(&$parentNode){
		parent::__construct();
		$this->m_node = $parentNode;
	}

	public function Node(){
		return $this->m_node;
	}

	public function set(){
		if(!isset($_POST)){
			return FALSE;
		}
		$this->get();
		return TRUE;
	}
	public function get($params){
		if(is_object($this->Node())){
			switch($params['view']){
				case 'header':{
						$new = $this->Node()->AddChild('h1');
						$new->SetValue('Hello World!');
				}break;
				case 'footer':{
					$new = $this->Node()->AddChild('p');
					$new->SetValue('Copyright &copy; '. date('Y') . ' - '.Tsama::_conf('NAME').' &amp; &reg; or &trade; as indicated. - All rights reserved.');
				}break;
				case 'blog':
				default: {
					$new = $this->Node()->AddChild('p');
					$new->SetValue('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vulputate hendrerit est. Cras velit diam, gravida sit amet sagittis eget, eleifend vel felis. Praesent in velit non odio tempus lobortis sed sed ligula. Donec pellentesque accumsan ligula et venenatis. Maecenas et sem nunc, ac commodo nisl. Vivamus quis urna enim. Phasellus lacus velit, accumsan id interdum eget, pellentesque eu quam. Integer venenatis libero sit amet tortor interdum accumsan. Proin at quam non mauris ullamcorper dapibus. Pellentesque semper, ipsum ut cursus porta, dui leo consequat libero, eu placerat eros urna eu ipsum. Nullam elit metus, mollis ut posuere at, blandit sit amet lacus. Donec varius fermentum gravida. Maecenas venenatis euismod condimentum.');
				}break;
			}
			return;
		}
		Tsama::Debug('Invalid node specified.');
		Tsama::Debug(print_r($params,TRUE));
	}
}
?>