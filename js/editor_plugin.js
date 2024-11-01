// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('weec');

	tinymce.create('tinymce.plugins.weec', {
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('mceweec', function() {
                            ed.execCommand('mceInsertContent', false, '[weecdatepicker]');
			});

			// Register example button
			ed.addButton('weec', {
				title : 'weec',
				cmd : 'mceweec',
				image : url + '/weec.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('weec', n.nodeName == 'IMG');
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
					longname  : 'weec',
					author 	  : 'Diego Hincapie',
					authorurl : 'http://weabers.com',
					infourl   : 'http://weabers.com',
					version   : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('weec', tinymce.plugins.weec);
})();