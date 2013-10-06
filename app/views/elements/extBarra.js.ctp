    var barra = new Ext.Toolbar({
    	itemId:'barraProvisional'
    	,items: [new Ext.Toolbar.Fill(),{
            xtype: 'tbbutton'
            ,text: 'Salir'
            ,handler:function(){ 
            	Ext.Ajax.request({
                    url: '/aclusers/logout'
                    ,method: 'POST'
                    ,params: {}
                    ,success: function(respuesta,request) {
                        obj = Ext.util.JSON.decode(respuesta.responseText);
                        if (obj.success){
                            if(obj.hasOwnProperty('message')){
                                Ext.Msg.alert('Correcto!', obj.message);
                            }
                            if(obj.hasOwnProperty('redirect')){
                                window.location = obj.redirect;
                            }
                        }else{
                            request.failure();
                        }
                    }
                    ,failure: function() {
                        if (obj.hasOwnProperty('errors')){
                            if(typeof(obj.errors)=='object'){
                                errorstring='';
                                for(prop in obj.errors){errorstring+=obj.errors[prop]+"<br>";}	
                            }else{
                                errorstring=obj.errors;
                            }
                            Ext.Msg.alert('Error!', errorstring)
                            if(obj.hasOwnProperty('redirect')){
                                window.location = obj.redirect;
                            }
                        }else{
                            Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
                        }
                    }
                });
            }
        }] 
    });

	Ext.Ajax.request({
		url:'/recursos/bar'
		,params:{}
		,success:function(respuesta, request) {
			obj = Ext.util.JSON.decode(respuesta.responseText);
			if (obj.success){
				if(obj.hasOwnProperty('message')){
					Ext.Msg.alert('Correcto!', obj.message);
				}
				if(obj.hasOwnProperty('redirect')){
					window.location = obj.redirect;
				}
				if(obj.hasOwnProperty('variable')){
					eval(obj.variable);
                    viewPort.getComponent('north').remove(viewPort.getComponent('north').getComponent('barraProvisional'));
					viewPort.getComponent('north').add(barraDinamica);
					viewPort.getComponent('north').doLayout();
				}
			}else{
				request.failure();
			}
		}
		,failure:function() {
			if (obj.hasOwnProperty('errors')){
				if(typeof(obj.errors)=='object'){
					errorstring='';
					for(prop in obj.errors){errorstring+=obj.errors[prop]+"<br>";}	
				}else{
					errorstring=obj.errors;
				}
				Ext.Msg.alert('Error!', errorstring);
				if(obj.hasOwnProperty('redirect')){
					window.location = obj.redirect;
				}
			}else{
				Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
			}
		}
	});