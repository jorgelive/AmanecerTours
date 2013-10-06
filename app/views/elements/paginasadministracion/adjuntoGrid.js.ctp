			var adjuntoEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : '<?php echo $html->url('/paginasadjuntos/modificar/') ?>'
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
										adjuntoEditor.grid.getStore().commitChanges();
										adjuntoEditor.grid.getView().refresh();
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
									adjuntoEditor.grid.getStore().rejectChanges();
									adjuntoEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			var adjuntoStore= new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,url: 'dummy'
				,root: 'adjuntos'
				,fields: [{
					name: 'id'
				},{
					name: 'icon'
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
							if(adjuntoPanel.paginatorStart&&adjuntoPanel.paginatorLimit){
								options.params.start=adjuntoPanel.paginatorStart;
								options.params.limit=adjuntoPanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						adjuntoPanel.paginatorStart=options.params.start;
						adjuntoPanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			var adjuntoGrid = new Ext.grid.GridPanel({
				store: adjuntoStore
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [new Ext.grid.RowNumberer()
				,{
					header: 'Id'
					,dataIndex: 'id'
					,width: 40
					,hidden: true
				},{
					header: 'Tipo de archivo'
					,width: 200
					,sortable: true
					,dataIndex: 'icon'
					,align:'center'
					,editor: {disabled: true}
					,renderer:Ext.util.Format.imageRenderer()
				},{
					header: 'Título'
					,id:'title'
					,dataIndex: 'title'
					,width: 200
					,editor: {
						allowBlank:false
						,blankText:'Ingrese el título de la imágen'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasadjuntos/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				}]
				,plugins: [adjuntoEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'title'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: adjuntoStore
					,displayMsg: 'Mostrando archivos adjuntos {0} - {1} de {2}'
					,emptyMsg: "No hay archivos adjuntos para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar archivo adjunto'
					,handler: function(){
						var agregarAdjuntoForm = new Ext.FormPanel({ 
							fileUpload: true
							,labelWidth:70
							,url:'<?php echo $html->url('/paginasadjuntos/agregar/') ?>'
							,frame:true
							,defaultType:'textfield'
							,monitorValid:true
							,items:[{ 
								xtype:'hidden'
								,name:'Paginasadjunto.pagina_id'
								,value: viewPort.getComponent('center').activeTab.newId
							},{ 
								fieldLabel:'Archivo'
								,name:'Paginasadjunto.title'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese el título'
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasadjuntos/validar/') ?>'})]
							},{ 
								xtype: 'fileuploadfield'
								,emptyText: 'Selecione un archivo adjunto'
								,buttonText: 'Seleccione'
								,fieldLabel:'Foto'
								,name:'Paginasadjunto.adjunto'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese el archivo adjunto'
							}]
							,buttons:[{ 
								text:'Enviar'
								,formBind: true
								,handler:function(){
									agregarAdjuntoForm.getForm().submit({ 
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
											agregarAdjuntoForm.getForm().reset();
											adjuntoGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasadjuntos/listar/') ?>'+viewPort.getComponent('center').activeTab.newId,true);
											adjuntoGrid.getStore().reload();
											adjuntoGrid.getView().refresh();
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
						var agregarAdjuntoWindow = new Ext.Window({
							autoScroll:true
							,title:'Agregar archivo adjunto'
							,modal:true
							,width: 330
							,height: 150
							,items:[agregarAdjuntoForm]
							,layout: 'fit'
						}).show();
						adjuntoEditor.stopEditing();
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar archivo adjunto'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						adjuntoEditor.startEditing(adjuntoGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar archivo adjunto'
					,disabled: true
					,handler: function(){
						adjuntoEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el archivo adjunto?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = adjuntoGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginasadjuntos/borrar') ?>'
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
												adjuntoEditor.grid.getStore().remove(row);
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
										adjuntoEditor.grid.getStore().rejectChanges();
										adjuntoEditor.grid.getView().refresh();
									}
								});
							}
						})
					}
				}]
			});
			adjuntoGrid.getSelectionModel().on('selectionchange', function(sm){
        		adjuntoGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				adjuntoGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var adjuntoMenuContextual = new Ext.menu.Menu({
				items: [{
					text: 'Modificar archivo adjunto'
					,handler:adjuntoGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-borrar'
				},
				'-'
				,{
					text: 'Borrar archivo adjunto'
					,handler:adjuntoGrid.removeBtn.handler
					,iconCls: 'x-menu-item-modificar'
				}]
			});
			
			adjuntoGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				adjuntoMenuContextual.showAt(event.getXY());
			});