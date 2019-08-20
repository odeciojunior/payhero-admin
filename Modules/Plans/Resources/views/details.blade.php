<div class='container-fluid'>
    <table class='table table-bordered table-striped table-hover table-responsive' style='overflow-x: auto !important;'>
        <tbody>
            <tr>
                <th style='width:40%;' class='text-center'>Nome:</th>
                {{--                <td style='width: 20px'></td>--}}
                <td class='text-left'>{{$plan->name}}</td>
                <br>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Descrição:</th>
                {{--                <td style='width: 20px'></td>--}}
                <td class='text-left'>{{$plan->description}}</td>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Código:</th>
                {{--                <td style='width: 20px'></td>--}}
                <td class='text-left'>{{$plan->code}}</td>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Preço:</th>
                {{--                <td style='width: 20px'></td>--}}
                <td class='text-left'>{{$plan->price}}</td>
            </tr>
            <tr>
                <th style='width:40%;' class='text-center'>Status:</th>
                {{--                <td style='width: 20px'></td>--}}
                <td class='text-left'>
                    {{--                    @dd($plan->projectId->domains[0]->name)--}}
                    @if(!empty($plan->projectId->domains[0]->name))
                        <span class='badge badge-success text-left'>Ativo</span>
                    @else
                        <span class='badge badge-danger'>Desativado</span>
                    @endif
                    {{--@if($plan->status == 1)--}}
                    {{--<span class='badge badge-success text-left'>Ativo</span>--}}
                    {{--@else--}}
                    {{--<span class='badge badge-danger'>Desativado</span>--}}
                    {{--@endif--}}
                </td>
            </tr>
        </tbody>
    </table>
    <table class='table table-bordered table-striped table-hover mt-2 text-center'>
        <tr>
            <th>Produto:</th>
            <th>Quantidade:</th>
        </tr>
        @foreach($plan->productsPlans as $productPlan)
            <tr>
                <td>{{$productPlan->getProduct->name}}</td>
                <td>{{$productPlan->amount}}</td>
            </tr>
        @endforeach
    </table>
</div>
