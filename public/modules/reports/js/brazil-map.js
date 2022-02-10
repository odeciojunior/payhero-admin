$(function() {
    $('.estado').on('click', function(e){
        e.preventDefault();
        $('a').removeClass('state-choose');
        $(this).addClass('state-choose');
        $('.list-states').hide();
        $('.inside-state').show();

        $('.name-state').text($(this).attr('rel'));
    });

    $('.back-list').on('click', function(e){
        e.preventDefault();
        $('.list-states').show();
        $('.inside-state').hide();
        $('a').removeClass('state-choose');
    });
});