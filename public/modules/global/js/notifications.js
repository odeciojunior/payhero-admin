$(document).ready(function () {

    var pusher = new Pusher('339254dee7e0c0a31840', {
        cluster: 'us2',
        forceTLS: true
    });

    Pusher.logToConsole = false;

    var user = $("#user").val();

    var channel = pusher.subscribe('channel-' + user);

    channel.bind('new-notification', function (data) {
        alertCustom('success', data.message);
        updateUnreadNotificationsAmount();
    });

    updateUnreadNotificationsAmount();

    // verifica se existem novas notificações
    function updateUnreadNotificationsAmount() {
        $.ajax({
            method: 'GET',
            url: '/notificacoes/unreadamount/',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                user: user
            },
            error: function () {
                //
            },
            success: function (response) {
                $("#notification-amount").html(response.qtd_notification);
                if (response.qtd_notification > 0) {
                    $("#notificationTemplate").html('');
                    $("#notificationTemplate").css({'height': '150px', 'overflow-y': 'scroll'});
                    updateUnreadNotification();
                } else {
                    htmlNotNotifications();
                }
            }
        });
    }

    function htmlNotNotifications() {
        $("#notificationTemplate").html('');
        dados = '';
        dados += '<div class="d-flex align-items-center">';
        dados += '<span class="">Nenhuma nova notificação</span>';
        dados += '</div>';
        dados += '</div>';
        $("#notificationTemplate").html(dados);
    }

    // monta html com as notificações
    function updateUnreadNotification() {
        $.ajax({
            method: 'GET',
            url: '/notificacoes/unread/',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                //
            },
            success: function (response) {
                $("#notificationTemplate").html('');
                $("#notificationTemplate").html(response.notificacoes);

                $("#item-notification").on('click', function () {
                    updateMarkAsReadNotification();
                });

            }
        });

    }

    // autaliza status das notificações
    function updateMarkAsReadNotification() {
        $.ajax({
            method: 'POST',
            url: '/notificacoes/markasread/',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                //
            },
            success: function (response) {
                //
            }
        });
    }

    /*channel.bind('new-sale', function (data) {
        alertPersonalizado('success', 'Nova venda realizada');
        clear_map_points();
        updateLastSales();
    });*/
});
