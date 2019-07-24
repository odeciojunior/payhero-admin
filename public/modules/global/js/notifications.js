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

    $("#notification").on('click', function () {
        $("#notification-amount").html('0');
        $('#notificationBadge').html('New 0');

        updateUnreadNotification();

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
                $('#notificationBadge').html('New ' + response.qtd_notification)
            }
        });
    }

    // monta hmtl quando não tem notificação notificação
    function htmlNotNotifications() {
        $("#notificationTemplate").html('');
        dados = '';
        dados += '<a class="list-group-item dropdown-item" role="menuitem">';
        dados += '<div class="media">';
        dados += '<div class="pr-10">';
        dados += '<i class="icon wb-chat bg-orange-600 white icon-circle" aria-hidden="true"></i>';
        dados += '</div>';
        dados += '<div class="media-body">';
        dados += '<h6 class="media-heading">Nenhuma nova notificação</h6>';
        dados += '</div>';
        dados += '</div>';
        dados += '</a>';
        $("#notificationTemplate").html(dados);
    }

    // monta html com as notificações
    function updateUnreadNotification() {
        loadOnNotification('#notificationTemplate');
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
                $("#notificationTemplate").css({'height': '150px', 'overflow-y': 'scroll'});
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
});
