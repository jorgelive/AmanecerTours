			var generalGrid = new Ext.grid.EditorGridPanel({
				store: new Ext.data.JsonStore({
					fields: [
						{name: 'accion', type: 'string'}
						,{name: 'valor', type: 'bool'}
					]
					,listeners:{
						'update':function(store,record,operacion){
							if(operacion==Ext.data.Record.EDIT){
								generalForm.getForm().findField(record.id).setValue(record.getChanges().valor);
							}
						}
					}
				})
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [
				{
					header: "Acción"
					,id:'accion'
					,width: 150
					,dataIndex: 'accion'
				},{
					header: "Valor"
					,dataIndex: 'valor'
					,align:'center'
					,width: 80
					,editor: new Ext.form.Checkbox({})
					,renderer: Ext.util.Format.siNoRenderer()
				}]
				,stripeRows: true
				,autoExpandColumn: 'accion'
				,clicksToEdit: 2
				,height:220
			});
			
			var generalForm = new Ext.FormPanel({ 
				xtype:'form'
				,labelAlign: 'top'
				,frame:true
				,monitorValid:true
				,items:[
				{
					xtype:'textfield'
					,fieldLabel:'Título'
					,name:'Pagina.title'
					,allowBlank:false
					,blankText:'Ingrese el titulo'
					,anchor:'100%'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginas/validar/') ?>'})]
				},{
					xtype: 'combo'
					,name: 'Pagina.predeterminado'
					,fieldLabel: 'Tipo al inicio'
					,hiddenName: 'Pagina.predeterminado'
					,anchor: '100%'
					,store: new Ext.data.JsonStore({
						autoLoad: false
						,proxy: new Ext.data.HttpProxy({
							url: 'dummypredeterminado'
							,method: 'POST'
						})
						,root: 'Tipos'
						,fields: ['id','name']
					})
					,displayField: 'name'
					,valueField: 'id'
					,typeAhead: true
					,mode: 'local'
					,triggerAction: 'all'
					,emptyText: 'Seleccione el tipo a mostrar al inicio'
					,selectOnFocus:true
					,allowBlank: true
				},{
					xtype:'panel'
					,items:[generalGrid]
				},{
					hidden:true
					,fieldLabel:'Publicar?'
					,xtype:'checkbox'
					,name:'Pagina.publicado'
					,inputValue:1
					,listeners:{
						'check':function(checkbox){
							if(checkbox.checked===true){
								generalForm.guardarBtn.setText('Publicar');
							}else{
								generalForm.guardarBtn.setText('Guardar borrador');
							}
						}
					}
				},{
					hidden:true
					,fieldLabel:'Mostrar en inicio'
					,xtype:'checkbox'
					,name:'Pagina.mostrarinicio'
					,inputValue:1
				},{
					
					hidden:true
					,fieldLabel:'Texto e imágenes?'
					,xtype:'checkbox'
					,name:'Pagina.texto'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Textos Multiples'
					,xtype:'checkbox'
					,name:'Pagina.multiple'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Galería de imágenes'
					,xtype:'checkbox'
					,name:'Pagina.imagen'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Galería de videos'
					,xtype:'checkbox'
					,name:'Pagina.video'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Panel de adjuntos'
					,xtype:'checkbox'
					,name:'Pagina.adjunto'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Promociones'
					,xtype:'checkbox'
					,name:'Pagina.promocion'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Formulario de contactos'
					,xtype:'checkbox'
					,name:'Pagina.contacto'
					,inputValue:1
				},{
					hidden:true
					,xtype: 'radiogroup'
					,name:'idioma'
					,allowBlank:false
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
					,name:'Pagina.id' 
				},{ 
					xtype:'hidden'
					,name:'Pagina.parent_id' 
				}]
				,buttons:[{
					ref: '../guardarBtn'
					,formBind: true	
					,iconCls: 'x-boton-guardar'
					,text:''
					,handler:function(){
						generalForm.getForm().submit({ 
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
								var selectNode=function(value){
									tree.getNodeById(value).select();
								}
								
								selectNode(viewPort.getComponent('center').activeTab.openerNode);
								if(generalForm.getForm().url=='/paginas/modificar/'){
									var node = tree.getSelectionModel().getSelectedNode();
									if(node.id!='root'){
										node=node.parentNode;
									}
									node.on('expand',selectNode.createDelegate(this,[viewPort.getComponent('center').activeTab.openerNode]),this,{single:true});
								}else{
									viewPort.getComponent('center').activeTab.newId=obj.data.newId;
									viewPort.getComponent('center').activeTab.openerNode=obj.data.newId;
									var node=tree.getSelectionModel().getSelectedNode();
									node.on('expand',selectNode.createDelegate(this,[obj.data.newId]),this,{single:true});
								}
								node.reload();
								node.expand();
								editarAccion(['generales','texto','contacto']);
								agregarEditar.doLayout();
							},
							failure:function(form, respuesta){ 
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
										Ext.Msg.alert('Error!', 'El servidor tuvo una respuesta nula');
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