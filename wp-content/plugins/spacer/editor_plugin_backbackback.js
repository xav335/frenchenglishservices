(function() {
	
	jQuery.ajax({
	   type: "POST",
	   dataType: "json",
	   url: ajaxurl,
	   data: {action: 'motech_spacer'},
	   success: function(response) { //ajax response
			 //modal.find('.modal-body').html(data); //insert the response.
			 ajaxresponse = response;

	   }
   });		

	var button_name = 'motech_spacer'; //set button name
	
	tinymce.create('tinymce.plugins.'+button_name, {
		init : function(ed, url) {
			ed.addButton(button_name, {
				title : 'Add a Spacer', //set button label
				//image : url+'/icon.png', //set icon filename (20 X 20px). put icon in same folder
				icon: 'icon motech_spacer_icon',
				onclick : function() {
					//idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
					//var vidId = prompt("YouTube Video", "Enter the id or url for your video");
					//var m = idPattern.exec(vidId);
					//if (m != null && m != 'undefined')
						ed.execCommand('mceInsertContent', false, '[spacer height="'+ajaxresponse["useheight"]+'"]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : button_name,
				author : 'Justin Saad',
				authorurl : 'http://clevelandwebdeveloper.com/',
				infourl : 'http://clevelandwebdeveloper.com/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add(button_name, tinymce.plugins[button_name]);
	
})();