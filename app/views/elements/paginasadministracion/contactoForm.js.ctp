			var contactoForm = new Ext.FormPanel({ 
				labelAlign: 'top'
				,url:'dummy'
				,frame:true
				,defaultType:'textfield'
				,monitorValid:true
				,items:[{ 
					xtype:'hidden'
					,name:'Paginascontacto.id'
				},{ 
					xtype:'hidden'
					,name:'Paginascontacto.pagina_id'
				},{ 
					fieldLabel:'Destinatario'
					,name:'Paginascontacto.destinatario'
					,width:200
					,allowBlank:false
					,emptyText:'<?php echo Configure::read('Default.email');?>'
					,blankText:'Ingrese destinatario'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascontactos/validar/') ?>'})]
				},{ 
					fieldLabel:'CCO'
					,name:'Paginascontacto.cco'
					,emptyText:'<?php echo Configure::read('Default.cco');?>'
					,width:200
					,allowBlank:true
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascontactos/validar/') ?>'})]
				}]
				,buttons:[{ 
					text:'Enviar'
					,formBind: true	 
					,handler:function(){
						contactoForm.getForm().submit({ 
							method:'POST'
							,waitTitle:'Conectando'
							,submitEmptyText:false
							,waitMsg:'Enviando información...'
							,success:function(form, respuesta){ 
								obj = Ext.util.JSON.decode(respuesta.response.responseText);
								if(obj.hasOwnProperty('message')){
									Ext.Msg.alert('Correcto!', obj.message);
								}
								if(obj.hasOwnProperty('redirect')){
									window.location = obj.redirect;
								}
								if (agregarEditar.items.length==0){
									viewPort.getComponent('center').remove(agregar);
								}
								else{
									editarAccion(['generales','contacto']);
									agregarEditar.doLayout();
								}
							}
							,failure:function(form, respuesta){ 
								if(respuesta.failureType == 'server'){ 
									obj = Ext.util.JSON.decode(respuesta.response.responseText); 
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
								}else if(respuesta.failureType == 'connect'){ 
									Ext.Msg.alert('Error!', 'El servidor tiene un error : ' + respuesta.response.responseText); 
								}else{ 
									Ext.Msg.alert('Error!', 'El cliente no puede procesar la respuesta del servidor');
								} 
							}  
						}); 
					} 
				}] 
			});