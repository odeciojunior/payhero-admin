const attachments2send = [];

$(() => {

    $("#project-empty").hide();
    $("#project-not-empty").show();

    // loadingOnScreen();
    //
    // getProjects();
    //
    // function getProjects(){
    //     $.ajax({
    //         method: "GET",
    //         url: '/api/projects?select=true',
    //         dataType: "json",
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         error: function error(response) {
    //             loadingOnScreenRemove();
    //             errorAjaxResponse(response);
    //         },
    //         success: function success(response) {
    //             if (response.data.length) {
    //                 getTickets();
    //                 $("#project-empty").hide();
    //                 $("#project-not-empty").show();
    //             } else {
    //                 $("#project-not-empty").hide();
    //                 $("#project-empty").show();
    //             }
    //             loadingOnScreenRemove();
    //         }
    //     });
    // }
    //
    // function getTickets(){
    //
    //     $.ajax({
    //         method: "GET",
    //         url: '/api/tickets',
    //         dataType: "json",
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         error: (resp) => {
    //             errorAjaxResponse(resp);
    //         },
    //         success: (resp) => {
    //             console.log(resp)
    //         }
    //     });
    // }

    // Comportamentos da tela

    $('.btn-search').on('click', function () {
        let searchBox = $('.search-box');
        searchBox.toggleClass('show');
        if (searchBox.hasClass('show')) {
            searchBox.find('input').focus();
        }
    });

    $('.filter-badge').on('click', function () {
        let btn = $(this);
        if (btn.hasClass('editable')) {
            if (btn.data('value')) {
                btn.data('value', '')
                    .removeClass('active');
            } else {
                let target = $(btn.data('target'));
                if (target.length) {
                    $('.filter-badge-input').not(target).removeClass('show');
                    if (target.hasClass('show')) {
                        target.removeClass('show')
                    } else {
                        let clientLeft = this.getBoundingClientRect().left
                        let offsetLeft = this.offsetLeft;
                        let left = clientLeft < offsetLeft ? clientLeft : offsetLeft;
                        target.css('left', (left - 145) + 'px');
                        target.css('top', '42px');
                        target.addClass('show');
                        target.find('input').val(btn.data('value'));
                    }
                }
            }
        } else {
            btn.toggleClass('active');
        }
    });

    $('.filter-badge-input button').on('click', function () {
        let parent = $(this).parent();
        let value = parent.find('input').val();
        if (value) {
            $(`[data-target='#${parent.attr('id')}']`).data('value', value)
                .addClass('active');
        }
        parent.removeClass('show');
    })

    $(document).on('click', function (e) {
        let container = $(".filter-badge.editable, .filter-badge-input");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('.filter-badge-input').removeClass('show');
        }
    });

    $(document).on('click', '.ticket-item', function () {
        $('.ticket-item').removeClass('active');
        $(this).addClass('active');
        let col1 = ['.search-container', '.tickets-container', '.pagination-container'];
        for (let i = 1; i <= col1.length; i++) {
            $(col1[i-1]).css('grid-area', i + ' / 2')
        }
        let col2 = ['.ticket-header', '.messages-container', '.write-container'];
        for (let i = 1; i <= col2.length; i++) {
            $(col2[i-1]).css('grid-area', i + ' / 1');
        }
    });

    $('.ticket-back').on('click', function () {
        let col1 = ['.ticket-header', '.messages-container', '.write-container'];
        for (let i = 1; i <= col1.length; i++) {
            $(col1[i-1]).css('grid-area', i + ' / 2');
        }
        let col2 = ['.search-container', '.tickets-container', '.pagination-container'];
        for (let i = 1; i <= col2.length; i++) {
            $(col2[i-1]).css('grid-area', i + ' / 1')
        }
    });

    const picker = new EmojiButton();
    picker.on('emoji', emoji => {
        document.querySelector('.writer-input').value += emoji;
    });
    $('#btn-emoji').on('click', function () {
        picker.togglePicker(this);
    });

    $('#btn-file').on('click', function () {
        $('#input-file').click();
    });

    $('#btn-image').on('click', function () {
        $('#input-image').click();
    });

    $('#input-file, #input-image').on('change', function () {
        updateAttachments(this.files);
    });

    function updateAttachments(files) {
        let container = $('.attachments-container');
        container.html('');
        attachments2send.push(...Array.from(files));
        attachments2send.forEach(function (attachment, key) {
            let name = attachment.name;
            if (name.length >= 10) {
                name = name.slice(0, 3) + '...' + name.slice(-6)
            }
            container.append(`<div class="attachment" data-id="${key}">${name}</div>`)
        });
        container.show();
    }

    $(document).on('click', '.attachment', function () {
        let elem = $(this);
        let id = elem.data('id');
        attachments2send.splice(id, 1);
        elem.remove();
        if (!$('.attachment').length) {
            $('.attachments-container').hide();
        }
    })

    $('.filter-container').slick({
        infinite: false,
        speed: 300,
        slidesToShow: 10,
        variableWidth: true,
        nextArrow: false,
        prevArrow: false,
        responsive: [
            {
                breakpoint: 1272,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 4,
                }
            },
            {
                breakpoint: 546,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 6,
                }
            },
            {
                breakpoint: 360,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 8,
                }
            }
        ]
    });

})
