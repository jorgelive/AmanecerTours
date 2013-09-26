<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra');?>
	var testimonioEditor = new Ext.ux.grid.RowEditor({
		permisos:true
		,listeners: {
			afteredit: {
				fn:function(roweditor, changes, record, rowIndex ){
					Ext.Ajax.request({
						url   : '<?php echo $html->url('/paginastestimonios/modificar/') ?>'
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
								testimonioEditor.grid.getStore().commitChanges();
								testimonioGrid.getStore().reload();
								testimonioEditor.grid.getView().refresh();
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
							testimonioEditor.grid.getStore().rejectChanges();
							testimonioEditor.grid.getView().refresh();
						}
					});
				}
			}
		}
	});
	
	var testimonioStore = new Ext.data.JsonStore({
		autoLoad: false
		,url: '<?php echo $html->url('/paginastestimonios/listar/') ?>'
		,root: 'testimonios'
		,fields: [{
			name: 'id'
		},{
			name: 'name'
		},{
			name: 'nacionalidad'
		},{
			name: 'imagen'
		},{
			name: 'email'
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
					if(testimonioPanel.paginatorStart&&testimonioPanel.paginatorLimit){
						options.params.start=testimonioPanel.paginatorStart;
						options.params.limit=testimonioPanel.paginatorLimit;
					}else{
						options.params.start=0;
						options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
					}
				}
				testimonioPanel.paginatorStart=options.params.start;
				testimonioPanel.paginatorLimit=options.params.limit;
				options.params.page = Math.floor(options.params.start / options.params.limit)+1;
				options.params.idioma = testimonioPanel.currentIdioma;
				return true;
			}
		}
	})
	
	var testimonioGrid = new Ext.grid.GridPanel({
		store: testimonioStore
		,loadMask: {msg:'Cargando Datos...'}
		,columns: [new Ext.grid.RowNumberer()
		,{
			header: "Id"
			,dataIndex: 'id'
			,width: 20
			,hidden: true
		},{
			header: "Nombre"
			,id:'name'
			,dataIndex: 'name'
			,width: 200
			,editor: {
				allowBlank:false
				,blankText:'Ingrese el nombre del pasajero'
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginastestimonios/validar/') ?>'})]	
			}
			,filter:true
		},{
			header: "Nacionalidad"
			,id:'nacionalidad'
			,dataIndex: 'nacionalidad'
			,width: 100
			,editor: {
				allowBlank:false
				,blankText:'Ingrese el la nacionalidad del pasajero'
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginastestimonios/validar/') ?>'})]
			}
			,filter:true
		},{
			header: "Imagen"
			,width: 125
			,dataIndex: 'imagen'
			,align:'center'
			,editor: {disabled: true}
			,renderer:Ext.util.Format.imageRenderer(120,true)
		},{
			header: "Correo electrónico"
			,id:'email'
			,dataIndex: 'email'
			,width: 150
			,editor: {
				allowBlank:false
				,blankText:'Ingrese el email'
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginastestimonios/validar/') ?>'})]
			}
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
				,blankText:'Ingrese el testimonio'
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
						testimonioEditor.verifyLayout.defer(100,testimonioEditor);
						this.onResize(testimonioGrid.getColumnModel().getColumnWidth(6),200)
					}
				}
			}
			
			,filter:true
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
			,filter:true
		},{
			header: "Borrar imagen"
			,dataIndex: 'borrar_imagen'
			,align:'center'
			,width: 80
			,xtype: 'booleancolumn'
			,trueText: 'Si'
			,falseText: 'No'
			,editor: {xtype: 'checkbox'}
		}]
		,plugins: [testimonioEditor,new Ext.ux.grid.FilterRow()]
		,ddText: '{0} fila{1} seleccionada{1}'
		,stripeRows: true
		,autoExpandColumn: 'contenido'
		,bbar: new Ext.PagingToolbar({
			pageSize: <?php echo Configure::read('Default.paginatorSize');?>
			,displayInfo: true
			,filter:true
			,store: testimonioStore
			,displayMsg: 'mostrando testimonios {0} - {1} de {2}'
			,emptyMsg: "No hay testimonios para mostrar"
		})
		,tbar: [{
			ref: '../agregarBtn'
			,iconCls: 'x-boton-agregar'
			,text: 'Agregar testimonio'
			,disabled: true
			,handler: function(){
				var agregarTestimonioForm = new Ext.FormPanel({ 
					fileUpload: true
					,labelWidth:120
					,url:'<?php echo $html->url('/paginastestimonios/agregar/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						fieldLabel:'Nombre'
						,name:'Paginastestimonio.name'
						,width:300
						,allowBlank:false
						,blankText:'Ingrese el nombre del pasajero'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginastestimonios/validar/') ?>'})]
					},{ 
						fieldLabel:'Nacionalidad'
						,name:'Paginastestimonio.nacionalidad'
						,width:300
						,allowBlank:false
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginastestimonios/validar/') ?>'})]
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen (opcional)'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginastestimonio.imagen'
						,width:300
						,allowBlank:true
					},{ 
						fieldLabel:'Correo electrónico'
						,name:'Paginastestimonio.email'
						,width:300
						,allowBlank:false
						,blankText:'Ingrese el email'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginastestimonios/validar/') ?>'})]
					},{ 
						xtype: 'tinymce'
						,allowBlank: false
						,tagMatters: true
						,blankText:'Ingrese el testimonio'
						,fieldLabel:'Contenido'
						,name:'Paginastestimonio.contenido'
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
						,name:'Paginastestimonio.fecha'
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
							agregarTestimonioForm.getForm().submit({ 
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
									agregarTestimonioForm.getForm().reset();
									testimonioGrid.getStore().reload();
									testimonioGrid.getView().refresh();
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
				var agregarTestimonioWindow = new Ext.Window({
					autoScroll:true
					,title:'Agregar testimonio'
					,modal:true
					,width: 470
					,height: 500
					,items:[agregarTestimonioForm]
					,layout: 'fit'
				}).show();
				testimonioEditor.stopEditing();
			}
		},{
			ref: '../modificarBtn'
			,text: 'Modificar testimonio'
			,iconCls: 'x-boton-modificar'
			,disabled: true
			,handler: function(){
				testimonioEditor.startEditing(testimonioGrid.getSelectionModel().getSelections()[0]);
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
					,url:'<?php echo $html->url('/paginastestimonios/modificarimagen/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						xtype:'hidden'
						,name:'Paginastestimonio.id'
						,value: testimonioGrid.getSelectionModel().selections.keys
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginastestimonio.imagen'
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
									testimonioGrid.getStore().reload();
									testimonioGrid.getView().refresh();
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
				testimonioEditor.stopEditing();
			}
		},{
			ref: '../removeBtn'
			,iconCls: 'x-boton-borrar'
			,text: 'Borrar testimonio'
			,disabled: true
			,handler: function(){
				testimonioEditor.stopEditing();
				Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el testimonio?',function(btn) {
					if (btn == 'yes') {
						var selectedRows = testimonioGrid.getSelectionModel().getSelections();
						Ext.Ajax.request({
							url   : '<?php echo $html->url('/paginastestimonios/borrar') ?>'
							,method: 'POST'
							,params: {id:testimonioGrid.getSelectionModel().selections.keys}
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
										testimonioEditor.grid.getStore().remove(row);
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
								testimonioEditor.grid.getStore().rejectChanges();
								testimonioEditor.grid.getView().refresh();
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
				if(testimonioGrid.getSelectionModel().getCount() == 1){
					var seleccion=testimonioGrid.getSelectionModel().selections.keys;
				}else
				if(testimonioGrid.getSelectionModel().getCount() == 0){
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
								caller: 'Paginastestimonio'
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
									str.push('caller:\'Paginastestimonio\'');
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
				permisosGrid.getStore().load({params:{caller:'Paginastestimonio',foreign_key:seleccion}});
				permisosGrid.getSelectionModel().on('selectionchange', function(sm){
					permisosGrid.removeBtn.setDisabled(sm.getCount() < 1);
					permisosGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				});
				
			}
		}]
	});
	testimonioGrid.getSelectionModel().singleSelect=true;
	testimonioGrid.getSelectionModel().on('selectionchange', function(sm){
		testimonioGrid.permisosBtn.setDisabled(1);
		testimonioGrid.removeBtn.setDisabled(1);
		testimonioGrid.modificarBtn.setDisabled(1);
		testimonioGrid.modificarImagenBtn.setDisabled(1);
		testimonioGrid.permisosBtn.setDisabled(1);
		if(sm.getCount()==0){
			if(rootPermisos.data.items[0].data['grant']){
				testimonioGrid.permisosBtn.setDisabled(0);
			}
		}else
		if(sm.getCount()==1){
			if(testimonioGrid.getStore().data.map[sm.selections.keys].data.permiso['delete']===true){
				testimonioGrid.removeBtn.setDisabled(0);
			}
			if(testimonioGrid.getStore().data.map[sm.selections.keys].data.permiso['update']===true){
				testimonioGrid.modificarBtn.setDisabled(0);
				testimonioGrid.modificarImagenBtn.setDisabled(0);
			}
			if(testimonioGrid.getStore().data.map[sm.selections.keys].data.permiso['grant']===true){
				testimonioGrid.permisosBtn.setDisabled(0);
			}
		}
	});
	
	var testimonioMenuContextual = new Ext.menu.Menu({
		items: [{
			text: 'Modificar testimonio'
			,handler:testimonioGrid.modificarBtn.handler
			,iconCls:'x-menu-item-modificar'
		},{
			text: 'Cambiar imagen'
			,handler:testimonioGrid.modificarImagenBtn.handler
			,iconCls:'x-menu-item-cambiarimagen'
		},{
			text: 'Modificar permisos'
			,handler:testimonioGrid.permisosBtn.handler
			,iconCls:'x-menu-item-permisos'
		},'-',{
			text: 'Borrar testimonio'
			,handler:testimonioGrid.removeBtn.handler
			,iconCls:'x-menu-item-borrar'
		}]
	});
	
	testimonioGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
		grid.getSelectionModel().selectRow(rowIndex);
		event.stopEvent();
		testimonioMenuContextual.items.each(function(item){
			item.disable();
		})
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['update']===true){
			testimonioMenuContextual.items.items[0].enable();
			testimonioMenuContextual.items.items[1].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['grant']===true){
			testimonioMenuContextual.items.items[2].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['delete']===true){
			testimonioMenuContextual.items.items[4].enable();
		}
		testimonioMenuContextual.showAt(event.getXY());
	});
	
	var testimonioPanel = new Ext.Panel({
		title: 'Testimonios'
		,items:[]
		,layout:'fit'
		,defaults:{autoScroll:true}
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
					testimonioPanel.add(testimonioGrid)
					testimonioPanel.doLayout();
					testimonioGrid.getStore().load();
				}
				if(rootPermisos.data.items[0].data['create']){
					testimonioGrid.agregarBtn.setDisabled(0);
				}
				if(rootPermisos.data.items[0].data['grant']){
					testimonioGrid.permisosBtn.setDisabled(0);
				}
			}
		}
	})
	rootPermisos.load({params:{caller:'Paginastestimonio'}});
	
	<?php
	
	$items=array(
		'north'=>array('title'=>$title_for_layout.' - '.Configure::read('Empresa.nombre'))
		,'center'=>array('layout'=>'fit','items'=>array('testimonioPanel'))
	);
	$ext->viewportTypes=array('north'=>'Panel','south'=>'Panel','west'=>'Panel','east'=>'Panel','center'=>'Panel');
	echo $ext->viewport($items);
	?>
});
</script>