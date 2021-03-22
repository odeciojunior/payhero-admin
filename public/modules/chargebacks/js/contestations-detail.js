$(() => {
    let contestation = '';
    // MODAL DETALHES DA VENDA
    $(document).on('click', '.contetation_file', function () {
        contestation = $(this).attr('contestation');
        loadOnAny('#modal-contestationFiles');
        $('#modal_contestation_files').modal('show');
        $('#modal-contestationFiles').show();


        $.ajax({
            method: "GET",
            url: '/contestations/' + contestation,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                $('#modal_contestation_files').modal('hide');
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#sale_hash").html(response.data.sale_code)
                $("#contestation").html(response.data.sale_code)
                $("#payment-type").html(response.data.sale_payment_method === 2 ? 'Boleto' : 'Cartão ' )
                $("#payday").html(response.data.sale_start_date)
                $("#company").html(response.data.company)
                $("#liberation").html(response.data.sale_release_date)
                $("#request_date").html(response.data.request_date)
                $("#reason").html(response.data.reason)

                $('#latest_files').html("");

                $.each(response.data.files, function (index, value) {

                    let data = `<tr id="${value.id}">
                        <td>
                            ${value.type_str}
                        </td>
                        <td>
                              <a href="${value.file}" target="_blank">Arquivo</a>
                        </td>
                        <td>
                            ${value.created_at}
                        </td>
                         <td>
                             <button type="button" title="Apagar arquivo" data-removeroute="${value.remove_route}" class="btn btn-danger btn-remove-file"  data-contestationfile="${value.id}"><i class="fa fa-trash-o"></i></button>
                        </td>
                     </tr>`;

                    $('#latest_files').append(data);

                });

            },
            complete: function(data) {
                loadOnAny('#modal_contestation_files', true);
            }
        });

    });

    $("#sendfilesform").on('submit', function(e){
        e.preventDefault();

        $('#update-contestation-observation').prop("disabled", true);
        loadOnAny('#latest_files');

        var formData = new FormData($('#sendfilesform')[0]);

        $('input[type="file"]').on('change', function (e) {
            [].forEach.call(this.files, function (file) {
                formData.append('files[]', file);
            });
        });

        var url = $("#sendfilesform").attr('action');
        formData.append('contestation', contestation);
        formData.append('type', $("#type").val());
        $("#multiplefiles").empty()

        $.ajax({
            type: "POST",
            data: formData,
            url: url,
            contentType: false,
            processData: false,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: function(response)
            {

                $('#latest_files').html('');

                $.each(response.data, function (index, value) {
                    let data = `<tr id="${value.id}">
                        <td>
                            ${value.type_str}
                        </td>
                        <td>
                            <a href="${value.file}" target="_blank">Arquivo</a>
                        </td>
                        <td>
                            ${value.created_at}
                        </td>
                         <td>
                            <button type="button" title="Apagar arquivo" data-removeroute="${value.remove_route}" class="btn btn-danger btn-remove-file"  data-contestationfile="${value.id}"><i class="fa fa-trash-o"></i></button>
                        </td>
                     </tr>`;

                    $('#latest_files').append(data);

                });

            },error: function(response) {
                console.log(response)
            },complete: function(data) {
                loadOnAny('#latest_files', true);
                $('#update-contestation-observation').prop("disabled", false);
            }
        });

    });



    $(document).on("click", "button.btn-remove-file" , function(e) {
        e.preventDefault();
        $(this).prop("disabled", true);
        let contestationfile = $(this).data("contestationfile")
        let removeroute = $(this).data("removeroute")
        loadOnAny('#latest_files');

        $.ajax({
            url: removeroute,
            type: 'GET',
            contentType: 'application/json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            success: function (data) {
                $("#"+contestationfile).remove()
            },error: function(response) {
                console.log(response)
            },complete: function(data) {
                loadOnAny('#latest_files', true);
                $(this).prop("disabled", false);
            }
        });

    });

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
                    $("#page_header").show()
                } else {
                    $("#page_header").hide()
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }
                loadingOnScreenRemove();
            }
        });
    }
});
