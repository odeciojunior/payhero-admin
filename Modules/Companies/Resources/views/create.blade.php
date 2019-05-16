@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar nova empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/empresas">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <div class="row" style="margin-bottom: 30px">
                    <div class="col-3">
                        <label for="country">Company country</label>
                        <select id="country" class="form-control">
                            <option value="usa">United States</option>
                            <option value="brazil">Brasil</option>
                        </select>
                    </div>
                </div>
                <div id="store_form" style="width:100%">
                </div>
            </div>
        </div>
    </div>

  <script>

    $(document).ready( function(){

        updateForm();

        $("#country").on("change", function(){
            updateForm();
        });

        function updateForm(){

            $("#store_form").html('');

            $.ajax({
                method: "GET",
                url: "/empresas/getformcadastrarempresa/"+$("#country").val(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    $('.loading').css("visibility", "hidden");
                },
                success: function(data){
                    $('.loading').css("visibility", "hidden");
                    $("#store_form").html(data);

                    $("#routing_number").on("blur", function(){

                        $.ajax({
                            method: "GET",
                            url: "https://www.routingnumbers.info/api/data.json?rn="+$("#routing_number").val(),
                            success: function(data){
                                if(data.message == 'OK'){
                                    $("#bank").val(data.customer_name);
                                }
                                else{
                                    alert(data.message);
                                }
                            }
                        });
                    });
                },
            });
        }

    });

  </script>


@endsection

