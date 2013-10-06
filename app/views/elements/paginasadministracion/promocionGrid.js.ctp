			var promocionEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : !record.data.id ? '<?php echo $html->url('/paginaspromociones/agregar/') ?>'+viewPort.getComponent('center').activeTab.newId : '<?php echo $html->url('/paginaspromociones/modificar/') ?>'
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
											if(!promocionEditor.record.data.id){
												promocionEditor.record.data.id=obj.data.newId;
												promocionEditor.record.id=obj.data.newId;
											}
											editarAccion('generales');
										}
										promocionEditor.grid.getStore().commitChanges();
										promocionEditor.grid.getView().refresh();
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
										promocionEditor.grid.getStore().removeAt(rowIndex);
									}else{
										promocionEditor.grid.getStore().rejectChanges()
									}
									promocionEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			var promocionStore=new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,root: 'promociones'
				,fields: [{
					name: 'id'
				},{
					name: 'inicio'
					,type: 'date'
					,dateFormat: 'Y-m-d'
				},{
					name: 'final'
					,type: 'date'
					,dateFormat: 'Y-m-d'
				},{
					name: 'title'
				},{
					name: 'notas'
				},{
					name: 'condiciones'
				},{
					name: 'precio'
				},{
					name: 'idioma'
				}]
				,remoteSort:true
				,sortInfo: {
					field: 'inicio'
					,direction: 'DESC'
				}
				,listeners:{
					'beforeload': function(store, options) {
						if (!options.params.start&&!options.params.limit){
							if(promocionPanel.paginatorStart&&promocionPanel.paginatorLimit){
								options.params.start=promocionPanel.paginatorStart;
								options.params.limit=promocionPanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						promocionPanel.paginatorStart=options.params.start;
						promocionPanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			
			var promocionGrid = new Ext.grid.GridPanel({
				store: promocionStore
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
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginaspromociones/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				},{
					header: "Inicio"
					,renderer: Ext.util.Format.dateRenderer('d-m-Y')
					,width: 90
					,sortable: true
					,dataIndex: 'inicio'
					,align:'center'
					,editor: {
						xtype: 'datefield'
						,itemId: 'inicioField'
						,format:'d-m-Y'
						,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
						,listeners:{
							'select':function(field){
								var date = field.parseDate(field.value);
								var end = field.ownerCt.getComponent('finalField');
								end.setMinValue(date);
								end.validate();
								end.dateRangeMin = date;
							}
						}
					}
					,sortable:true
					,filter:true
				},{
					header: "Final"
					,renderer: Ext.util.Format.dateRenderer('d-m-Y')
					,width: 90
					,sortable: true
					,dataIndex: 'final'
					,align:'center'
					,editor: {
						xtype: 'datefield'
						,itemId: 'finalField'
						,format:'d-m-Y'
						,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
						,listeners:{
							'select':function(field){
								var date = field.parseDate(field.value);
								var start = field.ownerCt.getComponent('inicioField');
								start.setMaxValue(date);
								start.validate();
								start.dateRangeMax = date;
							}
						}
					}
					,sortable:true
					,filter:true
				},{
					header: "Notas"
					,id:'notas'
					,dataIndex: 'notas'
					,width: 200
					,editor: {
						xtype: 'tinymce'
						,allowBlank: false
						,tagMatters: true
						,blankText:'Ingrese las notas'
						,minLength : 20
						,maxLength : 500
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
								promocionEditor.verifyLayout.defer(100,promocionEditor);
								this.onResize(promocionGrid.getColumnModel().getColumnWidth(4),200)
							}
						}
					}
					,sortable:true
					,filter:true
				},{
					header: "Condiciones"
					,dataIndex: 'condiciones'
					,width: 230
					,editor: {
						allowBlank:false
						,xtype: 'tinymce'
						,allowBlank: false
						,tagMatters: true
						,blankText:'Ingrese las condiciones'
						,minLength : 20
						,maxLength : 500
						,iHeight:200
						,iWidth:230
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
								promocionEditor.verifyLayout.defer(100,promocionEditor);
								this.onResize(promocionGrid.getColumnModel().getColumnWidth(5),200)
							}
						}
					}
					,sortable:true
					,filter:true
				},{
					header: "Precio"
					,dataIndex: 'precio'
					,width: 40
					,editor: {
						allowBlank:true
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginaspromociones/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				}]
				,plugins: [promocionEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'notas'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: promocionStore
					,displayMsg: 'Mostrando promociones {0} - {1} de {2}'
					,emptyMsg: "No hay promociones para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar promoción'
					,handler: function(){
						var Promocion = Ext.data.Record.create([
						{
							name: 'pagina_id'
							,type: 'string'
						},{
							name: 'title'
							,type: 'string'
						},{
							name: 'inicio'
							,type: 'date'
							,dateFormat: 'Y-m-d'
						},{
							name: 'final'
							,type: 'date'
							,dateFormat: 'Y-m-d'
						},{
							name: 'notas'
							,type: 'string'
						},{
							name: 'condiciones'
							,type: 'string'
						},{
							name: 'precio'
							,type: 'string'
						}]);
						var newRecord = new Promocion({
							pagina_id:viewPort.getComponent('center').activeTab.newId
							,title: ''
							,inicio: ''
							,final: ''
							,notas: ''
							,condiciones: ''
							,precio: ''
						});
						promocionEditor.stopEditing();
						promocionGrid.getStore().insert(0, newRecord);
						promocionGrid.getView().refresh();
						promocionGrid.getSelectionModel().selectRow(0);
						promocionEditor.startEditing(0);
						promocionEditor.agregando=true;
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar promoción'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						promocionEditor.startEditing(promocionGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar promoción'
					,disabled: true
					,handler: function(){
						promocionEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar la promoción?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = promocionGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginaspromociones/borrar') ?>'
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
												promocionEditor.grid.getStore().remove(row);
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
			promocionGrid.getSelectionModel().on('selectionchange', function(sm){
        		promocionGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				promocionGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var promocionMenuContextual = new Ext.menu.Menu({
				items: [{
					text: 'Modificar promoción'
					,handler:promocionGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-modificar'
				},
				'-'
				,{
					text: 'Borrar promoción'
					,handler:promocionGrid.removeBtn.handler
					,iconCls: 'x-menu-item-borrar'
				}]
			});
			
			promocionGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				promocionMenuContextual.showAt(event.getXY());
			});