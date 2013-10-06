<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra.js');?>
	var enlaceEditor = new Ext.ux.grid.RowEditor({
		permisos:true
		,listeners: {
			afteredit: {
				fn:function(roweditor, changes, record, rowIndex ){
					Ext.Ajax.request({
						url   : '<?php echo $html->url('/paginasenlaces/modificar/') ?>'
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
								enlaceEditor.grid.getStore().commitChanges();
								enlaceGrid.getStore().reload();
								enlaceEditor.grid.getView().refresh();
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
							enlaceEditor.grid.getStore().rejectChanges();
							enlaceEditor.grid.getView().refresh();
						}
					});
				}
			}
		}
	});
	
	var enlaceGrid = new Ext.grid.GridPanel({
		store: new Ext.data.JsonStore({
			autoLoad: false
			,proxy: new Ext.data.HttpProxy({
				url: '<?php echo $html->url('/paginasenlaces/listar/') ?>'
				,method: 'POST'
			})
			,root: 'enlaces'
			,fields: [{
				name: 'id'
			},{
				name: 'imagen'
			},{
				name: 'title'
			},{
				name: 'externo'
				,type: 'boolean'
			},{
				name: 'url'
			},{
				name: 'borrar_imagen'
				,type: 'boolean'
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
			,text: 'Agregar enlace'
			,disabled: true
			,handler: function(){
				var agregarEnlaceForm = new Ext.FormPanel({ 
					fileUpload: true
					,labelWidth:70
					,url:'<?php echo $html->url('/paginasenlaces/agregar/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						fieldLabel:'Título'
						,name:'Paginasenlace.title'
						,width:300
						,allowBlank:false
						,blankText:'Ingrese el título'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasenlaces/validar/') ?>'})]
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen (opcional)'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginasenlace.imagen'
						,width:300
						,allowBlank:true
					},{ 
						fieldLabel:'Dirección'
						,name:'Paginasenlace.url'
						,width:300
						,allowBlank:false
						,blankText:'Ingrese la dirección'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasenlaces/validar/') ?>'})]
					},new Ext.form.Checkbox({
						boxLabel:'Es externo?'
						,name:'Paginasenlace.externo'
						,inputValue:1
					})]
					,buttons:[{ 
						text:'Enviar'
						,formBind: true
						,handler:function(){
							agregarEnlaceForm.getForm().submit({ 
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
									agregarEnlaceForm.getForm().reset();
									enlaceGrid.getStore().reload();
									enlaceGrid.getView().refresh();
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
				var agregarEnlaceWindow = new Ext.Window({
					autoScroll:true
					,title:'Agregar enlace'
					,modal:true
					,width: 430
					,height: 180
					,items:[agregarEnlaceForm]
					,layout: 'fit'
				}).show();
				enlaceEditor.stopEditing();
			}
		},{
			ref: '../modificarBtn'
			,text: 'Modificar enlace'
			,iconCls: 'x-boton-modificar'
			,disabled: true
			,handler: function(){
				enlaceEditor.startEditing(enlaceGrid.getSelectionModel().getSelections()[0]);
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
					,url:'<?php echo $html->url('/paginasenlaces/modificarimagen/') ?>'
					,frame:true
					,defaultType:'textfield'
					,monitorValid:true
					,items:[{ 
						xtype:'hidden'
						,name:'Paginasenlace.id'
						,value: enlaceGrid.getSelectionModel().selections.keys
					},{ 
						xtype: 'fileuploadfield'
						,emptyText: 'Selecione una imagen'
						,buttonText: 'Seleccione'
						,fieldLabel:'Imagen'
						,name:'Paginasenlace.imagen'
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
									enlaceGrid.getStore().reload();
									enlaceGrid.getView().refresh();
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
				enlaceEditor.stopEditing();
			}
		},{
			ref: '../removeBtn'
			,iconCls: 'x-boton-borrar'
			,text: 'Borrar enlace'
			,disabled: true
			,handler: function(){
				enlaceEditor.stopEditing();
				Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el enlace?',function(btn) {
					if (btn == 'yes') {
						var selectedRows = enlaceGrid.getSelectionModel().getSelections();
						Ext.Ajax.request({
							url   : '<?php echo $html->url('/paginasenlaces/borrar') ?>'
							,method: 'POST'
							,params: {id:enlaceGrid.getSelectionModel().selections.keys}
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
										enlaceEditor.grid.getStore().remove(row);
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
								enlaceEditor.grid.getStore().rejectChanges();
								enlaceEditor.grid.getView().refresh();
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
				if(enlaceGrid.getSelectionModel().getCount() == 1){
					var seleccion=enlaceGrid.getSelectionModel().selections.keys;
				}else
				if(enlaceGrid.getSelectionModel().getCount() == 0){
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
								caller: 'Paginasenlace'
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
							Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el permiso?',function(btn) {
								if (btn == 'yes') {
									permisosEditor.stopEditing();
									var selectedRows = permisosGrid.getSelectionModel().getSelections();
									var str = [];
									for(var i = 0, row; row = selectedRows[i]; i++){
										str.push('row'+i+':'+ selectedRows[i].id);
									}
									str.push('caller:\'Paginasenlace\'');
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
				permisosGrid.getStore().load({params:{caller:'Paginasenlace',foreign_key:seleccion}});
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
			,id:'title'
			,dataIndex: 'title'
			,width: 200
			,editor: {
				allowBlank:false
				,blankText:'Ingrese el título del enlace'
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasenlaces/validar/') ?>'})]	
			}
		},{
			header: "Direccion"
			,id:'url'
			,dataIndex: 'url'
			,width: 250
			,editor: {
				allowBlank:false
				,blankText:'Ingrese la dirección'
				,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasenlaces/validar/') ?>'})]
			}
		},{
			header: "Externa"
			,dataIndex: 'externo'
			,align:'center'
			,width: 150
			,xtype: 'booleancolumn'
			,trueText: 'Si'
			,falseText: 'No'
			,editor: {xtype: 'checkbox'}
		},{
			header: "Borrar imagen"
			,dataIndex: 'borrar_imagen'
			,align:'center'
			,width: 150
			,xtype: 'booleancolumn'
			,trueText: 'Si'
			,falseText: 'No'
			,editor: {xtype: 'checkbox', inputValue:1}
		}]
		,plugins: [enlaceEditor,new Ext.ux.dd.GridDragDropRowOrder({
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
						var url = '<?php echo $html->url('/paginasenlaces/reorder/') ?>';
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
		,autoExpandColumn: 'title'
		,enableDD:true
		,errorDD:'No tiene permisos para mover el enlace'
	});
	enlaceGrid.getSelectionModel().singleSelect=true;
	enlaceGrid.getSelectionModel().on('selectionchange', function(sm){
		enlaceGrid.permisosBtn.setDisabled(1);
		enlaceGrid.removeBtn.setDisabled(1);
		enlaceGrid.modificarBtn.setDisabled(1);
		enlaceGrid.modificarImagenBtn.setDisabled(1);
		enlaceGrid.permisosBtn.setDisabled(1);
		if(sm.getCount()==0){
			if(rootPermisos.data.items[0].data['grant']){
				enlaceGrid.permisosBtn.setDisabled(0);
			}
		}else
		if(sm.getCount()==1){
			if(enlaceGrid.getStore().data.map[sm.selections.keys].data.permiso['delete']===true){
				enlaceGrid.removeBtn.setDisabled(0);
			}
			if(enlaceGrid.getStore().data.map[sm.selections.keys].data.permiso['update']===true){
				enlaceGrid.modificarBtn.setDisabled(0);
				enlaceGrid.modificarImagenBtn.setDisabled(0);
			}
			if(enlaceGrid.getStore().data.map[sm.selections.keys].data.permiso['grant']===true){
				enlaceGrid.permisosBtn.setDisabled(0);
			}
		}
	});
	
	var showFlash=function(delay){
		(function() {
			$('.flashswf').each(function(index){
				if($(this).html().substr(0,4)!="<obj"){
					var width=185;
					$(this).flash({
						swf: $(this).html()
						,width: width
						,params: {
							bgcolor: "#ffffff"
							,menu: "false"
							,scale: 'noScale'
							,wmode: "opaque"
							,allowfullscreen: "true"
							,allowScriptAccess: "always"
						}
					})
					var aspectRatio=$(this).flash(
						function() {
							movieH=this.TGetProperty('/', 9);
							movieV=this.TGetProperty('/', 8);
							aspectRatio =  movieH/movieV;
							$(this).height(width*aspectRatio);
							
						}
					);
				}
			});
		}.defer(delay,this));
		return true;
	}
	enlaceGrid.getView().on('refresh', function(){
		showFlash(50);
	});
	
	var enlaceMenuContextual = new Ext.menu.Menu({
		items: [{
			text: 'Modificar testimonio'
			,handler:enlaceGrid.modificarBtn.handler
			,iconCls:'x-menu-item-modificar'
		},{
			text: 'Cambiar imagen'
			,handler:enlaceGrid.modificarImagenBtn.handler
			,iconCls:'x-menu-item-cambiarimagen'
		},{
			text: 'Modificar permisos'
			,handler:enlaceGrid.permisosBtn.handler
			,iconCls:'x-menu-item-permisos'
		},'-',{
			text: 'Borrar enlace'
			,handler:enlaceGrid.removeBtn.handler
			,iconCls:'x-menu-item-borrar'
		}]
	});
	
	enlaceGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
		grid.getSelectionModel().selectRow(rowIndex);
		event.stopEvent();
		enlaceMenuContextual.items.each(function(item){
			item.disable();
		})
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['update']===true){
			enlaceMenuContextual.items.items[0].enable();
			enlaceMenuContextual.items.items[1].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['grant']===true){
			enlaceMenuContextual.items.items[2].enable();
		}
		if (grid.getStore().data.map[grid.getSelectionModel().selections.keys].data.permiso['delete']===true){
			enlaceMenuContextual.items.items[4].enable();
		}
		enlaceMenuContextual.showAt(event.getXY());
	});
	
	var enlacePanel = new Ext.Panel({
		title: 'Enlaces'
		,items:[]
		,layout:'fit'
		,defaults:{autoScroll:true}
		<?php
			$strings=array();
			foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
				$strings[] = '{id:\''.$key.'\'
					,handler: function(){
						enlacePanel.currentIdioma=\''.$key.'\'
						enlaceGrid.getStore().load({params:{idioma: enlacePanel.currentIdioma}});
						if(enlacePanel.currentIdioma != \''.Configure::read('Empresa.language').'\'){
							enlaceGrid.getColumnModel().config[3].editor.allowBlank=true;
							enlaceGrid.agregarBtn.disable();
						}else{
							enlaceGrid.agregarBtn.enable();
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
					enlacePanel.add(enlaceGrid)
					enlacePanel.doLayout();
					enlacePanel.currentIdioma='<?php echo Configure::read('Empresa.language');?>'
					enlaceGrid.getStore().load({params:{idioma: enlacePanel.currentIdioma}});
				}
				if(rootPermisos.data.items[0].data['create']){
					enlaceGrid.agregarBtn.setDisabled(0);
				}
				if(!rootPermisos.data.items[0].data['update']){
					enlaceGrid.enableDD=false;
				}
				if(rootPermisos.data.items[0].data['grant']){
					enlaceGrid.permisosBtn.setDisabled(0);
				}
			}
		}
	})
	rootPermisos.load({params:{caller:'Paginasenlace'}});
	
	<?php
	
	$items=array(
		'north'=>array('title'=>$title_for_layout.' - '.Configure::read('Empresa.nombre'),'items'=>array('barra'))
		,'center'=>array('layout'=>'fit','items'=>array('enlacePanel'))
	);
	$ext->viewportTypes=array('north'=>'Panel','south'=>'Panel','west'=>'Panel','east'=>'Panel','center'=>'Panel');
	echo $ext->viewport($items);
	

	?>
});
</script>