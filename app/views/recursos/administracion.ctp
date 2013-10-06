<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra.js');?>
	var treeHandler = function(button,event) {
		var selected=tree.getSelectionModel().getSelectedNode();
		if (button.id=='agregar'||button.id=='editar'){
			var agregarEditarForm = new Ext.FormPanel({ 
				labelWidth:110
				,url:'dummy' 
				,frame:true
				,defaultType:'textfield'
				,monitorValid:true
				,autoScroll:true
				,items:[{
					xtype: 'radiogroup'
					,fieldLabel: 'Seleccione tipo'
					,name:'Recurso.tipo'
					,allowBlank:false
					,width : 600
					,items: [{
						boxLabel: 'Recurso', name: 'tipo', inputValue: 1
					},{
						boxLabel: 'Enlace', name: 'tipo', inputValue: 2
					},{
						boxLabel: 'Botón', name: 'tipo', inputValue: 3
					},{
						boxLabel: 'Separador', name: 'tipo', inputValue: 4
					}]
					,listeners:{
						'change':function(radiogroup,checked){
							var nombreFormItem = new Ext.form.TextField({ 
								fieldLabel:'Nombre'
								,name:'Recurso.name' 
								,allowBlank:false
								,width : 600
								,blankText:'Ingrese el nombre o alias'
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/recursos/validar/') ?>'})]
							});
							var descripcionFormItem = new Ext.form.TextArea({
								fieldLabel:'Descripción'
								,name:'Recurso.descripcion' 
								,allowBlank:false
								,width : 600
								,height : 150
								,blankText:'Ingrese la descripción'
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/recursos/validar/') ?>'})]
							});
							var modeloFormItem = new Ext.form.ComboBox({
								fieldLabel: 'Modelo'
								,hiddenName: 'Recurso.model'
								,width:190
								,store: new Ext.data.JsonStore({
									url: '<?php echo $html->url('/recursos/listado/') ?>'
									,root: 'Model'
									,fields: ['id','model']
								})
								,displayField: 'model'
								,valueField: 'id'
								,typeAhead: true
								,mode: 'remote'
								,triggerAction: 'all'
								,emptyText: 'Ingrese el modelo'
								,selectOnFocus:true
								,allowBlank: false
							});
							
							var destinoFormItem = new Ext.form.TextField({
								name:'Recurso.accion' 
								,fieldLabel:'Destino'
								,allowBlank:false
								,blankText:'Ingrese el destino del botón'
								,width : 600
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/recursos/validar/') ?>'})]
							});
							
							var accionFormItem = new Ext.form.TextArea({
								name:'Recurso.accion'
								,fieldLabel:'Acción'
								,width : 600
								,height : 150
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/recursos/validar/') ?>'})]
							});
							
							var confirmarAccionFormItem = new Ext.form.Checkbox({
								boxLabel:'Requiere confirmación?'
								,name:'Recurso.confirmar_accion'
								,inputValue:1
							})
							
							if(agregarEditarForm.getForm().findField('Recurso.name')!=null){
								agregarEditarForm.remove(agregarEditarForm.getForm().findField('Recurso.name'));
							}
							if(agregarEditarForm.getForm().findField('Recurso.descripcion')!=null){
								agregarEditarForm.remove(agregarEditarForm.getForm().findField('Recurso.descripcion'));
							}
							if(agregarEditarForm.getForm().findField('Recurso.model')!=null){
								agregarEditarForm.remove(agregarEditarForm.getForm().findField('Recurso.model'));
							}
							if(agregarEditarForm.getForm().findField('Recurso.accion')!=null){
								agregarEditarForm.remove(agregarEditarForm.getForm().findField('Recurso.accion'));
							}
							if(agregarEditarForm.getForm().findField('Recurso.confirmar_accion')!=null){
								agregarEditarForm.remove(agregarEditarForm.getForm().findField('Recurso.confirmar_accion'));
							}
							
							if (checked.inputValue==1){
								agregarEditarForm.add(nombreFormItem);
								agregarEditarForm.add(descripcionFormItem);
								agregarEditarForm.add(modeloFormItem);
								if(agregarEditarForm.customStore){
									agregarEditarForm.getForm().findField('Recurso.name').setValue(agregarEditarForm.customStore.name);
									agregarEditarForm.getForm().findField('Recurso.descripcion').setValue(agregarEditarForm.customStore.descripcion);
									agregarEditarForm.getForm().findField('Recurso.model').setValue(agregarEditarForm.customStore.model);
									agregarEditarForm.getForm().findField('Recurso.model').getStore().load();
								}

							}else
							if (checked.inputValue==2){
								agregarEditarForm.add(nombreFormItem);
								agregarEditarForm.add(descripcionFormItem);
								agregarEditarForm.add(destinoFormItem);
								agregarEditarForm.add(confirmarAccionFormItem);
								if(agregarEditarForm.customStore){
									agregarEditarForm.getForm().findField('Recurso.name').setValue(agregarEditarForm.customStore.name);
									agregarEditarForm.getForm().findField('Recurso.descripcion').setValue(agregarEditarForm.customStore.descripcion);
									agregarEditarForm.getForm().findField('Recurso.accion').setValue(agregarEditarForm.customStore.accion);
									agregarEditarForm.getForm().findField('Recurso.confirmar_accion').setValue(agregarEditarForm.customStore.confirmar_accion);
									
								}
							}
							if (checked.inputValue==3){
								agregarEditarForm.add(nombreFormItem);
								agregarEditarForm.add(descripcionFormItem);
								agregarEditarForm.add(accionFormItem);
								agregarEditarForm.add(confirmarAccionFormItem);
								if(agregarEditarForm.customStore){
									agregarEditarForm.getForm().findField('Recurso.name').setValue(agregarEditarForm.customStore.name);
									agregarEditarForm.getForm().findField('Recurso.descripcion').setValue(agregarEditarForm.customStore.descripcion);
									agregarEditarForm.getForm().findField('Recurso.accion').setValue(agregarEditarForm.customStore.accion);
									agregarEditarForm.getForm().findField('Recurso.confirmar_accion').setValue(agregarEditarForm.customStore.confirmar_accion);
								}
							}
							agregarEditarForm.doLayout();
						}
					}
				},{ 
					xtype:'hidden'
					,name:'Recurso.id' 
				},{ 
					xtype:'hidden'
					,name:'Recurso.parent_id' 
				}
				]
				,buttons:[{ 
					text:'Enviar',
					formBind: true,	 
					handler:function(){ 
						agregarEditarForm.getForm().submit({ 
							method:'POST', 
							waitTitle:'Conectando', 
							waitMsg:'Enviando información...',
							success:function(form, action){ 
								obj = Ext.util.JSON.decode(action.response.responseText);
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
								if(agregarEditarForm.getForm().url=='/recursos/modificar/'){
									var node=tree.getSelectionModel().getSelectedNode().parentNode;
									node.on('expand',selectNode.createDelegate(this,[viewPort.getComponent('center').activeTab.openerNode]),this,{single:true});
								}else{
									viewPort.getComponent('center').activeTab.newId=obj.data.newId;
									var node=tree.getSelectionModel().getSelectedNode();
									node.on('expand',selectNode.createDelegate(this,[obj.data.newId]),this,{single:true});
								}
								node.reload();
								node.expand();
								viewPort.getComponent('center').remove(agregarEditarPanel)
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
			var agregarEditarPanel = new Ext.Panel({
				itemId:button.id+selected.id
				,xtype:'panel'
				,closable:true
				,layout:'fit'
				,items:[agregarEditarForm]
				,autoScroll: true
			});
			viewPort.getComponent('center').add(agregarEditarPanel);
		}
		if (button.id=='agregar'){
			agregarEditarPanel.setTitle('Agregar recurso en '+selected.attributes.text);
			agregarEditarForm.getForm().findField('Recurso.parent_id').setValue(selected.id);
			agregarEditarForm.getForm().url='<?php echo $html->url('/recursos/agregar/') ?>';
			viewPort.getComponent('center').setActiveTab(button.id+selected.id);
			viewPort.getComponent('center').activeTab.openerNode=selected.id;
		}else
		if (button.id=='editar'){
			Ext.Ajax.request({
				url: '<?php echo $html->url('/recursos/recursoinfo/') ?>'
				,method: 'POST'
				,params: {id:selected.id}
				,success: function(respuesta,request) {
					obj = Ext.util.JSON.decode(respuesta.responseText);
					if (obj.success){
						if(obj.hasOwnProperty('message')){
							Ext.Msg.alert('Correcto!', obj.message);
						}
						if(obj.hasOwnProperty('redirect')){
							window.location = obj.redirect;
						}
						agregarEditarForm.getForm().findField('Recurso.id').setValue(obj.data.Recurso.id);
						agregarEditarForm.customStore={
							name:obj.data.Recurso.name
							,descripcion:obj.data.Recurso.descripcion
							,model:obj.data.Recurso.model
							,accion:obj.data.Recurso.accion
							,confirmar_accion:obj.data.Recurso.confirmar_accion
						}
						agregarEditarForm.getForm().findField('Recurso.tipo').setValue(obj.data.Recurso.tipo);
						agregarEditarForm.getForm().url='<?php echo $html->url('/recursos/modificar/') ?>';
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
				}
			});
			agregarEditarPanel.setTitle('Modificar recurso en '+selected.attributes.text);
			viewPort.getComponent('center').setActiveTab(button.id+selected.id);
			viewPort.getComponent('center').activeTab.openerNode=selected.id;
		} else
		if (button.id=='permisos'){
			var permisosEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							if(selected.attributes.model){
								foreignKey=selected.attributes.model;
							}else{
								foreignKey=selected.id;
							}
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
						name: 'administrador'
					},{
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
							name: 'administrador'
							,type: 'string'
						},{
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
						
						if(!selected.attributes.model){
							var e = new Permiso({
								caller: 'Recurso'
								,foreign_key: selected.id
								,aro_id: ''
								,_read: false
								,_create: false
								,_update: false
								,_delete: false
							});
							
						}else{
							var e = new Permiso({
								administrador:'Recurso'
								,caller: selected.attributes.model
								,foreign_key: 'root'
								,aro_id: ''
								,_read: false
								,_create: false
								,_update: false
								,_delete: false
							});
						}
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
								str.push('administrador:\'Recurso\'');
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
			if(!selected.attributes.model){
				permisosGrid.getStore().load({params:{caller:'Recurso',foreign_key:selected.id}});
			}else{
				permisosGrid.getStore().load({params:{administrador:'Recurso',caller:selected.attributes.model,foreign_key:'root'}});
			}
			permisosGrid.getSelectionModel().on('selectionchange', function(sm){
        		permisosGrid.removeBtn.setDisabled(sm.getCount() < 1);
				permisosGrid.modificarBtn.setDisabled(sm.getCount() < 1);
   			});
		} else
		if (button.id=='borrar'){
			Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el recurso?',function(btn) {
				if (btn == 'yes') {
					Ext.Ajax.request({
						url: '<?php echo $html->url('/recursos/borrar/') ?>'
						,method: 'POST'
						,params: {id:selected.id}
						,success: function(respuesta,request) {
							obj = Ext.util.JSON.decode(respuesta.responseText);
							if (obj.success){
								if(obj.hasOwnProperty('message')){
									Ext.Msg.alert('Correcto!', obj.message);
								}
								if(obj.hasOwnProperty('redirect')){
									window.location = obj.redirect;
								}
								selected.parentNode.reload();	
								viewPort.getComponent('center').items.each(function(item){
									if(item.id=='agregar'+selected.id){
										viewPort.getComponent('center').remove(item);
									}else
									if(item.id=='editar'+selected.id){
										viewPort.getComponent('center').remove(item);
									}
								})
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
						}
					});
				}
			});
		} else {
			Ext.MessageBox.alert('No acción aplicable','No se puede aplicar esta acción a este recurso.');
		}
 	};
	
	var tree = new Ext.tree.TreePanel({ //global
		autoScroll:true
		,animate:true
		,enableDD:true
		,containerScroll: true
		,rootVisible: true
		,loader: new Ext.tree.TreeLoader({
			dataUrl:'<?php echo $html->url('/recursos/getnodes/') ?>'
			,listeners:{
				'load':function(loader,nodo,respuesta){
					obj = Ext.util.JSON.decode(respuesta.responseText);
					if (!obj.success){
						if(obj.hasOwnProperty('errors')){
							if(typeof(obj.errors)=='object'){
								errorstring='';
								for(prop in obj.errors){errorstring+=obj.errors[prop]+"<br>";}	
							}else{
								errorstring=obj.errors;
							}
							Ext.Msg.alert('Error!', errorstring);
						}
						if(obj.hasOwnProperty('redirect')){
							window.location = obj.redirect;
						}
					}
				}
			}
		})
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
				root.attributes.permiso=rootPermisos.data.items[0].data;
				if(root.attributes.permiso['create']===false&&root.attributes.permiso['update']===false&&root.attributes.permiso['grant']===false&&root.attributes.permiso['delete']===false){
					root.disable();
				}
				if(root.attributes.permiso['update']===false){
					tree.enableDD=false;
				}
				if(root.attributes.permiso['read']===true){
					root.expand();
				}else{
					root.disable();
					root.leaf=true;
				}
			}
		}
	});
	rootPermisos.load({params:{caller:'Recurso'}});
	
	var root = new Ext.tree.AsyncTreeNode({
		text:'Recursos'
		,draggable:false
		,id:'root'
		,permiso:{read:false,create:false,update:false,'delete':false,grant:false}
	});
	
	tree.setRootNode(root);
	
	
	var oldPosition = null;
	var oldNextSibling = null;
	
	
	var menucontextual = new Ext.menu.Menu({
		id: 'mainContext',
		items: [{
			text: 'Agregar recurso'
			,id: 'agregar'
			,handler:treeHandler
			,iconCls:'x-menu-item-agregar'
		},{
			text: 'Editar recurso'
			,id: 'editar'
			,handler:treeHandler
			,iconCls:'x-menu-item-modificar'
		},{
			text: 'Modificar permisos'
			,id: 'permisos'
			,handler:treeHandler
			,iconCls:'x-menu-item-permisos'
		},'-',{
			text: 'Borrar recurso'
			,id: 'borrar'
			,handler:treeHandler
			,iconCls:'x-menu-item-borrar'
		}]
	});
	
	tree.on('contextmenu',function(node){
		node.select();
		menucontextual.items.each(function(item){
			item.disable();
		})
		if (node.attributes.permiso['create']===true&&(node.leaf===false||node.isRoot===true)){
			menucontextual.items.items[0].enable();
		}
		if (node.attributes.permiso['update']===true&&node.isRoot!==true){
			menucontextual.items.items[1].enable();
		}
		if (node.attributes.permiso['grant']===true&&(node.isRoot===true||node.attributes.tipo==1)){
			menucontextual.items.items[2].enable();
		}
		if (node.attributes.permiso['delete']===true&&node.isRoot!==true){
			menucontextual.items.items[4].enable();
		}
		if(!node.disabled){
			menucontextual.show(node.ui.getAnchor());
		}
	});

	tree.on('startdrag', function(tree, node, event){
		oldPosition = node.parentNode.indexOf(node);
		oldNextSibling = node.nextSibling;
	});
	
	tree.on('movenode', function(tree, node, oldParent, newParent, position){
		if (oldParent == newParent){
			var url = '<?php echo $html->url('/recursos/reorder/') ?>';
			var params = {'node':node.id, 'delta':(position-oldPosition)};
		} else {
			var url = '<?php echo $html->url('/recursos/reparent/') ?>';
			var params = {'node':node.id, 'parent':newParent.id, 'position':position};
		}
		tree.disable();
		Ext.Ajax.request({
			url:url,
			params:params,
			success:function(respuesta, request) {
				obj = Ext.util.JSON.decode(respuesta.responseText);
				if (obj.success){
					if(obj.hasOwnProperty('message')){
						Ext.Msg.alert('Correcto!', obj.message);
					}
					if(obj.hasOwnProperty('redirect')){
						window.location = obj.redirect;
					}
					tree.enable();
				}else{
					request.failure();
				}
			},
			failure:function() {
				tree.suspendEvents();
				oldParent.appendChild(node);
				if (oldNextSibling){
					oldParent.insertBefore(node, oldNextSibling);
				}
				tree.resumeEvents();
				tree.enable();
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
	});
	
	<?php
	$items=array(
				'north'=>array('title'=>$title_for_layout.' - '.Configure::read('Empresa.nombre'),'items'=>array('barra'))
				,'west'=>array('title'=>'Secciones','items'=>array('tree'),'width'=>400)
			);
	echo $ext->viewport($items);
	?>
});
</script>