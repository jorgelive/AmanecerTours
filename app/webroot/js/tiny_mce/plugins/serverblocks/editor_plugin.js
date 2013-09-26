/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function(tinymce) {
	tinymce.create('tinymce.plugins.ServerBlocksPlugin', {
		init : function(ed, url) {
			// Register commands
			var currentId;
			if(Ext.ComponentMgr.all.map[ed.id]){
				currentId=Ext.ComponentMgr.all.map[ed.id].ownerCt.ownerCt.ownerCt.ownerCt.newId;
			}
			ed.addCommand('mceServerBlocks', function() {
				ed.windowManager.open({
					file : '/servicios/serverblocks/'+currentId,
					//file : url + '/serverblocks.php?id='+currentId,
					width : 250,
					height : 160,
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('serverblocks', {title : 'serverblocks.serverblocks_desc', cmd : 'mceServerBlocks'});
		},

		getInfo : function() {
			return {
				longname : 'Server Blocks',
				author : '',
				authorurl : '',
				infourl : '',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('serverblocks', tinymce.plugins.ServerBlocksPlugin);
})(tinymce);