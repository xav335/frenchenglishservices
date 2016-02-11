(function() {
	
	jQuery.ajax({
	   type: "POST",
	   dataType: "json",
	   url: ajaxurl,
	   data: {action: 'motech_spacer'},
	   success: function(response) { //ajax response
			 //modal.find('.modal-body').html(data); //insert the response.
			 ajaxresponse = response;
			var menu_array = [];
			menu_array.push({
								text:"Default",
								value:'[spacer height="'+ajaxresponse["useheight"]+'"]',
								onclick:function() {
													 tinyMCE.activeEditor.insertContent(this.value());
												}
							});			 
			var addspacers = ajaxresponse["addspacers"];					
			var index;
			if(ajaxresponse["addspacers"]){
				for	(index = 0; index < addspacers.length; index++) {
					menu_array.push({
						text: addspacers[index]["title"],
						value: '[spacer height="'+addspacers[index]["height"]+'" id="'+addspacers[index]["id"]+'"]',
						onclick:function() {
													 tinyMCE.activeEditor.insertContent(this.value());
												}
					});
				}
			}
			//Call following code block from ajax success method
			//Set new values to myKeyValueList 
			tinyMCE.activeEditor.settings.myKeyValueList = menu_array;
			//Call plugin method to reload the dropdown
			tinyMCE.activeEditor.plugins.motech_spacer.refresh();			 

	   }
   });		

	var button_name = 'motech_spacer'; //set button name
	
tinymce.PluginManager.add(button_name, function( editor, url ) {
   var self = this, button;

   function getValues() {
      return editor.settings.myKeyValueList;
   }
   // Add a button that opens a window
   editor.addButton(button_name, {
            title: 'Add a Spacer',
            type: 'menubutton',
            icon: 'icon motech_spacer_icon',
      menu: getValues(),
      onPostRender: function() {
         //this is a hack to get button refrence.
         //there may be a better way to do this
         button = this;
      },
   });

   self.refresh = function() {
      //remove existing menu if it is already rendered
      if(button.menu){
         button.menu.remove();
         button.menu = null;
      }

      button.settings.menu = getValues();
   };
});



})();