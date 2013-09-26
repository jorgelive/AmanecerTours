<script>

function co(obj) {
	  str='';
	  for(prop in obj)
	  {
		str+=prop + " value :"+ obj[prop]+"\n";
	  }
	var txt=document.getElementById("south")
  	//txt.innerHTML='<pre>'+str+'</pre>';
	alert(str);
	}
	
Ext.BLANK_IMAGE_URL =  './js/ext-3.2.1/resources/images/default/s.gif';

Ext.onReady(function(){
    Ext.QuickTips.init();
 
	// Create a variable to hold our EXT Form Panel. 
	// Assign various config options as seen.	 
    var login = new Ext.FormPanel({ 
        labelWidth:80
        ,url:'/aclusers/login' 
        ,frame:true
    	,title:'<?php echo $title_for_layout;?>'
    	,defaultType:'textfield'
		,monitorValid:true
        ,items:[{ 
                fieldLabel:'Nombre'
                ,name:'Acluser.username' 
                ,allowBlank:false
				,blankText:'Ingrese su nombre'
            },{ 
                fieldLabel:'Contraseña' 
                ,name:'Acluser.password'
                ,inputType:'password' 
                ,allowBlank:false 
				,blankText:'Ingrese su contraseña'
            }]
        ,buttons:[{ 
                text:'Ingresar',
                formBind: true,	 
                handler:enviar=function(){ 
                    login.getForm().submit({ 
                        method:'POST', 
                        waitTitle:'Conectando', 
                        waitMsg:'Enviando información...',
                        success:function(form, action){ 
		                	obj = Ext.util.JSON.decode(action.response.responseText);
							var redirect = obj.redirect; 
		                    window.location = redirect;
                        },
                        failure:function(form, action){ 
							if(action.failureType == 'server'){ 
								obj = Ext.util.JSON.decode(action.response.responseText);
								if (obj.hasOwnProperty('errors')){
									if(typeof(obj.errors)=='object'){
										errorstring='';
										for(prop in obj.errors){errorstring+=obj.errors[prop]+"<br>";}	
									}else{
										errorstring=obj.errors;
									}
									Ext.Msg.alert('Error!', errorstring)
								}else{
									Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
								}
                            }else if(action.failureType == 'connect'){ 
                                Ext.Msg.alert('Error!', 'El servidor tiene un error'); 
                            }else{ 
                                Ext.Msg.alert('Error!', 'Los campos ingresados son inválidos'); 
                            } 
                            login.getForm().reset(); 
                        } 
                    }); 
                } 
            }] 
    });
 
 
	// This just creates a window to wrap the login form. 
	// The login object is passed to the items collection.       
    var win = new Ext.Window({
		layout:'fit'
        ,width:250
        ,height:140
        ,closable: false
        ,resizable: false
        ,plain: true
        ,border: false
        ,items: [login]
	});
	win.show();
	
	var map = new Ext.KeyMap(document,[
		{
			key: Ext.EventObject.ENTER,
			stopEvent: true,
			alt: false,
			fn: enviar
		}
	]);
	
});
</script>