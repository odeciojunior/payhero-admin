$(() => {

    // MODAL DETALHES DA VENDA
    $(document).on('click', '.contetation_file', function () {
        let contestation = $(this).attr('contestation');
        loadOnAny('#modal-contestationFiles');
        $('#modal_contestation_files').modal('show');
        $('#modal-contestationFiles').show();


        $.ajax({
            method: "GET",
            url: '/contestations/get-contestation-files/' + contestation,
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
                console.log(response, 123)
            },
            complete: function(data) {
                loadOnAny('#modal_contestation_files', true);
            }
        });



    });

});
