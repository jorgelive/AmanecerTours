// JavaScript Document
function co(obj) {

	str='';
	for(prop in obj) {
		str+=prop + " value :"+ obj[prop]+"\n";
	}
	alert(str);
}


function in_array (needle, haystack, argStrict) {
	// Checks if the given value exists in the array  
	// *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);    // *     returns 1: true
	// *     example 4: in_array(1, ['1', '2', '3'], true);    // *     returns 4: false
	var key = '', strict = !!argStrict; 
	if (strict) {
		for (key in haystack) {
			if (haystack[key] === needle) {
				return true;
			}
		}
	} else {
		for (key in haystack) {
			if (haystack[key] == needle) {
				return true;
			}
		}
	}
	return false;
}