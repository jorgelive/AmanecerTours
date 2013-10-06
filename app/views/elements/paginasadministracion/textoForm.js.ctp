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
						,theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,help,"
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
										Ext.Msg.alert('Error!', 'El servidor tuvo una respuesta nula');
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