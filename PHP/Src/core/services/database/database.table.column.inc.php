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

class TsamaDatabaseTableColumn extends TsamaObject{

	public $name = '';
	public $value = NULL;
	public $type = NULL;
	public $size = 0;

	public function __construct($name, $value = '', $type=NULL){

		parent::__construct();

		$this->name = $name;
	}

}

class TsamaDatabaseTableColumns extends TsamaCollection{

	private $m_columns;

	public function __construct($columns = null){

		parent::__construct();
	}
}
?>
