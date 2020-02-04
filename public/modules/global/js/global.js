$(document).ready(function () {

    var current_url = window.location.pathname;

    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').css('scrollbar-width', 'none');
    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').removeClass('scrollable scrollable-inverse scrollable-vertical');

    if(current_url.includes('reports')){
        $("#reports-link").addClass('menu-active');
    } else if (current_url.includes('sales') || current_url.includes('recovery') || current_url.includes('trackings')){
        $("#sales-link").addClass('menu-active');
    }

    $(".mm-panels").css('scrollbar-width', 'none');

});

function alertCustom(type, message) {

    swal({
        position: 'bottom',
        type: type,
        toast: 'true',
        title: message,
        showConfirmButton: false,
        timer: 6000
    });
}

$(document).ajaxStart(function (event, jqXHR, ajaxOptions, data) {
    $("#loader").addClass("loader").fadeIn('slow');
    $("#loaderCard").addClass("loader").fadeIn('slow');
})

$(document).ajaxError(function (event, jqXHR, ajaxOptions, data) {
    $("#loader").removeClass('loader').fadeOut('slow');
    $("#loaderCard").removeClass('loader').fadeOut('slow');
})

$(document).ajaxSuccess(function (event, jqXHR, ajaxOptions, data) {
    $(".loaderCard").removeClass('loaderCard').fadeOut('slow');
});

$(".table").addClass('table-striped');

function loading(elementId, loaderClass) {

    if (loaderClass == '') {
        $(elementId).html('');
        $(elementId).append('<div class="loading"></div>');
    } else if (loaderClass == '#loaderCard') {
        $(elementId).append('<a class="loaderCard"></a>');
    }
}

function loadingOnScreen() {
    $('#loadingOnScreen').html('');
    $('#loadingOnScreen').append("<div class='loading2'><div class='loader'></div></div>")
}

function loadingOnScreenRemove() {
    $('#loadingOnScreen').html('');
    $('#btn-modal').show();
}

function loadOnNotification(whereToLoad) {
    $(whereToLoad).html('');
    $(whereToLoad).append("<div class='loading' style='width:346px; height:150px'>" +
        "<span class='loaderNotification' >" +
        "</span>" +
        "</div>");
}

function loadOnModal(whereToLoad) {

    $(whereToLoad).children().hide('fast');
    $('#modal-title').html('Carregando ...')
    $(whereToLoad).append("<div id='loaderModal' class='loadingModal'>" +
        "<div class='loaderModal'>" +
        "</div>" +
        "</div>");
    $('#loadingOnScreen').append("<div class='blockScreen'></div>");
}

function loadOnTable(whereToLoad, tableReference) {
    $(whereToLoad).html('');
    $(tableReference).removeClass('table-striped');
    $(whereToLoad).append("<tr id='loaderLine'>" +
        "<td colspan='12' align='center' class='loadingTable' style='height:100px'>" +
        "<a id='loader' class='loaderTable'></a>" +
        "</td>" +
        "</tr>");
}

function loadOnAny(target, remove = false, options = {}) {
    //cleanup
    target = $(target);
    target.parent().find('.loader-any-container').remove();

    if (!remove) {

        //create elements
        let container = $('<div class="loader-any-container"></div>');
        let loader = $('<span class="loader-any"></span>');

        //apply styles or use default
        options.styles = options.styles ? options.styles : {};
        options.styles.container = options.styles.container ? options.styles.container : {};
        options.styles.container.minWidth = options.styles.container.minWidth ? options.styles.container.minWidth : $(target).css('width');
        options.styles.container.minHeight = options.styles.container.minHeight ? options.styles.container.minHeight : $(window.top).height() * 0.7; //70% of visible window area
        container.css(options.styles.container);
        if (options.styles.loader) {
            loader.css(options.styles.loader);
        }

        //add loader to container
        container.append(loader);

        //add loader to screen
        target.hide();
        if (options.insertBefore) {
            container.insertBefore(target.parent().find(options.insertBefore));
        } else {
            target.parent().append(container);
        }
    } else {
        // show target again with fix to Bootstrap tabs
        if (!target.hasClass('tab-pane') ||
            (target.hasClass('tab-pane') &&
                target.hasClass('active'))) {
            $(target).show();
        }
    }
}

function modalClear(modalBody) {
    $(modalBody).html('');
}

function messageSwalSuccess(swalType, swalTitle, swalHtml, swalCloseButton, swalConfirmButton, swalFooter) {
    swal({
        type: swalType,
        title: swalTitle,
        html: swalHtml,
        showCloseButton: swalCloseButton,
        showConfirmButton: swalConfirmButton,
        footer: swalFooter
    })
}

$(document).ajaxComplete(function (jqXHR, textStatus) {

    switch (textStatus.status) {
        case 200:

            break;
        case 401:
            window.location.href = "/";
            break;
        case 404:
            break;
        case 500:
            break;
        case 413:
            alertCustom('error', 'O tamanho da imagem não pode exceder 2mb.')
            break;
        case 422:
            break;
        case 419:
            window.location.href = "/";
            break;
    }
});

$('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical.is-enabled').attr('overflow', 'hidden')

function pagination(response, model, callback) {

    let paginationContainer = "#pagination-" + model;

    $(paginationContainer).html("");

    let currentPage = response.meta.current_page;
    let lastPage = response.meta.last_page;

    if (lastPage === 1) {
        return false;
    }

    let first_page = `<button class='btn nav-btn first_page'>1</button>`;

    $(paginationContainer).append(first_page);

    if (currentPage === 1) {
        $(paginationContainer + ' .first_page').attr('disabled', true).addClass('nav-btn').addClass('active');
    }

    $(document).on("click", paginationContainer + ' .first_page', function () {
        callback('?page=1');
    });

    for (let x = 3; x > 0; x--) {

        if (currentPage - x <= 1) {
            continue;
        }

        $(paginationContainer).append(`<button class='btn nav-btn page_${(currentPage - x)}'>${(currentPage - x)}</button>`);

        $(document).on("click", paginationContainer + " .page_" + (currentPage - x), function () {
            callback('?page=' + $(this).html());
        });
    }

    if (currentPage !== 1 && currentPage !== lastPage) {
        var current_page = `<button class='btn nav-btn active current_page'>${currentPage}</button>`;

        $(paginationContainer).append(current_page);

        $(paginationContainer + " .current_page").attr('disabled', true).addClass('nav-btn').addClass('active');
    }
    for (let x = 1; x < 4; x++) {

        if (currentPage + x >= lastPage) {
            continue;
        }

        $(paginationContainer).append(`<button class='btn nav-btn page_${(currentPage + x)}'>${(currentPage + x)}</button>`);

        $(document).on("click", paginationContainer + " .page_" + (currentPage + x), function () {
            callback('?page=' + $(this).html());
        });
    }

    if (lastPage !== 1) {
        var last_page = `<button class='btn nav-btn last_page'>${lastPage}</button>`;

        $(paginationContainer).append(last_page);

        if (currentPage === lastPage) {
            $(paginationContainer + ' .last_page').attr('disabled', true).addClass('nav-btn').addClass('active');
        }

        $(document).on("click", paginationContainer + ' .last_page', function () {
            callback('?page=' + lastPage);
        });
    }
    $('table').addClass('table-striped')
}

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).html()).select();
    document.execCommand("copy");
    $temp.remove();
    alertCustom('success', 'Link copiado com sucesso')
}

function errorAjaxResponse(response) {
    if (response.responseJSON) {
        let errors = response.responseJSON.errors ? response.responseJSON.errors : {};
        errors = Object.values(errors).join('\n');
        if (response.status === 422 || response.status === 404 || response.status === 403) {
            alertCustom('error', errors);
        } else if (response.status === 401) { // Não esta autenticado
            window.location.href = window.location.origin + '/';
            alertCustom('error', errors);
        } else {
            alertCustom('error', response.responseJSON.message);
        }
    } else {
        alertCustom('error', 'Erro ao executar esta ação!');
    }
}

function extractIdFromPathName() {
    let urlParams = window.location.pathname.split('/');
    if (urlParams.length >= 2 && urlParams[urlParams.length - 1] == 'edit') {
        return urlParams[urlParams.length - 2];
    } else if (urlParams.length > 0) {
        return urlParams[urlParams.length - 1];
    } else {
        return '';
    }
}

function isEmptyValue(value) {
    return value.length !== 0;
}

function isEmpty(obj) {
    return Object.keys(obj ? obj : {}).length === 0;
}

function defaultSelectItemsFunction(item) {
    return {value: item.id_code, text: item.name};
}

function downloadFile(response, request) {
    let type = request.getResponseHeader("Content-Type");
    // Get file name
    let contentDisposition = request.getResponseHeader("Content-Disposition");
    let fileName = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
    fileName = fileName ? fileName[0].replace("filename=", "") : '';

    var a = document.createElement("a");
    a.style.display = "none";
    document.body.appendChild(a);
    a.href = window.URL.createObjectURL(new Blob([response], {type: type}));
    a.setAttribute("download", fileName);
    a.click();
    window.URL.revokeObjectURL(a.href);
    document.body.removeChild(a);
}

$(document).on('click', 'a[data-copy_text],a[data-copy_id]', function (event, i) {
    event.preventDefault();
    let inputId = $(this).data('copy_id') || '#copyText';
    let copyText = (inputId === '#copyText' ? $(this).data('copy_text') || '' : $(inputId).val() || '');
    if (copyText === '') {
        return false;
    }
    if (document.getElementById("copyText") === null) {
        let input = document.createElement("input");
        input.type = "text";
        input.id = "copyText";
        input.value = copyText;
        document.getElementsByTagName("body")[0].appendChild(input);
    } else {
        document.getElementById("copyText").value = copyText;
    }
    document.getElementById("copyText").select();
    document.execCommand("copy");
    setTimeout(function () {
        $('#copyText').remove();
    }, 1000);
    alert("Link " + $(inputId).val() + " copiado com Sucesso!");
});

/* Document Pending Alert */

sessionStorage.removeItem('documentsPending');

function ajaxVerifyDocumentPending(){
    $.ajax({
        method: 'GET',
        url: '/api/profile/verifydocuments',
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: response => {
            errorAjaxResponse(response);
        },
        success: response => {
            sessionStorage.setItem('documentsPending', JSON.stringify(response));
            if (response.pending) {
                $('.document-pending').show();
                $('.btn-finalize').attr('href', response.link);
            }
        },
    });
}

function verifyDocumentPending(){
    if(window.location.href.includes('/dashboard')){
        sessionStorage.removeItem('documentsPending');
        $('.document-pending').hide();
    }

    if (!window.location.href.includes('/companies') && !window.location.href.includes('/profile')) {
        let documentsPending = sessionStorage.getItem('documentsPending');
        if (documentsPending === null) {
            ajaxVerifyDocumentPending();
        } else {
            documentsPending = JSON.parse(documentsPending);
            if (documentsPending.pending) {
                $('.document-pending').show();
                $('.btn-finalize').attr('href', documentsPending.link);
            }
        }
    }
}

/* End - Document Pending Alert */

$.fn.shake = function () {
    let distance = 5;
    let speed = 50;
    let repeat = 3;
    let animation1 = {left: "+=" + distance};
    let animation2 = {left: "-=" + (distance * 2)};

    for (let i = 0 ; i < repeat; i++ ) {
        $(this).animate(animation1, speed)
            .animate(animation2, speed)
            .animate(animation1, speed);
    }
};

