function ValidMail() {
    var re = /^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i;
    var myMail = document.getElementById('email').value;
    var valid = re.test(myMail);
    if (valid) output = '';
    else output = 'Адрес электронной почты введен неправильно!';
    document.getElementById('message_email').innerHTML = output;
    return valid;
}

function ValidPhone() {
    var re = /^\d[\d\(\)\ -]{4,14}\d$/;
    var myPhone = document.getElementById('phone').value;
    var valid = re.test(myPhone);
    if (valid) output = '';
    else output = 'Номер телефона введен неправильно!';
    document.getElementById('message_phone').innerHTML = output;
    return valid;
}

$(document).ready(function () {
    $('#zayavka #phone').mask("8(999) 999-99-99");

    $('#zayavka').on('submit', function (e) { // При отправке формы
        e.preventDefault(); // Предотвращаем ее отправку
        if (ValidMail() & ValidPhone()) {
            var details = $('#zayavka').serialize(); // Сериализуем ее данные
            $.post('/ajax/zayavka_form.php', details, function (data) { // Отправляем их с помощью $.post()
                $('.container .sec-form-answer').html('Спасибо, Ваша заявка отправлена!'); // Здесь выводим результат
                $('#zayavka').trigger('reset');

            });
        }
    });
});

/////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function () {

    $(".phonemasked").mask("+7 (999) 999-9999", {autoclear: false});
    /*FORM SEND*/


    if ($(".soglok").length > 0) {
        $(".soglok").click(function () {
            var blockedbutton = $(this).attr("data-blockingbutton");
            if ($(this).is(':checked')) {
                $("#" + blockedbutton).attr("disabled", false);

            }
            else {
                $("#" + blockedbutton).attr("disabled", true);
            }
        });

    }


    $('#modalsubm').click(function () {


        var vname = $("input[name=modalusername]").val();
        var vphone = $("input[name=modaluserphone]").val();
        var vemail = $("input[name=modaluseremail]").val();
        var vfile = $("input[name=modaluserfile]").val();
        var vadress = $("input[name=modaluseradress]").val();
        var vcomment = $("input[name=modalusertext]").val();
        var vformname = $("input[name=modalformname]").val();

        if (vname && vphone) {

            var formData = new FormData();


            //присоединяем наш файл
            $.each($("input[name=modaluserfile]")[0].files, function (i, file) {
                formData.append('file_v', file);
            });

            //присоединяем остальные поля
            formData.append('name', vname);
            formData.append('phone', vphone);
            formData.append('email', vemail);
            formData.append('adress', vadress);
            formData.append('comment', vcomment);
            formData.append('formname', vformname);

            formData.append('urla', document.location.href);
            //отправляем через ajax
            $.ajax({
                url: "/ajax/post.php",
                type: "POST",
                /*dataType : "json", */
                cache: false,
                contentType: false,
                processData: false,
                data: formData, //указываем что отправляем
                success: function (data) {
                    if (data) {
                        //если ок, выводим сообщение
                        $('#modalanswer').hide();
                        $("input[name=modalusername]").val('');
                        $("input[name=modalusertext]").val('');
                        $("input[name=modaluseradress]").val('');
                        $("input[name=modaluserphone]").val('');
                        $("input[name=modaluseremail]").val('');
                        $("input[name=modaluserfile]").val('');
                        $('.addfile').html("Прикрепить файл");


                        $('#modalanswererror').html('<p style="color: white; text-align: center; margin: 20px 10px;">Ваша заявка отправлена!<br>В ближайшее время мы свяжемся с Вами!</p>');
                        /*$('#modalanswer').fadeOut(1000);
                        setTimeout(function() { $('#modalanswererror').html(' '); }, 5000);*/
                    } else {
                        $('#modalanswererror').html('<p style="color: red; text-align: center; margin: 20px 0;">Сообщение не может быть отправлено!<br>Попробуйте позже!</p>');
                        setTimeout(function () {
                            $('#modalanswererror').html(' ');
                        }, 5000);
                    }
                }
            });

        } else {
            $("#modalanswererror").html("Заполните имя и телефон");
            setTimeout(function () {
                $('#modalanswererror').html(' ');
            }, 5000);
        }
        return false;
    });

    /*END FORMSEND*/

    /* modal */
    $('.modalshow').click(function () {
        $('#modalanswererror').html(' ');
        $('#modalanswer').show();

        if ($(this).attr("data-formtype")) {

            if ($(this).attr("data-formtype") == 1) {
                $("input[name=modalusertext]").show();
                $("input[name=modaluseradress]").hide();
                $("#modalfile").hide();
            }
            else if ($(this).attr("data-formtype") == 2) {
                $("input[name=modalusertext]").hide();
                $("input[name=modaluseradress]").hide();
                $("#modalfile").show();
            }
            else if ($(this).attr("data-formtype") == 3) {
                $("input[name=modalusertext]").hide();
                $("input[name=modaluseradress]").show();
                $("#modalfile").hide();
            }

        }

        if ($(this).attr("data-formname")) $("input[name=modalformname]").val($(this).attr("data-formname"));


        $('.modal-bg, .modal-wrap').addClass('active');
        return false;
    });
    $('.modal-close, .modal-bg').click(function () {
        $("input[name=modalusername]").val('');
        $("input[name=modalusertext]").val('');
        $("input[name=modaluseradress]").val('');
        $("input[name=modaluserphone]").val('');
        $("input[name=modaluseremail]").val('');
        $("input[name=modaluseremail]").val('');
        $("input[name=modaluserfile]").val('');
        $('.addfile').html("Прикрепить файл");

        $('.modal-bg, .modal-wrap').removeClass('active');
        return false;
    });
    $('.addfile').click(function () {
        $(this).parent().find('input[type="file"]').click();
        return false;
    });


    $('.modal-inner input[type="file"]').change(function () {
        var filename = $(this).val();
        if (filename) {
            filename = filename.replace(/.*\\(.*)$/gi, '$1');
            $('.addfile').html(filename);
        } else $('.addfile').html("Прикрепить файл");
    });

    /* / modal */
});


<!-- Modal -->
<div class="modal-bg"></div>
< div class="modal-wrap" >
    < div
class
= "modal-inner" >
    < div
class
= "modal-close" > < /div>
<div id="modalanswererror"></div>
< div
id = "modalanswer" > < form >
    < input
type = "hidden"
name = "modalformname" >
    < input
type = "text"
name = "modalusername"
placeholder = "Ваше имя" >
    < input
class
= "phonemasked"
type = "text"
name = "modaluserphone"
placeholder = "Телефон" >
    < input
type = "email"
name = "modaluseremail"
placeholder = "E-mail" >
    < input
type = "text"
name = "modalusertext"
placeholder = "Комментарий" >
    < input
type = "text"
name = "modaluseradress"
placeholder = "Адрес сайта" >
    < div
id = "modalfile" >
    < a
href = "#"
class
= "addfile" > Прикрепить
файл < /a>
<input type="file" name="modaluserfile">
</div>
< div
class
= "form-group-sogl" >
    < input
data - blockingbutton = "modalsubm"
type = "checkbox"
value = "1"
class
= "soglok"
id = "soglasie"
name = "soglasie"
checked = "" >
    < label
class
= "soglasielabel"
for= "soglasie" > Я согласен
на < a
href = "/soglasie/" > обработку
персональных
данных < /a></
label >
< /div>
<button id="modalsubm" type="button" class="btn btn-modal">Отправить заявку</button>
< /form></
div >
< /div>
</div>
<!-- / Modal -->
