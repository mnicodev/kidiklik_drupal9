(function($) {
  // Argument passed from InvokeCommand.
  $.fn.myAjaxCallback = function(argument) {
    console.log('myAjaxCallback is called.');
    // Set textfield's value to the passed arguments.
    $('input#edit-output').attr('value', argument);
  };
})(jQuery);

Drupal.behaviors.kidiklik = {
	attach: function(context, settings) {
		var charging_blocs = jQuery('[data-big-pipe-placeholder-id]').length;
	      	if (charging_blocs ===1){
			if(jQuery(".zone-flag").length) {
				jQuery('.zone-image').append(jQuery(".zone-flag").html());
			}
			jQuery('.form-item-quand').find('select').on('change',function() {
				console.log(jQuery(this).val())
				if(jQuery(this).val() === 'date') {
					jQuery('.form-type-date').show();
					jQuery('#views-exposed-form-activites-recherche-activites').addClass('with-dates');
				} else {
					jQuery('.form-type-date').hide();
					jQuery('#views-exposed-form-activites-recherche-activites').removeClass('with-dates');
				}
			})
			if(jQuery('.field--name-field-image').length) {
				jQuery('.image_kidiklik_old').remove();
			}
		}
		if(jQuery('select[name="ville"]').val() === 'geo') {
				getCurrentPosition();
			//maPosition();
		} else {
			jQuery('input[name="center[coordinates][lng]"]').val('');
			jQuery('input[name="center[coordinates][lat]"]').val('');
		}
		jQuery('select[name="ville"]').on('select2:select', function(e) {
			var data = e.params.data;
			console.log('ok')
			if(data.id === "geo") {
				getCurrentPosition();
				//maPosition();
			} else {
				jQuery('input[name="center[coordinates][lng]"]').val('');
				jQuery('input[name="center[coordinates][lat]"]').val('');
			}
		});
		jQuery('select[name="ville"]').on('select2:clearing', function(e) {
			jQuery('input[name="center[coordinates][lng]"]').val('');
			jQuery('input[name="center[coordinates][lat]"]').val('');
		});
	}
}

jQuery(document).ready(function() {
	/*if(jQuery('.field--name-field-image').length) {
		jQuery('.image_kidiklik_old').remove();
	}*/

	if(jQuery(".field--name-field-date").length) {
		jQuery(".field--name-field-date .field--items").on('click', function() {
			if(jQuery(".field--name-field-date").height() == '20') {
				h="100%";
			} else {
				h="20px";
			}
			jQuery(".field--name-field-date").animate({
				height: h,
			}, 100, function() {

			});
		})

	}


  if(jQuery('#edit-field-rubriques-activite-target-id').length) {
   /* jQuery('#edit-field-rubriques-activite-target-id').select2({
      placeholder: 'Choisissez une rubrique ...',
      allowClear: true
    })*/
  }
  if(jQuery('.message-contact').length) {
    jQuery('h1').html(jQuery('.field--name-body h2').html());
    jQuery('.field--name-body h2').remove();
  }

  jQuery(".icon-menu").on('click', function () {
    jQuery('.shadow').show();
    jQuery('.navbar-haut').addClass('slide-menu');
    jQuery('.icon-menu-open').show();

    jQuery('.icon-menu-open').on('click', function () {
      jQuery('.shadow').hide();
      jQuery('.icon-menu-open').hide();
      jQuery('.navbar-haut').removeClass('slide-menu');
    })
  })

  node = jQuery("#newsletter-form").attr('data-drupal-selector');

  if(window.email !== undefined) {
    //jQuery("#newsletter-form").attr('method', 'get');
    if(window.email !== null && window.email !== '') {
      jQuery('.main-container .highlighted').html('<div class="alert alert-secondary">Envoi de la demande d\'inscription. Veuillez patienter ...</div>');
      jQuery.ajax({
        url: window.url_mailjet,
        data: {email:window.email, dept: window.departement},
        success: function(response) {
          if(response === 'ok') {
            msg = '<div class="alert alert-success">Inscription réussie</div>';
          } else {
            msg = '<div class="alert alert-danger">'+response+'</div>';
          }
          jQuery('.main-container .highlighted').html(msg);
        }

      })
    }

  }

	jQuery('.bloc-publicite img').each(function() {
		nid=jQuery(this).attr('data-nid');
		jQuery.ajax({
			url: window.url_statistiques,
			data: "nid="+nid,
			method: "POST",
			success: function(data) {
			},
		});
	});

    //jQuery('.form-search').html(jQuery('.view-activites .view-filters').html());
    //jQuery('.view-activites .view-filters').html('');
	jQuery('#search_ville').select2({
		placeholder: 'Choisissez une ville ...',
		ajax: {
			url: '/kidiklik_front/search_city',
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
	jQuery('select[name="ville"]').select2({
		placeholder: 'Choisissez une ville ...',
		allowClear: true
	});
	if(jQuery('select[name="ville"]').val() === 'geo') {
			getCurrentPosition();
		//maPosition();
	} else {
		jQuery('input[name="center[coordinates][lng]"]').val('');
		jQuery('input[name="center[coordinates][lat]"]').val('');
	}
	jQuery('select[name="ville"]').on('select2:select', function(e) {
		var data = e.params.data;
		console.log('ok')
		if(data.id === "geo") {
			getCurrentPosition();
			//maPosition();
		} else {
			jQuery('input[name="center[coordinates][lng]"]').val('');
			jQuery('input[name="center[coordinates][lat]"]').val('');
		}
	});
	jQuery('select[name="ville"]').on('select2:clearing', function(e) {
		jQuery('input[name="center[coordinates][lng]"]').val('');
		jQuery('input[name="center[coordinates][lat]"]').val('');
	});
	jQuery('select[name="ville"]').on('select2:open', function(e) {

	})

	//jQuery("#views-exposed-form-activites-recherche-activites").attr('action', '/recherche');


	if(jQuery('select[name="quand"]').val() === 'date') {
			jQuery('#views-exposed-form-activites-recherche-activites .form-type-date').show();
	} else {
		jQuery('#views-exposed-form-activites-recherche-activites .form-type-date').hide();
	}

	jQuery('select[name="quand"]').on('change', function() {
		if(jQuery(this).val() === 'date') {
			jQuery('#views-exposed-form-activites-recherche-activites .form-type-date').show();
		} else {
			jQuery('#views-exposed-form-activites-recherche-activites .form-type-date').hide();
			jQuery('#views-exposed-form-activites-recherche-activites .form-type-date').find('input').val('');
		}
	});

	jQuery('#search_ville').on('select2:select', function (e) {
		var data = e.params.data;

		window.location="http://"+data.id+"."+window.domain_name;
	});

	jQuery('#search_dep').select2({
		placeholder: 'Choisissez un département ...',
		ajax: {
			url: '/kidiklik_front/search_dep',
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
	jQuery('#change_dep').select2();
	jQuery('#change_dep').on('select2:select', function(e) {
		var data = e.params.data;

		window.location="http://"+data.id+"."+window.domain_name;
	});
	jQuery('#search_dep').on('select2:select', function (e) {
		var data = e.params.data;

		window.location="http://"+data.id+"."+window.domain_name;
	});

	jQuery('#complete_map path').on('click', function (e) {
    actif = parseInt(jQuery(this).attr("data-active"));
    e.preventDefault();
    if (actif) {
      dep = jQuery(this).attr("data-num");
      url = dep + "." + window.domain_name;

      window.location = "https://" + url;
    } else window.location = window.domain_name+"/kidiklik-recrute.html";
  });



  function maPosition(position) {//position
		lng = position.coords.longitude;
		lat = position.coords.latitude;
		console.log(position)
		//lng=1.7945;
		//lat=47.820614;
		 jQuery('input[name="center[coordinates][lng]"]').val(lng);
		 jQuery('input[name="center[coordinates][lat]"]').val(lat);
	}
	function erreur(err) {
		console.log("erreur localisation : "+err.message)
		window.alert("erreur localisation : "+err.message)
		//$(".shadow").hide();
	}

	function getCurrentPosition() {
		if(navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(maPosition,erreur);
		}
	}
})
