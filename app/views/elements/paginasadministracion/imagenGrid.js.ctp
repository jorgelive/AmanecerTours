			var imagenEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : '<?php echo $html->url('/paginasimagenes/modificar/') ?>'
								,method: 'POST'
								,params: record.data
								,success: function(respuesta,request) {
									obj = Ext.util.JSON.decode(respuesta.responseText);
									if (obj.success){
										if(obj.hasOwnProperty('message')){
											Ext.Msg.alert('Correcto!', obj.message);
										}
										if(obj.hasOwnProperty('redirect')){
											window.location = obj.redirect;
										}
										imagenEditor.grid.getStore().commitChanges();
										imagenEditor.grid.getView().refresh();
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
									imagenEditor.grid.getStore().rejectChanges();
									imagenEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			
			var imagenStore = new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,url: 'dummy'
				,root: 'imagenes'
				,fields: [{
					name: 'id'
				},{
					name: 'imagen'
				},{
					name: 'title'
				},{
					name: 'idioma'
				}]
				,remoteSort:true
				,sortInfo: {
					field: 'id'
					,direction: 'DESC'
				}
				,listeners:{
					'beforeload': function(store, options) {
						if (!options.params.start&&!options.params.limit){
							if(imagenPanel.paginatorStart&&imagenPanel.paginatorLimit){
								options.params.start=imagenPanel.paginatorStart;
								options.params.limit=imagenPanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						imagenPanel.paginatorStart=options.params.start;
						imagenPanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			
			var imagenGrid = new Ext.grid.GridPanel({
				store: imagenStore
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [new Ext.grid.RowNumberer()
				,{
					header: 'Id'
					,dataIndex: 'id'
					,width: 20
					,hidden: true
				},{
					header: 'Imagen'
					,width: 100
					,sortable: false
					,dataIndex: 'imagen'
					,align:'center'
					,editor: {disabled: true}
					,renderer:Ext.util.Format.imageRenderer(100,true)
				},{
					header: 'Titulo'
					,id:'title'
					,dataIndex: 'title'
					,width: 200
					,editor: {
						allowBlank:false
						,blankText:'Ingrese el título de la imágen'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasimagenes/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				}]
				,plugins: [imagenEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'title'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: imagenStore
					,displayMsg: 'Mostrando imágenes {0} - {1} de {2}'
					,emptyMsg: "No hay imágenes para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar imagen'
					,handler: function(){
						var agregarImagenForm = new Ext.FormPanel({ 
							fileUpload: true
							,labelWidth:70
							,url:'<?php echo $html->url('/paginasimagenes/agregar/') ?>'
							,frame:true
							,defaultType:'textfield'
							,monitorValid:true
							,items:[{ 
								xtype:'hidden'
								,name:'Paginasimagen.pagina_id'
								,value: viewPort.getComponent('center').activeTab.newId
							},{ 
								fieldLabel:'Título'
								,name:'Paginasimagen.title'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese el título'
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasimagenes/validar/') ?>'})]
							},{ 
								xtype: 'fileuploadfield'
								,emptyText: 'Selecione una imagen'
								,buttonText: 'Seleccione'
								,fieldLabel:'Foto'
								,name:'Paginasimagen.imagen'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese la imagen'
							}]
							,buttons:[{ 
								text:'Enviar'
								,formBind: true
								,handler:function(){
									agregarImagenForm.getForm().submit({ 
										method:'POST'
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
											agregarImagenForm.getForm().reset();
											imagenGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasimagenes/listar/') ?>'+viewPort.getComponent('center').activeTab.newId,true);
											imagenGrid.getStore().reload();
											imagenGrid.getView().refresh();
											editarAccion('generales');
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
						var agregarImagenWindow = new Ext.Window({
							autoScroll:true
							,title:'Agregar imagen'
							,modal:true
							,width: 330
							,height: 150
							,items:[agregarImagenForm]
							,layout: 'fit'
						}).show();
						imagenEditor.stopEditing();
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar imagen'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						imagenEditor.startEditing(imagenGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar imagen'
					,disabled: true
					,handler: function(){
						imagenEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar la imagen?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = imagenGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginasimagenes/borrar') ?>'
									,method: 'POST'
									,params: rowIds
									,success: function(respuesta,request) {
										obj = Ext.util.JSON.decode(respuesta.responseText);
										if (obj.success){
											if(obj.hasOwnProperty('message')){
												Ext.Msg.alert('Correcto!', obj.message);
											}
											if(obj.hasOwnProperty('redirect')){
												window.location = obj.redirect;
											}
											for(var i = 0, row; row = selectedRows[i]; i++){
												imagenEditor.grid.getStore().remove(row);
											}
											editarAccion('generales');
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
										imagenEditor.grid.getStore().rejectChanges();
										imagenEditor.grid.getView().refresh();
									}
								});
							}
						})
					}
				}]
			});
			imagenGrid.getSelectionModel().on('selectionchange', function(sm){
        		imagenGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				imagenGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var imagenMenuContextual = new Ext.menu.Menu({
				items: [
				{
					text: 'Modificar imagen'
					,handler:imagenGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-modificar'
				},
				'-'
				,{
					text: 'Borrar imagen'
					,handler:imagenGrid.removeBtn.handler
					,iconCls: 'x-menu-item-borrar'
				}]
			});
			
			imagenGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				imagenMenuContextual.showAt(event.getXY());
			});