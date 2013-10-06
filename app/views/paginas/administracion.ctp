<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
	Ext.EventManager.on(window, 'keydown', function(e, t) {
		if (e.getKey() == e.BACKSPACE && (!/^input$/i.test(t.tagName) || t.disabled || t.readOnly)) {
			e.stopEvent();
		}
	});
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra.js');?>
	var treeHandler = function(button,event) {
		var selected=tree.getSelectionModel().getSelectedNode();
		if (button.id=='agregar'||button.id=='editar'){
			<?php
    				echo $this->element('paginasadministracion/generalForm.js');
    				echo $this->element('paginasadministracion/opcionalForm.js');
    				echo $this->element('paginasadministracion/textoForm.js');
    				echo $this->element('paginasadministracion/multipleGrid.js');
    				echo $this->element('paginasadministracion/imagenGrid.js');
	   				echo $this->element('paginasadministracion/videoGrid.js');
    				echo $this->element('paginasadministracion/adjuntoGrid.js');
    				echo $this->element('paginasadministracion/promocionGrid.js');
    				echo $this->element('paginasadministracion/contactoForm.js');
    		?>			
			
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
			var multiplePanel = new Ext.Panel({
				title: 'Textos Multiples'
				,hidden:true
				,items:[multipleGrid]
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
			var promocionPanel = new Ext.Panel({
				title: 'Promociones'
				,hidden:true
				,items:[promocionGrid]
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
				items:[textoPanel,multiplePanel,imagenPanel,videoPanel,adjuntoPanel,promocionPanel,contactoPanel]
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
			<?php
    				echo $this->element('paginasadministracion/editarAction.js');
    		?>			
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
			<?php
    				echo $this->element('paginasadministracion/permisoGrid.js');
    		?>
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