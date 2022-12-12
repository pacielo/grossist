import 'select2';

$( () => {
    $('.js-select2').select2(); 

    $("form").validate({
        rules: {
            "registration[rppsNumber]": {
              required: true,
              minlength: 11,
              maxlength: 11,
              number: true
            }, 
            "registration[email][second]": {
                required: true,
                equalTo: "#registration_email_first"
            },
            "registration[password][first]": {
                required: true,
                minlength: 8
            },
            "registration[password][second]": {
                required: true,
                minlength: 8,
                equalTo: "#registration_password_first"
            },            
         },
         submitHandler: function(form) {
                $(".spinner").show();
                form.submit();
        },
         error: function() {
                $(".spinner").hide();
        }
    }); 

    $('input').on('input', function() {
        $(this).parent().find('.invalid-feedback').remove();
        $(this).removeClass('is-invalid');
        $(".alert").remove();
    });

    if ($('#app_request_method').val() == "GET" ) {
        $(".gerant").hide();
    }

   


    $('body').on('blur', '#registration_password_first', function(){
        if ($('#registration_password_first').val()) {
            var patt = new RegExp("^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9@#$%^&+=]*$");
            if (!patt.test($('#registration_password_first').val())) {
                $(':input[type="submit"]').prop('disabled', true);
                 toastr.error("Le mot de passe doit contenir au moins un chiffre, un caractère minuscule et un caractère majuscule, et minimum 8 caractères", "Warning"); 
                 $('#registration_password_first').addClass('is-invalid');
            } else {
                $(':input[type="submit"]').prop('disabled', false);
                $('#registration_password_first').removeClass('is-invalid');
            }
        }
    });
    
    $('body').on('blur', '#registration_rppsNumber', function(){
        event.preventDefault();
        if ($('#registration_rppsNumber').val()) {
            $('#registration_rppsNumber').removeClass('is-invalid');
            $.ajax({
                method: 'post',
                url: $('#search_rpps').val(),
                contentType: 'application/json; charset=utf-8', 
                data: JSON.stringify({'rpps': $('#registration_rppsNumber').val() }),
                cache: false,
                dataType: "json",
                success: function(response){
                    if (response.success == 0) { // rpps existe pas
                        $('#registration_rppsNumber').addClass('is-invalid');
                        toastr.error(response.message, "Error");     
                    }    
                }, 
                error: function (jxh, textmsg, errorThrown) {
                    toastr.error(jxh.status + " " + jxh.statusText, "Error");                  
                }            
            });
        }
    });
    
    function checkrRecaptcha(){
        if ($('#g-recaptcha-response').val()) {
            $(':input[type="submit"]').prop('disabled', false);
        } else {
            $(':input[type="submit"]').prop('disabled', true);
        }
        setTimeout(checkrRecaptcha,1000);
    }
    checkrRecaptcha();  
});