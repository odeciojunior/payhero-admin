$(document).ready(function () {

    $("#company").on("change", function () {

        $("#invite-link").val('https://app.cloudfox.net/register/' + $("#company option:selected").attr('invite-parameter'));
    });

});

$("#copy-link").on("click", function () {

    var copyText = document.getElementById("invite-link");
    copyText.select();
    document.execCommand("copy");

    alertCustom('success', 'Link copiado!');
});
