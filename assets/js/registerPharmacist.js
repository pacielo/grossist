import 'select2';

$( () => {

    $('input').on('input', function() {
        $(this).parent().find('.invalid-feedback').remove();
        $(this).removeClass('is-invalid');
        $(".alert").remove();
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