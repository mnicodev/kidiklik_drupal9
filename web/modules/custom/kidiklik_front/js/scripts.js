$ = jQuery;

$(function(){

	if($("#node-message-contact-form").length) {
		$("#edit-submit").val("Envoyer");
		
		$("#edit-submit").click(function () {
			$("#edit-title-wrapper").find(".form-text").val($("#edit-field-nom-wrapper").find(".form-text").val()+" "+$("#edit-field-prenom-wrapper").find(".form-text").val())
		})
		
		$("#edit-field-departement-wrapper").hide();
	}

})
