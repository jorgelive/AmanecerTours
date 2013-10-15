			var multipleEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : !record.data.id ? '<?php echo $html->url('/paginasmultiples/agregar/') ?>'+viewPort.getComponent('center').activeTab.newId : '<?php echo $html->url('/paginasmultiples/modificar/') ?>'
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
											if(!multipleEditor.record.data.id){
												multipleEditor.record.data.id=obj.data.newId;
												multipleEditor.record.id=obj.data.newId;
											}
											editarAccion('generales');
										}
										multipleEditor.grid.getStore().commitChanges();
										multipleEditor.grid.getView().refresh();
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
										multipleEditor.grid.getStore().removeAt(rowIndex);
									}else{
										multipleEditor.grid.getStore().rejectChanges()
									}
									multipleEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			var multipleStore=new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,root: 'multiples'
				,fields: [{
					name: 'id'
				},{
					name: 'title'
				},{
					name: 'contenido'
				},{
					name: 'orden'
				},{
					name: 'idioma'
				}]
				,remoteSort:true
				,sortInfo: {
					field: 'orden'
					,direction: 'ASC'
				}
				,listeners:{
					'beforeload': function(store, options) {
						if (!options.params.start&&!options.params.limit){
							if(multiplePanel.paginatorStart&&multiplePanel.paginatorLimit){
								options.params.start=multiplePanel.paginatorStart;
								options.params.limit=multiplePanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						multiplePanel.paginatorStart=options.params.start;
						multiplePanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			
			var multipleGrid = new Ext.grid.GridPanel({
				store: multipleStore
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [new Ext.grid.RowNumberer()
				,{
					header: "Id"
					,dataIndex: 'id'
					,width: 20
					,hidden: true
				},{
					header: "Título"
					,dataIndex: 'title'
					,width: 120
					,editor: {
						allowBlank:false
						,blankText:'Ingrese el título'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasmultiples/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				},{
					header: "Contenido"
					,id:'contenido'
					,dataIndex: 'contenido'
					,width: 200
					,editor: {
						xtype: 'tinymce'
						,allowBlank: false
						,tagMatters: true
						,blankText:'Ingrese el contenido'
						,minLength : 20
						,maxLength : 20000
						,iHeight:200
						,iWidth:200
						,msgTarget: 'under'
						,labelSeparator: ''
						,tinymceSettings: {
							theme: "advanced"
							,plugins: "paste,nonbreaking,insertdatetime"
							,theme_advanced_resizing : true
							,theme_advanced_buttons1: "bold,italic,underline,|,cut,copy,paste,pasteword"
							,theme_advanced_buttons2: "insertdate,inserttime,|,bullist,numlist,|,cleanup,code"
							,theme_advanced_buttons3:""
							,theme_advanced_toolbar_location: "bottom"
							,theme_advanced_toolbar_align: "left"
							,theme_advanced_statusbar : false
							,extended_valid_elements: "a[name],hr[class|width|size|noshade]"
							,content_css: "/css/tinyMCE_content.css"
							,accessibility_focus: false
							,accessibility_warnings : false
						}
						,listeners:{
							'editorcreated':function(){
								multipleEditor.verifyLayout.defer(100,multipleEditor);
								this.onResize(multipleGrid.getColumnModel().getColumnWidth(4),200)
							}
						}
					}
					,sortable:true
					,filter:true
				},{
                    header: "Orden"
                    ,dataIndex: 'orden'
                    ,width: 60
                    ,editor: {
                        allowBlank:false
                        ,blankText:'Ingrese el el orden del texto'
                        ,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasmultiples/validar/') ?>'})]
                    }
                    ,sortable:true
                    ,filter:true
                }]
				,plugins: [multipleEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'contenido'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: multipleStore
					,displayMsg: 'Mostrando textos {0} - {1} de {2}'
					,emptyMsg: "No hay textos para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar texto'
					,handler: function(){
						var Multiple = Ext.data.Record.create([
						{
							name: 'pagina_id'
							,type: 'string'
						},{
							name: 'title'
							,type: 'string'
						},{
							name: 'contenido'
							,type: 'string'
						},{
							name: 'resumen'
							,type: 'string'
						}]);
						var newRecord = new Multiple({
							pagina_id:viewPort.getComponent('center').activeTab.newId
							,title: ''
							,contenido: ''
							,resumen: ''
						});
						multipleEditor.stopEditing();
						multipleGrid.getStore().insert(0, newRecord);
						multipleGrid.getView().refresh();
						multipleGrid.getSelectionModel().selectRow(0);
						multipleEditor.startEditing(0);
						multipleEditor.agregando=true;
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar texto multiple'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						multipleEditor.startEditing(multipleGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar texto multiple'
					,disabled: true
					,handler: function(){
						multipleEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el texto?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = multipleGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginasmultiples/borrar') ?>'
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
												multipleEditor.grid.getStore().remove(row);
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
			multipleGrid.getSelectionModel().on('selectionchange', function(sm){
        		multipleGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				multipleGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var multipleMenuContextual = new Ext.menu.Menu({
				items: [{
					text: 'Modificar texto'
					,handler:multipleGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-modificar'
				},
				'-'
				,{
					text: 'Borrar texto'
					,handler:multipleGrid.removeBtn.handler
					,iconCls: 'x-menu-item-borrar'
				}]
			});
			
			multipleGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				multipleMenuContextual.showAt(event.getXY());
			});