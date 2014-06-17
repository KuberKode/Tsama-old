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

require_once('database.table.inc.php');

class TsamaDatabase extends TsamaObject{

	public function __construct(){
		parent::__construct();
	}

	public function OnConnect(){
		$this->NotifyObservers('OnConnect',$this);
	}

	public static function IsConfigured(){
		$db_file = Tsama::_conf('BASEDIR').DS.'conf'.DS.'db.conf.php';

		if(file_exists($db_file)){
			return TRUE;
		}
		return FALSE;
	}

	public static function InstallCore(){
		if(TsamaDatabase::IsConfigured() && TsamaDatabase::IsActive()){
			//if connnected
			$conn = TsamaDatabase::Connection();
			//install core tables
			$services = new TsamaDatabaseTable('t_services');

			if(!$services->Exist()){
				//set columns
				$services->AddColumn('id',MYSQL_COLUMN_TYPE_INT);
				$services->AddColumn('type',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$services->AddColumn('name',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$services->AddColumn('status',MYSQL_COLUMN_TYPE_TINYINT);

				//install
				$services->Create();
			}
		}
	}

	public static function IsActive(){
		global $_DB;

		return $_DB['Active'];
	}

	public static function Connection(){
		global $_DB;

		return $_DB['Connection'];
	}
	public static function Driver(){
		global $_DB;

		return $_DB['Driver'];
	}
	public static function Host(){
		global $_DB;

		return $_DB['Host'];
	}
	public static function Username(){
		global $_DB;

		return $_DB['Username'];
	}
	public static function Name(){
		global $_DB;

		return $_DB['Name'];
	}

	public function Join($tables,$table,$where,$type){

	}

	public function Union($tables,$table,$where,$joinType){

	}

	public static function Select($query = null){
		global $_DB;
		if(is_string($query)){
			return $this->Query($query);
		}
		/*tables structure
			tables[
				'table-name' => [
					'table-alias' => '',
					'select-columns' => [
						'column-name' => 'column-alias',
						...
					],
					'table-where' => [
						'column-name' => 'column-value',
						...
					],
					'table-order-by' [
						'column-name' => 'column-order',
						...
					],
					'table-group-by' [
						'column-name' => 'column-group',
						...
					]

				],
				...
			]
		*/

	}

	public static function Insert($query = null){
		global $_DB;
		if(is_string($query)){			
			return $this->Query($query);
		}

	}

	public static function Update($query = null){
		global $_DB;
		if(is_string($query)){
			return $this->Query($query);
		}

	}

	public static function Delete($query = null){
		global $_DB;
		if(is_string($query)){
			return $this->Query($query);
		}

	}

	public static function Execute($sql,$params = null){
		global $_DB;

		$sth = null;

		$conn = $_DB['Connection'];
		if($_DB['Active'] && is_object($conn)){
			$sth = $conn->prepare($sql);
			if($params){
				$sth->execute($params);
			}else{
				$sth->execute();
			}
			return $sth;
		}
		$conn->NotifyObservers('OnQuery',$conn,$sql);
		if(is_null($sth)){return FALSE;}
		return TRUE;		
	}

	public static function GetTables(){
		//Show tables from active db
	}
	public function Query($sql,$params = null){
		global $_DB;

		$sth = null;

		$conn = $_DB['Connection'];
		if($_DB['Active'] && is_object($conn)){
			$sth = $conn->prepare($sql);
			if($params){
				$sth->execute($params);
			}else{
				$sth->execute();
			}
			return $sth;
		}
		$this->NotifyObservers('OnQuery',$this,$sql);
		if(is_null($sth)){return FALSE;}
		return TRUE;
	}

	public function Connect($main,$attempts = 0){
			global $_DB;

			try{

 				$_DB['Connection'] = new PDO(strtolower($_DB['Driver']).':host='.$_DB['Host'].';dbname='.$_DB['Name'], $_DB['Username'],$_DB['Password']);
 				$_DB['Connection']->setAttribute( PDO::ATTR_PERSISTENT, true );

				$_DB['Active'] = TRUE;

				Tsama::Debug("Database [".$_DB['Name']."@".$_DB['Host']."] connected.");
			}catch(PDOException $e) {

				//For error:  MySQL server has gone away 
					//Sometimes after long period of inactivity the conn will go away, so try again
				if($attempts==0){
					$this->Connect($main,1);
					return;
				}

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