$(document).ready(function () {
    const user_id = $("#user_hash").val();
    const user_name = $("#user_name").val();
    const user_email = $("#user_email").val();

    window.announcekit = window.announcekit || {
        queue: [],
        on: function (n, x) {
            window.announcekit.queue.push([n, x]);
        },
        push: function (x) {
            window.announcekit.queue.push(x);
        },
    };

    window.announcekit.push({
        widget: "https://updates.cloudfox.net/widgets/v2/Qkw3C",
        name: "Qkw3C",
        user: {
            id: user_id,
            name: user_name,
            email: user_email,
        },
    });

    $(".announcekit-widget-mobile").on("click", function () {
        announcekit.widget$Qkw3C.open();
    });
});
