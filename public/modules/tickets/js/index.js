const statusEnum = {
    1: 'Aberto',
    2: 'Finalizado',
    3: 'Em mediação',
};
const categoryEnum = {
    1: 'Reclamação',
    2: 'Dúvida',
    3: 'Sugestão',
};
const typeEnum = {
    1: 'customer',
    2: 'admin',
    3: 'system',
};
const statusColor = {
    1: 'open',
    2: 'closed',
    3: 'mediation',
};
const resumeLoader = {
    styles: {
        container: {
            minHeight: 33
        },
        loader: {
            width: 20,
            height: 20,
            borderWidth: 4
        }
    }
};
const loader = {
    width: '60px',
    height: '60px',
    borderWidth: '5px',
    borderColor: '#d3d3d3',
    borderLeftColor: 'transparent',
}
const ticketLoader = {
    styles: {
        loader: loader,
        container: {
            minHeight: '430px'
        }
    },
    insertBefore: '.pagination-container'
};
const messageLoader = {
    styles: {
        loader: loader,
        container: {
            minHeight: '520px'
        }
    },
}

const attachments2send = [];

$(() => {

    loadingOnScreen();

    getProjects();

    function getProjects() {
        $.ajax({
            method: "GET",
            url: '/api/projects?select=true',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (response.data.length) {
                    for (let project of response.data) {
                        $('#project-select').append(`<option value="${project.id}">${project.name}</option>`)
                    }
                    index();
                    getResume();
                    $('.page-header').show();
                    $("#project-not-empty").show();
                    $("#project-empty").hide();
                } else {
                    $('.page-header').hide();
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }
                loadingOnScreenRemove();
            }
        });
    }

    window.getFilters = function (page = 1) {

        let project = $('#project-select').val();

        let category = [];
        if ($('#category-complaint.active').length) {
            category.push(1);
        }
        if ($('#category-doubt.active').length) {
            category.push(2);
        }
        if ($('#category-suggestion.active').length) {
            category.push(3);
        }

        let document = $('#filter-document').data('value') || '';
        let name = $('#filter-name').data('value') || '';
        let answered = $("#filter-answer").data('value') || '';

        let period = $('#filter-date').data('value') || '';

        let status = $('#filter-status').val();

        let nameOrDocument = $('#name-or-document').val();

        let filters = {
            project,
            category,
            document,
            name,
            answered,
            period,
            status,
            nameOrDocument,
            page,
        }

        return new URLSearchParams(filters).toString();
    }

    function index(page = 1) {

        loadOnAny('.tickets-container', false, ticketLoader);
        clearViews();

        $.ajax({
            method: "GET",
            url: '/api/tickets?' + getFilters(page),
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                loadOnAny('.tickets-container', true);
                if (resp.data.length) {
                    renderTickets(resp.data);
                    if (!isMobile()) {
                        $('.ticket-item:first').click();
                    }
                    $('.current-page-text .per-page').text(resp.data.length);
                    $('.current-page-text .total').text(resp.meta.total);
                } else {
                    setEmptyViews();
                }
                paginate('#tickets-pagination', resp.meta);
            }
        });
    }

    function renderTickets(tickets) {
        let html = ``;
        for (let ticket of tickets) {
            html += `<div class="ticket-item" data-id="${ticket.id}">
                         <div class="px-30">
                             <span class="ticket-status-icon ${statusColor[ticket.ticket_status_enum] || 'answered'}"></span>
                         </div>
                         <div class="d-flex flex-column">
                             <div class="customer-name">${ticket.customer_name}</div>
                             <small class="ticket-subject">${ticket.subject}</small>
                             <div class="ticket-last-message">${ticket.description}</div>
                         </div>
                     </div>`;
        }
        $('.tickets-container').html(html);
        $('.pagination-container').show();
    }

    function show(id) {
        if (!isMobile()) {
            loadOnAny('.tickets-grid-right', false, messageLoader);
        }
        $.ajax({
            method: "GET",
            url: '/api/tickets/' + id,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                loadOnAny('.tickets-grid-right', true);
                showTicket(resp.data);
                showMessagesMobile();
            }
        });
    }

    function renderMessage(message) {
        if (message.type === 'text') {
            return `<div class="ticket-message ${typeEnum[message.from]}">
                        <span>${message.content}</span>
                        <div class="ticket-message-date">${message.created_at}</div>
                    </div>`;
        } else if (message.type === 'file') {
            return `<div class="ticket-message file ${typeEnum[message.from]}">
                        <div class="file-download" data-id="${message.id}">
                           <span>${message.content}</span>
                           <i class="material-icons">file_download</i>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="file-extension">${message.content.split('.')[1]}</div>
                            <div class="ticket-message-date">${message.created_at}</div>
                        </div>
                    </div>`;
        }
    }

    function showTicket(ticket) {

        $('#ticket-id').val(ticket.id);
        $('.ticket-customer').text(ticket.customer_name);
        $('.ticket-status .ticket-status-text').text(statusEnum[ticket.ticket_status_enum]);
        $('.ticket-status .ticket-status-icon').removeClass('open closed mediation answered')
            .addClass(statusColor[ticket.ticket_status_enum] || 'answered');
        $('.ticket-category-text').text(categoryEnum[ticket.ticket_category_enum]);
        $('.ticket-start-date').text(ticket.created_at);
        $('.ticket-project').text(ticket.project_name);

        let html = '';
        for (let message of ticket.messages) {
            html += renderMessage(message)

        }
        $('.messages-container').html(html)
            .scrollTop(function () {
                return this.scrollHeight;
            });
        $('.ticket-header *').show();
        $('.write-container').show();
    }

    function clearViews() {
        $('.tickets-container').html('');
        $('.messages-container').html('');
        $('.ticket-header *').hide();
        $('.write-container').hide();
        $('.pagination-container').hide();
    }

    function setEmptyViews() {
        const ticketEmpty = `<div class="tickets-empty">
                               <img src="/modules/global/img/tickets.svg">
                               <h3>Tudo tranquilo por aqui!</h3>
                               <div>Por enquanto, não há nenhum atendimento a ser resolvido. Volte mais tarde!</div>
                             </div>`;
        $('.tickets-container').html(ticketEmpty);

        const messageEmpty = `<div class="messages-empty">
                                <img src="/modules/global/img/chat.svg">
                                <h3>Esse é o seu espaço de chat.</h3>
                                <div>
                                  Para usar o chat e responder seus clientes, basta selecionar algum dos tickets
                                  na aba aqui no lado direito. Nesse espaço, você poderá tirar dúvidas, responder
                                  reclamações e sugestões de seus compradores.
                                </div>
                              </div>`;
        $('.messages-container').html(messageEmpty);
    }

    function getResume() {

        loadOnAny('.number', false, resumeLoader);

        $.ajax({
            method: "GET",
            url: '/api/tickets/getvalues',
            dataType: "json",
            data: {
                date: $("#date_range").val(),
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: resp => {
                loadOnAny('.number', true);
                errorAjaxResponse(resp);
            },
            success: resp => {
                $('#ticket-open .number').html(resp.open);
                let percentual = (100 / resp.total * resp.open) || 0;
                $('#ticket-open .detail').html(`(${(percentual).toFixed(1)}%)`);

                $('#ticket-mediation .number').html(resp.mediation);
                percentual = (100 / resp.total * resp.mediation) || 0;
                $('#ticket-mediation .detail').html(`(${(percentual).toFixed(1)}%)`);

                $('#ticket-closed .number').html(resp.closed);
                percentual = (100 / resp.total * resp.closed) || 0;
                $('#ticket-closed .detail').html(`(${(percentual).toFixed(1)}%)`);

                $('#ticket-total .number').html(resp.total);

                loadOnAny('.number', true);
            }
        });
    }

    // Comportamentos da tela

    // Filters
    $('#project-select, #filter-status').on('change', function () {
        index();
    });

    // Filter Badge
    $('.filter-badge').on('click', function () {
        let btn = $(this);
        if (btn.hasClass('editable')) {
            btn.removeClass('active');
            if (btn.data('value')) {
                btn.data('value', '');
                if (btn.data('original-text')) {
                    btn.text(btn.data('original-text'));
                }
                index();
            } else {
                btn.addClass('focused');
                let target = $(btn.data('target'));
                if (target.length) {
                    $('.filter-badge-input').not(target).removeClass('show');
                    if (target.hasClass('show')) {
                        target.removeClass('show');
                    } else {
                        const inputWidth = 250;
                        const colPadding = 15;
                        let parent = target[0].parentNode;
                        let {left: parentLeft, width: parentWidth} = parent.getBoundingClientRect();
                        let maxLeft = parentWidth - inputWidth - (colPadding * 2);
                        let left = this.getBoundingClientRect().left - parentLeft - colPadding;
                        left = left > maxLeft ? maxLeft : left;
                        target.css('margin-left', left + 'px')
                            .addClass('show');
                        target.find('input').val('').focus();
                    }
                }
            }
        } else if (btn.hasClass('daterange')) {
            btn.data('dateRangePicker')
                .clear()
            btn.removeClass('active')
                .addClass('focused');
        } else {
            btn.toggleClass('active');
            index();
        }

        showTicketsMobile();
    });

    $('.filter-badge-input button').on('click', function () {
        let parent = $(this).parent();
        let parentId = parent.attr('id');
        let input = parent.find('input, select');
        let value = input.val();
        if (value) {
            let badge = $(`[data-target='#${parentId}']`);
            if (input.prop('tagName') === 'SELECT') {
                badge.data('original-text', badge.text());
                badge.text(input.find('option:selected').text());
            }
            badge.data('value', value)
                .addClass('active');

            index();
        }
        parent.removeClass('show');
    });

    $(document).on('click', function (e) {
        let target = $(e.target);

        if (!target.is('.filter-badge-input')) {
            if (!target.is('.filter-badge.editable')
                && !target.parents('.filter-badge-input').length) {
                $('.filter-badge-input').removeClass('show');
            }
            if (!target.is('.filter-badge-input input') && !target.is('.filter-badge-input select')) {
                $('.filter-badge').not(target).removeClass('focused');
            }
        }
    });

    $('#input-document input').on('keyup', function () {
        let masks = ['00.000.000/0000-00', '000.000.000-000'];
        $(this).mask((this.value.length > 14) ? masks[0] : masks[1]);
    });

    // Search
    $('.btn-search').on('click', function () {
        let searchBox = $('.search-box');
        searchBox.toggleClass('show');
        if (searchBox.hasClass('show')) {
            searchBox.find('input').focus();
        }
    });

    $('.search-box button').on('click', function () {
        index();
        $('.search-box').removeClass('show');
    });

    // Ticket item
    $(document).on('click', '.ticket-item', function () {
        let id = $(this).data('id');
        show(id);

        $('.ticket-item').removeClass('active');
        $(this).addClass('active');
    });

    $('.ticket-back').on('click', function () {
        showTicketsMobile();
    });

    function showMessagesMobile() {
        if (isMobile()) {
            $('.tickets-grid-left').css('grid-area', '1 / 2')
            $('.tickets-grid-right').css('grid-area', '1 / 1')
        }
    }

    function showTicketsMobile() {
        if (isMobile()) {
            $('.tickets-grid-left').css('grid-area', '1 / 1')
            $('.tickets-grid-right').css('grid-area', '1 / 2')
        }
    }

    // Attachments
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

    function cleanAttachments() {
        while (attachments2send.length) {
            attachments2send.pop();
        }
        $('.attachments-container').html('').hide();
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

    $(document).on('click', '.file-download', function () {
        let id = $(this).data('id');
        $.ajax({
            method: "GET",
            url: '/api/tickets/file/' + id,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                window.open(resp.url, '_blank');
            }
        });
    });

    // Send Message
    $('#btn-send').on('click', function () {
        let writeArea = $('#write-area');
        if (writeArea.val().length || attachments2send.length) {
            let data = new FormData();
            data.append('ticket_id', $('#ticket-id').val())
            data.append('message', writeArea.val());

            attachments2send.forEach((file, i) => {
                data.append(`attachments[${i}]`, file);
            });

            $.ajax({
                method: "POST",
                url: '/api/tickets/sendmessage',
                data: data,
                contentType: false,
                processData: false,
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: resp => {
                    errorAjaxResponse(resp);
                },
                success: resp => {
                    writeArea.val('');
                    cleanAttachments();
                    let container = $('.messages-container');
                    for (let message of resp) {
                        container.append(renderMessage(message))
                    }
                    container.scrollTop(function () {
                        return this.scrollHeight;
                    });
                }
            });
        }
    });

    // pagination
    function paginate(target, meta) {
        const {current_page: currentPage, last_page: lastPage, per_page: perPage, total} = meta;
        const displayMax = 4;
        const data = [];

        $(target).html('');

        if (perPage >= total) return;
        if (lastPage <= displayMax) {
            for (let i = 1; i <= lastPage; i++) data.push(i)
        } else if (currentPage < displayMax - 1) {
            for (let i = 1; i < displayMax; i++) {
                data.push(i)
            }
            data.push(lastPage)
        } else {
            let prev = currentPage - 1;
            let next = currentPage + 1;
            data.push(1)
            if (prev > 1) data.push(prev)
            data.push(currentPage);
            if (next < lastPage) data.push(next)
            if (currentPage < lastPage) data.push(lastPage)
        }

        data.forEach(page => {
            const button = `<button ${page === currentPage ? 'class="active"' : ''}>${page}</button>`
            $(target).append(button)
        });
    }

    $(document).on('click', '.pagination button:not(.active)', function () {
        $('.pagination button').removeClass('active');
        $(this).addClass('active')
        const page = parseInt($(this).text())
        index(page);
    });

    function isMobile() {
        return window.innerWidth < 768;
    }

    // third party

    $('#filter-date').dateRangePicker({
        setValue: function (s) {
            if (s) {
                let normalize = s.replace(/(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/, "$120$2-$320$4");
                $(this).html(s).data('value', normalize);
            } else {
                $(this).html('Selecionar').data('value', '');
            }
        }
    }).on('datepicker-close', function () {
        if ($(this).data('value')) {
            $(this).addClass('active')
        }
        $(this).removeClass('focused');
        index();
    });

    const picker = new EmojiButton();
    picker.on('emoji', emoji => {
        let writeArea = document.querySelector('#write-area');
        writeArea.value += emoji;
        setTimeout(function () {
            writeArea.focus();
        }, 100);
    });
    $('#btn-emoji').on('click', function () {
        picker.togglePicker(this);
    });

    $('.filter-container').slick({
        infinite: false,
        speed: 300,
        slidesToShow: 10,
        variableWidth: true,
        nextArrow: false,
        prevArrow: false,
        responsive: [
            {
                breakpoint: 1584,
                settings: {
                    slidesToShow: 9,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll: 5,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll: 4,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2,
                }
            },
        ]
    });
})
