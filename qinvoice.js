jQuery(function($)
	{
		$("#doRegister").click(function(){
			var form = $(this).closest('form');
			
		     $.ajax({
		         type: "GET",
		         dataType: "jsonp",
		         //url: "https://app.q-invoice.com/ajax/formAccount.php",
		         url: "https://app.q-invoice.com/ajax/formAccount.php",
		         data: form.serialize() +"&mode=register_v2",
		         success: function(result) {
		         	//console.log(result);
		         	if(result.result == 'OK'){
		         		_gaq.push(['_trackEvent', 'Form', 'Signup']);
		         		form.hide();
						$("#successMessage").fadeIn();	
						var href = $("#successMessage").find('a').attr('href');
						$("#successMessage").find('a').attr('href', href+'&u='+ result.username);	

		         	}else{
			         	$("span.error").hide();
			         	$('input').each(function(){
			         		$(this).removeClass('hasError');
			         	});
						
						
						$.each(result.errors, function(ee, type) 
						{
							//alert(ee);
							// var ee = $("#"+ ee);
							// ee.addClass('hasError');
							
							//alert(ee.attr('id'));
							//ee.find('.error').remove();
							var eep = $("[data-type='" + type + "']");
							eep.fadeIn();
							
							console.log(ee +' : '+ type);
							console.log(eep);
							//ee.closest('.control-group').addClass('error');
							//eep.find('.error').hide();
							//eep.html(message);
						});
					}
		            //$(this).hide();
		         },
		         error:function(result){
		         	//console.log(result);
				    // failed request; give feedback to user
				    // alert('niet ok');
				 }  
		     });
			return false;
		});
	});