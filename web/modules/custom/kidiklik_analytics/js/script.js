$(function() {
	$("#data").html("CHARGEMENT DES DONNEES GOOGLE ... ");
	$.ajax({
		url: "/kidiklik_analytics/content_data",
		type: "POST",
		data: "dep="+dep,
		success: function(result,statut) {
			console.log(result);
		}
	});
})
