// JavaScript Document
Ext.override(Ext.form.Field, {
	fireKey : function(e) {
		if(((Ext.isIE && e.type == 'keydown') || e.type == 'keypress') && e.isSpecialKey()) {
			this.fireEvent('specialkey', this, e);
		}
		else {
			this.fireEvent(e.type, this, e);
		}
	}
  , initEvents : function() {
//                this.el.on(Ext.isIE ? "keydown" : "keypress", this.fireKey,  this);
		this.el.on("focus", this.onFocus,  this);
		this.el.on("blur", this.onBlur,  this);
		this.el.on("keydown", this.fireKey, this);
		this.el.on("keypress", this.fireKey, this);
		this.el.on("keyup", this.fireKey, this);

		// reference to original value for reset
		this.originalValue = this.getValue();
	}
});