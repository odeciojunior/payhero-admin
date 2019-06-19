function alertPersonalizado(tipo, mensagem){

    swal({
        position: 'bottom',
        type: tipo,
        toast: 'true',
        title: mensagem,
        showConfirmButton: false,
        timer: 6000
    });
}

function alertCustom(type, message){

    swal({
        position: 'bottom',
        type: type,
        toast: 'true',
        title: message,
        showConfirmButton: false,
        timer: 6000
    });
}

$(document).ajaxStart(function(event, jqXHR, ajaxOptions, data) {
    $(".loading").css("visibility", "visible");
})

$(document).ajaxError(function(event, jqXHR, ajaxOptions, data) {
    $(".loading").css("visibility", "hidden");
})

$(document).ajaxSuccess(function(event, jqXHR, ajaxOptions, data) {
    $(".loading").css("visibility", "hidden");
})

