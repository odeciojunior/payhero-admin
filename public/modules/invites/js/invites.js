$(document).ready( function(){

    $("#company").on("change", function(){

        $("#invite-link").val('https://app.cloudfox.net/register/' + $("#company option:selected").attr('invite-parameter'));
    });

    $("#copy-link").on("click", function(){

        var link = $("#invite-link").val();

        var temp = $("<input>");
        $("body").append(temp);
        temp.val(link).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success','Link copiado!');
    });

});
