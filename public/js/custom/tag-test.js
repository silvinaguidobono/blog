$(document).ready(function(){
    $("#tag_tag").on('blur',function(){
        var tag=$("#tag_tag").val();
        var miurl=URL+"tag-test";
        $.ajax({
            url: miurl,
            data: {tag: tag},
            type: 'POST',
            success: function(response){
                if(response=="used"){
                    $("#tag_tag").css("border","1px solid red");
                    $(".error-message").html('<p>La etiqueta ya existe</p>');
                    $("#tag_tag").focus();
                    setTimeout(function(){
                        $(".error-message").html('<p></p>');
                    },5000);
                    $('input[type="submit"]').attr('disabled','disabled');
                }else{
                    $("#tag_tag").css("border","1px solid green");
                    $('input[type="submit"]').removeAttr('disabled');
                }

            }
            /*,
            error: function () {
                $(".error-message").html("se ha producido un error");
            }
            */
        });
    });
});