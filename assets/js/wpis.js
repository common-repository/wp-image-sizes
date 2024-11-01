var modal_options = {};
modal_options.modal = true;
modal_options.width=300;
modal_options.draggable= false;
modal_options.resizable= false;
modal_options.closeOnEscape= true;
modal_options.position= {my: "center",at: "center",of: window };

function wpis_alert(string){
	var $ = jQuery;
	
	modal_options.title  = "Action Alert";
	modal_options.buttons = {
			"Ok": function() {
				$(this).dialog("close");
			}
	}
	
	$("<div>" + string + "</div>").dialog(modal_options); 
}

function wpis_confirm(string, callback){
	var $ = jQuery;
	
	modal_options.title  = "Action Confirm";
	modal_options.buttons = {
			"Cancel": function() {
				$(this).dialog("close");
			},
			"Confirm": callback,
	}
	
	jQuery( "<div>" + string + "</div>" ).dialog(modal_options); 	
}