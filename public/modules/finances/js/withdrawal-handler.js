$(window).on("load", function() {

    if(window.gatewayCode == 'w7YL9jZD6gp4qmv') {
        $('#withdrawalsTable >thead tr').append('<td scope="col" class="table-title"></td>');
    }

    let url = '';
    if(window.gatewayCode == 'w7YL9jZD6gp4qmv' || window.gatewayCode == 'oXlqv13043xbj4y'){
        url = '/modules/finances/js/withdrawal-custom.js';
    }
    else {
        url = '/modules/finances/js/withdrawal-default.js';
    }

    var script = document.createElement("script");
    script.src = url;
    script.type = "text/javascript";

    document.head.appendChild(script);

});
