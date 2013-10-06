			var videoEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : !record.data.id ? '<?php echo $html->url('/paginasvideos/agregar/') ?>'+viewPort.getComponent('center').activeTab.newId : '<?php echo $html->url('/paginasvideos/modificar/') ?>'
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
										if (obj.hasOwnProperty('data')){
											if(!videoEditor.record.data.id){
												videoEditor.record.data.id=obj.data.newId;
												videoEditor.record.id=obj.data.newId;
											}
											editarAccion('generales');
										}
										videoEditor.grid.getStore().commitChanges();
										videoEditor.grid.getView().refresh();
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
										Ext.Msg.alert('Error!', 'El servidor tuvo una respuesta nula');
									}
									if(!record.data.id){
										videoEditor.grid.getStore().removeAt(rowIndex);
									}else{
										videoEditor.grid.getStore().rejectChanges()
									}
									videoEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			var videoStore = new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,root: 'videos'
				,fields: [{
					name: 'id'
				},{
					name: 'fuente'
				},{
					name: 'imagen'
				},{
					name: 'codigo'
				},{
					name: 'descripcion'
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
							if(videoPanel.paginatorStart&&videoPanel.paginatorLimit){
								options.params.start=videoPanel.paginatorStart;
								options.params.limit=videoPanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						videoPanel.paginatorStart=options.params.start;
						videoPanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			var videoGrid = new Ext.grid.GridPanel({
				store: videoStore
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [new Ext.grid.RowNumberer()
				,{
					header: "Id"
					,dataIndex: 'id'
					,width: 20
					,hidden: true
				},{
					header: "Fuente"
					,width: 130
					,dataIndex: 'fuente'
					,editor: {
						xtype: 'combo'
						,store: new Ext.data.JsonStore({
							autoLoad: false
							,proxy: new Ext.data.HttpProxy({
								url: '/dummyfuentes'
								,method: 'POST'
							})
							,root: 'fuentes'
							,fields: ['id','name']
							,listeners:{
								'beforeload': function(store, options) {
									options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
									return true;
								}
							}
						})
						,displayField: 'name'
						,valueField: 'id'
						,typeAhead: true
						,mode: 'local'
						,triggerAction: 'all'
						,emptyText: 'Seleccione la fuente'
						,selectOnFocus:true
						,allowBlank:false
						,blankText:'Ingrese la fuente'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasvideos/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				},{
					header: 'Imagen'
					,width: 110
					,sortable: false
					,dataIndex: 'imagen'
					,align:'center'
					,editor: {disabled: true}
					,renderer:Ext.util.Format.imageRenderer(100)
				},{
					header: "Código"
					,width: 250
					,dataIndex: 'codigo'
					,editor: {
						allowBlank:false
						,blankText:'Ingrese el código'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasvideos/validar/') ?>'})]
					}
				},{
					header: "Descripción"
					,id:'descripcion'
					,dataIndex: 'descripcion'
					,width: 200
					,editor: {
						allowBlank:false
						,blankText:'Ingrese la descripción'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasvideos/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
					
				}]
				,plugins: [videoEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'descripcion'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: videoStore
					,displayMsg: 'Mostrando videos {0} - {1} de {2}'
					,emptyMsg: "No hay videos para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar video'
					,handler: function(){
						var Video = Ext.data.Record.create([
						{
							name: 'pagina_id'
							,type: 'string'
						},{
							name: 'fuente'
						},{
							name: 'codigo'
						},{
							name: 'descripcion'
							,type: 'string'
						}]);
						var newRecord = new Video({
							pagina_id:viewPort.getComponent('center').activeTab.newId
							,fuente: 0
							,codigo: ''
							,descripcion: ''
						});
						videoEditor.stopEditing();
						videoGrid.getStore().insert(0, newRecord);
						videoGrid.getView().refresh();
						videoGrid.getSelectionModel().selectRow(0);
						videoEditor.startEditing(0);
						videoEditor.agregando=true;
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar video'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						videoEditor.startEditing(videoGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar video'
					,disabled: true
					,handler: function(){
						videoEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el video?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = videoGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginasvideos/borrar') ?>'
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
												videoEditor.grid.getStore().remove(row);
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
											Ext.Msg.alert('Error!', 'El servidor tuvo una respuesta nula');
										}
									}
								});
							}
						})
					}
				}]
			});
			videoGrid.getSelectionModel().on('selectionchange', function(sm){
        		videoGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				videoGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var videoMenuContextual = new Ext.menu.Menu({
				items: [
				{
					text: 'Modificar video'
					,handler:videoGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-modificar'
				},
				'-'
				,{
					text: 'Borrar video'
					,handler:videoGrid.removeBtn.handler
					,iconCls: 'x-menu-item-borrar'
				}]
			});
			
			videoGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				videoMenuContextual.showAt(event.getXY());
				
			});