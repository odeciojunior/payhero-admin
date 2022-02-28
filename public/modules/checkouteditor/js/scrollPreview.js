$(() => {
    $("body,html").scroll(function () {
        
        if ($("#tab-checkout").hasClass("active")) {
                    var scrollWindow = $(this).scrollTop();
                    var topVisual = $("#checkout_type").position().top;
                    var topPayment = $("#payment_container").position().top - 200;
                    var topPostPurchase = $("#post_purchase").position().top - 350;
            
                    if (scrollWindow > topVisual) {
                        var marginTop = scrollWindow - topVisual;
                        $("#preview_div").css("margin-top", marginTop + 10);
                    } else {
                        $("#preview_div").css("margin-top", 0);
                    }
            
                    if (scrollWindow < topPayment) {
                        $("#preview_payment").fadeOut("slow", "swing");
                        $("#preview_post_purchase").fadeOut("slow", "swing");
                        $("#preview_visual").fadeIn("slow", "swing");
                    } else if (scrollWindow > topPayment && scrollWindow < topPostPurchase) {
                        $("#preview_visual").fadeOut("slow", "swing");
                        $("#preview_post_purchase").fadeOut("slow", "swing");
                        $("#preview_payment").fadeIn("slow", "swing");
                    } else if (scrollWindow > topPostPurchase) {
                        $("#preview_visual").fadeOut("slow", "swing");
                        $("#preview_payment").fadeOut("slow", "swing");
                        $("#preview_post_purchase").fadeIn("slow", "swing");
                    }
                }
    });
});
