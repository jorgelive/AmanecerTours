// JavaScript Document
Ext.util.Format.comboRenderer = function(combo){
	return function(value){
		var text;
		if(combo.valueField){
            var r = combo.findRecord(combo.valueField, value);
            if(r){
                text = r.data[combo.displayField];
            }else if(combo.valueNotFoundText !== undefined){
                text = combo.valueNotFoundText;
            }
        }
		return text;
	};
};

Ext.util.Format.siNoRenderer = function(){
	return function(value){
		if(value===true){return 'Si';}else
		if(value===false){return 'No';}
	};
};

Ext.util.Format.imageRenderer = function(w,thumbphp,zc){
	return function(value){
        if (typeof value !== "undefined"){
            var part = value.split('.');
            part.reverse();

            if(value==''){
                return '<img src="'+Ext.BLANK_IMAGE_URL+'">';
            }

            if(typeof(w)=='number'){
                if(part[0]!='swf'){
                    if(typeof(thumbphp)=='boolean'&&thumbphp===true){
                        if(typeof(zc)=='boolean'&&zc===true){
                            return '<img src="/thumbs/index/?src='+value+'&w='+w+'&h='+w+'&zc=C">';
                        }else{
                            return '<img src="/thumbs/index/?src='+value+'&w='+w+'">';
                        }
                    }else{
                        return '<img style="width:'+w+'px;" src="'+value+'">';
                    }
                }else{
                    return '<div class="flashswf" width="'+w+'">'+value+'</div>';
                }
            }else{
                if(part[0]!='swf'){
                    return '<img src="'+value+'">';
                }else{
                    return value;
                }
            }
        }
	};
};