<div class="transition-details">
    <p class="sm-text text-muted">
    </p>
    <div class="status d-inline">
    </div>
    <div class="row justify-content-center">
        <div class="col">
            <h4> Detalhes: </h4>

            <div class="panel-group my-30" aria-multiselectable="true" role="tablist">
                <div class="panel bg-grey-100 panel-result-postback-{{ $contestationsDetails->id }}">
                    <div class="panel-heading" id="exampleHeadingDefaultOne" role="tab">
                        <a class="panel-title" data-toggle="collapse"
                           href="#result-postback-{{ $contestationsDetails->id }}"
                           data-parent="#exampleAccordionDefault" aria-expanded="true"
                           aria-controls="exampleCollapseDefaultOne">
                            <strong>Detalhe - {{ $contestationsDetails->id }}</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="result-postback-{{ $contestationsDetails->id }}"
                         aria-labelledby="exampleHeadingDefaultOne" role="tabpanel">
                        <div id="pre-postback-send-data-{{ $contestationsDetails->id }}" class="panel-body ">
                            <h4>Resultado:</h4>
                            <pre class="pre-postback-send-data-{{ $contestationsDetails->id }}">
                                {!! $contestationsDetails->data !!}
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<script>

    @if($contestationsDetails->data)
        $('.' + 'pre-postback-send-data-' + {{ $contestationsDetails->id }}).html(beautifyJson(JSON.parse({!! json_encode($contestationsDetails->data) !!})) );
    @endif
</script>