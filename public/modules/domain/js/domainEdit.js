$(document).ready(function () {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    let btnAddDomain = $("#btn-add-domain");
    let btnDeleteDomain = $("#btn-delete-domain");
    let btnAddDomainModal = $("#btn-modal-add-domain");

    let infoDomain = $(".info-domain");

    $("#tab-domains").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        updateDomains();
    });

    updateDomains();

    /**
     * Atualiza tabelas de dominios
     * @param link
     */
    function updateDomains(link = null) {
        loadOnTable('#domain-table-body', '#tabela-dominios');

        if (link == null) {
            link = '/api/project/' + projectId + '/domains';
        } else {
            link = '/api/project/' + projectId + '/domains' + link;
        }

        $.ajax({
            method: 'GET',
            url: link,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            }, success: function (response) {
                $("#domain-table-body").html('');
                if (response.data == '') {
                    $("#domain-table-body").html("<tr class='text-center'><td colspan='4' style='height: 70px; vertical-align: middle;'>Nenhum domínio encontrado</td></tr>")
                    $('#tabela-dominios').addClass('table-striped');

                } else {
                    $.each(response.data, function (index, value) {
                        tableDomains(value);
                    });
                    $('#tabela-dominios').addClass('table-striped');

                    pagination(response, 'domain', updateDomains);
                    verifyCompanyDocuments(response);
                    /**
                     * Delete Domain
                     */
                    $(".delete-domain").on('click', function () {
                        let domain = $(this).attr('domain');
                        deleteDomain(domain);
                    });

                    /**
                     * Update Domain
                     */
                    $(".edit-domain").on('click', function () {
                        $("#btn-modal-add-input").show();
                        let domain = $(this).attr('domain');
                        $("#modal-content-domain").css('overflow-y', 'auto').modal('show');
                        $("#domain").val(domain);

                        updateTableRecords(domain);
                    });

                    $(".details-domain").on('click', function () {
                        let domainId = $(this).attr('domain');
                        $("#domain").val('');
                        $("#domain").val(domainId);
                        $("#content-modal-recheck-dns-error").hide();
                        $("#content-modal-recheck-dns").show();
                        verifyDataDomain();
                    });

                }

            }
        });
    }

    /**
     * Delete Domain
     * @param domain
     */
    function deleteDomain(domain) {
        $("#modal-delete-domain-body, #title-delete-domain, #description-delete-domain, .btn-delete-modal-domain").show();

        $("#modal-delete-domain").modal("show");

        btnDeleteDomain.unbind('click');
        btnDeleteDomain.on('click', function () {
            $(".btn-delete-modal-domain").hide();

            loadOnAny('#modal-delete-domain-body', false, {
                styles: {
                    container: {
                        minHeight: '240px'
                    }
                },
                insertBefore: '.modal-delete-footer'
            });

            $.ajax({
                method: 'DELETE',
                url: '/api/project/' + projectId + '/domains/' + domain,
                dataType: 'json',
                data: {
                    'project': projectId,
                    'domain': domain,
                },
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    $('#close-modal-delete-domain').click();
                    loadOnAny('#modal-delete-domain-body', true);

                    errorAjaxResponse(response);
                },
                success: function (response) {

                    $('#close-modal-delete-domain').click();
                    loadOnAny('#modal-delete-domain-body', true);

                    alertCustom('success', response.message);
                    updateDomains();
                }
            });

        })

    }

    /**
     * Monta tabela de dominios
     * @param value
     */
    function tableDomains(value) {
        var dados = '';
        dados += '<tr>';
        dados += '<td class="text-center">' + value.domain + '</td>';
        dados += '<td class="text-center"><span class="badge badge-' + statusDomain[value.status] + '">' + value.status_translated + '</span></td>';
        dados += "<td style='text-align:center;'>"
        dados += "<a title='Visualizar' role='button' class='mg-responsive details-domain pointer' status='" + value.status + "' domain='" + value.id + "' ><i class='material-icons gradient'>remove_red_eye</i> </a>"
        dados += "<a title='Editar' role='button' class='mg-responsive edit-domain    pointer' status='" + value.status + "' domain='" + value.id + "' data-toggle='modal'><i class='material-icons gradient'>edit</i> </a>"
        dados += "<a title='Excluir' role='button' class='mg-responsive delete-domain  pointer' status='' domain='" + value.id + "' data-toggle='modal'><i class='material-icons gradient'>delete_outline</i> </a>";
        dados += "</td>";
        dados += '</tr>';
        $("#domain-table-body").append(dados);
    }

    /**
     * Verifica dados e exibi modal para adicionar novo dominio
     */
    btnAddDomain.unbind('click');
    btnAddDomain.on('click', function () {
        $("#btn-modal-add-input").show();
        $("#loaderModal").remove();
        loadingOnScreenRemove();

        $("#modal-title-add-domain").html('Novo domínio').show();
        $("#form-add-domain, #btn-modal-add-domain").show();

        $("#form-add-domain").submit(function () {
            return false;
        });

        $('#modal-body-add-domain, #btn-modal-add-domain').show();

        $("#modal-add-domain").modal('show');

        btnAddDomainModal.unbind('click');
        btnAddDomainModal.on('click', function () {

            if ($.trim($(".name-domain").val()).length === 0) {
                infoDomain.addClass('text-danger').html('Preencha corretamente o domínio').show();
            } else {
                $(".info-domain").html('');
                addNewDomain();
            }

        });
    });

    /**
     * Salva dados novo dominio da modal
     */
    function addNewDomain() {
        $("#especialModalTitle").remove();
        $("#modal-title-add-domain").html('');
        //loadOnModalDomainEspecial('#modal-content-add-domain');
        loadOnModalDomainEspecial('#modal-body-add-domain');
        $("#btn-modal-add-domain").hide();

        btnAddDomainModal.hide();

        let formData = new FormData(document.getElementById('form-add-domain'));
        formData.append('project_id', projectId);

        $.ajax({
            method: "POST",
            url: '/api/project/' + projectId + '/domains',
            processData: false,
            contentType: false,
            cache: false,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            dataType: "json",
            data: formData,
            error: function (response) {
                $("#especialModalTitle").remove();

                loadOnAny('#modal-body-add-domain', true);

                $("#modal-title-add-domain").html('Novo domínio').show();
                $("#form-add-domain, #btn-modal-add-domain").show();

                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny('#modal-body-add-domain', true);

                $("#modal-button-close").click();
                alertCustom('success', response.message);
                updateDomains();
                $(".btn-continue-domain").show();
                infoDomain = response.data;
                modalDomainEdit(response);

            }
        });

    }

    /**
     * Remove Loads
     */
    function removeLoad() {
        loadingOnScreenRemove();
        $("#loaderModal").remove();
        $('#especialModalTitle').remove();
    }

    /**
     * Muda input quando for entrada MX
     */
    $("#type-register").change(function () {
        if ($("#type-register option:selected").val() === 'MX') {
            $("#name-register").parent().removeClass('col-lg-10').addClass('col-lg-8');
            $("#div-input-priority").remove();
            $("#name-register").parent().after(
                ' <div id="div-input-priority" class="col-sm-12 col-md-5 col-lg-2 mb-3">' +
                '<input id="value-priority" name="priority" class="input-pad" data-mask="0#" placeholder="Prioridade">' +
                '</div>'
            );
            $("#proxy-active").attr('disabled', true);
            $("#proxy-select ").val('0').change();

            $('#value-priority').mask('0#');

        } else if ($("#type-register option:selected").val() === 'TXT') {
            $("#div-input-priority").remove();

            $("#proxy-active").attr('disabled', true);
            $("#proxy-select").val('0').change();

        } else {
            $("#div-input-priority").remove();

            $("#proxy-active").removeAttr('disabled');

            $("#div-input-priority").remove();
            $("#name-register").parent().removeClass('col-lg-8').addClass('col-lg-10');
        }
    });

    /**
     * Cancela o submit do form de adicionar record
     */
    $("#form-modal-add-domain-record").submit(function () {
        return false;
    });

    /**
     * Modal carrega dados apos adicionar dominio, editar records
     * @param response
     */
    function modalDomainEdit(response) {

        $("#domain").val('');
        $("#domain").val(response.data.id_code);
        $("#btn-modal-continue-domain").show();

        $("#modal-content-domain").css('overflow-y', 'auto').modal('show');

        updateTableRecords(response.data.id_code);

    }

    function verifyInputEmpty() {
        let returno = false;
        if (($("#name-register").val().length === 0)) {
            $("#error-name-register-dns").show();
            returno = true;
        } else {
            $("#error-name-register-dns").hide();
        }

        if (($("#value-record").val().length === 0)) {
            $("#error-value-record").show();
            returno = true;
        } else {
            $("#error-value-record").hide();
        }

        return returno;
    }

    /**
     * Adiciona records ao dominio
     */
    $("#bt-add-record").on('click', function () {
        if (verifyInputEmpty()) {
            return false;
        } else {
            $("#error-name-register-dns, #error-value-record").hide();

            let domainId = $("#domain").val();
            loadOnTable('#table-body-new-records', '#new-registers-table');

            let formData = new FormData(document.getElementById('form-modal-add-domain-record'));
            formData.append('project', projectId);

            formData.append('domain', domainId);

            $.ajax({
                method: 'POST',
                url: '/api/project/' + projectId + '/domain/' + domainId + '/records',
                processData: false,
                contentType: false,
                cache: false,
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: formData,
                dataType: "json",
                error: function (response) {
                    $(".swal2-container, #modal-backdrop, #loaderLine").remove();
                    removeLoad();
                    errorAjaxResponse(response);
                    updateTableRecords(domainId);

                },
                success: function (response) {
                    $(".swal2-container").remove();
                    removeLoad();
                    alertCustom('success', response.message);
                    $('#name-register, #value-record').val('');
                    updateTableRecords(domainId);

                }
            });
        }

    });

    /**
     * Atualiza tabelas de records
     * @property response.data.domainRecords
     */
    function updateTableRecords(domainId) {
        loadOnTable('#table-body-new-records', '#new-registers-table');
        $.ajax({
            method: 'GET',
            url: '/api/project/' + projectId + '/domain/' + domainId + '/records',
            processData: false,
            contentType: false,
            cache: false,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            dataType: 'json',
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                $("#table-body-new-records").html('');
                tableRecords(response.data.domainRecords, domainId);

                $(".delete-domain-record").unbind('click');
                $(".delete-domain-record").on('click', function () {
                    if (isEmptyValue($(this).data('domain')) && isEmptyValue($(this).data('record'))) {
                        deleteRecord(projectId, $(this).data('domain'), $(this).data('record'));
                    }
                });

                $('.check-proxy').bind('change', function () {

                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }

                    if (!$(this).data('system')) {
                        $.ajax({
                            method: 'PUT',
                            url: '/api/project/' + projectId + '/domain/' + $(this).data('domain') + '/records/' + $(this).data('record'),
                            data: {
                                proxy: this.value
                            },
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function (response) {

                            }, success: function (response) {
                                alertCustom('success', response.message);
                            }
                        });
                    }
                });

            }
        });
    }

    /**
     * Monta tabela com todos os records atualizados
     * @param domainRecords
     * @param domainId
     * @param domainRecords.value.system_flag
     * @param domainRecords.value.domain_name
     */
    function tableRecords(domainRecords, domainId) {
        let data = '';
        let cont = 0;
        let proxyVar = 'checked';
        let disable = '';
        $.each(domainRecords, function (index, value) {
            data += '<tr>';
            data += '<td >' + value.type + '</td>';
            data += '<td >' + value.name + '</td>';
            data += '<td style="overflow-wrap: break-word;">' + value.content + '</,td>';

            if (!value.proxy) {
                proxyVar = '';
            } else {
                proxyVar = 'checked'
            }

            if (value.type === 'MX' || value.type === 'TXT') {
                disable = 'disabled';
            }

            if (!value.system_flag) {
                data += '<td><div class="switch-holder">' +
                    '<label class="switch">';

                data += '<input type="checkbox" value="' + value.proxy + '" name="proxy" id="proxy"  class="check check-proxy" data-domain="' + domainId + '" data-system="' + value.system_flag + '" data-record="' + value.id + '"  ' + proxyVar + '' + disable + '  >' +
                    '<span class="slider round"></span>' +
                    '</label>' +
                    '</div></td>';
                data += "<td><button style='background-color: transparent;' role='button' title='Excluir' class='btn mg-responsive delete-domain-record pointer' data-domain='" + domainId + "' data-system='" + value.system_flag + "' data-record='" + value.id + "'><i class='material-icons gradient'>delete_outline</i> </button></td>";

            } else {
                cont++;
                let enabledA = '';
                let enabledEntrada = 'disabled';
                if ((value.type === 'A' && value.name === value.domain_name) || (value.type === 'CNAME' && value.name === 'www') || (value.type === 'CNAME' && value.name.indexOf('mail.') === 0)) {
                    enabledA = "<td><button style='background-color: transparent;' role='button' title='Excluir' class='btn mg-responsive delete-domain-record pointer' data-domain='" + domainId + "' data-system='" + value.system_flag + "' data-record='" + value.id + "'><i class='material-icons gradient'>delete_outline</i> </button></td>";
                } else {
                    enabledA = "<td><button style='background-color: transparent;' role='button' class='btn mg-responsive pointer'  " + enabledEntrada + "><i class='material-icons gradient' >delete_outline</i> </a></td>";
                }
                data += '<td><div class="switch-holder" style=" opacity: 0.5;">' +
                    '                    <label class="switch" style="cursor: not-allowed">' +
                    '                        <input type="checkbox" style="cursor: not-allowed" value="' + value.proxy + '" name="proxy" id="proxy" class="check check-proxy" ' + proxyVar + '  ' + enabledEntrada + ' >' +
                    '                        <span class="slider round" style="cursor: not-allowed"></span>' +
                    '                    </label>' +
                    '                </div></td>';
                data += enabledA;

            }

            data += "</td>";
            data += '</tr>';
        });
        $("#loaderLine").remove();
        $("#new-registers-table").addClass('table-striped');
        if (cont > 0) {
            $("#empty-info").hide();
        } else {
            $("#empty-info").show();
        }
        $("#table-body-new-records").append(data);

    }

    /**
     * Deleta record
     * @param projectId
     * @param domain
     * @param record
     */
    function deleteRecord(projectId, domain, record) {
        if (isEmptyValue(projectId) && isEmptyValue(domain) && isEmptyValue(record)) {
            $.ajax({
                method: 'DELETE',
                url: '/api/project/' + projectId + '/domain/' + domain + '/records/' + record,
                processData: false,
                contentType: false,
                cache: false,
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                dataType: 'json',
                error: function (response) {
                    errorAjaxResponse(response);
                    $("#table-body-new-records").html('');
                    updateTableRecords(domain);

                },
                success: function (response) {
                    alertCustom('success', response.message);
                    $("#table-body-new-records").html('');
                    updateTableRecords(domain);
                }
            });

        } else {
            alertCustom('error', 'Ocorreu um erro, tente novamente mais tarde!');
        }
    }

    /**
     *
     */
    function verifyDataDomain() {
        $.ajax({
            method: 'GET',
            url: '/api/project/' + projectId + '/domains/' + $("#domain").val(),
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                $(".table-title-entry").remove();
                if (response.data.status === 3) {
                    $("#content-modal-recheck-dns").hide();
                    $("#modal-info-dsn-success-body, #content-modal-recheck-dns-success").show();

                } else {

                    var data = '';
                    $.each(response.data.zones, function (index, value) {
                        data += '<tr class="table-title-entry">' +
                            '<td class= "table-title" > <b>Novo servidor DNS: </b></td>' +
                            '<td> ' + value + ' </td>' +
                            '</tr>';
                    });

                    $("#table-zones-add").append(data);
                    if (response.data.domainHost) {
                        $("#nameHost").html(response.data.domainHost);
                    } else {
                        $("#nameHost").html('');
                    }
                    $("#content-modal-recheck-dns-success").hide();

                    $("#modal-title-dns-recheck, #modal-info-dsn-body, #content-modal-recheck-dns").show();
                }

                $("#modal-info-dns").modal('show');

            }
        });
    }

    /**
     * Mostra modal para validar entradas
     */
    $(".btn-continue-domain").on('click', function () {
        $("#modal-button-close-edit-domain-record").click();

        $("#content-modal-recheck-dns-error").hide();
        $("#content-modal-recheck-dns").show();
        verifyDataDomain();

    });

    /**
     * Recheck dominio
     */
    $(".btn-verify-domain").unbind('click');
    $(document).on('click', '.btn-verify-domain', function () {
        let domainId = $("#domain").val();
        $("#modal-title-dns-recheck").hide();

        loadOnModal('.content-dns');
        // #modal-info-dsn-body
        $.ajax({
            method: 'GET',
            url: '/api/project/' + projectId + '/domain/' + domainId + '/recheck',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                $(".swal2-container").remove();
                removeLoad();
                $("#loaderModal").remove();
                loadingOnScreenRemove();
                errorAjaxResponse(response);
                $("#content-modal-recheck-dns").hide();
                $("#content-modal-recheck-dns-error").show();

            },
            success: function (response) {
                $(".swal2-container").remove();
                removeLoad();
                $("#loaderModal").remove();
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                $("#content-modal-recheck-dns").hide();
                $("#modal-info-dsn-success-body, #content-modal-recheck-dns-success").show();
                updateDomains();
            }
        });

    });

    /**
     * Loader do titulo ao cadastrar dominio
     * @param whereToLoad
     */
    function loadOnModalDomainEspecial(whereToLoad) {

        $('#modal-title-add-domain').after('<h3 id="especialModalTitle" style="font-weight:bold; color:black"></h3>');
        $('#modal-title-add-domain').hide();
        loadOnAny(whereToLoad, false, {
            styles: {
                container: {
                    minHeight: '180px'
                }
            }
        });

        $('#especialModalTitle').html('Iniciando ... ');

        setTimeout(function () {
            $('#especialModalTitle').html('Configurando domínio');
        }, 1000);
        setTimeout(function () {
            $('#especialModalTitle').html('Configurando entradas DNS');
        }, 6000);
        setTimeout(function () {
            $('#especialModalTitle').html('Preparando servidores de Email');
        }, 13000);
        setTimeout(function () {
            $('#especialModalTitle').html('Preparando checkout transparente');
        }, 20000);
        setTimeout(function () {
            $('#especialModalTitle').html('Finalizando ... ');
        }, 25000);
    }

    /**
     * Presenter
     */
    let statusDomain = {
        1: 'warning',
        2: 'warning',
        3: 'success',
        4: 'danger'
    };

    /**
     * Não sei o que faz
     */
    $('.modal').on('hidden.bs.modal', function () {
        resetHtml();
        $('.modal-footer').css('display', '');
        $('#btn-modal').show();
        $('#modal-title').show();
        $('#especialModalTitle').remove();
    });

    /**
     * Não sei
     * @param whereToReset
     */
    function resetHtml(whereToReset) {
        $(whereToReset).html('');
    }
    function verifyCompanyDocuments(response) {
        if (response.data[0].document_status == 'approved') {
            $('#div-recheck-dns').html("<button class='btn btn-success btn-verify-domain' domain='' style='font-size: 25px;'>Verificar</button>")
        } else {
            $('#div-recheck-dns').html('<span class="table-title">A aprovação do domínio só ficará disponível quando seus documentos e da sua empresa estiverem aprovados</span>');
        }
    }
});
