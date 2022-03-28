//Показывает кнопку для движения списка меню
function MainmenuBtnMoveShow(t, btn_nach, btn_kon, num_nach, num_kon, flag) {
	var div = $(t).find("div");
	var ul = div.children("ul");
	div.removeClass("elem_none_imp").addClass("elem_block_imp");
	/*if (flag) {
		div.find("li").find("div").removeClass("elem_block_imp").addClass("elem_none_imp");
	} else {
		div.find("li").find("div").removeClass("elem_none_imp").addClass("elem_block_imp").find("li").find("div").removeClass("elem_block_imp").addClass("elem_none_imp");
	}*/
	if (ul.children("li").eq(num_nach).css("display") == "none") {
		div.children(btn_nach).removeClass("elem_none_imp").addClass("elem_block_imp");
	} else {
		div.children(btn_nach).removeClass("elem_block_imp").addClass("elem_none_imp");
	}
	if (ul.children("li").eq(num_kon).css("display") == "none") {
		div.children(btn_kon).removeClass("elem_none_imp").addClass("elem_block_imp");
	} else {
		div.children(btn_kon).removeClass("elem_block_imp").addClass("elem_none_imp");
	}
}
	
//Движение списка меню к началу
function MainmenuListMoveToBegin(t, btn_begin_class, btn_end_class, gorisontal, elem) {
	if (elem == "_imp") {
		var elem = "_imp";
		var num = 5;
	} else {
		var elem = "_inline";
		var num = 1;
	}
	var div = $(t).parent();
	var ul = div.children("ul");
	var index = ul.children("li").index(ul.children("li.elem_block"+elem).eq(num));
	ul.children("li.elem_block"+elem).eq(0).removeClass("elem_block"+elem).addClass("elem_none"+elem);
	ul.children("li").eq(index + 1).removeClass("elem_none"+elem).addClass("elem_block"+elem);
	div.find(btn_begin_class).removeClass("elem_none"+elem).addClass("elem_block"+elem);
	if (ul.children("li").index(ul.children("li").eq(index + 2)) == -1) {
		div.find(btn_end_class).removeClass("elem_block"+elem).addClass("elem_none"+elem);
	}
	if (gorisontal == 1) {
		ul.children("li.elem_block"+elem).children("p").removeClass("mainmenu_p_last").removeClass("mainmenu_p_first").addClass("mainmenu_p_else");
		ul.children("li.elem_block"+elem).eq(0).children("p").removeClass("mainmenu_p_last").removeClass("mainmenu_p_else").addClass("mainmenu_p_first");
		ul.children("li.elem_block"+elem).eq(num).children("p").removeClass("mainmenu_p_first").removeClass("mainmenu_p_else").addClass("mainmenu_p_last");
	}
	div.removeClass("elem_none"+elem).addClass("elem_block"+elem);
}
	
//Движение списка меню к концу
function MainmenuListMoveToEnd(t, btn_begin_class, btn_end_class, gorisontal, elem) {
	if (elem == "_imp") {
		var elem = "_imp";
		var num = 5;
	} else {
		var elem = "_inline";
		var num = 1;
	}
	var div = $(t).parent();
	var ul = div.children("ul");
	var index = ul.children("li").index(ul.children("li.elem_block"+elem).eq(0));	
	ul.children("li.elem_block"+elem).eq(num).removeClass("elem_block"+elem).addClass("elem_none"+elem);
	ul.children("li").eq(index - 1).removeClass("elem_none"+elem).addClass("elem_block"+elem);
	div.find(btn_end_class).removeClass("elem_none"+elem).addClass("elem_block"+elem);
	if ((index - 2) < 0) {
		div.find(btn_begin_class).removeClass("elem_block"+elem).addClass("elem_none"+elem);
	}
	if (gorisontal == 1) {
		ul.children("li.elem_block"+elem).children("p").removeClass("mainmenu_p_last").removeClass("mainmenu_p_first").addClass("mainmenu_p_else");
		ul.children("li.elem_block"+elem).eq(0).children("p").removeClass("mainmenu_p_last").removeClass("mainmenu_p_else").addClass("mainmenu_p_first");
		ul.children("li.elem_block"+elem).eq(num).children("p").removeClass("mainmenu_p_first").removeClass("mainmenu_p_else").addClass("mainmenu_p_last");
	}
	div.removeClass("elem_none"+elem).addClass("elem_block"+elem);
}

//Смена изображений в галерее
function ZoomMoving(t) {
	var zoom_moving = document.getElementById("tabs_zoom_moving").parent_obj;
	var small_arr = $(t).children("img").eq(0).attr("src").split("/80/");
	var little_arr = zoom_moving.little_img.src.split("/380/");
	$(t).children("img").eq(0).attr("src", little_arr[0] + "/80/" + little_arr[1]);
	zoom_moving.little_img.src = small_arr[0] + "/380/" + small_arr[1];
	zoom_moving.big_img.src = small_arr[0] + "/" + small_arr[1];
}

//Вывод аякс-окон
function showAjaxWindow(id){
	if (!document.getElementById("ajax_window"+id)){
        $.ajax({
            type: "POST",
            url: "index.php",
            data: "ajax_window=1&aux_page=" + id, 
            dataType: "text",
            success: function(msg){
                var arr = msg.split("|||");
                var div = document.createElement("div");
                div.id = "ajax_window" + id;
				div.className="ajax_window_class";
				div.innerHTML = '<a href="javascript:void(0)" class="ajax_window_close" onclick="hideAjaxWindow()">Закрыть</a>'+
				'<div class="ajax_window_name">'+
					arr[0]+
				'</div>'+
				'<div class="ajax_window_doc">'+
					arr[1]+
				'</div>';
                document.body.appendChild(div);
				$( ".lightbox" ).css("display", "block");
            }
        });
    } else {
		$( "#ajax_window"+id ).css("display", "block");
		$( ".lightbox" ).css("display", "block");
    }
}

function hideAjaxWindow(){	
	$(".ajax_window_class").css("display", "none");
	$(".lightbox").css("display", "none"); 
}

function CatSliderInterval(arr, i, place) {
	var place = (place) ? place : "";
	if (i>0) {
		var j = i - 1;
		$("#img_slider"+place+"_"+j).css("display", "none");
	} 
	$("#img_slider"+place+"_"+i).css("display", "block");
	if (i < arr.length) {i++;} else {i = 0;}
	setTimeout(function() {CatSliderInterval(arr, i, place);}, 10000);
}


//--------------------------------------------------------------------------------------------

$(document).ready(function(){	
	//Наведение мыши на раскрывающий пункт меню
	$(".mainmenu_text").mouseenter(function() {
		MainmenuBtnMoveShow(this, ".mainmenu_pred_big", ".mainmenu_next_big", 0, 6, false);
		//$(this).children("div").removeClass("elem_none_imp").addClass("elem_block_imp").children("li").children("li").children("div").removeClass("elem_block_imp").addClass("elem_none_imp");
		if ($(this).children("div").children("ul").children("li").length < 5) {
			$(this).children("div").css("left", "221px");
		}			
	}).mouseleave(function() {
		$(this).children("div").removeClass("elem_block_imp").addClass("elem_none_imp").children("li").children("li").children("div").removeClass("elem_none_imp").addClass("elem_block_imp");
	});
	
	//Перелистывание по горизонтале
	$("#mainmenu").find(".mainmenu_next_little").click(function(){
		MainmenuListMoveToBegin(this, ".mainmenu_pred_little", ".mainmenu_next_little", 0, "1");
		$("#mainmenu").children("ul").children("li.elem_block_inline:last").prev().children(".mainmenu_next").css("display", "block");
		$("#mainmenu").children("ul").children("li.elem_block_inline:last").prev().css("padding", "0 30px 0 0");
	});
	$("#mainmenu").find(".mainmenu_pred_little").click(function(){
		MainmenuListMoveToEnd(this, ".mainmenu_pred_little", ".mainmenu_next_little", 0, "1");
		$("#mainmenu").children("ul").children("li.elem_block_inline:last").children(".mainmenu_next").css("display", "none");
		$("#mainmenu").children("ul").children("li.elem_block_inline:last").css("padding", "0");
	});
	$(".mainmenu_text").find(".mainmenu_next_big").click(function(){
		MainmenuListMoveToBegin(this, ".mainmenu_pred_big", ".mainmenu_next_big", 1, "_imp");
	});
	$(".mainmenu_text").find(".mainmenu_pred_big").click(function(){
		MainmenuListMoveToEnd(this, ".mainmenu_pred_big", ".mainmenu_next_big", 1, "_imp");
	});
	
	/*
	$(".mainmenu_text").find("li").find("li").mouseenter(function() {
		MainmenuBtnMoveShow(this, ".btn_top", ".btn_bottom", 0, 6, true);
		var ul = $(this).closest("li.mainmenu_text").children("div").eq(0).children("ul").eq(0);
		var li = $(this).closest("li.mainmenu_two");
		if (ul.children("li.elem_block_imp").index(li) > 3) {
			$(this).children("div").eq(0).css("left", "-132px");
			for (var i = 0; i <= 5; i++) {
				var n = 101 + i;
				ul.children("li.elem_block_imp").eq(i).css("z-index", "" + n);
			}
		} else {
			for (var i = 0; i <= 5; i++) {
				var m = 5 - i;
				var n = 101 + m;
				ul.children("li.elem_block_imp").eq(i).css("z-index", "" + n);
			}
		}
	}).mouseleave(function() {
		$(this).find("div").removeClass("elem_block_imp").addClass("elem_none_imp");
	});
	
	$(".mainmenu_text").find("li").find("li").find("li").mouseenter(function() {
		MainmenuBtnMoveShow(this, ".btn_top", ".btn_bottom", 0, 6, true);
		$(this).children("div").eq(0).css("top", "auto").css("bottom", "-15px");
		if ($(this).parent().parent().parent().parent().parent().parent().parent().children("li.elem_block_imp").index($(this).parent().parent().parent().parent().parent().parent()) > 3) {
			$(this).children("div").eq(0).css("left", "-132px");
		}
	}).mouseleave(function() {
		$(this).find("div").removeClass("elem_block_imp").addClass("elem_none_imp");
	});
	
	$(".mainmenu_text").find("li").find("li").find("li").find("li").mouseenter(function() {
		MainmenuBtnMoveShow(this, ".btn_top", ".btn_bottom", 0, 6, true);
		$(this).children("div").eq(0).css("top", "-5px").css("bottom", "auto");
		if ($(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().children("li.elem_block_imp").index($(this).parent().parent().parent().parent().parent().parent().parent().parent().parent()) > 3) {
			$(this).children("div").eq(0).css("left", "-132px");
		}
	}).mouseleave(function() {
		$(this).find("div").removeClass("elem_block_imp").addClass("elem_none_imp");
	});

	//Перелистывание по вертикале
	$(".mainmenu_text").find(".btn_bottom").click(function(){
		MainmenuListMoveToBegin(this, ".btn_top", ".btn_bottom", 0, "_imp");
	});
	$(".mainmenu_text").find(".btn_top").click(function(){
		MainmenuListMoveToEnd(this, ".btn_top", ".btn_bottom", 0, "_imp");
	});
	*/
	
	//Фокус ввода в "Поиск"
	$("#search").find("input").focus(function(){
		if ($(this).val() == "ПОИСК") {
			$(this).val("");
		}
	}).focusout(function(){
		if ($(this).val() == "") {
			$(this).val("ПОИСК");
		} 		
	});
	
	var blocks = $("#prod_desc").children(".tab_block"),
		len = blocks.length;
	if (len && len > 0)	{
		for (var i=0; i<len; i++) {
			if (i == 0) {
				$("#tabs").children("ul").eq(1).find(".prod_description").append(blocks.eq(i).clone());
			} else {
				$("#tabs").children("ul").eq(0).append('<li class="tabs_passive"><a href="javascript:void(0)" class="tabs_name"></a></li>');
				$("#tabs").children("ul").eq(0).find(".tabs_name").eq(i).html(blocks.eq(i).children(".tab_block_name").eq(0).html());
				$("#tabs").children("ul").eq(0).find(".tabs_name").eq(i).attr("href", "#"+blocks.eq(i).children(".tab_block_href").eq(0).html());
				//$("#tabs").children("ul").eq(1).append('<li class="elem_none_imp"><div class="tabs_doc"></div></li>');
				//$("#tabs").children("ul").eq(1).find(".tabs_doc").eq(i).append(blocks.eq(i).clone());
			}	
		}
		$("#tabs").children("ul").eq(1).find(".tab_block_name").remove();
		$("#prod_desc").remove();
	}
	
	//Клик по табу
	/*$("#tabs").children("ul").children("li").children("a").click(function(){
		$("#tabs_doc").find("li.elem_block_imp").removeClass("elem_block_imp").addClass("elem_none_imp");
		$("#tabs").find("li.tabs_active").removeClass("tabs_active").addClass("tabs_passive");
		$(this).parent().removeClass("tabs_passive").addClass("tabs_active");
		$("#tabs_doc").children("li").eq($(this).parent().parent().find("li").index($(this).parent())).removeClass("elem_none_imp").addClass("elem_block_imp");
	});*/
	
});