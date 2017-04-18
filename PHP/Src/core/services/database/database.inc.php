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
		Tsama::Debug('In TsamaDatabase::InstallCore()');
		if(TsamaDatabase::IsConfigured() && TsamaDatabase::IsActive()){
			//if connnected
			$conn = TsamaDatabase::Connection();
			//install core tables
			//Services
			$services = new TsamaDatabaseTable('t_services');
			if(!$services->Exist()){
				Tsama::Debug('Table t_services do not exist. Creating.');
				//set columns
				$serviceId = $services->AddColumn('id',MYSQL_COLUMN_TYPE_INT);
				$serviceId->key = MYSQL_COLUMN_KEY_PRIMARY;
				$services->AddColumn('type',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$services->AddColumn('name',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$services->AddColumn('status',MYSQL_COLUMN_TYPE_TINYINT);

				//install
				$services->Create();
			}
			//People
			$person = new TsamaDatabaseTable('t_person');
			if(!$person->Exist()){
				Tsama::Debug('Table t_person do not exist. Creating.');
				//set columns
				$personId = $person->AddColumn('id',MYSQL_COLUMN_TYPE_INT);
				$personId->key = MYSQL_COLUMN_KEY_PRIMARY;
				$person->AddColumn('firstnames',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$person->AddColumn('surname',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$person->AddColumn('name',MYSQL_COLUMN_TYPE_TEXT);
				$person->AddColumn('alias',MYSQL_COLUMN_TYPE_TEXT);
				$person->AddColumn('title',MYSQL_COLUMN_TYPE_VARCHAR,48);
				$person->AddColumn('job_title',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$person->AddColumn('bio',MYSQL_COLUMN_TYPE_TEXT);
				$person->AddColumn('img_url',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$person->AddColumn('birthdate',MYSQL_COLUMN_TYPE_DATE);
				$person->AddColumn('email',MYSQL_COLUMN_TYPE_VARCHAR,255); 
				$person->AddColumn('username',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$person->AddColumn('password',MYSQL_COLUMN_TYPE_TEXT);
				$person->AddColumn('created',MYSQL_COLUMN_TYPE_DATETIME);
				$personCB = $person->AddColumn('created_by',MYSQL_COLUMN_TYPE_INT);
				$personCB->key = MYSQL_COLUMN_KEY_FOREIGN;
				$person->AddColumn('modified',MYSQL_COLUMN_TYPE_DATETIME);
				$personMB = $person->AddColumn('modified_by',MYSQL_COLUMN_TYPE_INT);
				$personMB->key = MYSQL_COLUMN_KEY_FOREIGN;
				$person->AddColumn('last_login',MYSQL_COLUMN_TYPE_DATETIME);

				//install
				$person->Create();
			}
			//Roles
			$role = new TsamaDatabaseTable('t_role');
			if(!$role->Exist()){
				Tsama::Debug('Table t_role do not exist. Creating.');
				//set columns
				$roleId = $role->AddColumn('id',MYSQL_COLUMN_TYPE_INT);
				$roleId->key = MYSQL_COLUMN_KEY_PRIMARY;
				$role->AddColumn('title',MYSQL_COLUMN_TYPE_VARCHAR,255);
				$role->AddColumn('description',MYSQL_COLUMN_TYPE_TEXT);
				//install
				$role->Create();
				//TODO: data 
				//INSERT INTO `t_role` VALUES (1,'Owner','Owner Role'),(2,'Administrator','Administrator Role'),(3,'Member','Member Role');
			}
			//Person Role
			$personrole = new TsamaDatabaseTable('t_person_role');
			if(!$personrole->Exist()){
				Tsama::Debug('Table t_person_role do not exist. Creating.');
				//set columns
				$prId = $personrole->AddColumn('id',MYSQL_COLUMN_TYPE_INT);
				$prId->key = MYSQL_COLUMN_KEY_PRIMARY;
				$personFk = $personrole->AddColumn('fk_person',MYSQL_COLUMN_TYPE_INT);
				$personFk->key = MYSQL_COLUMN_KEY_FOREIGN;
				$roleFk = $personrole->AddColumn('fk_role',MYSQL_COLUMN_TYPE_INT);
				$roleFk->key = MYSQL_COLUMN_KEY_FOREIGN;
				//install
				$personrole->Create();
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
	
	public function LoadConfig(){ //Manually Load the db configuration
		
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
			
			return $_DB['Active'];
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