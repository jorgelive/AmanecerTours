<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra');?>
	var cabeceraEditor = new Ext.ux.grid.RowEditor({
		permisos:true
		,listeners: {
			afteredit: {
				fn:function(roweditor, changes, record, rowIndex ){
					Ext.Ajax.request({
						url   : '<?php echo $html->url('/paginascabeceras/modificar/') ?>'
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
								cabeceraEditor.grid.getStore().commitChanges();
								cabeceraGrid.getStore().reload();
								cabeceraEditor.grid.getView().refresh();
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
							cabeceraEditor.grid.getStore().rejectChanges();
							cabeceraEditor.grid.getView().refresh();
						}
					});
				}
			}
		}
	});
	
	var cabeceraGrid = new Ext.grid.GridPanel({
		store: new Ext.data.JsonStore({
			autoLoad: false
			,proxy: new Ext.data.HttpProxy({
				url: '<?php echo $html->url('/paginascabeceras/listar/') ?>'
				,method: 'POST'
			})
			,root: 'cabeceras'
			,fields: [{
				name: 'id'
			},{
				name: 'imagen'
			},{
				name: 'title'
			},{
				name: 'texto'
			},{
				name: 'tiempo'
			},{
				name: 'externo'
				,type: 'boolean'
			},{
				name: 'url'
			},{
				name: 'idioma'
			},{
				name: 'permiso'
			}]
		})
		,loadMask: {msg:'Cargando Datos...'}
		,tbar: [{
			ref: '../agregarBtn'
			,iconCls: 'x-boton-agregar'
			,text: 'Agregar cabecera'
			,disabled: true
			,handler: function(){
				var agregarCabeceraForm = new Ext.FormPanel({ 
					fileUpload: true
					,labelWidth:70
					,url:'<?php echo $html->url('/paginascabeceras/agregar/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						fieldLabel:'Título'
						,name:'Paginascabecera.title'
						,width:300
						,allowBlank:true
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascabeceras/validar/') ?>'})]
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen (opcional)'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginascabecera.imagen'
						,width:300
						,allowBlank:true
					},{ 
						xtype: 'tinymce'
						,allowBlank: true
						,tagMatters: true
						,fieldLabel:'Texto'
						,name:'Paginascabecera.texto'
						,maxLength : 2000
						,iHeight:200
						,iWidth:300
						,msgTarget: 'under'
						,labelSeparator: ''
						,tinymceSettings: {
							theme: "advanced"
							,plugins: "paste,nonbreaking,insertdatetime"
							,theme_advanced_resizing : true
							,theme_advanced_buttons1: "bold,italic,underline,|,cut,copy,paste,pasteword"
							,theme_advanced_buttons2: "insertdate,inserttime,|,bullist,numlist,|,cleanup,code"
							,theme_advanced_buttons3:""
							,theme_advanced_toolbar_location: "top"
							,theme_advanced_toolbar_align: "left"
							,theme_advanced_statusbar : false
							,extended_valid_elements: "a[name],hr[class|width|size|noshade]"
							,content_css: "/css/tinyMCE_content.css"
							,accessibility_focus: false
							,accessibility_warnings : false
						}
					},{ 
						fieldLabel:'Dirección'
						,name:'Paginascabecera.url'
						,width:300
						,allowBlank:true
						,blankText:'Ingrese la dirección'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascabeceras/validar/') ?>'})]
					},new Ext.form.Checkbox({
						boxLabel:'Es externo?'
						,name:'Paginascabecera.externo'
						,inputValue:1
					}),{ 
						fieldLabel:'Tiempo'
						,name:'Paginascabecera.tiempo'
						,width:100
						,allowBlank:false
						,blankText:'Ingrese el tiempo de reproducción'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascabeceras/validar/') ?>'})]
					}]
					,buttons:[{ 
						text:'Enviar'
						,formBind: true
						,handler:function(){
							agregarCabeceraForm.getForm().submit({ 
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
									agregarCabeceraForm.getForm().reset();
									cabeceraGrid.getStore().reload();
									cabeceraGrid.getView().refresh();
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
				var agregarCabeceraWindow = new Ext.Window({
					autoScroll:true
					,title:'Agregar cabecera'
					,modal:true
					,width: 430
					,height: 500
					,items:[agregarCabeceraForm]
					,layout: 'fit'
				}).show();
				cabeceraEditor.stopEditing();
			}
		},{
			ref: '../modificarBtn'
			,text: 'Modificar cabecera'
			,iconCls: 'x-boton-modificar'
			,disabled: true
			,handler: function(){
				cabeceraEditor.startEditing(cabeceraGrid.getSelectionModel().getSelections()[0]);
			}
		},{
			ref: '../modificarImagenBtn'
			,iconCls: 'x-boton-cambiarimagen'
			,text: 'Cambiar imagen'
			,disabled: true
			,handler: function(){
				var modificarImagenForm = new Ext.FormPanel({ 
					fileUpload: true
					,labelWidth:70
					,url:'<?php echo $html->url('/paginascabeceras/modificarimagen/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						xtype:'hidden'
						,name:'Paginascabecera.id'
						,value: cabeceraGrid.getSelectionModel().selections.keys
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginascabecera.imagen'
						,width:300
						,allowBlank:false
						,blankText:'Seleccione una imagen'
					}]
					,buttons:[{ 
						text:'Enviar'
						,formBind: true
						,handler:function(){
							 modificarImagenForm.getForm().submit({ 
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
									modificarImagenForm.getForm().reset();
									cabeceraGrid.getStore().reload();
									cabeceraGrid.getView().refresh();
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
				var modificarImagenWindow = new Ext.Window({
					autoScroll:true
					,title:'Cambiar imagen'
					,modal:true
					,width: 430
					,height: 120
					,items:[modificarImagenForm]
					,layout: 'fit'
				}).show();
				cabeceraEditor.stopEditing();
			}
		},{
			ref: '../removeBtn'
			,iconCls: 'x-boton-borrar'
			,text: 'Borrar cabecera'
			,disabled: true
			,handler: function(){
				cabeceraEditor.stopEditing();
				Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar la cabecera?',function(btn) {
					if (btn == 'yes') {
						var selectedRows = cabeceraGrid.getSelectionModel().getSelections();
						Ext.Ajax.request({
							url   : '<?php echo $html->url('/paginascabeceras/borrar') ?>'
							,method: 'POST'
							,params: {id:cabeceraGrid.getSelectionModel().selections.keys}
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
										cabeceraEditor.grid.getStore().remove(row);
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
								cabeceraEditor.grid.getStore().rejectChanges();
								cabeceraEditor.grid.getView().refresh();
							}
						});
					}
				})
			}
		},{
			ref: '../permisosBtn'
			,iconCls: 'x-boton-permisos'
			,text: 'Modificar permisos'
			,disabled: true
			,handler: function(){
				if(cabeceraGrid.getSelectionModel().getCount() == 1){
					var seleccion=cabeceraGrid.getSelectionModel().selections.keys;
				}else
				if(cabeceraGrid.getSelectionModel().getCount() == 0){
					var seleccion='root';
				}
				var permisosEditor = new Ext.ux.grid.RowEditor({
					listeners: {
						afteredit: {
							fn:function(roweditor, changes, record, rowIndex ){
								Ext.Ajax.request({
									url   : !record.data.id ? '<?php echo $html->url('/aros_acos/agregarpermisos') ?>' : '<?php echo $html->url('/aros_acos/modificarpermisos') ?>'
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
												if(!permisosEditor.record.data.id){
													permisosEditor.record.data.id=obj.data.newId;
													permisosEditor.record.id=obj.data.newId;
												}
											}
											permisosEditor.grid.getStore().commitChanges();
											permisosEditor.grid.getView().refresh();
	
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
										if(!permisosEditor.record.data.id){
											permisosEditor.grid.getStore().removeAt(rowIndex);
											permisosEditor.grid.getView().refresh();
										}else{
											permisosEditor.grid.getStore().rejectChanges();
											permisosEditor.grid.getView().refresh();
										}
									}
								});
							}
						}
					}
				});
				
				var permisosGrid = new Ext.grid.GridPanel({
					store: new Ext.data.JsonStore({
						autoLoad: false
						,proxy: new Ext.data.HttpProxy({
							method: 'POST'
							,url: '<?php echo $html->url('/acos/permisosxmodel/') ?>'
						})
						,root: 'node'
						,fields: [{
							name: 'caller'
						},{
							name: 'foreign_key'
						},{
							name: 'id'
						},{
							name: 'aro_id'
						},{
							name: '_read'
							,type: 'boolean'
						},{
							name: '_create'
							,type: 'boolean'
						},{
							name: '_update'
							,type: 'boolean'
	
						},{
							name: '_delete'
							,type: 'boolean'
						},{
							name: '_grant'
							,type: 'boolean'
						}]
					})
					,loadMask: {msg:'Cargando Datos...'} 
					,tbar: [{
						iconCls: 'x-boton-agregarpermiso'
						,text: 'Agregar permiso'
						,handler: function(){
							var Permiso = Ext.data.Record.create([
							{
								name: 'caller'
								,type: 'string'
							},{
								name: 'foreign_key'
								,type: 'string'
							},{
								name: 'aro_id'
								,type: 'string'
							},{
								name: '_read'
								,type: 'boolean'
							},{
								name: '_create'
								,type: 'boolean'
							},{
								name: '_update'
								,type: 'boolean'
							},{
								name: '_delete'
								,type: 'boolean'
							},{
								name: '_grant'
								,type: 'boolean'
							}]);
							var e = new Permiso({
								caller: 'Paginascabecera'
								,foreign_key: seleccion
								,aro_id: ''
								,_read: false
								,_create: false
								,_update: false
								,_delete: false
							});
							permisosEditor.stopEditing();
							permisosGrid.getStore().insert(0, e);
							permisosGrid.getView().refresh();
							permisosGrid.getSelectionModel().selectRow(0);
							permisosEditor.startEditing(0);
							permisosEditor.agregando=true;
						}
					},{
						ref: '../modificarBtn'
						,text: 'Modificar permiso'
						,iconCls: 'x-boton-modificarpermiso'
						,disabled: true
						,handler: function(){
							permisosEditor.startEditing(permisosGrid.getSelectionModel().getSelections()[0]);
						}
					},{
						ref: '../removeBtn'
						,iconCls: 'x-boton-borrarpermiso'
						,text: 'Borrar permiso'
						,disabled: true
						,handler: function(){
							permisosEditor.stopEditing();
							Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el permiso?',function(btn) {
								if (btn == 'yes') {
									var selectedRows = permisosGrid.getSelectionModel().getSelections();
									var str = [];
									for(var i = 0, row; row = selectedRows[i]; i++){
										str.push('row'+i+':'+ selectedRows[i].id);
									}
									str.push('caller:\'Paginascabecera\'');
									str.push('foreign_key:\''+ seleccion+'\'');
									var string = '{'+str.join(',')+'}';
									var rowIds = eval('('+string+')');
									Ext.Ajax.request({
										url   : '<?php echo $html->url('/aros_acos/borrarpermisos') ?>'
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
													permisosGrid.getStore().remove(row);
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
											permisosEditor.grid.getStore().rejectChanges();
											permisosEditor.grid.getView().refresh();
										}
									});
								}
							})
						}
					}]
					,columns: [new Ext.grid.RowNumberer()
					,{
						header: "Id"
						,dataIndex: 'id'
						,width: 20
						,hidden: true
					},{
						header: "Nombre"
						,id:'aco_id'
						,width: 200
						,sortable: true
						,dataIndex: 'aro_id'
						,editor: new Ext.form.ComboBox({
							fieldLabel: 'Nombre del operador'
							,hiddenName: 'Acluser.operador_id'
							,width:190
							,store: new Ext.data.JsonStore({
								autoLoad: true
								,url: '<?php echo $html->url('/aros/treelist/') ?>'
								,root: 'Aro'
								,fields: ['id','alias']
								,listeners:{'load':function(){
										permisosGrid.getColumnModel().getColumnById('aco_id').renderer=Ext.util.Format.comboRenderer(permisosGrid.getColumnModel().getColumnById('aco_id').getEditor());
										permisosGrid.getView().refresh();
									}
								}
							})
							,displayField: 'alias'
							,valueField: 'id'
							,typeAhead: true
							,mode: 'local'
							,triggerAction: 'all'
							,emptyText: 'Seleccione'
							,selectOnFocus:true
							,allowBlank: false
							,blankText:'Ingrese un controlador'
							,valueNotFoundText: ''
						})
					},{
						header: "Lectura"
						,dataIndex: '_read'
						,align:'center'
						,width: 80
						,xtype: 'booleancolumn'
						,trueText: 'Si'
						,falseText: 'No'
						,editor: {xtype: 'checkbox'}
					},{
						header: "Creación"
						,dataIndex: '_create'
						,align:'center'
						,width: 80
						,xtype: 'booleancolumn'
						,trueText: 'Si'
						,falseText: 'No'
						,editor: {xtype: 'checkbox'}
					},{
						header: "Edición"
						,dataIndex: '_update'
						,align:'center'
						,width: 80
						,xtype: 'booleancolumn'
						,trueText: 'Si'
						,falseText: 'No'
						,editor: {xtype: 'checkbox'}
					},{
						header: "Borrado"
						,dataIndex: '_delete'
						,align:'center'
						,width: 80
						,xtype: 'booleancolumn'
						,trueText: 'Si'
						,falseText: 'No'
						,editor: {xtype: 'checkbox'}
					},{
						header: "Permisos"
						,dataIndex: '_grant'
						,align:'center'
						,width: 80
						,xtype: 'booleancolumn'
						,trueText: 'Si'
						,falseText: 'No'
						,editor: {xtype: 'checkbox'}
					}]
					,plugins: [permisosEditor]
					,stripeRows: true
					,autoExpandColumn: 'aco_id'
				});
				
				var permisosMenuContextual = new Ext.menu.Menu({
					items: [
					{
						text: 'Modificar permiso'
						,handler:permisosGrid.modificarBtn.handler
						,iconCls: 'x-menu-item-modificarpermiso'
					},'-',{
						text: 'Borrar permiso'
						,handler:permisosGrid.removeBtn.handler
						,iconCls: 'x-menu-item-borrarpermiso'
					}]
				});
				
				permisosGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
					grid.getSelectionModel().selectRow(rowIndex);
					event.stopEvent();
					permisosMenuContextual.showAt(event.getXY());
					
				});
				
				var permisosWindow = new Ext.Window({
					autoScroll:true
					,title:'Permisos'
					,modal:true
					,width: 600
					,height: 400
					,items:[permisosGrid]
					,layout: 'fit'
				}).show();
				permisosGrid.getStore().load({params:{caller:'Paginascabecera',foreign_key:seleccion}});
				permisosGrid.getSelectionModel().on('selectionchange', function(sm){
					permisosGrid.removeBtn.setDisabled(sm.getCount() < 1);
					permisosGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				});
				
			}
		}]
		,columns: [new Ext.grid.RowNumberer()
		,{
			header: "Id"
			,dataIndex: 'id'
			,width: 20
			,hidden: true
		},{
			header: "Imagen"
			,width: 200
			,dataIndex: 'imagen'
			,align:'center'
			,editor: {disabled: true}
			,renderer:Ext.util.Format.imageRenderer(120,true)
		},{
			header: "Titulo"
			,dataIndex: 'title'
			,width: 200
			,editor: {
				allowBlank:true
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascabeceras/validar/') ?>'})]	
			}
		},{
			header: "Texto"
			,dataIndex: 'texto'
			,width: 300
			,editor: {
				xtype: 'tinymce'
				,allowBlank: true
				,tagMatters: true
				,maxLength : 2000
				,iHeight:200
				,iWidth:300
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
						cabeceraEditor.verifyLayout.defer(100,cabeceraEditor);
						this.onResize(cabeceraGrid.getColumnModel().getColumnWidth(4),300)
					}
				}
			}
		},{
			header: "Direccion"
			,id:'url'
			,dataIndex: 'url'
			,width: 250
			,editor: {
				allowBlank:true
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascabeceras/validar/') ?>'})]
			}
		},{
			header: "Externa"
			,dataIndex: 'externo'
			,align:'center'
			,width: 100
			,xtype: 'booleancolumn'
			,trueText: 'Si'
			,falseText: 'No'
			,editor: {xtype: 'checkbox'}
		},{
			header: "Tiempo"
			,dataIndex: 'tiempo'
			,width: 80
			,editor: {
				allowBlank:false
				,blankText:'Ingrese la el tiempo'
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascabeceras/validar/') ?>'})]
			}
		}]
		,plugins: [cabeceraEditor,new Ext.ux.dd.GridDragDropRowOrder({
			scrollable: true // enable scrolling support (default is false)
			,listeners: {
				'afterrowmove':{
					fn: function(ddGrid,oldPosition,position) {
						var selectedRows = ddGrid.grid.getSelectionModel().getSelections();
						var str = [];
						for(var i = 0, row; row = selectedRows[i]; i++){
							str.push('row'+i+':'+ selectedRows[i].id);
						}
						var string = '{'+str.join(',')+'}';
						var rowIds = eval('('+string+')');
						var url = '<?php echo $html->url('/paginascabeceras/reorder/') ?>';
						var params = {'nodes':Ext.util.JSON.encode(rowIds), 'delta':(position-oldPosition)};
						
						Ext.Ajax.request({
							url:url
							,params:params
							,success:function(respuesta,request) {
								obj = Ext.util.JSON.decode(respuesta.responseText);
								if (obj.success){
									if(obj.hasOwnProperty('message')){
										Ext.Msg.alert('Correcto!', obj.message);
									}
									if(obj.hasOwnProperty('redirect')){
										window.location = obj.redirect;
									}
									ddGrid.grid.getStore().commitChanges();
									ddGrid.grid.getView().refresh();
								}else{
									request.failure();
								}
							}
							,failure:function(){
								ddGrid.grid.getStore().rejectChanges();
								ddGrid.grid.getView().refresh();
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
					}
				}
			}
		})]
		,ddText: '{0} fila{1} seleccionada{1}'
		,stripeRows: true
		,autoExpandColumn: 'url'
		,enableDD:true
		,errorDD:'No tiene permisos para mover la cabecera'
	});
	cabeceraGrid.getSelectionModel().singleSelect=true;
	cabeceraGrid.getSelectionModel().on('selectionchange', function(sm){
		cabeceraGrid.permisosBtn.setDisabled(1);
		cabeceraGrid.removeBtn.setDisabled(1);
		cabeceraGrid.modificarBtn.setDisabled(1);
		cabeceraGrid.modificarImagenBtn.setDisabled(1);
		cabeceraGrid.permisosBtn.setDisabled(1);
		if(sm.getCount()==0){
			if(rootPermisos.data.items[0].data['grant']){
				cabeceraGrid.permisosBtn.setDisabled(0);
			}
		}else
		if(sm.getCount()==1){
			if(cabeceraGrid.getStore().data.map[sm.selections.keys].data.permiso['delete']===true){
				cabeceraGrid.removeBtn.setDisabled(0);
			}
			if(cabeceraGrid.getStore().data.map[sm.selections.keys].data.permiso['update']===true){
				cabeceraGrid.modificarBtn.setDisabled(0);
				cabeceraGrid.modificarImagenBtn.setDisabled(0);
			}
			if(cabeceraGrid.getStore().data.map[sm.selections.keys].data.permiso['grant']===true){
				cabeceraGrid.permisosBtn.setDisabled(0);
			}
		}
	});
	
	var cabeceraMenuContextual = new Ext.menu.Menu({
		items: [{
			text: 'Modificar testimonio'
			,handler:cabeceraGrid.modificarBtn.handler
			,iconCls:'x-menu-item-modificar'
		},{
			text: 'Cambiar imagen'
			,handler:cabeceraGrid.modificarImagenBtn.handler
			,iconCls:'x-menu-item-cambiarimagen'
		},{
			text: 'Modificar permisos'
			,handler:cabeceraGrid.permisosBtn.handler
			,iconCls:'x-menu-item-permisos'
		},'-',{
			text: 'Borrar cabecera'
			,handler:cabeceraGrid.removeBtn.handler
			,iconCls:'x-menu-item-borrar'
		}]
	});
	
	cabeceraGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
		grid.getSelectionModel().selectRow(rowIndex);
		event.stopEvent();
		cabeceraMenuContextual.items.each(function(item){
			item.disable();
		})
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['update']===true){
			cabeceraMenuContextual.items.items[0].enable();
			cabeceraMenuContextual.items.items[1].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['grant']===true){
			cabeceraMenuContextual.items.items[2].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['delete']===true){
			cabeceraMenuContextual.items.items[4].enable();
		}
		cabeceraMenuContextual.showAt(event.getXY());
	});
	
	var cabeceraPanel = new Ext.Panel({
		title: 'Cabeceras'
		,items:[]
		,layout:'fit'
		,defaults:{autoScroll:true}
		<?php
			$strings=array();
			foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
				$strings[] = '{id:\''.$key.'\'
					,handler: function(){
						cabeceraPanel.currentIdioma=\''.$key.'\'
						cabeceraGrid.getStore().load({params:{idioma: cabeceraPanel.currentIdioma}});
						if(cabeceraPanel.currentIdioma != \''.Configure::read('Empresa.language').'\'){
							cabeceraGrid.getColumnModel().config[3].editor.allowBlank=true;
							cabeceraGrid.agregarBtn.disable();
						}else{
							cabeceraGrid.agregarBtn.enable();
						}
					}
				}';
			}
		?>
		,tools: [<?php echo implode(',',$strings);?>]
		
	});
	
	var rootPermisos= new Ext.data.JsonStore({
		autoLoad: false
		,proxy: new Ext.data.HttpProxy({
			method: 'POST'
			,url: '<?php echo $html->url('/acos/permisosxroot/') ?>'
		})
		,root: 'permiso'
		,fields: [{
			name: 'read',
			type: 'boolean'
		},{
			name: 'create',
			type: 'boolean'
		},{
			name: 'update',
			type: 'boolean'

		},{
			name: 'delete',
			type: 'boolean'
		},{
			name: 'grant',
			type: 'boolean'
		}]
		,listeners:{
			load:function(){
				
				if(rootPermisos.data.items[0].data['read']){
					cabeceraPanel.add(cabeceraGrid)
					cabeceraPanel.doLayout();
					cabeceraPanel.currentIdioma='<?php echo Configure::read('Empresa.language');?>'
					cabeceraGrid.getStore().load({params:{idioma: cabeceraPanel.currentIdioma}});
				}
				if(rootPermisos.data.items[0].data['create']){
					cabeceraGrid.agregarBtn.setDisabled(0);
				}
				if(!rootPermisos.data.items[0].data['update']){
					cabeceraGrid.enableDD=false;
				}
				if(rootPermisos.data.items[0].data['grant']){
					cabeceraGrid.permisosBtn.setDisabled(0);
				}
			}
		}
	})
	rootPermisos.load({params:{caller:'Paginascabecera'}});
	
	<?php
	
	$items=array(
		'north'=>array('title'=>$title_for_layout.' - '.Configure::read('Empresa.nombre'),'items'=>array('barra'))
		,'center'=>array('layout'=>'fit','items'=>array('cabeceraPanel'))
	);
	$ext->viewportTypes=array('north'=>'Panel','south'=>'Panel','west'=>'Panel','east'=>'Panel','center'=>'Panel');
	echo $ext->viewport($items);
	

	?>
});
</script>