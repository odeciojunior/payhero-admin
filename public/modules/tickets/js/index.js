$(document).ready(function () {
    let cardColorByStatus = {
        1: 'orange',
        2: 'green',
        3: '',
    };
    let letterColorByStatus = {
        1: 'orange-gradient',
        2: 'green-gradient',
        3: '',
    };

    let pageCurrent = JSON.parse(getCookie('filterTickets') || '{}').page;
    if (!/\/attendance\/[a-zA-Z0-9]{15}/.test(document.referrer)) {
        deleteCookie('filterTickets');
    }
    let ticketId = '';
    $('#cpf-filter').mask('000.000.000-00');


    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        deleteCookie('filterTickets');
        getTickets();
        getTotalValues();
    });

    $("#pagination-tickets").on('click', function () {
        deleteCookie('filterTickets');
    });

    getProjects();

    function getProjects() {
        loadingOnScreen();

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
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();

                    $.each(response.data, function (i, project) {
                        $("#projeto").append($('<option>', {
                            value: project.id,
                            text: project.name
                        }));
                    });

                    dateRangePicker();
                    getTickets();
                    getTotalValues();
                } else {
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }

                loadingOnScreenRemove();
            }
        });
    }

    function getFilters(urlParams = false) {

        let data = {
            status: $("#status-filter").val(),
            customer: $("#customer-filter").val(),
            cpf: $("#cpf-filter").val(),
            ticket_id: $("#ticker-code-filter").val().replace("#", ""),
            date: $("#date_range").val(),
            category: $("#category-filter").val(),
            answered: $("#answered").val(),
        };

        if (urlParams) {
            let params = "";
            for (let param in data) {
                params += '&' + param + '=' + data[param];
            }
            return encodeURI(params);
        } else {
            return data;
        }
    }

    function getTickets(link = null) {
        loadOnAny('#div-tickets', false, {
            styles: {
                container: {
                    minHeight: '140px',
                },
                loader: {
                    width: '40px',
                    height: '40px',
                    borderWidth: '5px'
                }
            },
            insertBefore: '#div-tickets'
        });
        $('#div-tickets').html('').show();
        $("#div-ticket-empty").hide();

        if (link !== null) {
            pageCurrent = link;
            deleteCookie('filterTickets');
        }

        let cookie = getCookie('filterTickets');
        if (!!cookie) {
            cookie = JSON.parse(cookie);
            $("#status-filter").val(cookie.status);
            $("#customer-filter").val(cookie.customer);
            $("#ticker-code-filter").val(cookie.ticket_id);
            $("#date_range").val(cookie.date);
            $("#category-filter").val(cookie.category);
            $("#answered").val(cookie.answered);
            link = cookie.page;
        }

        if (link == null) {
            link = '/api/tickets?' + getFilters(true).substr(1);
        } else {
            link = '/api/tickets/' + link + getFilters(true);
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('#div-tickets', true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (!isEmpty(response.data)) {
                    for (let ticket of response.data) {
                        let data = `
                           <div class='col-12 col-lg-12'>
                                <div class="card card-shadow bg-white card-left ${ticket.last_message_type_enum == 'from_admin' ? 'blue' : !ticket.admin_answered ? 'red' : 'orange'}">
                                    <div class="card-header bg-white p-20 pb-0">
                                        <i class="material-icons mr-1">chat_bubble_outline</i>
                                        <a class="not-hover ticket-details" data-id='${ticket.id}' href="#"><span class='font-size-18 font-weight-bold'>${ticket.subject}</span></a>
                                        <div class='float-right'>
                                            <div class='dropdown'>
                                                <i class="material-icons" id="dropdownMenuButton" title='Opções' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style='cursor:pointer'>more_vert</i>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item ticket-details" data-id='${ticket.id}' href="#">Detalhes</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body font-size-12 bg-white">
                                        <div class='row'>
                                            <div class='col-12 col-lg-12'>
                                                <div>
                                                    <span>Descrição</span>
                                                </div>
                                               <p class='font-size-12 font-weight-bold mt-3'>${ticket.description}</p>
                                            </div>
                                            <div class='col-6 col-lg-2'>
                                                <div>
                                                    <span>Empresa</span>
                                                </div>
                                                <span class='font-weight-bold'>${ticket.company_name}</span>
                                            </div>
                                            <div class='col-6 col-lg-2'>
                                                <div>
                                                    <span>ID</span>
                                                </div>
                                                <span class='font-weight-bold'>#${ticket.id}</span>
                                            </div>
                                            <div class='col-6 col-lg-2'>
                                                <div>
                                                    <span>Cliente</span>
                                                </div>
                                                <span class='font-weight-bold'>${ticket.customer_name}</span>
                                            </div>
                                            <div class='col-6 col-lg-2'>
                                                <div>
                                                    <span>Motivo</span>
                                                </div>
                                                <span class='font-weight-bold'>${ticket.ticket_category}</span>
                                            </div>
                                            <div class='col-6 col-lg-2'>
                                                <div>
                                                    <span>Aberto em</span>
                                                </div>
                                                <span class='font-weight-bold'>${ticket.created_at}</span>
                                            </div>
                                            <div class='col-6 col-lg-2'>
                                                <div>
                                                    <span>Última resposta</span>
                                                </div>
                                                <span class='font-weight-bold'>${ticket.last_message_date}</span>
                                            </div>
                                            <div class='col-6 col-lg-2 mt-10'>
                                                <span class='font-size-12 ${ticket.last_message_type_enum == 'from_admin' ? 'blue-gradient' : !ticket.admin_answered ? 'red-gradient' : 'orange-gradient'} mt-20'>
                                                    ${ticket.ticket_status}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        $("#div-tickets").append(data);
                    }
                } else {
                    $("#div-tickets").hide();
                    $("#div-ticket-empty").show();
                }
                loadOnAny('#div-tickets', true);
                let filter = {...getFilters(), page: pageCurrent || null};
                setCookie('filterTickets', 1, filter);
                pagination(response, 'tickets', getTickets);
            }
        });
    }
    $(document).on('click', '.ticket-details', function (event) {
        event.preventDefault();
        let id = $(this).data('id');
        ticketId = id;
        $('#modal-title-ticket').html(`Detalhes do Chamado #${ticketId}`);
        ticketShow(id);
    });
    $(document).on('click', '#btn-answer', function (event) {
        event.preventDefault();
        $(".div-message").slideDown();
    });

    $(document).on('click', '#btn-cancel', function (event) {
        event.preventDefault();
        $(".div-message").slideUp();
    });
    $(document).on('click', '#btn-send', function (event) {
        event.preventDefault();
        if ($('.user-message').val() == '') {
            alertCustom("error", "Preencha o campo mensagem");
            return false;
        }
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/tickets/sendmessage',
            dataType: "json",
            data: {
                message: $('.user-message').val(),
                ticket_id: ticketId,
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadingOnScreenRemove();
                $(".div-message").slideUp();
                alertCustom("success", "Mensagem enviada com sucesso");
                ticketShow(ticketId);
                $('.user-message').val('');
            }
        });
    });
    function ticketShow(ticketId) {

        $.ajax({
            method: "GET",
            url: '/api/tickets/' + ticketId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                if ($('.card-ticket-color').hasClass('orange')) {
                    $('.card-ticket-color').removeClass('orange');
                    $('.ticket-status').removeClass('orange-gradient');
                } else if ($('.card-ticket-color').hasClass('green')) {
                    $('.card-ticket-color').removeClass('green');
                    $('.ticket-status').removeClass('green-gradient');
                }
                $('.card-ticket-color').addClass(`${cardColorByStatus[response.data.ticket_status_enum]}`);
                $('.ticket-subject').html(`${response.data.subject}`);
                $('.ticket-description').html(`<b>Descrição:</b> ${response.data.description}`);
                $('.customer-name').html(`<b>Cliente:</b> ${response.data.customer_name}`);
                $('.ticket-informations').html(`<b>Empresa</b>: ${response.data.company_name} | <b>Motivo:</b> ${response.data.ticket_category} | <b>Aberto em:</b> ${response.data.created_at} | <b>Última resposta em:</b> ${response.data.last_message_date}`);
                $('#ticket-id').html(response.data.id);
                $('.company-name').html(`${response.data.company_name}`);
                $('.total-value').html(`${response.data.total_paid_value}`);
                $('.sale-code').html(`${response.data.sale_code}`);
                $('.ticket-status').html(`${response.data.ticket_status}`);
                $('.ticket-status').addClass(`${letterColorByStatus[response.data.ticket_status_enum]}`);
                $('.ticket-products').html('');

                for (let product of response.data.products) {
                    $('.ticket-products').append(`${product.name}<br>`);
                }

                //Monta a div de anexos
                if (!isEmpty(response.data.attachments)) {
                    $("#div-ticket-attachments").html('');
                    for (let attachment of response.data.attachments) {
                        let data = `<div class="mini-card mr-10 my-5 d-inline-block">
                                        <a class="not-hover" target="_blank" href="${attachment.file}">
                                            <i class="material-icons">attach_file</i>
                                            <span>${attachment.id}</span>
                                        </a>
                                    </div>`;

                        $('#div-ticket-attachments').append(data);
                    }
                    $("#div-ticket-attachments").parent().show();
                } else {
                    $("#div-ticket-attachments").parent().hide();
                }

                //Monta a div de comentários
                $("#div-ticket-comments").html('');
                if (!isEmpty(response.data.messages)) {
                    let customerNameSplit = response.data.customer_name.split(' ');
                    let foxSrcImage = $('.navbar-brand-logo').attr('src');

                    for (let ticketMessage of response.data.messages) {
                        let data = '';
                        data = `
                        <div class="d-flex flex-row mb-10">
                                <img ${ticketMessage.type === 'from_admin' ? `src="${response.data.project_logo}"` : ticketMessage.type === 'from_system' ? `src="${foxSrcImage}"` : `src="https://ui-avatars.com/api/?name=${customerNameSplit[0]}+${customerNameSplit[1]}&background=0D8ABC&color=fff&bold=true"`}
                                style='height:50px;width:50px;object-fit:contain;' class="rounded-circle bg-light">
                            <div class="ml-15">
                                <span class='font-weight-bold'>${ticketMessage.type === 'from_admin' ? response.data.project_name : ticketMessage.type === 'from_system' ? 'CloudFox' : response.data.customer_name}</span>
                                <br>
                                <small>${ticketMessage.created_at}</small>
                                <p>${ticketMessage.message}</p>
                            </div>
                        </div>`;

                        $('#div-ticket-comments').append(data);
                    }
                } else {
                    $('#div-ticket-comments').append('<div class="alert alert-info text-center font-size-14">Nenhuma mensagem encontrada</div>');
                }

                if (response.data.ticket_status_enum === 2) {
                    $('#btn-answer').hide();
                } else {
                    $('#btn-answer').show();
                }

                $('#modal-ticket').modal('show');
            }

        });
    }
    function getTotalValues() {
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
            error: (response) => {
                errorAjaxResponse(response);

            },
            success: (response) => {
                $('#ticket-open').html(`${response.total_ticket_open}`);
                $('#ticket-mediation').html(`${response.total_ticket_mediation}`);
                $('#ticket-closed').html(`${response.total_ticket_closed}`);
                $('#ticket-total').html(`${response.total_ticket}`);
            }

        });
    }

    //DatePicker
    function dateRangePicker() {
        let startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
        let endDate = moment().format('YYYY-MM-DD');
        $('#date_range').daterangepicker({
            startDate: moment().subtract(30, 'days'),
            endDate: moment(),
            opens: 'center',
            maxDate: moment().endOf("day"),
            alwaysShowCalendar: true,
            showCustomRangeLabel: 'Customizado',
            autoUpdateInput: true,
            locale: {
                locale: 'pt-br',
                format: 'DD/MM/YYYY',
                applyLabel: "Aplicar",
                cancelLabel: "Limpar",
                fromLabel: 'De',
                toLabel: 'Até',
                customRangeLabel: 'Customizado',
                weekLabel: 'W',
                daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                firstDay: 0
            },
            ranges: {
                'Hoje': [moment(), moment()],
                'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
                'Este mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Vitalício': [moment('2018-01-01 00:00:00'), moment()]
            }
        }, function (start, end) {
            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
        });
    }
    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            deleteCookie('filterTickets');
            getTickets();
            getTotalValues();
        }
    });

    $('.btn-light-1').click(function () {
        var collapse = $('#icon-filtro')
        var text = $('#text-filtro')

        text.fadeOut(10);
        if (collapse.css('transform') == 'matrix(1, 0, 0, 1, 0, 0)' || collapse.css('transform') == 'none') {
            collapse.css('transform', 'rotate(180deg)')
            text.text('Minimizar filtros').fadeIn();
        } else {
            collapse.css('transform', 'rotate(0deg)')
            text.text('Filtros avançados').fadeIn()
        }
    });
});
