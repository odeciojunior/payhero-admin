$("#password").keyup(function (e) {
    let strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
    let mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");

    let enoughRegex = new RegExp("(?=.{8,}).*", "g");
    let passwordStrongth = strongRegex.test($(this).val());
    let passwordMedium = mediumRegex.test($(this).val());

    let score = 0;
    if (false === enoughRegex.test($(this).val())) {
        $('#text-password').html('Mais caracteres');
        $("#number-count-correct").css('display', 'none');
        $("#number-count-incorrect").css('display', 'block');

        score = 0;

    } else if (passwordStrongth) {
        $('#text-password').html('forte!');
        $("#number-count-incorrect").css('display', 'none');
        $("#number-count-correct").css('display', 'block');

        score = 100;

    } else if (passwordMedium) {

        $('#text-password').html('boa!');
        $("#number-count-correct").css('display', 'block');
        $("#number-count-incorrect").css('display', 'none');

        score = 50;
    } else {
        $('#text-password').html('fraca');
        $("#number-count-correct").css('display', 'block');
        $("#number-count-incorrect").css('display', 'none');

        score = 25;
    }

    if($("#password").val().replace(/[^0-9]/g,'').length > 0){
        $("#length-correct").show();
        $("#length-incorrect").hide();
    }
    else{
        $("#length-correct").hide();
        $("#length-incorrect").show();
    }

    let widt = 0 + '%';
    let color = '';

    if (score === 100) {
        widt = 100 + '%';
        color = 'green'

    } else if (score === 50) {
        widt = 50 + '%';
        color = 'yellow';

    } else if (score === 25) {
        widt = 25 + '%';
        color = 'red';

    } else {
        widt = 0 + '%';
    }

    $('#progress-password').css({'background': color});
    $("#progress-password").width(widt);

    return true;
});




