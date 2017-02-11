$(document).ready(function() {
    
    var $formLogin = $('#login-form');
    var $formLost = $('#lost-form');
    var $formRegister = $('#register-form');
    var $divForms = $('#div-forms');
    var $modalAnimateTime = 300;
    var $msgAnimateTime = 150;
    var $msgShowTime = 2000;

    $('#login-form').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'signin.php',
            type: 'POST',
            data: $('#login-form').serialize(),

            success: function(data){
                if(data == 1){
                    msgChange($('#div-login-msg'), $('#icon-login-msg'), $('#text-login-msg'), "success", "glyphicon-ok", "Redirigiendo");

                    setTimeout(function () {
                       window.location.replace("index.php");
                    }, 1000);
                    
                }
                else{
                    msgChange($('#div-login-msg'), $('#icon-login-msg'), $('#text-login-msg'), "error", "glyphicon-remove", "Credenciales no válidas");
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log('error(s):'+textStatus, errorThrown);
            }

        });
    });

    $('#lost-form').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'signin.php',
            type: 'POST',
            data: $('#login-form').serialize(),

            success: function(data){
                if(data == 1){
                    msgChange($('#div-lost-msg'), $('#icon-lost-msg'), $('#text-lost-msg'), "success", "glyphicon-ok", "Solicitud Enviada");
                }
                else{
                    msgChange($('#div-lost-msg'), $('#icon-lost-msg'), $('#text-lost-msg'), "error", "glyphicon-remove", "Hubo un Error. Intentar Nuevamente");
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log('error(s):'+textStatus, errorThrown);
            }

        });
    });

    $('#register-form').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: 'signin.php',
            type: 'POST',
            data: $('#login-form').serialize(),

            success: function(data){
                if(data == 1){
                    msgChange($('#div-register-msg'), $('#icon-register-msg'), $('#text-register-msg'), "success", "glyphicon-ok", "Solicitud Enviada");
                }
                else{
                    msgChange($('#div-register-msg'), $('#icon-register-msg'), $('#text-register-msg'), "error", "glyphicon-remove", "Hubo un Error. Intentar Nuevamente");
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log('error(s):'+textStatus, errorThrown);
            }

        });
    });
    
    $('#login_register_btn').click( function () { modalAnimate($formLogin, $formRegister) });
    $('#register_login_btn').click( function () { modalAnimate($formRegister, $formLogin); });
    $('#login_lost_btn').click( function () { modalAnimate($formLogin, $formLost); });
    $('#lost_login_btn').click( function () { modalAnimate($formLost, $formLogin); });
    $('#lost_register_btn').click( function () { modalAnimate($formLost, $formRegister); });
    $('#register_lost_btn').click( function () { modalAnimate($formRegister, $formLost); });
    
    function modalAnimate ($oldForm, $newForm) {
        var $oldH = $oldForm.height();
        var $newH = $newForm.height();
        $divForms.css("height",$oldH);
        $oldForm.fadeToggle($modalAnimateTime, function(){
            $divForms.animate({height: $newH}, $modalAnimateTime, function(){
                $newForm.fadeToggle($modalAnimateTime);
            });
        });
    }
    
    function msgFade ($msgId, $msgText) {
        $msgId.fadeOut($msgAnimateTime, function() {
            $(this).text($msgText).fadeIn($msgAnimateTime);
        });
    }
    
    function msgChange($divTag, $iconTag, $textTag, $divClass, $iconClass, $msgText) {
        var $msgOld = $divTag.text();
        msgFade($textTag, $msgText);
        $divTag.addClass($divClass);
        $iconTag.removeClass("glyphicon-chevron-right");
        $iconTag.addClass($iconClass + " " + $divClass);
        setTimeout(function() {
            msgFade($textTag, $msgOld);
            $divTag.removeClass($divClass);
            $iconTag.addClass("glyphicon-chevron-right");
            $iconTag.removeClass($iconClass + " " + $divClass);
  		}, $msgShowTime);
    }
});