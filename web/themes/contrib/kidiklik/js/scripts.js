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

	      	if (charging_blocs ===1 || charging_blocs === 0){
			jQuery('.ajout-non-connect').unbind('click');
			jQuery('.ajout-non-connect').click(function() {
				jQuery('.menu-user').find('.login-popup-form').click()
			});
			jQuery('#block-carre1copie').html(jQuery('#block-carreblock').html());
			jQuery('select[name="ville"]').select2({
				placeholder: 'Choisissez une ville ...',
				allowClear: true,
				tags: true,
			});

			if(jQuery('.bloc-publicite').length) {
				jQuery('.bloc-publicite').find('img').on('click', function(event) {
					event.preventDefault();
					var url = jQuery(this).parent().attr('href');
					var nid = jQuery(this).attr('data-nid');
					jQuery.ajax({
						url: '/kidiklik_front_publicite/addClick/' + nid,
						cache: false,
						contentType: false,
						processData: false,
						type: 'GET', // For jQuery < 1.9
						success: function(data){
							window.location = url;
						}
					})
				})
			}
			if(jQuery("#groupe-actions").length) {
				if(jQuery('#block-boutonfavorinonconnecte').length) {

					jQuery('#groupe-actions').append(jQuery('#block-boutonfavorinonconnecte').html());

					jQuery('#block-boutonfavorinonconnecte').remove();

				}
				jQuery('#groupe-actions').append(jQuery('#block-reserverblock').html());
				jQuery('#groupe-actions').append(jQuery('#block-sortiesboutonblock').html());
				if(!jQuery('.zone-image').find('#groupe-actions').length) {
					jQuery('.zone-image').append('<div class="groupe-actions">'+jQuery("#groupe-actions").html()+'</div>');
				}
				jQuery('#block-sortiesboutonblock').remove();
				jQuery('#block-reserverblock').remove();

				if(jQuery('.date-changed').length) {
					let date = jQuery('.date-changed').html();
					jQuery('.zone-image').append('<div class="fs-2 text-left font-italic">'+date+'</div>');
					jQuery('.date-changed').remove()
				}
			}
			jQuery('.form-item-quand').find('select').on('change',function() {
				if(jQuery(this).val() === 'date') {
					jQuery('.form-type-date').show();
					jQuery('#views-exposed-form-activites-recherche-activites').addClass('with-dates');
				} else {
					jQuery('.form-type-date').hide();
					jQuery('#views-exposed-form-activites-recherche-activites').removeClass('with-dates');
				}
			})
			if(jQuery('select[name="quand"]').val() === 'date') {
					jQuery('.form-type-date').show();
					jQuery('#views-exposed-form-activites-recherche-activites').addClass('with-dates');

			}
			jQuery('.user-register-form').find('.form-item-name').hide();
			jQuery('.user-register-form').find('.form-item-mail').find('input').on('change',function() {
				console.log(jQuery(this).val())
				jQuery('.user-register-form').find('.form-item-name input').val(jQuery(this).val())
			})

		}
		

		// chargement asynchrone des images
		jQuery('.lazy-img').each(function() {
			var self = jQuery(this);
      var parent = self.closest('.views-field-nothing');
      jQuery(parent).on('click', function() {
        window.location = jQuery(parent).find('a').attr('href');
      })
      //parent.css('background-image', 'url('+self.attr('data-src')+')');
      //self.hide();

		});


	} /* fin attach */
}

jQuery(document).ready(function() {

	
	if(jQuery('.console-recherche').length) {
		form = jQuery('.console-recherche');
		jQuery(form).find('.onoff').on('click', function() {
			if(jQuery(form).hasClass('view')) {
				jQuery(form).removeClass('view');
			} else {
				jQuery(form).addClass('view');
			}
		})
	}

	// chargement asynchrone des images
	jQuery('.lazy-img').each(function() {
		var self = jQuery(this);
      var parent = self.closest('div.views-field-nothing');
      jQuery(parent).on('click', function() {
        window.location = jQuery(parent).find('a').attr('href');
      })
      //parent.css('background-image', 'url('+self.attr('data-src')+')');
      //self.hide();

	});

	jQuery('article a').click(function(e) {
		if(!jQuery(this).hasClass('btn-voir-autres-dates') &&
			!jQuery(this).hasClass('voir-actualites') &&
			!jQuery(this).hasClass('reservation') &&
			!jQuery(this).hasClass('ajout-non-connect') &&
			!jQuery(this).hasClass('leaflet-control-zoom-in') &&
			!jQuery(this).hasClass('leaflet-control-zoom-out')) {
			e.preventDefault();
			url = jQuery(this).attr('href');
			window.open(url, 'blank','');
		}
	});

	jQuery('.paragraph--type--paragraphe').each(function() {
		let url = jQuery(this).find('.field--name-field-url a').attr('href');
		if(url !== undefined) {
			jQuery('img').css('cursor','pointer');
		}
	})
	jQuery('.paragraph--type--paragraphe').find('img').click(function() {
		let url = jQuery(this).closest('.paragraph').find('.field--name-field-url a').attr('href');

		if(url !== undefined) {
			window.open(url,'_blank','');
		}

	})
			jQuery('.ajout-non-connect').click(function() {
				jQuery('.menu-user').find('.login-popup-form').click()
			});
	/*if(jQuery('.zone-flag').length) {
		jQuery('.zone-flag').append(jQuery('#block-sortiesboutonblock').html());
		jQuery('.zone-flag').append(jQuery('#block-reserverblock').html());
	}*/

	if(jQuery(".btn-voir-autres-dates").length) {
		jQuery(".btn-voir-autres-dates").on('click', function() {
			if(jQuery(".field--name-field-date").height() == '20') {
				h="auto";
			} else {
				h="20px";
			}
			jQuery(".field--name-field-date").css('height',h);
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

		jQuery('#mobi_change_dep').select2();
		jQuery('#mobi_change_dep').on('select2:select', function(e) {
			var data = e.params.data;
			window.location="http://"+data.id+"."+window.domain_name;
		});

  jQuery(".icon-menu").on('click', function () {
	 //jQuery('.icon-menu-open').show();
		jQuery('#mobimenu .navbar-haut').addClass('slide-menu');
	// jQuery('#mobimenu .navbar-haut').append(jQuery('.icon-menu-open'));
    	jQuery('.shadow').show();
		jQuery('#entete').css('z-index','2');
	 	jQuery('#menu').hide();
		jQuery('#mobimenu').show();
	/* jQuery('#mobimenu').append(jQuery('#menu').html());



	 setTimeout(function() {
		jQuery('#mobimenu .navbar-haut').addClass('slide-menu');

	 }, 100)*/


    jQuery('.icon-menu-open').on('click', function () {

	  jQuery('#entete').css('z-index','2');

      jQuery('#mobimenu .navbar-haut').removeClass('slide-menu');
	  setTimeout(function() {
	//	jQuery('body').append(jQuery('#mobimenu .icon-menu-open'));
	//	jQuery('#mobimenu').html('');
		jQuery('.shadow').hide();
		jQuery('#menu').show();
	},100)


    })
  })

  node = jQuery("#newsletter-form").attr('data-drupal-selector');

	var url=new URL(window.location.href);
	var email = url.searchParams.get('record_email');
	console.log(email);
  if(email !== undefined && email !== null && email !== '') {
      jQuery('.main-container .highlighted').html('<div class="alert alert-secondary">Envoi de la demande d\'inscription. Veuillez patienter ...</div>');
      jQuery.ajax({
        url: window.url_mailjet,
        data: {email:email, dept: window.departement},
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
		allowClear: true,
		tags: true,
	});
	
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
    } else {
	    window.location = "/kidiklik-recrute.html";
    }
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
