$(function() {
    loadingOnScreen();
    exportReports();
    updateReports();    
    
    changeCompany();
    changeCalendar();
    
    let info = JSON.parse(sessionStorage.getItem('info'));
    $('input[name=daterange]').val(info.calendar);

    
});

let resumeUrl = '/api/reports/resume';

function getCoupons() {
    let couponsBlock = '';
    $('#card-coupon .onPreLoad *' ).remove();
    $("#block-coupons").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: resumeUrl + "/coupons?date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if(response.data[0].total != 0){
                $('.box-donut').css('height','190px');
                // $(".box-donut").next('.no-graph').remove();
                //$('#card-coupons .value-price').addClass('invisible');
                couponsBlock = `
                    <div class="container d-flex justify-content-between box-donut">
                        <div class="new-graph-pie-mkt"><div class=graph-pie></div></div>
                        <div class="data-pie"><ul></ul></div>
                `;
                let arr = [];
                let seriesArr = [];
                
                $.each(response.data, function (i, coupon) {
                    arr.push(coupon);
                });
                

                for(let i = 0; i < arr.length; i++) {
                    if(arr[i].amount != undefined) {
                        seriesArr.push(arr[i].amount);

                        $('.data-pie ul').html(
                            `
                                <li>
                                    <div class="donut-pie ${arr[i].color}">
                                        <figure>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke-width="3"/>
                                            </svg>
                                        </figure>
                                        <div>${arr[i].coupon}</div>
                                    </div>
                                    <div class="grey bold">${arr[i].amount}</div>
                                </li>                                    
                            `
                        );
                        
                    }
                }
                
                

                new Chartist.Pie('.graph-pie', 
                { series: seriesArr }, 
                {
                    donut: true,
                    donutWidth: 20,
                    donutSolid: true,
                    startAngle: 270,
                    showLabel: false,
                    chartPadding: 0,
                    labelOffset: 0,
                });
                $("#block-coupons").html(couponsBlock);
                $('#card-coupon').height('232px');
            } else {
                $('#card-coupon').height('232px');
                $('#block-coupons *').remove();
                $('#block-coupons').after('<div class=no-graph>Não há dados suficientes</div>');
            }        
        }
    });
}           
                

$('.box-export').on('click', function($q) {

  $.ajax({
      method: "GET",
      url: "http://dev.sirius.com/api/reports/marketing/resume?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
      dataType: "json",
      headers: {
          Authorization: $('meta[name="access-token"]').attr("content"),
          Accept: "application/json",
      },
      error: function error(response) {

      },
      success: function success(response) {

      }
  }); 

  $.ajax({
      method: "GET",
      url: "http://dev.sirius.com/api/reports/marketing/sales-by-state?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
      dataType: "json",
      headers: {
          Authorization: $('meta[name="access-token"]').attr("content"),
          Accept: "application/json",
      },
      error: function error(response) {

      },
      success: function success(response) {

      }
  }); 

});

function exportReports() {
    // show/hide modal de exportar relatórios
    $(".lk-export").on('click', function(e) {
        e.preventDefault();
        $('.inner-reports').addClass('focus');
        $('.line-reports').addClass('d-flex');
    });

    $('.reports-remove').on('click', function (e) {
        e.preventDefault();
        $('.inner-reports').removeClass('focus');
        $('.line-reports').removeClass('d-flex');
    });

}

function changeCalendar() {
    $('.onPreLoad *').remove();
    
    $('input[name="daterange"]').daterangepicker(
        {
            startDate: moment().subtract(30, "days"),
            endDate: moment(),
            opens: "left",
            maxDate: moment().endOf("day"),
            alwaysShowCalendar: true,
            showCustomRangeLabel: "Customizado",
            autoUpdateInput: true,
            locale: {
                locale: "pt-br",
                format: "DD/MM/YYYY",
                applyLabel: "Aplicar",
                cancelLabel: "Limpar",
                fromLabel: "De",
                toLabel: "Até",
                customRangeLabel: "Customizado",
                weekLabel: "W",
                daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
                monthNames: [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro",
                ],
                firstDay: 0,
            },
            ranges: {
                Hoje: [moment(), moment()],
                Ontem: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                "Este mês": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Mês passado": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            },
        },
        function (start, end) {
            startDate = start.format("YYYY-MM-DD");
            endDate = end.format("YYYY-MM-DD");
            
            
            $('.onPreLoad *').remove();
            $('.onPreLoad').append(skeLoad);
            
            updateReports();
        }
    );
    
    $('input[name="daterange"]').change(function() {
        updateStorage({calendar: $(this).val()})
    })
    
}

function changeCompany() {
    $("#select_projects").on("change", function () {
        
        $('.onPreLoad *').remove();
        $('.onPreLoad').append(skeLoad);
        updateStorage({company: $(this).val()})
        updateReports();
    });
}

function updateReports() {
    $('.onPreLoad *').remove();
    $('.onPreLoad').append(skeLoad);

    $.ajax({
        method: "GET",
        url: "/api/projects?select=true",
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            loadingOnScreenRemove();
            $("#modal-content").hide();
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (!isEmpty(response.data)) {

                $("#project-empty").hide();
                $("#project-not-empty").show();
                $("#export-excel").show();

                $.each(response.data, function (i, project) {
                    $("#select_projects").append(
                        $("<option>", {
                            value: project.id,
                            text: project.name,
                        })
                    );
                });
                $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
            } else {
                $("#export-excel").hide();
                $("#project-not-empty").hide();
                $("#project-empty").show();
            }

            loadingOnScreenRemove();
        },
    });

    var date_range = $("#date_range_requests").val();
    var startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
    var endDate = moment().format("YYYY-MM-DD");
    
    $.ajax({
        url: "/api/reports",
        type: "GET",
        data: {
            project: $("#select_projects").val(),
            endDate: endDate,
            startDate: startDate,
        },
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $('.onPreLoad *').remove();
            getCoupons();
        },
    });
}

let skeLoad = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text"></div>
        </div>
    </div>
`;