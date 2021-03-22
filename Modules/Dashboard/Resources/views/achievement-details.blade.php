@push('css')
    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/achievement-details.css?v=01') }}">
@endpush

<div id="modal-achievement" class="modal fade modal-fade-in-scale-up show">
    <div id="achievement-details" class="modal-dialog modal-simple achievement-details-style">
        <div class="modal-content">
            <div class="modal-header flex-wrap">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-target="#modal-achievement">
                    <span aria-hidden="true" class="material-icons">close</span>
                </button>
                <div class="w-p100">
                    <img id="icon" src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/nivel-2.png" alt="Image">
                </div>
            </div>
            <div class="modal-body">

                <div id="description"><strong id="description-level"></strong></div>
                <div id="name"></div>
                <div id="storytelling"></div>

                <div id="reward">
                    <div id="reward-title"></div>
                    <div class="d-flex justify-content-center align-items-center">
                        <span class="material-icons">done</span> <span id="reward-data"></span>
                    </div>
                </div>

                <div id="reward-check" class="btn btn-primary">Ok, legal!</div>
            </div>
        </div>
    </div>
</div>
