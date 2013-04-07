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

require_once(dirname(__FILE__).DS."node.class.php");

class HTML5Parser{

	protected static $ids = NULL; //Make sure ids are unique as per HTML standard
	protected static $processing_head = false;

	public function __construct(){
		//parent::__construct();
	}
	
	static public function _unique(&$node){
	
		//remember tag IDs
		if(!HTML5Parser::$ids){HTML5Parser::$ids = array();}
	
		if(is_object($node)){
			//Check for id attribute
			if($node->IsUnique()){
				if(!$node->HasAttribute("id")){
					$node->SetAttribute("id", $node->getName());
				}
				//Enforce no spaces in id as per HTML specification
				$node->SetAttribute("id",str_replace(" ","",$node->GetAttribute("id")));
				
				//ensure id is unique
				if(array_key_exists($node->GetAttribute("id"),HTML5Parser::$ids)){
					HTML5Parser::$ids[$node->GetAttribute("id")]++;
					$node->SetAttribute("id",$node->GetAttribute("id") . '-'.HTML5Parser::$ids[$node->GetAttribute("id")]);
				}else{
					HTML5Parser::$ids[$node->GetAttribute("id")]=1;
				}
			}
			
			//Ensures the node's attributes are unique
			$attribs = $node->GetAttributes();
			if($attribs){$attribs = array_unique($attribs);}
		}

	}
	
	static public function CreateNodes($string = ''){

		$node = null;
		if(empty($string)){
			$node = new TsamaNode('html');
			$node->SetUnique(FALSE);
			$head = $node->AddChild('head');
			$body = $node->AddChild('body');
		}
		
		return $node;
	}

	public static function CreateForm(&$parentNode,$action){
		
		$form = $parentNode->AddChild("form");
		$form->attribs(array( 'id' => 'f', 'name' => 'f','class' => 'form', 'method' => 'post', 'action' => $action, 'enctype' => "multipart/form-data" ));
		
		return $form;
	}

	public static function CreateButton(&$parentNode,$label,$isSubmit=FALSE){
	
		$button = $parentNode->AddChild("input");
		
		$button->attr("type","button");
		
		if($isSubmit){
			$button->attr("type","submit");
		}
		
		$button->attr("class","button");
		$button->attr("value",$label);
		
		return $button;
		
	}

	public static function CreateHiddenField(&$parentNode,$name,$id,$value=''){

		$options = array(
			"name" => $name,
			"required" => FALSE,
			"data+" => FALSE, //used for email, file inputs as well as radio and checkboxes fields
			"label" => null,
			"data" => array(
				array('#tag'=>'input','type'=>'hidden','name'=>$name,'id'=>$id,'value'=>$value)
			)
		);

		$parentNode->AddChildArray($options['data']);
		
	}

	public static function CreateField(&$parentNode,$options = null){
	
		/*$options = array(
			"name" => 'myfield',
			"required" => TRUE,
			"data+" => FALSE, //used for radio and checkboxes fields
			"label" => array(
				"#text" => 'Hello:',
				"class" => 'goLeft',
			),
			"data" => array(
				array('#tag'=>'input','type'=>'text','id'=>'myfield','name'=>'myfield','value'=>'')
			)
		);*/

		$field = $parentNode->AddChild("div");
		$field->attr("class","field ".$options["name"]);
		$field->attr("id","fld".$options["name"]);

		$label = null;
		
		if(is_array($options)){

			//Label Options
			if(isset($options['label']) && is_array($options['label'])){

				$label = $field->AddChild("label");

				$label->attr("for",$options['name']);

				$label->attr("class","label");
				if(isset($options['label']['class'])){ $label->attr("class",$options['label']['class']." label"); }

				$txt = $label->AddChild("span");
				$txt->SetValue($options['label']["#text"]);

				if(isset($options['required']) && $options['required']){

					$lblReq = $label->AddChild("span");
					$lblReq->attr("class","required");
					$lblReq->SetValue("*");

					foreach($options['data'] as &$data){
						$data["required"]='required';
						$data["aria-required"]='true';
					}
				}
			}

			//Data Options
			if(isset($options['data']) && is_array($options['data'])){
				//test for radio or checkbox in which case the data is part of label
				if($options['data+'] && $label){
					$label->AddChildArray($options['data']);
				}else{
					$field->AddChildArray($options['data']);
				}
			}
			
		}
		
		return $field;
	}

	public static function CreateSelectField(&$parentNode,$label,$name,$options,$required=FALSE){
		$field = $parentNode->AddChild("div");
		$field->attr("class","field $name");
		$field->attr("id","fld".$name);

		$fldLabel = $field->AddChild('label');
		$fldLabel->SetValue($label);
		$fldLabel->attr('for',$name);

		$select = $field->AddChild('select');
		$select->attr('id','sel'.$name);
		$select->attr('name',$name);

		foreach($options as $val => $txt){
			$opt= $select->AddChild('option');
			$opt->attr('value',$val);
			$opt->SetValue($txt);
		}

		if($required){
			$select->attr('required','required');
			$select->attr('aria-required','true');
		}

		return $field;
	}

	public static function CreateTextField(&$parentNode,$label,$name,$placeholder='',$value='',$required=FALSE){

		$options = array(
			"name" => $name,
			"required" => $required,
			"data+" => FALSE, //used for radio and checkboxes fields
			"label" => array(
				"#text" => $label,
				"class" => 'label default'
			),
			"data" => array(
				array('#tag'=>'input','type'=>'text','id'=>$name,'name'=>$name,'placeholder'=>$placeholder,'value'=>$value)
			)
		);

		return HTML5Parser::CreateField($parentNode, $options);
	}

	public static function CreateFileUploadField(&$parentNode,$label,$name,$placeholder='',$value='',$required=FALSE){

		$options = array(
			"name" => $name,
			"required" => $required,
			"data+" => FALSE, //used for radio and checkboxes fields
			"label" => array(
				"#text" => $label,
				"class" => 'label default'
			),
			"data" => array(
				array('#tag'=>'input','type'=>'file','id'=>$name,'name'=>$name,'placeholder'=>$placeholder,'value'=>$value)
			)
		);

		return HTML5Parser::CreateField($parentNode, $options);
	}

	public static function CreateEmailField(&$parentNode,$label,$name,$placeholder='',$value='',$required=FALSE,$multiple = FALSE){

		$options = array(
			"name" => $name,
			"required" => $required,
			"data+" => FALSE, //used for radio and checkboxes fields
			"label" => array(
				"#text" => $label,
				"class" => 'label default'
			),
			"data" => array()
		);

		$data = array('#tag'=>'input','type'=>'text','id'=>$name,'name'=>$name,'placeholder'=>$placeholder,'value'=>$value);
		if($multiple){
			$data['multiple'] = "multiple";
		}

		$options["data"][] = $data;

		return HTML5Parser::CreateField($parentNode, $options);
	}

	public static function CreatePasswordField(&$parentNode,$label,$name,$placeholder='',$required=FALSE,$createDummy = FALSE,$dummyName="password"){
		
		if($createDummy){
			$dummy = HTML5Parser::CreateHiddenField($parentNode,$dummyName,$dummyName,$dummyName);
		}

		$options = array(
			"name" => $name,
			"required" => $required,
			"data+" => FALSE, //used for radio and checkboxes fields
			"label" => array(
				"#text" => $label,
				"class" => 'label default'
			),
			"data" => array(
				array('#tag'=>'input','type'=>'password','id'=>$name,'name'=>$name,'placeholder'=>$placeholder,'value'=>$value)
			)
		);

		return HTML5Parser::CreateField($parentNode, $options);
		
	}

	public static function CreateTokenField(&$parentNode,$name,$id,$value){
	
		HTML5Parser::CreateHiddenField($parentNode,$name,$id,$value);
		
	}
	
	static public function SetLanguage(&$htmlNode,$language){
		$head = &$htmlNode->GetFirstChild('head');
	
		$htmlNode->attr('lang',$language);
		//language head node
		$langNode = $head->GetFirstChildByAttribute("http-equiv","content-language");
		if($langNode){
			$langNode->attr('content',$language);
			return;
		}
		$lang = $head->AddChild('meta');
		$lang->attr('content',$language);
		$lang->attr('http-equiv','content-language');
	}

	static public function SetBase(&$htmlNode,$href){
		$head = &$htmlNode->GetFirstChild('head');

		$base = $head->GetFirstChild('base');
		if(!$base){
			$base = $head->AddChild("base");
		}
		$base->attr('href',$href);
	}

	static public function SetFavIcon(&$htmlNode,$ico){
		$head = &$htmlNode->GetFirstChild('head');

		$base = $head->GetFirstChild('base');
		if(!$base){
			$base = $head->AddChild("base");
			$base->attr('href','');
		}

		$favicon = $base->GetAttribute('href').$ico;

		$icon = $head->AddChild('link');
		$icon->attr('rel','icon');
		$icon->attr('type','image/vnd.microsoft.icon');
		$icon->attr('href',$favicon);
				
		$icon_ie = $head->addComment('[if IE]><link rel="SHORTCUT ICON" type="image/x-icon" href="'.$favicon.'"/><![endif]');
	}
	
	static public function SetTitle(&$htmlNode,$title,$siteName=''){
		
		$head = &$htmlNode->GetFirstChild('head');
		
		$titles =  $head->GetChildren('title');
		if(!$titles || count($titles) == 0){
			$titleNode = $head->AddChild("title");
			$titleNode->SetValue($title . ' - ' . $siteName);
		}
		//else get title and set
		//remove any additional titles found.
		$count = 0;
		foreach($titles as $titleKey => $titleNode){
			if($count == 0){
				$titleNode->SetValue($title. ' - ' . $siteName);
			}
			if($count > 0){
				$this->m_head->RemoveChild($titleKey);
			}
			$count++;
		}
	}

	static public function AddScript(&$htmlNode,$script){
		
		$head = &$htmlNode->GetFirstChild('head');

		$scriptNode = $head->AddChild('script');
		$scriptNode->attr("type","text/javascript");

		$scriptNode->SetValue($script);
	}
	static public function AddScriptFile(&$htmlNode,$scriptFile){
		
		$head = &$htmlNode->GetFirstChild('head');

		$scriptNode = $head->AddChild('script');
		$scriptNode->attr("type","text/javascript");
		$scriptNode->attr("src",$scriptFile);
	}

	static public function AddCSS(&$htmlNode,$css){
		
		$head = &$htmlNode->GetFirstChild('head');

		$cssNode = $head->AddChild('style');
		$cssNode->attr("type","text/css");

		$cssNode->SetValue($css);
	}
	static public function AddCSSFile(&$htmlNode,$cssFile){
		
		$head = &$htmlNode->GetFirstChild('head');

		$cssNode = $head->AddChild('link');
		$cssNode->attr("rel","stylesheet");
		$cssNode->attr("type","text/css");
		$cssNode->attr("href",$cssFile);
	}
	
	static public function ProcessingHead($flag = true){
		HTML5Parser::$processing_head = $flag;
	}
	static public function InHead(){
		return HTML5Parser::$processing_head;
	}
	
	static public function _isHtml5($tagName){
		//The HTML5 Tags - 13 February 2013
		//http://www.w3.org/TR/html5/
		
		//HTML5 specific tags
		$validTags = array(
			//ROOT
				'html', 
			//METADATA
				'head','title','base','link','meta','style', 
			 //SCRIPTING
				'script','noscript',
			//SECTIONS
				'body','article','section','nav','aside','h1','h2','h3','h4','h5','h6','hgroup','header','footer','address',
			//GROUPING
				'p','hr','pre','blockquote','ol','ul','li','dl','dt','dd','figure','figcaption','div',
			//TEXT-LEVEL
				'a','em','strong','small','s','cite','q','dfn','abbr','time','code','var','samp','kbd','sub','sup','i','b','u','mark','ruby','rt','rp','bdi','bdo','span','br','wbr',
			//EDITS
				'ins','del',
			//EMBEDDED			
				'img','iframe','embed','object','param','video','audio','source','track','canvas','map','area','math','svg',
			//TABULAR
				'table','caption','colgroup','col','tbody','thead','tfoot','tr','td','th',
			//FORMS
				'form','fieldset','legend','label','input','button','select','datalist','optgroup','option','textarea','keygen','output','progress','meter',
			//INTERACTIVE
				'details','summary','command','menu','dialog'
			);
			
		
		$result = in_array($tagName,$validTags);
		
		unset($validTags); return $result;
	}
	
	
	static public function _isSelfClosingHtml5($tagName){
		//The Self Closing Tags - 13 February 2013
		
		$scHeadTags = array('base','basefont','bgsound','command','link','meta');
		
		//Custom enforced list of self closing tags
		//'area','br','embed','img','keygen','wbr','input','param','source','track','hr','isindex','math','svg','col','frame'
		$scBodyTags = array('br','img','input','hr','col','frame');
		
		$validTags = array_merge($scHeadTags,$scBodyTags);
		$result = in_array($tagName,$validTags);
		unset($scTags); return $result;
	}
	
	static public function GetAttributes(&$node,$idPrefix = ""){
		
		//TODO: only valid attributes
		
		if($node->HasAttributes()){
			$html = ' ';
			
			$idp = $idPrefix;
			
			$attribs = array();

			$attributes = $node->GetAttributes();
			foreach($attributes as $attribute => $value){
			
				if(strtolower($attribute) == "id"){
					$value = $idPrefix.$value;
				}
				$attribs[] = $attribute . '="' . str_replace('"', '', $value) .'"';
			}
			
			$html .= implode(" ",$attribs);
			unset($attribs);
			return $html;
		}
		return '';
	}
	
	static public function Start(&$node, $doctype, $compress){

		$NL = '';
		if(!$compress){$NL = "\r\n";}
		
		//Make sure node attribs & children attribs is unique
		HTML5Parser::_unique($node);

		$html = '';
		
		if($node->IsComment()){
			$html = "<!--";
			//TODO: test fo IE
			return $html;
		}
		
		$nodeName = $node->GetName();
		if(HTML5Parser::_isHtml5($nodeName)){
			$html .= '<'.$node->GetName();	
		}else{
			$html .= '<div';
		}
		
		$html .= HTML5Parser::GetAttributes($node);
		
		if($node->IsSelfClosing() || HTML5Parser::_isSelfClosingHtml5($nodeName)){
			$html .= ' />';
		}else{
			$html .= '>';
		}
		$html .= $NL;
		return $html;
	}
	
	public static function Stop(&$node, $doctype, $compress){
		
		$NL = '';
		if(!$compress){$NL = "\r\n";}
	
		$html = '';
		
		if($node->IsComment()){
			$html = "-->".$NL;
			//TODO: test fo IE
			return $html;
		}
		
		$nodeName = $node->GetName();
		if(!HTML5Parser::_isHtml5($nodeName)){
			$nodeName = 'div';
		}
		if(!$node->IsSelfClosing() && !HTML5Parser::_isSelfClosingHtml5($nodeName)){$html .= '</'.$nodeName.'>'.$NL;};
		return $html;
	}
	
	public static function _out(&$node,$doctype, $compress=false){

		$NL = '';
		if(!$compress){$NL = "\r\n";}
		
		$nChildren = 0;

		if($node->GetName() =='head' || $node->GetName() =='html'){HTML5Parser::ProcessingHead();}
		if($node->GetName() =='body'){HTML5Parser::ProcessingHead(FALSE);$node->SetUnique(TRUE);}
		
	
		$out = HTML5Parser::Start($node,$doctype,$compress);

		if(!$node->IsSelfClosing()){
				
				if($node->GetValue()!= ''){$out .= $node->GetValue().$NL ;}
				
				if($node->HasChildren()){
					
					$nChildren = count($node->GetChildren());
					
					for($i = 0;$i < $nChildren;$i++){
						//get children count on each iteration since parent can get more children
						$nChildren = count($node->GetChildren());
						
						$child = $node->GetChild($i);
						if(HTML5Parser::InHead()){
							$child->SetUnique(FALSE);
						}
						$out .= HTML5Parser::_out($child,$doctype,$compress); 
						
					}
				}
		}

		$out .= HTML5Parser::Stop($node,$doctype,$compress);
		return $out;
	}

}


?>