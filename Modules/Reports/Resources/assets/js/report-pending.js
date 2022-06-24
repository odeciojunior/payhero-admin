var currentPage = null;
//var atualizar = null;
let hasSale = false;

$(function() {
    changeCalendar();
    changeCompany();
});

function atualizar (link = null) {

    currentPage = link;
    let updateResume = true;

    loadOnTable('#body-table-pending', '.table-pending');

    if (link == null) {
        link = '/api/reports/pending-balance?' + getFilters(true).substr(1);
    } else {
        link = '/api/reports/pending-balance' + link + getFilters(true);
        updateResume = false;
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
            $('#body-table-pending').html('');
            $('.table-pending').addClass('table-striped');

            if (!isEmpty(response.data)) {
                $.each(response.data, function (index, value) {
                    let start_date='';
                    if (value.start_date) {
                        start_date=value.start_date.split(/\s/g);//data inicial
                        start_date= "<strong class='bold-mobile'>"+
                                    start_date[0]
                                +" </strong> <br> <small class='gray font-size-12'>"+
                                    start_date[1]
                                +" </small>";
                    }

                    let end_date='';
                    if (value.end_date) {
                        end_date=value.end_date.split(/\s/g);//data final
                        end_date= "<strong class='bold-mobile'>"+
                                    end_date[0]
                                +" </strong> <br> <small class='gray font-size-12'>"+
                                    end_date[1]
                                +" </small>";
                    }
                    let is_security_reserve = "";
                    if (value.is_security_reserve) {
                        is_security_reserve = `<br><label data-toggle="tooltip" title="Reserva de Segurança">
                                                   <img width="12px" src="/build/global/img/money_lock.svg" alt="Reserva de Segurança">
                                               </label>`;
                    }

                    dados = `  <tr>
                                <td class="text-center">
                                    ${value.sale_code}
                                    ${is_security_reserve}
                                </td>
                                <td class="text-left font-size-14">${value.project}</td>
                                <td class="text-left font-size-14">${value.client}</td>
                                <td class="display-sm-none display-m-none display-lg-none">
                                    <img src='/build/global/img/cartoes/${value.brand}.png' alt="${value.brand}"  style='width: 45px'>
                                </td>
                                <td class="display-sm-none display-m-none display-lg-none text-left font-size-14">${start_date}</td>
                                <td class="text-left font-size-14">${end_date}</td>
                                <td><b class="font-md-size-20">${value.total_paid}</b></td>
                                <td>
                                    <a role='button' class='detalhes_venda pointer' venda='${value.id}'><span class="o-eye-1"></span></button></a>
                                </td>
                            </tr>`;

                    $("#body-table-pending").append(dados);
                });

                $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                $("#date").attr('min', moment(new Date()).format("YYYY-MM-DD"));
            } else {
                $('#body-table-pending').html("<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                    $("#body-table-pending").attr("img-empty") +
                    "'> Nenhuma venda encontrada </td></tr>");
            }
            pagination(response, 'pending', atualizar);
        }
    });

    if (updateResume) {
        resumePending();
    }
}

function getFilters(urlParams = false) {
    let data = {
        'company': $("#company").val(),
        'project': $("#project").val(),
        'client': $("#client").val(),
        'customer_document': $("#customer_document").val(),
        'payment_method': $("#payment_method").val(),
        'sale_code': $("#sale_code").val().replace('#', ''),
        'date_type': $("#date_type").val(),
        'date_range': $("#date-filter").val(),
        'statement': hasSale == false ? 'automatic_liquidation' : $("#type_statement").val(),
        'acquirer':$("#acquirer").val(),
        'is_security_reserve': $('#is-security-reserve').is(':checked') ? 1: 0,
    };

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

function resumePending() {

    loadOnAny('.number', false, {
        styles: {
            container: {
                minHeight: '32px',
                height: 'auto'
            },
            loader: {
                width: '20px',
                height: '20px',
                borderWidth: '4px'
            },
        }
    });

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
            loadOnAny('.number', true);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            loadOnAny('.number', true);
            $('#total_sales').text('0');
            $('#total-pending, #total').html('R$ <strong class="font-size-30">0,00</strong>');
            if (response.total_sales) {
                $('#total_sales, #total-pending, #total').text('');
                $('#total_sales').text(response.total_sales);
                var comission=response.commission.split(/\s/g);
                $('#total-pending').html(comission[0]+' <span class="font-size-30 bold">'+comission[1]+'</span>');
            }
        }
    });
}

$(document).ready(function () {

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

    $('.btn-light-1').click(function () {
        var collapse = $('#icon-filtro')
        var text = $('#text-filtro')

        text.fadeOut(10);
        if (collapse.css('transform') == 'matrix(1, 0, 0, 1, 0, 0)' || collapse.css('transform') == 'none') {
            collapse.css('transform', 'rotate(180deg)')
            text.text('Minimizar filtros').fadeIn();
        } else {
            collapse.css('transform', 'rotate(0deg)')
            text.text('Filtros avançados').fadeIn()
        }
    });

    let startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
    let endDate = moment().format('YYYY-MM-DD');
    // $('#date-filter').daterangepicker({
    //     startDate: moment('2018-01-01 00:00:00'),
    //     endDate: moment(),
    //     opens: 'center',
    //     maxDate: moment().endOf("day"),
    //     alwaysShowCalendar: true,
    //     showCustomRangeLabel: 'Customizado',
    //     autoUpdateInput: true,
    //     locale: {
    //         locale: 'pt-br',
    //         format: 'DD/MM/YYYY',
    //         applyLabel: "Aplicar",
    //         cancelLabel: "Limpar",
    //         fromLabel: 'De',
    //         toLabel: 'Até',
    //         customRangeLabel: 'Customizado',
    //         weekLabel: 'W',
    //         daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
    //         monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
    //         firstDay: 0
    //     },
    //     ranges: {
    //         'Hoje': [moment(), moment()],
    //         'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    //         'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
    //         'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
    //         'Este mês': [moment().startOf('month'), moment().endOf('month')],
    //         'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
    //         'Vitalício': [moment('2018-01-01 00:00:00'), moment()]
    //     }
    // }, function (start, end) {
    //     startDate = start.format('YYYY-MM-DD');
    //     endDate = end.format('YYYY-MM-DD');
    // });

    getCompanies();

    function getCompanies() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
            //url: '/api/projects?select=true',
            url: '/api/core/companies?select=true',
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
                    $.each(response.data, function (index, company) {
                        if (company.company_has_sale_before_getnet) {
                            hasSale = true;
                        }
                        $('#company').append('<option value="' + company.id + '">' + company.name + '</option>')
                    });

                    if (hasSale) {
                        $("#select-statement-div").show();
                    }
                }

                getProjects();
                getAcquirer();
            }
        });
    }

    function getProjects() {
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
                        $("#project").append($('<option>', {
                            value: project.id,
                            text: project.name
                        }));
                        $("#select_projects").append(
                            $("<option>", {
                                value: project.id,
                                text: project.name,
                            })
                        );
                    });
                    if(sessionStorage.info) {
                        $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
                        $("#select_projects").find('option:selected').text(JSON.parse(sessionStorage.getItem('info')).companyName);
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

    function getAcquirer() {
        $.ajax({
            method: "GET",
            url: '/api/finances/acquirers/'+$('#company').val(),
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
                $.each(response.data, function (i, acquirer) {
                    $("#acquirer").append($('<option>', {
                        value: acquirer,
                        text: acquirer
                    }));
                });

                atualizar();

                loadingOnScreenRemove();
            }
        });
    }

    function resumePending() {

        loadOnAny('.number', false, {
            styles: {
                container: {
                    minHeight: '32px',
                    height: 'auto'
                },
                loader: {
                    width: '20px',
                    height: '20px',
                    borderWidth: '4px'
                },
            }
        });

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
                loadOnAny('.number', true);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny('.number', true);
                $('#total_sales').text('0');
                $('#total-pending, #total').html('<small class="font-size-16 small gray-1">R$</small> <strong class="font-size-24 orange">0,00</strong>');
                if (response.total_sales) {
                    $('#total_sales, #commission_blocked, #total').text('');
                    $('#total_sales').text(response.total_sales);
                    var comission=response.commission.split(/\s/g);
                    $('#total-pending').html('<small class="font-size-16 small gray-1">R$</small> <strong class="font-size-24 orange bold">'+comission[1]+'</strong>');
                    var total=response.total.split(/\s/g);
                    $('#total').html(total[0]+' <span class="font-size-24 orange bold">'+total[1]+'</span>');
                }
            }
        });
    }

    atualizar = function (link = null) {

        currentPage = link;
        let updateResume = true;

        loadOnTable('#body-table-pending', '.table-pending');

        if (link == null) {
            link = '/api/reports/pending-balance?' + getFilters(true).substr(1);
        } else {
            link = '/api/reports/pending-balance' + link + getFilters(true);
            updateResume = false;
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
                $('#body-table-pending').html('');
                $('.table-pending').addClass('table-striped');

                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, value) {
                        let start_date='';
                        if (value.start_date) {
                            start_date=value.start_date.split(/\s/g);//data inicial
                            start_date= "<strong class='bold-mobile'>"+
                                        start_date[0]
                                    +" </strong> <br> <small class='gray font-size-12'>"+
                                        start_date[1]
                                    +" </small>";
                        }

                        let end_date='';
                        if (value.end_date) {
                            end_date=value.end_date.split(/\s/g);//data final
                            end_date= "<strong class='bold-mobile'>"+
                                        end_date[0]
                                    +" </strong> <br> <small class='gray font-size-12'>"+
                                        end_date[1]
                                    +" </small>";
                        }
                        let is_security_reserve = "";
                        if (value.is_security_reserve) {
                            is_security_reserve = `<br><label data-toggle="tooltip" title="Reserva de Segurança">
                                                       <img width="12px" src="/build/global/img/money_lock.svg" alt="Reserva de Segurança">
                                                   </label>`;
                        }

                        dados = `  <tr>
                                    <td class="text-center">
                                        ${value.sale_code}
                                        ${is_security_reserve}
                                    </td>
                                    <td class="text-left font-size-14">${value.project}</td>
                                    <td class="text-left font-size-14">${value.client}</td>
                                    <td class="display-sm-none display-m-none display-lg-none">
                                        <img src='/build/global/img/cartoes/${value.brand}.png' alt="${value.brand}"  style='width: 45px'>
                                    </td>
                                    <td class="display-sm-none display-m-none display-lg-none text-left font-size-14">${start_date}</td>
                                    <td class="text-left font-size-14">${end_date}</td>
                                    <td><b class="font-md-size-20">${value.total_paid}</b></td>
                                    <td>
                                        <a role='button' class='detalhes_venda pointer' venda='${value.id}'><span class="o-eye-1"></span></button></a>
                                    </td>
                                </tr>`;

                        $("#body-table-pending").append(dados);
                    });

                    $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                    $("#date").attr('min', moment(new Date()).format("YYYY-MM-DD"));
                } else {
                    $('#body-table-pending').html("<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                        $("#body-table-pending").attr("img-empty") +
                        "'> Nenhuma venda encontrada </td></tr>");
                }
                pagination(response, 'pending', atualizar);
            }
        });

        if (updateResume) {
            resumePending();
        }
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
function changeCompany() {
    $("#select_projects").on("change", function () {
        updateStorage({company: $(this).val(), companyName: $(this).find('option:selected').text()});
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