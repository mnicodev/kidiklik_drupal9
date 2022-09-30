var bloc_mea=[];


function blocAction(o) {
	$(o).find(".btn-danger").click(function () {
		//alert("ok")
		$(this).parent().parent().remove();

	})

}

function getBloc(obj) {
console.log(obj.nid);
	bloc=document.createElement("div");
	bloc_image=document.createElement("div");
	bloc_data=document.createElement("div");
	bloc_titre=document.createElement("div");
	bloc_resume=document.createElement("div");
	bloc_bouton=document.createElement("div");
	titre=document.createElement("input");
	nid=document.createElement("input");
	fid=document.createElement("input");
	resume=document.createElement("textarea");
	bouton=document.createElement("button");
  lien=document.createElement("input");

	$(fid).attr("type","hidden");
	$(fid).attr("name","fid[]");
	$(fid).val(obj.fid);
	$(nid).attr("type","hidden");
	$(nid).attr("name","nid_bloc[]");
	$(nid).val(obj.nid);

  $(lien).val(obj.lien);
  $(lien).attr('type','hidden');
  $(lien).attr('name','lien[]');

	$(bloc_bouton).attr("class","col-md-12 space");

	$(bouton).attr("class","btn btn-danger pull-right btn-social");
	$(bouton).attr("type","button");
	$(bouton).append("<li class='fa fa-remove'></li>Effacer");

	$(bloc_data).attr("class","col-md-8");
	$(bloc_image).attr("class","col-md-4");

	$(bloc_titre).attr("class","form-group");
	$(bloc_resume).attr("class","form-group");
	$(bloc_image).append("<img src='"+obj.image+"' \>");
	$(titre).attr("name","titre_bloc[]");
	$(titre).attr("class","form-control");
	$(titre).val(obj.titre);
	$(resume).attr("name","resume_bloc[]");
	$(resume).attr("class","form-control resize-vertical");
	$(resume).append(obj.resume);


	$(bloc_titre).append(titre);
	$(bloc_resume).append(resume);

	$(bloc_data).append(bloc_titre);
	$(bloc_data).append(bloc_resume);
	$(bloc_bouton).append(bouton);
	$(bloc).append(bloc_bouton);
	$(bloc).append(bloc_image);

	$(bloc).append(bloc_data);
	$(bloc).append(fid);
	$(bloc).append(nid);
	$(bloc).append(lien);
	if(obj.pid) {
		pid=document.createElement("input");
		$(pid).attr("type","hidden");
		$(pid).attr("name","pid[]");
		$(pid).val(obj.pid);
		$(bloc).append(pid);
	}
	$(bloc).attr("class","row");
	blocAction(bloc);
	return bloc;
}



$(function(){
	$(document).ready(function () {

		if($("#bloc-donnees").length) {
			nid=$("#nid").val();
			$.ajax({
				url: '/admin/newsletter/blocs-datas/'+nid,
				success: function (result) {
					if(result === null) {
						return null;
					}
					console.log(result)
					rs=JSON.parse(result);
					for(item in rs) {
						console.log(rs[item])
						$("#blocs_datas").append(getBloc(rs[item]));
						if($("#edit-bandeau-rose").is(":checked")) {
							$("#blocs_datas").find(".row").each(function () {
								$(this).addClass("pink");
								return false;
							})
						}
					}
				},

			})

		}

		$("#edit-bandeau-rose").click(function () {
			if($(this).is(":checked")) {

				$("#blocs_datas").find(".row").each(function () {
					$(this).addClass("pink");
					return false;
				})
			} else {
				$("#blocs_datas").find(".row").each(function () {
					$(this).removeClass("pink");
					return false;
				})
			}
		})
	})



	$.fn.putBlocContent = function (argument) {
		console.log(argument);


		add_bloc=1;
		$("#blocs_datas").find(".row").each(function () {
			if($(this).find(".nid").val()==argument.bloc.nid) {
        add_bloc=0;
      }

		})

		if(add_bloc) {
			$("#blocs_datas").append(getBloc(argument.bloc));


		}

	}
})
