$(function () {

    var projectId = $(window.location.pathname.split('/')).get(-2);

    var pageCurrent;

    $('#tab_links').on('click', function () {
        index();
    });

    $("#btn-search-link").on('click', function () {
        index();
    });

    /**
     * Update Table Link
     */
    function index() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        pageCurrent = link;

        loadOnTable('#data-table-link', '#table-links');
        if (link == null) {
            link = '/api/affiliates/affiliatelinks/' + projectId;

        } else {
            link = '/api/affiliates/affiliatelinks/' + projectId + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            data: {
                plan: $("#plan-name").val()
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (_error2) {
                function error() {
                    return _error2.apply(this, arguments);
                }

                error.toString = function () {
                    return _error2.toString();
                };

                return error;
            }(function (response) {
                $("#data-table-link").html('Erro ao encontrar dados');
                errorAjaxResponse(response);

            }),
            success: function success(response) {

                if (isEmpty(response.data)) {
                    $("#data-table-link").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                    $('#table-links').addClass('table-striped');

                } else {
                    $("#data-table-link").html('');

                    if (response.data[0].document_status == 'approved') {
                        $.each(response.data, function (index, value) {
                            data = '';
                            data += '<tr>';
                            if(value.link_plan == null) {
                                data += '<td class="display-sm-none display-m-none copy_link_plan" title="Copiar Link" style="cursor:pointer;" link="' + value.link_project + '">';
                                data += value.link_project + ' <br><small>('+ value.project_name +')</small> </td>';
                                data += '<td class="display-lg-none display-xlg-none" title="Copiar Link"><a class="material-icons pointer gradient copy_link_plan" link="'+value.link_project+'"> file_copy</a></td>';
                            } else {
                                data += '<td class="display-sm-none display-m-none copy_link" title="Copiar Link" style="cursor:pointer;" link="' + value.link_plan + '">';
                                data += value.link_plan + ' <br><small>('+ value.plan_name + value.description +')</small> </td>';
                                data += '<td class="display-lg-none display-xlg-none" title="Copiar Link"><a class="material-icons pointer gradient copy_link_plan" link="'+value.link_plan+'"> file_copy</a></td>';
                            }
                            if(value.link_affiliate == null) {
                                data += '<td class="display-sm-none display-m-none copy_link" title="Copiar Link" style="cursor:pointer;" link="' + value.link + '">' + value.link + '</td>';
                                data += '<td class="display-lg-none display-xlg-none" title="Copiar Link"><a class="material-icons pointer gradient copy_link" link="'+value.link+'"> file_copy</a></td>';
                            } else {
                                data += '<td class="display-sm-none display-m-none copy_link" title="Copiar Link" style="cursor:pointer;" link="' + value.link_affiliate + '">' + value.link_affiliate + '</td>';
                                data += '<td class="display-lg-none display-xlg-none" title="Copiar Link"><a class="material-icons pointer gradient copy_link" link="'+value.link_affiliate+'"> file_copy</a></td>';
                            }

                            data += '<td class="text-center" >' + value.price + '<br><small>('+ value.commission +' commissão)<small></td>';
                            data += '<td></td>'
                            data += '</tr>';
                            $("#data-table-link").append(data);
                            $('#table-links').addClass('table-striped');
                        });

                        pagination(response, 'links', index);
                    } else {
                        $("#data-table-link").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Link de pagamento só ficará disponível quando seus documentos e da sua empresa estiverem aprovados</td></tr>");
                        $('#table-links').addClass('table-striped');
                    }

                }
            }
        });

        $(".table-links").on("click", ".copy_link", function () {
            var temp = $("<input>");
            $("#table-links").append(temp);
            temp.val($(this).attr('link')).select();
            document.execCommand("copy");
            temp.remove();
            alertCustom('success', 'Link copiado!');
        });

        $(".table-links").on("click", ".copy_link_plan", function () {
            var temp = $("<input>");
            $("#table-links").append(temp);
            temp.val($(this).attr('link')).select();
            document.execCommand("copy");
            temp.remove();
            alertCustom('success', 'Link copiado!');
        });
    }
})
;
