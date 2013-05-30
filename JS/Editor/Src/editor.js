/*
** 30 May 2013
**
** The author disclaims copyright to this source code.
**
*************************************************************************/

function el(id){
 return document.getElementById(id);
}

function TsamaEditor(element,options){

	var Editor = this;

	this.node = document.createElement("div");
	this.node.style.boxShadow = '0 0 6px #888';
	this.node.style.background = '#FFFFFF';

	this.toolbar = document.createElement("div");
	this.toolbar.id = 'editor-toolbar';

	//default toolbar styles
	this.toolbar.style.background = '#CCCCCC';
	this.toolbar.style.border = '1px solid #ABABAB';
	this.toolbar.style.color = '#FFFFFF';
	this.toolbar.style.padding = '6px';

	this.body = document.createElement("div");
	this.body.id = 'editor-body';

	this.editor = document.createElement('div');
	this.editor.id = 'editor-input';
	this.editor.setAttribute('contenteditable',"true"); /*magic, HTML5 only*/

	//default body styles
	this.body.style.background = '#FFFFFF';
	this.body.style.border = '1px solid #CCCCCC';
	this.body.style.borderTop = '0';

	//default editor styles
	this.editor.style.border = '6px solid #EFEFEF';
	this.editor.style.padding = '24px';

	if(typeof options != 'undefined'){
		//editor toolbar options
		if (typeof options.toolbar != 'undefined') {
			//background
			if (typeof options.toolbar.background != 'undefined') {
				this.toolbar.style.background = options.toolbar.background;
			}
			//border
			if (typeof options.toolbar.border != 'undefined') {
				this.toolbar.style.border = options.toolbar.border;
			}
			//color
			if (typeof options.toolbar.color != 'undefined') {
				this.toolbar.style.color = options.toolbar.color;
			}
			//padding
			if (typeof options.toolbar.padding != 'undefined') {
				this.toolbar.style.padding = options.toolbar.padding;
			}
		}
		//editor body options
		if (typeof options.body != 'undefined') {
			//background
			if (typeof options.body.background != 'undefined') {
				this.body.style.background = options.body.background;
			}
			//border
			if (typeof options.body.border != 'undefined') {
				this.body.style.border = options.body.border;
			}
		}
		//editor text options
		if (typeof options.editor != 'undefined') {
			//padding
			if (typeof options.editor.padding != 'undefined') {
				this.editor.style.padding = options.editor.padding;
			}
		}
		
	}

	this.buttons = new Array(
		{
			'id':'editor-button-bold',
			'class':'editor-button',
			'text':'B',
			'title':'Strong <strong> Ctrl+B',
			'styles':{
				'fontWeight': 'bold'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("strong");
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}	
		},{
			'id':'editor-button-italic',
			'class':'editor-button',
			'text':'I',
			'title':'Emphasis <em> Ctrl+I',
			'styles':{
				'fontStyle': 'italic'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("em");
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}
		},{
			'id':'editor-button-underline',
			'class':'editor-button',
			'text':'U',
			'title':'Underline <u> Ctrl+U',
			'styles':{
				'textDecoration' : 'underline'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("span");
				el.style.textDecoration = 'underline';
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}
		},{
			'id':'editor-button-line-through',
			'class':'editor-button',
			'text':'S',
			'title':'Line Through',
			'styles':{
				'textDecoration': 'line-through'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("span");
				el.style.textDecoration = 'line-through';
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}
		},{
			'id':'editor-button-sub',
			'class':'editor-button',
			'text':'A<sub>x</sub>',
			'title':'Subscript',
			'styles':{},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("sub");
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}
		},{
			'id':'editor-button-sup',
			'class':'editor-button',
			'text':'A<sup>x</sup>',
			'title':'Superscript',
			'styles':{},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("sup");
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}
		},{
			'id':'editor-button-remove',
			'class':'editor-button',
			'text':'X',
			'title':'Remove Formatting',
			'styles':{
				'textDecoration' : 'underline'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createTextNode(selection.content.textContent);
			    selection.range.insertNode(el);

			    return false;
			}
		}
	);

	this.GetSelectionRange = function(){
		var range = null;

		if (typeof window.getSelection != "undefined") {

	    	range =  window.getSelection().getRangeAt(0);

	    } else if (typeof document.selection != "undefined") {

	    	range = document.selection.createRange();

	    }

		return range;
	}

	this.GetSelection = function(){

		var range = this.GetSelectionRange();

	    var selection = {
	    		'range' : range,
	    		'content' : range.extractContents()
	    };

	    return selection;

	}

	//this.toolbar.innerHTML = '<select><option>Font</option></select><select><option>Size</option></select><button>B</button><button>I</button><button>U</button>';
	var bl =  document.createElement("ul");
	bl.id = 'button-list';
	bl.style.listStyle = 'none';
	bl.style.padding = '0';
	bl.style.margin = '0';

	for (var key in this.buttons) {
		var button = this.buttons[key];

		var btnLi = document.createElement("li");
		btnLi.style.float = 'left';

		var btnA = document.createElement("a");
		btnA.id = button.id;
		btnA.className = button.class;
		btnA.innerHTML = button.text;
		btnA.title = button.title;
		btnA.style.display = 'block';
		btnA.style.padding = '3px';
		btnA.style.paddingLeft = '6px';
		btnA.style.paddingRight = '6px';
		btnA.style.marginRight = '3px';
		if(typeof button.styles.fontStyle != "undefined"){
			btnA.style.fontStyle = button.styles.fontStyle;
		}

		btnA.style.fontWeight = 'bold';
		btnA.style.fontSize = '120%';

		if(typeof button.styles.textDecoration != "undefined"){
			btnA.style.textDecoration = button.styles.textDecoration;
		}else{
			btnA.style.textDecoration = 'none';
		}		
		btnA.style.border = '1px solid ' + this.toolbar.style.background;
		btnA.style.background = this.toolbar.style.background;
		btnA.style.color = this.toolbar.style.color;
		btnA.style.cursor = 'pointer';
		btnA.href= "#";
		btnA.onclick = button.onclick;
		btnA.onmouseover = function(){
			this.style.border = '1px solid #999999';
		};
		btnA.onmouseout = function(){
			this.style.border = '1px solid ' + Editor.toolbar.style.background;
		};

		btnLi.appendChild(btnA);
		bl.appendChild(btnLi);
		
	}

	this.toolbar.appendChild(bl);
	var clr = document.createElement("div");
	clr.style.padding = '0';
	clr.style.margin = '0';
	clr.style.clear = 'both';
	clr.style.height = '1px';
	clr.style.lineHeight = '1px';
	clr.innerHTML = '&nbsp;';
	this.toolbar.appendChild(clr);

	this.editor.innerHTML = element.value;

	this.node.appendChild(this.toolbar);

	this.body.appendChild(this.editor);
	this.node.appendChild(this.body);

	element.parentNode.appendChild(this.node);

	element.style.display = 'none';

}