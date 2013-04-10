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

class TsamaDatabase extends TsamaObject{

	public function __construct(){

		parent::__construct();
	}

	public function OnConnect(){
		$this->NotifyObservers('OnConnect',$this);
	}

	public function Connect($main){
			global $_DB;

			try{
 				$_DB['Connection'] = new PDO('mysql:host='.$_DB['Host'].';dbname='.$_DB['Name'], $_DB['Username'],$_DB['Password'],array( PDO::ATTR_PERSISTENT => true ));
				$_DB['Active'] = TRUE;

				Tsama::Debug("Database [".$_DB['Name']."@".$_DB['Host']."] connected.");
			}catch(PDOException $e) {
			    Tsama::Debug("Database Error!: " . $e->getMessage());
			    $_DB['Active'] = FALSE;
			    $_DB['Connection'] = null;
			    Tsama::Debug("Database  [".$_DB['Name']."@".$_DB['Host']."] NOT connected.");
			}
			
			$this->OnConnect();

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