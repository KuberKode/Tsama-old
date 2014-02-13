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

require_once("database.table.column.types.inc.php");

define ('MYSQL_COLUMN_KEY_NONE',0);
define ('MYSQL_COLUMN_KEY_PRIMARY',10);
define ('MYSQL_COLUMN_KEY_FOREIGN',20);

class TsamaDatabaseTableColumn extends TsamaObject{

	public $name = '';
	public $alias = '';
	public $value = NULL;
	public $type = NULL;
	public $where = NULL;
	public $size = NULL;
	public $zeroFill = '';
	public $key = MYSQL_COLUMN_KEY_NONE;
	public $parent = NULL;

	public function __construct($name,$type=NULL, $size=NULL, $alias = '', $value = NULL, $zeroFill = FALSE){

		parent::__construct();

		$this->name = $name;
		$this->alias = $alias;
		$this->type = $type;
		$this->size = $size;
		$this->value = $value;
		$this->zeroFill = $zeroFill;
	}

	public function DescribeKey($table){
		$keyDesc = "";
		switch($this->key){
			case MYSQL_COLUMN_KEY_PRIMARY:{
				$keyDesc .=  sprintf("PRIMARY KEY (`%s`),\r\n",$this->name);
			}break;
			case MYSQL_COLUMN_KEY_FOREIGN:{
				$keyDesc .=  sprintf("KEY `idx_%s_%s` (`%s`),\r\n",$table,$this->name,$this->name);
			}break;
			default: break;
		}
		return $keyDesc;
	}

	public function DescribeType(){
		$typeDesc = "";
		switch($this->type){
			case MYSQL_COLUMN_TYPE_BIT:{
				$typeDesc .= "BIT";
				if($this->size != NULL){
					if($this->size > 64){ $this->size = 64;}
					$typeDesc .='('.$this->size.') ';
				}
				$typeDesc .= " NOT NULL";
				
			}break;
			case MYSQL_COLUMN_TYPE_TINYINT:{
				$typeDesc .= "TINYINT";
				if($this->size != NULL){
					if($this->size > 127){ $this->size = 127;}
					if($this->size < -128){ $this->size = -128;}
					$typeDesc .='('.$this->size.') ';
				}
				if($this->zeroFill){ $typeDesc .= " ZEROFILL"; }
				$typeDesc .= " NOT NULL";
			}break;
			case MYSQL_COLUMN_TYPE_TINYINT_UNSIGNED:{
				$typeDesc .= "TINYINT";
				if($this->size != NULL){
					if($this->size > 255){ $this->size = 255;}
					if($this->size < 0){ $this->size = 0;}
					$typeDesc .='('.$this->size.') ';
				}
				$typeDesc .= " UNSIGNED"; 
				if($this->zeroFill){ $typeDesc .= " ZEROFILL"; }
				$typeDesc .= " NOT NULL";
			}break;
			case MYSQL_COLUMN_TYPE_BOOL: case MYSQL_COLUMN_TYPE_BOOLEAN:{
				$typeDesc .= "BOOL NOT NULL";
			}break;
			case MYSQL_COLUMN_TYPE_SMALLINT:{
				$typeDesc .= "SMALLINT";
				if($this->size != NULL){
					if($this->size > 32767){ $this->size = 32767;}
					if($this->size < -32768){ $this->size = -32768;}
					$typeDesc .='('.$this->size.') ';
				}
				if($this->zeroFill){ $typeDesc .= " ZEROFILL"; }
				$typeDesc .= " NOT NULL";
			}break;
			case MYSQL_COLUMN_TYPE_SMALLINT_UNSIGNED:{
				$typeDesc .= "SMALLINT";
				if($this->size != NULL){
					if($this->size > 65535){ $this->size = 65535;}
					if($this->size < 0){ $this->size = 0;}
					$typeDesc .='('.$this->size.') ';
				}
				$typeDesc .= " UNSIGNED"; 
				if($this->zeroFill){ $typeDesc .= " ZEROFILL"; }
				$typeDesc .= " NOT NULL";
			}break;
			case MYSQL_COLUMN_TYPE_MEDIUMINT:{
				$typeDesc .= "MEDIUMINT ";
			}break;
			case MYSQL_COLUMN_TYPE_MEDIUMINT_UNSIGNED:{
				$typeDesc .= "MEDIUMINT";
				$typeDesc .= " UNSIGNED"; 
			}break;
			case MYSQL_COLUMN_TYPE_INT: case MYSQL_COLUMN_TYPE_INTEGER:{
				$typeDesc .= "INT";
			}break;
			case MYSQL_COLUMN_TYPE_INT_UNSIGNED: case MYSQL_COLUMN_TYPE_INTEGER_UNSIGNED:{
				$typeDesc .= "INT";
				$typeDesc .= " UNSIGNED"; 
			}break;
			case MYSQL_COLUMN_TYPE_BIGINT:{
				$typeDesc .= "BIGINT";
			}break;
			case MYSQL_COLUMN_TYPE_BIGINT_UNSIGNED:{
				$typeDesc .= "BIGINT";
				$typeDesc .= " UNSIGNED"; 
			}break;
			case MYSQL_COLUMN_TYPE_DECIMAL:  case MYSQL_COLUMN_TYPE_DEC: case MYSQL_COLUMN_TYPE_NUMERIC: case MYSQL_COLUMN_TYPE_FIXED:{
				$typeDesc .= "DECIMAL";
			}break;
			case MYSQL_COLUMN_TYPE_DECIMAL_UNSIGNED: case MYSQL_COLUMN_TYPE_DEC_UNSIGNED: case MYSQL_COLUMN_TYPE_NUMERIC_UNSIGNED: case MYSQL_COLUMN_TYPE_FIXED_UNSIGNED:{
				$typeDesc .= "DECIMAL";
				$typeDesc .= " UNSIGNED"; 
			}break;
			case MYSQL_COLUMN_TYPE_FLOAT:{
				$typeDesc .= "FLOAT";
			}break;
			case MYSQL_COLUMN_TYPE_FLOAT_UNSIGNED:{
				$typeDesc .= "FLOAT";
				$typeDesc .= " UNSIGNED";
			}break;
			case MYSQL_COLUMN_TYPE_DOUBLE:{
				$typeDesc .= "DOUBLE ";
			}break;
			case MYSQL_COLUMN_TYPE_DOUBLE_UNSIGNED:{
				$typeDesc .= "DOUBLE";
				$typeDesc .= " UNSIGNED";
			}break;
			case MYSQL_COLUMN_TYPE_DOUBLE_PRECISION: case MYSQL_COLUMN_TYPE_REAL:{
				$typeDesc .= "DOUBLE PRECISION";
			}break;
			case MYSQL_COLUMN_TYPE_FLOATING_POINT:{
				$typeDesc .= "FLOATING POINT ";
			}break;
			case MYSQL_COLUMN_TYPE_DATE:{
				$typeDesc .= "DATE ";
			}break;
			case MYSQL_COLUMN_TYPE_DATETIME:{
				$typeDesc .= "DATETIME ";
			}break;
			case MYSQL_COLUMN_TYPE_TIMESTAMP:{
				$typeDesc .= "TIMESTAMP ";
			}break;
			case MYSQL_COLUMN_TYPE_TIME:{
				$typeDesc .= "TIME ";
			}break;
			case MYSQL_COLUMN_TYPE_YEAR:{
				$typeDesc .= "YEAR ";
			}break;
			case MYSQL_COLUMN_TYPE_BINARY:{
				$typeDesc .= "BINARY ";
			}break;
			case MYSQL_COLUMN_TYPE_VARBINARY:{
				$typeDesc .= "VARBINARY ";
			}break;
			case MYSQL_COLUMN_TYPE_TINYBLOB:{
				$typeDesc .= "TINYBLOB ";
			}break;
			case MYSQL_COLUMN_TYPE_TINYTEXT:{
				$typeDesc .= "TINYTEXT ";
			}break;
			case MYSQL_COLUMN_TYPE_BLOB:{
				$typeDesc .= "BLOB ";
			}break;
			case MYSQL_COLUMN_TYPE_TEXT:{
				$typeDesc .= "TEXT ";
			}break;
			case MYSQL_COLUMN_TYPE_MEDIUMBLOB:{
				$typeDesc .= "MEDIUMBLOB ";
			}break;
			case MYSQL_COLUMN_TYPE_MEDIUMTEXT:{
				$typeDesc .= "MEDIUMTEXT ";
			}break;
			case MYSQL_COLUMN_TYPE_LONGBLOB:{
				$typeDesc .= "LONGBLOB ";
			}break;
			case MYSQL_COLUMN_TYPE_LONGTEXT:{
				$typeDesc .= "LONGTEXT ";
			}break;
			case MYSQL_COLUMN_TYPE_ENUM:{
				$typeDesc .= "ENUM ";
			}break;
			case MYSQL_COLUMN_TYPE_SET:{
				$typeDesc .= "SET ";
			}break;
			default: break;
		}
		//if primary key, auto increment
		if($this->key == MYSQL_COLUMN_KEY_PRIMARY){
			$typeDesc .= " AUTO_INCREMENT";
		}
		$typeDesc .= ",\r\n";
		return $typeDesc;
	}


}

class TsamaDatabaseTableColumns extends TsamaCollection{

	private $m_columns;

	public function __construct($columns = null){

		parent::__construct();
	}
}
?>
