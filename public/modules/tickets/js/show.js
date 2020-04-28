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
        loadOnAny('.page');

        $.ajax({
            method: "GET",
            url: '/api/tickets/' + ticketId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('.page', true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadOnAny('.page', true);
                $('.card').addClass(`${cardColorByStatus[response.data.ticket_status_enum]}`);
                $('.ticket-subject').html(`${response.data.subject}`);
                $('.ticket-description').html(`<b>Descrição:</b> ${response.data.description}`);
                $('.customer-name').html(`<b>Cliente:</b> ${response.data.customer_name}`);
                $('.ticket-informations').html(`<b>Empresa</b>: ${response.data.company_name} | <b>Motivo:</b> ${response.data.ticket_category} | <b>Aberto em:</b> ${response.data.created_at} | <b>Última resposta em:</b> ${response.data.last_message}`);
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
                    // let adminSrcImage = $('.img-user-menu-principal').attr('src');
                    let foxSrcImage = $('.navbar-brand-logo').attr('src');

                    for (let ticketMessage of response.data.messages) {
                        let data = '';
                        data = `
                        <div class="d-flex flex-row mb-10">
                                <img ${ticketMessage.from_admin == 1 ? `src="${response.data.project_logo}"` : ticketMessage.from_system ? `src="${foxSrcImage}"`: `src="https://ui-avatars.com/api/?name=${customerNameSplit[0]}+${customerNameSplit[1]}&background=0D8ABC&color=fff&bold=true"`}
                                style='height:50px;width:50px;' class="img-fluid rounded-circle ${ticketMessage.from_system ? 'bg-dark' : ''}">
                            <div class="ml-15">
                                <span class='font-weight-bold'>${ticketMessage.from_admin == 1 ? response.data.project_name : ticketMessage.from_system ? 'CloudFox': response.data.customer_name}</span>
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

                if(response.data.ticket_status_enum === 2){
                    $('#btn-answer').hide();
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
