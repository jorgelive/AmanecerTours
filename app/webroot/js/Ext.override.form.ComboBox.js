// JavaScript Document
Ext.override(Ext.form.ComboBox, {
    setValue : function(v){
        var text;
        if(this.valueField){
            var r = this.findRecord(this.valueField, v);
            if(r){
                text = r.data[this.displayField];
				var output=true;
            }else if(this.valueNotFoundText !== undefined){
                text = this.valueNotFoundText;
				this.store.on('load', this.setValue.createDelegate(this, [v]), null, {single: true});
				var output=true;
            }else{
				text= '';
				this.store.on('load', this.setValue.createDelegate(this, [v]), null, {single: true});
				var output=false;
			}
        }
        if (output){
			this.lastSelectionText = text;
			if(this.hiddenField){
				this.hiddenField.value = v;
			}
			Ext.form.ComboBox.superclass.setValue.call(this, text);
			this.value = v;
		}
    }
});