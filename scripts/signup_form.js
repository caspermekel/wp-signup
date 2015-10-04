jQuery(function($){
	
	 
	function format(state) {
	    if (!state.id) return state.text; // optgroup
	    return "<span class='flag' style='background: url("+ qinvoice.url + "images/flags/24x24/" + state.id.toUpperCase() + ".png);'>" + state.text +"<span>";
	}
	if (typeof $("#s_country") != 'undefined'){
		$("#s_country").select2({
		    formatResult: format,
		    formatSelection: format,
		    escapeMarkup: function(m) { return m; }
		});
		$("#s_country").select2("val", $("#current_country").val());

	}


	$('#s_password').strength({
            strengthClass: 'strength',
            strengthMeterClass: 'strength_meter',
            strengthButtonClass: 'button_strength',
            strengthButtonText: $("#show_password").val(),
            strengthButtonTextToggle: $("#hide_password").val()
        });


});