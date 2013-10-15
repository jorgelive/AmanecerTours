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
								generalForm.getForm().findField('Pagina.multiple').setValue(obj.data.Pagina.multiple);
								generalForm.getForm().findField('Pagina.imagen').setValue(obj.data.Pagina.imagen);
								generalForm.getForm().findField('Pagina.video').setValue(obj.data.Pagina.video);
								generalForm.getForm().findField('Pagina.adjunto').setValue(obj.data.Pagina.adjunto);
								generalForm.getForm().findField('Pagina.promocion').setValue(obj.data.Pagina.promocion);
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
									opcionalForm.getForm().findField('Paginasopcional.imagenpath').setValue(obj.data.Paginasopcional.imagenpath);
									opcionalForm.getForm().findField('Paginasopcional.etiquetas').setValue(obj.data.Paginasopcional.etiquetas);
									opcionalForm.getForm().findField('Paginasopcional.duracion').setValue(obj.data.Paginasopcional.duracion);
									opcionalForm.getForm().findField('Paginasopcional.urlfija').setValue(obj.data.Paginasopcional.urlfija);
                                    opcionalForm.getForm().url='<?php echo $html->url('/paginasopcionales/modificar/') ?>';
									opcionalPanel.setTitle('Información opcional ('+idiomaName+')');
								}else{
									opcionalForm.getForm().url='<?php echo $html->url('/paginasopcionales/agregar/') ?>';
									opcionalForm.getForm().findField('Paginasopcional.pagina_id').setValue(obj.data.Pagina.id);
									opcionalForm.getForm().findField('Paginasopcional.id').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.publicado_inicio').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.publicado_final').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.imagenpath').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.etiquetas').setValue('');
									opcionalForm.getForm().findField('Paginasopcional.urlfija').setValue('');
                                    opcionalForm.getForm().findField('Paginasopcional.duracion').setValue('');
									
									
								}
								opcionalForm.getForm().findField('Paginasopcional.imagenpath').getStore().proxy.setUrl('<?php echo $html->url('/paginas/listadofotos') ?>',true);
								opcionalForm.getForm().findField('Paginasopcional.imagenpath').getStore().load({params:{id:obj.data.Pagina.id}});
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
							
							if (in_array('multiple',parcial)||in_array('todo',parcial)){
								multipleGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasmultiples/listar') ?>',true);
								multipleGrid.getStore().load();
								if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
									multipleGrid.getColumnModel().config[3].editor.allowBlank=true;
									multipleGrid.agregarBtn.disable();
								}else{
									multipleGrid.getColumnModel().config[3].editor.allowBlank=false;
									multipleGrid.agregarBtn.enable();
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

							        //videoGrid.getStore().load();
								})
                                    videoGrid.getColumnModel().config[2].renderer=Ext.util.Format.comboRenderer(videoGrid.getColumnModel().config[2].editor);
                                    videoGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginasvideos/listar') ?>',true);
                                    videoGrid.getStore().load();
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
							
							if (in_array('promocion',parcial)||in_array('todo',parcial)){
								promocionGrid.getStore().proxy.setUrl('<?php echo $html->url('/paginaspromociones/listar') ?>',true);
								promocionGrid.getStore().load();
								if(viewPort.getComponent('center').activeTab.currentIdioma != '<?php echo Configure::read('Empresa.language');?>'){
									promocionGrid.getColumnModel().config[4].editor.allowBlank=true;
									promocionGrid.getColumnModel().config[5].editor.allowBlank=true;
									promocionGrid.agregarBtn.disable();
								}else{
									promocionGrid.getColumnModel().config[4].editor.allowBlank=false;
									promocionGrid.getColumnModel().config[5].editor.allowBlank=false;
									promocionGrid.agregarBtn.enable();
								}
							}
							
							
							if (in_array('generales',parcial)||in_array('todo',parcial)){
								opcionalPanel.show();
								if(obj.data.Pagina.texto==1){
									textoPanel.show();
								}else if(obj.data.Pagina.texto==0){
									textoPanel.hide();
								}
								if(obj.data.Pagina.multiple==1){
									multiplePanel.show();
								}else if(obj.data.Pagina.multiple==0){
									multiplePanel.hide();
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
								if(obj.data.Pagina.promocion==1){
									
									promocionPanel.show();
								}else if(obj.data.Pagina.promocion==0){
									promocionPanel.hide();
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
							Ext.Msg.alert('Error!', 'El servidor tuvo una respuesta nula');
						}
					}
				});
			}