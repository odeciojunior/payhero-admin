<a class="list-group-item dropdown-item" href="/sales" role="menuitem" id='item-notification'>
    <div class="media">
        <div class="pr-10">
            <i class="icon wb-shopping-cart bg-green-600 green icon-circle" sty aria-hidden="true"></i>
        </div>
        <div class="media-body">
            <h6 class="media-heading">
                {{$notification->data['qtd']}} {{ $notification->data['qtd'] > 1 ? ' novas vendas' : ' nova venda' }}
            </h6>
            <time class="media-meta" datetime="2018-06-11T18:29:20+08:00">{{ date('d/m/Y H:m:s', strtotime($notification->updated_at)) }}</time>
        </div>
    </div>
</a>

