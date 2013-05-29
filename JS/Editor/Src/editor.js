function el(id){
 return document.getElementById(id);
}

function TsamaEditor(element,options){

	var Editor = this;

	this.toolbar = document.createElement("div");
	this.toolbar.id = 'editor-toolbar';

	//default toolbar styles
	this.toolbar.style.background = '#EFEFEF';
	this.toolbar.style.border = '1px solid #CCCCCC';
	this.toolbar.style.padding = '6px';

	this.body = document.createElement("div");
	this.body.id = 'editor-body';

	this.editor = document.createElement('div');
	this.editor.id = 'editor-input';
	this.editor.setAttribute('contenteditable',"true"); /*magic, HTML5 only*/

	//default body styles
	this.body.style.background = '#FFFFFF';
	this.body.style.border = '1px solid #CCCCCC';
	this.body.style.borderTop = '0px';

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
		document.createElement("button"),
		document.createElement("button"),
		document.createElement("button"),
		document.createElement("button"),
		document.createElement("button"),
		document.createElement("button")
	);

	this.buttons[0].id = 'editor-button-bold';
	this.buttons[0].innerHTML = 'B';
	this.buttons[0].title = 'Strong <Strong> Ctrl+B';
	this.buttons[0].style.fontWeight = 'bold';

	this.buttons[1].id = 'editor-button-italic';
	this.buttons[1].innerHTML = 'I';
	this.buttons[1].title = 'Emphasis <em> Ctrl+I';
	this.buttons[1].style.fontStyle = 'italic';

	this.buttons[2].id = 'editor-button-underline';
	this.buttons[2].innerHTML = 'U';
	this.buttons[2].title = 'Underline <u> Ctrl+U';
	this.buttons[2].style.textDecoration = 'underline';

	this.buttons[3].id = 'editor-button-line-through';
	this.buttons[3].innerHTML = 'abc';
	this.buttons[3].title = 'Line Through';
	this.buttons[3].style.textDecoration = 'line-through';

	this.buttons[4].id = 'editor-button-sub';
	this.buttons[4].title = 'Sub';
	this.buttons[4].innerHTML = 'A<sub>x</sub>';

	this.buttons[5].id = 'editor-button-sup';
	this.buttons[5].title = 'Sup';
	this.buttons[5].innerHTML = 'A<sup>x</sup>';

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

	this.buttons[0].onclick = function(){

		var selection = Editor.GetSelection();

		var el = document.createElement("strong");
	    el.appendChild(selection.content);
	    selection.range.insertNode(el);
		
	}

	this.buttons[1].onclick = function(){

		var selection = Editor.GetSelection();

		var el = document.createElement("em");
	    el.appendChild(selection.content);
	    selection.range.insertNode(el);
		
	}

	this.buttons[2].onclick = function(){

		var selection = Editor.GetSelection();

		var el = document.createElement("span");
		el.style.textDecoration = 'underline';
	    el.appendChild(selection.content);
	    selection.range.insertNode(el);
		
	}

	this.buttons[3].onclick = function(){

		var selection = Editor.GetSelection();

		var el = document.createElement("span");
		el.style.textDecoration = 'line-through';
	    el.appendChild(selection.content);
	    selection.range.insertNode(el);
		
	}

	this.buttons[4].onclick = function(){

		var selection = Editor.GetSelection();

		var el = document.createElement("sub");
	    el.appendChild(selection.content);
	    selection.range.insertNode(el);
		
	}

	this.buttons[5].onclick = function(){

		var selection = Editor.GetSelection();

		var el = document.createElement("sup");
	    el.appendChild(selection.content);
	    selection.range.insertNode(el);
		
	}

	//this.toolbar.innerHTML = '<select><option>Font</option></select><select><option>Size</option></select><button>B</button><button>I</button><button>U</button>';
	for (var key in this.buttons) {
		this.toolbar.appendChild(this.buttons[key]);
	}

	this.editor.innerHTML = element.value;

	element.parentNode.appendChild(this.toolbar);

	//this.editor.appendChild(this.blinker);
	this.body.appendChild(this.editor);
	element.parentNode.appendChild(this.body);

	element.style.display = 'none';

}