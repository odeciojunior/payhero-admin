$(document).ready(function() {
    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').css('scrollbar-width', 'none');
    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').removeClass(
        'scrollable scrollable-inverse scrollable-vertical',
    );
    $('.mm-panels').css('scrollbar-width', 'none');

    showBonusBalance();

    $('.bonus-balance-button').on('click', function() {
        $('body').addClass('bonus-modal-opened');
        $('#bonus-balance-modal').fadeToggle('slow', 'linear');
    });

    $('.init-operation-container').on('click', '.redirect-to-accounts', function(e) {
        e.preventDefault();

        let url_data = $(this).attr('data-url-value');

        redirectToAccounts(url_data);
    });

    $('.redirect-to-accounts').on('click', function(e) {
        e.preventDefault();

        let url_data = $(this).attr('data-url-value');

        redirectToAccounts(url_data);
    });

    var newRegisterStepAux;

    window.onresize = changeNewRegisterLayoutOnWindowResize;

    $('.alert-demo-account').on('click', function() {
        $('.alert-demo-account-overlay').fadeIn();
    });

    $('.alert-demo-account-close-modal, .alert-demo-account-close-modal-x').on('click', function() {
        $('.alert-demo-account-overlay').fadeOut(400);
    });

    $('.new-register-open-modal-btn')
        .parent()
        .on('click', function() {
            if ($('.alert-demo-account-overlay').css('display') == 'block') {
                $('.alert-demo-account-overlay').hide();
            }

            $('.new-register-navbar-open-modal-container').fadeOut('slow');

            setStepContainer();

            $('.new-register-overlay').fadeIn();
        });

    $('.close-modal').on('click', function() {
        $('.new-register-overlay').fadeOut(400, function() {
            changeNewRegisterLayoutOnWindowResize();
        });
    });

    $('#new-register-steps-actions').on('click', '.close-modal', function() {
        if (getNewRegisterStep() == '4') {
            $('#new-register-steps-container').fadeOut(400, function() {
                changeNewRegisterLayoutOnWindowResize();
            });

            $('#new-register-first-page').fadeIn();
        } else {
            $('.new-register-overlay').fadeOut(400, function() {
                changeNewRegisterLayoutOnWindowResize();
            });
        }
    });

    $('.init-operation-container').on('click', '.extra-informations-user', function() {
        $('#new-register-first-page').hide();

        $('.modal-top-btn').hide();

        setStepButton(getNewRegisterStep());

        $('#new-register-steps-container').show();
    });

    $('#new-register-step-container input[type=text]').on('input', function() {
        setStepButton(getNewRegisterStep());

        if ($(this).val()) {
            setNewRegisterSavedItem($(this).attr('id'), $(this).val());
        } else {
            removeNewRegisterSavedItem($(this).attr('id'));
        }
    });

    $('#new-register-step-container input[type=checkbox]').change(function() {
        setStepButton(getNewRegisterStep());
    });

    $('.step-1-option').on('click', function() {
        if ($(this).hasClass('option-selected')) {
            $(this).removeClass('option-selected');
            $(this).attr('data-step-1-selected', '0');

            removeNewRegisterSavedItem($(this).attr('id'));
        } else {
            $(this).addClass('option-selected');
            $(this).attr('data-step-1-selected', '1');

            setNewRegisterSavedItem($(this).attr('id'), 'true');
        }

        setStepButton(getNewRegisterStep());
    });

    $('.step-2-checkbox-option input[type=\'checkbox\']').on('click', function() {
        if ($(this).is(':checked')) {
            setNewRegisterSavedItem($(this).attr('id'), 'true');
        } else {
            removeNewRegisterSavedItem($(this).attr('id'));
        }
    });

    $('input[name=\'step-2-other-ecommerce-check\']').on('change', function() {
        step2CheckboxOnChange($(this), $('input[name=\'step-2-other-ecommerce\']'));
    });

    $('input[name=\'step-2-know-cloudfox-check\']').on('change', function() {
        step2CheckboxOnChange($(this), $('input[name=\'step-2-know-cloudfox\']'));
    });

    $('input[name=\'step-2-other-ecommerce\']').on('input', function() {
        if (!$(this).val()) {
            removeNewRegisterSavedItem($(this).attr('id'));
        } else {
            setNewRegisterSavedItem($(this).attr('id'), $(this).val());
        }
    });

    $('input[name=\'step-2-know-cloudfox\']').on('input', function() {
        if (!$(this).val()) {
            removeNewRegisterSavedItem($(this).attr('id'));
        } else {
            setNewRegisterSavedItem($(this).attr('id'), $(this).val());
        }
    });

    $('input[name=\'step-3-sales-site-check\']').change(function() {
        let input = $('input[name=\'step-3-sales-site\']');

        if ($(this).is(':checked')) {
            input.val('');
            input.attr('disabled', true);
            input.removeClass('input-invalid input-valid');

            setNewRegisterSavedItem($(this).attr('id'), 'true');
            removeNewRegisterSavedItem(input.attr('id'));
        } else {
            input.removeAttr('disabled');
            input.addClass('input-invalid');

            removeNewRegisterSavedItem($(this).attr('id'));
        }
    });

    $('input[name=\'step-3-gateway-check\']').change(function() {
        let input = $('input[name=\'step-3-gateway\']');

        if ($(this).is(':checked')) {
            input.val('');
            input.attr('disabled', true);
            input.removeClass('input-invalid input-valid');

            setNewRegisterSavedItem($(this).attr('id'), 'true');
            removeNewRegisterSavedItem(input.attr('id'));
        } else {
            input.removeAttr('disabled');
            input.addClass('input-invalid');

            removeNewRegisterSavedItem($(this).attr('id'));
        }
    });

    $('.new-register-input-validation').on('blur input', function() {
        if (!$(this).val()) {
            $(this).removeClass('input-valid');
            $(this).addClass('input-invalid');
        } else {
            $(this).removeClass('input-invalid');
            $(this).addClass('input-valid');
        }
    });

    $('#new-register-previous-step').on('click', function() {
        let step = parseInt(getNewRegisterStep());

        if (step === 1) {
            $('#new-register-first-page').show();

            $('.modal-top-btn').show();

            $('#new-register-steps-container').hide();

            return;
        }

        $('#new-register-step-' + step + '-container').removeClass('d-flex flex-column');

        step--;

        setNewRegisterStep(step.toString());

        changeProgressBar(step, 'prev');

        setStepButton(step);

        $('#new-register-step-' + step + '-container').addClass('d-flex flex-column');
    });

    $('#new-register-next-step').on('click', function() {
        let lastStep = parseInt(getNewRegisterStep());

        let step = lastStep + 1;

        if (step === 4) {
            saveNewRegisterData();

            return;
        }

        setNewRegisterStep(step.toString());

        $('#new-register-step-' + lastStep + '-container').removeClass('d-flex flex-column');

        changeProgressBar(step, 'next');

        setStepButton(step);

        $('#new-register-step-' + step + '-container').addClass('d-flex flex-column');
    });

    const monthRevenueInput = document.getElementById('new-register-range');

    if (monthRevenueInput) {
        monthRevenueInput.style.backgroundSize =
            ((monthRevenueInput.value - monthRevenueInput.min) * 100) /
            (monthRevenueInput.max - monthRevenueInput.min) +
            '% 100%';
    }

    function handleInputRangeChange(e) {
        setInputRangeOnInput(e.target);
    }

    if (monthRevenueInput) {
        monthRevenueInput.addEventListener('input', handleInputRangeChange);
    }

    loadNewRegisterSavedData();
});

function redirectToAccounts(url_data) {
    $.ajax({
        method: 'GET',
        url: '/send-authenticated',
        headers: {
            Authorization: $('meta[name="access-token"]').attr('content'),
            Accept: 'application/json',
        },
        error: (response) => {
            errorAjaxResponse(response);
        },
        success: (response) => {
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

    return value.toLocaleString('pt-br', {
        style: 'currency',
        currency: currency,
    });
}

function scrollCustom(div, padding = false, type = '') {
    var scroll = 0;
    var scrollDiv = 0;
    var valuePadding = 0;
    var heightAdjust = 0;

    $(div).css('padding-right', '12px');
    $(div).append('<div class="scrollbox"></div>');
    $(div).append('<div class="scrollbox-bar"></div>');

    $(div).on('wheel', function(event) {
        if (event.originalEvent.deltaY !== 0) {
            if (padding == true) {
                valuePadding = 40;
            }

            if (type == 'modal-body') {
                heightAdjust = 20;
            }

            var heightDivScroll = $(div).height() + valuePadding;
            var heightDivScrollTotal = $(div).children(':first').height() + valuePadding;

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
                if (sumScroll <= heightDivScroll - 60) {
                    scroll += heightCalculateScroll;
                    scrollDiv += heightCalculateTotal;
                } else {
                    scroll = heightDivScroll - 60;
                    scrollDiv = heightDivScrollTotal - heightDivScroll;
                }
            }

            $(div)
                .find('.scrollbox-bar')
                .css('top', scroll + 'px');
            $(div)
                .children(':first')
                .css('margin-top', '-' + scrollDiv + 'px');
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
        var scrollDiv = changePosition ? $(div).children(':first').css('margin-left').replace('px', '') : 0;
    }

    $(div).on('wheel', function(event) {
        if (event.originalEvent.deltaY !== 0) {
            var widthDivScroll = $(div).width();
            var widthDivScrollTotal = $(div).children(':first').width() - 12;

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
                if (sumScroll <= widthDivScroll - 60) {
                    scroll += widthtCalculateScroll;
                    scrollDiv += widthCalculateTotal;
                } else {
                    scroll = widthDivScroll - 60;
                    scrollDiv = widthDivScrollTotal - widthDivScroll;
                }
            }

            $(div)
                .find('.scrollbox-bar')
                .css('left', scroll + 'px');
            $(div)
                .children(':first')
                .css('margin-left', '-' + scrollDiv + 'px');
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
        timer: 6000,
    });
}

$(document).ajaxStart(function(event, jqXHR, ajaxOptions, data) {
    $('#loader').addClass('loader').fadeIn('slow');
    $('#loaderCard').addClass('loader').fadeIn('slow');
});

$(document).ajaxError(function(event, jqXHR, ajaxOptions, data) {
    $('#loader').removeClass('loader').fadeOut('slow');
    $('#loaderCard').removeClass('loader').fadeOut('slow');
});

$(document).ajaxSuccess(function(event, jqXHR, ajaxOptions, data) {
    $('.loaderCard').removeClass('loaderCard').fadeOut('slow');
});

$('.table').addClass('table-striped');

function loading(elementId, loaderClass) {
    if (loaderClass == '') {
        $(elementId).html('');
        $(elementId).append('<div class="loading"></div>');
    } else if (loaderClass == '#loaderCard') {
        $(elementId).append('<a class="loaderCard"></a>');
    }
}

function loadingOnScreen() {
    loadOnAnyPage('.page');
    $('body').css('overflow-y', 'hidden');
    $('.new-register-page-open-modal-container').hide();
}

function loadingOnChart(target) {
    $(target)
        .fadeIn()
        .append(
            `<div style="z-index: 100; border-radius: 16px; position: absolute;" class="sirius-loading bg-white">
        <span class="loader-any" style="margin-top: 150px"></span>
        </div>`,
        );
}

function loadingOnAccountsHealth(target, margin = '80px') {
    $(target)
        .fadeIn()
        .append(
            `<div style="z-index: 100; border-radius: 16px; position: absolute;width: 100%;height: 100%;" class="d-flex justify-content-center align-items-center align-self-center bg-white block-loader-any"
        style="background-color: #f4f4f4;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        ">
            <span class="loader-any" style="margin-top: ` +
            margin +
            `"></span>
        </div>`,
        );
}

function loadingOnChartRemove(target) {
    $(target).fadeOut(function() {
        $(target).html('');
    });
}

function loadingOnAccountsHealthRemove(target) {
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
        options.styles.container.minWidth = options.styles.container.minWidth
            ? options.styles.container.minWidth
            : $(target).css('width');
        options.styles.container.minHeight = options.styles.container.minHeight
            ? options.styles.container.minHeight
            : $(window.top).height() * 0.7; //70% of visible window area
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
        if (!target.hasClass('tab-pane') || (target.hasClass('tab-pane') && target.hasClass('active'))) {
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

function loadingSkeletonCards(elementAppend) {
    const loadingHtml =
        '<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 card-skeleton-loading">' +
        '    <div class="card">' +
        '        <div class="d-flex justify-content-center">' +
        '            <div class="skeleton-loading card-skeleton-loading-img-top" style="border-radius: 0;"></div>' +
        '        </div>' +
        '        <div class="card-body">' +
        '            <div class="skeleton-loading mt-3" style="width: 80%; height: 20px;"></div>' +
        '            <div class="skeleton-loading mt-45" style="width: 60%; height: 10px"></div>' +
        '        </div>' +
        '    </div>' +
        '</div>';

    let cont = 2;

    if (window.innerWidth > 479 && window.innerWidth <= 767) {
        cont = 4;
    }

    if (window.innerWidth > 767 && window.innerWidth <= 991) {
        cont = 6;
    }

    if (window.innerWidth > 991) {
        cont = 8;
    }

    let html = '';

    for (let i = 0; i < cont; i++) {
        html += loadingHtml;
    }

    elementAppend.append(html);
}

function removeLoadingSkeletonCards() {
    $('.card-skeleton-loading').remove();
}

function loadingOnScreenRemove() {
    window.setTimeout(function() {
        loadOnAnyPage('.page', true);
        $('body').css('overflow-y', 'unset');
    }, 2000);
    $('.page-header').fadeIn();
    $('#btn-modal').fadeIn();
}

function loadOnNotification(whereToLoad) {
    $(whereToLoad).html('');
    $(whereToLoad).append(
        '<div class=\'loading\' style=\'width:346px; height:150px\'>' +
        '<span class=\'loaderNotification\' >' +
        '</span>' +
        '</div>',
    );
}

function loadOnModal(whereToLoad) {
    $(whereToLoad).children().hide('fast');
    $('#modal-title').html('Carregando...');
    $(whereToLoad).append('<div id=\'loaderModal\' class=\'loadinModal\'><div class=\'loaderModal\'></div></div>');
    $('#loadingOnScreen').append('<div class=\'blockScreen\'></div>');
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
        $(modal)
            .find(whereToLoad)
            .append(
                '<div id=\'loaderModal\' class=\'loadingModal\' style=\'height: 80px; position: relative;\'><div class=\'loaderModal\' style=\'position: absolute;\'></div></div>',
            );
    } else {
        $(modal).find('.modal-title').html('Carregando...');
        $(modal).find('.modal-body').children().fadeOut('fast');
        $(modal).find('.modal-footer').fadeOut('fast');
        $(modal)
            .find('.modal-body')
            .append(
                '<div id=\'loaderModal\' class=\'loadingModal\' style=\'height: 80px; position: relative;\'><div class=\'loaderModal\' style=\'position: absolute;\'></div></div>',
            );
    }

    $(modal).modal('show');
}

function loadOnModalRemove(modal) {
    $(modal).find('.modal-body').addClass('show');
    $(modal)
        .find('.ph-item')
        .fadeOut(3000, function() {
            this.remove();
        });

    $(modal).find('.modal-body').children().fadeIn(3000);

    $(modal).find('.modal-footer').fadeIn(3000);
}

function loadOnTable(whereToLoad, tableReference) {
    $(whereToLoad).html('');
    $(tableReference).removeClass('table-striped');
    $(whereToLoad).append(
        '<tr id=\'loaderLine\'>' +
        '<td colspan=\'12\' align=\'center\' class=\'loadingTable\' style=\'height:100px\'>' +
        '<a id=\'loader\' class=\'loaderTable\'></a>' +
        '</td>' +
        '</tr>',
    );
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
        options.styles.container.minWidth = options.styles.container.minWidth
            ? options.styles.container.minWidth
            : $(target).css('width');
        options.styles.container.minHeight = options.styles.container.minHeight
            ? options.styles.container.minHeight
            : $(window.top).height() * 0.7; //70% of visible window area
        container.css(options.styles.container);
        if (options.styles.loader) {
            loader.css(options.styles.loader);
        }

        //add message load
        if (options.message) {
            container.append(`<p class="mb-30">${options.message}</p>`);
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
        if (!target.hasClass('tab-pane') || (target.hasClass('tab-pane') && target.hasClass('active'))) {
            $(target).fadeIn();
        }
    }
}

function loadOnAnyPage(target, remove = false, options = {}) {
    //cleanup
    target = $(target);
    target.parent().find('.loader-any-container-page').remove();

    if (!remove) {
        //create elements
        let container = $('<div class="loader-any-container-page"></div>');
        let loader = $('<span class="loader-any-page"></span>');

        //apply styles or use default
        options.styles = options.styles ? options.styles : {};
        options.styles.container = options.styles.container ? options.styles.container : {};
        options.styles.container.minWidth = options.styles.container.minWidth
            ? options.styles.container.minWidth
            : $(target).css('width');
        options.styles.container.minHeight = options.styles.container.minHeight
            ? options.styles.container.minHeight
            : $(window.top).height() * 0.7; //70% of visible window area
        container.css(options.styles.container);
        if (options.styles.loader) {
            loader.css(options.styles.loader);
        }

        //add message load
        if (options.message) {
            container.append(`<p class="mb-30">${options.message}</p>`);
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
        if (!target.hasClass('tab-pane') || (target.hasClass('tab-pane') && target.hasClass('active'))) {
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
        footer: swalFooter,
    });
}

$(document).ajaxComplete(function(jqXHR, textStatus) {
    switch (textStatus.status) {
        case 200:
            break;
        case 401:
            window.location.href = '/';
            break;
        case 404:
            break;
        case 500:
            break;
        case 413:
            alertCustom('error', 'O tamanho do arquivo é maior que o limite máximo.');
            break;
        case 422:
            break;
        case 419:
            window.location.href = '/';
            break;
    }
});

$('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical.is-enabled').attr('overflow', 'hidden');

function isMobile() {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

    return width < 500;
}

function pagination(response, model, callback) {
    $(paginationContainer).hide();
    let paginationContainer = '#pagination-' + model;

    $(paginationContainer).children().attr('disabled', 'disabled');
    $(paginationContainer).html('');

    let currentPage = response.meta.current_page;
    let lastPage = response.meta.last_page;

    if (lastPage === 1) {
        $(paginationContainer).css({ background: '#f4f4f4' });
        return false;
    }

    let first_page = `<button class='btn nav-btn first_page'>1</button>`;
    $(paginationContainer).append(first_page);

    if (currentPage === 1) {
        $(paginationContainer).css({ background: '#ffffff' });
        $(paginationContainer + ' .first_page')
            .attr('disabled', true)
            .addClass('nav-btn')
            .addClass('active');
    }

    $(paginationContainer + ' .first_page').on('click', function() {
        callback('?page=1');
    });

    if (isMobile()) {
        for (let x = 1; x > 0; x--) {
            if (currentPage - x <= 1) {
                continue;
            }
            if (x >= 1) {
                $(paginationContainer).append(`
                    <button class='btn nav-btn page_${currentPage - x}'>
                        ${currentPage - x}
                    </button>
                `);

                $(paginationContainer + ' .page_' + (currentPage - x)).on('click', function() {
                    callback('?page=' + $(this).html());
                });
            }
        }

        if (currentPage !== 1 && currentPage !== lastPage) {
            var current_page = `<button class='btn nav-btn active current_page'>${currentPage}</button>`;
            $(paginationContainer).append(current_page);
            $(paginationContainer + ' .current_page')
                .attr('disabled', true)
                .addClass('nav-btn')
                .addClass('active');
        }

        for (let x = 1; x < 2; x++) {
            if (currentPage + x >= lastPage) {
                continue;
            }

            if (x >= 1) {
                $(paginationContainer).append(
                    `<button class='btn nav-btn page_${currentPage + x}'>
                        ${currentPage + x}
                    </button>`,
                );

                $(paginationContainer + ' .page_' + (currentPage + x)).on('click', function() {
                    callback('?page=' + $(this).html());
                });
            }
        }
    }

    if (!isMobile()) {
        for (let x = 3; x > 0; x--) {
            if (currentPage - x <= 1) {
                continue;
            }

            if (x >= 1) {
                $(paginationContainer).append(`
                    <button class='btn nav-btn page_${currentPage - x}'>
                        ${currentPage - x}
                    </button>
                `);

                $(paginationContainer + ' .page_' + (currentPage - x)).on('click', function() {
                    callback('?page=' + $(this).html());
                });
            }
        }

        if (currentPage !== 1 && currentPage !== lastPage) {
            var current_page = `<button class='btn nav-btn active current_page'>${currentPage}</button>`;

            $(paginationContainer).append(current_page);

            $(paginationContainer + ' .current_page')
                .attr('disabled', true)
                .addClass('nav-btn')
                .addClass('active');
        }

        for (let x = 1; x < 4; x++) {
            if (currentPage + x >= lastPage) {
                continue;
            }
            if (x >= 1) {
                $(paginationContainer).append(
                    `<button class='btn nav-btn page_${currentPage + x}'>
                        ${currentPage + x}
                    </button>`,
                );

                $(paginationContainer + ' .page_' + (currentPage + x)).on('click', function() {
                    callback('?page=' + $(this).html());
                });
            }
        }
    }

    if (lastPage !== 1) {
        var last_page = `<button class='btn nav-btn last_page mr-0'>${lastPage}</button>`;

        $(paginationContainer).append(last_page);

        if (currentPage === lastPage) {
            $(paginationContainer + ' .last_page')
                .attr('disabled', true)
                .addClass('nav-btn')
                .addClass('active');
        }

        $(paginationContainer + ' .last_page').on('click', function() {
            callback('?page=' + lastPage);
        });
    }

    $('table').addClass('table-striped');
    $(paginationContainer).show();
}

function copyToClipboard(elem) {
    // create hidden text element, if it doesn't already exist
    var targetId = '_hiddenCopyText_';
    var isInput = elem.tagName === 'INPUT' || elem.tagName === 'TEXTAREA';
    var origSelectionStart, origSelectionEnd;

    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement('textarea');
            target.style.position = 'absolute';
            target.style.left = '-9999px';
            target.style.top = '0';
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
        succeed = document.execCommand('copy');
    } catch (e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === 'function') {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = '';
    }
    return succeed;
}

function errorAjaxResponse(response) {
    if (response.responseJSON) {
        let errors = response.responseJSON.errors ? response.responseJSON.errors : {};
        errors = Object.values(errors).join('\n');
        if (response.status === 422 || response.status === 404 || (response.status === 403 && !isEmpty(errors))) {
            alertCustom('error', errors);
        } else if (response.status === 401) {
            // Não esta autenticado
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
    let type = request.getResponseHeader('Content-Type');
    // Get file name
    let contentDisposition = request.getResponseHeader('Content-Disposition');
    let fileName = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
    fileName = fileName ? fileName[0].replace('filename=', '') : '';

    var a = document.createElement('a');
    a.style.display = 'none';
    document.body.appendChild(a);
    a.href = window.URL.createObjectURL(new Blob([response], { type: type }));
    a.setAttribute('download', fileName);
    a.click();
    window.URL.revokeObjectURL(a.href);
    document.body.removeChild(a);
}

$(document).on('click', 'a[data-copy_text],a[data-copy_id]', function(event, i) {
    event.preventDefault();
    let inputId = $(this).data('copy_id') || '#copyText';
    let copyText = inputId === '#copyText' ? $(this).data('copy_text') || '' : $(inputId).val() || '';
    if (copyText === '') {
        return false;
    }
    if (document.getElementById('copyText') === null) {
        let input = document.createElement('input');
        input.type = 'text';
        input.id = 'copyText';
        input.value = copyText;
        document.getElementsByTagName('body')[0].appendChild(input);
    } else {
        document.getElementById('copyText').value = copyText;
    }
    document.getElementById('copyText').select();
    document.execCommand('copy');
    setTimeout(function() {
        $('#copyText').remove();
    }, 1000);
    alert('Link ' + $(inputId).val() + ' copiado com Sucesso!');
});

/* TOP ALERT */

$('.top-alert-close').on('click', function() {
    $('#document-pending').fadeOut();
});

/* END - TOP ALERT */

/* Document Pending Alert */

sessionStorage.removeItem('documentsPending');

function verifyDocumentPending() {
    changeNewRegisterLayoutOnWindowResize();
    var count = 0;

    $.ajax({
        method: 'GET',
        url: '/api/core/verify-account/' + $('meta[name="user-id"]').attr('content'),
        headers: {
            Authorization: $('meta[name="access-token"]').attr('content'),
            Accept: 'application/json',
        },
        error: (response) => {
            errorAjaxResponse(response);
        },
        success: (response) => {
            if (response.data.user_account !== 'approved') {
                let verifyAccount = localStorage.getItem('verifyAccount');
                if (verifyAccount == null) {
                    $('.new-register-page-open-modal-container').hide();
                    $('.new-register-navbar-open-modal-container').fadeOut();

                    setStepContainer();

                    $('.new-register-overlay').fadeIn();
                } else {
                    changeNewRegisterLayoutOnWindowResize();
                }

                localStorage.setItem('verifyAccount', JSON.stringify(response.data));

                var card_user_info_status = '';
                var card_user_info_icon = '';
                var card_user_info_title = 'Nos conte sobre você';
                var card_user_info_description = 'Temos algumas perguntas para conhecer melhor você e seu negócio.';

                if (!response.data.informations_completed) {
                    count += 1;

                    card_user_info_status = 'extra-informations-user';
                    card_user_info_icon =
                        '<svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 10.999L11 11C12.0538 11 12.9181 11.8155 12.9945 12.8507L13 13V14.5C12.999 18 9.284 19 6.5 19C3.77787 19 0.164695 18.044 0.00545406 14.7296L0 14.5V12.999C0 11.9452 0.816397 11.0809 1.85081 11.0045L2 10.999ZM13.22 11H18C19.0538 11 19.9181 11.8164 19.9945 12.8508L20 13V14C19.999 17.062 17.142 18 15 18C14.32 18 13.569 17.904 12.86 17.678C13.196 17.292 13.467 16.851 13.662 16.351C14.205 16.476 14.715 16.5 15 16.5L15.2665 16.494C16.2518 16.4509 18.3529 16.1306 18.4927 14.205L18.5 14V13C18.5 12.7547 18.3222 12.5504 18.0896 12.5081L18 12.5H13.949C13.865 11.9986 13.6554 11.5432 13.3545 11.1598L13.22 11H18H13.22ZM2 12.499L1.89934 12.509C1.77496 12.5343 1.69 12.6018 1.646 12.645C1.6028 12.689 1.53528 12.7733 1.51 12.898L1.5 12.999V14.5C1.5 15.509 1.95 16.222 2.917 16.742C3.74315 17.1869 4.91951 17.4563 6.18258 17.4951L6.5 17.5L6.8174 17.4951C8.08035 17.4563 9.25592 17.1869 10.083 16.742C10.9886 16.2545 11.4416 15.5974 11.4947 14.6849L11.5 14.499V13C11.5 12.7547 11.3222 12.5504 11.0896 12.5081L11 12.5L2 12.499ZM6.5 0C8.985 0 11 2.015 11 4.5C11 6.985 8.985 9 6.5 9C4.015 9 2 6.985 2 4.5C2 2.015 4.015 0 6.5 0ZM15.5 2C17.433 2 19 3.567 19 5.5C19 7.433 17.433 9 15.5 9C13.567 9 12 7.433 12 5.5C12 3.567 13.567 2 15.5 2ZM6.5 1.5C4.846 1.5 3.5 2.846 3.5 4.5C3.5 6.154 4.846 7.5 6.5 7.5C8.154 7.5 9.5 6.154 9.5 4.5C9.5 2.846 8.154 1.5 6.5 1.5ZM15.5 3.5C14.397 3.5 13.5 4.397 13.5 5.5C13.5 6.603 14.397 7.5 15.5 7.5C16.603 7.5 17.5 6.603 17.5 5.5C17.5 4.397 16.603 3.5 15.5 3.5Z" fill="#5B5B5B"/></svg>';
                } else {
                    card_user_info_status = 'status-check';
                    card_user_info_icon =
                        '<svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.6876 0.346147C16.1041 0.807675 16.1041 1.55596 15.6876 2.01749L6.08758 12.6539C5.67102 13.1154 4.99565 13.1154 4.57909 12.6539L0.312419 7.92658C-0.10414 7.46505 -0.10414 6.71677 0.312419 6.25524C0.728979 5.79371 1.40435 5.79371 1.82091 6.25524L5.33333 10.1468L14.1791 0.346147C14.5956 -0.115382 15.271 -0.115382 15.6876 0.346147Z" fill="white"/></svg>';
                }

                $('.user-informations-status').html(`
                    <div class="card ${card_user_info_status}">
                        <div class="d-flex">
                            <div>
                                <div class="icon d-flex align-items-center">
                                    ${card_user_info_icon}
                                </div>
                            </div>
                            <div class="content">
                                <h1 class="title">${card_user_info_title}</h1>
                                <p class="description">${card_user_info_description}</p>
                            </div>
                        </div>
                    </div>
                `);

                var card_company_status = '';
                var card_company_icon = '';
                var card_company_title = '';
                var card_company_description = '';
                var card_company_button = '';
                var card_company_link = response.data.link_company;

                if (response.data.company_status == null) {
                    count += 1;

                    card_company_status = 'redirect-to-accounts';
                    card_company_icon =
                        '<svg width="17" height="20" viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 3.5C3.94772 3.5 3.5 3.94772 3.5 4.5C3.5 5.05228 3.94772 5.5 4.5 5.5C5.05229 5.5 5.5 5.05228 5.5 4.5C5.5 3.94772 5.05229 3.5 4.5 3.5ZM3.5 11.5C3.5 10.9477 3.94772 10.5 4.5 10.5C5.05229 10.5 5.5 10.9477 5.5 11.5C5.5 12.0523 5.05229 12.5 4.5 12.5C3.94772 12.5 3.5 12.0523 3.5 11.5ZM4.5 7C3.94772 7 3.5 7.44771 3.5 8C3.5 8.55229 3.94772 9 4.5 9C5.05229 9 5.5 8.55229 5.5 8C5.5 7.44771 5.05229 7 4.5 7ZM7 4.5C7 3.94772 7.44771 3.5 8 3.5C8.55229 3.5 9 3.94772 9 4.5C9 5.05228 8.55229 5.5 8 5.5C7.44771 5.5 7 5.05228 7 4.5ZM8 10.5C7.44771 10.5 7 10.9477 7 11.5C7 12.0523 7.44771 12.5 8 12.5C8.55229 12.5 9 12.0523 9 11.5C9 10.9477 8.55229 10.5 8 10.5ZM10.5 11.5C10.5 10.9477 10.9477 10.5 11.5 10.5C12.0523 10.5 12.5 10.9477 12.5 11.5C12.5 12.0523 12.0523 12.5 11.5 12.5C10.9477 12.5 10.5 12.0523 10.5 11.5ZM8 7C7.44771 7 7 7.44771 7 8C7 8.55229 7.44771 9 8 9C8.55229 9 9 8.55229 9 8C9 7.44771 8.55229 7 8 7ZM2.25 0C1.00736 0 0 1.00736 0 2.25V18.75C0 19.1642 0.335786 19.5 0.75 19.5H15.2528C15.667 19.5 16.0028 19.1642 16.0028 18.75V9.7493C16.0028 8.50666 14.9954 7.4993 13.7528 7.4993H12.5V2.25C12.5 1.00736 11.4926 0 10.25 0H2.25ZM1.5 2.25C1.5 1.83579 1.83579 1.5 2.25 1.5H10.25C10.6642 1.5 11 1.83579 11 2.25V8.2493C11 8.66352 11.3358 8.9993 11.75 8.9993H13.7528C14.167 8.9993 14.5028 9.33509 14.5028 9.7493V18H12.5V15.25C12.5 14.8358 12.1642 14.5 11.75 14.5H4.25C3.83579 14.5 3.5 14.8358 3.5 15.25V18H1.5V2.25ZM11 16V18H8.75V16H11ZM7.25 16V18H5V16H7.25Z" fill="#5B5B5B"/></svg>';
                    card_company_title = 'Cadastre sua empresa';
                    card_company_description = 'Na Azcend você pode ter uma ou mais empresas.';
                    card_company_button = '';
                } else {
                    if (response.data.company_status == 'pending') {
                        count += 1;

                        card_company_status = 'status-info';
                        card_company_icon =
                            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C15.523 0 20 4.478 20 10C20 15.522 15.523 20 10 20C4.477 20 0 15.522 0 10C0 4.478 4.477 0 10 0ZM10 1.667C5.405 1.667 1.667 5.405 1.667 10C1.667 14.595 5.405 18.333 10 18.333C14.595 18.333 18.333 14.595 18.333 10C18.333 5.405 14.595 1.667 10 1.667ZM9.25 4C9.6295 4 9.94346 4.28233 9.99315 4.64827L10 4.75V10H13.25C13.664 10 14 10.336 14 10.75C14 11.1295 13.7177 11.4435 13.3517 11.4931L13.25 11.5H9.25C8.8705 11.5 8.55654 11.2177 8.50685 10.8517L8.5 10.75V4.75C8.5 4.336 8.836 4 9.25 4Z" fill="#FAFAFA"/></svg>';
                        card_company_title = 'Você cadastrou sua empresa, mas não recebemos nenhum documento';
                        card_company_description =
                            'Você só poderá começar a sua operação depois de enviar e aprovar os documentos da sua empresa.';
                        card_company_button =
                            '<button class="btn btn-default redirect-to-accounts" data-url-value="' +
                            card_company_link +
                            '">Enviar documentos</button>';
                    } else if (response.data.company_status == 'analyzing') {
                        count += 1;

                        card_company_status = 'status-warning redirect-to-accounts';
                        card_company_icon =
                            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C15.523 0 20 4.478 20 10C20 15.522 15.523 20 10 20C4.477 20 0 15.522 0 10C0 4.478 4.477 0 10 0ZM10 1.667C5.405 1.667 1.667 5.405 1.667 10C1.667 14.595 5.405 18.333 10 18.333C14.595 18.333 18.333 14.595 18.333 10C18.333 5.405 14.595 1.667 10 1.667ZM9.25 4C9.6295 4 9.94346 4.28233 9.99315 4.64827L10 4.75V10H13.25C13.664 10 14 10.336 14 10.75C14 11.1295 13.7177 11.4435 13.3517 11.4931L13.25 11.5H9.25C8.8705 11.5 8.55654 11.2177 8.50685 10.8517L8.5 10.75V4.75C8.5 4.336 8.836 4 9.25 4Z" fill="#FAFAFA"/></svg>';
                        card_company_title = 'Estamos analisando seus documentos da sua empresa';
                        card_company_description =
                            'Esse processo de revisão leva um tempinho. Mas em breve retornaremos.';

                        card_company_button = '';
                    } else if (response.data.company_status == 'refused') {
                        count += 1;

                        card_company_status = 'status-error';
                        card_company_icon =
                            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C15.5228 0 20 4.47715 20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10C0 4.47715 4.47715 0 10 0ZM10 1.5C5.30558 1.5 1.5 5.30558 1.5 10C1.5 14.6944 5.30558 18.5 10 18.5C14.6944 18.5 18.5 14.6944 18.5 10C18.5 5.30558 14.6944 1.5 10 1.5ZM13.4462 6.39705L13.5303 6.46967C13.7966 6.73594 13.8208 7.1526 13.6029 7.44621L13.5303 7.53033L11.061 10L13.5303 12.4697C13.7966 12.7359 13.8208 13.1526 13.6029 13.4462L13.5303 13.5303C13.2641 13.7966 12.8474 13.8208 12.5538 13.6029L12.4697 13.5303L10 11.061L7.53033 13.5303C7.26406 13.7966 6.8474 13.8208 6.55379 13.6029L6.46967 13.5303C6.2034 13.2641 6.1792 12.8474 6.39705 12.5538L6.46967 12.4697L8.939 10L6.46967 7.53033C6.2034 7.26406 6.1792 6.8474 6.39705 6.55379L6.46967 6.46967C6.73594 6.2034 7.1526 6.1792 7.44621 6.39705L7.53033 6.46967L10 8.939L12.4697 6.46967C12.7359 6.2034 13.1526 6.1792 13.4462 6.39705Z" fill="white"/></svg>';
                        card_company_title = 'Tivemos problemas em verificar sua empresa';
                        card_company_description = 'Há um problema com seus documentos.';
                        card_company_button =
                            '<button class="btn btn-default redirect-to-accounts" data-url-value="' +
                            card_company_link +
                            '">Reenviar documentos</button>';
                    } else if (response.data.company_status == 'approved') {
                        card_company_status = 'status-check redirect-to-accounts';
                        card_company_icon =
                            '<svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.6876 0.346147C16.1041 0.807675 16.1041 1.55596 15.6876 2.01749L6.08758 12.6539C5.67102 13.1154 4.99565 13.1154 4.57909 12.6539L0.312419 7.92658C-0.10414 7.46505 -0.10414 6.71677 0.312419 6.25524C0.728979 5.79371 1.40435 5.79371 1.82091 6.25524L5.33333 10.1468L14.1791 0.346147C14.5956 -0.115382 15.271 -0.115382 15.6876 0.346147Z" fill="white"/></svg>';
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
                                    ${card_company_icon}
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

                var card_user_biometry_status = '';
                var card_user_biometry_icon = '';
                var card_user_biometry_title = '';
                var card_user_biometry_description = '';
                var card_user_biometry_button = '';
                var card_user_biometry_link = '/personal-info';

                if (response.data.user_status === 'pending' || response.data.user_status === '') {
                    count += 1;

                    card_user_biometry_status = 'redirect-to-accounts';
                    card_user_biometry_icon =
                        '<svg width="16" height="22" viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.5 0.5C1.84315 0.5 0.5 1.84315 0.5 3.5V18.5C0.5 20.1569 1.84315 21.5 3.5 21.5H12.5C14.1569 21.5 15.5 20.1569 15.5 18.5V7.12132C15.5 6.52458 15.2629 5.95229 14.841 5.53033L10.4697 1.15901C10.0477 0.737053 9.47542 0.5 8.87868 0.5H3.5ZM2 3.5C2 2.67157 2.67157 2 3.5 2H8V5.75C8 6.99264 9.00736 8 10.25 8H14V18.5C14 19.3284 13.3284 20 12.5 20H3.5C2.67157 20 2 19.3284 2 18.5V3.5ZM13.6893 6.5H10.25C9.83579 6.5 9.5 6.16421 9.5 5.75V2.31066L13.6893 6.5Z" fill="#5B5B5B"/></svg>';
                    card_user_biometry_title = 'Valide sua identidade';
                    card_user_biometry_description =
                        'Para reforçarmos a segurança, coletaremos seus dados. Acesse as configurações e realize a biometria.';
                    card_user_biometry_button =
                        '<button class="btn btn-default redirect-to-accounts" data-url-value="' +
                        card_user_biometry_link +
                        '">Ir para configurações</button>';
                } else if (response.data.user_status === 'analyzing') {
                    count += 1;

                    card_user_biometry_status = 'status-warning redirect-to-accounts';
                    card_user_biometry_icon =
                        '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C15.523 0 20 4.478 20 10C20 15.522 15.523 20 10 20C4.477 20 0 15.522 0 10C0 4.478 4.477 0 10 0ZM10 1.667C5.405 1.667 1.667 5.405 1.667 10C1.667 14.595 5.405 18.333 10 18.333C14.595 18.333 18.333 14.595 18.333 10C18.333 5.405 14.595 1.667 10 1.667ZM9.25 4C9.6295 4 9.94346 4.28233 9.99315 4.64827L10 4.75V10H13.25C13.664 10 14 10.336 14 10.75C14 11.1295 13.7177 11.4435 13.3517 11.4931L13.25 11.5H9.25C8.8705 11.5 8.55654 11.2177 8.50685 10.8517L8.5 10.75V4.75C8.5 4.336 8.836 4 9.25 4Z" fill="#FAFAFA"/></svg>';
                    card_user_biometry_title = 'Estamos analisando sua identidade';
                    card_user_biometry_description =
                        'O processo de revisão dos dados biométricos e seu comprovante de residência leva um tempinho. Em breve retornaremos!';
                } else if (response.data.user_status == 'refused') {
                    count += 1;

                    card_user_biometry_status = 'status-error';
                    card_user_biometry_icon =
                        '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C15.5228 0 20 4.47715 20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10C0 4.47715 4.47715 0 10 0ZM10 1.5C5.30558 1.5 1.5 5.30558 1.5 10C1.5 14.6944 5.30558 18.5 10 18.5C14.6944 18.5 18.5 14.6944 18.5 10C18.5 5.30558 14.6944 1.5 10 1.5ZM13.4462 6.39705L13.5303 6.46967C13.7966 6.73594 13.8208 7.1526 13.6029 7.44621L13.5303 7.53033L11.061 10L13.5303 12.4697C13.7966 12.7359 13.8208 13.1526 13.6029 13.4462L13.5303 13.5303C13.2641 13.7966 12.8474 13.8208 12.5538 13.6029L12.4697 13.5303L10 11.061L7.53033 13.5303C7.26406 13.7966 6.8474 13.8208 6.55379 13.6029L6.46967 13.5303C6.2034 13.2641 6.1792 12.8474 6.39705 12.5538L6.46967 12.4697L8.939 10L6.46967 7.53033C6.2034 7.26406 6.1792 6.8474 6.39705 6.55379L6.46967 6.46967C6.73594 6.2034 7.1526 6.1792 7.44621 6.39705L7.53033 6.46967L10 8.939L12.4697 6.46967C12.7359 6.2034 13.1526 6.1792 13.4462 6.39705Z" fill="white"/></svg>';
                    card_user_biometry_title = 'Seus dados foram recusados';
                    card_user_biometry_description =
                        'Acesse as configurações da sua conta e realize a biometria ou envie seu comprovante de residência novamente.';
                    card_user_biometry_button =
                        '<button class="btn btn-default redirect-to-accounts" data-url-value="' +
                        card_user_biometry_link +
                        '">Ir para configurações</button>';
                } else if (response.data.user_status === 'approved') {
                    //
                    card_user_biometry_status = 'status-check redirect-to-accounts';
                    card_user_biometry_icon =
                        '<svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.6876 0.346147C16.1041 0.807675 16.1041 1.55596 15.6876 2.01749L6.08758 12.6539C5.67102 13.1154 4.99565 13.1154 4.57909 12.6539L0.312419 7.92658C-0.10414 7.46505 -0.10414 6.71677 0.312419 6.25524C0.728979 5.79371 1.40435 5.79371 1.82091 6.25524L5.33333 10.1468L14.1791 0.346147C14.5956 -0.115382 15.271 -0.115382 15.6876 0.346147Z" fill="white"/></svg>';
                    card_user_biometry_title = 'Sua identidade foi validada';
                    card_user_biometry_description = 'Seus dados biométricos foram coletados e aprovados.';
                    card_user_biometry_button = '';
                }

                $('.user-biometry-status').html(`
                    <div class="card ${card_user_biometry_status}" data-url-value="${card_user_biometry_link}">
                        <div class="d-flex">
                            <div>
                                <div class="icon d-flex align-items-center">
                                    ${card_user_biometry_icon}
                                </div>
                            </div>
                            <div class="content">
                                <h1 class="title">${card_user_biometry_title}</h1>
                                <p class="description">${card_user_biometry_description}</p>
                                ${card_user_biometry_button}
                            </div>
                        </div>
                    </div>
                `);

                $('.new-register-open-modal-btn')
                    .find('.count')
                    .html(' (' + count + (count > 1 ? ' itens pendentes' : ' item pendente') + ')')
                    .promise()
                    .done(function() {
                        $('.alert-pendings').css('display', 'inline-flex');
                    });
            } else {
                $('.new-register-navbar-open-modal-container').remove();

                let verifyAccount = JSON.parse(localStorage.getItem('verifyAccount'));
                if (verifyAccount && verifyAccount.user_status !== 'approved') {
                    localStorage.setItem('verifyAccount', JSON.stringify(response.data));
                }
            }
        },
    });
}

function setNewRegisterSavedItem(item, value) {
    var userId = $('meta[name="user-id"]').attr('content');

    if (!localStorage.getItem('new-register-data-' + userId)) {
        localStorage.setItem('new-register-data-' + userId, JSON.stringify({}));
    }

    if (item) {
        let obj = JSON.parse(localStorage.getItem('new-register-data-' + userId));
        obj[item] = value;

        localStorage.setItem('new-register-data-' + userId, JSON.stringify(obj));
    }
}

function removeNewRegisterSavedItem(item) {
    var userId = $('meta[name="user-id"]').attr('content');

    if (localStorage.getItem('new-register-data-' + userId)) {
        let obj = JSON.parse(localStorage.getItem('new-register-data-' + userId));
        delete obj[item];

        localStorage.setItem('new-register-data-' + userId, JSON.stringify(obj));
    }
}

function setNewRegisterStep(step) {
    try {
        var userId = $('meta[name="user-id"]').attr('content');

        localStorage.setItem('new-register-step-' + userId, step);
    } catch (e) {
        newRegisterStepAux = step;
    }
}

function getNewRegisterStep() {
    let value;

    try {
        var userId = $('meta[name="user-id"]').attr('content');

        value = localStorage.getItem('new-register-step-' + userId);
    } catch (e) {
        value = newRegisterStepAux;
    }

    return value;
}

function changeProgressBar(step, action = 'next') {
    switch (parseInt(step)) {
        case 1:
            $('.new-register-step[data-step*=\'1\']').addClass('step-active');
            $('#new-register-step-progress-bar-1').css('transition-delay', action !== 'next' ? '1.5s' : '');
            $('#new-register-step-progress-bar-1').css('width', '50%');
            $('.new-register-step[data-step*=\'2\']').css('transition-delay', action !== 'next' ? '1s' : '');
            $('.new-register-step[data-step*=\'2\']').removeClass('step-active');
            $('#new-register-step-progress-bar-2').css('transition-delay', action !== 'next' ? '0.5s' : '');
            $('#new-register-step-progress-bar-2').css('width', '0');
            break;
        case 2:
            $('.new-register-step[data-step*=\'1\']').addClass('step-active');
            $('#new-register-step-progress-bar-1').css('transition-delay', '');
            $('#new-register-step-progress-bar-1').css('width', '100%');
            $('.new-register-step[data-step*=\'2\']').css('transition-delay', action === 'next' ? '0.5s' : '1.5s');
            $('.new-register-step[data-step*=\'2\']').addClass('step-active');
            $('#new-register-step-progress-bar-2').css('transition-delay', action === 'next' ? '1s' : '1s');
            $('#new-register-step-progress-bar-2').css('width', '50%');
            $('.new-register-step[data-step*=\'3\']').css('transition-delay', action !== 'next' ? '0.5s' : '');
            $('.new-register-step[data-step*=\'3\']').removeClass('step-active');
            break;
        case 3:
        case 4:
            $('.new-register-step[data-step*=\'1\']').addClass('step-active');
            $('#new-register-step-progress-bar-1').css('width', '100%');
            $('.new-register-step[data-step*=\'2\']').addClass('step-active');
            $('#new-register-step-progress-bar-2').css('transition-delay', action === 'next' ? '0.5s' : '1s');
            $('#new-register-step-progress-bar-2').css('width', '100%');
            $('.new-register-step[data-step*=\'3\']').css('transition-delay', action === 'next' ? '1s' : '0.5s');
            $('.new-register-step[data-step*=\'3\']').addClass('step-active');
            break;
    }
}

function changeNewRegisterLayoutOnWindowResize() {
    let userNameText = $('.new-register-overlay-title strong').text();

    if (window.innerWidth <= 370) {
        $('.new-register-overlay-title strong').css({
            display: 'block',
            'padding-top': '8px',
        });
    } else if (window.innerWidth > 370 && window.innerWidth <= 470) {
        $('.new-register-overlay-title strong').css({
            display: 'block',
            'padding-top': '0px',
        });

        if (userNameText.length > 10) {
            $('.new-register-overlay-title strong').text(userNameText.substring(0, 9) + '...');
        }
    } else if (window.innerWidth > 470 && window.innerWidth <= 665) {
        $('.new-register-overlay-title strong').css({
            display: 'unset',
            'padding-top': '0px',
        });

        if (userNameText.length > 14) {
            $('.new-register-overlay-title strong').text(userNameText.substring(0, 13) + '...');
        }
    } else if (window.innerWidth > 665) {
        $('.new-register-overlay-title strong').css({
            display: 'unset',
            'padding-top': '0px',
        });

        if (userNameText.length > 20) {
            $('.new-register-overlay-title strong').text(userNameText.substring(0, 19) + '...');
        }
    }

    if ($('.new-register-overlay').css('display') !== 'none') {
        return;
    }

    if (window.innerWidth >= 847) {
        $('.new-register-page-open-modal-container').fadeOut();
        $('.new-register-navbar-open-modal-container').fadeIn();
    } else {
        $('.new-register-navbar-open-modal-container').hide();
        $('.new-register-page-open-modal-container').fadeIn();
    }
}

function validateStep(step) {
    let isValid = false;

    switch (parseInt(step)) {
        case 1:
            isValid = $('div[data-step-1-selected*=\'1\']').length > 0;
            break;
        case 2:
            isValid = true;
            break;
        case 3:
            isValid =
                ($('input[name=\'step-3-sales-site-check\']').is(':checked') ||
                    $('input[name=\'step-3-sales-site\']').val()) &&
                ($('input[name=\'step-3-gateway-check\']').is(':checked') || $('input[name=\'step-3-gateway\']').val());
            break;
        default:
            isValid = true;
            break;
    }

    return isValid;
}

function setStepContainer() {
    if (!getNewRegisterStep()) {
        setNewRegisterStep('1');
    }

    let step = getNewRegisterStep();

    changeProgressBar(step);

    setStepButton(step);

    $('#new-register-step-' + step + '-container').addClass('d-flex flex-column');
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

function step2CheckboxOnChange(checkbox, inputText) {
    if (checkbox.is(':checked')) {
        inputText.removeAttr('disabled');
    } else {
        inputText.val('');
        inputText.attr('disabled', true);

        removeNewRegisterSavedItem(inputText.attr('id'));
    }
}

function setInputRangeOnInput(target) {
    const minVal = target.min;
    const maxVal = target.max;
    let val = target.value;

    target.style.backgroundSize = ((val - minVal) * 100) / (maxVal - minVal) + '% 100%';

    val = val * 1000;

    $('#new-register-month-revenue span:first-child').text(
        (val === 5000 ? 'Até ' : val === 1000000 ? 'Acima de ' : '') + 'R$',
    );
    $('#new-register-month-revenue span:last-child').text(
        val.toLocaleString('pt-BR', {
            maximumFractionDigits: 2,
            minimumFractionDigits: 2,
        }),
    );

    setNewRegisterSavedItem(target.id, target.value);
}

function loadNewRegisterSavedData() {
    var userId = $('meta[name="user-id"]').attr('content');

    if (localStorage.getItem('new-register-data-' + userId)) {
        let obj = JSON.parse(localStorage.getItem('new-register-data-' + userId));

        for (const prop in obj) {
            const element = $('#' + prop);

            if (element.prop('nodeName') === 'DIV') {
                element.addClass('option-selected').attr('data-step-1-selected', 1);
            }

            if (element.prop('nodeName') === 'INPUT' && element.attr('type') === 'checkbox') {
                element.prop('checked', true);
                element.trigger('change');
            }

            if (element.prop('nodeName') === 'INPUT' && element.attr('type') === 'text') {
                element.val(obj[prop]);
            }

            if (element.prop('nodeName') === 'INPUT' && element.attr('type') === 'range') {
                element.val(obj[prop]);
                setInputRangeOnInput(document.getElementById('new-register-range'));
            }
        }
    }
}

function saveNewRegisterData() {
    const newRegisterData = {
        niche: JSON.stringify({
            others: $('div[data-step-1-value=others]').attr('data-step-1-selected'),
            classes: $('div[data-step-1-value=classes]').attr('data-step-1-selected'),
            subscriptions: $('div[data-step-1-value=subscriptions]').attr('data-step-1-selected'),
            digitalProduct: $('div[data-step-1-value=digital-product]').attr('data-step-1-selected'),
            physicalProduct: $('div[data-step-1-value=physical-product]').attr('data-step-1-selected'),
            dropshippingImport: $('div[data-step-1-value=dropshipping-import]').attr('data-step-1-selected'),
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
            otherName: $('#know-cloudfox').val(),
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
        method: 'POST',
        url: '/api/user-informations',
        data: newRegisterData,
        dataType: 'json',
        headers: {
            Authorization: $('meta[name="access-token"]').attr('content'),
            Accept: 'application/json',
        },
        error: function error(response) {
            loadingOnScreenRemove();

            alertCustom('error', response.responseJSON.message);
        },
        success: function success(response) {
            verifyDocumentPending();

            setNewRegisterStep('4');

            $('#new-register-step-3-container').removeClass('d-flex flex-column');

            $('#new-register-step-4-container').addClass('d-flex flex-column');

            $('#new-register-steps-actions').removeClass('justify-content-between');
            $('#new-register-steps-actions').addClass('justify-content-center');

            $('.extra-informations-user').hide();

            $('#new-register-steps-actions').html(
                '<button type="button" class="btn new-register-btn close-modal">Fechar</button>',
            );

            var userId = $('meta[name="user-id"]').attr('content');

            localStorage.removeItem('new-register-data-' + userId);

            loadingOnScreenRemove();
        },
    });
}

/* End - Document Pending Alert */

/* Cookies */
function setCookie(name, hours, object) {
    var expires;
    var date;
    var value;
    date = new Date(); // criando cookie com a data atual
    date.setTime(date.getTime() + hours * 3600 * 1000);
    expires = date.toUTCString();
    value = JSON.stringify(object);

    document.cookie = name + '=' + value + '; expires=' + expires + ';path=/';
}

function getCookie(name) {
    name += '=';
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
    return '';
}

function deleteCookie(name) {
    setCookie(name, -1);
}

/* Cookies */

$.fn.shake = function() {
    let distance = 5;
    let speed = 50;
    let repeat = 3;
    let animation1 = { left: '+=' + distance };
    let animation2 = { left: '-=' + distance * 2 };

    for (let i = 0; i < repeat; i++) {
        $(this).animate(animation1, speed).animate(animation2, speed).animate(animation1, speed);
    }
};

// sirius select

function initSiriusSelect(target) {
    let $target = $(target);
    let classes = Array.from(target[0].classList)
        .filter((e) => e !== 'sirius-select' && e !== 'company-navbar')
        .join(' ');
    $target.removeClass(classes);
    if ($target.is(':disabled')) classes += ' disabled';
    $target.wrap(`<div class="sirius-select-container ${classes}"></div>`);
    $target.hide();
    $target.after(`<div class="sirius-select-options"></div>`);
    $target.after(`<div class="sirius-select-text"></div>`);

    renderSiriusSelect($target);
}

function renderSiriusSelect(target) {
    let $target = $(target);
    let $wrapper = $target.parent();
    let $text = $wrapper.find('.sirius-select-text');
    let $options = $wrapper.find('.sirius-select-options');
    $options.html('');
    $target.children('option').each(function() {
        let option = $(this);
        let attributes = Object.values(this.attributes).reduce((text, attr) => {
            if (!['id', 'value', 'data-value', 'selected', 'disabled'].includes(attr.name)) {
                if (attr.value) return text + ` ${attr.name}="${attr.value}"`;
                return text + ` ${attr.name}`;
            }
            return text;
        }, '');
        let disabled = option.is(':disabled') ? `class="disabled"` : '';
        $options.append(`<div data-value="${option.val()}" ${attributes} ${disabled}>${option.text()}</div>`);
    });
    $text.text($target.children('option:selected').eq(0).text());
}

$.fn.siriusSelect = function() {
    initSiriusSelect(this);
};
// END sirius select

/**
 * Menu implementation
 */
$(document).ready(function() {
    var bodyEl = $('body');
    var menuBarToggle = $('[data-toggle="menubar"]');
    var toggle = $('[data-toggle="menubar"].nav-link');
    menuBarToggle.off().on('click', function() {
        bodyEl.toggleClass('site-menubar-unfold site-menubar-fold site-menubar-open site-menubar-hide');
        menuBarToggle.toggleClass('hided');
        if (toggle.hasClass('hided')) {
            $('#logoIconSirius').fadeOut().addClass('d-none');
            $('#logoSirius').fadeIn().removeClass('d-none');
            $('.hamburger-desk').css('margin-left', '240px');
        } else {
            $('#logoIconSirius').fadeIn().removeClass('d-none');
            $('#logoSirius').fadeOut().addClass('d-none');
            $('.hamburger-desk').css('margin-left', '70px');
        }
    });

    var siteMenuItems = $('.site-menu-item.has-sub');
    var siteMenuBar = $('.site-menubar');
    var menuTimeout;
    siteMenuBar
        .on('mouseenter', function() {
            bodyEl.addClass('site-menubar-hover');
            $('#logoIconSirius').fadeOut().addClass('d-none');
            $('#logoSirius').fadeIn().removeClass('d-none');
        })
        .on('mouseleave', function() {
            menuTimeout = setTimeout(function() {
                bodyEl.removeClass('site-menubar-hover');
                if (!toggle.hasClass('hided')) {
                    $('#logoIconSirius').fadeIn().removeClass('d-none');
                    $('#logoSirius').fadeOut().addClass('d-none');
                }
            }, 500);
        })
        .find('*')
        .on('mouseenter', function(event) {
            clearTimeout(menuTimeout);
            bodyEl.addClass('site-menubar-hover');
        });

    siteMenuItems.on('click', function() {
        siteMenuItems.not($(this)).removeClass('active');
        $(this).toggleClass('active');
    });

    var links = $('.site-menubar .site-menu-item a');
    $.each(links, function(key, va) {
        var current = document.URL;

        if (va.href == document.URL || (current.match(va.href) || []).length >= 1) {
            $(this).addClass('menu-active');
            $(this).parents('.site-menu-item.has-sub').find('> a').addClass('menu-active');
        }
    });

    // Disable page scroll when a modal is open
    $(document).on('shown.bs.modal', function(e) {
        document.querySelector('body').style.overflowY = 'hidden';
    });
    $(document).on('hidden.bs.modal', function(e) {
        document.querySelector('body').style.overflowY = 'unset';
    });

    // sirius select
    $('.sirius-select').each(function() {
        $(this).siriusSelect();
    });

    // Função que será chamada quando uma mudança for detectada
    function handleMutations(mutationsList, observer) {
        mutationsList.forEach(mutation => {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                renderSiriusSelect(mutation.target);
            }
        });
    }

    // Seleciona todos os elementos '.sirius-select-container select'
    document.querySelectorAll('.sirius-select-container select').forEach(select => {
        // Cria uma instância de MutationObserver e passa a função de callback
        const observer = new MutationObserver(handleMutations);

        // Configura as opções de observação: observe mudanças nos filhos, atributos e no próprio nó
        const config = { childList: true, subtree: true, attributes: true };

        // Começa a observar o elemento alvo com as configurações especificadas
        observer.observe(select, config);
    });


    $(document).on('click', '.sirius-select-text', function() {
        let $target = $(this);
        let $options = $target.parent().find('.sirius-select-options');

        $('.sirius-select-text').not($target).removeClass('active');
        $('.sirius-select-options').not($options).fadeOut();

        $target.toggleClass('active');
        $target.hasClass('active') ? $options.fadeIn() : $options.fadeOut();
    });

    $(document).on('click', '.sirius-select-options div', function() {
        let $target = $(this);
        if (!$target.hasClass('disabled')) {
            let $wrapper = $target.parents('.sirius-select-container');
            $wrapper.find('select').val($target.data('value')).trigger('change');
            $wrapper.find('.sirius-select-text').removeClass('active').text($target.text());
            $target.parent().fadeOut();
        }
    });

    $(document).on('click', function(e) {
        let target = $(e.target);
        if (!target.parents('.sirius-select-container').length) {
            $('.sirius-select-container .sirius-select-text').removeClass('active');
            $('.sirius-select-container .sirius-select-options').fadeOut();
        }
    });
    // END sirius select

    // vertical scroll
    $('.vertical-scroll').on({
        'mousewheel wheel': function(e) {
            e.preventDefault();
            this.scrollLeft += e.originalEvent.deltaY;
        },
        mousedown: function(e) {
            $(this).addClass('scrolling').data('x', e.clientX).data('left', this.scrollLeft);
        },
        'mouseup mouseleave': function(e) {
            $(this).removeClass('scrolling').data('x', 0).data('left', 0);
        },
    });
    $(document).on('mousemove', '.vertical-scroll.scrolling', function(e) {
        const dx = e.clientX - $(this).data('x');
        this.scrollLeft = $(this).data('left') - dx;
    });
    // END vertical scroll
});

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
    return (string.includes('-') ? -1 : 1) * string.replace(/\D/g, '');
}

function removeMoneyCurrency(string) {
    if (string.charAt(0) == '-') {
        return '-' + string.substring(4);
    }
    return string.substring(3);
}

function buildModalBonusBalance(bonusObject) {
    var userName = bonusObject.user_name;
    var totalBalance = bonusObject.total_bonus;
    var alreadyUsed = bonusObject.used_bonus;
    var remainValue = bonusObject.current_bonus;
    var expireDate = bonusObject.expires_at;
    var percent = bonusObject.used_percentage;
    var chartColor = '';
    var chartColorSecondary = '';
    var chartSize = 106;

    if (percent < 25) {
        var chartColor = '#59BF75';
        var chartColorSecondary = '#CCF5D5';
    } else if (percent >= 25 && percent < 50) {
        var chartColor = '#2E85EC';
        var chartColorSecondary = '#CCDAF5';
    } else if (percent >= 50 && percent < 75) {
        var chartColor = '#F6BE2A';
        var chartColorSecondary = '#F4E9DB';
    } else if (percent >= 75 && percent < 100) {
        var chartColor = '#FF9900';
        var chartColorSecondary = '#F5E2CC';
    } else {
        var chartColor = '#E81414';
        var chartColorSecondary = '#E81414';
    }

    if ($(window).width() <= 768) {
        chartSize = 140;
    }

    content = `
        <div class="bonus-balance-content">
            <img class="bonus-illustration-1" src="../../../../../../build/global/img/svg/bonus-illustration-1.svg" alt=""/>

            <div class="bonus-text">
                <div class="d-flex justify-content-between align-items-center" style="width: 100%">
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 10px 0;">
                        <path d="M15.125 0C17.3687 0 19.1875 1.81884 19.1875 4.0625C19.1875 4.86819 18.953 5.6191 18.5484 6.25068L21.6875 6.25C22.5504 6.25 23.25 6.94955 23.25 7.8125V12.1875C23.25 12.943 22.7138 13.5733 22.0012 13.7185L22 20.9375C22 23.1038 20.3044 24.8741 18.168 24.9936L17.9375 25H6.0625C3.89621 25 2.12594 23.3044 2.00643 21.168L2 20.9375L2.00006 13.7188C1.28683 13.574 0.75 12.9434 0.75 12.1875V7.8125C0.75 6.94955 1.44956 6.25 2.3125 6.25L5.45157 6.25068C5.04704 5.6191 4.8125 4.86819 4.8125 4.0625C4.8125 1.81884 6.63134 0 8.875 0C10.1319 0 11.2555 0.57083 12.0007 1.46739C12.7445 0.57083 13.8681 0 15.125 0ZM11.0625 13.7487H3.875V20.9375C3.875 22.0852 4.75889 23.0265 5.88309 23.1178L6.0625 23.125H11.0625V13.7487ZM20.125 13.7487H12.9375V23.125H17.9375C19.0852 23.125 20.0265 22.2411 20.1178 21.1169L20.125 20.9375V13.7487ZM11.0625 8.125H2.625V11.875L11.0625 11.8737V8.125ZM21.375 11.875V8.125H12.9375V11.8737L21.375 11.875ZM15.125 1.875C13.9169 1.875 12.9375 2.85438 12.9375 4.0625V6.24875H15.155L15.3044 6.24275C16.4286 6.15149 17.3125 5.21022 17.3125 4.0625C17.3125 2.85438 16.3331 1.875 15.125 1.875ZM8.875 1.875C7.66688 1.875 6.6875 2.85438 6.6875 4.0625C6.6875 5.21022 7.57139 6.15149 8.69559 6.24275L8.845 6.24875H11.0625V4.0625L11.0552 3.88309C10.964 2.75889 10.0227 1.875 8.875 1.875Z" fill="#FF4E05"/>
                    </svg>

                    <span id="modal-bonus-close" class="modal-bonus-close">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#464646">
                            <g data-name="Layer 2"><g data-name="close"><rect width="24" height="24" transform="rotate(180 12 12)" opacity="0"/><path d="M13.41 12l4.3-4.29a1 1 0 1 0-1.42-1.42L12 10.59l-4.29-4.3a1 1 0 0 0-1.42 1.42l4.3 4.29-4.3 4.29a1 1 0 0 0 0 1.42 1 1 0 0 0 1.42 0l4.29-4.3 4.29 4.3a1 1 0 0 0 1.42 0 1 1 0 0 0 0-1.42z"/></g></g>
                        </svg>
                    </span>
                </div>


                <h3 class="bonus-title"><span id="bonus-username">${
        userName.charAt(0).toUpperCase() + userName.slice(1).toLowerCase() || 'Olá!'
    }</span>, aqui está seu <b>desconto!</b></h3>

                <p>
                    Você ganhou <span id="total-bonus-balance" class="bold">${totalBalance}</span> em isenção de taxa sobre suas vendas. Você vai se impressionar com toda a tecnologia embarcada em sua
                    conta, que seja o início de uma parceria lucrativa. Boas vendas!
                </p>


                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="bonus-subtitle bold">
                        Acompanhe seu consumo
                    </h4>
                </div>

            </div>

            <div class="bonus-infos d-flex align-items-center">
                <div class="bonus-circle-chart d-flex justify-content-center align-items-center" style="width: ${chartSize}px; height: ${chartSize}px">
                    <div class="mkCharts" data-percent="${percent}" data-size="${chartSize}" data-stroke="4" data-color="${chartColor}" data-border="${chartColorSecondary}"></div>
                    <span class="bonus-percent-label" style="color: ${chartColor}">${percent}%</span>
                </div>

                <div class="bonus-numbers d-flex align-items-start ml-5">

                    <div class="d-flex flex-column align-items-baseline justify-content-start" style="width: 110px; gap: 8px">
                        <div>
                            <h4 class="bonus-number-title">
                                Você ganhou
                            </h4>
                            <span class="bonus-number">
                                ${totalBalance}
                            </span>
                        </div>

                        <div>
                            <h4 class="bonus-number-title">
                                Restam mais
                            </h4>
                            <span class="bonus-number" style="color: ${chartColor}">
                                ${remainValue}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex flex-column align-items-baseline justify-content-start" style="width: 110px; gap: 8px">
                        <div>
                            <h4 class="bonus-number-title">
                                Você já utilizou
                            </h4>
                            <span class="bonus-number">
                                ${alreadyUsed}
                            </span>
                        </div>
                        <div>
                            <h4 class="bonus-number-title">
                                Vence em
                            </h4>
                            <span class="bonus-number">
                                ${expireDate}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <span class="bonus-slogan">O seu sucesso é o <b>nosso combustível!</b>🚀</span>
        </div>
        `;

    // FUTURP HISTÓRICO DE BONUS
    // <button class="orange-link" onclick="toggleBonusContent()">
    //     Ver histórico
    // </button>
    // +
    // `
    // <div class="bonus-balance-history">
    //     <button class="orange-link back-button" onclick="toggleBonusContent()">
    //         <i class="material-icons">arrow_back</i> Voltar
    //     </button>
    //     <div class="d-flex">
    //         <h3 class="bonus-title d-flex align-items-center">
    //             <i class="material-icons">history</i> Histórico de descontos
    //         </h3>
    //     </div>
    //     <p>
    //         Aqui você controla todos os descontos que já recebeu
    //     </p>
    //     <div class="bonus-table-container scroller">
    //         <table class="bonus-history-table">
    //             <tr>
    //                 <th>Motivo</th>
    //                 <th>Valor</th>
    //                 <th>Período</th>
    //             </tr>
    //             <tr>
    //                 <td>Afiliados Brasil</td>
    //                 <td>R$ 5 mil</td>
    //                 <td>24/04 - 05/05/22</td>
    //             </tr>
    //         </table>
    //     </div>
    // </div>
    // `;

    $('.bonus-balance-container').html(content);
    mkChartRender();

    $('.close-bonus-modal, .modal-bonus-close').on('click', function() {
        $('body').removeClass('bonus-modal-opened');
        $('#bonus-balance-modal').fadeToggle('slow', 'linear');
        // $('.bonus-balance-container').html(loadSkeletonBonus);
    });
}

const toggleBonusContent = function() {
    $('.bonus-balance-content').fadeToggle();
    $('.bonus-balance-history').fadeToggle();
};

function showBonusBalance() {
    if (getCookie($('meta[name="user-id"]').attr('content') + '_bonus_balance')) {
        var bonus_balance = JSON.parse(getCookie($('meta[name="user-id"]').attr('content') + '_bonus_balance'));

        $('#total-bonus-balance').html(bonus_balance.current_bonus);

        buildModalBonusBalance(bonus_balance);

        if ($(window).width() <= 768) {
            $('.bonus-balance-button.mobile').show();
        } else {
            $('.bonus-balance-button.desktop').show();
        }
    } else {
        $.ajax({
            method: 'GET',
            url: '/api/core/get-bonus-balance',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            error: (response) => {
            },
            success: (response) => {
                if (response.error) {
                    return;
                }
                setCookie($('meta[name="user-id"]').attr('content') + '_bonus_balance', 0.083, response);

                $('#total-bonus-balance').html(response.current_bonus);

                buildModalBonusBalance(response);

                if ($(window).width() <= 768) {
                    $('.bonus-balance-button.mobile').show();
                } else {
                    $('.bonus-balance-button.desktop').show();
                }
            },
        });
    }
}

function generateJwt(userId, userName, userEmail) {
    var header = {
        alg: 'HS256',
        typ: 'JWT',
        kid: 'app_62e44ebe3f1d5c00ef1c6d40',
    };

    var stringifiedHeader = CryptoJS.enc.Utf8.parse(JSON.stringify(header));
    var encodedHeader = base64url(stringifiedHeader);

    var data = {
        external_id: userId,
        name: userName,
        email: userEmail,
        exp: new Date().getTime(),
        scope: 'user',
    };

    var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(data));
    var encodedData = base64url(stringifiedData);

    var token = encodedHeader + '.' + encodedData;

    var secret = 'iA4US5NugWzhYMdpVXY9uH9TPxWhtmyDVkIoxJ1jUhRHGts4Lrkl2SrjsbmbncnDd-_UVMQSMbwkJT_tjuVfvQ';

    var signature = CryptoJS.HmacSHA256(token, secret);
    signature = base64url(signature);

    var signedToken = token + '.' + signature;
    return signedToken;
}

function base64url(source) {
    // Encode in classical base64
    encodedSource = CryptoJS.enc.Base64.stringify(source);

    // Remove padding equal characters
    encodedSource = encodedSource.replace(/=+$/, '');

    // Replace characters according to base64url specifications
    encodedSource = encodedSource.replace(/\+/g, '-');
    encodedSource = encodedSource.replace(/\//g, '_');

    return encodedSource;
}

const loadSkeletonBonus = `
            <div class="bonus-balance-content">
                <img class="bonus-illustration-1" src="../../../../../../build/global/img/svg/bonus-illustration-1.svg" alt=""/>

                <div class="bonus-text">
                    <div class="skeleton skeleton-circle" style="width: 25px; height: 25px; margin-bottom: 10px;"> </div>

                    <h3 class="bonus-title"><div class="skeleton skeleton-text" style="width: 70%;"></div></h3>


                    <p>
                        <div class="skeleton skeleton-p"></div>
                        <div class="skeleton skeleton-p"></div>
                        <div class="skeleton skeleton-p"></div>
                        <div class="skeleton skeleton-p" style="width: 70%"></div>
                    </p>


                    <h4 class="bonus-subtitle bold">
                        <div class="skeleton skeleton-p" style="width: 50%; height: 25px;"></div>
                    </h4>

                </div>

                <div class="d-flex align-items-center">
                    <div class="bonus-circle-chart">
                        <div class="skeleton skeleton-circle" style="width: 100px; height: 100px;">
                        </div>
                    </div>

                    <div class="bonus-numbers d-flex flex-column">

                        <div class="d-flex justify-content-between">
                            <div style="margin-right: 15px; width: 90px">
                                <h4 class="bonus-number-title">
                                    <div class="skeleton skeleton-p" style="width: 60px; height: 15px;"></div>
                                </h4>
                                <span class="bonus-number">
                                    <div class="skeleton skeleton-p" style="width: 80px; height: 20px;"></div>
                                </span>
                            </div>

                            <div style="width: 90px">
                                <h4 class="bonus-number-title">
                                    <div class="skeleton skeleton-p" style="width: 60px; height: 15px;"></div>
                                </h4>
                                <span class="bonus-number">
                                    <div class="skeleton skeleton-p" style="width: 80px; height: 20px;"></div>
                                </span>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div style="margin-right: 15px; width: 90px">
                                <h4 class="bonus-number-title">
                                    <div class="skeleton skeleton-p" style="width: 60px; height: 15px;"></div>
                                </h4>
                                <span class="bonus-number">
                                    <div class="skeleton skeleton-p" style="width: 80px; height: 20px;"></div>
                                </span>
                            </div>

                            <div  style="width: 90px">
                                <h4 class="bonus-number-title">
                                    <div class="skeleton skeleton-p" style="width: 60px; height: 15px;"></div>
                                </h4>
                                <span class="bonus-number">
                                    <div class="skeleton skeleton-p" style="width: 80px; height: 20px;"></div>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="skeleton skeleton-p" style="width: 60%; margin-top: 10px"></div>
            </div>
            `;

function getCompaniesAndProjects(removeLoadingFunction = null) {
    var ajax = $.ajax({
        method: 'GET',
        url: `/api/core/usercompanies`,
        dataType: 'json',
        headers: {
            Authorization: $('meta[name="access-token"]').attr('content'),
            Accept: 'appliation/json',
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(data) {
            data.companies.push({
                id: 'v2RmA83EbZPVpYB',
                name: 'Empresa Demo',
                company_document_status: 'approved',
                active_flag: 1,
                projects: [
                    {
                        id: 'v2RmA83EbZPVpYB',
                        name: 'Loja Demonstrativa Azcend',
                        order_p: 1,
                        status: 1,
                    },
                ],
            });

            companies = data.companies;
            company_default = data.company_default;
            company_default_name = data.company_default_name;
            $.each(companies, function(c, company) {
                if (data.company_default == company.id) {
                    data.company_default_projects = company.projects;
                }
            });

            if (company_default == 'v2RmA83EbZPVpYB') {
                $('.alert-demo-account').fadeIn();
            } else {
                $('.alert-demo-account').fadeOut();
            }

            if (!isEmpty(companies)) {
                $('.company_id').val(company_default);
                $('.company-navbar').html('');

                for (let i = 0; i < companies.length; i++) {
                    if (company_default === companies[i].id)
                        itemSelected = 'selected="selected" style="font-weight:bold"';
                    else itemSelected = '';

                    if (companies[i].active_flag == false || companies[i].company_document_status != 'approved')
                        itemDisabled = 'disabled="disabled"';
                    else itemDisabled = '';

                    if (companies[i].company_type == '1') {
                        $('.company-navbar').append(
                            '<option value="' +
                            companies[i].id +
                            '" ' +
                            itemSelected +
                            ' ' +
                            itemDisabled +
                            '>Pessoa Física</option>',
                        );
                    } else {
                        if (companies[i].name.length > 20) companyName = companies[i].name.substring(0, 20) + '...';
                        else companyName = companies[i].name;
                        $('.company-navbar').append(
                            '<option value="' +
                            companies[i].id +
                            '" ' +
                            itemSelected +
                            ' ' +
                            itemDisabled +
                            '  title="' + companies[i].name + '">' +
                            companyName +
                            '</option>',
                        );
                    }
                }
                $('#company-select').addClass('d-sm-flex').css('display', 'block');
                return data;
            } else {
                //$(".content-error").show();
                $('#company-select, .page-content').hide();
                if (removeLoadingFunction) {
                    removeLoadingFunction();
                }
                loadingOnScreenRemove();
            }
        },
    });
    return ajax;
}

function updateCompanyDefault() {
    var company_id = $('.company-navbar').val();
    var ajax = $.ajax({
        method: 'POST',
        url: '/api/core/company-default',
        data: { company_id: company_id },
        headers: {
            Authorization: $('meta[name="access-token"]').attr('content'),
            Accept: 'application/json',
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(data) {
            getCompaniesAndProjects();
            return;
        },
    });
    return ajax;
}

function verifyIfCompanyIsDefault(companyId) {
    if ($('.alert-demo-account-overlay').css('display') == 'block') {
        $('.alert-demo-account-overlay').fadeOut();
    }
    $('.company-navbar').val(companyId);
    if ($('.company-navbar').find('option:selected').css('font-weight') == '700') {
        $('.sirius-select-options').css('display', 'none');
        return true;
    }
    return false;
}

function fillSelectProject(companiesAndProjects, selectorName, value = '') {
    $.each(companiesAndProjects.company_default_projects, function(i, project) {
        if (parseInt(project.status) === 1) {
            $(selectorName).append($('<option>', { value: project.id, text: project.name }));
        }
    });
    if (!isEmpty(value)) {
        $(selectorName).val(value);
    }
}

function showFiltersInReports(show) {
    if (show) {
        $('#box-projects').show();
        $('.date-report').show();
        return;
    }
    $('#box-projects').hide();
    $('.date-report').hide();
}

// Returns the status of the filtering button
function searchIsLocked(elementButton) {
    return elementButton.attr('block_search');
}

// Lock filter button
function lockSearch(elementButton) {
    elementButton.attr('block_search', 'true');
}

// Unlock filter button
function unlockSearch(elementButton) {
    elementButton.attr('block_search', 'false');
}

$('.btn-copy').on('click', function() {
    const button = $(this);
    const input = button.prev('input');
    copyTextToClipboard(input, 'Copiado com sucesso!');
});

$(document).on('click', '.copy-token', function() {
    const inputElement = $(this);

    copyTextToClipboard(inputElement, 'Token copiado!', 'Erro ao copiar token');
});

function copyTextToClipboard(inputElement, successMessage = 'Texto copiado!', errorMessage = 'Não foi possível copiar o texto.') {
    const isReadonly = inputElement.prop('readonly');
    if (isReadonly) {
        inputElement.prop('readonly', false);
    }
    inputElement.select();

    try {
        const successful = document.execCommand('copy');
        const msg = successful ? successMessage : errorMessage;
        alertCustom('success', msg);
    } catch (err) {
    }

    if (isReadonly) {
        inputElement.prop('readonly', true);
    }
}
