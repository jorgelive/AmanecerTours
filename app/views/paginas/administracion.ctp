<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra');?>
	var treeHandler = function(button,event) {
		var selected=tree.getSelectionModel().getSelectedNode();
		if (button.id=='agregar'||button.id=='editar'){
			var generalGrid = new Ext.grid.EditorGridPanel({
				store: new Ext.data.JsonStore({
					fields: [
						{name: 'accion', type: 'string'}
						,{name: 'valor', type: 'bool'}
					]
					,listeners:{
						'update':function(store,record,operacion){
							if(operacion==Ext.data.Record.EDIT){
								generalForm.getForm().findField(record.id).setValue(record.getChanges().valor);
							}
						}
					}
				})
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [
				{
					header: "Acción"
					,id:'accion'
					,width: 150
					,dataIndex: 'accion'
				},{
					header: "Valor"
					,dataIndex: 'valor'
					,align:'center'
					,width: 80
					,editor: new Ext.form.Checkbox({})
					,renderer: Ext.util.Format.siNoRenderer()
				}]
				,stripeRows: true
				,autoExpandColumn: 'accion'
				,clicksToEdit: 2
				,height:200
			});
			
			var generalForm = new Ext.FormPanel({ 
				xtype:'form'
				,labelAlign: 'top'
				,frame:true
				,monitorValid:true
				,items:[
				{
					xtype:'textfield'
					,fieldLabel:'Título'
					,name:'Pagina.title'
					,allowBlank:false
					,blankText:'Ingrese el titulo'
					,anchor:'100%'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginas/validar/') ?>'})]
				},{
					xtype: 'combo'
					,name: 'Pagina.predeterminado'
					,fieldLabel: 'Tipo al inicio'
					,hiddenName: 'Pagina.predeterminado'
					,anchor: '100%'
					,store: new Ext.data.JsonStore({
						autoLoad: false
						,proxy: new Ext.data.HttpProxy({
							url: 'dummypredeterminado'
							,method: 'POST'
						})
						,root: 'Tipos'
						,fields: ['id','name']
					})
					,displayField: 'name'
					,valueField: 'id'
					,typeAhead: true
					,mode: 'local'
					,triggerAction: 'all'
					,emptyText: 'Seleccione el tipo a mostrar al inicio'
					,selectOnFocus:true
					,allowBlank: true
				},{
					xtype:'panel'
					,items:[generalGrid]
				},{
					hidden:true
					,fieldLabel:'Publicar?'
					,xtype:'checkbox'
					,name:'Pagina.publicado'
					,inputValue:1
					,listeners:{
						'check':function(checkbox){
							if(checkbox.checked===true){
								generalForm.guardarBtn.setText('Publicar');
							}else{
								generalForm.guardarBtn.setText('Guardar borrador');
							}
						}
					}
				},{
					hidden:true
					,fieldLabel:'Mostrar en inicio'
					,xtype:'checkbox'
					,name:'Pagina.mostrarinicio'
					,inputValue:1
				},{
					
					hidden:true
					,fieldLabel:'Texto e imágenes?'
					,xtype:'checkbox'
					,name:'Pagina.texto'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Galería de imágenes'
					,xtype:'checkbox'
					,name:'Pagina.imagen'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Galería de videos'
					,xtype:'checkbox'
					,name:'Pagina.video'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Panel de adjuntos'
					,xtype:'checkbox'
					,name:'Pagina.adjunto'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Ofertas'
					,xtype:'checkbox'
					,name:'Pagina.oferta'
					,inputValue:1
				},{
					hidden:true
					,fieldLabel:'Formulario de contactos'
					,xtype:'checkbox'
					,name:'Pagina.contacto'
					,inputValue:1
				},{
					hidden:true
					,xtype: 'radiogroup'
					,name:'idioma'
					,allowBlank:false
					,items: [
					<?php
						$idiomas=array();
						foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
							$idiomas[]='{name: \'idioma\', inputValue:\''.$key.'\'}';
						}
						echo implode(',',$idiomas);
					?>
					]
				},{ 
					xtype:'hidden'
					,name:'Pagina.id' 
				},{ 
					xtype:'hidden'
					,name:'Pagina.parent_id' 
				}]
				,buttons:[{
					ref: '../guardarBtn'
					,formBind: true	
					,iconCls: 'x-boton-guardar'
					,text:''
					,handler:function(){
						generalForm.getForm().submit({ 
							method:'POST'
							,submitEmptyText:false
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
								var selectNode=function(value){
									tree.getNodeById(value).select();
								}
								
								selectNode(viewPort.getComponent('center').activeTab.openerNode);
								if(generalForm.getForm().url=='/paginas/modificar/'){
									var node = tree.getSelectionModel().getSelectedNode();
									if(node.id!='root'){
										node=node.parentNode;
									}
									node.on('expand',selectNode.createDelegate(this,[viewPort.getComponent('center').activeTab.openerNode]),this,{single:true});
								}else{
									viewPort.getComponent('center').activeTab.newId=obj.data.newId;
									viewPort.getComponent('center').activeTab.openerNode=obj.data.newId;
									var node=tree.getSelectionModel().getSelectedNode();
									node.on('expand',selectNode.createDelegate(this,[obj.data.newId]),this,{single:true});
								}
								node.reload();
								node.expand();
								editarAccion(['generales','texto','contacto']);
								agregarEditar.doLayout();
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
			
			var opcionalForm = new Ext.FormPanel({ 
				xtype:'form'
				,labelAlign: 'top'
				,url:'dummy'
				,frame:true
				,monitorValid:true
				,items:[{
					layout:'column'
					,defaults:{
						layout:'form'
						,border:false
						,xtype:'panel'
					}
					,items:[{
						columnWidth:0.5
						,bodyStyle:'padding:0 10px 5px 0'
						,items:[{ 
							xtype: 'datefield'
							,fieldLabel: 'Inicio'
							,name:'Paginasopcional.publicado_inicio'
							,anchor: '100%'
							,format:'d-m-Y'
							,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
							,listeners:{
								'select':function(field){
									var date = field.parseDate(field.value);
									var end = field.ownerCt.ownerCt.ownerCt.getForm().findField('Paginasopcional.publicado_final');
									end.setMinValue(date);
									end.validate();
									end.dateRangeMin = date;
								}
							}
						}]
					},{
						columnWidth:0.5
						,bodyStyle:'padding:0 0px 5px 5px'
						,items:[{ 
							xtype: 'datefield'
							,fieldLabel: 'Final'
							,name:'Paginasopcional.publicado_final'
							,anchor: '100%'
							,format:'d-m-Y'
							,invalidText : "{0} no es una fecha válida - esta debe ser en el formato dd-mm-yyyy"
							,listeners:{
								'select':function(field){
									var date = field.parseDate(field.value);
									var start = field.ownerCt.ownerCt.ownerCt.getForm().findField('Paginasopcional.publicado_inicio');
									start.setMaxValue(date);
									start.validate();
									start.dateRangeMax = date;
								}
							}
						}]
					}]
				},{
					xtype: 'combo'
					,name: 'Paginasopcional.idfoto'
					,fieldLabel: 'Imagen a mostrar'
					,hiddenName: 'Paginasopcional.idfoto'
					,anchor: '100%'
					,store: new Ext.data.JsonStore({
						autoLoad: false
						,proxy: new Ext.data.HttpProxy({
							url: 'dummycombo'
							,method: 'POST'
						})
						,root: 'Imagen'
						,fields: ['id','name']
					})
					,displayField: 'name'
					,valueField: 'id'
					,typeAhead: true
					,mode: 'local'
					,triggerAction: 'all'
					,emptyText: 'Seleccione foto a mostrar'
					,selectOnFocus:true
					,allowBlank: true
				},{ 
					xtype:'textfield'
					,fieldLabel:'Dirección fija'
					,name:'Paginasopcional.urlfija'
					,anchor: '100%'
					,allowBlank:true
					,emptyText:'Sólo para páginas corporativas'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasopcionales/validar/') ?>'})]
				},{
					xtype: "textarea"
					,name:'Paginasopcional.etiquetas'
					,fieldLabel: 'Etiquetas :'
					,allowBlank: true
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasopcionales/validar/') ?>'})]
					,anchor: '100%'
					,height:70
				},{
					xtype: 'radiogroup'
					,name:'idioma'
					,allowBlank:false
					,hidden:true
					,items: [
					<?php
						$idiomas=array();
						foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
							$idiomas[]='{name: \'idioma\', inputValue:\''.$key.'\'}';
						}
						echo implode(',',$idiomas);
					?>
					]
				},{ 
					xtype:'hidden'
					,name:'Paginasopcional.id'
				},{ 
					xtype:'hidden'
					,name:'Paginasopcional.pagina_id'
				}]
				,buttons:[{ 
					text:'Enviar',
					formBind: true,	 
					handler:function(){
						opcionalForm.getForm().submit({ 
							method:'POST'
							,submitEmptyText:false
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
								if (agregarEditar.items.length==0){
									viewPort.getComponent('center').remove(agregarEditar);
								}
								else{
									//agregarEditar.items.items[0].expand();
									editarAccion('generales');
									agregarEditar.doLayout();
								}
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
			
			var textoForm = new Ext.FormPanel({ 
				xtype:'form'
				,labelAlign: 'top'
				,url:'dummy'
				,frame:true
				,monitorValid:true
				,items:[{
					xtype: "tinymce"
					,fieldLabel: 'Contenido :'
					,blankText:'Ingrese el contenido'
					,allowBlank:false
					,tagMatters: true
					,minLength : 25
					,maxLength : 12000
					,name:'Paginastexto.contenido'
					,iHeight:400
					,iWidth:680
					,msgTarget: 'under'
					,labelSeparator: ''
					,tinymceSettings: {
						theme: "advanced"
						,relative_urls : false
						,plugins: "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imanager"
						,theme_advanced_resizing : true
						,theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,|,help,"
						,theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,imanager,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor"
						,theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|"
						,theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,|,preview"
						,theme_advanced_toolbar_location: "top"
						,theme_advanced_toolbar_align: "left"
						,theme_advanced_statusbar_location: "bottom"
						,extended_valid_elements: "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
						,content_css: "/css/tinyMCE_content.css"
						,accessibility_focus: false
						,accessibility_warnings : false
					}
				},{
					xtype: "tinymce"
					,relative_urls : false
					,name:'Paginastexto.resumen'
					,fieldLabel: 'Resumen :'
					,allowBlank: true
					,tagMatters: true
					,blankText:'Ingrese contenido'
					,minLength : 0
					,maxLengthText : 'El tamaño máximo es 300 caracteres'
					,maxLength : 300
					,iHeight:150
					,iWidth:680
					,msgTarget: 'under'
					,labelSeparator: ''
					,tinymceSettings: {
						theme: "advanced"
						,plugins: "paste,nonbreaking"
						,theme_advanced_resizing : true
						,theme_advanced_buttons1: "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,undo,redo,|cleanup,code"
						,theme_advanced_buttons2:""
						,theme_advanced_buttons3:""
						,theme_advanced_toolbar_location: "bottom"
						,theme_advanced_toolbar_align: "left"
						,theme_advanced_statusbar : false
						,extended_valid_elements: "a[name|href|target|title|onclick],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
						,content_css: "/css/tinyMCE_content.css"
						,accessibility_focus: false
						,accessibility_warnings : false
					}
					,listeners:{
						'change':function(field){
							if(field.ownerCt){
								if(field.ownerCt.resumenoriginal != field.getValue()){
									field.ownerCt.getForm().findField('Paginastexto.resumenCambio').setValue('si');
								}else{
									field.ownerCt.getForm().findField('Paginastexto.resumenCambio').setValue('no');
								}
							};
						}
					}
				},{
					xtype: 'radiogroup'
					,name:'idioma'
					,allowBlank:false
					,hidden:true
					,items: [
					<?php
						$idiomas=array();
						foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
							$idiomas[]='{name: \'idioma\', inputValue:\''.$key.'\'}';
						}
						echo implode(',',$idiomas);
					?>
					]
				},{ 
					xtype:'hidden'
					,name:'Paginastexto.id'
				},{ 
					xtype:'hidden'
					,name:'Paginastexto.pagina_id'
				},{ 
					xtype:'hidden'
					,name:'Paginastexto.resumenCambio'
				}]
				,buttons:[{ 
					text:'Enviar',
					formBind: true,	 
					handler:function(){
						textoForm.getForm().submit({ 
							method:'POST'
							,submitEmptyText:false
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
								if (agregarEditar.items.length==0){
									viewPort.getComponent('center').remove(agregarEditar);
								}
								else{
									editarAccion(['generales','texto']);
									agregarEditar.doLayout();
								}
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
			
			var imagenEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : '<?php echo $html->url('/paginasimagenes/modificar/') ?>'
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
										imagenEditor.grid.getStore().commitChanges();
										imagenEditor.grid.getView().refresh();
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
									imagenEditor.grid.getStore().rejectChanges();
									imagenEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			
			var imagenStore = new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,url: 'dummy'
				,root: 'imagenes'
				,fields: [{
					name: 'id'
				},{
					name: 'imagen'
				},{
					name: 'title'
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
							if(imagenPanel.paginatorStart&&imagenPanel.paginatorLimit){
								options.params.start=imagenPanel.paginatorStart;
								options.params.limit=imagenPanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						imagenPanel.paginatorStart=options.params.start;
						imagenPanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			
			var imagenGrid = new Ext.grid.GridPanel({
				store: imagenStore
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [new Ext.grid.RowNumberer()
				,{
					header: 'Id'
					,dataIndex: 'id'
					,width: 20
					,hidden: true
				},{
					header: 'Imagen'
					,width: 100
					,sortable: false
					,dataIndex: 'imagen'
					,align:'center'
					,editor: {disabled: true}
					,renderer:Ext.util.Format.imageRenderer(100,true)
				},{
					header: 'Titulo'
					,id:'title'
					,dataIndex: 'title'
					,width: 200
					,editor: {
						allowBlank:false
						,blankText:'Ingrese el título de la imágen'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasimagenes/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				}]
				,plugins: [imagenEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'title'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: imagenStore
					,displayMsg: 'Mostrando imágenes {0} - {1} de {2}'
					,emptyMsg: "No hay imágenes para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar imagen'
					,handler: function(){
						var agregarImagenForm = new Ext.FormPanel({ 
							fileUpload: true
							,labelWidth:70
							,url:'<?php echo $html->url('/paginasimagenes/agregar/') ?>'
							,frame:true
							,defaultType:'textfield'
							,monitorValid:true
							,items:[{ 
								xtype:'hidden'
								,name:'Paginasimagen.pagina_id'
								,value: viewPort.getComponent('center').activeTab.newId
							},{ 
								fieldLabel:'Título'
								,name:'Paginasimagen.title'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese el título'
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasimagenes/validar/') ?>'})]
							},{ 
								xtype: 'fileuploadfield'
								,emptyText: 'Selecione una imagen'
								,buttonText: 'Seleccione'
								,fieldLabel:'Foto'
								,name:'Paginasimagen.imagen'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese la imagen'
							}]
							,buttons:[{ 
								text:'Enviar'
								,formBind: true
								,handler:function(){
									agregarImagenForm.getForm().submit({ 
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
											agregarImagenForm.getForm().reset();
											imagenGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasimagenes/listar/') ?>'+viewPort.getComponent('center').activeTab.newId,true);
											imagenGrid.getStore().reload();
											imagenGrid.getView().refresh();
											editarAccion('generales');
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
						var agregarImagenWindow = new Ext.Window({
							autoScroll:true
							,title:'Agregar imagen'
							,modal:true
							,width: 330
							,height: 150
							,items:[agregarImagenForm]
							,layout: 'fit'
						}).show();
						imagenEditor.stopEditing();
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar imagen'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						imagenEditor.startEditing(imagenGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar imagen'
					,disabled: true
					,handler: function(){
						imagenEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar la imagen?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = imagenGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginasimagenes/borrar') ?>'
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
												imagenEditor.grid.getStore().remove(row);
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
											Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
										}
										imagenEditor.grid.getStore().rejectChanges();
										imagenEditor.grid.getView().refresh();
									}
								});
							}
						})
					}
				}]
			});
			imagenGrid.getSelectionModel().on('selectionchange', function(sm){
        		imagenGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				imagenGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var imagenMenuContextual = new Ext.menu.Menu({
				items: [
				{
					text: 'Modificar imagen'
					,handler:imagenGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-modificar'
				},
				'-'
				,{
					text: 'Borrar imagen'
					,handler:imagenGrid.removeBtn.handler
					,iconCls: 'x-menu-item-borrar'
				}]
			});
			
			imagenGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				imagenMenuContextual.showAt(event.getXY());
			});
			
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
										Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
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
											Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
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
			
			var adjuntoEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : '<?php echo $html->url('/paginasadjuntos/modificar/') ?>'
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
										adjuntoEditor.grid.getStore().commitChanges();
										adjuntoEditor.grid.getView().refresh();
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
									adjuntoEditor.grid.getStore().rejectChanges();
									adjuntoEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			var adjuntoStore= new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,url: 'dummy'
				,root: 'adjuntos'
				,fields: [{
					name: 'id'
				},{
					name: 'icon'
				},{
					name: 'title'
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
							if(adjuntoPanel.paginatorStart&&adjuntoPanel.paginatorLimit){
								options.params.start=adjuntoPanel.paginatorStart;
								options.params.limit=adjuntoPanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						adjuntoPanel.paginatorStart=options.params.start;
						adjuntoPanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			var adjuntoGrid = new Ext.grid.GridPanel({
				store: adjuntoStore
				,loadMask: {msg:'Cargando Datos...'} 
				,columns: [new Ext.grid.RowNumberer()
				,{
					header: 'Id'
					,dataIndex: 'id'
					,width: 40
					,hidden: true
				},{
					header: 'Tipo de archivo'
					,width: 200
					,sortable: true
					,dataIndex: 'icon'
					,align:'center'
					,editor: {disabled: true}
					,renderer:Ext.util.Format.imageRenderer()
				},{
					header: 'Título'
					,id:'title'
					,dataIndex: 'title'
					,width: 200
					,editor: {
						allowBlank:false
						,blankText:'Ingrese el título de la imágen'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasadjuntos/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				}]
				,plugins: [adjuntoEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'title'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: adjuntoStore
					,displayMsg: 'Mostrando archivos adjuntos {0} - {1} de {2}'
					,emptyMsg: "No hay archivos adjuntos para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar archivo adjunto'
					,handler: function(){
						var agregarAdjuntoForm = new Ext.FormPanel({ 
							fileUpload: true
							,labelWidth:70
							,url:'<?php echo $html->url('/paginasadjuntos/agregar/') ?>'
							,frame:true
							,defaultType:'textfield'
							,monitorValid:true
							,items:[{ 
								xtype:'hidden'
								,name:'Paginasadjunto.pagina_id'
								,value: viewPort.getComponent('center').activeTab.newId
							},{ 
								fieldLabel:'Archivo'
								,name:'Paginasadjunto.title'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese el título'
								,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasadjuntos/validar/') ?>'})]
							},{ 
								xtype: 'fileuploadfield'
								,emptyText: 'Selecione un archivo adjunto'
								,buttonText: 'Seleccione'
								,fieldLabel:'Foto'
								,name:'Paginasadjunto.adjunto'
								,width:200
								,allowBlank:false
								,blankText:'Ingrese el archivo adjunto'
							}]
							,buttons:[{ 
								text:'Enviar'
								,formBind: true
								,handler:function(){
									agregarAdjuntoForm.getForm().submit({ 
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
											agregarAdjuntoForm.getForm().reset();
											adjuntoGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasadjuntos/listar/') ?>'+viewPort.getComponent('center').activeTab.newId,true);
											adjuntoGrid.getStore().reload();
											adjuntoGrid.getView().refresh();
											editarAccion('generales');
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
						var agregarAdjuntoWindow = new Ext.Window({
							autoScroll:true
							,title:'Agregar archivo adjunto'
							,modal:true
							,width: 330
							,height: 150
							,items:[agregarAdjuntoForm]
							,layout: 'fit'
						}).show();
						adjuntoEditor.stopEditing();
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar archivo adjunto'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						adjuntoEditor.startEditing(adjuntoGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar archivo adjunto'
					,disabled: true
					,handler: function(){
						adjuntoEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar el archivo adjunto?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = adjuntoGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginasadjuntos/borrar') ?>'
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
												adjuntoEditor.grid.getStore().remove(row);
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
											Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
										}
										adjuntoEditor.grid.getStore().rejectChanges();
										adjuntoEditor.grid.getView().refresh();
									}
								});
							}
						})
					}
				}]
			});
			adjuntoGrid.getSelectionModel().on('selectionchange', function(sm){
        		adjuntoGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				adjuntoGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var adjuntoMenuContextual = new Ext.menu.Menu({
				items: [{
					text: 'Modificar archivo adjunto'
					,handler:adjuntoGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-borrar'
				},
				'-'
				,{
					text: 'Borrar archivo adjunto'
					,handler:adjuntoGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-modificar'
				}]
			});
			
			adjuntoGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				adjuntoMenuContextual.showAt(event.getXY());
			});
			
			var ofertaEditor = new Ext.ux.grid.RowEditor({
				listeners: {
                    afteredit: {
                        fn:function(roweditor, changes, record, rowIndex ){
							Ext.Ajax.request({
								url   : !record.data.id ? '<?php echo $html->url('/paginasofertas/agregar/') ?>'+viewPort.getComponent('center').activeTab.newId : '<?php echo $html->url('/paginasofertas/modificar/') ?>'
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
											if(!ofertaEditor.record.data.id){
												ofertaEditor.record.data.id=obj.data.newId;
												ofertaEditor.record.id=obj.data.newId;
											}
											editarAccion('generales');
										}
										ofertaEditor.grid.getStore().commitChanges();
										ofertaEditor.grid.getView().refresh();
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
									if(!record.data.id){
										ofertaEditor.grid.getStore().removeAt(rowIndex);
									}else{
										ofertaEditor.grid.getStore().rejectChanges()
									}
									ofertaEditor.grid.getView().refresh();
								}
							});
                        }
                    }
                }
			});
			var ofertaStore=new Ext.data.JsonStore({
				autoLoad: false
				,proxy: new Ext.data.HttpProxy({
					url: 'dummy'
					,method: 'POST'
				})
				,root: 'ofertas'
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
							if(ofertaPanel.paginatorStart&&ofertaPanel.paginatorLimit){
								options.params.start=ofertaPanel.paginatorStart;
								options.params.limit=ofertaPanel.paginatorLimit;
							}else{
								options.params.start=0;
								options.params.limit=<?php echo Configure::read('Default.paginatorSize');?>;
							}
						}
						ofertaPanel.paginatorStart=options.params.start;
						ofertaPanel.paginatorLimit=options.params.limit;
						options.params.page = Math.floor(options.params.start / options.params.limit)+1;
						options.params.pagina_id = viewPort.getComponent('center').activeTab.newId;
						options.params.idioma = viewPort.getComponent('center').activeTab.currentIdioma;
						return true;
					}
				}
			})
			
			var ofertaGrid = new Ext.grid.GridPanel({
				store: ofertaStore
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
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasofertas/validar/') ?>'})]
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
								ofertaEditor.verifyLayout.defer(100,ofertaEditor);
								this.onResize(ofertaGrid.getColumnModel().getColumnWidth(4),200)
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
								ofertaEditor.verifyLayout.defer(100,ofertaEditor);
								this.onResize(ofertaGrid.getColumnModel().getColumnWidth(5),200)
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
						allowBlank:false
						,blankText:'Ingrese el precio'
						,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginasofertas/validar/') ?>'})]
					}
					,sortable:true
					,filter:true
				}]
				,plugins: [ofertaEditor,new Ext.ux.grid.FilterRow()]
				,stripeRows: true
				,autoExpandColumn: 'notas'
				,bbar: new Ext.PagingToolbar({
					pageSize: <?php echo Configure::read('Default.paginatorSize');?>
					,displayInfo: true
					,filter:true
					,store: ofertaStore
					,displayMsg: 'Mostrando ofertas {0} - {1} de {2}'
					,emptyMsg: "No hay ofertas para mostrar"
				})
				,tbar: [{
					ref: '../agregarBtn'
					,iconCls: 'x-boton-agregar'
					,text: 'Agregar oferta'
					,handler: function(){
						var Oferta = Ext.data.Record.create([
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
						var newRecord = new Oferta({
							pagina_id:viewPort.getComponent('center').activeTab.newId
							,title: ''
							,inicio: ''
							,final: ''
							,notas: ''
							,condiciones: ''
							,precio: ''
						});
						ofertaEditor.stopEditing();
						ofertaGrid.getStore().insert(0, newRecord);
						ofertaGrid.getView().refresh();
						ofertaGrid.getSelectionModel().selectRow(0);
						ofertaEditor.startEditing(0);
						ofertaEditor.agregando=true;
					}
				},{
					ref: '../modificarBtn'
					,text: 'Modificar oferta'
					,iconCls: 'x-boton-modificar'
					,disabled: true
					,handler: function(){
						ofertaEditor.startEditing(ofertaGrid.getSelectionModel().getSelections()[0]);
					}
				},{
					ref: '../removeBtn'
					,iconCls: 'x-boton-borrar'
					,text: 'Borrar oferta'
					,disabled: true
					,handler: function(){
						ofertaEditor.stopEditing();
						Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar la oferta?',function(btn) {
							if (btn == 'yes') {
								var selectedRows = ofertaGrid.getSelectionModel().getSelections();
								var str = [];
								for(var i = 0, row; row = selectedRows[i]; i++){
									str.push('row'+i+':'+ selectedRows[i].id);
								}
								str.push('pagina_id:'+ viewPort.getComponent('center').activeTab.openerNode);
								var string = '{'+str.join(',')+'}';
								var rowIds = eval('('+string+')');
								Ext.Ajax.request({
									url   : '<?php echo $html->url('/paginasofertas/borrar') ?>'
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
												ofertaEditor.grid.getStore().remove(row);
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
											Ext.Msg.alert('Errors!', 'El servidor tuvo una respuesta nula');
										}
									}
								});
							}
						})
					}
				}]
			});
			ofertaGrid.getSelectionModel().on('selectionchange', function(sm){
        		ofertaGrid.modificarBtn.setDisabled(sm.getCount() < 1);
				ofertaGrid.removeBtn.setDisabled(sm.getCount() < 1);
   			});
			
			var ofertaMenuContextual = new Ext.menu.Menu({
				items: [{
					text: 'Modificar oferta'
					,handler:ofertaGrid.modificarBtn.handler
					,iconCls: 'x-menu-item-modificar'
				},
				'-'
				,{
					text: 'Borrar oferta'
					,handler:ofertaGrid.removeBtn.handler
					,iconCls: 'x-menu-item-borrar'
				}]
			});
			
			ofertaGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
				grid.getSelectionModel().selectRow(rowIndex);
				event.stopEvent();
				ofertaMenuContextual.showAt(event.getXY());
			});
			
			var contactoForm = new Ext.FormPanel({ 
				labelAlign: 'top'
				,url:'dummy'
				,frame:true
				,defaultType:'textfield'
				,monitorValid:true
				,items:[{ 
					xtype:'hidden'
					,name:'Paginascontacto.id'
				},{ 
					xtype:'hidden'
					,name:'Paginascontacto.pagina_id'
				},{ 
					fieldLabel:'Destinatario'
					,name:'Paginascontacto.destinatario'
					,width:200
					,allowBlank:false
					,emptyText:'<?php echo Configure::read('Default.email');?>'
					,blankText:'Ingrese destinatario'
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascontactos/validar/') ?>'})]
				},{ 
					fieldLabel:'CCO'
					,name:'Paginascontacto.cco'
					,emptyText:'<?php echo Configure::read('Default.cco');?>'
					,width:200
					,allowBlank:true
					,plugins:[new Ext.ux.form.ServerValidator({url:'<?php echo $html->url('/paginascontactos/validar/') ?>'})]
				}]
				,buttons:[{ 
					text:'Enviar'
					,formBind: true	 
					,handler:function(){
						contactoForm.getForm().submit({ 
							method:'POST'
							,waitTitle:'Conectando'
							,submitEmptyText:false
							,waitMsg:'Enviando información...'
							,success:function(form, respuesta){ 
								obj = Ext.util.JSON.decode(respuesta.response.responseText);
								if(obj.hasOwnProperty('message')){
									Ext.Msg.alert('Correcto!', obj.message);
								}
								if(obj.hasOwnProperty('redirect')){
									window.location = obj.redirect;
								}
								if (agregarEditar.items.length==0){
									viewPort.getComponent('center').remove(agregar);
								}
								else{
									editarAccion(['generales','contacto']);
									agregarEditar.doLayout();
								}
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
			
			var generalPanel = new Ext.Panel({
				title: 'Información general'
				,items:[generalForm]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var opcionalPanel = new Ext.Panel({
				title: 'Información opcional'
				,hidden:true
				,items:[opcionalForm]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var textoPanel = new Ext.Panel({
				title: 'Texto e imágenes'
				,hidden:true
				,items:[textoForm]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var imagenPanel = new Ext.Panel({
				title: 'Galería de imágenes'
				,hidden:true
				,items:[imagenGrid]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var videoPanel = new Ext.Panel({
				title: 'Galería de videos'
				,hidden:true
				,items:[videoGrid]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var adjuntoPanel = new Ext.Panel({
				title: 'Panel de archivos adjuntos'
				,hidden:true
				,items:[adjuntoGrid]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var ofertaPanel = new Ext.Panel({
				title: 'Ofertas'
				,hidden:true
				,items:[ofertaGrid]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var contactoPanel = new Ext.Panel({
				title: 'Información de contacto'
				,hidden:true
				,items:[contactoForm]
				,layout:'fit'
				,defaults:{autoScroll:true}
			});
			var dependientesPanel = new Ext.Panel({
				items:[textoPanel,imagenPanel,videoPanel,adjuntoPanel,ofertaPanel,contactoPanel]
				,defaults:{autoScroll:true}
				,region	:'center'
				,layout:'accordion'
			});
			var principalPanel = new Ext.Panel({
				region :'west'
				,width:280
				,split:true
				,collapsible:true
				,items:[generalPanel,opcionalPanel]
				,defaults:{autoScroll:true,autoLayout:true}
				,layout:'accordion'
			});
			var agregarEditar = new Ext.Panel({
				itemId:button.id+selected.id
				,layout:'border'
				,closable:true
				,items:[principalPanel,dependientesPanel]
				,defaults:{autoScroll:true}
				,currentIdioma:'<?php echo Configure::read('Empresa.language');?>'
			});
			
			viewPort.getComponent('center').add(agregarEditar);
			generalForm.getForm().findField('idioma').setValue('<?php echo Configure::read('Empresa.language');?>');
			opcionalForm.getForm().findField('idioma').setValue('<?php echo Configure::read('Empresa.language');?>');
			textoForm.getForm().findField('idioma').setValue('<?php echo Configure::read('Empresa.language');?>');
			
			var generalGridDatos = function(formulario) {
				var armazonRecord = Ext.data.Record.create([
					{name: "accion"} 
					,{name: 'valor'}
				]);
				if(formulario.items.each){
					formulario.items.each(function(item, index, length) {
						if (item instanceof Ext.form.Field){
							if(item.name&&item.fieldLabel&&item.hidden===true&&item.xtype=='checkbox'){
								var recordData = new armazonRecord({
									accion: item.fieldLabel
									,valor: item.getValue()
								},item.name);
								if(generalGrid.getStore().indexOfId(item.name)<0){
									generalGrid.getStore().add(recordData);
								}else{
									generalGrid.getStore().getById(item.name).set('valor',item.getValue());
								}
							}
						}
						if (item.items){
							generalGridDatos(item);
						}
					});
					generalGrid.getView().refresh();
					generalGrid.getStore().commitChanges();
				}
				
			}	
			
			var editarAccion=function(parcial){
				if(typeof(parcial)=='object'){
					//nada
				}else if(typeof(parcial)=='string'){
					parcial=[parcial];
				}else if(typeof(parcial)=='boolean'){
					if(parcial===true){
						parcial=['todo']
					}else{
						parcial=['generales']
					}
				}else{
					parcial=['generales'];
				}
				Ext.Ajax.request({
					url: '<?php echo $html->url('/paginas/paginainfo/') ?>'
					,method: 'POST'
					,params: {id:viewPort.getComponent('center').activeTab.newId,idioma:viewPort.getComponent('center').activeTab.currentIdioma}
					,success: function(respuesta,request) {
						obj = Ext.util.JSON.decode(respuesta.responseText);
						<?php
							$strings=array();
							foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
								$strings[]='if(viewPort.getComponent(\'center\').activeTab.currentIdioma==\''.$key.'\'){var idiomaName=\''.$nombre.'\';}';
							}
							echo implode('else ',$strings);
						?>
						if(viewPort.getComponent('center').activeTab.currentIdioma=='en-us'){var idiomaName='Inglés';}else
						if(viewPort.getComponent('center').activeTab.currentIdioma=='es-es'){var idiomaName='Español';}
						if (obj.success){
							if(obj.hasOwnProperty('message')){
								Ext.Msg.alert('Correcto!', obj.message);
							}
							if(obj.hasOwnProperty('redirect')){
								window.location = obj.redirect;
							}
							
							agregarEditar.setTitle('Editar página '+obj.data.Pagina.title);
							<?php
								foreach (Configure::read('Empresa.languageList') as $key=>$nombre){
									?>
									generalPanel.addTool({id:'<?php echo $key;?>'
										,handler: function(){
											viewPort.getComponent('center').activeTab.currentIdioma='<?php echo $key;?>';
											generalForm.getForm().findField('idioma').setValue('<?php echo $key;?>');
											opcionalForm.getForm().findField('idioma').setValue('<?php echo $key;?>');
											textoForm.getForm().findField('idioma').setValue('<?php echo $key;?>');
											editarAccion('todo');
										}
									});
									<?php
								}
							?>
							if (in_array('generales',parcial)||in_array('todo',parcial)){
								generalForm.getForm().findField('Pagina.id').setValue(obj.data.Pagina.id);
								generalForm.getForm().findField('Pagina.title').setValue(obj.data.Pagina.title);
								generalForm.getForm().findField('Pagina.publicado').setValue(obj.data.Pagina.publicado);
								if(obj.data.Pagina.publicado==1){
									generalForm.guardarBtn.setText('Publicar');
								}else{
									generalForm.guardarBtn.setText('Guardar borrador');
								}
								generalForm.getForm().findField('Pagina.mostrarinicio').setValue(obj.data.Pagina.mostrarinicio);
								generalForm.getForm().findField('Pagina.texto').setValue(obj.data.Pagina.texto);
								generalForm.getForm().findField('Pagina.imagen').setValue(obj.data.Pagina.imagen);
								generalForm.getForm().findField('Pagina.video').setValue(obj.data.Pagina.video);
								generalForm.getForm().findField('Pagina.adjunto').setValue(obj.data.Pagina.adjunto);
								generalForm.getForm().findField('Pagina.oferta').setValue(obj.data.Pagina.oferta);
								generalForm.getForm().findField('Pagina.contacto').setValue(obj.data.Pagina.contacto);
								
								generalGridDatos(generalForm.getForm());//llenamos el grid
								
								generalForm.getForm().findField('Pagina.predeterminado').setValue(obj.data.Pagina.predeterminado);
								generalForm.getForm().findField('Pagina.predeterminado').getStore().proxy.setUrl('<?php echo $html->url('/paginas/listadotipos') ?>',true);
								generalForm.getForm().findField('Pagina.predeterminado').getStore().load({params:{id:obj.data.Pagina.id}});
								
								generalForm.getForm().url='<?php echo $html->url('/paginas/modificar/') ?>';
								generalPanel.setTitle('Información general ('+idiomaName+')');
								if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
									generalForm.getForm().findField('Pagina.title').allowBlank=true;
								}else{
									generalForm.getForm().findField('Pagina.title').allowBlank=false;
								}
								
								if(obj.data.Paginasopcional.id){
									opcionalForm.getForm().findField('Paginasopcional.id').setValue(obj.data.Paginasopcional.id);
									opcionalForm.getForm().findField('Paginasopcional.pagina_id').setValue(obj.data.Paginasopcional.pagina_id);
									if(obj.data.Paginasopcional.publicado_inicio!='0000-00-00'){
										opcionalForm.getForm().findField('Paginasopcional.publicado_inicio').setValue(obj.data.Paginasopcional.publicado_inicio);
									}
									if(obj.data.Paginasopcional.publicado_final!='0000-00-00'){
										opcionalForm.getForm().findField('Paginasopcional.publicado_final').setValue(obj.data.Paginasopcional.publicado_final);
									}
									opcionalForm.getForm().findField('Paginasopcional.idfoto').setValue(obj.data.Paginasopcional.idfoto);
									opcionalForm.getForm().findField('Paginasopcional.etiquetas').setValue(obj.data.Paginasopcional.etiquetas);
									opcionalForm.getForm().findField('Paginasopcional.urlfija').setValue(obj.data.Paginasopcional.urlfija);
									opcionalForm.getForm().url='<?php echo $html->url('/paginasopcionales/modificar/') ?>';
									opcionalPanel.setTitle('Información opcional ('+idiomaName+')');
								}else{
									opcionalForm.getForm().url='<?php echo $html->url('/paginasopcionales/agregar/') ?>';
									opcionalForm.getForm().findField('Paginasopcional.pagina_id').setValue(obj.data.Pagina.id);
									opcionalForm.getForm().findField('Paginasopcional.id').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.publicado_inicio').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.publicado_final').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.idfoto').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.etiquetas').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.urlfija').setValue('');
									
									
								}
								opcionalForm.getForm().findField('Paginasopcional.idfoto').getStore().proxy.setUrl('<?php echo $html->url('/paginas/listadofotos') ?>',true);
								opcionalForm.getForm().findField('Paginasopcional.idfoto').getStore().load({params:{id:obj.data.Pagina.id}});
							}
							
							if (in_array('texto',parcial)||in_array('todo',parcial)){
								if(obj.data.Paginastexto.id){
									textoForm.getForm().findField('Paginastexto.id').setValue(obj.data.Paginastexto.id);
									textoForm.getForm().findField('Paginastexto.pagina_id').setValue(obj.data.Paginastexto.pagina_id);
									textoForm.getForm().findField('Paginastexto.contenido').setValue(obj.data.Paginastexto.contenido);
									
									textoForm.getForm().findField('Paginastexto.resumen').setValue(obj.data.Paginastexto.resumen);
									textoForm.resumenoriginal=obj.data.Paginastexto.resumen;
									textoForm.getForm().findField('Paginastexto.resumenCambio').setValue('no');
									textoForm.getForm().url='<?php echo $html->url('/paginastextos/modificar/') ?>';
									textoForm.getForm().findField('Paginastexto.contenido').allowBlank=true;
									textoForm.getForm().findField('Paginastexto.contenido').minLength=0;
								}else{
									textoForm.getForm().url='<?php echo $html->url('/paginastextos/agregar/') ?>';
									textoForm.getForm().findField('Paginastexto.pagina_id').setValue(obj.data.Pagina.id);
									textoForm.getForm().findField('Paginastexto.id').setValue('');
									textoForm.getForm().findField('Paginastexto.contenido').setValue('');
									textoForm.getForm().findField('Paginastexto.resumen').setValue('');
									
									textoForm.resumenoriginal='';
									if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
										textoForm.getForm().findField('Paginastexto.contenido').allowBlank=true;
										textoForm.getForm().findField('Paginastexto.contenido').minLength=0;
									}else{
										textoForm.getForm().findField('Paginastexto.contenido').allowBlank=false;
										textoForm.getForm().findField('Paginastexto.contenido').minLength=25;
									}
									
								}
							}
							
							if (in_array('contacto',parcial)||in_array('todo',parcial)){
								if(obj.data.Paginascontacto.id){
									contactoForm.getForm().findField('Paginascontacto.id').setValue(obj.data.Paginascontacto.id);
									contactoForm.getForm().findField('Paginascontacto.pagina_id').setValue(obj.data.Paginascontacto.pagina_id);
									contactoForm.getForm().findField('Paginascontacto.destinatario').setValue(obj.data.Paginascontacto.destinatario);
									contactoForm.getForm().findField('Paginascontacto.cco').setValue(obj.data.Paginascontacto.cco);
									contactoForm.getForm().url='<?php echo $html->url('/paginascontactos/modificar/') ?>';
								}else{
									contactoForm.getForm().url='<?php echo $html->url('/paginascontactos/agregar/') ?>';
									contactoForm.getForm().findField('Paginascontacto.pagina_id').setValue(obj.data.Pagina.id);
									contactoForm.getForm().findField('Paginascontacto.id').setValue('');
									contactoForm.getForm().findField('Paginascontacto.destinatario').setValue('');
									contactoForm.getForm().findField('Paginascontacto.cco').setValue('');
								}
							}
							if (in_array('imagen',parcial)||in_array('todo',parcial)){
								imagenGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasimagenes/listar') ?>',true);
								imagenGrid.getStore().load();
								if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
									imagenGrid.getColumnModel().config[3].editor.allowBlank=true;
									imagenGrid.agregarBtn.disable();
								}else{
									imagenGrid.getColumnModel().config[3].editor.allowBlank=false;
									imagenGrid.agregarBtn.enable();
								}
							}
							
							if (in_array('video',parcial)||in_array('todo',parcial)){
								videoGrid.getColumnModel().config[2].editor.getStore().proxy.setUrl('<?php echo $html->url('/paginasvideos/listarfuentes') ?>',true);
								videoGrid.getColumnModel().config[2].editor.getStore().load();
								videoGrid.getColumnModel().config[2].editor.getStore().on('load',function(){
									videoGrid.getColumnModel().config[2].renderer=Ext.util.Format.comboRenderer(videoGrid.getColumnModel().config[2].editor);
									videoGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasvideos/listar') ?>',true);
									
									videoGrid.getStore().load();
								})
								//los videos se cargan en el listener del combo
								if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
									videoGrid.getColumnModel().config[3].editor.allowBlank=true;
									videoGrid.agregarBtn.disable();
								}else{
									videoGrid.getColumnModel().config[3].editor.allowBlank=false;
									videoGrid.agregarBtn.enable();
								}	
							}
							
							if (in_array('adjunto',parcial)||in_array('todo',parcial)){
								adjuntoGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasadjuntos/listar') ?>',true);
								adjuntoGrid.getStore().load();
								if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
									adjuntoGrid.getColumnModel().config[3].editor.allowBlank=true;
									adjuntoGrid.agregarBtn.disable();
								}else{
									adjuntoGrid.getColumnModel().config[3].editor.allowBlank=false;
									adjuntoGrid.agregarBtn.enable();
								}
							}
							
							if (in_array('oferta',parcial)||in_array('todo',parcial)){
								ofertaGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasofertas/listar') ?>',true);
								ofertaGrid.getStore().load();
								if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
									ofertaGrid.getColumnModel().config[4].editor.allowBlank=true;
									ofertaGrid.getColumnModel().config[5].editor.allowBlank=true;
									ofertaGrid.agregarBtn.disable();
								}else{
									ofertaGrid.getColumnModel().config[4].editor.allowBlank=false;
									ofertaGrid.getColumnModel().config[5].editor.allowBlank=false;
									ofertaGrid.agregarBtn.enable();
								}
							}
							
							
							if (in_array('generales',parcial)||in_array('todo',parcial)){
								opcionalPanel.show();
								if(obj.data.Pagina.texto==1){
									textoPanel.show();
								}else if(obj.data.Pagina.texto==0){
									textoPanel.hide();
								}
								if(obj.data.Pagina.contacto==1){
									contactoPanel.show();
								}else if(obj.data.Pagina.contacto==0){
									contactoPanel.hide();
								}
								if(obj.data.Pagina.imagen==1){
									imagenPanel.show();
								}else if(obj.data.Pagina.imagen==0){
									imagenPanel.hide();
								}
								if(obj.data.Pagina.video==1){
									videoPanel.show();
								}else if(obj.data.Pagina.video==0){
									videoPanel.hide();
								}
								if(obj.data.Pagina.adjunto==1){
									
									adjuntoPanel.show();
								}else if(obj.data.Pagina.adjunto==0){
									adjuntoPanel.hide();
								}
								if(obj.data.Pagina.oferta==1){
									
									ofertaPanel.show();
								}else if(obj.data.Pagina.oferta==0){
									ofertaPanel.hide();
								}
							}
							agregarEditar.doLayout();
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
		}
		if (button.id=='agregar'){
			generalForm.getForm().findField('Pagina.parent_id').setValue(selected.id);
			//generalForm.getForm().findField('Pagina.publicado').setValue(1);
			generalForm.getForm().findField('Pagina.predeterminado').getStore().proxy.setUrl('<?php echo $html->url('/paginas/listadotipos') ?>',true);
			generalForm.guardarBtn.setText('Guardar Borrador');
			generalForm.getForm().url='<?php echo $html->url('/paginas/agregar/') ?>';
			agregarEditar.setTitle('Agregar página en '+selected.attributes.text)
			viewPort.getComponent('center').setActiveTab(button.id+selected.id);
			viewPort.getComponent('center').activeTab.currentIdioma='<?php echo Configure::read('Empresa.language');?>';
			viewPort.getComponent('center').activeTab.openerNode=selected.id;
			generalGridDatos(generalForm.getForm());
		} else
		if (button.id=='editar'){
			viewPort.getComponent('center').setActiveTab(button.id+selected.id);
			viewPort.getComponent('center').activeTab.currentIdioma='<?php echo Configure::read('Empresa.language');?>';
			viewPort.getComponent('center').activeTab.newId=selected.id;
			viewPort.getComponent('center').activeTab.openerNode=selected.id;
			editarAccion('todo');
		} else
		if (button.id=='permisos'){
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
			
			var permisosWindow = new Ext.Window({
				autoScroll:true
				,title:'Permisos'
				,modal:true
				,width: 600
				,height: 400
				,items:[permisosGrid]
				,layout: 'fit'
			}).show();
			
			permisosGrid.getStore().load({params:{caller:'Pagina',foreign_key:selected.id}});
			permisosGrid.getSelectionModel().on('selectionchange', function(sm){
        		permisosGrid.removeBtn.setDisabled(sm.getCount() < 1);
				permisosGrid.modificarBtn.setDisabled(sm.getCount() < 1);
   			});
		}else
		if (button.id=='borrar'){
			Ext.MessageBox.confirm('Confirme', 'Esta seguro que desea borrar la página?',function(btn) {
				if (btn == 'yes') {
					Ext.Ajax.request({
						url: '<?php echo $html->url('/paginas/borrar/') ?>'
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
									if(item.newId==selected.id){
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
	
	var tree = new Ext.tree.TreePanel({
		autoScroll:true
		,animate:true
		,enableDD:true
		,containerScroll: true
		,rootVisible: true
		,loader: new Ext.tree.TreeLoader({
			dataUrl:'<?php echo $html->url('/paginas/getnodes/') ?>'
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
	})
	
	rootPermisos.load({params:{caller:'Pagina'}});
	
	var root = new Ext.tree.AsyncTreeNode({
		text:'Paginas'
		,draggable:false
		,id:'root'
		,permiso:{read:false,create:false,update:false,'delete':false,grant:false}
	});
	
	<?php
	if (isset($parents)&&!empty($parents)){
	?>
	root.on('expand',function(){
		var parent=new Array();
		<?php
			foreach($parents as $key=>$parent):
				echo 'parent['.$key.']='.$parent.';';
			endforeach;
		?>
		var editPagina=function(parent,nodo){
			
			if(parent[nodo+1]){
				
				tree.getNodeById(parent[nodo]).on('expand',editPagina.createDelegate(this,[parent,nodo+1]),this,{single:true});
				tree.getNodeById(parent[nodo]).expand();
			}else{
				tree.getNodeById(parent[nodo]).select();
				accion=new Object;
				accion.id='editar';
				treeHandler(accion);
			}
		};
		editPagina(parent,0)
	});
	<?php
	}
	?>
	
	tree.setRootNode(root);
	
	var oldPosition = null;
	var oldNextSibling = null;
	
	tree.on('dblclick', function(){
	});
	
	var menucontextual = new Ext.menu.Menu({
		items: [{
			text: 'Agregar página'
			,id: 'agregar'
			,handler:treeHandler
			,iconCls:'x-menu-item-agregar'
		},{
			text: 'Editar página'
			,id: 'editar'
			,handler:treeHandler
			,iconCls:'x-menu-item-modificar'
		},{
			text: 'Modificar permisos'
			,id: 'permisos'
			,handler:treeHandler
			,iconCls:'x-menu-item-permisos'
		},'-',{
			text: 'Borrar página'
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
		if (node.attributes.permiso['create']===true){
			menucontextual.items.items[0].enable();
		}
		if (node.attributes.permiso['update']===true&&node.leaf===false){
			menucontextual.items.items[1].enable();
		}
		if (node.attributes.permiso['grant']===true){
			menucontextual.items.items[2].enable();
		}
		if (node.attributes.permiso['delete']===true&&node.leaf===false){
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
			var url = '<?php echo $html->url('/paginas/reorder/') ?>';
			var params = {'node':node.id, 'delta':(position-oldPosition)};
		}else {
			var url = '<?php echo $html->url('/paginas/reparent/') ?>';
			var params = {'node':node.id, 'parent':newParent.id, 'position':position};
		}
		tree.disable();
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
					tree.enable();
				}else{
					request.failure();
				}
			}
			,failure:function(){
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
				,'west'=>array('title'=>'Paginas','items'=>array('tree'),'width'=>250)
			);
	echo $ext->viewport($items);
	?>
});
</script>