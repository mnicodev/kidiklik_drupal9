$ = jQuery;

$(function(){
	if($(".form-textarea-wrapper").length) {
		counter=document.createElement("div");
		limit=150;
		$(counter).html("Nombre de caractères restants : <span id='counter'></span>");
		$(".form-textarea-wrapper").append(counter);
		$(".form-textarea-wrapper").find("textarea").keyup(function(event) {
			text=$(this).val();
			console.log(text.length)
			$("#counter").text(limit-text.length);


		});
	}
	$(window).scroll(function() {
		if($(window).scrollTop()>100) {
			$(".sidebar-menu").addClass("affix");
			$(".sidebar-menu").css("top","0px");
		} else {

			$(".sidebar-menu").removeClass("affix");
		}
	});
	$(".sidebar-toggle").click(function () {
		/*console.log($(".sidebar-menu").find("li.prem").find("i.fa-circle-o").length)
		if($(".sidebar-menu").find("li.prem").find("i.fa-circle-o").length) 
			$(".sidebar-menu").find("li.prem").find("i.fa-circle-o").remove();
		else 
			$(".sidebar-menu").find("li.prem").append("<i class='fa fa-circle-o'></i>");*/
	})
	
	$(".sidebar-menu").find("li").each(function () {
		icone=$(this).find("a").find("span").find("i").attr("class");
		/*if($(this).find("a").find("i").hasClass("fa-circle-o"))
			$(this).find("a").find("i.fa-circle-o").attr("class",icone);*/
	})

	$('ol.breadcrumb li').each(function(index, value){
		if( $.trim($(value).html()).length == 0 )
			$(value).remove();
	});

    /*$(".views-field-operations").find(".dropbutton").find(".delete").find("a").each(function () {
		$(this).addClass("use-ajax");
		$(this).attr("data-dialog-options","{&quot;width&quot;:400}");
		$(this).attr("data-dialog-type","modal");
	})*/
	
})
