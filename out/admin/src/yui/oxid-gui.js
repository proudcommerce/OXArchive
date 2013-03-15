YAHOO.namespace( 'YAHOO.oxid' );

var $  = YAHOO.util.Dom.get,
    $D = YAHOO.util.Dom,
    $E = YAHOO.util.Event;

// --------------------------------------------------------------------------------

if(YAHOO.widget.Dialog && YAHOO.widget.ColorPicker){
(function () {

    YAHOO.oxid.gui = function( elBox, elTabs, elTheme , elPicker, elTree, elPreview, selfUrl, srcUrl)
    {
        var me     = this;
        var slf    = selfUrl;
        
        var css    = 'cl=gui&fnc=previewCss';
        var sav    = 'cl=gui&fnc=save';

        var assets = srcUrl + 'yui/build/colorpicker/assets/';

        this.theme = '';
        this.color = '';
        this.style = '';

        this.edit    = '';

        this.dialog  = '';
        this.picker  = '';
        this.tabs    = '';
        this.tree    = '';
        
        this.init = function() {

            me.dialog = new YAHOO.widget.Dialog(elBox, {  
                width : "350px",
                height : "518px",
                fixedcenter : false, 
                visible : false,  
                constraintoviewport : true , 
                buttons : [ { text:"Save", handler:me.doSave, isDefault:true } ] 
             }); 

            this.dialog.renderEvent.subscribe(this.addPicker);
        };
   
        
        this.addPicker = function(){
            
            if (!me.tabs) {
                me.tabs = new YAHOO.widget.TabView(elTabs,{orientation:'top'}); 
            }
            
            if (!me.picker) {
                me.picker = new YAHOO.widget.ColorPicker( elPicker, { 
                    showcontrols: true,  showhexcontrols: true, container:this, 
                    images: { 
                        PICKER_THUMB: assets+"picker_thumb.png", 
                        HUE_THUMB: assets+"hue_thumb.png"} 
                    });
                    
                me.picker.on("rgbChange", function(o) { me.returnColor("#" + this.get("hex")); });
            }

            if (!me.tree) {
                me.tree = new YAHOO.widget.TreeView( elTree ); 
                me.tree.render();
                me.tree.subscribe("expand", function(node) {
                	
            	// all colors has no child nodes
            	if (!node.hasChildren()) {
            		var iid = node.getEl().getElementsByTagName('b')[0].title;
            		
            		me.editColor( iid );
            		return false;
            	}
                	
                });
            }
            
        };
        
        this.loadColors = function() {
        	var inputs = document.getElementsByTagName('input');
        	
        	for(var i=0;i<inputs.length;i++){
        		var input = inputs[i];
        		if( input.getAttribute("rel") ){
        			var cl =  this.parseColor(input.getAttribute('rel'),'backgroundColor');
        			input.value = cl;
        		}
        	}

        };

        this.parseColor = function(id,style){
        	
        	var cl = $D.getStyle($(id),style);
        	
        	if(cl.indexOf('#') != -1){
        		return this.normalizeColor(cl);
        	}
        	
        	if(cl.indexOf('rgb') != -1){
        		return '#'+YAHOO.util.Color.rgb2hex( cl.replace(/[rgb ()]/img, "").split(',') );
        	}

        };
        
        this.normalizeColor = function(cl){
            if(cl.length == 4){
            	cl = cl.charAt(1) + cl.charAt(1) + cl.charAt(2) + cl.charAt(2) + cl.charAt(3) + cl.charAt(3);
            }
            return cl.toUpperCase();
        };
        
        this.editColor = function(id) {
            this.edit = id;
            
            var value = this.normalizeColor($(id).value).replace('#','');
            
            if(value.length == 6) {
                this.picker.setValue(YAHOO.util.Color.hex2rgb(value), true);
            }
        };
        
        this.returnColor = function(c){
            
        	if( $(this.edit) ) {
        		
        		var tab = this.tabs.get('activeIndex');

                $(this.edit).value = c;

                if(tab == 1) {
                	this.addStyle(2, this.edit, c);
                	this.setColor();
                }
                
                if(tab == 2) {
                	this.addStyle(1, this.edit, c);
                	this.setStyle();
                }
            }
        };
        
        this.switchStyles = function(id) {
    	    var i, a;
    	    for(i=0; (a = document.getElementsByTagName("style")[i]); i++) { 
    	    	if( a.title && a.getAttribute("rel") && a.getAttribute("rel").indexOf("style") != -1 ) {
    	    		a.disabled = (a.id != id);
    	    	}
    	    }
        };
        
        this.clearStyles = function(){
    		var css = $(block);
    		//css.innerHTML = '';
    	};
        
        this.addStyle = function(nng,name,color){
        	
        	var ln = document.styleSheets.length;
        	var nr = ln-nng;
        	var st = document.styleSheets[nr];
        	
        	if(st){
        		if (st.insertRule) {
        		    st.insertRule ('.'+name+'{  background-color:'+color+';', st.cssRules.length );
        		}  
        		else if (st.addRule) {
        		    st.addRule ('.'+name , 'background-color:'+color+';' );
        		}
        	}

        };
        
        this.render = function() {
            this.dialog.render(document.body);
            
            $E.on(elTheme,'change',function() {me.setTheme(this.value);});
            $E.on(elPreview,'load',function() {me.doPreview();});
        };
        
        this.show = function() {
            this.dialog.show();
        };
        
        this.setTheme = function(t){
            this.setUrl('theme',"&t="+t);      
            this.switchStyles('gui-th-'+t);
            this.doPreview();
            this.loadColors();
        };
        
        this.setColor = function(){

            var data = this.dialog.getData();
            var url  = '';

            for (var i in data){
                if(i.substring(0,2) !='s['){
                    url = url+'&'+i+'='+escape(data[i]);
                }
            }

            this.setUrl('color',url);
            this.doPreview();
            this.loadColors();
        };
        
        this.setStyle = function(){
        	
            var data = this.dialog.getData();
            var url  = '';
            
            for (var i in data){
            	url = url+'&'+i+'='+escape(data[i]);
            }
            
            this.setUrl('style',url);
            this.doPreview();
        };
        
        this.getUrl = function() {
            if(this.theme) { return me.theme;}
            if(this.color) { return me.color;}
            if(this.style) { return me.style;}
            return '';
        };
        
        this.setUrl = function(type,url) {
        	this.theme = type=='theme'?url:'';
            this.color = type=='color'?url:'';
            this.style = type=='style'?url:'';
        };
        
        
        this.doPreview = function() {
            var url = me.getUrl();
            if(url) {
            	//YAHOO.util.Get.css(css+data,{data:'aaa=10',win:frames.preview});
            	var rq = YAHOO.util.Connect.asyncRequest('POST', slf, { success: function(o) { me.setCss( o.responseText)}}, css+url );
            	
            }
        };
        
        this.doSave = function() {
        	var url = me.getUrl();
            if(url) {
                var rq = YAHOO.util.Connect.asyncRequest('POST', slf, { success: function(o) { me.showMessage(o.responseText)}}, sav+url); 
            }
        };
        
        this.setCss = function (csstext) {

        	var el = frames.preview.document.styleSheets[0];
        	
        	if(el.cssText){
        		el.cssText = csstext;
        	}else{
        		var cel , sel, gid = 'gui-css-preview';
        		
    			el  = frames.preview.document.getElementsByTagName('link')[0];
        		cel = document.createElement('style');
        		cel.setAttribute('id',gid);
        		cel.appendChild(document.createTextNode(csstext));

        		sel = frames.preview.document.getElementById(gid);
        		
        		if(sel){
        			sel.parentNode.replaceChild(cel,sel);
        		}else{
        			$D.insertAfter(cel,el);
        		}
    		}
        	
        };

        this.showMessage = function (msgtext) {
        	if(msgtext.length){ 
        		alert(msgtext) 
        	}
        };
    
        this.init();
    };

}());
}
