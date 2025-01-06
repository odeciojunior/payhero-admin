<div class="form-group col-12">
    <label for="{{$id}}" class="form-label">
        {{$label}}
    </label>
    <div class="input-group">
        <input type="text"
               id="{{$id}}"
               name="{{$name}}"
               value="{{$value}}"
               class="form-control"
               readonly
               placeholder="{{$placeholder}}"
               aria-describedby="btn-copy-{{$id}}"
        >
        <button class="btn btn-primary btn-outline-secondary btn-copy"
                type="button"
                id="btn-copy-{{$id}}"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{$tooltip}}"
        >
            <img src="{{asset('/build/global/img/icon-copy-b.svg')}}" alt="image copy">
        </button>
    </div>
    @if(!is_null($description))
        <small class="text-muted">
            {{ $description }}
        </small>
    @endif
</div>
