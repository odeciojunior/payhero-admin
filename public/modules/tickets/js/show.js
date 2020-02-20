$(document).ready(function () {
    let ticketId = $(window.location.pathname.split('/')).get(-1);
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
    getTicket();

    function getTicket() {
        loadOnAny('.page-content');

        $.ajax({
            method: "GET",
            url: '/api/tickets/' + ticketId,
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
                $('.card').addClass(`${cardColorByStatus[response.data.ticket_status_enum]}`);
                $('.ticket-subject').html(`${response.data.subject}`);
                $('.customer-name').html(`Cliente: ${response.data.customer_name}`);
                $('.ticket-informations').html(`Empresa: ${response.data.company_name} | Motivo: ${response.data.ticket_category} | Aberto em: ${response.data.created_at} | Última resposta em: ${response.data.last_message}`);
                $('.page-title').html(`<i class="material-icons turn-back" style='color:grey;cursor:pointer;' title='Voltar'>arrow_back</i> Chamado: ${response.data.id}`);
                $('.company-name').html(`${response.data.company_name}`);
                $('.total-value').html(`${response.data.total_paid_value}`);
                $('.sale-code').html(`${response.data.sale_code}`);
                $('.ticket-status').html(`${response.data.ticket_status}`);
                $('.ticket-status').addClass(`${letterColorByStatus[response.data.ticket_status_enum]}`);
                $('.ticket-products').html('');

                for (let product of response.data.products) {
                    $('.ticket-products').append(`${product.name}<br>`);
                }

                //Monta a div de comentários
                $("#div-ticket-comments").html('');
                if (!isEmpty(response.data.messages)) {
                    let customerNameSplit = response.data.customer_name.split(' ');
                    let adminSrcImage = $('.img-user-menu-principal').attr('src');

                    for (let ticketMessage of response.data.messages) {
                        let data = '';
                        data = `
                        <div class="d-flex flex-row mb-10">
                            <div class="p-2 bd-highlight">
                                <img ${ticketMessage.from_admin == 1 ? `src="${adminSrcImage}"` : `src="https://ui-avatars.com/api/?name=${customerNameSplit[0]}+${customerNameSplit[1]}&background=0D8ABC&color=fff&bold=true"`}
                                style='height:50px;width:50px;' class="img-fluid rounded-circle">
                            </div>
                            <div class="p-2">
                                <span class='font-weight-bold'>${ticketMessage.from_admin == 1 ? ticketMessage.admin_name : response.data.customer_name}</span>
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

                if (response.data.ticket_status == 'Resolvido') {
                    $('#btn-solve').hide();
                }
            }

        });
    }

    $('#btn-answer').on('click', function (event) {
        event.preventDefault();
        $(".div-message").slideDown();
    });

    $('#btn-cancel').on('click', function (event) {
        event.preventDefault();
        $(".div-message").slideUp();
    });

    $(document).on('click', '.turn-back', function () {
        let locationUrl = window.location.protocol + "//" + window.location.hostname + '/attendance';
        window.location.replace(locationUrl);
    });

    $('#btn-solve').on('click', function (event) {
        event.preventDefault();
        let status = $(this).data('status');
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
                getTicket();
                $('#btn-solve').hide();
                alertCustom("success", "Chamado marcado como resolvido");
            }
        });
    });

    $('#btn-send').on('click', function (event) {
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
                getTicket();
                $('.user-message').val('');
            }

        });
    });

});
