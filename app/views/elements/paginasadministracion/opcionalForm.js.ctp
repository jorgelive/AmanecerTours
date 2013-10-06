			var opcionalForm = new Ext.FormPanel({ 
				xtype:'form'
				,labelAlign: 'top'
				,url:'dummy'
				,frame:true
				,monitorValid:true
				,items:[{
					layout:'column'
					,defaults:{
						layout:'form'
						,border:false
						,xtype:'panel'
					}
					,items:[{
						columnWidth:0.5
						,bodyStyle:'padding:0 10px 5px 0'
						,items:[{ 
							xtype: 'datefield'
							,fieldLabel: 'Inicio'
							,name:'Paginasopcional.publicado_inicio'
							,anchor: '100%'
							,format:'d-m-Y'
							,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
							,listeners:{
								'select':function(field){
									var date = field.parseDate(field.value);
									var end = field.ownerCt.ownerCt.ownerCt.getForm().findField('Paginasopcional.publicado_final');
									end.setMinValue(date);
									end.validate();
									end.dateRangeMin = date;
								}
							}
						}]
					},{
						columnWidth:0.5
						,bodyStyle:'padding:0 0px 5px 5px'
						,items:[{ 
							xtype: 'datefield'
							,fieldLabel: 'Final'
							,name:'Paginasopcional.publicado_final'
							,anchor: '100%'
							,format:'d-m-Y'
							,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
							,listeners:{
								'select':function(field){
									var date = field.parseDate(field.value);
									var start = field.ownerCt.ownerCt.ownerCt.getForm().findField('Paginasopcional.publicado_inicio');
									start.setMaxValue(date);
									start.validate();
									start.dateRangeMax = date;
								}
							}
						}]
					}]
				},{
					xtype: 'combo'
					,name: 'Paginasopcional.idfoto'
					,fieldLabel: 'Imagen a mostrar'
					,hiddenName: 'Paginasopcional.idfoto'
					,anchor: '100%'
					,store: new Ext.data.JsonStore({
						autoLoad: false
						,proxy: new Ext.data.HttpProxy({
							url: 'dummycombo'
							,method: 'POST'
						})
						,root: 'Imagen'
						,fields: ['id','name']
					})
					,displayField: 'name'
					,valueField: 'id'
					,typeAhead: true
					,mode: 'local'
					,triggerAction: 'all'
					,emptyText: 'Seleccione foto a mostrar'
					,selectOnFocus:true
					,allowBlank: true
				},{ 
					xtype:'textfield'
					,fieldLabel:'Dirección fija'
					,name:'Paginasopcional.urlfija'
					,anchor: '100%'
					,allowBlank:true
					,emptyText:'Sólo para páginas corporativas'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasopcionales/validar/') ?>'})]
				},{
					xtype: "textarea"
					,name:'Paginasopcional.etiquetas'
					,fieldLabel: 'Etiquetas'
					,allowBlank: true
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasopcionales/validar/') ?>'})]
					,anchor: '100%'
					,height:70
				},{
					xtype: 'radiogroup'
					,name:'idioma'
					,allowBlank:false
					,hidden:true
					,items: [
					<?php
						$idiomas=array();
						foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
							$idiomas[]='{name: \'idioma\', inputValue:\''.$key.'\'}';
						}
						echo implode(',',$idiomas);
					?>
					]
				},{ 
					xtype:'hidden'
					,name:'Paginasopcional.id'
				},{ 
					xtype:'hidden'
					,name:'Paginasopcional.pagina_id'
				}]
				,buttons:[{ 
					text:'Enviar',
					formBind: true,	 
					handler:function(){
						opcionalForm.getForm().submit({ 
							method:'POST'
							,submitEmptyText:false
							,waitTitle:'Conectando'
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
									viewPort.getComponent('center').remove(agregarEditar);
								}
								else{
									//agregarEditar.items.items[0].expand();
									editarAccion('generales');
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