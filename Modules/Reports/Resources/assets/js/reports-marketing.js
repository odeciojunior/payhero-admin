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
let mktUrl = '/api/reports/marketing';

function getCoupons() {
    $('#card-coupon .onPreLoad *' ).remove();
    $("#block-coupons").prepend(skeLoad);
    let couponList = '';
    
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
            let arr = [];
            let seriesArr = [];
            $('.new-graph-pie-mkt').html('<div class=graph-pie></div>');
            
            $.each(response.data, function (i, coupon) {
                if(coupon != undefined) {
                    arr.push(coupon);
                    $('.data-pie li.donut-pie').remove();
                }
                else {
                    $('#card-coupon').height('232px');
                    $('#block-coupons *').remove();
                    $('#block-coupons').after('<div class=no-graph>Não há dados suficientes</div>');
                }
            });
            for(let i = 0; i < arr.length; i++) {
                if(arr[i].total == 0) {
                    $('#card-coupon').height('232px');
                    $('#block-coupons *').remove();
                    $('#block-coupons').after('<div class=no-graph>Não há dados suficientes</div>');
                    $(".box-donut").addClass('invis');
                    $(".data-pie ul").remove();
                } else {
                    $(".box-donut").removeClass('invis');
                    if($('.data-pie *').length == 0) $('.data-pie').html('<ul></ul>');
                    
                    if(arr[i].amount != undefined) {
                        seriesArr.push(arr[i].amount);
                        couponList = 
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
                        
                        $('.data-pie ul').append(couponList)
                    }
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
            $('#card-coupon').height('232px');
            $('#card-coupon .onPreLoad *' ).remove();
        }
    });
}           

function resume() {
    let checkouts, salesCount,salesValue = '';
    $("#checkouts_count, #sales_count, #sales_value").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: mktUrl + "/resume?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            checkouts = `
                <span class="title">Acessos</span>
                <div class="d-flex">
                    <strong class="number">${response.data.checkouts_count}</strong>
                </div>
            `;
            salesCount = `
                <span class="title">Vendas</span>
                <div class="d-flex">
                    <strong class="number">${response.data.sales_count}</strong>
                    <small class="percent">(52%)</small>
                </div>
            `;
            salesValue = `
                <span class="title">Receita</span>
                <div class="d-flex">
                    <span class="detail">R$</span>
                    <strong class="number">${removeMoneyCurrency(response.data.sales_value)}</strong>
                </div>
            `;
            $("#checkouts_count").html(checkouts);
            $("#sales_count").html(salesCount);
            $("#sales_value").html(salesValue);
        }
    }); 
}

function frequenteSales() {
    let salesBlock = '';
    $('#card-most-sales .onPreLoad *' ).remove();
    $("#block-sales").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: mktUrl + "/most-frequent-sales?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $.each(response.data, function (i, item) {
                salesBlock = `
                    <div class="box-payment-option pad-0">
                        <div class="d-flex align-items list-sales">
                            <div class="d-flex align-items">
                                <div>
                                    <figure class="box-ico">
                                        <img width="34px" height="34px" src="${item.photo}" alt="${item.description}">
                                    </figure>
                                </div>
                                <div>
                                    <span>${item.name}</span>
                                </div>
                            </div>
                            <div class="grey font-size-14">${item.sales_amount}</div>
                            <div class="grey font-size-14"><strong>${item.value}</strong></div>
                        </div>
                    </div>
                `;
                $("#block-sales").append(salesBlock);
            });
            $('#block-sales .ske-load' ).remove();
        }
    }); 
  
}

$('.box-export').on('click', function($q) {  

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

  $.ajax({
        method: "GET",
        url: "http://dev.sirius.com/api/reports/marketing/operational-systems?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
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
        url: "http://dev.sirius.com/api/reports/marketing/state-details?state=MG&company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
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

function updateStorage(value){
    let prevData = JSON.parse(sessionStorage.getItem('info'));
    Object.keys(value).forEach(function(val, key){
         prevData[val] = value[val];
    })
    sessionStorage.setItem('info', JSON.stringify(prevData));
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
        
        $(".data-pie ul").remove();
        $('.onPreLoad *').remove();
        $('.onPreLoad').append(skeLoad);
        updateStorage({company: $(this).val()})
        updateReports();
    });
}

function updateReports() {
    $(".box-donut").addClass('invis');
    $('.no-graph').remove();
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
            resume();
            devices();
            frequenteSales();
        },
    });
}

function devices() {
    let deviceBlock = '';
    $('#card-devices .onPreLoad *' ).remove();
    $("#block-devices").prepend(skeLoad);

    return $.ajax({
        method: "GET",
        url: mktUrl + "/devices?company_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            let {
                total, 
                percentage_desktop, 
                percentage_mobile, 
                count_desktop, 
                count_mobile
            } = response.data;
            
            deviceBlock = `
                <div class="row container-devices">
                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#636363"/></svg>
                                    </div>Smartphones
                                </div>
                                
                                <div class="box-payment-option option">
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span class="silver">${percentage_mobile}</span>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-14">R$ ${count_mobile}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 2.83333C0 1.26853 1.26853 0 2.83333 0H14.1667C15.7315 0 17 1.26853 17 2.83333V11.3333C17 12.8981 15.7315 14.1667 14.1667 14.1667H11.3333V14.875C11.3333 15.2662 11.6505 15.5833 12.0417 15.5833H12.75C13.1412 15.5833 13.4583 15.9005 13.4583 16.2917C13.4583 16.6829 13.1412 17 12.75 17H4.25C3.8588 17 3.54167 16.6829 3.54167 16.2917C3.54167 15.9005 3.8588 15.5833 4.25 15.5833H4.95833C5.34953 15.5833 5.66667 15.2662 5.66667 14.875V14.1667H2.83333C1.26853 14.1667 0 12.8981 0 11.3333V2.83333ZM10.0376 15.5833C9.95928 15.3618 9.91667 15.1234 9.91667 14.875V14.1667H7.08333V14.875C7.08333 15.1234 7.04072 15.3618 6.96242 15.5833H10.0376ZM14.1667 12.75C14.9491 12.75 15.5833 12.1157 15.5833 11.3333H1.41667C1.41667 12.1157 2.05093 12.75 2.83333 12.75H14.1667ZM15.5833 2.83333C15.5833 2.05093 14.9491 1.41667 14.1667 1.41667H2.83333C2.05093 1.41667 1.41667 2.05093 1.41667 2.83333V9.91667H15.5833V2.83333Z" fill="#636363"/></svg>
                                    </div> Desktop
                                </div>
                                <div class="box-payment-option option">
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span class="silver">${percentage_desktop}</span>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-14">R$ ${count_desktop}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $("#block-devices").html(deviceBlock);
        }
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