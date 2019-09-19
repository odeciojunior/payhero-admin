@extends("layouts.master")
@push('css')
    <style type='text/css'>
        /* SWITCH CONFIG */
        label.switch {
            margin-bottom: 0 !important;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 15px;
            margin-right: 15px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: -3px;
            top: -2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);
        }
        input:checked + .slider {
            background-color: #f78d1e;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #f78d1e;
        }
        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }
        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endpush
@section('content')
    <div id='project-content'>
        <div class='page'>
            <div class="page-header container">
                <div class="row jusitfy-content-between">
                    <div class="col-lg-8">
                        <h1 class="page-title">Integrações com HotZapp</h1>
                    </div>
                    <div class="col text-right">
                        <a id='btn-add-integration' class="btn btn-floating btn-danger" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                            <i class="icon wb-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class='page-content container' id='project-integrated'>
            @include('hotzapp::include')
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="/modules/hotzapp/js/index.js"></script>
    @endpush
@endsection
