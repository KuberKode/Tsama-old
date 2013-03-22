<?php 

define('DS', DIRECTORY_SEPARATOR);

$_TSAMA_CONFIG = array(
	'BASEDIR' => dirname(__FILE__),
	'BASE' => 'http://'.$_SERVER['SERVER_NAME'].'/',
	'DOMAIN' => $_SERVER['SERVER_NAME'],
	'SUBDIR' => '',
	'ROUTE' => explode("/",substr($_SERVER['REQUEST_URI'],1,strlen($_SERVER['REQUEST_URI'])-1)),
	'NAME' => 'Tsama PHP',
	'VERSION' => '0.0.14',
	'OUTPUT' => 'HTML5',
	'THEME' => 'default',
	'LAYOUT' => 'default',
	'LOGO' => 'tsama.png',
	'COMPRESS' => FALSE,
	'HIDE_TSAMA' => FALSE,
	'LANGUAGE' => 'en',
	'DEBUG' => TRUE
);

$_DEBUG = array();

$nlast = count($_TSAMA_CONFIG['ROUTE'])-1;
$last = $_TSAMA_CONFIG['ROUTE'][$nlast];

$npos = strpos($last,'?');
if($npos !== FALSE){
	$_TSAMA_CONFIG['ROUTE'][$nlast] = substr($last,0,$npos);
}
if(empty($_TSAMA_CONFIG['ROUTE'][$nlast])){ 
	unset($_TSAMA_CONFIG['ROUTE'][$nlast]); 
	$_TSAMA_CONFIG['ROUTE'] = array_values($_TSAMA_CONFIG['ROUTE']);
}

$subdir = str_replace(realpath($_SERVER['DOCUMENT_ROOT']),'',$_TSAMA_CONFIG['BASEDIR']);

if(!empty($subdir)){
	$subdir = str_replace("\\","/",$subdir); //fix for url in windows
	if(substr($subdir,0,1) == '/'){
		$subdir = substr($subdir,1);
	}
	$_TSAMA_CONFIG['SUBDIR'] = '/' . $subdir . '/';
	$_TSAMA_CONFIG['BASE'] .= $subdir . '/'; 
	//adjust route accordingly
	$remArr =  explode("/",$subdir);
	$_TSAMA_CONFIG['ROUTE'] = array_values(array_diff_assoc($_TSAMA_CONFIG['ROUTE'],$remArr));
}

unset($nlast); unset($last); unset($npos); unset($subdir);

$_DB = array(
	'Active' => FALSE,
	'Connection' => null,
	'Host' => 'localhost',
	'Username' => '',
	'Password' => '',
	'Name' => ''
);

$db_file = $_TSAMA_CONFIG['BASEDIR'].DS.'conf'.DS.'db.inc.php';

if(file_exists($db_file)){
	include($db_file);
}

$site_file = $_TSAMA_CONFIG['BASEDIR'].DS.'conf'.DS.'site.inc.php';

if(file_exists($site_file)){
	include($site_file);
}

?>