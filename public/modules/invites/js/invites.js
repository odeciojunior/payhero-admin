$(document).ready(function () {

    $("#company").on("change", function () {

        $("#invite-link").val('https://app.cloudfox.net/register/' + $("#company option:selected").attr('invite-parameter'));
    });

});

function copyTextToClipboard(text) {
    var textArea = document.createElement("textarea");

    textArea.style.position = 'fixed';
    textArea.style.top = 0;
    textArea.style.left = 0;
    textArea.style.width = '2em';
    textArea.style.height = '2em';
    textArea.style.padding = 0;
    textArea.style.border = 'none';
    textArea.style.outline = 'none';
    textArea.style.boxShadow = 'none';
    textArea.style.background = 'transparent';
    textArea.value = text;

    document.body.appendChild(textArea);
    textArea.select();

    document.execCommand('copy');
    document.body.removeChild(textArea);
}

$("#copy-link").on("click", function () {

    var linkText = $("#invite-link").val();
    copyTextToClipboard(linkText);

    alertCustom('success', 'Link copiado!');
});
