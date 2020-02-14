$(document).ready(function () {
    let locationUrl = window.location.href;
    let cardColorByStatus = {
        1: 'orange',
        2: 'green',
        3: 'red',
    };
    let letterColorByStatus = {
        1: 'orange-gradient',
        2: 'green-gradient',
        3: 'red-gradient',
    };
    getTickets();
    getTotalValues();

    $("#btn-filter").on("click", function (event) {
        event.preventDefault();
        getTickets();
    });

    function getTickets() {
        loadOnAny('.page-content');

        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/tickets?' + 'status=' + $("#status-filter").val() + '&customer=' + $("#customer-filter").val() + '&ticket_id=' + $("#ticker-code-filter").val();
        } else {
            link = '/api/tickets/' + link + '&status=' + $("#status-filter").val() + '&customer=' + $("#customer-filter").val() + '&ticket_id=' + $("#ticker-code-filter").val();
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
                                <div class="card card-shadow bg-white card-left ${cardColorByStatus[ticket.ticket_status_enum]}">
                                    <div class="card-header bg-white p-20 pb-0">
                                        <i class="material-icons mr-1">chat_bubble_outline</i>
                                        <span id='' class='font-size-18 font-weight-bold'>${ticket.subject}</span>
                                        <div class='float-right'>
                                            <div class='dropdown'>
                                                <i class="material-icons" id="dropdownMenuButton" title='Opções' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style='cursor:pointer'>more_vert</i>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item details" href="${locationUrl}/${ticket.id}">Detalhes</a>
                                                    ${ticket.ticket_status != 'Resolvido' ? `<a class="dropdown-item solve" href="#" data-status="closed" data-ticket="${ticket.id}">Resolver</a` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body font-size-12 bg-white">
                                        <div class='row'>
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
                                                <span class='font-weight-bold'>${ticket.id}</span>
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
                                                <span class='font-size-12 ${letterColorByStatus[ticket.ticket_status_enum]} mt-20'>${ticket.ticket_status}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        $("#div-tickets").append(data);
                    }
                    pagination(response, 'tickets', getTickets);

                } else {
                    $("#div-tickets").hide();
                    $("#div-ticket-empty").show();
                }
            }
        });
    }
    function getTotalValues() {
        $.ajax({
            method: "GET",
            url: '/api/tickets/getvalues',
            dataType: "json",
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
    $(document).on('click', '.solve', function (event) {
        event.preventDefault();
        let status = $(this).data('status');
        let ticketId = $(this).data('ticket');
        $.ajax({
            method: "PUT",
            url: '/api/tickets/' + ticketId,
            dataType: "json",
            data: {
                ticket_id: ticketId,
                status: status,
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                getTickets();
                alertCustom("success", "Chamado marcado como resolvido");
            }
        });
    });
});
