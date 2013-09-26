<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra');?>
	var noticiaEditor = new Ext.ux.grid.RowEditor({
		permisos:true
		,listeners: {
			afteredit: {
				fn:function(roweditor, changes, record, rowIndex ){
					Ext.Ajax.request({
						url   : '<?php echo $html->url('/paginasnoticias/modificar/') ?>'
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
								noticiaEditor.grid.getStore().commitChanges();
								noticiaGrid.getStore().reload();
								noticiaEditor.grid.getView().refresh();
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
							noticiaEditor.grid.getStore().rejectChanges();
							noticiaEditor.grid.getView().refresh();
						}
					});
				}
			}
		}
	});
	
	
	var noticiaStore = new Ext.data.JsonStore({
		autoLoad: false
		,url: '<?php echo $html->url('/paginasnoticias/listar/') ?>'
		,root: 'noticias'
		,totalProperty: 'total'
		,fields: [{
			name: 'id'
		},{
			name: 'title'
		},{
			name: 'imagen'
		},{
			name: 'contenido'
		},{
			name: 'fecha'
			,type: 'date'
			,dateFormat: 'Y-m-d'
		},{
			name: 'borrar_imagen'
			,type: 'boolean'
		},{
			name: 'idioma'
		},{
			name: 'permiso'
		}]
		,remoteSort:true
		,sortInfo: {
        	field: 'fecha'
        	,direction: 'DESC'
    	}
		,listeners:{
			'beforeload': function(store, options) {
				if (!options.params.start&&!options.params.limit){
					if(noticiaPanel.paginatorStart&&noticiaPanel.paginatorLimit){
						options.params.start=noticiaPanel.paginatorStart;
						options.params.limit=noticiaPanel.paginatorLimit;
					}else{
						options.params.start=0;
						options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
					}
				}
				noticiaPanel.paginatorStart=options.params.start;
				noticiaPanel.paginatorLimit=options.params.limit;
				options.params.page = Math.floor(options.params.start / options.params.limit)+1;
				options.params.idioma = noticiaPanel.currentIdioma;
				return true;
			}
		}
	})
	
	var noticiaGrid = new Ext.grid.GridPanel({
		store: noticiaStore
		,loadMask: {msg:'Cargando Datos...'}
		,columns: [new Ext.grid.RowNumberer()
		,{
			header: "Id"
			,dataIndex: 'id'
			,width: 20
			,hidden: true
		},{
			header: "Título"
			,id:'title'
			,dataIndex: 'title'
			,width: 200
			,editor: {
				allowBlank:false
				,blankText:'Ingrese el título de la noticia'
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasnoticias/validar/') ?>'})]	
			}
			,sortable:true
			,filter: true
		},{
			header: "Imagen"
			,width: 125
			,dataIndex: 'imagen'
			,align:'center'
			,editor: {disabled: true}
			,renderer:Ext.util.Format.imageRenderer(120,true)
			
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
				,maxLength : 2000
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
						noticiaEditor.verifyLayout.defer(100,noticiaEditor);
						this.onResize(noticiaGrid.getColumnModel().getColumnWidth(4),200)
					}
				}
			}
			,sortable:true
			,filter: true
		},{
			header: "Fecha"
			,id:'fecha'
			,dataIndex: 'fecha'
			,renderer: Ext.util.Format.dateRenderer('d-m-Y')
			,width: 90
			,editor: {
				xtype: 'datefield'
				,fieldLabel: 'Fecha'
				,allowBlank:false
				,blankText:'Ingrese la fecha a mostrar'
				,format:'d-m-Y'
				,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
			}
			,sortable:true
			,filter: true
		},{
			header: "Borrar imagen"
			,dataIndex: 'borrar_imagen'
			,align:'center'
			,width: 90
			,xtype: 'booleancolumn'
			,trueText: 'Si'
			,falseText: 'No'
			,editor: {xtype: 'checkbox'}
		}]
		,plugins: [noticiaEditor,new Ext.ux.grid.FilterRow()]
		,ddText: '{0} fila{1} seleccionada{1}'
		,stripeRows: true
		,autoExpandColumn: 'contenido'
		,bbar: new Ext.PagingToolbar({
			pageSize: <?php echo Configure::read('Default.paginatorSize');?>
			,displayInfo: true
			,filter:true
			,store: noticiaStore
			,displayMsg: 'mostrando noticias {0} - {1} de {2}'
			,emptyMsg: "No hay noticias para mostrar"
		})
		,tbar: [{
			ref: '../agregarBtn'
			,iconCls: 'x-boton-agregar'
			,text: 'Agregar noticia'
			,disabled: true
			,handler: function(){
				var agregarNoticiaForm = new Ext.FormPanel({ 
					fileUpload: true
					,labelWidth:90
					,url:'<?php echo $html->url('/paginasnoticias/agregar/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						fieldLabel:'Título'
						,name:'Paginasnoticia.title'
						,width:300
						,allowBlank:false
						,blankText:'Ingrese el título de la noticia'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasnoticias/validar/') ?>'})]
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen (opcional)'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginasnoticia.imagen'
						,width:300
						,allowBlank:true
					},{ 
						xtype: 'tinymce'
						,allowBlank: false
						,tagMatters: true
						,blankText:'Ingrese el contenido'
						,fieldLabel:'Contenido'
						,name:'Paginasnoticia.contenido'
						,minLength : 20
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
						xtype: 'datefield'
						,fieldLabel: 'Fecha'
						,name:'Paginasnoticia.fecha'
						,emptyText:'Ingrese la fecha a mostrar'
						,width:300
						,allowBlank:false
						,blankText:'Ingrese la fecha a mostrar'
						,format:'d-m-Y'
						,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
					}]
					,buttons:[{ 
						text:'Enviar'
						,formBind: true
						,handler:function(){
							agregarNoticiaForm.getForm().submit({ 
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
									agregarNoticiaForm.getForm().reset();
									noticiaGrid.getStore().reload();
									noticiaGrid.getView().refresh();
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
				var agregarNoticiaWindow = new Ext.Window({
					autoScroll:true
					,title:'Agregar noticia'
					,modal:true
					,width: 440
					,height: 380
					,items:[agregarNoticiaForm]
					,layout: 'fit'
				}).show();
				noticiaEditor.stopEditing();
			}
		},{
			ref: '../modificarBtn'
			,text: 'Modificar noticia'
			,iconCls: 'x-boton-modificar'
			,disabled: true
			,handler: function(){
				noticiaEditor.startEditing(noticiaGrid.getSelectionModel().getSelections()[0]);
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
					,url:'<?php echo $html->url('/paginasnoticias/modificarimagen/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						xtype:'hidden'
						,name:'Paginasnoticia.id'
						,value: noticiaGrid.getSelectionModel().selections.keys
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginasnoticia.imagen'
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
									noticiaGrid.getStore().reload();
									noticiaGrid.getView().refresh();
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
				noticiaEditor.stopEditing();
			}
		},{
			ref: '../removeBtn'
			,iconCls: 'x-boton-borrar'
			,text: 'Borrar noticia'
			,disabled: true
			,handler: function(){
				noticiaEditor.stopEditing();
				Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar la noticia?',function(btn) {
					if (btn == 'yes') {
						var selectedRows = noticiaGrid.getSelectionModel().getSelections();
						Ext.Ajax.request({
							url   : '<?php echo $html->url('/paginasnoticias/borrar') ?>'
							,method: 'POST'
							,params: {id:noticiaGrid.getSelectionModel().selections.keys}
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
										noticiaEditor.grid.getStore().remove(row);
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
								noticiaEditor.grid.getStore().rejectChanges();
								noticiaEditor.grid.getView().refresh();
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
				if(noticiaGrid.getSelectionModel().getCount() == 1){
					var seleccion=noticiaGrid.getSelectionModel().selections.keys;
				}else
				if(noticiaGrid.getSelectionModel().getCount() == 0){
					var seleccion='root';
				}
				var permisosEditor = new Ext.ux.grid.RowEditor({
					listeners: {
						afteredit: {
							fn:function(roweditor, changes, record, rowIndex ){
								Ext.Ajax.request({
									url   : !record.data.id ? '<?php echo $html->url('/aros_acos/agregarpermisos') ?>' : '<?php echo $html->url('/aros_acos/modificarpermisos/') ?>'
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
								name: 'aro_id',
								type: 'string'
							},{
								name: '_read',
								type: 'boolean'
							},{
								name: '_create',
								type: 'boolean'
							},{
								name: '_update',
								type: 'boolean'
							},{
								name: '_delete',
								type: 'boolean'
							},{
								name: '_grant',
								type: 'boolean'
							}]);
							var e = new Permiso({
								caller: 'Paginasnoticia'
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
									str.push('caller:\'Paginasnoticia\'');
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
					items: [{
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
				permisosGrid.getStore().load({params:{caller:'Paginasnoticia',foreign_key:seleccion}});
				permisosGrid.getSelectionModel().on('selectionchange', function(sm){
					permisosGrid.removeBtn.setDisabled(sm.getCount() < 1);
					permisosGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				});
				
			}
		}]
	});
	noticiaGrid.getSelectionModel().singleSelect=true;
	noticiaGrid.getSelectionModel().on('selectionchange', function(sm){
		noticiaGrid.permisosBtn.setDisabled(1);
		noticiaGrid.removeBtn.setDisabled(1);
		noticiaGrid.modificarBtn.setDisabled(1);
		noticiaGrid.modificarImagenBtn.setDisabled(1);
		noticiaGrid.permisosBtn.setDisabled(1);
		if(sm.getCount()==0){
			if(rootPermisos.data.items[0].data['grant']){
				noticiaGrid.permisosBtn.setDisabled(0);
			}
		}else
		if(sm.getCount()==1){
			if(noticiaGrid.getStore().data.map[sm.selections.keys].data.permiso['delete']===true){
				noticiaGrid.removeBtn.setDisabled(0);
			}
			if(noticiaGrid.getStore().data.map[sm.selections.keys].data.permiso['update']===true){
				noticiaGrid.modificarImagenBtn.setDisabled(0);
				noticiaGrid.modificarBtn.setDisabled(0);
			}
			if(noticiaGrid.getStore().data.map[sm.selections.keys].data.permiso['grant']===true){
				noticiaGrid.permisosBtn.setDisabled(0);
			}
		}
	});
	
	var noticiaMenuContextual = new Ext.menu.Menu({
		items: [{
			text: 'Modificar noticia'
			,handler:noticiaGrid.modificarBtn.handler
			,iconCls:'x-menu-item-modificar'
		},{
			text: 'Cambiar imagen'
			,handler:noticiaGrid.modificarImagenBtn.handler
			,iconCls:'x-menu-item-cambiarimagen'
		},{
			text: 'Modificar permisos'
			,handler:noticiaGrid.permisosBtn.handler
			,iconCls:'x-menu-item-permisos'
		},'-',{
			text: 'Borrar noticia'
			,handler:noticiaGrid.removeBtn.handler
			,iconCls:'x-menu-item-borrar'
		}]
	});
	
	noticiaGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
		grid.getSelectionModel().selectRow(rowIndex);
		event.stopEvent();
		noticiaMenuContextual.items.each(function(item){
			item.disable();
		})
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['update']===true){
			noticiaMenuContextual.items.items[0].enable();
			noticiaMenuContextual.items.items[1].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['grant']===true){
			noticiaMenuContextual.items.items[2].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['delete']===true){
			noticiaMenuContextual.items.items[4].enable();
		}
		noticiaMenuContextual.showAt(event.getXY());
	});
	
	var noticiaPanel = new Ext.Panel({
		title: 'Noticias'
		,items:[]
		,layout:'fit'
		,defaults:{autoScroll:true}
		<?php
			$strings=array();
			foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
				$strings[] = '{id:\''.$key.'\'
					,handler: function(){
						noticiaPanel.currentIdioma=\''.$key.'\'
						noticiaGrid.getStore().load({params:{idioma: noticiaPanel.currentIdioma}});
						if(noticiaPanel.currentIdioma != \''.Configure::read('Empresa.language').'\'){
							noticiaGrid.getColumnModel().config[3].editor.allowBlank=true;
							noticiaGrid.agregarBtn.disable();
						}else{
							noticiaGrid.agregarBtn.enable();
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
					noticiaPanel.add(noticiaGrid)
					noticiaPanel.doLayout();
					noticiaPanel.currentIdioma='<?php echo Configure::read('Empresa.language');?>'
					noticiaGrid.getStore().load();
				}
				if(rootPermisos.data.items[0].data['create']){
					noticiaGrid.agregarBtn.setDisabled(0);
				}
				if(rootPermisos.data.items[0].data['grant']){
					noticiaGrid.permisosBtn.setDisabled(0);
				}
			}
		}
	})
	rootPermisos.load({params:{caller:'Paginasnoticia'}});
	
	<?php
	$items=array(
		'north'=>array('title'=>$title_for_layout.' - '.Configure::read('Empresa.nombre'))
		,'center'=>array('layout'=>'fit','items'=>array('noticiaPanel'))
	);
	$ext->viewportTypes=array('north'=>'Panel','south'=>'Panel','west'=>'Panel','east'=>'Panel','center'=>'Panel');
	echo $ext->viewport($items);
	?>
});
</script>