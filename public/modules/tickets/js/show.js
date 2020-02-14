$(document).ready(function () {

    $('#btn-answer').on('click', function (event) {
        event.preventDefault();
        $(".div-message").slideDown();
    });

    $('#btn-cancel').on('click', function (event) {
        event.preventDefault();
        $(".div-message").slideUp();
    });

    $('.turn-back').on('click', function () {
        let locationUrl = window.location.protocol + "//" + window.location.hostname + '/attendance';
        window.location.replace(locationUrl);
    });

    $('#btn-solve').on('click', function (event) {
        event.preventDefault();
    });

    $('#btn-send').on('click', function (event) {
        event.preventDefault();
    });

});
