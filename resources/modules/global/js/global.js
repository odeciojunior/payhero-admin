$(document).ready(function () {
    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').css('scrollbar-width', 'none');
    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').removeClass('scrollable scrollable-inverse scrollable-vertical');
    $(".mm-panels").css('scrollbar-width', 'none');

    $('.init-operation-container').on('click', '.redirect-to-accounts', function (e) {
        e.preventDefault();

        let url_data = $(this).attr('data-url-value');

        redirectToAccounts(url_data);
    });

    $('.redirect-to-accounts').on('click', function (e) {
        e.preventDefault();

        let url_data = $(this).attr('data-url-value');

        redirectToAccounts(url_data);
    });

    localStorage.setItem('new-register-step', '1');

    changeNewRegisterLayoutOnWindowResize();

    window.onresize = changeNewRegisterLayoutOnWindowResize;

    $('.new-register-open-modal-btn').on('click', function () {
        $('.new-register-navbar-open-modal-container').fadeOut('slow');

        if (!localStorage.getItem('new-register-step')) {
            localStorage.setItem('new-register-step', '1');
        }

        let step = localStorage.getItem('new-register-step');

        changeProgressBar(step);

        setStepButton(step);

        $('#new-register-step-' + step + '-container').addClass('d-flex flex-column');

        $('.new-register-overlay').fadeIn();
    });

    $('.close-modal').on('click', function () {
        $('.new-register-overlay').fadeOut();

        changeNewRegisterLayoutOnWindowResize();
    });

    $('#new-register-steps-actions').on('click', '.close-modal', function () {
        if (localStorage.getItem('new-register-step') == '4') {
            $('#new-register-steps-container').fadeOut();
            $('#new-register-firt-page').fadeIn();
        } else {
            $('.new-register-overlay').fadeOut();
        }

        changeNewRegisterLayoutOnWindowResize();
    });

    $('#open-steps-btn').on('click', function () {
        $('#new-register-firt-page').hide();

        $('.modal-top-btn').hide();

        setStepButton(localStorage.getItem('new-register-step'));

        $('#new-register-steps-container').show();
    });

    $('#new-register-step-container input[type=text]').on('input', function() {
        setStepButton(localStorage.getItem('new-register-step'));
    });

    $('#new-register-step-container input[type=checkbox]').change(function() {
        setStepButton(localStorage.getItem('new-register-step'));
    });

    $('.step-1-option').on('click', function () {
        if ($(this).hasClass('option-selected')) {
            $(this).removeClass('option-selected');
            $(this).attr('data-step-1-selected', '0');
        } else {
            $(this).addClass('option-selected');
            $(this).attr('data-step-1-selected', '1');
        }

        setStepButton(localStorage.getItem('new-register-step'));
    });

    $("input[name='step-2-other-ecommerce-check']").change(function () {
        let input = $("input[name='step-2-other-ecommerce']");

        if ($(this).is(":checked")) {
            input.removeAttr('disabled');
        } else {
            input.val('');
            input.attr('disabled', true);
        }
    });

    $("input[name='step-2-know-cloudfox-check']").change(function () {
        let input = $("input[name='step-2-know-cloudfox']");

        if ($(this).is(":checked")) {
            input.removeAttr('disabled');
        } else {
            input.val('');
            input.attr('disabled', true);
        }
    });

    $("input[name='step-3-sales-site-check']").change(function () {
        let input = $("input[name='step-3-sales-site']");

        if ($(this).is(":checked")) {
            input.val('');
            input.attr('disabled', true);
        } else {
            input.removeAttr('disabled');
        }
    });

    $("input[name='step-3-gateway-check']").change(function () {
        let input = $("input[name='step-3-gateway']");

        if ($(this).is(":checked")) {
            input.val('');
            input.attr('disabled', true);
        } else {
            input.removeAttr('disabled');
        }
    });

    $('#new-register-previous-step').on('click', function () {
        let step = parseInt(localStorage.getItem('new-register-step'));

        if (step === 1) {
            $('#new-register-firt-page').show();

            $('.modal-top-btn').show();

            $('#new-register-steps-container').hide();

            return;
        }

        $('#new-register-step-' + step + '-container').removeClass('d-flex flex-column');

        step--;

        localStorage.setItem('new-register-step', step.toString());

        changeProgressBar(step);

        setStepButton(step);

        $('#new-register-step-' + step + '-container').addClass('d-flex flex-column');
    });

    $('#new-register-next-step').on('click', function () {
        let lastStep = parseInt(localStorage.getItem('new-register-step'));

        let step = lastStep + 1;

        if (step === 4) {
            saveNewRegisterData();

            return;
        }

        localStorage.setItem('new-register-step', step.toString());

        $('#new-register-step-' + lastStep + '-container').removeClass('d-flex flex-column');

        changeProgressBar(step);

        setStepButton(step);

        $('#new-register-step-' + step + '-container').addClass('d-flex flex-column');
    });

    const monthRevenueInput = document.getElementById('new-register-range');

    monthRevenueInput.style.backgroundSize = (monthRevenueInput.value - monthRevenueInput.min) * 100 / (monthRevenueInput.max - monthRevenueInput.min) + '% 100%';

    function handleInputRangeChange(e) {
        let target = e.target;

        const minVal = target.min;
        const maxVal = target.max;
        let val = target.value;

        target.style.backgroundSize = (val - minVal) * 100 / (maxVal - minVal) + '% 100%';

        val = val * 1000;

        $('#new-register-month-revenue span:first-child').text((val === 5000 ? 'Até ' : val === 1000000 ? 'Acima de ' : '') + 'R$');
        $('#new-register-month-revenue span:last-child').text(val.toLocaleString('pt-BR', { maximumFractionDigits: 2, minimumFractionDigits: 2 }));
    }

    monthRevenueInput.addEventListener('input', handleInputRangeChange);
});

function redirectToAccounts(url_data)
{
    $.ajax({
        method: 'GET',
        url: '/send-authenticated',
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: response => {
            errorAjaxResponse(response);
        },
        success: response => {
            let url = response.url;

            if (url_data) {
                url = url + url_data;
            }

            window.location.href = url;
        },
    });
}

function stringToMoney(string, currency = 'BRL') {
    let value = parseInt(string, 10);

    return value.toLocaleString('pt-br', { style: 'currency', currency: currency });
}

function scrollCustom(div, padding = false, type = '') {
    var scroll = 0;
    var scrollDiv = 0;
    var valuePadding = 0;
    var heightAdjust = 0;

    $(div).css('padding-right', '12px');
    $(div).append('<div class="scrollbox"></div>');
    $(div).append('<div class="scrollbox-bar"></div>');

    $(div).on('wheel', function (event) {
        if (event.originalEvent.deltaY !== 0) {
            if (padding == true) {
                valuePadding = 40;
            }

            if (type == 'modal-body') {
                heightAdjust = 20;
            }

            var heightDivScroll = $(div).height() + valuePadding;
            var heightDivScrollTotal = $(div).children(":first").height() + valuePadding;

            var heightCalculateScroll = ((heightDivScroll - 60) / 20) * 2;
            var heightCalculateTotal = ((heightDivScrollTotal - heightDivScroll) / 20) * 2;

            if (event.originalEvent.deltaY < 0) {
                // wheeled up
                if (scroll > heightCalculateScroll) {
                    scroll -= heightCalculateScroll;
                    scrollDiv -= heightCalculateTotal;
                } else if (scroll == heightCalculateScroll || scroll > 0) {
                    scroll = 0;
                    scrollDiv = 0;
                }
            } else {
                // wheeled down
                var sumScroll = scroll + heightCalculateScroll;
                if (sumScroll <= (heightDivScroll - 60)) {
                    scroll += heightCalculateScroll;
                    scrollDiv += heightCalculateTotal;
                } else {
                    scroll = heightDivScroll - 60;
                    scrollDiv = (heightDivScrollTotal - heightDivScroll);
                }
            }

            $(div).find('.scrollbox-bar').css('top', scroll + 'px');
            $(div).children(":first").css('margin-top', '-' + scrollDiv + 'px');
        }
    });
}

function scrollCustomX(div, addScroll = true, changePosition = false) {
    if ($(div).find('.scrollbox').length == 0 && $(div).find('.scrollbox-bar').length == 0) {
        $(div).css('padding-bottom', '12px');
        $(div).append('<div class="scrollbox"></div>');
        $(div).append('<div class="scrollbox-bar"></div>');
    }

    if ($(div).find('.scrollbox').length > 0 && $(div).find('.scrollbox-bar').length > 0) {
        var scroll = changePosition ? $(div).find('.scrollbox-bar').css('left').replace('px', '') : 0;
        var scrollDiv = changePosition ? $(div).children(":first").css('margin-left').replace('px', '') : 0;
    }

    $(div).on('wheel', function (event) {
        if (event.originalEvent.deltaY !== 0) {
            var widthDivScroll = $(div).width();
            var widthDivScrollTotal = $(div).children(":first").width() - 12;

            var widthtCalculateScroll = ((widthDivScroll - 60) / 20) * 2;
            var widthCalculateTotal = ((widthDivScrollTotal - widthDivScroll) / 20) * 2;

            if (event.originalEvent.deltaY < 0) {
                // wheeled left
                if (scroll > widthtCalculateScroll) {
                    scroll -= widthtCalculateScroll;
                    scrollDiv -= widthCalculateTotal;
                } else if (scroll == widthtCalculateScroll || scroll > 0) {
                    scroll = 0;
                    scrollDiv = 0;
                }
            } else {
                // wheeled right
                var sumScroll = scroll + widthtCalculateScroll;
                if (sumScroll <= (widthDivScroll - 60)) {
                    scroll += widthtCalculateScroll;
                    scrollDiv += widthCalculateTotal;
                } else {
                    scroll = widthDivScroll - 60;
                    scrollDiv = (widthDivScrollTotal - widthDivScroll);
                }
            }

            $(div).find('.scrollbox-bar').css('left', scroll + 'px');
            $(div).children(":first").css('margin-left', '-' + scrollDiv + 'px');
        }
    });
}

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
    $('#loadingOnScreen').append(
        `<div class="sirius-loading">
            <img style="height: 125px; width: 125px" src="/build/global/img/logos/2021/svg/icon-sirius.svg"
                 class="img-responsive"/>
        </div>`
    ).fadeIn()

    $('body').css('overflow-y', 'hidden')
}

function loadingOnChart(target) {
    $(target).fadeIn().append(
        `<div style="z-index: 100; border-radius: 16px; position: absolute;" class="sirius-loading">
            <img style="height: 125px; width: 125px;" src="/build/global/img/logos/2021/svg/icon-sirius.svg"
                 class="img-responsive"/>
        </div>`
    )
}

function loadingOnAccountsHealth(target) {
    $(target).fadeIn().append(
        `<div style="z-index: 100; border-radius: 16px; position: absolute;" class="sirius-loading d-flex justify-content-center align-items-center align-self-center">
            <img style="height: 125px; width: 125px; top: auto;" src="/build/global/img/logos/2021/svg/icon-sirius.svg"
                 class="img-responsive"/>
        </div>`
    )
}

function loadingOnChartRemove(target) {
    $(target).fadeOut(function () {
        $(target).html('');
    });
}

function loadingOnAccountsHealthRemove(target) {
    //$(target).fadeOut(function () {
    //    $(target).html('');
    //});
    $(target).remove();
}

function loadOnAnyEllipsis(target, remove = false, options = {}) {
    //cleanup
    target = $(target);
    $('.loader-any-container-ellipsis').fadeOut();
    target.parent().find('.loader-any-container-ellipsis').remove();

    if (!remove) {

        //create elements
        let container = $('<div class="loader-any-container-ellipsis"></div>');
        let loader = $('<span class="ellipsis-anim"><span>.</span><span>.</span><span>.</span></span>');

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
            $(target).fadeIn();
        }
    }
}

function heightAnimate(element, height) {
    var curHeight = element.height(); // Get Default Height
    var autoHeight = element.css('height', 'auto').height(); // Get Auto Height

    element.height(curHeight); // Reset to Default Height
    element.stop().animate({ height: autoHeight }, time); // Animate to Auto Height
}

function loadingOnScreenRemove() {
    window.setTimeout(function () {
        $('#loadingOnScreen').fadeOut(function () {
            $(this).html('')
            $('body').css('overflow-y', 'unset')
        });
    }, 2000)


    $('.page-header').fadeIn();
    $('#btn-modal').fadeIn();
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
    $('#modal-title').html('Carregando...')
    $(whereToLoad).append("<div id='loaderModal' class='loadinModal'><div class='loaderModal'></div></div>");
    $('#loadingOnScreen').append("<div class='blockScreen'></div>");
}

function loadModalPlaceholderLoading(modal, whereToLoad, htmlLoad) {
    if (whereToLoad) {
        $(modal).find(whereToLoad).children().fadeOut('fast');
        $(modal).find(whereToLoad).append(htmlLoad);
    } else {
        $(modal).find('.modal-title').html('Carregando...');
        $(modal).find('.modal-body').children().fadeOut('fast');
        $(modal).find('.modal-footer').fadeOut('fast');
        $(modal).find('.modal-body').append(htmlLoad);
    }
}

function loadOnModalNewLayout(modal, whereToLoad) {
    $(modal).find('.modal-body').removeClass('show');

    if (whereToLoad) {
        $(modal).find(whereToLoad).children().fadeOut('fast');
        $(modal).find(whereToLoad).append("<div id='loaderModal' class='loadingModal' style='height: 80px; position: relative;'><div class='loaderModal' style='position: absolute;'></div></div>");
    } else {
        $(modal).find('.modal-title').html('Carregando...');
        $(modal).find('.modal-body').children().fadeOut('fast');
        $(modal).find('.modal-footer').fadeOut('fast');
        $(modal).find('.modal-body').append("<div id='loaderModal' class='loadingModal' style='height: 80px; position: relative;'><div class='loaderModal' style='position: absolute;'></div></div>");
    }

    $(modal).modal('show');
}

function loadOnModalRemove(modal) {
    $(modal).find('.modal-body').addClass('show');
    $(modal).find('.ph-item').fadeOut(3000, function () {
        this.remove();
    });

    $(modal).find('.modal-body').children().fadeIn(3000);

    $(modal).find('.modal-footer').fadeIn(3000);
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
    target.parent()
        .find('.loader-any-container')
        .remove();

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

        //add message load
        if (options.message) {
            container.append(`<p class='mb-30'>${options.message}</p>`);
            container.addClass('d-flex').addClass('flex-column');
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
            $(target).fadeIn();
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
            alertCustom('error', 'O tamanho do arquivo é maior que o limite máximo.')
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

    $(paginationContainer + ' .first_page').on("click", function () {
        callback('?page=1');
    });

    for (let x = 3; x > 0; x--) {

        if (currentPage - x <= 1) {
            continue;
        }

        $(paginationContainer).append(`<button class='btn nav-btn page_${(currentPage - x)}'>${(currentPage - x)}</button>`);

        $(paginationContainer + " .page_" + (currentPage - x)).on("click", function () {
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

        $(paginationContainer + " .page_" + (currentPage + x)).on("click", function () {
            callback('?page=' + $(this).html());
        });
    }

    if (lastPage !== 1) {
        var last_page = `<button class='btn nav-btn last_page'>${lastPage}</button>`;

        $(paginationContainer).append(last_page);

        if (currentPage === lastPage) {
            $(paginationContainer + ' .last_page').attr('disabled', true).addClass('nav-btn').addClass('active');
        }

        $(paginationContainer + ' .last_page').on("click", function () {
            callback('?page=' + lastPage);
        });
    }
    $('table').addClass('table-striped')
}

function copyToClipboard(elem) {
    // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;

    console.log(elem.tagName)
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
    } catch (e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    console.log(target)
    return succeed;
}

function errorAjaxResponse(response) {
    if (response.responseJSON) {
        let errors = response.responseJSON.errors ? response.responseJSON.errors : {};
        errors = Object.values(errors).join('\n');
        if (response.status === 422 || response.status === 404 || (response.status === 403 && !isEmpty(errors))) {
            alertCustom('error', errors);
        } else if (response.status === 401) { // Não esta autenticado
            window.location.href = window.location.origin + '/';
            alertCustom('error', errors);
        } else {
            alertCustom('error', response.responseJSON.message);
        }
    }
    // else {
    //     alertCustom('error', 'Erro ao executar esta ação!');
    // }
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

function companyIsApproved(company) {
    return company.company_is_approved ? true : false;
}

function defaultSelectItemsFunction(item) {
    return { value: item.id_code, text: item.name };
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
    a.href = window.URL.createObjectURL(new Blob([response], { type: type }));
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


/* TOP ALERT */

$('.top-alert-close').on('click', function () {
    $('#document-pending').fadeOut();
});

/* END - TOP ALERT */


/* Document Pending Alert */

sessionStorage.removeItem('documentsPending');

function ajaxVerifyAccount() {
    $.ajax({
        method: 'GET',
        url: '/api/core/verify-account/' + $('meta[name="user-id"]').attr('content'),
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: response => {
            errorAjaxResponse(response);
        },
        success: response => {
            if (response.data.account !== 'approved') {
                let verifyAccount = localStorage.getItem('verifyAccount');
                if (verifyAccount == null) {
                    $('.new-register-page-open-modal-container').hide();
                    $('.new-register-navbar-open-modal-container').hide();

                    $('.new-register-overlay').fadeIn();
                }

                localStorage.setItem('verifyAccount', JSON.stringify(response.data));

                if (!response.data.user.informations) {
                    $('.extra-informations-user').show();
                } else {
                    $('.extra-informations-user').hide();
                }

                var card_company_status = '';
                var card_company_icon = '';
                var card_company_title = '';
                var card_company_description = '';
                var card_company_button = '';
                var card_company_link = response.data.company.link;

                if (response.data.company.status == null) {
                    card_company_status = 'redirect-to-accounts';
                    card_company_icon = '/build/global/img/icon-company.svg';
                    card_company_title = 'Cadastre sua empresa';
                    card_company_description = 'Na Cloudfox você pode ter uma ou mais empresas.';
                    card_company_button = '';
                } else {
                    if (response.data.company.status == 'pending' || response.data.company.status == 'pending') {
                        card_company_status = 'status-info';
                        card_company_icon = '/build/global/img/icon-analysing.svg';
                        card_company_title = 'Você cadastrou sua empresa, mas não recebemos nenhum documento';
                        card_company_description = 'Você só poderá começar a sua operação depois de enviar e aprovar os documentos da sua empresa.';
                        card_company_button = '<button class="btn btn-default redirect-to-accounts" data-url-value="'+ card_company_link +'">Enviar documentos</button>';
                    } else if (response.data.company.status == 'analyzing' || response.data.company.status == 'analyzing') {
                        card_company_status = 'status-warning';
                        card_company_icon = '/build/global/img/icon-analysing.svg';
                        card_company_title = 'Estamos analisando seus documentos da sua empresa';
                        card_company_description = 'Esse processo de revisão leva um tempinho. Mas em breve retornaremos.';
                        card_company_button = '<button class="btn btn-default redirect-to-accounts" data-url-value="'+ card_company_link +'">Enviar documentos</button>';
                    } else if (response.data.company.status == 'refused' || response.data.company.status == 'refused') {
                        card_company_status = 'status-error';
                        card_company_icon = '/build/global/img/icon-error.svg';
                        card_company_title = 'Tivemos problemas em verificar sua empresa';
                        card_company_description = 'Há um problema com seus documentos.';
                        card_company_button = '<button class="btn btn-default redirect-to-accounts" data-url-value="'+ card_company_link +'">Enviar documentos</button>';
                    } else if (response.data.company.status == 'approved' || response.data.company.status == 'approved') {
                        card_company_status = 'status-check redirect-to-accounts';
                        card_company_icon = '/build/global/img/icon-check.svg';
                        card_company_title = 'A documentação da sua empresa foi recebida e aprovada.';
                        card_company_description = 'Se você já aprovou seus documentos pessoais, agora é só vender!';
                        card_company_button = '';
                    }
                }

                $('.company-status').html(`
                    <div class="card ${card_company_status}" data-url-value="${card_company_link}">
                        <div class="d-flex">
                            <div>
                                <div class="icon d-flex align-items-center">
                                    <img src="${card_company_icon}" alt="">
                                </div>
                            </div>
                            <div class="content">
                                <h1 class="title">${card_company_title}</h1>
                                <p class="description">${card_company_description}</p>
                                ${card_company_button}
                            </div>
                        </div>
                    </div>
                `);

                var card_user_status = '';
                var card_user_icon = '';
                var card_user_title = '';
                var card_user_description = '';
                var card_user_button = '';
                var card_user_link = response.data.user.link;

                if (response.data.user.status == 'pending' || response.data.user.status == 'pending') {
                    card_user_status = 'redirect-to-accounts';
                    card_user_icon = '/build/global/img/icon-docs.svg';
                    card_user_title = 'Envie sua documentação pessoal';
                    card_user_description = 'Precisamos do seu documento oficial com foto e um comprovante de residência.';
                    card_user_button = '';
                } else if (response.data.user.status == 'analyzing' || response.data.user.status == 'analyzing') {
                    card_user_status = 'status-warning redirect-to-accounts';
                    card_user_icon = '/build/global/img/icon-analysing.svg';
                    card_user_title = 'Estamos analisando seus documentos';
                    card_user_description = 'Esse processo de revisão leva um tempinho. Mas em breve retornaremos.';
                    card_user_button = '<button class="btn btn-default redirect-to-accounts" data-url-value="'+ card_user_link +'">Enviar documentos</button>';
                } else if (response.data.user.status == 'refused' || response.data.user.status == 'refused') {
                    card_user_status = 'status-error';
                    card_user_icon = '/build/global/img/icon-error.svg';
                    card_user_title = 'Tivemos um problema com o seu documento';
                    card_user_description = 'Um ou mais documentos foram reprovados após a análise.';
                    card_user_button = '<button class="btn btn-default redirect-to-accounts" data-url-value="'+ card_user_link +'">Enviar documentos</button>';
                } else if (response.data.user.status == 'approved' || response.data.user.status == 'approved') {
                    card_user_status = 'status-check redirect-to-accounts';
                    card_user_icon = '/build/global/img/icon-check.svg';
                    card_user_title = 'Sua documentação foi recebida e aprovada';
                    card_user_description = 'Se você já aprovou uma empresa com a gente, agora é só vender!';
                    card_user_button = '';
                }

                $('.user-status').html(`
                    <div class="card ${card_user_status}" data-url-value="${card_user_link}">
                        <div class="d-flex">
                            <div>
                                <div class="icon d-flex align-items-center">
                                    <img src="${card_user_icon}" alt="">
                                </div>
                            </div>
                            <div class="content">
                                <h1 class="title">${card_user_title}</h1>
                                <p class="description">${card_user_description}</p>
                                ${card_user_button}
                            </div>
                        </div>
                    </div>
                `);
            } else {
                let verifyAccount = JSON.parse(localStorage.getItem('verifyAccount'));
                if (verifyAccount.account !== 'approved') {
                    localStorage.setItem('verifyAccount', JSON.stringify(response.data));
                }
            }
        },
    });
}

function verifyDocumentPending() {
    ajaxVerifyAccount();
}

function changeProgressBar(step) {
    switch (parseInt(step)) {
        case 1:
            $('#new-register-step-progress-bar-1').css('width', '50%');
            $('#new-register-step-progress-bar-2').css('width', '0');
            $(".new-register-step[data-step*='1']").addClass('step-active');
            $(".new-register-step[data-step*='2']").removeClass('step-active');
            break;
        case 2:
            $('#new-register-step-progress-bar-1').css('width', '100%');
            $('#new-register-step-progress-bar-2').css('width', '50%');
            $(".new-register-step[data-step*='1']").addClass('step-active');
            $(".new-register-step[data-step*='2']").addClass('step-active');
            $(".new-register-step[data-step*='3']").removeClass('step-active');
            break;
        case 3:
        case 4:
            $('#new-register-step-progress-bar-1').css('width', '100%');
            $('#new-register-step-progress-bar-2').css('width', '100%');
            $(".new-register-step[data-step*='1']").addClass('step-active');
            $(".new-register-step[data-step*='2']").addClass('step-active');
            $(".new-register-step[data-step*='3']").addClass('step-active');
            break;
    }
}

function changeNewRegisterLayoutOnWindowResize() {
    let userNameText = $('.new-register-overlay-title strong').text();

    if (window.innerWidth <= 370) {
        $('.new-register-overlay-title strong').css('display', 'block');
    } else if (window.innerWidth > 370 && window.innerWidth <= 470) {
        $('.new-register-overlay-title strong').css('display', 'unset');

        if (userNameText.length > 10) {
            $('.new-register-overlay-title strong').text(userNameText.substring(0, 9) + '...');
        }
    } else if (window.innerWidth > 470 && window.innerWidth <= 665) {
        $('.new-register-overlay-title strong').css('display', 'unset');

        if (userNameText.length > 14) {
            $('.new-register-overlay-title strong').text(userNameText.substring(0, 13) + '...');
        }
    } else if (window.innerWidth > 665) {
        $('.new-register-overlay-title strong').css('display', 'unset');

        if (userNameText.length > 20) {
            $('.new-register-overlay-title strong').text(userNameText.substring(0, 19) + '...');
        }
    }

    if (window.innerWidth >= 847) {
        $('.new-register-page-open-modal-container').fadeOut();
        $('.new-register-navbar-open-modal-container').fadeIn();
    } else {
        $('.new-register-navbar-open-modal-container').fadeOut();
        $('.new-register-page-open-modal-container').fadeIn();
    }
}

function validateStep(step) {
    let isValid = false;

    switch (parseInt(step)) {
        case 1:
            isValid = $("div[data-step-1-selected*='1']").length > 0;
            break;
        case 2:
            isValid = true;
            break;
        case 3:
            isValid = ($("input[name='step-3-sales-site-check']").is(':checked') || $("input[name='step-3-sales-site']").val()) &&
                ($("input[name='step-3-gateway-check']").is(':checked') || $("input[name='step-3-gateway']").val());
            break;
        default:
            isValid = true;
            break;
    }

    return isValid;
}

function setStepButton(step) {
    let btn = $('#new-register-next-step');

    if (!validateStep(step)) {
        btn.attr('disabled', true);
    } else {
        btn.removeAttr('disabled');
    }

    btn.attr('data-step-btn', step);
}

function saveNewRegisterData() {
    const newRegisterData = {
        document: JSON.parse(localStorage.getItem('verifyAccount')).user.document,
        email: JSON.parse(localStorage.getItem('verifyAccount')).user.email,
        niche: JSON.stringify({
            others: $("div[data-step-1-value=others]").attr('data-step-1-selected'),
            classes: $("div[data-step-1-value=classes]").attr('data-step-1-selected'),
            subscriptions: $("div[data-step-1-value=subscriptions]").attr('data-step-1-selected'),
            digitalProduct: $("div[data-step-1-value=digital-product]").attr('data-step-1-selected'),
            physicalProduct: $("div[data-step-1-value=physical-product]").attr('data-step-1-selected'),
            dropshippingImport: $("div[data-step-1-value=dropshipping-import]").attr('data-step-1-selected'),
        }),
        ecommerce: JSON.stringify({
            wix: +$('#wix').is(':checked'),
            shopify: +$('#shopify').is(':checked'),
            pageLand: 0,
            wooCommerce: +$('#woo-commerce').is(':checked'),
            otherEcommerce: +$('#other-ecommerce').is(':checked'),
            integratedStore: +$('#integrated-store').is(':checked'),
            otherEcommerceName: $('#other-ecommerce-name').val(),
        }),
        cloudfox_referer: JSON.stringify({
            ad: +$('#cloudfox-referer-ad').is(':checked'),
            email: 0,
            other: +$('#cloudfox-referer-other').is(':checked'),
            youtube: +$('#cloudfox-referer-youtube').is(':checked'),
            facebook: +$('#cloudfox-referer-facebook').is(':checked'),
            linkedin: +$('#cloudfox-referer-linkedin').is(':checked'),
            instagram: 0,
            recomendation: 0,
        }),
        website_url: $('#step-3-sales-site').val(),
        gateway: $('#step-3-gateway').val(),
        monthly_income: $('#new-register-range').val() * 1000,
    };

    loadingOnScreen();

    $.ajax({
        method: "POST",
        url: "/api/user-informations",
        data: newRegisterData,
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json'
        },
        error: function error(response) {
            loadingOnScreenRemove();

            alertCustom('error', response.responseJSON.message);
        },
        success: function success(response) {
            localStorage.setItem('new-register-step', '4');

            $('#new-register-step-3-container').removeClass('d-flex flex-column');

            $('#new-register-step-4-container').addClass('d-flex flex-column');

            $('#new-register-steps-actions').removeClass('justify-content-between');
            $('#new-register-steps-actions').addClass('justify-content-center');
            $('.extra-informations-user').hide();

            $('#new-register-steps-actions').html('<button type="button" class="btn new-register-btn close-modal">Fechar</button>');

            loadingOnScreenRemove();
        }
    });
}

/* End - Document Pending Alert */

/* Cookies */
function setCookie(name, exdays, object) {

    var expires;
    var date;
    var value;
    date = new Date(); // criando cookie com a data atual
    date.setTime(date.getTime() + (exdays * 3600 * 1000));
    expires = date.toUTCString();
    value = JSON.stringify(object);

    document.cookie = name + "=" + value + "; expires=" + expires + ";path=/";
}

function getCookie(name) {
    name += "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function deleteCookie(name) {
    setCookie(name, -1);
}

/* Cookies */

$.fn.shake = function () {
    let distance = 5;
    let speed = 50;
    let repeat = 3;
    let animation1 = { left: "+=" + distance };
    let animation2 = { left: "-=" + (distance * 2) };

    for (let i = 0; i < repeat; i++) {
        $(this).animate(animation1, speed)
            .animate(animation2, speed)
            .animate(animation1, speed);
    }
};

// sirius select

function initSiriusSelect(target) {
    let $target = $(target);
    let classes = Array.from(target[0].classList).filter(e => e !== 'sirius-select').join(' ');
    $target.removeClass(classes);
    $target.wrap(`<div class="sirius-select-container ${classes}"></div>`);
    $target.hide();
    $target.after(`<div class="sirius-select-options"></div>`);
    $target.after(`<div class="sirius-select-text"></div>`);

    renderSiriusSelect($target)
}

function renderSiriusSelect(target) {
    let $target = $(target);
    let $wrapper = $target.parent();
    let $text = $wrapper.find('.sirius-select-text');
    let $options = $wrapper.find('.sirius-select-options');
    $options.html('');
    $target.children('option').each(function () {
        let option = $(this);
        let attributes = Object.values(this.attributes)
            .reduce((text, attr) => {
                if (!['id', 'value', 'data-value', 'selected', 'disabled'].includes(attr.name)) {
                    if(attr.value) return text + ` ${attr.name}="${attr.value}"`;
                    return text + ` ${attr.name}`;
                }
                return text;
            }, '');
        let disabled = option.is(':disabled') ? `class="disabled"` : '';
        $options.append(`<div data-value="${option.val()}" ${attributes} ${disabled}>${option.text()}</div>`);
    });
    $text.text($target.children('option:selected').eq(0).text());
}

$.fn.siriusSelect = function () {
    initSiriusSelect(this);
};
// END sirius select

/**
 * Menu implementation
 */
$(document).ready(function () {
    var bodyEl = $('body')
    var menuBarToggle = $('[data-toggle="menubar"]');
    var toggle = $('[data-toggle="menubar"].nav-link');
    menuBarToggle.off().on('click', function () {
        bodyEl.toggleClass('site-menubar-unfold site-menubar-fold site-menubar-open site-menubar-hide');
        menuBarToggle.toggleClass('hided')
        if (toggle.hasClass('hided')) {
            $('#logoIconSirius').fadeOut().addClass('d-none');
            $('#logoSirius').fadeIn().removeClass('d-none');
        } else {
            $('#logoIconSirius').fadeIn().removeClass('d-none');
            $('#logoSirius').fadeOut().addClass('d-none');
        }
    })

    var siteMenuItems = $('.site-menu-item.has-sub')
    var siteMenuBar = $('.site-menubar')
    var menuTimeout
    siteMenuBar.on('mouseenter', function () {
        bodyEl.addClass('site-menubar-hover');
        $('#logoIconSirius').fadeOut().addClass('d-none');
        $('#logoSirius').fadeIn().removeClass('d-none');
    }).on('mouseleave', function () {
        menuTimeout = setTimeout(function () {
            bodyEl.removeClass('site-menubar-hover');
            if (!toggle.hasClass('hided')) {
                $('#logoIconSirius').fadeIn().removeClass('d-none');
                $('#logoSirius').fadeOut().addClass('d-none');
            }
        }, 500)
    }).find('*').on('mouseenter', function (event) {
        clearTimeout(menuTimeout)
        bodyEl.addClass('site-menubar-hover')
    })

    siteMenuItems.on('click', function () {
        siteMenuItems.not($(this)).removeClass('active')
        $(this).toggleClass('active')
    })

    var links = $('.site-menubar .site-menu-item a');
    $.each(links, function (key, va) {
        var current = document.URL

        if (va.href == document.URL || (current.match(va.href) || []).length >= 1) {
            $(this).addClass('menu-active')
            $(this).parents('.site-menu-item.has-sub').find('> a').addClass('menu-active')
        }
    });

    // Disable page scroll when a modal is open
    $(document).on('shown.bs.modal', function (e) {
        document.querySelector('body').style.overflowY = 'hidden';
    });
    $(document).on('hidden.bs.modal', function (e) {
        document.querySelector('body').style.overflowY = 'unset';
    });

    // sirius select
    $('.sirius-select').each(function () {
        $(this).siriusSelect();
    });

    $(document).on('DOMSubtreeModified propertychange change', '.sirius-select-container select', function () {
        renderSiriusSelect(this);
    });

    $(document).on('click', '.sirius-select-text', function () {
        $('.sirius-select-text').removeClass('active');
        $('.sirius-select-options').fadeOut();

        let $target = $(this);
        $target.toggleClass('active');
        let $wrapper = $target.parent();
        let $options = $wrapper.find('.sirius-select-options');
        $target.hasClass('active') ? $options.fadeIn() : $options.fadeOut();
    });

    $(document).on('click', '.sirius-select-options div', function () {
        let $target = $(this);
        if(!$target.hasClass('disabled')) {
            let $wrapper = $target.parents('.sirius-select-container');
            $wrapper.find('select')
                .val($target.data('value'))
                .trigger('change');
            $wrapper.find('.sirius-select-text')
                .removeClass('active')
                .text($target.text());
            $target.parent().fadeOut();
        }
    });

    $(document).on('click', function (e) {
        let target = $(e.target);
        if (!target.parents('.sirius-select-container').length) {
            $('.sirius-select-container .sirius-select-text').removeClass('active');
            $('.sirius-select-container .sirius-select-options').fadeOut();
        }
    });
    // END sirius select

    // vertical scroll
    $('.vertical-scroll').on({
        'mousewheel wheel': function (e) {
            e.preventDefault();
            this.scrollLeft += e.originalEvent.deltaY;
        },
        'mousedown': function (e) {
            $(this).addClass('scrolling').data('x', e.clientX).data('left', this.scrollLeft);
        },
        'mouseup mouseleave': function (e) {
            $(this).removeClass('scrolling').data('x', 0).data('left', 0);
        }
    });
    $(document).on('mousemove', '.vertical-scroll.scrolling', function (e) {
        const dx = e.clientX - $(this).data('x');
        this.scrollLeft = $(this).data('left') - dx;
    });
    // END vertical scroll
})

function verifyAccountFrozen() {
    if ($('#accountStatus').val() == 'account frozen') {
        return true;
    }
    return false;
}

function onlyNumbers(string) {
    if (string == undefined) {
        return 0;
    }
    return string.replace(/\D/g, '');
}

function removeMoneyCurrency(string) {
    if (string.charAt(0) == '-') {
        return '-' + string.substring(4);
    }
    return string.substring(3);
}
