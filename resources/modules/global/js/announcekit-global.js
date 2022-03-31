$(document).ready(function() {
    const user_id = $("#user_hash").val();
    const user_name = $("#user_name").val();
    const user_email = $("#user_email").val();

    window.announcekit = (window.announcekit || { queue: [], on: function(n, x) { 
        window.announcekit.queue.push([n, x]); }, push: function(x) { window.announcekit.queue.push(x); } 
    });

    announcekit.push({
        "widget": "https://updates.cloudfox.net/widgets/v2/2gTmDK",
        "selector": ".announcekit-widget",
        user: {
            id: user_id,
            name: user_name,
            email: user_email,
        }
    });
})