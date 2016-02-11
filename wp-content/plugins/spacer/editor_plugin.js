(function() {
	
	var button_name = 'motech_spacer'; //set button name
	//console.log(ajaxresponse);
    tinymce.PluginManager.add(button_name, function( editor, url ) {
		var self = this, button;

/*		function getMenu() {
			  return editor.settings.myMenu;
		   }*/
   
        editor.addButton( button_name, {
            title: 'Add a Spacer',
            type: 'menubutton',
            icon: 'icon motech_spacer_icon',
			 onPostRender: function() {
					 //this is a hack to get button refrence.
					 //there may be a better way to do this
					 var ctrl = this;
					jQuery.ajax({
					   type: "POST",
					   dataType: "json",
					   //async: false,
					   url: ajaxurl,
					   data: {action: 'motech_spacer'},
					   success: function(response) { //ajax response
							 //modal.find('.modal-body').html(data); //insert the response.
							 ajaxresponse = response;
							 
							var menu_array = [];
							menu_array.push({
												text:"Default",
												onclick:function() {
													 editor.insertContent('[spacer height="'+ajaxresponse["useheight"]+'"]');
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
											editor.insertContent(this.value());
										}
									});
								}
							}
				
							//Set new values to myKeyValueList 
							ctrl.state.data.menu = ctrl.settings.menu = menu_array;
				

					   }
				   });						 
			 }

        });
		
   
    });
	

	
	
})();