$(document).ready(function () {

    updateUnreadNotificationsAmount();

    var pusher = new Pusher('339254dee7e0c0a31840', {
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
        updateUnreadNotificationsAmount();
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
                //
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
                $("#notification-amount").html(response.qtd_notification);
                $('#notificationBadge').html('New ' + response.qtd_notification)
            }
        });
    }

    // monta html com as notificações
    function getNotifications() {
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
                $("#notificationTemplate").css({'height': '250px', 'overflow-y': 'scroll'});
                $(response.data).each(function(index, data){
                    $("#notificationTemplate").append(notificationTemplate(data));
                });
                markNotificationsAsRead();
            }
        });
    }

    // notification template
    function notificationTemplate(data){

        data = getNotificationData(data);

        return `<a class="list-group-item dropdown-item" href=` + data.link + ` role="menuitem" id='item-notification' style='width:100%;` + data.background + `'>
                    <div class="media">
                        <div class="pr-10" style='margin:auto'>
                            <span class='` + data.iconClass + `'></span>
                        </div>
                        <div class="media-body">
                            <h6 class="media-heading" style='white-space:normal'>
                                ` + data.message + `
                            </h6>
                            <time class="media-meta">` + data.date + `</time>
                        </div>
                    </div>
                </a>`;
    }

    // prepare data to create a template
    function getNotificationData(data){
        var message = '', iconClass = '', link = '';
        switch (data.type) {
            case 'BoletoCompensatedNotification' :
                message   = data.message + (data.message > 1 ? ' boletos compensados' : ' boleto compensado');
                iconClass = 'money-success';
                link      = '/sales';
                break;
            case 'DomainApprovedNotification' :
                message   = data.message;
                iconClass = 'cloud-success';
                link      = '/projects';
                break;
            case 'ReleasedBalanceNotification' :
                message   = data.message;
                iconClass = 'money-success';
                link      = '/finances';
                break;
            case 'SaleNotification' :
                message   = data.message + (data.message > 1 ? ' novas vendas' : ' nova venda');
                iconClass = 'money-success';
                link      = '/sales';
                break;
            case 'ShopifyIntegrationReadyNotification' :
                message   = data.message;
                iconClass = 'shopify-success';
                link      = '/projects';
                break;
            case 'UserShopifyIntegrationStoreNotification' :
                message   = data.message;
                iconClass = 'shopify-success';
                link      = '/projects';
                break;
            case 'WithdrawalApprovedNotification' :
                message   = data.message;
                iconClass = 'money-success';
                link      = '/finances';
                break;
            case 'TrackingsImportedNotification':
                message   = data.message;
                iconClass = 'tracking-success';
                link      = '/trackings';
                break;
            case 'SalesExportedNotification':
                message   = 'Exportação do relatório de vendas concluída.';
                iconClass = 'money-success';
                link      = '/sales/download/' + data.message;
                break;
            case 'TrackingsExportedNotification':
                message   = 'Exportação do relatório de códigos de rastreio concluída.';
                iconClass = 'tracking-success';
                link      = '/trackings/download/' + data.message;
                break;
            default:
                break;
        }

        if(data.read == 1){
            var backgroundColor = '';
        }
        else{
            var backgroundColor = 'background-color:#b5e0ee5e';
        }

        return { message: message, iconClass: iconClass, link: link, date: data.date, background: backgroundColor};
    }

});
