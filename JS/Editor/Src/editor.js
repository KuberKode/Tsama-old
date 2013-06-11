/*
** 30 May 2013
**
** The author disclaims copyright to this source code.
**
*************************************************************************/

function el(id){ return document.getElementById(id); }

function TsamaEditor(){
	var Editor = this; //for internal use only

	this.data = null; //TOO: Data used for Posts
	this.element = null; //Active Element

	this.toolbar = document.createElement("div");
	this.toolbar.id = 'editor-toolbar';

	//default toolbar styles
	this.toolbar.style.background = '#CCCCCC';
	this.toolbar.style.border = '1px solid #ABABAB';
	this.toolbar.style.color = '#FFFFFF';
	this.toolbar.style.padding = '3px';
	this.toolbar.style.fontSize = '80%';
	this.toolbar.style.display = 'none';
	this.toolbar.style.position = 'absolute';
	this.toolbar.style.boxShadow = '0 0 6px #888';

	document.body.appendChild(this.toolbar);

	this.init = function(){
		if(typeof document.getElementsByClassName != 'undefined'){

			var elements = document.getElementsByClassName('editable');
			if(elements.length > 0){
				for (var key in elements){
					elements[key].onclick = Editor.edit;
					//TODO: create data entry for this
				}
			}
		}

		window.document.onkeydown = function (e){
	        if (!e){ e = event; }

	        //TODO: detect CTRL+B, CTRL+U , CTRL+I

	        if (e.keyCode == 27){
	        	Editor.escape();
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
		},{
			'id':'editor-button-normal',
			'class':'editor-button-style',
			'text':'AaBbCc<br />Normal',
			'title':'Normal',
			'styles':{
				'fontWeight': 'bold'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("p");
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}	
		},{
			'id':'editor-button-h1',
			'class':'editor-button-style',
			'text':'<span class="biggest">AaBb</span><br />Heading 1',
			'title':'H1',
			'styles':{
				'fontWeight': 'bold'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("h1");
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}	
		},{
			'id':'editor-button-h2',
			'class':'editor-button-style',
			'text':'<span class="biggest">AaBb</span><br />Heading 2',
			'title':'H2',
			'styles':{
				'fontWeight': 'bold'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				var el = document.createElement("h2");
			    el.appendChild(selection.content);
			    selection.range.insertNode(el);

			    return false;
			}	
		},{
			'id':'editor-button-h3',
			'class':'editor-button-style',
			'text':'<span class="biggest">AaBb</span><br />Heading 3',
			'title':'H3',
			'styles':{
				'fontWeight': 'bold'
			},
			'onclick': function(){
				var selection = Editor.GetSelection();

				container = selection.range.startContainer.parentNode;

				alert(container.nodeName);

				var el = document.createTextNode(selection.content.textContent);
			    selection.range.insertNode(el);

			    var h3 = document.createElement("h3");
				h3.innerHTML = container.innerHTML;

			    container.parentNode.replaceChild(h3, container);

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
		if(button.class=='editor-button-style'){
			btnA.style.border = '2px solid #999999';
		}
		btnA.style.background = this.toolbar.style.background;
		if(button.class=='editor-button-style'){
			btnA.style.background = '#FFFFFF';
		}
		btnA.style.color = this.toolbar.style.color;
		if(button.class=='editor-button-style'){
			btnA.style.color = '#333333';
		}
		btnA.style.cursor = 'pointer';
		btnA.href= "#";
		btnA.onclick = button.onclick;
		btnA.onmouseover = function(){
			this.style.border = '1px solid #999999';
			if(this.className=='editor-button-style'){
				this.style.border = '2px solid #BBBBBB';
			}
		};
		btnA.onmouseout = function(){
			this.style.border = '1px solid ' + Editor.toolbar.style.background;
			if(this.className=='editor-button-style'){
				this.style.border = '2px solid #999999';
			}
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

	this.ShowToolbar = function(){
		this.toolbar.style.display='block';
		Editor.element.parentNode.position = 'relative';
		this.toolbar.style.top = (Editor.element.offsetTop - this.toolbar.offsetHeight - 3) + "px";
		Editor.element.parentNode.insertBefore(this.toolbar,Editor.element);
	}

	this.HideToolbar = function(){
		this.toolbar.style.display='none';
	}

	this.escape = function(){

		Editor.HideToolbar();
		if(Editor.element != null){
			Editor.element.style.boxShadow = 'none';

			Editor.element.setAttribute('contenteditable',"false");
			Editor.element.style.border = '';
			Editor.element.style.padding = '';

			Editor.element = null;
		}
	}

	this.edit = function(){

		if(Editor.element != null){
			Editor.escape();
		}
		Editor.element = this;

		//this.style.boxShadow = '0 0 6px #888';
		this.style.background = '#FFFFFF';

		this.setAttribute('contenteditable',"true"); /*magic, HTML5 only*/
		this.style.border = '1px dotted #CCCCCC';
		this.style.padding = '6px';

		Editor.ShowToolbar();

		//this.onblur = Editor.escape;
	}

	Editor.init();
}