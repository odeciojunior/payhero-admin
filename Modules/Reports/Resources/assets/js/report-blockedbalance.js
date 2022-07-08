var currentPage = null;
//var atualizar = null;

$(function() {
    changeCalendar();
    changeCompany();
});

function atualizar(link = null) {

    currentPage = link;
    let updateResume = true;

    loadOnTable('#dados_tabela', '#tabela_vendas');
    //$('#dados_tabela').html(skeLoad);

    if (link == null) {
        link = '/api/reports/blocked-balance?' + getFilters(true).substr(1);
    } else {
        link = '/api/reports/blocked-balance' + link + getFilters(true);
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
            $('#dados_tabela').html('');
            $('#tabela_vendas').addClass('table-striped');
            $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                    $("#dados_tabela").attr("img-empty") +
                    "'>Nenhuma venda encontrada</td></tr>");
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $('#dados_tabela').html('');
            $('#tabela_vendas').addClass('table-striped');

            let statusArray = {
                1: 'success',
                6: 'primary',
                7: 'danger',
                8: 'warning',
                4: 'danger',
                3: 'danger',
                2: 'pendente',
                12: 'success',
                20: 'antifraude',
                22: 'danger',
                23: 'warning',
                24: 'antifraude',
            };

            if (!isEmpty(response.data)) {
                $.each(response.data, function (index, value) {
                    let start_date = '';
                    if (value.start_date) {
                        start_date = value.start_date.split(/\s/g);//data inicial
                        start_date = "<strong class='bold-mobile'>" +
                            start_date[0]
                            + " </strong> <br> <small class='gray font-size-12'>" +
                            start_date[1]
                            + " </small>";
                    }
                    let end_date = '';
                    if (value.end_date) {
                        end_date = value.end_date.split(/\s/g);//data final
                        end_date = "<strong class='bold-mobile'>" +
                            end_date[0]
                            + " </strong> <br> <small class='gray font-size-12'>" +
                            end_date[1]
                            + " </small>";
                    }
                    dados = `  <tr>
                                <td class='display-sm-none display-m-none display-lg-none text-center text-left font-size-14'>
                                    ${value.sale_code}
                                    ${value.upsell ? '<span class="text-muted font-size-10">(Upsell)</span>' : ''}
                                </td>
                                <td class="text-left font-size-14">${value.project}</td>
                                <td class="text-left font-size-14">${value.product}${value.affiliate != null && value.user_sale_type == 'producer' ? `<br><small>(Afiliado: ${value.affiliate})</small>` : ''}</td>
                                <td class='display-sm-none display-m-none display-lg-none text-left font-size-14'>${value.client}</td>
                                <td>
                                    <img src='/build/global/img/cartoes/${value.brand}.png'  style='width: 45px'>
                                </td>
                                <td>
                                   <div class="d-flex align-items-center">
                                        <span class="badge badge-${statusArray[value.status]} ${value.status_translate === 'Pendente' ? 'boleto-pending' : ''}" ${value.status_translate === 'Pendente' ? 'status="' + value.status_translate + '" sale="' + value.id_default + '"' : ''}>${value.status_translate}</span>
                                           ${value.is_chargeback_recovered && value.status_translate === 'Aprovado' ? `
                                            <img class="orange-gradient ml-10" width="20px" src="/build/global/img/svg/chargeback.svg" title="Chargeback recuperado">`
                            : ''}
                                    </div>
                                </td>
                                <td class='display-sm-none text-left font-size-14 display-m-none'>${start_date}</td>
                                <td class='display-sm-none text-left font-size-14'>${end_date}</td>
                                <td style='white-space: nowrap' class="text-left font-size-14"><b>${value.total_paid}</b></td>
                                <td class="text-left font-size-14">
                                    ${value.reason_blocked}
                                </td>
                            </tr>`;

                    $("#dados_tabela").append(dados);
                });

                $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                $("#date").attr('min', moment(new Date()).format("YYYY-MM-DD"));
            } else {
                $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                    $("#dados_tabela").attr("img-empty") +
                    "'>Nenhuma venda encontrada</td></tr>");
            }
            pagination(response, 'sales', atualizar);
        }
    });

    if (updateResume) {
        blockedResume();
    }

}

function getFilters(urlParams = false) {
    let data = {
        'company': $("#company").val(),
        'project': $("#project").val(),
        'payment_method': $("#payment_method").val(),
        'status': $("#status").val(),
        'client': $("#client").val(),
        'customer_document': $("#customer_document").val(),
        'date_type': $("#date_type").val(),
        'date_range': $("#date-filter").val(),
        'transaction': $("#transaction").val().replace('#', ''),
        'reason': $('#reason').val(),
        'plan': $('#plan').val(),
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

function blockedResume() {
    $("#commission_blocked, #total_sales").html(skeLoadMini).width('100%');
    // loadOnAny('.number', false, {
    //     styles: {
    //         container: {
    //             minHeight: '32px',
    //             height: 'auto'
    //         },
    //         loader: {
    //             width: '20px',
    //             height: '20px',
    //             borderWidth: '4px'
    //         },
    //     }
    // });

    $.ajax({
        method: "GET",
        url: '/api/reports/resume-blocked-balance',
        data: getFilters(),
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: function error(response) {
            //loadOnAny('.number', true);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            //loadOnAny('.number', true);
            //$('#total_sales').text('0');
            
            if (response.total_sales) {
                $('#total_sales, #commission_blocked, #total').text('');
                $('#total_sales').html(response.total_sales);
                $('#commission_blocked').html(`<small class="font-size-16 small gray-1">R$</small> <strong class="font-size-24 bold">${response.commission}</strong>`);
                $('.blocked-balance-icon').attr('title', 'Saldo retido de convites: R$ ' + response.commission_invite).tooltip({ placement: 'bottom' });
                $('.blocked-balance-icon').attr('data-original-title', 'Saldo retido de convites: R$ ' + response.commission_invite).tooltip({ placement: 'bottom' });
                $('#total').html(`R$ <span class="font-size-24 bold">${response.total}</span>`);
            }
            else {
                $('#commission_blocked, #total').html('<small class="font-size-16 small gray-1">R$</small> <span class="font-size-24 bold">0,00</span>');
                $('#total_sales').html('<strong class="font-size-24 orange">0</strong>');
            }
        }
    });
}

$(document).ready(function () {

    //checkbox
    $('.check').on('click', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
    // COMPORTAMENTOS DA JANELA

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
    // $('#date_range').daterangepicker({
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

    // FIM - COMPORTAMENTOS DA JANELA

    getBlockReasons();

    function getBlockReasons() {
        $.ajax({
            url: '/api/reports/block-reasons',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
            },
            error: resp => {
                errorAjaxResponse(resp);
            },
            success: resp => {
                for(const item of resp) {
                    const option = `<option value="${item.id}" data-toggle="tooltip" title="${item.reason}">
                                        ${item.reason}
                                    </option>`;
                    $('#reason').append(option)
                }
            }
        })
    }

    getCompanies();

    function getCompanies() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
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
                        const document = (company.document.replace(/\D/g, '').length > 11 ? 'CNPJ: ' : 'CPF: ') + company.document;
                        $('#company').append(`<option value="${company.id}" data-toggle="tooltip" title="${document}">${company.name}</option>`)
                    });

                    // if (hasSale) {
                    //     $("#select-statement-div").show();
                    // }
                }

                // getProjects();
                // getAcquirer();
            }
        });
    }

    getProjects();

    // Obtem o os campos dos filtros
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

    $("#project").on('change', function () {
        let value = $(this).val();
        $("#plan").val(null).trigger('change');
    });

    //Search plan
    $('#plan').select2({
        placeholder: 'Nome do plano',
        // multiple: true,
        allowClear: true,
        language: {
            noResults: function () {
                return 'Nenhum plano encontrado';
            },
            searching: function () {
                return 'Procurando...';
            },
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'plan',
                    search: params.term,
                    project_id: $("#project").val(),
                };
            },
            method: "GET",
            url: "/api/sales/user-plans",
            delay: 300,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processResults: function (res) {
                return {
                    results: $.map(res.data, function (obj) {
                        return { id: obj.id, text: obj.name + (obj.description ? ' - ' + obj.description : '') };
                    })
                };
            },
        }
    });

    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            atualizar();
        }
    });

    function blockedResume() {

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
            url: '/api/reports/blockedresume',
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
                $('#commission_blocked, #total').html('R$ <span class="font-size-30 bold">0,00</span>');
                if (response.total_sales) {
                    $('#total_sales, #commission_blocked, #total').text('');
                    $('#total_sales').html(response.total_sales);
                    $('#commission_blocked').html(`R$ <span class="font-size-30 bold">${response.commission}</span>`);
                    $('.blocked-balance-icon').attr('title', 'Saldo bloqueado de convites: R$ ' + response.commission_invite).tooltip({placement: 'bottom'});
                    $('.blocked-balance-icon').attr('data-original-title', 'Saldo bloqueado de convites: R$ ' + response.commission_invite).tooltip({placement: 'bottom'});
                    $('#total').html(`R$ <span class="font-size-30 bold">${response.total}</span>`);
                }
            }
        });
    }
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