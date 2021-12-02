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
            justifyContent: 'flex-start',
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
            error: resp => {
                loadingOnScreenRemove();
                errorAjaxResponse(resp);
            },
            success: resp => {
                if (resp.data.length) {
                    for (let project of resp.data) {
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

        let plan = $('#filter-plan').data('value') || '';

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
            plan,
            category,
            document,
            name,
            answered,
            period,
            status,
            nameOrDocument,
            page,
        }

        return Object.entries(filters).map(([key, val]) => `${key}=${val}`).join('&');
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
                        show(resp.data[0].id);
                        $('.ticket-item').removeClass('active');
                        $('.ticket-item:first').addClass('active');
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
                        <a download="${message.content}"  href="${message.link}" target="_blank" class="file-download">
                           <span>${message.content}</span>
                           <i class="material-icons">file_download</i>
                        </a>
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
        $('.ticket-header *').show();

        let html = '';
        if (isMobile()) {
            html += `<div class="ticket-messages-resume"><b>${categoryEnum[ticket.ticket_category_enum]}</b> aberta em ${ticket.created_at} para <b>${ticket.project_name}</b></div>`;
        }
        for (let message of ticket.messages) {
            html += renderMessage(message);
        }

        $('.messages-container').html(html)
            .scrollTop(function () {
                return this.scrollHeight;
            });

        if (statusColor[ticket.ticket_status_enum] === 'closed') {
            $('.write-container .inputs-container').children().hide();
            $('#btn-send').hide();
            $('.ticket-closed-info').show();
        } else {
            $('.write-container .inputs-container').children().show();
            $('#btn-send').show();
            $('.ticket-closed-info').hide();
        }

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

        $('#ticket-open .detail').html('');
        $('#ticket-mediation .detail').html('');
        $('#ticket-closed .detail').html('');

        loadOnAny('.number', false, resumeLoader);

        $.ajax({
            method: "GET",
            url: '/api/tickets/getvalues?project=' + $('#project-select').val(),
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

    // Project Select
    $('#project-select').on('change', function () {
        $('#plan-select').clear();
        index();
        getResume();
    });

    // Filters
    $('#filter-status').on('change', function () {
        index();
    });

    // Filter Badge
    $('.filter-badge').on('click', function (e) {
        let btn = $(this);
        if (btn.hasClass('editable')) {
            let target = $(btn.data('target'));

            let closeClick = btn.hasClass('active') && e.offsetX >= (this.offsetWidth - 32);
            if (closeClick) {
                btn.removeClass('active')
                    .removeClass('focused');
                btn.text(btn.data('original-text'));
                btn.data('value', '');
                if (target.length) {
                    target.removeClass('show').find('input').val('');
                    if(btn.hasClass('dropdown')) {
                        target.find('select').val('').clear();
                    }
                    index();
                }

                if (btn.hasClass('daterange')) {
                    btn.data('dateRangePicker').clear();
                    btn.data('dateRangePicker').close();
                    index();
                    e.stopImmediatePropagation();
                }
            } else {

                if (!btn.hasClass('active')) {
                    btn.addClass('focused');
                }

                if (target.length) {
                    $('.filter-badge-input').not(target).removeClass('show');
                    const inputWidth = target.width();
                    const colPadding = 55;
                    let parent = target[0].parentNode;
                    let {left: parentLeft, width: parentWidth} = parent.getBoundingClientRect();
                    let maxLeft = parentWidth - inputWidth - colPadding;
                    let left = this.getBoundingClientRect().left - parentLeft;
                    left = left > maxLeft ? maxLeft : left;
                    left = left < 0 ? 0 : left;
                    target.css('margin-left', left + 'px')
                        .addClass('show');
                    target.find('input').focus();
                }
            }
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

    $(document).on('click', '.filter-badge-input .select3-option', function () {
        let parent = $(this).parents('.filter-badge-input');
        let parentId = parent.attr('id');
        let badge = $(`[data-target='#${parentId}']`);
        let value = $(this).data('value');
        if (value) {
            badge.addClass('active');
        } else {
            badge.removeClass('active');
        }
        badge.data('value', value)
            .addClass('active');
        index();
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
            $('.tickets-grid-left').css('grid-area', '1 / 2');
            $('.tickets-grid-right').css('grid-area', '1 / 1');
            $('.search-box').removeClass('show');
        }
    }

    function showTicketsMobile() {
        if (isMobile()) {
            $('.tickets-grid-left').css('grid-area', '1 / 1');
            $('.tickets-grid-right').css('grid-area', '1 / 2');
        }
    }

    $(window).resize(function () {
        if(!isMobile()) {
            $('.tickets-grid-left').css('grid-area', '1 / 1');
            $('.tickets-grid-right').css('grid-area', '1 / 2');
        }
    });

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

    // Send Message
    function sendMessage() {
        $('#btn-send').prop('disabled', true);
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
                    $('#btn-send').prop('disabled', false);
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
                    $('#btn-send').prop('disabled', false);
                }
            });
        }
    }

    $('#btn-send').on('click', function () {
        sendMessage();
    });

    $('#write-area').on('keypress', function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault()
            sendMessage()
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

    $('#plan-select').select3({
        placeholder: 'Selecione o plano',
        language: {
            noResults: 'Nenhum plano encontrado',
            searching: 'Procurando...',
            loadingMore: 'Carregando mais planos...'
        },
        ajax: {
            url: '/api/plans/user-plans',
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: params => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    project_id: $('#project-select').val()
                }
            },
            processResults: res => {
                return {
                    results: $.map(res.data, function (obj) {
                        return {
                            id: obj.id,
                            text: obj.name + (obj.description ? " - " + obj.description : ""),
                        };
                    }),
                    pagination: {
                        more: res.meta.current_page < res.meta.last_page
                    }
                }
            }
        }
    });

    $('#filter-date').dateRangePicker({
        setValue: function (s) {
            if (s) {
                let normalize = s.replace(/(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/, "$120$2-$320$4");
                $(this).html(s).data('value', normalize);
            } else {
                $(this).html('Selecionar').data('value', '');
            }
        }
    }).on('datepicker-change', function () {
        index();
    }).on('datepicker-open', function () {
        $('.filter-badge-input').removeClass('show');
    }).on('datepicker-close', function () {
        $(this).removeClass('focused');
        if ($(this).data('value')) {
            $(this).addClass('active');
        }
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

    $('.filter-container').on({
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
    $(document).on('mousemove', '.filter-container.scrolling', function (e) {
        const dx = e.clientX - $(this).data('x');
        this.scrollLeft = $(this).data('left') - dx;
    });
});
