$ = jQuery;

$(function(){

	if($("#node-message-contact-form").length) {
		$("#edit-submit").val("Envoyer");
		
		$("#edit-submit").click(function () {
			$("#edit-title-wrapper").find(".form-text").val($("#edit-field-nom-wrapper").find(".form-text").val()+" "+$("#edit-field-prenom-wrapper").find(".form-text").val())
		})
       

       // $('#cp-ville').append($('#ville').html('ville'));
        /*$('#edit-field-ville-contact-wrapper').css('border', '1px solid lightgray');
        $('#edit-field-ville-contact-wrapper').css('padding', '10px');
        $('#edit-field-ville-contact-wrapper').css('cursor', 'pointer');
		*/
		$("#edit-field-departement-wrapper").hide();
	}

})