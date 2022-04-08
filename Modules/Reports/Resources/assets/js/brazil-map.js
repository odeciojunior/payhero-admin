$(function() {

    loadBrazilMap();

    $("#map-filter").on("change", function(){
        loadBrazilMap();
    });

    $('input[name="daterange"]').on("change", function() {
        loadBrazilMap();
    })

    $('.estado').on('click', function(e){
        e.preventDefault();
        $('a').removeClass('state-choose');
        $(this).addClass('state-choose');
        $('#list-states').hide();
        $('.inside-state').show();

        $('.name-state').text($(this).attr('rel'));
    });

    $('.back-list').on('click', function(e){
        e.preventDefault();
        $('#list-states').show();
        $('.inside-state').hide();
        $('a').removeClass('state-choose');
    });

    function loadBrazilMap() {  

        $.ajax({
            method: "GET",
            url: "http://dev.sirius.com/api/reports/marketing/sales-by-state?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val() + "&map_filter="+ $("#map-filter").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
    
            },
            success: function success(response) {

                $("#list-states").html("");

                let maxValue = null;
                $.each(response.data, function(i, data){
                    if(maxValue == null) {
                        if($("#map-filter").val() == 'density'){
                            maxValue = onlyNumbers(data.percentage);
                        }
                        else{
                            maxValue = onlyNumbers(data.value);
                        }
                    }

                    if($("#map-filter").val() == 'density'){
                        $('#state-' + data.state + ' path').css({ fill: getStateBackgroundCollor(maxValue, onlyNumbers(data.percentage)) });
                    }
                    else {
                        $('#state-' + data.state + ' path').css({ fill: getStateBackgroundCollor(maxValue, onlyNumbers(data.value)) });
                    }
                    appendStateDataToList(data, i + 1);
                });
            }
        });
    }

    function getStateBackgroundCollor(maxValue, value) {

        if(maxValue == value) {
            return '#15034C';
        }

        let percentage = (100 * value) / maxValue;

        if(percentage > 0 && percentage <= 12){
            return '#F2F8FF'
        }
        else if(percentage > 12 && percentage <= 25) {
            return '#BFDCFF';
        }
        else if(percentage > 25 && percentage <= 37) {
            return '#A6CFFF';
        }
        else if(percentage > 37 && percentage <= 50) {
            return '#73B2FF';
        }
        else if(percentage > 50 && percentage <= 62) {
            return '#59A5FF';
        }
        else if(percentage > 62 && percentage <= 75) {
            return '#3089F2';
        }
        else if(percentage > 75 && percentage <= 99) {
            return '#1F5DA7';
        }
    }

    function appendStateDataToList(data, index) {

        let stateData = `
                        <li class="states-list">
                            <div class="d-flex container">
                                <ul>
                                    <li class="item-state">
                                        <dl class="d-flex">
                                            <dd>${index}°</dd>
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
