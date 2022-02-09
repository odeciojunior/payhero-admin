$(function() {
    $('.estado').on('click', function(e){
        e.preventDefault();
        $('a').removeClass('state-choose');
        $(this).addClass('state-choose');
        $('.list-states').hide();

        $('.name-state').text($(this).attr('rel'));
    });    
});