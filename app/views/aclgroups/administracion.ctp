<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra');?>
	var treeHandler = function(button,event) {
		var selected=tree.getSelectionModel().getSelectedNode();
		if (button.id=='agregarUsuario'||(button.id=='editar'&&selected.leaf===true)){
			var usuarioForm = new Ext.FormPanel({ 
				labelWidth:140
				,url:'dummy' 
				,frame:true
				,defaultType:'textfield'
				,monitorValid:true
				,items:[{ 
					xtype:'hidden'
					,name:'Acluser.id' 
				},{ 
					xtype:'hidden'
					,name:'Acluser.aclgroup_id' 
				},{ 
					fieldLabel:'Nombre de Usuario'
					,name:'Acluser.username'
					,width:120
					,allowBlank:false
					,blankText:'Ingrese el nombre'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/aclusers/validar/') ?>'})]
				},{ 
					fieldLabel:'Nombre completo'
					,name:'Acluser.name' 
					,width:190
					,allowBlank:false
					,blankText:'Ingrese el nombre completo'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/aclusers/validar/') ?>'})]
				},{ 
					fieldLabel:'Contraseña'
					,name:'Acluser.clear_password' 
					,width:190
					,allowBlank:false
					,inputType: 'password'
					,blankText:'Ingrese la contraseña'
					,listeners: {
						'keypress': function(){
							if(this.getValue()!=''){
								usuarioForm.getForm().findField('Acluser.confirm_password').setValue('');
								usuarioForm.getForm().findField('Acluser.confirm_password').allowBlank=false;
							}
						}
					}
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/aclusers/validar/') ?>'})]
				},{ 
					fieldLabel:'Confirme la contraseña'
					,name:'Acluser.confirm_password' 
					,width:190
					,allowBlank:false
					,inputType: 'password'
					,blankText:'Ingrese la confirmacion de la contraseña'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/aclusers/validar/') ?>',params:{
							clear_password: function(){
								return usuarioForm.getForm().getValues()['Acluser.clear_password']
							}
						}
					})]
				},{ 
					fieldLabel:'Correo Electrónico'
					,name:'Acluser.email' 
					,width:190
					,allowBlank:false
					,blankText:'Ingrese el correo electrónico'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/aclusers/validar/') ?>'})]
				}]
				,buttons:[{ 
					text:'Enviar',
					formBind: true,	 
					handler:function(){ 
						usuarioForm.getForm().submit({ 
							method:'POST' 
							,waitTitle:'Conectando'
							,waitMsg:'Enviando información...'
							,success:function(form, action){ 
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
								if(usuarioForm.getForm().url=='/aclusers/modificar/'){
									var node=tree.getSelectionModel().getSelectedNode().parentNode;
									node.on('expand',selectNode.createDelegate(this,[viewPort.getComponent('center').activeTab.openerNode]),this,{single:true});
								}else{
									viewPort.getComponent('center').activeTab.newId=obj.data.newId;
									var node=tree.getSelectionModel().getSelectedNode();
									node.on('expand',selectNode.createDelegate(this,[obj.data.newId]),this,{single:true});
								}
								node.reload();
								node.expand();
								viewPort.getComponent('center').remove(usuarioForm);
							}
							,failure:function(form, action){ 
								if(action.failureType == 'server'){ 
									obj = Ext.util.JSON.decode(action.response.responseText); 
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
								}else if(action.failureType == 'connect'){ 
									Ext.Msg.alert('Error!', 'El servidor tiene un error : ' + action.response.responseText); 
								}else{ 
									Ext.Msg.alert('Error!', 'El cliente no puede procesar la respuesta del servidor'); 
								} 
								usuarioForm.getForm().reset(); 
							} 
						}); 
					} 
				}] 
			});
			
			var usuarioPanel = new Ext.Panel({title: 'Información del usuario',items:[usuarioForm],layout:'fit',defaults:{autoScroll:true}});
			
		}
		if (button.id=='agregarGrupo'||(button.id=='editar'&&selected.leaf===false)){
			var grupoForm = new Ext.FormPanel({ 
				labelWidth:80
				,url:'dummy' 
				,frame:true
				,defaultType:'textfield'
				,monitorValid:true
				,items:[{ 
					xtype:'hidden'
					,name:'Aclgroup.id' 
				},{ 
					xtype:'hidden'
					,name:'Aclgroup.parent_id' 
				},{ 
					fieldLabel:'Nombre'
					,name:'Aclgroup.name' 
					,allowBlank:false
					,blankText:'Ingrese el nombre'
					,width:190
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/aclgroups/validar/') ?>'})]
				}]
				,buttons:[{ 
					text:'Enviar',
					formBind: true,	 
					handler:function(){ 
						grupoForm.getForm().submit({ 
							method:'POST'
							,waitTitle:'Conectando'
							,waitMsg:'Enviando información...'
							,success:function(form, action){ 
								obj = Ext.util.JSON.decode(action.response.responseText);
								if(obj.hasOwnProperty('message')){
									Ext.Msg.alert('Correcto!', obj.message);
								}
								if(obj.hasOwnProperty('redirect')){
									window.location = obj.redirect;
								}
								if(grupoForm.getForm().url=='/aclgroups/modificar/'){
									selected.parentNode.reload();
								}else{
									selected.reload();
								}
								grupoWindow.close();
							}
							,failure:function(form, action){ 
								if(action.failureType == 'server'){ 
									obj = Ext.util.JSON.decode(action.response.responseText); 
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
								}else if(action.failureType == 'connect'){ 
									Ext.Msg.alert('Error!', 'El servidor tiene un error : ' + action.response.responseText); 
								}else{ 
									Ext.Msg.alert('Error!', 'El cliente no puede procesar la respuesta del servidor : ' + action.response.responseText); 
								} 
							} 
						}); 
					} 
				}] 
			});
			var grupoWindow = new Ext.Window({
				autoScroll:true
				,modal:true
				,width: 320
				,height: 120
				,items:[grupoForm]
				,layout: 'fit'
			})
		}
		if (button.id=='agregarUsuario'){
			usuarioForm.getForm().findField('Acluser.aclgroup_id').setValue(selected.id);
			usuarioForm.getForm().url='<?php echo $html->url('/aclusers/agregar/') ?>';
			var agregarEditar = new Ext.Panel({
				itemId:button.id+selected.id
				,closable:true
				,title: 'Agregar usuario en '+selected.attributes.text
				,items:[usuarioPanel]
				,defaults:{autoScroll:true}
				,layout:'accordion'
			});
			viewPort.getComponent('center').add(agregarEditar);
			viewPort.getComponent('center').setActiveTab(button.id+selected.id);
			viewPort.getComponent('center').activeTab.openerNode=selected.id;
		} else
		if (button.id=='agregarGrupo'){
			grupoForm.getForm().url='<?php echo $html->url('/aclgroups/agregar/') ?>';
			grupoForm.getForm().findField('Aclgroup.parent_id').setValue(selected.id);
			grupoWindow.setTitle('Agregar grupo en '+selected.attributes.text);
			grupoWindow.show();
		} else
		if (button.id=='editar'){
			if (selected.leaf == true){
				Ext.Ajax.request({
					url: '<?php echo $html->url('/aclusers/userinfo/') ?>'
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
							usuarioForm.getForm().findField('Acluser.id').setValue(obj.data.Acluser.id);
							usuarioForm.getForm().findField('Acluser.username').setValue(obj.data.Acluser.username);
							usuarioForm.getForm().findField('Acluser.name').setValue(obj.data.Acluser.name);
							usuarioForm.getForm().findField('Acluser.email').setValue(obj.data.Acluser.email);
							usuarioForm.getForm().url='<?php echo $html->url('/aclusers/modificar/') ?>';
							usuarioForm.getForm().findField('Acluser.clear_password').allowBlank=true;
							usuarioForm.getForm().findField('Acluser.confirm_password').allowBlank=true;
							var agregarEditar = new Ext.Panel({
								itemId:button.id+selected.id
								,closable:true
								,title: 'Editar usuario '+selected.attributes.text
								,items:[usuarioPanel]
								,defaults:{autoScroll:true}
								,layout:'accordion'
							});
							viewPort.getComponent('center').add(agregarEditar);
							viewPort.getComponent('center').setActiveTab(button.id+selected.id);
							viewPort.getComponent('center').activeTab.openerNode=selected.id;
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
			}else
			if (selected.leaf == false){
				Ext.Ajax.request({
					url: '<?php echo $html->url('/aclgroups/groupinfo/') ?>'
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
							grupoForm.getForm().url='<?php echo $html->url('/aclgroups/modificar/') ?>';
							grupoForm.getForm().findField('Aclgroup.id').setValue(obj.data.Aclgroup.id);
							grupoForm.getForm().findField('Aclgroup.name').setValue(obj.data.Aclgroup.name);
							grupoWindow.setTitle('Editar grupo '+selected.attributes.text);
							grupoWindow.show();
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
		}else
		if (button.id=='permisos'){
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
							caller: 'Aclgroup'
							,foreign_key: selected.id
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
								str.push('caller:\'Aclgroup\'');
								str.push('foreign_key:\''+ selected.id +'\'');
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
			permisosGrid.getStore().load({params:{caller:'Aclgroup',foreign_key:selected.id}});
			permisosGrid.getSelectionModel().on('selectionchange', function(sm){
        		permisosGrid.removeBtn.setDisabled(sm.getCount() < 1);
				permisosGrid.modificarBtn.setDisabled(sm.getCount() < 1);
   			});
		} else
		if (button.id=='borrar'){
			Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el recurso?',function(btn) {
				if (btn == 'yes') {
					if (selected.leaf == false){
						var urlBorrar='<?php echo $html->url('/aclgroups/borrar/') ?>';
					}else
					if (selected.leaf == true){
						var urlBorrar='<?php echo $html->url('/aclusers/borrar/') ?>';
					}
					Ext.Ajax.request({
						url: urlBorrar
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
									if(item.id=='agregarUsuario'+selected.id){
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
			dataUrl:'<?php echo $html->url('/aclgroups/getnodes/') ?>'
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
	
	rootPermisos.load({params:{caller:'Aclgroup'}});
	
	var root = new Ext.tree.AsyncTreeNode({
		text:'Usuarios/Grupos'
		,draggable:false
		,id:'root'
		,permiso:{read:false,create:false,update:false,'delete':false,grant:false}
	});
	
	tree.setRootNode(root);
	
	var oldPosition = null;
	var oldNextSibling = null;
	
	tree.on('dblclick', function(){
	});
	
	var menucontextual = new Ext.menu.Menu({
		id: 'mainContext',
		items: [{
			text: 'Agregar usuario'
			,id: 'agregarUsuario'
			,handler:treeHandler
			,iconCls:'x-menu-item-agregarusuario'
		},{
			text: 'Agregar grupo'
			,id: 'agregarGrupo'
			,handler:treeHandler
			,iconCls:'x-menu-item-agregargrupo'
		},{
			text: 'Editar'
			,id: 'editar'
			,handler:treeHandler
			,iconCls:'x-menu-item-modificar'
		},{
			text: 'Modificar permisos'
			,id: 'permisos'
			,handler:treeHandler
			,iconCls:'x-menu-item-permisos'
		},'-',{
			text: 'Borrar'
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
		if (node.attributes.permiso['create']===true&&(node.isRoot===true||node.leaf==false)){
			menucontextual.items.items[0].enable();
		}
		if (node.attributes.permiso['create']===true&&(node.isRoot===true||node.leaf==false)){
			menucontextual.items.items[1].enable();
		}
		if (node.attributes.permiso['update']===true&&node.isRoot!==true){
			menucontextual.items.items[2].enable();
		}
		if (node.attributes.permiso['grant']===true&&node.isRoot===true){
			menucontextual.items.items[3].enable();
		}
		if (node.attributes.permiso['delete']===true&&node.isRoot!==true){
			menucontextual.items.items[5].enable();
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
		if (oldParent != newParent){
			if (node.leaf == false){
				var url = '<?php echo $html->url('/aclgroups/reparent/') ?>';
			}else{
				var url = '<?php echo $html->url('/aclusers/reparent/') ?>';
			}
			var params = {'node':node.id, 'parent':newParent.id};
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
		}
	});
	
	<?php
	$items=array(
				'north'=>array('title'=>$title_for_layout.' - '.Configure::read('Empresa.nombre'),'items'=>array('barra'))
				,'west'=>array('title'=>'Grupos y usuarios','items'=>array('tree'))

			);
	echo $ext->viewport($items);
	?>
});
</script>