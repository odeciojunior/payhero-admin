<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center justify-content-end'>
            <div id="add-plan" class="d-flex align-items-center justify-content-end pointer">
                <span class="link-button-dependent red"> Adicionar Plano </span>
                <a class="ml-10 rounded-add pointer"><i class="icon wb-plus" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-plans' class='table text-left table-pixels table-striped' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title' width='300px'>Nome</td>
                        <td class='table-title' width='300px'>Descrição</td>
                        <td class='table-title' width='300px'>Link</td>
                        <td class='table-title' width='300px'>Preço</td>
                        <td class='table-title' width='300px'>Status</td>
                        <td class='table-title text-center' width='200px'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-plan'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-plans" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
    {{-- js pagination carrega --}}
</ul>
<div class="modal fade modal-3d-flip-vertical" id="modal-plans-error" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1" {{--style='display:none;'--}}>
    <div class="modal-dialog modal-simple">
        <div id="modal-not-products" class='modal-content p-10'>
            <div class='header-modal simple-border-bottom'>
                <h2 id='modal-title-plans-erro' class='modal-title-plans-erro'>Ooooppsssss!</h2>
            </div>
            <div class='modal-body simple-border-bottom' style='padding-bottom:1%; padding-top:1%;'>
                <div class='swal2-icon swal2-error swal2-animate-error-icon' style='display:flex;'>
                                <span class='swal2-x-mark'>
                                    <span class='swal2-x-mark-line-left'></span>
                                    <span class='swal2-x-mark-line-right'></span>
                                </span>
                </div>
                <h3 align='center'>Você não cadastrou nenhum produto</h3>
                <h5 align='center'>Deseja cadastrar uma produto?
                    <a class='red pointer' href='/products/create'>clique aqui</a>
                </h5>
            </div>
            <div style='width:100%; text-align:center; padding-top:3%;'>
                <span class='btn btn-danger' data-dismiss='modal' style='font-size: 25px;'>Retornar</span>
            </div>
        </div>
    </div>
</div>
