$(document).ready(function () {
    let locationUrl = window.location.href;
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

    $('#cpf-filter').mask('000.000.000-00');

    dateRangePicker();
    getTickets();
    getTotalValues();

    $("#btn-filter").on("click", function (event) {
        event.preventDefault();
        deleteCookie('filterTickets');
        getTickets();
        getTotalValues();
    });

    $("#pagination-tickets").on('click', function () {
        deleteCookie('filterTickets');
    });

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
        loadOnAny('.page-content');

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
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);

            },
            success: (response) => {
                loadOnAny('.page-content', true);
                $("#div-tickets").html('');
                $("#div-ticket-empty").hide();
                $("#div-tickets").show();
                let data = '';
                if (!isEmpty(response.data)) {
                    for (let ticket of response.data) {
                        data = `
                           <div class='col-12 col-lg-12'>
                                <div class="card card-shadow bg-white card-left ${ticket.last_message_from == 'admin' ? 'blue' : !ticket.admin_answered ? 'red' : 'orange'}">
                                    <div class="card-header bg-white p-20 pb-0">
                                        <i class="material-icons mr-1">chat_bubble_outline</i>
                                        <a class="not-hover" href="${locationUrl}/${ticket.id}"><span class='font-size-18 font-weight-bold'>${ticket.subject}</span></a>
                                        <div class='float-right'>
                                            <div class='dropdown'>
                                                <i class="material-icons" id="dropdownMenuButton" title='Opções' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style='cursor:pointer'>more_vert</i>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item details" href="${locationUrl}/${ticket.id}">Detalhes</a>
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
                                                <span class='font-weight-bold'>${ticket.last_message}</span>
                                            </div>
                                            <div class='col-6 col-lg-2 mt-10'>
                                                <span class='font-size-12 ${ticket.last_message_from == 'admin' ? 'blue-gradient' : !ticket.admin_answered ? 'red-gradient' : 'orange-gradient'} mt-20'>
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
                let filter = {...getFilters(), page: pageCurrent || null};
                setCookie('filterTickets', 1, filter);
                pagination(response, 'tickets', getTickets);
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
                'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
});
