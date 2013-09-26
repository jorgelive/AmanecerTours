// JavaScript Document
Ext.override(Ext.PagingToolbar, {
	clearFiltersText : 'Page'
	,initComponent : function(){
        var T = Ext.Toolbar;
		var pagingItems = [this.first = new T.Button({
            tooltip: this.firstText,
            overflowText: this.firstText,
            iconCls: 'x-tbar-page-first',
            disabled: true,
            handler: this.moveFirst,
            scope: this
        }), this.prev = new T.Button({
            tooltip: this.prevText,
            overflowText: this.prevText,
            iconCls: 'x-tbar-page-prev',
            disabled: true,
            handler: this.movePrevious,
            scope: this
        }), '-', this.beforePageText,
        this.inputItem = new Ext.form.NumberField({
            cls: 'x-tbar-page-number',
            allowDecimals: false,
            allowNegative: false,
            enableKeyEvents: true,
            selectOnFocus: true,
            submitValue: false,
            listeners: {
                scope: this,
                keydown: this.onPagingKeyDown,
                blur: this.onPagingBlur
            }
        }), this.afterTextItem = new T.TextItem({
            text: String.format(this.afterPageText, 1)
        }), '-', this.next = new T.Button({
            tooltip: this.nextText,
            overflowText: this.nextText,
            iconCls: 'x-tbar-page-next',
            disabled: true,
            handler: this.moveNext,
            scope: this
        }), this.last = new T.Button({
            tooltip: this.lastText,
            overflowText: this.lastText,
            iconCls: 'x-tbar-page-last',
            disabled: true,
            handler: this.moveLast,
            scope: this
        }), '-', this.refresh = new T.Button({
            tooltip: this.refreshText,
            overflowText: this.refreshText,
            iconCls: 'x-tbar-loading',
            handler: this.doRefresh,
            scope: this
        })];

        var userItems = this.items || this.buttons || [];
        if (this.prependButtons) {
            this.items = userItems.concat(pagingItems);
        }else{
            this.items = pagingItems.concat(userItems);
        }
        delete this.buttons;
		if(this.filter){
			this.items.push(this.filterItem = new T.Button({
				tooltip: 'Filtros'
            	,overflowText: 'Filtros'
            	,iconCls: 'x-tbar-filtro'
				,handler: this.showFilters
				,scope: this
			
			}));
        	this.items.push(this.clearFilterItem = new T.Button({
				tooltip: 'Limpiar filtos'
            	,overflowText: 'Limpiar filtros'
            	,iconCls: 'x-tbar-borrarfiltro'
				,handler: this.cleanFilters
				,scope: this
			}));
		}
        if(this.displayInfo){
            this.items.push('->');
            this.items.push(this.displayItem = new T.TextItem({}));
        }
        Ext.PagingToolbar.superclass.initComponent.call(this);
        this.addEvents(
            'change'
            ,'beforechange'
			,'closeFilterWindow'
        );
        this.on('afterlayout', this.onFirstLayout, this, {single: true});
        this.cursor = 0;
        this.bindStore(this.store, true);
    }
	,showFilters : function(){
		var paginationBar=this;
		var fieldData=new Array();
		var j=0;
		for(var i in this.ownerCt.colModel.config){
			if (this.ownerCt.colModel.config[i].dataIndex && this.ownerCt.colModel.config[i].filter){
				fieldData[j]=new Array();
				fieldData[j].push(this.ownerCt.colModel.config[i].dataIndex);
				fieldData[j].push(this.ownerCt.colModel.config[i].header);
				j++;
			
			}
		}
		
		var fieldDataJson=eval(Ext.util.JSON.encode(fieldData));
		
		var fieldStore = new Ext.data.ArrayStore({
			fields: ['field', 'name']
			,listeners:{
				'load':function(){
					filtrosGrid.getColumnModel().getColumnById('campo').renderer=Ext.util.Format.comboRenderer(filtrosGrid.getColumnModel().getColumnById('campo').getEditor());
					filtrosGrid.getColumnModel().getColumnById('operador').renderer=Ext.util.Format.arrayRenderer();
				}
			}
		});
		
		var numberDateOperatorData = [['','Sea igual a'],[' >','Mayor que'],[' <','Menor que'],[' >=','Mayor o igual que'],[' <=','Menor o igual que']];
		var booleanOperatorData = [['','Sea igual a']];
		var defaultOperatorData = [[' LIKE','Que contenga'],['','Sea igual a']];
		var fechaEditor = new Ext.form.DateField({
			format: 'd m Y', // 'd mmm yyyy' is not valid, but I assume this is close?
		});
		var booleanEditor =	new Ext.form.Checkbox({});
		var defaultEditor =	new Ext.form.Field({});

		Ext.util.Format.arrayRenderer = function(){
			return function(value){
				var array= new Array();
				array['']='Sea igual a';
				array[' LIKE']='Que contenga';
				array[' >']='Mayor que';
				array[' <']='Menor que';
				array[' >=']='Mayor o igual que';
				array[' <=']='Menor o igual que';
				return array[value];
			}
		};
		
		Ext.util.Format.valorFieldRenderer = function(paginationBar){
			return function(value,meta,record){
				if(paginationBar.store.fields.map[record.data['campo']]){
					if(paginationBar.store.fields.map[record.data['campo']].type.type=='date'){
						return Ext.util.Format.date(value,'d-m-Y');
					}else
					if(paginationBar.store.fields.map[record.data['campo']].type.type=='bool'){
						if(value===true){
							return 'Si';
						}else
						if(value===false){
							return 'No';
						}
					}else{
						for(var i in paginationBar.ownerCt.getColumnModel().config){
							if(paginationBar.ownerCt.getColumnModel().config[i].dataIndex&&paginationBar.ownerCt.getColumnModel().config[i].dataIndex==record.data['campo']){
								if(paginationBar.ownerCt.getColumnModel().config[i].editor.xtype=='combo'){
									if(paginationBar.ownerCt.getColumnModel().config[i].editor.valueField){
										var r = paginationBar.ownerCt.getColumnModel().config[i].editor.findRecord(paginationBar.ownerCt.getColumnModel().config[i].editor.valueField, value);
										if(r){
											value = r.data[paginationBar.ownerCt.getColumnModel().config[i].editor.displayField];
										}else if(paginationBar.ownerCt.getColumnModel().config[i].editor.valueNotFoundText !== undefined){
											value = paginationBar.ownerCt.getColumnModel().config[i].editor.valueNotFoundText;
										}
									}
									return value;
								}else{
									return value
								}
							}
						}
					}
				}
			}
		};
		
		var operadorStore = new Ext.data.ArrayStore({
			fields: ['operador', 'name']
		});
		
		if(!this.filterStore){
			this.filterStore=new Ext.data.SimpleStore({
				fields: ['campo', 'operador']
				,data: []
				//,data: [['title',''],['contenido',' >=']]
				,autoLoad: true
			});	
		}
		
		var filtrosEditor = new Ext.ux.grid.RowEditor({
			commitChangesText: 'Actualice o cancele antes de editar otro filtro'
			,listeners: {
				'beforeedit': function(editor,rowIndex) {
					if(!this.agregando){
						filterGridAcccion(editor.grid.getStore().getAt(rowIndex).data['campo']);
					}else{
						filtrosGrid.getColumnModel().getColumnById('valor').setEditor(false);
						filtrosEditor.refreshFields();
					}
				}
				,'afteredit': function(editor,rowIndex) {
					editor.grid.getStore().commitChanges();
				}
			}
		});
	    var filtrosGrid = new Ext.grid.GridPanel({
			store: paginationBar.filterStore
			,loadMask: {msg:'Cargando Datos...'} 
			,bbar: [{
				tooltip: 'Agregar filtro'
            	,overflowText: 'Agregar filtro'
				,iconCls: 'x-tbar-agregarfiltro'
				,handler: function(){
						var Filtro = Ext.data.Record.create([
						{
							name: 'campo'
							,type: 'string'
						},{
							name: 'operador'
							,type: 'string'

						},{
							name: 'valor'
							,type: 'string'

						}]);
						var newRecord = new Filtro({
							campo:''
							,operador: ''
							,valor: ''
						});
						filtrosEditor.stopEditing();
						paginationBar.filterStore.insert(0, newRecord);
						filtrosGrid.getView().refresh();
						filtrosGrid.getSelectionModel().selectRow(0);
						filtrosEditor.agregando=true;
						filtrosEditor.startEditing(0);
				}
			},{
				ref: '../modificarBtn'
				,tooltip: 'Modificar filtro'
            	,overflowText: 'Modificar filtro'
				,iconCls: 'x-tbar-modificarfiltro'
				,disabled: true
				,handler: function(){
					filtrosEditor.startEditing(filtrosGrid.getSelectionModel().getSelections()[0]);
				}
			},{
				ref: '../removeBtn'
				,tooltip: 'Borrar filtro'
            	,overflowText: 'Borrar filtro'
            	,iconCls: 'x-tbar-borrarfiltro'
				,disabled: true
				,handler: function(){
					var rowIndex = filtrosGrid.getStore().indexOf(filtrosGrid.getSelectionModel().getSelections()[0]);
					filtrosGrid.getStore().removeAt(rowIndex);
					filtrosGrid.getView().refresh();
				}
			},'->',{
				ref: '../aplicarBtn'
				,tooltip: 'Aplicar filtro'
            	,overflowText: 'Aplicar filtro'
				,iconCls: 'x-tbar-aplicar'
				,handler: function(){
					paginationBar.doRefresh();
				}
			}]
			,columns: [
			{
				header: 'Campo'
				,id:'campo'
				,width: 110
				,dataIndex: 'campo'
				,editor: new Ext.form.ComboBox({
					hiddenName: 'campo'
					,store: fieldStore
					,displayField: 'name'
					,valueField: 'field'
					,typeAhead: true
					,mode: 'local'
					,triggerAction: 'all'
					,emptyText: 'Seleccione'
					,selectOnFocus:true
					,allowBlank: false
					,blankText:'Ingrese el campo'
					,valueNotFoundText: ''
					,listeners:{
						'select':function(editor){
							filterGridAcccion(editor.value);
						}
					}
				})
			},{
				header: 'Operador'
				,id:'operador'
				,dataIndex: 'operador'
				,width: 120
				,editor: new Ext.form.ComboBox({
					hiddenName: 'operador'
					,store: operadorStore
					,displayField: 'name'
					,valueField: 'operador'
					,typeAhead: true
					,mode: 'local'
					,triggerAction: 'all'
					,emptyText: 'Seleccione'
					,selectOnFocus:true
					,allowBlank: false
					,blankText:'Ingrese el operador'
					,valueNotFoundText: ''
					,listeners:{
						'change':function(editor){
							//filtrosEditor.items;
						}
					}
				})
			},{
				header: 'Valor'
				,id:'valor'
				,dataIndex: 'valor'
				,width: 110
				,editor: false
				,renderer: Ext.util.Format.valorFieldRenderer(paginationBar)
			}]
			,plugins: [filtrosEditor]
			,stripeRows: true
			,autoExpandColumn: 'valor'
		});
		
		var filtrosMenuContextual = new Ext.menu.Menu({
			items: [
			{
				text: 'Modificar filtro'
				,handler:filtrosGrid.modificarBtn.handler
				,iconCls: 'x-menu-item-modificarfiltro'
			},'-',{
				text: 'Borrar filtro'
				,handler:filtrosGrid.removeBtn.handler
				,iconCls: 'x-menu-item-borrarfiltro'
			}]
		});
		
		filtrosEditor.on('startediting', function (editor) {
			for(var i in editor.items.items){
				if(editor.items.items[i].hiddenName=='campo'){
					if(editor.items.items[i].value==editor.grid.getSelectionModel().selections.keys){
						editor.items.items[i].setDisabled(true);
						editor.items.items[i].nextSibling().setDisabled(true);
						
					}else{
						editor.items.items[i].setDisabled(false);
						editor.items.items[i].nextSibling().setDisabled(false);
					}
				}
			}
		});
		
		filtrosGrid.on('rowcontextmenu', function (grid,rowIndex,event) {
			grid.getSelectionModel().selectRow(rowIndex);
			event.stopEvent();
			filtrosMenuContextual.showAt(event.getXY());
		});
		
		var filterGridAcccion=function(campo){
			if(paginationBar.store.fields.map[campo].type.type=='date'){
				operadorStore.loadData(numberDateOperatorData);
				filtrosGrid.getColumnModel().getColumnById('valor').setEditor(
					new Ext.form.DateField({
						format: 'd-m-Y'
					})
				);
				if(filtrosEditor.items){
					filtrosEditor.items.items[1].setValue('');
				}
			}else
			if(paginationBar.store.fields.map[campo].type.type=='bool'){
				operadorStore.loadData(booleanOperatorData);
				filtrosGrid.getColumnModel().getColumnById('valor').setEditor(new Ext.form.Checkbox({}));
				if(filtrosEditor.items){
					filtrosEditor.items.items[1].setValue('');
				}
			}else{
				operadorStore.loadData(defaultOperatorData);
				for(var i in paginationBar.ownerCt.getColumnModel().config){
					if(paginationBar.ownerCt.getColumnModel().config[i].dataIndex&&paginationBar.ownerCt.getColumnModel().config[i].dataIndex==campo){
						if(paginationBar.ownerCt.getColumnModel().config[i].editor.xtype=='combo'){
							filtrosGrid.getColumnModel().getColumnById('valor').setEditor(new Ext.form.ComboBox({
								store:paginationBar.ownerCt.getColumnModel().config[i].editor.getStore()
								,displayField: paginationBar.ownerCt.getColumnModel().config[i].editor.displayField
								,valueField: paginationBar.ownerCt.getColumnModel().config[i].editor.valueField
								,typeAhead: true
								,mode: 'local'
								,triggerAction: 'all'
								,emptyText: 'Seleccione un valor'
								,selectOnFocus:true
								,allowBlank:false
								,blankText:'Seleccione un valor'
							}));
							if(filtrosEditor.items){
								filtrosEditor.items.items[1].setValue('');
							}
						}else{
							filtrosGrid.getColumnModel().getColumnById('valor').setEditor(new Ext.form.Field({}));
							if(filtrosEditor.items){
								if(paginationBar.store.fields.map[campo].type.type=='number'){
									filtrosEditor.items.items[1].setValue('');
								}else{
									filtrosEditor.items.items[1].setValue(' LIKE');
								}
							}	
						}
					}
				}
			}
			filtrosEditor.refreshFields();
		}
		
		var filtrosWindow = new Ext.Window({
			autoScroll:true
			,title:'Filtros de busqueda'
			,width: 400
			,height: 200
			,modal:true
			,items:[filtrosGrid]
			,layout: 'fit'
		}).show();
		
		fieldStore.loadData(fieldDataJson);
		filtrosGrid.getSelectionModel().singleSelect=true;
		filtrosGrid.getSelectionModel().on('selectionchange', function(sm){
        	filtrosGrid.removeBtn.setDisabled(sm.getCount() < 1);
			filtrosGrid.modificarBtn.setDisabled(sm.getCount() < 1);
   		});
		filtrosWindow.on('close', this.fireCloseWindow, this);
    }
	,fireCloseWindow: function(){
		this.fireEvent("closeFilterWindow");
	}
	,cleanFilters : function(){
		if(this.filterStore){
			this.filterStore.removeAll();
		}
		this.doLoad(this.cursor);
		this.fireEvent("closeFilterWindow");
    }
	,doLoad : function(start){
		var o = {}, pn = this.getParams();
		if(this.filter&&this.filterStore){
			o['filtro']=new Array();
			for(row in this.filterStore.data.items) {
				o['filtro'][row]=new Array();
				o['filtro'][row]=this.filterStore.data.items[row].data;
			}
			o['filtro']=Ext.util.JSON.encode(o['filtro']);
		}
        o[pn.start] = start;
        o[pn.limit] = this.pageSize;
        if(this.fireEvent('beforechange', this, o) !== false){
            this.store.load({params:o});
        }
    }
});
