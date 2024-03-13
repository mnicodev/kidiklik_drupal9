$ = jQuery;

$(function(){
	data = {
		text: $('#geoloc').attr('data-save-text'),
		id: $('#geoloc').attr('data-save-id')
	}
	$('#geoloc').select2({
		placeholder: 'Où ?',
		allowClear: true,
		ajax: {
			url: '/kidiklik_front/search_geoloc',
			dataType: 'json',
      		multiple: true,
			tags: true,
			allowClear: true,
			data: function (params) {
				var query = {
					search: params.term,
					type: 'public'
				}
				return query;
			},
		}
	});
	
	var newOption = new Option(data.text,data.id, true, true);
	$('#geoloc').append(newOption).trigger('change');
})
