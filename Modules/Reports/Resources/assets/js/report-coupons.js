var currentPage = null;
var atualizar = null;

$(document).ready(function () {
    changeCalendar();
    changeCompany();

    if(sessionStorage.info) {
        let info = JSON.parse(sessionStorage.getItem('info'));
        $('input[name=daterange]').val(info.calendar);
        $('#status').val(info.statusCompany);
        $("#status").find('option:selected').text(info.statusCompanyText);
    }

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar();
    });

    function getFilters(urlParams = false) {
        let data = {
            'project': $("#projeto").val(),
            'status': $("#status").val(),
            'date_range': $("#date-filter").val(),
        };
        updateStorage({statusCompany: data["status"], statusCompanyText: $("#status").find('option:selected').text()});

        if (urlParams) {
            let params = "";
            for (let param in data) {
                params += '&' + param + '=' + data[param];
            }
            return encodeURI(params);
        } else {
            return data;
        }
    }    

    getProjects();

    function getProjects() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: '/api/projects?select=true',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $("#export-excel").show()

                    $.each(response.data, function (i, project) {
                        $("#projeto").append($('<option>', {
                            value: project.id,
                            text: project.name
                        }));
                    });

                    if(sessionStorage.info) {
                        $("#projeto").val(JSON.parse(sessionStorage.getItem('info')).company);
                        $("#projeto").find('option:selected').text(JSON.parse(sessionStorage.getItem('info')).companyName);
                    }

                    atualizar();

                } else {
                    $("#export-excel").hide()
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }

                loadingOnScreenRemove();
            }
        });
    }

    atualizar = function (link = null) {

        currentPage = link;

        let updateResume = true;
        loadOnTable('#body-table-coupons', '.table-coupons');
        $('#body-table-coupons').html(skeLoad);

        if (link == null) {
            link = '/api/reports/coupons?' + getFilters(true).substr(1);
        } else {
            link = '/api/reports/coupons' + link + getFilters(true);
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#body-table-coupons').html('');
                $('.table-coupons').addClass('table-striped');

                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, value) {

                        dados = `  <tr>
                                    <td>${value.cupom_code}</td>
                                    <td>${value.project}</td>
                                    <td>${value.amount}</td>
                                </tr>`;

                        $("#body-table-coupons").append(dados);
                    });

                    $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                    $("#date").attr('min', moment(new Date()).format("YYYY-MM-DD"));
                } else {
                    $('#body-table-coupons').html("<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                        $("#body-table-coupons").attr("img-empty") +
                        "'> Nenhum cupom encontrado</td></tr>");
                }
                pagination(response, 'coupons', atualizar);
            }
        });

        // if(updateResume) {
        //     resumePending();
        // }
        
    }

    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            atualizar();
        }
    });
});

function changeCalendar() {
    var startDate = moment().subtract(30, "days").format("DD/MM/YYYY");
    var endDate = moment().format("DD/MM/YYYY");

    $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
    $('input[name="daterange"]').dateRangePicker({
        setValue: function (s) {
            if (s) {
                let normalize = s.replace(/(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/, "$120$2-$320$4");
                $(this).html(s).data('value', normalize);
                $('input[name="daterange"]').attr('value', normalize);
                $('input[name="daterange"]').val(normalize);
            } else {
                $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
                $('input[name="daterange"]').val(`${startDate}-${endDate}`);
            }
        }
    })
    .on('datepicker-change', function () {
        updateStorage({calendar: $(this).val()});
    })
    .on('datepicker-open', function () {
        $('.filter-badge-input').removeClass('show');
    })
    .on('datepicker-close', function () {
        $(this).removeClass('focused');
        if ($(this).data('value')) {
            $(this).addClass('active');
        }
    });
}

function updateStorage(v){
    var existing = sessionStorage.getItem('info');
    existing = existing ? JSON.parse(existing) : {};
    Object.keys(v).forEach(function(val, key){
        existing[val] = v[val];
   })
    sessionStorage.setItem('info', JSON.stringify(existing));
}

function changeCompany() {
    $("#projeto").on("change", function () {
        // $('.onPreLoad *, .onPreLoadBig *').remove();
        // $('.onPreLoad').html(skeLoad);
        // $('.onPreLoadBig').html(skeLoadBig);
        $.ajaxQ.abortAll();
        updateStorage({company: $(this).val(), companyName: $(this).find('option:selected').text()});
        
        atualizar();
    });
}


function resumePending() {

    $("#total_sales").html(skeLoadMini);    

    $.ajax({
        method: "GET",
        url: '/api/reports/resume-pending-balance',
        data: getFilters(),
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            if (response.total_sales) {
                $('#total_sales, #total-pending, #total').text('');
                $('#total_sales').text(response.total_sales);
                var comission=response.commission.split(/\s/g);
                $('#total-pending').html(comission[0]+' <span class="font-size-30 bold">'+comission[1]+'</span>');
            } else {
                $('#total-pending, #total').html('R$ <strong class="font-size-30">0,00</strong>');
            }
        }
    });
}


// abort all ajax
$.ajaxQ = (function(){
    var id = 0, Q = {};
  
    $(document).ajaxSend(function(e, jqx){
      jqx._id = ++id;
      Q[jqx._id] = jqx;
    });
    $(document).ajaxComplete(function(e, jqx){
      delete Q[jqx._id];
    });
  
    return {
      abortAll: function(){
        var r = [];
        $.each(Q, function(i, jqx){
          r.push(jqx._id);
          jqx.abort();
        });
        return r;
      }
    };
  
  })();

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
            <div class="skeleton skeleton-text ske"></div>
        </div>
    </div>
`;

let skeLoadBig = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>

        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>

        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>
    </div>
`;

let skeLoadMini = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
        </div>
    </div>
`;