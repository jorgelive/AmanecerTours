Ext.namespace('Ext.ux.grid');

Ext.ux.grid.FilterRow = Ext.extend(Ext.util.Observable, {
	constructor: function(conf) {
		Ext.apply(this, conf || {});
		this.addEvents(
			"change"
		);
		if (this.listeners) {
			this.on(this.listeners);
		}
	}
	,init: function(grid) {
		this.grid = grid;
		var cm = grid.getColumnModel();
		var view = grid.getView();
		var Filter = Ext.ux.grid.FilterRowFilter;
		this.eachFilterColumn(function(col) {
			if (!(col.filter instanceof Filter)) {
				col.filter = new Filter(col.filter);
			}
			col.filter.on("change", this.onFieldChange, this);
			col.filter.on("enter", this.onFieldEnter, this);
			col.filter.on("focus", this.onFieldFocus, this);
		});
		this.applyTemplate();
		grid.addClass('filter-row-grid');
		grid.on("afterrender", this.renderFields, this);
		grid.getView().on("refresh", this.renderFields, this);
		grid.on("staterestore", this.onColumnChange, this);
		grid.on("resize", this.resizeAllFilterFields, this);
		cm.on("widthchange", this.onColumnWidthChange, this);
		view.onColumnWidthUpdated = view.onColumnWidthUpdated.createSequence(function(colIndex, newWidth) {
			this.onColumnWidthChange(this.grid.getColumnModel(), colIndex, parseInt(newWidth, 10));
		}, this);
		cm.on("columnmoved", this.onColumnChange, this);
		view.afterMove = view.afterMove.createSequence(this.renderFields, this);
		cm.on("hiddenchange", this.onColumnHiddenChange, this);
		grid.bottomToolbar.on("closeFilterWindow", this.onCloseFilterWindow, this);
	}
	,onCloseFilterWindow: function() {
		this.chargeData();
	}
	,chargeData: function() {
		var filtros=this.getFilterData();
		if(this.grid.bottomToolbar.filterStore){
			for(var field in filtros){
				for(var i in this.grid.getColumnModel().config){
					if(this.grid.getColumnModel().config[i].dataIndex&&this.grid.getColumnModel().config[i].dataIndex==field){
						if(this.grid.bottomToolbar.filterStore.getById(field)){
							this.grid.getColumnModel().config[i].filter.field.setValue(this.grid.bottomToolbar.filterStore.getById(field).data['valor']);
						}else
						if(this.grid.getStore().fields.map[field].type.type=='bool'||(this.grid.getColumnModel().config[i].editor&&this.grid.getColumnModel().config[i].editor.xtype=='combo')){
							this.grid.getColumnModel().config[i].filter.field.setValue('emptyjg');
						}else{
							this.grid.getColumnModel().config[i].filter.field.setValue('');
						}
					}
				}
			}
		}
	}
	,onColumnHiddenChange: function(cm, colIndex, hidden) {
		var filterDiv = Ext.get(this.getFilterDivId(cm.getColumnId(colIndex)));
		if (filterDiv){
			filterDiv.parent().dom.style.display = hidden ? 'none' : '';
		}
		this.resizeAllFilterFields();
	}
	,applyTemplate: function() {
		var colTpl = "";
		this.eachColumn(function(col) {
			var filterDivId = this.getFilterDivId(col.id);
			var style = col.hidden ? " style='display:none'" : "";
			colTpl += '<td' + style + '><div class="x-small-editor" id="' + filterDivId + '"></div></td>';
		});
		var headerTpl = new Ext.Template(
			'<table border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
			'<thead><tr class="x-grid3-hd-row">{cells}</tr></thead>',
			'<tbody><tr class="filter-row-header">',
			colTpl,
			'</tr></tbody>',
			"</table>"
		);
		var view = this.grid.getView();
		Ext.applyIf(view, { templates: {} });
		view.templates.header = headerTpl;
	}
	,onColumnChange: function() {
		this.eachFilterColumn(function(col) {
			var editor = col.filter.getCurrentField();
			if (editor && editor.rendered) {
				var el = col.filter.getFieldDom();
				var parentNode = el.parentNode;
				parentNode.removeChild(el);
			}
		});
		this.applyTemplate();
	}
	,renderFields: function() {
		this.eachFilterColumn(function(col) {
			var currentEditor = col.filter.getCurrentField();
			if (currentEditor && currentEditor.rendered) {
				var el = col.filter.getFieldDom();
				if(el.parentNode){
					var parentNode = el.parentNode;
					parentNode.removeChild(el);
				}
			}
			var filterDiv = Ext.get(this.getFilterDivId(col.id));
			if(this.grid.getStore().fields.map[col.dataIndex]){
				var tipo = this.grid.getStore().fields.map[col.dataIndex].type.type;
				var editor = col.filter.getField(tipo,col);
				editor.setWidth(col.width - 2);
				editor.render(filterDiv);
			}

		});
		this.chargeData();
	}
	,onFieldEnter: function() {
		
		this.grid.bottomToolbar.doRefresh();
	}
	,onFieldFocus: function() {
		this.grid.getSelectionModel().clearSelections();
	}
	,onFieldChange: function() {
		for(var i in this.grid.getColumnModel().config){
			if (this.grid.getColumnModel().config[i].dataIndex&&this.grid.getColumnModel().config[i].filter&&this.grid.getColumnModel().config[i].filter.field.hasFocus){
				if(!this.grid.bottomToolbar.filterStore){
					this.grid.bottomToolbar.filterStore=new Ext.data.SimpleStore({
						fields: ['campo', 'operador']
						,data: []
						,autoLoad: true
					});	
				}
				var filtros=this.getFilterData();
				for (var field in filtros){
					if(this.grid.getColumnModel().config[i].dataIndex==field){
						if(filtros[field]===''||filtros[field]=='emptyjg'){
							var fila=this.grid.bottomToolbar.filterStore.getById(field);
							if(fila!=undefined){
								this.grid.bottomToolbar.filterStore.remove(fila);
							}
						}else{
							if(this.grid.getStore().fields.map[field].type.type=='date'||this.grid.getStore().fields.map[field].type.type=='bool'||(this.grid.getColumnModel().config[i].editor&&this.grid.getColumnModel().config[i].editor.xtype=='combo')){
								var operador='';
							}else{
								var operador=' LIKE';
							}
							var armazonRecord = Ext.data.Record.create([
								{name: "campo"} 
								,{name: 'operador'}
								,{name: 'valor'}
							]);
							var recordData = new armazonRecord({
								campo: field
								,operador: operador
								,valor: filtros[field]
							},field);
							
							this.grid.bottomToolbar.filterStore.add(recordData);
						}
					}
				}		
			}
		}
		if(this.grid.bottomToolbar.filterStore){
			this.grid.bottomToolbar.filterStore.commitChanges();
		}
	}
	,getFilterData: function() {
		var data = {};
		this.eachFilterColumn(function(col) {
			var name = col.dataIndex || col.id;
			data[name] = col.filter.getFieldValue();
		});
		return data;
	}
	,onColumnWidthChange: function(cm, colIndex, newWidth) {
		var col = cm.getColumnById(cm.getColumnId(colIndex));
		if (col.filter) {
			this.resizeFilterField(col, newWidth);
		}
	}
	,resizeAllFilterFields: function() {
		var cm = this.grid.getColumnModel();
		this.eachFilterColumn(function(col, i) {
			this.resizeFilterField(col, cm.getColumnWidth(i));
		});
	}
	,resizeFilterField: function(column, newColumnWidth) {
		var editor = column.filter.getCurrentField();
		if(editor){
			editor.setWidth(newColumnWidth - 2);
		}
	}
	,getFilterDivId: function(columnId) {
		return this.grid.id + '-filter-' + columnId;
	}
	,eachFilterColumn: function(func) {
		this.eachColumn(function(col, i) {
			if(col.filter &&col.dataIndex){
				func.call(this, col, i);
			}
		});
	}
	,eachColumn: function(func) {
		Ext.each(this.grid.getColumnModel().config, func, this);
	}
});

Ext.preg("filterrow", Ext.ux.grid.FilterRow);

Ext.ux.grid.FilterRowFilter = Ext.extend(Ext.util.Observable, {
	field: undefined
	,fieldEvents: ["keyup"]
	,constructor: function(config) {
		Ext.apply(this, config);
		this.addEvents(
			'change'
			,'select'
			,'specialkey'
			,'enter'
			,'focus'
		);
	}
	,fireChangeEvent: function(){
		this.fireEvent("change");
	}
	,fireEnter: function(){
		this.fireEvent("enter");
	}
	,fireFocus: function(){
		this.fireEvent("focus");
	}
	,getField: function(tipo,columna) {
		//if (!this.field) {
		if(tipo=='bool'){
			var boolStore=new Ext.data.SimpleStore({
					fields: ['value', 'name']
					,data: [['emptyjg','Todo'],[false,'No'],[true,'Si']]
					,autoLoad: true
				});
			this.field = new Ext.form.ComboBox({
				store: boolStore	
				,displayField: 'name'
				,valueField: 'value'
				,typeAhead: true
				,mode: 'local'
				,value:'emptyjg'
				,triggerAction: 'all'
				,selectOnFocus:true
				,allowBlank: true
			});
			this.field.on('select', this.fireChangeEvent, this);
		}else
		if(tipo=='date'){
			this.field = new Ext.form.DateField({format: 'd-m-Y'});
			this.field.on('select', this.fireChangeEvent, this);
		}else{
			if(columna&&columna.editor&&columna.editor.xtype=='combo'){
				var armazonRecord = Ext.data.Record.create([ // creates a subclass of Ext.data.Record
					{name: columna.editor.valueField}
					,{name: columna.editor.displayField}
				]);
				var emptyRow = new armazonRecord();
				emptyRow.data[columna.editor.valueField]='emptyjg';
				emptyRow.data[columna.editor.displayField]='Todos'
				var opciones = new Object();
				opciones.opciones = new Array();
				var i=0;
				opciones.opciones.push(emptyRow.data);
				i++;
				columna.editor.getStore().each(function(record) {
					opciones.opciones[i]=record.json;
					i++;
				},this);
				this.field = new Ext.form.ComboBox({
					store: new Ext.data.JsonStore({
						root: 'opciones'
						,fields: [columna.editor.valueField, columna.editor.displayField]
						,autoLoad: true
						,data: opciones
					})
					,displayField: columna.editor.displayField
					,valueField: columna.editor.valueField
					,typeAhead: true
					,mode: 'local'
					,triggerAction: 'all'
					,emptyText: 'Seleccione un valor'
					,selectOnFocus:true
					,value:'emptyjg'
					,allowBlank:false
					,blankText:'Seleccione un valor'
				});
				this.field.on('select', this.fireChangeEvent, this);
			}else{
				this.field = new Ext.form.TextField({enableKeyEvents: true});
			}
			this.field.on('change', this.fireChangeEvent, this);
		}
		//para firefox y chrome
		this.field.on('keydown', function(el,ev){
			if (ev.button==12) {
				ev.preventDefault
				ev.stopPropagation();
            	this.fireEnter();
       		}
		},this);
		
		this.field.on('focus', this.fireFocus,this);
		
		Ext.each(this.fieldEvents, function(event) {
			this.field.on(event, this.fireChangeEvent, this);
		}, this);
		return this.field;
		
	}
	,getCurrentField: function() {
		if(this.field){
			return this.field;
		}
	}
	,getFieldDom: function(){
		return this.field.wrap ? this.field.wrap.dom : this.field.el.dom;
	}
	,getFieldValue: function(){
		return this.field.getValue();
	}
});