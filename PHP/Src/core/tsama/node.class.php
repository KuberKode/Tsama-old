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

require_once(dirname(__FILE__).DS."html5parser.class.php");

/*TsamaNode, a minimal node structure*/
class TsamaNode{

	private $m_children = null;
	private $m_parent = null;
	private $m_attibutes = null;
	private $m_name = '';
	private $m_value = '';
	private $m_selfClosing = FALSE;
	private $m_unique = TRUE;
	private $m_prefix = '';
	private $m_suffix = '';
	private $m_isComment = FALSE;

	public function __construct($name,$selfClosing = FALSE){

		$this->m_children = array();
		$this->m_attibutes = array();
		$this->m_name = $name;
		$this->m_selfClosing = $selfClosing;
		$this->m_unique = TRUE;
	}
	
	public function SetName($name){$this->m_name = $name;}
	public function GetName(){return $this->m_name;}
		
	public function SetPreFix($prefix){$this->m_prefix = $prefix;}
	public function GetPreFix(){return $this->m_prefix;}
	
	public function SetSuffix($suffix){$this->m_suffix = $suffix;}
	public function GetSuffix(){return $this->m_suffix;}
	
	public function SetParent(&$parent){ $this->m_parent = $parent; }
	public function &GetParent(){ return $this->m_parent; }

	public static function Clear(&$node){
		$node->ClearNodes();
	}
	public function ClearNodes(){
		$this->m_children = array();
		$this->m_attibutes = array();
	}
	
	public function &AddChild($name,$selfClosing = FALSE){
		
		$node = new TsamaNode($name,$selfClosing);
		$key = $this->AddChildObject($node);
		return $this->GetChild($key);
	}
	
	//Handle Child Objects
	public function AddChildObject(&$object,$createId = TRUE){
		if(is_object($object)){
			$key = count($this->m_children);
			$object->SetParent($this);
			$this->m_children[] = clone $object;
			/*if(!$this->m_children[$key]->HasAttribute('id') && $createId){
				$this->m_children[$key]->attr("id",$object->GetName());
			}*/
			$object = null;
			return $key;
		}
		return null;
	}
	
	//Handle Child as an input Array
	public function AddChildArray(&$childArr){
		//Example array
		/*$children = array(
			'h1' => array(
				'#tag' => 'h1',
				'id' => 'heading-1',
				'class' => 'class-1 class-2',
				'#text' => 'Heading 1'
			),
			'p' => array(
				'#tag' => 'p',
				'#text' => 'Woof'
			)
		);*/
		if(is_array($childArr) && count($childArr) > 0){
			foreach($childArr as $child){
				$node = new TsamaNode($child['#tag']);
				switch($child['type']){
					default:{
						foreach($child as $cName => $value){
							switch($cName){
								case '#tag':break;
								case '#text':{$node->SetValue($value);}break;
								default:{$node->attr($cName,$value);}break;
							}
						}
						$key = $this->AddChildObject($label);
						$key = $this->AddChildObject($node);
					}break;
				}
				
			}
			return TRUE;
		}
		return FALSE;
	}
	
	//Handle Batch of Child Objects
	public function AddChildObjects(&$objects){
		$newObs = array();
		foreach($objects as $object){
				$object->SetParent($this);
				$newObs[] = clone $object;
				$object = null;
			}
		$this->m_children = array_merge_recursive($this->m_children, $newObs);
		return TRUE;
	}
	
	public function &GetChild($key){
		if(array_key_exists($key,$this->m_children)) {	return $this->m_children[$key]; }
		return NULL; 
	}
	public function &GetChildren($name = ''){ 
		//get all children
		if(empty($name)){ return $this->m_children; }

		//or get children according to nodename
		if($this->HasChildren()){
			$children = $this->GetChildren();
			$childrenArray = array();
			for($key=0;$key < count($children); $key++){
				$child = $children[$key];
				if($child->GetName() == $name){$childrenArray[$key] = $this->GetChild($key);} //Retain key in childrenArray
			}
			return $childrenArray;
		}
		return NULL;
 
	}
	public function HasChildren(){ 
		if(count($this->m_children)>0){return TRUE;} 
		return FALSE; 
	}
	
	public function SetUnique($unique = TRUE){ $this->m_unique = $unique; }
	public function GetUnique(){ return $this->m_unique; }
	public function IsUnique(){ return $this->m_unique; }
	
	public function HasAttributes(){
		if(count($this->m_attibutes)>0)return TRUE;
		return FALSE;
	}
	public function HasAttribute($attribute){ return(array_key_exists($attribute, $this->m_attibutes)); }
	public function SetAttribute($name,$value){	$this->m_attibutes[$name] = $value; ksort($this->m_attibutes);	}

	/*Popular attribute shortcuts*/
	public function id($value=''){
		if(!empty($value)){ $this->attr('id',$value); }
		return $this->GetAttribute('id');
	}
	public function className($value=''){ //class is a php keyword so use DOM className instead
		if(!empty($value)){ $this->attr('class',$value); }
		return $this->GetAttribute('class');
	}
	public function value($value=''){
		if(!empty($value)){ $this->attr('value',$value); }
		return $this->GetAttribute('value');
	}
	public function title($value=''){
		if(!empty($value)){ $this->attr('title',$value); }
		return $this->GetAttribute('title');
	}
	public function src($value=''){
		if(!empty($value)){ $this->attr('src',$value); }
		return $this->GetAttribute('src');
	}
	public function href($value=''){
		if(!empty($value)){ $this->attr('href',$value); }
		return $this->GetAttribute('href');
	}
	public function name($value=''){
		if(!empty($value)){ $this->SetName($value); }
		return $this->GetName();
	}
	public function text($value=''){
		if(!empty($value)){ $this->SetValue($value); }
		return $this->GetValue();
	}
	public function html($value=''){
		if(!empty($value)){ $this->AddChildObject(HTML5Parser::CreateNodes($value));  } //parse HTML and create nodes
		return HTML5Parser::_out(this,null);
	}

	/*~End popular*/

	public function attr($name,$value){
		$this->SetAttribute($name,$value);
	}
	public function attribs($attrArray){
		if(is_array($attrArray) && count($attrArray)>0){
			foreach($attrArray as $name => $value){
				$this->attr($name,$value);
			}
		}
	}

	public function &GetAttribute($name){ return $this->m_attibutes[$name];	}
	public function &GetAttributes(){
		if($this->HasAttributes()){
			return $this->m_attibutes;
		}
		$n = NULL;
		return $n;
	}
	
	public function SetValue($value){$this->m_value = $value;}
	public function GetValue(){return $this->m_value;}
	
	public function IsSelfClosing(){
		return $this->m_selfClosing;
	}

	public function IsComment($comment = null){
		if($comment != null){
			$this->m_isComment = $comment;
		}
		return $this->m_isComment;
	}
	
	public function AddChildren($nodes){
		if($nodes){ 
			$this->AddChildObjects($nodes); return TRUE;
		}
		return FALSE;
	}
	public function HasChild($name){
		if($this->HasChildren()){
			$children = $this->GetChildren();
			foreach($children as $child){
				if($child->GetName() == $name){return TRUE;}
			}
		}
		return FALSE;
	}
	
	public function &GetFirstChild($name = ""){
		$nothing = NULL;
		if($this->HasChildren()){
			$children = $this->GetChildren();
			if($name == ""){return $children[0];}
			for($key=0;$key < count($children); $key++){
				$child = $children[$key];
				if($child->GetName() == $name){return $this->GetChild($key);}
				$childOfChild = $child->GetFirstChild($name);
				if($childOfChild){ return $childOfChild; }
			}
		}
		return $nothing;
	}
	
	public function &GetSubNode($name = ""){
		if($this->HasChildren()){
			$children = $this->GetChildren();
			if($name == ""){return $children[0];}
			for($key=0;$key < count($children); $key++){
				$child = $children[$key];
				if($child->GetName() == $name){return $this->GetChild($key);}
				//search sub nodes
				$subNode = $child->GetSubNode($name);
				if($subNode){return $subNode;}
			}
		}
		return NULL;
	}
	
	public function &RemoveFirstChild($name = ""){
		if($this->HasChildren()){
			if($name == ""){return;}
			for($key=0;$key < count($this->m_children); $key++){
				$child = $this->m_children[$key];
				if($child->GetName() == $name){
					unset($this->m_children[$key]);
					$this->m_children = array_values($this->m_children);
					break;
				}
			}
		}
	}
	
	public function &RemoveChild($key){
		if($this->HasChildren()){
			if(array_key_exists($key,$this->m_children)) {	
				unset($this->m_children[$key]);
				$this->m_children = array_values($this->m_children);
			}
		}
	}
	
	public function &GetFirstChildByAttribute($attribute, $value){
		$nothing = NULL;
		if($this->HasChildren()){
			$children = $this->GetChildren();
			for($key=0;$key < count($children); $key++){
				$child = $children[$key];
				if($child->HasAttribute($attribute)){
					$att = $child->GetAttribute($attribute);
					if($att == $value){return $this->GetChild($key);}
				}
				$childOfChild = $child->GetFirstChildByAttribute($attribute, $value);
				if($childOfChild){ return $childOfChild; }
			}
		}
		return $nothing;
	}
	
	public function AddComment($comment){
		$commentNode = $this->AddChild("comment");
		$commentNode->IsComment(TRUE);
		$commentNode->SetValue($comment);
	}
	
}
 
 
 ?>