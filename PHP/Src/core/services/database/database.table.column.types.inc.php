<?php 
/*
** 08 July 2013
**
** The author disclaims copyright to this source code. 
**
*************************************************************************/

if(!defined('TSAMA'))exit;

/********************************************************************/
/* MYSQL                                                            *
/* http://dev.mysql.com/doc/refman/5.7/en/data-type-overview.html   *
/********************************************************************/

// Numeric Types
//--------------
// BIT[(M)]
define('MYSQL_COLUMN_TYPE_BIT',0); //1 - 64
// TINYINT[(M)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_TINYINT',1); // -128 to 127
define('MYSQL_COLUMN_TYPE_TINYINT_UNSIGNED',2); // 0 to 255
// BOOL,BOOLEAN = TINYINT(1)
define('MYSQL_COLUMN_TYPE_BOOL',3); //0/1, TRUE/FALSE
define('MYSQL_COLUMN_TYPE_BOOLEAN',3);//0/1, TRUE/FALSE
// SMALLINT[(M)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_SMALLINT',4); //-32768 - 32767
define('MYSQL_COLUMN_TYPE_SMALLINT_UNSIGNED',5); //0 to 65535
// MEDIUMINT[(M)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_MEDIUMINT',6); // -8388608 to 8388607
define('MYSQL_COLUMN_TYPE_MEDIUMINT_UNSIGNED',7);// 0 to 16777215
// INT[(M)] [UNSIGNED] [ZEROFILL],INTEGER[(M)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_INT',8); // -2147483648 to 2147483647
define('MYSQL_COLUMN_TYPE_INTEGER',8);
define('MYSQL_COLUMN_TYPE_INT_UNSIGNED',9); // 0 to 4294967295
define('MYSQL_COLUMN_TYPE_INTEGER_UNSIGNED',9);
// BIGINT[(M)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_BIGINT',10); // -9223372036854775808 to 9223372036854775807
define('MYSQL_COLUMN_TYPE_BIGINT_UNSIGNED',11); // 0 to 18446744073709551615
// DECIMAL[(M[,D])] [UNSIGNED] [ZEROFILL],DEC[(M[,D])] [UNSIGNED] [ZEROFILL], NUMERIC[(M[,D])] [UNSIGNED] [ZEROFILL], FIXED[(M[,D])] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_DECIMAL',12); // Default: [[10].0] - Max is [[64].30]
define('MYSQL_COLUMN_TYPE_DEC',12);
define('MYSQL_COLUMN_TYPE_NUMERIC',12);
define('MYSQL_COLUMN_TYPE_FIXED',12);
define('MYSQL_COLUMN_TYPE_DECIMAL_UNSIGNED',13); // No negative values
define('MYSQL_COLUMN_TYPE_DEC_UNSIGNED',13);
define('MYSQL_COLUMN_TYPE_NUMERIC_UNSIGNED',13);
define('MYSQL_COLUMN_TYPE_FIXED_UNSIGNED',13);
// FLOAT[(M,D)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_FLOAT',14); // -3.402823466E+38 to -1.175494351E-38, 0  and 1.175494351E-38 to 3.402823466E+38
define('MYSQL_COLUMN_TYPE_FLOAT_UNSIGNED',15); //no negative values
//DOUBLE[(M,D)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_DOUBLE',16); //-1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308
define('MYSQL_COLUMN_TYPE_DOUBLE_UNSIGNED',17); // No negative values
//DOUBLE PRECISION[(M,D)] [UNSIGNED] [ZEROFILL], REAL[(M,D)] [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_DOUBLE_PRECISION',18); //See MySql url
define('MYSQL_COLUMN_TYPE_REAL',18); //See MySql url
//FLOAT(p) [UNSIGNED] [ZEROFILL]
define('MYSQL_COLUMN_TYPE_FLOATING_POINT',19); //See MySql url

// Date and Time Types
//--------------------
//DATE
define('MYSQL_COLUMN_TYPE_DATE',20); // 'YYYY-MM-DD' - '1000-01-01' to '9999-12-31'
//DATETIME[(fsp)]
define('MYSQL_COLUMN_TYPE_DATETIME',21); // 'YYYY-MM-DD HH:MM:SS' - '1000-01-01 00:00:00' to '9999-12-31 23:59:59', fsp 0 to 6
//TIMESTAMP[(fsp)]
define('MYSQL_COLUMN_TYPE_TIMESTAMP',22); // 'YYYY-MM-DD HH:MM:SS' - '1970-01-01 00:00:01' UTC to '2038-01-19 03:14:07' UTC, fsp 0 to 6
//TIME[(fsp)]
define('MYSQL_COLUMN_TYPE_TIME',23); // 'HH:MM:SS' - '-838:59:59' to '838:59:59', fsp 0 to 6
//YEAR[(2|4)]
define('MYSQL_COLUMN_TYPE_YEAR',24); // YEAR(2) = YY = 70 to 69 (i.e. 1970 to 2069), YEAR(4) =  YYYY =  1901 to 2155, and 0000

// String Types
//--------------------
//BINARY(M)
define('MYSQL_COLUMN_TYPE_BINARY',30);
//VARBINARY(M)
define('MYSQL_COLUMN_TYPE_VARBINARY',31);
//TINYBLOB
define('MYSQL_COLUMN_TYPE_TINYBLOB',33); //255 (2^8 – 1) bytes
//TINYTEXT [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_TINYTEXT',34); //255 (2^8 – 1) bytes
//BLOB[(M)]
define('MYSQL_COLUMN_TYPE_BLOB',35); //65,535 (2^16 – 1) bytes
//[NATIONAL] CHAR[(M)] [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_CHAR',36);  //0 to 255 characters. If M is omitted, the length is 1.
//[NATIONAL] VARCHAR(M) [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_VARCHAR',37); //0 to 65,535 characters, 0 to 21,844 for utf8
//TEXT[(M)] [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_TEXT',38);  //65,535 (2^16 – 1) characters
//MEDIUMBLOB
define('MYSQL_COLUMN_TYPE_MEDIUMBLOB',39);//16,777,215 (2^24 – 1) bytes
//MEDIUMTEXT [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_MEDIUMTEXT',40);//16,777,215 (2^24 – 1) characters
//LONGBLOB
define('MYSQL_COLUMN_TYPE_LONGBLOB',41);//4,294,967,295 or 4GB (232 – 1) bytes
//LONGTEXT [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_LONGTEXT',42);//4,294,967,295 or 4GB (232 – 1) characters
//ENUM('value1','value2',...) [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_ENUM',43);//maximum of 65,535 distinct elements , The practical limit is less than 3000
//SET('value1','value2',...) [CHARACTER SET charset_name] [COLLATE collation_name]
define('MYSQL_COLUMN_TYPE_SET',44);//maximum of 64 distinct members
?>
