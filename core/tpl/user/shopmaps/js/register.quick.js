function addHandler(object, event, handler, useCapture) {
      if (object.addEventListener) {
            object.addEventListener(event, handler, useCapture ? useCapture : false);
      } else if (object.attachEvent) {
            object.attachEvent('on' + event, handler);
      } 
}

function removeHandler(object, event, handler) {
      if (object.removeEventListener) {
            object.removeEventListener(event, handler, false);
      } else if (object.detachEvent) {
            object.detachEvent('on' + event, handler);
      } 
}
        var unloadFlag = false;
        if (window.addHandler) {	
            function addUnload() {
                if (!unloadFlag) {
                    addHandler(window, "beforeunload", unloadHandler);
                    unloadFlag = true;
                }
            }
            
            function unloadHandler(evt) {
                evt = evt || window.event;
                evt.returnValue = "Оформление не окончено, заказ не отправлен."; // [[ Менять можно только этот текст!!! ]]
            }
            function removeUnload() {
                removeHandler(window, "beforeunload", unloadHandler);
                unloadFlag = false;
            }
        }
		var user_change=false;
		
                function submitF(save1)
                {
				removeUnload();
				document.getElementById('save3').value=save1;
                document.getElementById('RegisterForm').submit();
                }
                function change_country_handler(sel_text)
                {
				curent_country=sel_text;
				navigat=document.getElementById('navigator_download');
				if (user_change!=3)
				{
				if (sel_text=='Другая')
				{
				$("#select_shipping_method_4")[0].checked=true;
				$("#select_shipping_method_3")[0].checked=false;
				$("#select_shipping_method_0")[0].checked=false;
				$("#select_payment_method_0")[0].checked=false;
				$("#select_payment_method_1")[0].checked=true;
				}
				else
				{
				$("#select_shipping_method_4")[0].checked=false;
				$("#select_shipping_method_3")[0].checked=false;
				$("#select_shipping_method_0")[0].checked=true;
				$("#select_payment_method_0")[0].checked=true;
				$("#select_payment_method_1")[0].checked=false;
				}
				}
				if (sel_text=='Беларусь'){
				$("#nal_tr").removeClass("hyd");
				$("#curier_tr").addClass("hyd");
				$("#qiwi_tr").addClass("hyd");
				$("#qiwi_tr .td1 input")[0].checked=false;
				$("#bank_tr").addClass("hyd");
				$("#bank_tr .td1 input")[0].checked=false;
				$("#tim_tr").addClass("hyd");
				$("#pocita_tr").removeClass("hyd");
				change_pocita('Белпошта','30р.');
				$("#select_shipping_method_2")[0].checked=false;
				$("#select_shipping_method_3")[0].checked=false;
				}
				else if (sel_text=='Украина'){
				$("#nal_tr").removeClass("hyd");
				$("#qiwi_tr").addClass("hyd");
				$("#qiwi_tr .td1 input")[0].checked=false;
				$("#bank_tr").addClass("hyd");
				$("#bank_tr .td1 input")[0].checked=false;
				$("#curier_tr").removeClass("hyd");
				$("#tim_tr").removeClass("hyd");
				$("#pocita_tr").removeClass("hyd");
				change_pocita('Укрпочта','80р.');
				change_curier('Украина');
				$("#select_shipping_method_2")[0].checked=false;
				}
				else if (sel_text=='Россия'){
				$("#nal_tr").removeClass("hyd");change_shipping2();
				$("#curier_tr").removeClass("hyd");
				$("#qiwi_tr").removeClass("hyd");
				$("#bank_tr").removeClass("hyd");
				$("#tim_tr").addClass("hyd");
				$("#pocita_tr").removeClass("hyd");
				change_curier('Россия');
				change_pocita('Почта России','200р.');
				$("#select_shipping_method_3")[0].checked=false;
				}
				else if (sel_text=='Казахстан'){
				$("#nal_tr").removeClass("hyd");
				$("#curier_tr").addClass("hyd");
				$("#qiwi_tr").addClass("hyd");
				$("#qiwi_tr .td1 input")[0].checked=false;
				$("#bank_tr").addClass("hyd");
				$("#bank_tr .td1 input")[0].checked=false;
				$("#tim_tr").addClass("hyd");
				$("#pocita_tr").removeClass("hyd");
				change_pocita('Казпочта','200р.');
				$("#select_shipping_method_2")[0].checked=false;
				$("#select_shipping_method_3")[0].checked=false;
				}
				else if (sel_text=='Другая') {
				$("#nal_tr").addClass("hyd");
				$("#pocita_tr").addClass("hyd");
				$("#curier_tr").addClass("hyd");
				$("#qiwi_tr .td1 input")[0].checked=false;
				$("#qiwi_tr").addClass("hyd");
				$("#bank_tr").addClass("hyd");
				$("#bank_tr .td1 input")[0].checked=false;
				$("#tim_tr").addClass("hyd");
				$("#select_shipping_method_2")[0].checked=false;
				$("#select_shipping_method_0")[0].checked=false;
				$("#select_shipping_method_3")[0].checked=false;
				if (user_change==3)$("#select_payment_method_0")[0].checked=false;	
				$("#select_shipping_method_4")[0].checked=true;	
				//user_change=false;				
				}
				if (sel_text=='Другая' && navigat!=null)
				{
				$("#navigator_download").removeClass("hyd");
				$("#select_shipping_method_4")[0].checked=true;
				change_shipping2();
				}
				else if ( navigat!=null)
				{
				$("#navigator_download").addClass("hyd");
				$("#select_shipping_method_4")[0].checked=false;
				$("#select_shipping_method_0")[0].checked=true;
				change_shipping2();
				}
				else{				change_shipping2();}

				
				}
				function redraw_bottoms()
				{
				if ($.browser.msie ) {
				var t1=($("#center .block").height()+20)+"px";
				var t2=($("#center .blok").height()+30)+"px";
				$(".bl-b1").css({top:t1});
				$("#center .count-b:last").css({top:t2});
				}
				}
                function billingAddressCheckHandler()
                {
                  return;
                }
				function change_pocita(name_pocita,cost)
				{
				if (name_pocita=="Белпошта")
				$("#pocita_td")[0].innerHTML='<p>'+name_pocita+'</p><p class="grey">Доставка от 2 до 5 дней</p>';
				else if (name_pocita=="Укрпочта")
				$("#pocita_td")[0].innerHTML='<p>'+name_pocita+'</p><p class="grey">Доставка от 2 до 10 дней</p>';
				else
				$("#pocita_td")[0].innerHTML='<p>'+name_pocita+'</p><p class="grey">Доставка от 5 до 15 дней</p>';
				if($("#pocita_td2")[0].innerHTML!='0р.')$("#pocita_td2").text(cost);
				}
				function change_curier(country)
				{
				if (country=="Украина")
				{$("#curier_td")[0].innerHTML='<p>Курьер</p><p class="grey">Доставка в любой регион Украины от 1 до 3 дней</p>';
				if($("#curier_td2")[0].innerHTML=='500р.')$("#curier_td2").text("200р.");
				else if($("#curier_td2")[0].innerHTML=='300р.')$("#curier_td2").text("100р.");
				}
				else if(country=="Россия")
				{$("#curier_td")[0].innerHTML='<p>Курьер</p><p class="grey">Доставка в любой регион России от 2 до 5 дней</p>';
				if($("#curier_td2")[0].innerHTML=='200р.')$("#curier_td2").text("500р.");
				else if($("#curier_td2")[0].innerHTML=='100р.')$("#curier_td2").text("300р.");
				}
				}
				function change_shipping()
				{
				user_change=3;
				change_shipping2();
				}
				function change_shipping2()
				{
				pay_met=document.getElementById('select_payment_method_0');
				ship_met=document.getElementById('select_shipping_method_4');
				ship_met2=document.getElementById('select_shipping_method_2');
				ship_met3=document.getElementById('select_shipping_method_0');
				ship_met4=document.getElementById('select_shipping_method_3');
				if ( pay_met!=null && ship_met!=null ) 
				{
				if (ship_met.checked){
				pay_met.disabled=true;
				$("#nal_tr").addClass("hyd");
				$("#adress_div").addClass("hyd");
				$("#city_div").addClass("hyd");
				$("#name_need").addClass("hyd");
				shipp_cost='0р.';
				$("#dost_price").text(shipp_cost);
				$("#total_price").text((tovar_cost+parseFloat(shipp_cost))+"р.");
				
				for (i=1;i<=12;i=i+1)
					{
						hship=document.getElementById('pay_tr_'+i);
						if (hship!=null )$("#pay_tr_"+i).removeClass("hyd");
					}
					if(curent_country=='Россия'){$("#qiwi_tr").removeClass("hyd");$("#bank_tr").removeClass("hyd");}
					$("#email_need").removeClass("hyd");
				} else{
				pay_met.disabled=false;
				$("#nal_tr").removeClass("hyd");
				$("#adress_div").removeClass("hyd");
				$("#name_need").removeClass("hyd");	
				$("#bank_tr").addClass("hyd");	
				for (i=1;i<=12;i=i+1)
					{
						hship=document.getElementById('pay_tr_'+i);
						if (hship!=null )$("#pay_tr_"+i).addClass("hyd");
					}
					$("#email_need").addClass("hyd");
					if(curent_country=='Россия')$("#qiwi_tr").addClass("hyd");
				if (!pay_met.checked)
				{
				var dont_change=false;
				for (i=1;i<=7;i=i+1)
					{
						pay_met2=document.getElementById('select_payment_method_'+i);
						if (pay_met2!=null && pay_met2.checked)dont_change=true;
					}
						if(!dont_change)pay_met.checked=true;
				}		
				}
				if (pay_met.checked && ship_met.checked){pay_met.checked=false;}
				}
				if ( ship_met2!=null ) {
				if (ship_met2.checked){
				//$("#telefon_need").removeClass("hyd");
				var shipp_cost=$("#curier_tr .td3").text();
				if(shipp_cost=='0р.')shipp_cost='0р.'; 
				$("#dost_price").text(shipp_cost);
				$("#total_price").text((tovar_cost+parseFloat(shipp_cost))+"р.");
				} 
				/*else {
					if(!$("#qiwi_tr .td1 input")[0].checked) $("#telefon_need").addClass("hyd");
					}				*/
				}
				if ( ship_met3!=null ) {
				if (ship_met3.checked){var shipp_cost=$("#pocita_td2").text(); if(shipp_cost=='0р.')shipp_cost='0р.'; $("#dost_price").text(shipp_cost);$("#total_price").text((tovar_cost+parseFloat(shipp_cost))+"р.");} 
				}
				if ( ship_met4!=null ) {
				if (ship_met4.checked){var shipp_cost=$("#pocita_td2").text(); if(shipp_cost=='0р.')shipp_cost='0р.'; $("#dost_price").text(shipp_cost);$("#total_price").text((tovar_cost+parseFloat(shipp_cost))+"р.");
				$("#adress_div").addClass("hyd");
				$("#city_div").removeClass("hyd");
				}else{$("#city_div").addClass("hyd");} 
				}

				
				redraw_bottoms();
				}
				function change_billing()
				{
				user_change=3;
				if($("#qiwi_tr .td1 input")[0].checked)
				{
				//$("#telefon_need").removeClass("hyd");
				$("#telef_com").removeClass("hyd");				
				}
				else if (!$("#select_shipping_method_2")[0].checked)
				{
				//$("#telefon_need").addClass("hyd");
				$("#telef_com").addClass("hyd");
				}
				else $("#telef_com").addClass("hyd");
				
				}