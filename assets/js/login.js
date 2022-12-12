'use strict';

$( () => {
    if ($(".server-error").length > 0 ) {
        $('input').on('input', function() {
            $(".server-error").fadeOut();
          });        
    }

    $("form").validate( {
        submitHandler: (form) => {
            $(".spinner").show();
            $('button').attr("disabled", true);
            form.submit();
           },
        error: () => {
          $(".spinner").hide();
          $('button').attr("disabled", false);
       }
    });
    
});