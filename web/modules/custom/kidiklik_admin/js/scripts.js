var href="";

function getGPS(ville) {

	$.ajax({
		url: "/admin/villes/gps/"+ville,
		success: function(result) {
			console.log(result);
			if(result) {
				$(".geolocation-input-latitude").val(result.lat);
				$(".geolocation-input-longitude").val(result.lng);

			}


		}
	})
}

$(function(){

	if($('#all_select_dep').length) {
		$('#all_select_dep').on('click', function() {
			if(parseInt($(this).attr('data-checked')) === 1) {
				$('#edit-field-partage-departements .form-checkbox').prop('checked', false);
			} else {
				$('#edit-field-partage-departements .form-checkbox').prop('checked', true);
			}
			
			
			$(this).attr('data-checked',$(this).attr('data-checked') *(-1));
		});
	}
	
	if($('.adherent-contents').length) {
		var kvp = document.location.search.substr(1).split('&');
		for(item in kvp) {
			console.log(item)
		}
	}
	if($('.save-stay').length) {
		$('.save-stay').on('click', function(e) {
			e.preventDefault();
			var kvp = document.location.search.substr(1).split('&');
			var href='';
			x = kvp[0].split('=');

			if($(this).attr('data-node') === 'create') {
				href = $('.node-form').attr('action') + '&create=1';
				$('.node-form').attr('action', href);
			} else {
				if(x[0] !== '') {
					search="?" + x[0] + "=" + $(this).attr('href') ;
				} else {
					search="?" + x[0] + "destination=" + $(this).attr('href') ;
				}
				
				href=$(this).attr('href') + search
			}

			
			//window.location.replace(href);
			$('.node-form').attr('action', href  + "?destination=/admin/" + $(this).attr('data-type'));
			history.pushState(null, null, href);
			//document.location.search= href;			
			//document.location.href=$(this).attr('href');
			$('#edit-submit').click();
		})
	}
	//if($('.rubriques').length) {

		$('#edit-field-rubriques').select2({
			placeholder: 'Choisissez des rubriques ...',
      		allowClear: true
		});
	//}
  if($("#node-bloc-de-mise-en-avant-edit-form").length) {
    $("#edit-field-newsletter").select2({
      placeholder: 'Choisissez une newsletter ...',
      allowClear: true
    });
  }
	if($('#edit-field-client-wrapper').length) {
		  $('#edit-field-client-wrapper select').select2({
			placeholder: 'Choisissez un client ...',
			allowClear: true,
			ajax: {
				url: '/admin/select/clients',
				dataType: 'json',
				data: function (params) {

					var query = {
						search: params.term,
						type: 'public'
					}
					return query;
				},
			}
		  })
	}
	if($('#edit-field-adherent-wrapper').length) {
		  $("#edit-field-adherent-wrapper select").select2({
		    placeholder: 'Choisissez un adhérent ...',
		    allowClear: true,
			ajax: {
				url: '/admin/select/adherents',
				dataType: 'json',
				data: function (params) {

					var query = {
						search: params.term,
						type: 'public'
					}
					return query;
				},
			}
		  });
	}
  $('#edit-field-rubriques-activite').select2({
    placeholder: 'Choisissez des rubriques ...',
    allowClear: true
  });
	//alert("ok");
	$("#edit-ville").change(function () {

		$.ajax({
			url: "/admin/villes/gps/"+$(this).val(),
			success: function(result) {
				console.log(result);
				if(result) {
					$(".geolocation-input-latitude").val(result.lat);
					$(".geolocation-input-longitude").val(result.lng);

				}


			}
		})


	})
	$(".node--type-activite").find("#edit-field-date-wrapper").find(".paragraphs-dropbutton-wrapper").remove();

	if($("#edit-field-conditionne-nombre-aff-value").length) {
	    $("#edit-field-conditionne-nombre-aff-value").on('change',function() {
	        if($("#edit-field-conditionne-nombre-aff-value").is(':checked')==false) {
	            $('#edit-field-date-debut-0-value-date').val(null);
	            $('#edit-field-date-fin-0-value-date').val(null);
	        } else {
	            $('#edit-field-nombre-affichage-possible-0-value').val(0);

	        }
	    })

	}


	if($("#node-newsletter-edit-form").length) {
		$(".horizontal-tabs-list").find("li a").click(function(e) {
			e.preventDefault();
			if(href=="") href=$("#node-newsletter-edit-form").attr("action");

			$("#node-newsletter-edit-form").attr("action",href+$(this).attr("href"));
		});

		//$("#node-newsletter-edit-form").attr("action",location.href)


	}

	/* gestion des pubs pour le dep */
	if($("#gestion_dep").length) {

		$("#edit-field-partage-departements--wrapper").find("input").each(function() {
			$(this).attr("type","radio");
		});
		$("#edit-field-partage-departements--wrapper").find("input").click(function() {
			$("#edit-field-partage-departements--wrapper").find("input").prop("checked",false);
			$(this).prop("checked","checked");
		});
	}

	/* fonctionnalités adhérent */
	if($("#node-adherent-edit-form").length) {

		$('.tab-content .view-content-adherent').find('a.button').on('click',function(e) {
			//e.preventDefault();
			id = $('.save-stay').attr('data-ref-id');
			
			$(this).attr('href', $(this).attr('href') + '&ref_id=' +id);
			
		})


		$(".action-filtre").find("input").click(function(e) {
			e.preventDefault();
			adherent_id=$(this).attr("data-adherent-id");
			type=$(this).attr("data-type");
			url=$(this).attr("data-url");

			date_deb=$(this).parent().parent().find(".date-deb").find("input").val();
			date_fin=$(this).parent().parent().find(".date-fin").find("input").val();


			$.ajax({
				url: "/admin/adherent/contenu/"+type+"/"+adherent_id+"?date_deb="+date_deb+"&date_fin="+date_fin+"&url"+url,

				success: function(result) {
					console.log(result);
					$("#"+type+"_liste").html(result);
				}
			});

		});
	}

	/* fin */
	if($("#edit-field-activite-wrapper").length)
		$("#edit-field-activite-wrapper").find("select").change(function() {
			$("#edit-field-activite-save-wrapper").find("input").val($(this).val());
		});
	if($("#edit-field-activite").length)
		$("#edit-field-activite").change(function() {
			$("#edit-field-activite-save-wrapper").find("input").val($(this).val());
		});

	if($("#edit-field-ville").length)
		$("#edit-field-ville").find("select").change(function() {
			$("#edit-field-ville-save-wrapper").find("input").val($(this).val());
		});

	Drupal.behaviors.activites_agenda = {
		attach: function (context, settings) {

			$("#edit-field-ville select").change(function () {

				$.ajax({
					url: "/admin/villes/gps/"+$(this).val(),
					success: function(result) {
						console.log(result);
						if(result) {
							$(".geolocation-input-latitude").val(result.lat);
							$(".geolocation-input-longitude").val(result.lng);

						}


					}
				})


			})



			/*
			 * on insert le titre et résumé dans un bloc de mise en vant
			 */
			if($("#edit-group-mise-en-avant").find(".field--name-title").find("input").val() === '' || $("#edit-group-mise-en-avant").find(".field--name-title").find("input").val() === null) {
			//	$("#edit-group-mise-en-avant").find(".field--name-title").find("input").val($("#edit-title-wrapper").find("input").val());
			}
			if($("#edit-group-mise-en-avant").find(".field--name-field-resume").find("textarea").val() === '' || $("#edit-group-mise-en-avant").find(".field--name-field-resume").find("textarea").val() === null) {
				var resume = $("#edit-field-resume-wrapper").find("textarea").val()??$("#edit-body-wrapper").find("textarea").val();
				//$("#edit-group-mise-en-avant").find(".field--name-field-resume").find("textarea").val(resume.replace(/(<([^>]+)>)/gi, ""));
			}

			/* cas où on ajoute un adhérent depuis le formulaire client */
			if($("#adherent-client").length && $("#node-client-edit-form").length) {
				/* si on est en mode édition, une ville a déjà été enregistrée, donc save ville existe
				 * comme on ne peut (pas trouvé pour le moment) modifier le formulaire dans un contenu imbriqué
				 * on traite en JS
				 **/
				if($("#adherent-client").find(".field--name-field-ville-save").length) {
					ville_id=$("#adherent-client").find(".field--name-field-ville-save input").val();
					cp=$("#adherent-client").find(".field--name-field-code-postal input").val();
					$.ajax({
						url:  "/admin/ville/getByCp"+ville_id,
						success: function(result) {
							console.log(result);
							$(".form-ville").remove();
							output_html=document.createElement("div");
							$(output_html).addClass("js-form-item form-item form-ville");
							$(output_html).attr("id","edit-field-ville");
							output_html_label=document.createElement("label");
							$(output_html_label).text("Ville");
							output_html_select=document.createElement("select");
							$(output_html_select).addClass("form-select form-control");
							$(output_html_select).append("<option>Choisssez une ville ... </option>");
							for(item in result) {

								$(output_html_select).append("<option selected='selected' value='"+result[item].name+"'>"+result[item].name+"</option>");
							}

							$(output_html).append(output_html_label);
							$(output_html).append(output_html_select);
							$("#adherent-client").find(".field--name-field-code-postal").append(output_html);
						},
					});
				}

				$("#adherent-client").find(".field--name-field-code-postal").find("input").focusout(function() {
					$.ajax({
						url: "/admin/villes/getByCp/"+$(this).val(),
						success: function(result) {
							$(".form-ville").remove();
							output_html=document.createElement("div");
							$(output_html).addClass("js-form-item form-item form-ville");
							$(output_html).attr("id","edit-field-ville");
							output_html_label=document.createElement("label");
							$(output_html_label).text("Ville");
							output_html_select=document.createElement("select");
							$(output_html_select).addClass("form-select form-control");
							$(output_html_select).append("<option>Choisssez une ville ... </option>");
							for(item in result) {

								$(output_html_select).append("<option value='"+result[item].name+"'>"+result[item].name+"</option>");
							}

							$(output_html).append(output_html_label);
							$(output_html).append(output_html_select);
							$("#adherent-client").find(".field--name-field-code-postal").append(output_html);
							$("#adherent-client").find(".field--name-field-ville-save").find("input").attr('type', 'hidden');
							$("#adherent-client").find(".field--name-field-ville-save").addClass('hidden');

							/* on ajoute l'action jQuery */
							$("#adherent-client").find("#edit-field-ville").find("select").change(function() {
								$("#adherent-client").find(".field--name-field-ville-save").find("input").val($(this).val());
								
							});

						},

					});

				});
			}

			/** !!! L'appel ajax méthode form api ne fonctionne plus après un premier appel ajax !!! ? */
			/* on passe à la méthode JS */
			if($(".field--name-field-code-postal").length) {

				$(".field--name-field-code-postal").focusout(function() {
					console.log($(this).find("input").val());
					$.ajax({
						url: "/admin/villes/getByCp/"+$(this).find("input").val(),
						success: function(result) {
							console.log(result);
							$("#edit-field-ville").html('<label>Ville</label>');
							ville=document.createElement("select");
							/*$("#edit-field-ville select option").each(function() {
								$(this).remove();
							});*/
							$(ville).append("<option>Choisssez une ville ... </option>");
							for(item in result) {

								$(ville).append("<option value='"+result[item].name+"'>"+result[item].name+"</option>");
							}
							$(ville).addClass('form-control');
							$("#edit-field-ville").append(ville);

							$("#edit-field-ville").find("select").change(function() {
								$(".field--name-field-ville-save").find("input").val($(this).val());
							});

						},

					});
				});

			}


			if($("#edit-field-ville").length)
				$("#edit-field-ville").find("select").change(function() {
					$("#edit-field-ville-save-wrapper").find("input").val($(this).val());
				});

			$("#bloc-ville").find("select").change(function() {
				$("#edit-field-ville-save-wrapper").find("input").val($(this).val());
			});

			$("#activites").change(function() {
				console.log($(this).val());
				$("#edit-field-activite-save-wrapper").find("input").val($(this).val());
				let gps = JSON.parse(jQuery(this).attr('data-gps'));

				for(item of gps) {

					if(item.id === $(this).val()) {
						jQuery('#edit-field-geolocation-demo-single-0-lat').val(item.gps.lat);
						jQuery('#edit-field-geolocation-demo-single-0-lng').val(item.gps.lng);
					}
				}
				
			});

			$(".field--name-field-type").find("select").change(function() {

				$(".field--name-field-newsletter").hide();
				if($(this).val()==2) $(".field--name-field-newsletter").show();
				else $(".field--name-field-newsletter").find("select").val("");
			});

			if($(".field--name-field-newsletter").find("select").val()=="_none")
				$(".field--name-field-newsletter").hide();

			if($("#edit-field-adherent-wrapper").find("select").val()) {
				$(".field--name-field-adherent-cache").find("input").val($("#edit-field-adherent-wrapper").find("select").val());
			}

			$("#edit-field-adherent-wrapper").find("select").change(function() {
				$(".field--name-field-adherent-cache").find("input").val($(this).val());

			});
		}
	}

	

	$.fn.putSelect2 = function(argument) {
		console.log(argument);
	}

	$.fn.putGps = function(argument) {
		let gps = JSON.parse(argument);
		
		jQuery('#edit-field-geolocation-demo-single-0-lat').val(gps.lat);
		jQuery('#edit-field-geolocation-demo-single-0-lng').val(gps.lng);


	}

	$.fn.getAjaxVille = function(argument) {
		 console.log(argument);
		 //ville=document.createElement("select");

		 $("#edit-field-ville").html("")
		 $("#edit-field-ville").append("<option value=''></option>");
//$("#edit-field-ville-wrapper").html(argument);
		 for(item in argument) {
		 	$("#edit-field-ville").append("<option value='"+argument[item].key+"'>"+argument[item].val+"</option>");
		 }

	}

	$.fn.getAjaxVille2 = function(argument) {
		 console.log(argument);
		 $("#bloc-ville").removeClass("form-select");
		 ville=document.createElement("select");
		 $(ville).attr("class","form-select form-control");
		 $(ville).append("<option value=''></option>");

		 for(item in argument) {
		 	$(ville).append("<option value='"+argument[item].key+"'>"+argument[item].val+"</option>");
		 }

		 $("#bloc-ville").append(ville);

	}



	$.fn.getAjaxCoordonnees = function(argument) {


		$("#edit-field-adresse-wrapper").find("input").val("");
		$("#edit-field-telephone-wrapper").find("textarea").val("");
		$("#edit-field-email-wrapper").find("input").val("");
		$("#edit-field-code-postal-wrapper").find("input").val("");
		$("#edit-field-lien-wrapper").find("input").val("");
		if(argument) {
			if(argument.adresse) $("#edit-field-adresse-wrapper").find("input").val(argument.adresse.value);
			if(argument.code_postal) $("#edit-field-code-postal-wrapper").find("input").val(argument.code_postal.value);
			if(argument.telephone) $("#edit-field-telephone-wrapper").find("textarea").val(argument.telephone.value);
			if(argument.email) $("#edit-field-email-wrapper").find("input").val(argument.email.value);
			if(argument.lien) $("#edit-field-lien-wrapper").find("input").val(argument.lien.value);
			if(argument.ville) {
				console.log(argument.ville.target_id+"-"+$(this).val());

				//$("#edit-field-ville option").prop("disabled","false");
				$("#edit-field-ville").find("option").each(function () {

					$(this).text(argument.ville.name);
					$(this).val(argument.ville.id);
					$(this).attr("selected","selected");
					/*
					$(this).removeAttr("disabled");
					$(this).removeAttr("selected");
					if($(this).val()!=argument.ville.id) {
						$(this).prop("disabled",true);

					} else {
						$(this).attr("selected","selected");
					}*/
					console.log($(this).val());
				})
			}
			if(argument.activites) {
				for(activite in argument.activites) {
					o=document.createElement("option");
					console.log(activite)
					$(o).val( argument.activites[activite].id);
					$(o).text(argument.activites[activite].name);
					$("#edit-field-activite").append(o)

				}
			}
		}

	};
})
