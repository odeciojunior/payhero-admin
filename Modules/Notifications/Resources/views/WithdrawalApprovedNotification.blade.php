<a class="list-group-item dropdown-item" href="/finances" role="menuitem" id='item-notification' style='width:100%;@if($notification->read_at == null) background-color:#b5e0ee5e @endif'>
    <div class="media">
        <div class="pr-10" style='margin:auto'>
            <i class="icon wb-shopping-cart bg-green-600 green icon-circle" aria-hidden="true"></i>
        </div>
        <div class="media-body">
            <h6 class="media-heading" style='white-space:normal'>
                {{$notification->data['message']}}
            </h6>
            <time class="media-meta" datetime="2018-06-11T18:29:20+08:00">{{ date('d/m/Y H:m:s', strtotime($notification->updated_at)) }}</time>
        </div>
    </div>
</a>

