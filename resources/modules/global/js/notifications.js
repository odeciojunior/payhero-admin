$(document).ready(function () {

    updateUnreadNotificationsAmount();

    var pusher = new Pusher('ee4529bae28bb85defaf', {
        cluster: 'us2',
        forceTLS: true
    });

    Pusher.logToConsole = false;

    var channel = pusher.subscribe('channel-' + $("#user").val());

    channel.bind('new-notification', function (data) {
        alertCustom('success', data.message);
        updateUnreadNotificationsAmount();
    });

    $("#notification").on('click', function () {
        getNotifications();
        //updateUnreadNotificationsAmount();
    });

    // autaliza status das notificações para lidas
    function markNotificationsAsRead() {
        $.ajax({
            method: 'POST',
            url: '/api/notifications/markasread',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function () {
                //
            },
            success: function (response) {
                updateUnreadNotificationsAmount();
            }
        });
    }

    //verifica se existem novas notificações
    function updateUnreadNotificationsAmount() {
        $.ajax({
            method: 'GET',
            url: '/api/notifications/unreadamount',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function () {
                //
            },
            success: function (response) {
                // $("#notification-amount").html(response.qtd_notification);
                if (parseInt(response.qtd_notification) > 0) {
                    $("#notification-amount").removeClass("badge-notification-false");
                    $("#notification-amount").addClass("badge-notification");
                    $("#notification-amount").text(response.qtd_notification);
                }else {
                    $("#notification-amount").removeClass("badge-notification");
                    $("#notification-amount").addClass("badge-notification-false");
                    $("#notification-amount").text();

                }

                $('#notificationBadge').html('New ' + response.qtd_notification)
            }
        });
    }

    // monta html com as notificações
    function getNotifications() {
        $("#notificationTemplate").css({'height': '150px'});
        loadOnNotification('#notificationTemplate');
        $.ajax({
            method: 'GET',
            url: '/api/notifications/unread',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function () {
                //
            },
            success: function (response) {
                $("#notificationTemplate").html('');
                $("#notificationTemplate").css({'height': '300px', 'overflow-y': 'scroll'});
                if($(response.data).length>0){
                    $(response.data).each(function(index, data){
                        $("#notificationTemplate").append(notificationTemplate(data));
                    });
                }else{
                    $('#notificationTemplate').html("<div style='vertical-align middle;max-height:100px;padding-top:12%' class='text-center'><img src='" +
                    $("#notificationTemplate").attr("img-empty") +
                    "'><br> Nenhuma notificação por aqui.</div>");
                }
                markNotificationsAsRead();
            }
        });
    }

    // notification template
    function notificationTemplate(data){

        data = getNotificationData(data);

        return `<a class="list-group-item dropdown-item item-notification d-flex flex-row justify-content-around align-self-center" href=` + data.link + ` role="menuitem" id='item-notification' style='` + data.background + `'>

                        <div class="mr-10 icon-notification d-flex justify-content-center align-self-center align-items-center" style='` + data.iconBackgroundColor + `'>
                            <span class='` + data.iconClass + `' style='` + data.iconColor + `'></span>
                        </div>
                        <div class="media-body description-notification">
                            <h6 class="media-heading" style='white-space:normal'>
                                ` + data.message + `
                            </h6>
                            <time class="media-meta"> ` + data.time + `</time>
                        </div>
                </a>`;
    }

    // prepare data to create a template
    function getNotificationData(data){
        var message = '', iconClass = '', iconColor = '', iconBackgroundColor = '', link = '';
        switch (data.type) {
            case 'BoletoCompensatedNotification' :
                message   = data.message + (data.message > 1 ? ' boletos compensados' : ' boleto compensado');
                iconClass = 'o-currency-1';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/sales';
                break;
            case 'DomainApprovedNotification' :
                message   = data.message;
                iconClass = 'o-clouds-1'; // 'cloud-success';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/projects';
                break;
            case 'ReleasedBalanceNotification' :
                message   = data.message;
                iconClass = 'o-money-bag-1';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/finances';
                break;
            case 'SaleNotification' :
                message   = data.message + (data.message > 1 ? ' novas vendas' : ' nova venda');
                iconClass = 'o-checkout-cart-1';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/sales';
                break;
            case 'ShopifyIntegrationReadyNotification' :
                message   = data.message;
                iconClass = 'o-checked-circle-1';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/projects';
                break;
            case 'UserShopifyIntegrationStoreNotification' :
                message   = data.message;
                iconClass = 'o-checked-circle-1';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/projects';
                break;
            case 'WithdrawalApprovedNotification' :
                message   = data.message;
                iconClass = 'o-cash-dispenser-1';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/finances';
                break;
            case 'TrackingsImportedNotification':
                message   = data.message;
                iconClass = 'o-config-1'; // 'tracking-success';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/trackings';
                break;
            case 'SalesExportedNotification':
                message   = 'Exportação do relatório de vendas concluída.';
                iconClass = 'o-sales-up-1';
                iconColor = 'color:#2E85EC;';
                iconBackgroundColor = 'background: #D5F6FF;';
                link      = '/sales/download/' + data.message;
                break;
            case 'TrackingsExportedNotification':
                message   = 'Exportação do relatório de códigos de rastreio concluída.';
                iconClass = 'o-config-1'; // 'tracking-success';
                iconColor = 'color:#5EE2A1;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/trackings/download/' + data.message;
                break;
            case 'WithdrawalBlockedNotification':
                message   = 'O saque está bloqueado. Entre em contato com o suporte para mais informações.';
                iconClass = 'o-money-bag-1';
                iconColor = 'color:#f41c1c;';
                iconBackgroundColor = 'background: #E2FFF1;';
                link      = '/finances';
            default:
                break;
        }

        if(data.read == 1){
            var backgroundColor = '';
        }
        else{
            var backgroundColor = 'background-color:#F2FFF9';
        }

        return { message: message, iconClass: iconClass, iconColor: iconColor, iconBackgroundColor: iconBackgroundColor, link: link, time: data.time, background: backgroundColor};
    }
});
