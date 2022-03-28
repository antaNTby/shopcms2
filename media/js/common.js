	$(document).ready(function(){
      $('.variable-width').slick({
        dots: true,
  		infinite: true,
  		speed: 300,
  		slidesToShow: 1,
  		centerMode: true,
  		variableWidth: true,
  		arrows: false,
  		centerMode: false
      });
	  
	  $(".top").remove();
	  $(".bot").remove();
    });
	
	$(function () {
        var email = $("#email"),
        allFields = $([]).add(email),
        tips = $(".validateTips");

        function updateTips(t) {
            tips
                    .text(t)
                    .addClass("ui-state-highlight");
            setTimeout(function () {
                tips.removeClass("ui-state-highlight", 1500);
            }, 500);
        }

        function checkLength(o, n, min, max) {
            if (o.length > max || o.length < min) {
                return false;
            } else {
                return true;
            }
        }

        function checkEmail(str){
            if(!checkLength(str, "email", 3, 50)) return false;
            var dogPos = str.indexOf('@');//не первый символ
            if(dogPos<1) return false;

            var dotPos = str.indexOf('.',dogPos);
            if((dotPos<dogPos+2) || (dotPos==(str.length-1))) return false;//не сразу после @ и не последний
            return true;
        }

        function checkRegexp(o, regexp, n) {
            if (!( regexp.test(o.val()) )) {
                o.addClass("ui-state-error");
                updateTips(n);
                return false;
            } else {
                return true;
            }
        }

        $('#next_w1').click(function () {
            var bValid = checkEmail(email.val());
            allFields.removeClass("ui-state-error");

            if (bValid) {
                        var inputMail = email.val();
                        $('#WMI_DESCRIPTION').val($('#description').val() + ' - ' + inputMail);
                        $('#WMI_CUSTOMER_EMAIL').val(inputMail);
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "/add_order.php",
                            data: {
                                email: inputMail,
                                itemID: product.id,
                                name: product.name,
                                price: product.price
                            },
                            cache: false,
                            success: function (responce) {
                                $('.user_id').val(responce.user_id);
                                $('.order_id').val(responce.order_id);
                                var order_id = responce.order_id;
                                $('#nInvId').val(responce.order_id);
                                $('#w1').submit();
                            }
                        });
             }else{
                email.addClass("ui-state-error");
                updateTips("Введите, пожалуйста, E-Mail ");
             }
        });
    });