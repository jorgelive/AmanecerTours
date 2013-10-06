			var permisosEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : !record.data.id ? '<?php echo $html->url('/aros_acos/agregarpermisos/') ?>' : '<?php echo $html->url('/aros_acos/modificarpermisos') ?>'
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
										Ext.Msg.alert('Error!', 'El servidor tuvo una respuesta nula');
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
						var newRecord = new Permiso({
							caller: 'Pagina'
							,foreign_key: selected.id
							,aro_id: ''
							,_read: false
							,_create: false
							,_update: false
							,_delete: false
						});
						permisosEditor.stopEditing();
						permisosGrid.getStore().insert(0, newRecord);
						permisosGrid.getView().refresh();
						permisosGrid.getSelectionModel().selectRow(0);
						permisosEditor.startEditing(0);
						permisosEditor.agregando=true;
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar premiso'
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
								str.push('caller:\'Pagina\'');
								str.push('foreign_key:\''+ selected.id+'\'');
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
											Ext.Msg.alert('Error!', 'El servidor tuvo una respuesta nula');
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
							,listeners:{
								'load':function(){
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