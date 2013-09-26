/**
 * @class Ext.ux.form.ServerValidator
 * @extends Ext.util.Observable
 *
 * Server-validates field value
 *
 * @author    Ing. Jozef Sakáloš
 * @copyright (c) 2008, by Ing. Jozef Sakáloš
 * @date      8. February 2008
 * @version   1.0
 * @revision  $Id: Ext.ux.form.ServerValidator.js 645 2009-03-24 02:35:56Z jozo $
 *
 */
Ext.ns('Ext.ux.form');
/**
 * Creates new ServerValidator
 * @constructor
 * @param {Object} config A config object
 */
Ext.ux.form.ServerValidator = function(config) {
    Ext.apply(this, config, {
         url:'/request.php'
        ,method:'post'
        ,cmd:'validateField'
        ,paramNames:{
             valid:'valid'
            ,reason:'reason'
        }
        ,validationDelay:1000
        ,validationEvent:'keyup'
        ,logFailure:true
        ,logSuccess:true
    });
    Ext.ux.form.ServerValidator.superclass.constructor.apply(this, arguments);
}; // eo constructor

// extend
Ext.extend(Ext.ux.form.ServerValidator, Ext.util.Observable, {

    // {{{
    init:function(field) {
		this.field = field;
        // save original functions
        var isValid = field.isValid;
        var validate = field.validate;

        Ext.apply(field, {
            // is field validated by server flag
             serverValid: undefined !== this.serverValid ? this.serverValid : true
//           
            ,isValid:function(preventMark) {
                if(this.disabled) {
                    return true;
                }
                return isValid.call(this, preventMark) && this.serverValid;
            }

            // private
            ,validate:function() {
                var clientValid = validate.call(this);

                // return false if client validation failed
                if(!this.disabled && !clientValid) {
                    return false;
                }

                // return true if both client valid and server valid
                if(this.disabled || (clientValid && this.serverValid)) {
                    this.clearInvalid();
                    return true;
                }

                // mark invalid and return false if server invalid
                if(!this.serverValid) {
                    this.markInvalid(this.reason);
                    return false;
                }

                return false;
            } // eo function validate

        }); // eo apply

        // install listeners
        this.field.on({
             render:{single:true, scope:this, fn:function() {
                this.serverValidationTask = new Ext.util.DelayedTask(this.serverValidate, this);
                this.field.el.on(this.validationEvent, function(e){
                    this.field.serverValid = false;
                    this.filterServerValidation(e);
                }, this);
//                this.field.el.on({
//                    keyup:{scope:this, fn:function(e) {
//                        this.field.serverValid = false;
//                        this.filterServerValidation(e);
//                    }}
////                    ,blur:{scope:this, fn:function(e) {
////                        this.field.serverValid = false;
////                        this.filterServerValidation(e);
////                    }}
//                });
            }}
        });
    } // eo function init
    // }}}

	,serverValidate:function() {
	   var options = {
            url:this.url + '?#' + (this.name || this.field.name || this.field.column.dataIndex || this.field.column.id)
            ,method:this.method
            ,scope:this
            ,success:this.handleSuccess
            ,failure:this.handleFailure
            ,params:this.params || {}
        };
		
		var actualOptions = {params:{}};
		for (propName in options.params){
        	var p = options.params[propName];
			if (typeof p === 'function'){
				actualOptions.params[propName] = p.call(this);
				
			}else
				actualOptions.params[propName] = p;
		}
		
		//co (actualOptions.params);
		
		options.params=actualOptions.params;
		
        Ext.applyIf(options.params, {
             cmd:this.cmd
            ,field:this.name || this.field.name || this.field.column.dataIndex || this.field.column.id
            ,value:this.field.getValue()
            ,table:this.table
        });
        Ext.Ajax.request(options);
    } // eo function serverValidate

    // {{{
    ,filterServerValidation:function(e) {
        if(this.field.value === this.field.getValue() || (this.field.getValue() == "" && this.field.allowBlank)) {
            this.serverValidationTask.cancel();
            this.field.serverValid = true;
            return;
        }
        if(!e.isNavKeyPress()) {
            this.serverValidationTask.delay(this.validationDelay);
        }
    } // eo function filterServerValidation
    // }}}
    // {{{
    ,handleSuccess:function(response, options) {
        var o;
        try {o = Ext.decode(response.responseText);}
        catch(e) {
            if(this.logFailure) {
                this.log(response.responseText);
            }
        }
        if(true !== o.success) {
            if(this.logFailure) {
                this.log(response.responseText);
            }
        }
        this.field.serverValid = true === o[this.paramNames.valid];
        this.field.reason = o[this.paramNames.reason];
        this.field.validate();
    } // eo function handleSuccess
    // }}}
    // {{{
    ,handleFailure:function(response, options) {
        if(this.logFailure) {
            this.log(response.responseText);
        }
    } // eo function handleFailure
    // }}}
    // {{{
    ,log:function(msg) {
        if(console && console.log) {
            console.log(msg);
        }
    } // eo function log
    // }}}

});  