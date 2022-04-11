$(function() {

    $("#map-filter").on("change", function(){
        $('.back-list').trigger('click');
        loadBrazilMap();
    });

    $('input[name="daterange"]').on("change", function() {
        $('.back-list').trigger('click');
        loadBrazilMap();
    });

    $("#select_projects").on("change", function () {
        $('.back-list').trigger('click');
        loadBrazilMap();
    });

    $('.state').on('click', function(e){

        e.preventDefault();

        if(!$($('#' + $(this).attr('id') + '-position')).length) {
            return;
        }

        $('a').removeClass('state-choose');
        $(this).addClass('state-choose');
        $('#list-states').hide();
        $('#inside-state').show();
        $('.name-state').text($(this).attr('rel'));
        $('#state-position').text($('#' + $(this).attr('id') + '-position').text());

        $.ajax({
            method: "GET",
            url: "http://dev.sirius.com/api/reports/marketing/state-details?state=" + $(this).children('text').text() + "&project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {

            },
            success: function success(response) {
                $('#state-total-value').html(response.data.total_value);
                $('#state-sales-amount').html(response.data.total_sales);
                $('#state-accesses').html(response.data.accesses);
                $('#state-conversion').html(response.data.conversion);
            }
        });
    });

    $('.back-list').on('click', function(e){
        e.preventDefault();
        $('#list-states').show();
        $('#inside-state').hide();
        $('a').removeClass('state-choose');
    });

    function loadBrazilMap() {  

        $.ajax({
            method: "GET",
            url: "http://dev.sirius.com/api/reports/marketing/sales-by-state?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val() + "&map_filter="+ $("#map-filter").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
    
            },
            success: function success(response) {

                $('.state path').css({ fill: '#FFFFFF' });
                $('.state text').css({ fill: '#6C757D' });

                $("#list-states").html('');

                let maxValue = null;
                $.each(response.data, function(i, data){
                    if(maxValue == null) {
                        if($("#map-filter").val() == 'density'){
                            maxValue = data.percentage;
                        }
                        else{
                            maxValue = onlyNumbers(data.value);
                        }
                    }

                    if($("#map-filter").val() == 'density'){
                        setCustomMapCss('#state-' + data.state, maxValue, data.percentage);
                    }
                    else {
                        setCustomMapCss('#state-' + data.state, maxValue, onlyNumbers(data.value));
                    }
                    appendStateDataToList(data, i + 1);

                });
            }
        });
    }

    function setCustomMapCss(selector, maxValue, value) {

        if(maxValue == value) {
            $(selector + ' path').css({ fill: '#15034C' });
            $(selector + ' text').css({ fill: '#FFFFFF' });
            return;
        }

        let percentage = (100 * value) / maxValue;

        if(percentage > 0 && percentage <= 12){
            $(selector + ' path').css({ fill: '#F2F8FF' });
            $(selector + ' text').css({ fill: '#3089F2' });
            return;
        }
        else if(percentage > 12 && percentage <= 25) {
            $(selector + ' path').css({ fill: '#BFDCFF' });
            $(selector + ' text').css({ fill: '#3089F2' });
            return;
        }
        else if(percentage > 25 && percentage <= 37) {
            $(selector + ' path').css({ fill: '#A6CFFF' });
            $(selector + ' text').css({ fill: '##1F5DA7' });
            return;
        }
        else if(percentage > 37 && percentage <= 50) {
            $(selector + ' path').css({ fill: '#73B2FF' });
            $(selector + ' text').css({ fill: '#FFFFFF' });
            return;
        }
        else if(percentage > 50 && percentage <= 62) {
            $(selector + ' path').css({ fill: '#59A5FF' });
            $(selector + ' text').css({ fill: '#FFFFFF' });
            return;
        }
        else if(percentage > 62 && percentage <= 75) {
            $(selector + ' path').css({ fill: '#3089F2' });
            $(selector + ' text').css({ fill: '#FFFFFF' });
            return;
        }
        else if(percentage > 75 && percentage <= 99) {
            $(selector + ' path').css({ fill: '#1F5DA7' });
            $(selector + ' text').css({ fill: '#FFFFFF' });
            return;
        }

        $(selector + ' text').css({ fill: '#6C757D' });
    }

    function appendStateDataToList(data, index) {

        let stateData = `
                        <li class="states-list">
                            <div class="d-flex container">
                                <ul>
                                    <li class="item-state">
                                        <dl class="d-flex">
                                            <dd id="state-${data.state}-position">${index}°</dd>
                                            <dd class="dd-state">${data.state}</dd>
                                        </dl>
                                    </li>
                                    <li class="item-state">
                                        <dl class="d-flex justify-content-between">
                                            <dd><span>${data.percentage}</span></dd>
                                            <dd><strong>${data.value}</strong></dd>
                                        </dl>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    `;

        $("#list-states").append(stateData);
    }

});
