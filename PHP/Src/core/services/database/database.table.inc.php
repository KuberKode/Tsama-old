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

require_once('database.table.column.inc.php');

class TsamaDatabaseTable extends TsamaObject{

	private $m_exist = FALSE;
	private $m_columns;
	private $m_joins;

	public $alias = '';
	public $name = '';

	public function __construct($name,$alias='',$columns = null){

		parent::__construct();

		$conn = TsamaDatabase::Connection();

		$this->name = $name;
		$this->alias = $alias;
		$this->m_columns = array();

		//see if table exist in db
		$q = sprintf("SHOW TABLES LIKE '%s';",$name);
		$sth = $conn->prepare($sql);
		$sth->execute();

		if($sth->rowCount() > 0){ 
			$this->m_exist = TRUE; 
			if($columns == null){
				//get all current column info
			}else{
				//get only certain column info
			}
		}else{
			//add columns
			$this->m_columns = $columns;
		}
		
	}
	public function Exist(){
		return $this->m_exist;
	}

	public function AddColumn($name,$type=NULL, $size='', $alias = '', $value = '', $zeroFill = FALSE){
		$this->m_columns[] = new TsamaDatabaseTableColumn($name, $type, $size, $alias, $value, $zeroFill);
	}

	public function GetColumns(){
		//Load Columns from table
		$conn = TsamaDatabase::Connection();

		//see if table exist in db
		$q = sprintf("SHOW COLUMNS FROM `%s`;",$this->name);
		$sth = $conn->prepare($sql);
		$sth->execute();
		if($sth->rowCount() > 0){
			while($column = $sth->fetch(PDO::FETCH_OBJ)){
				//get column info
				//Field | Type | Null | Key | Default | Extra
			}
		}
	}

	public function Create($drop = FALSE){
		if($this->Exist() && $drop){
			$conn = TsamaDatabase::Connection();

			$q = sprintf("DROP TABLE IF EXISTS `%s`;",$this->name);
			$sth = $conn->prepare($sql);
			$sth->execute();

			$this->m_exist = FALSE;
		}
		//create the table if it does not exist
		if(!$this->Exist()){
			//build ze sql
		}
		
		
	}

	public function Join($table,$on,$type){

	}

	public function Union($table){

	}

}

class TsamaDatabaseTables extends TsamaCollection{

	public function __construct(){

		parent::__construct();
	}
}
?>
