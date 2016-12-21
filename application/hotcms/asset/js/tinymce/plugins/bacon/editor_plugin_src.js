/**
 * editor_plugin_src.js
 *
 * Copyright 2012, Hot Tomali Communications LTD
 *
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('bacon');

	tinymce.create('tinymce.plugins.BaconPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceHotimg');
			ed.addCommand('mceBaconmore', function() {
				ed.windowManager.open({
					//file : '/hotcms/media-library/tinymce_image_picker',
					//width : 620 + parseInt(ed.getLang('hotimg.delta_width', 0)),
					//height : 520 + parseInt(ed.getLang('hotimg.delta_height', 0)),
					//inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register buttons
			ed.addButton('bacon_adv', {
				title : 'bacon.desc',
				cmd : 'mceBaconmore',
				image : url + '/img/hotimg.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('bacon_adv', n.nodeName == 'IMG');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'BaconCMS plugin',
				author : 'Hot Tomali',
				authorurl : 'http://www.hottomali.com',
				infourl : 'http://www.hottomali.com',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('bacon', tinymce.plugins.BaconPlugin);
})();