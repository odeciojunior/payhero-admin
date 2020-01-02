<!doctype html>
<html lang="pt-BR" class="h-100">
<head>
    <title>Rastreamento</title>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(90deg, #fafafa 36px, transparent 1%) center, linear-gradient(#fafafa 36px, transparent 1%) center, #e5e5e5;
            background-size: 40px 40px;
        }

        .table {
            background-color: #ffffff;
        }

        .tracking-icon {
            background-color: #e5e5e5;
            width: 80px;
            height: 80px;
            padding: 9px;
            border-radius: 40px;
            border: 6px solid #cfd2d4;
        }

        .tracking-icon i {
            font-size: 50px;
            color: #999999;
        }

        .line {
            height: 5px;
            width: 100%;
            background: #cfd2d4;
            margin-top: 37px;
        }

        @media (max-width: 500px) {
            .tracking-icon {
                width: 50px;
                height: 50px;
                padding: 9px;
                border-width: 4px;
                border-radius: 25px;
            }

            .tracking-icon i {
                font-size: 24px;
            }

            .line {
                margin-top: 24px;
                height: 3px;
            }
        }

        .tracking-icon.active {
            background-color: #47c1bf;
            border-color: #b6eceb;
        }

        .tracking-icon.active + .line {
            background: #b6eceb;
        }

        .tracking-icon.exception {
            background-color: #f49342;
            border-color: #ffc48b;
        }

        .tracking-icon.exception + .line {
            background: #ffc48b;
        }

        .tracking-icon.exception i,
        .tracking-icon.active i {
            color: #ffffff;
        }

        .loader {
            width: 75px;
            height: 75px;
            margin: 150px auto;
            border-top: solid #47c1bf;
            border-right: solid #47c1bf;
            border-bottom: solid #47c1bf;
            border-left: solid transparent;
            border-width: 7px;
            border-radius: 50%;
            -webkit-animation: spin 1.1s infinite linear;
            animation: spin 1.1s infinite linear;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body class="h-100">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="div-tracking-info" style="display:none;">
                <h3 class="mb-5 text-center" id="tracking-code">Objeto #PM441390955BR</h3>
                <div class="justify-content-between mb-5" style="display: flex;">
                    <div class="tracking-icon active">
                        <i class="material-icons" style="font-size: 38px; margin: 6px;">markunread_mailbox</i>
                    </div>
                    <div class="line"></div>
                    <div class="tracking-icon">
                        <i class="material-icons">local_shipping</i>
                    </div>
                    <div class="line"></div>
                    <div class="tracking-icon">
                        <i class="material-icons">arrow_right_alt</i>
                    </div>
                    <div class="line"></div>
                    <div class="tracking-icon">
                        <i class="material-icons">check_circle</i>
                    </div>
                </div>
                <div class="row" id="div-checkpoints">
                    <div class="col">
                        <table class="table table-striped">
                            <thead class="thead-dark">
                            <tr>
                                <th>Data</th>
                                <th>Evento</th>
                            </tr>
                            </thead>
                            <tbody id="table-tracking">
                            </tbody>
                        </table>
                        <button class="btn bg-dark text-white text-uppercase float-right" id="btn-see-more">Ver mais
                        </button>
                    </div>
                </div>
            </div>
            <div class="row" id="div-unavailable" style="display: none">
                <div class="col pt-5 text-center">
                    <h3 class="mb-5">O rastreamento não está disponível</h3>
                    <svg version="1.1" width="150px" height="150px" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512">
                        <path style="fill:#d69a73;" d="M248.768,1.654L19.499,112.336c-2.875,1.388-4.701,4.298-4.701,7.49v272.348
                            c0,3.192,1.827,6.102,4.701,7.49l229.269,110.682c4.569,2.206,9.895,2.206,14.464,0l229.269-110.682
                            c2.875-1.388,4.701-4.298,4.701-7.49V119.826c0-3.192-1.827-6.102-4.701-7.49L263.232,1.654
                            C258.663-0.551,253.337-0.551,248.768,1.654z"/>
                        <path style="fill:#7d5c48;" d="M16.181,115.273c-0.874,1.331-1.383,2.898-1.383,4.553v272.348c0,3.192,1.827,6.102,4.701,7.49
                            l229.269,110.681c2.284,1.103,4.758,1.654,7.232,1.654V231.048L16.181,115.273z"/>
                        <path style="fill:#a77a5d;" d="M495.819,115.273c0.874,1.331,1.383,2.898,1.383,4.553v272.348c0,3.192-1.827,6.102-4.701,7.49
                            L263.232,510.345C260.948,511.448,258.474,512,256,512V231.048L495.819,115.273z"/>
                        <path style="fill:#FFF6D8;" d="M83.407,377.657c-1.21,0-2.445-0.268-3.606-0.828L44.404,359.74
                            c-4.142-1.998-5.872-6.969-3.874-11.103c1.99-4.134,6.977-5.856,11.103-3.874l35.397,17.09c4.142,1.998,5.872,6.969,3.874,11.103
                            C89.475,375.927,86.502,377.657,83.407,377.657z"/>
                        <path style="fill:#FFF6D8;" d="M115.986,356.45c-1.21,0-2.445-0.268-3.606-0.828l-67.928-32.79
                            c-4.134-1.998-5.872-6.969-3.874-11.103c1.99-4.126,6.985-5.856,11.103-3.874l67.927,32.79c4.134,1.998,5.872,6.969,3.874,11.103
                            C122.054,354.719,119.081,356.45,115.986,356.45z"/>
                        <polygon style="fill:#64d8d6;"
                                 points="414.549,154.373 173.887,38.11 110.447,68.86 351.649,185.303 "/>
                        <path style="fill:#47c1bf;" d="M351.649,185.303v90.207c0,3.066,3.205,5.078,5.967,3.745l52.232-25.215
                            c2.875-1.388,4.701-4.298,4.701-7.49v-92.176L351.649,185.303z"/>
                    </svg>
                </div>
            </div>
            <div class="loader"></div>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
<script>

    $(document).ready(function () {

        let trackingCode = $(window.location.pathname.split('/')).get(-1);
        let checkpoints = [];

        function isEmpty(obj) {
            return Object.keys(obj ? obj : {}).length === 0;
        }

        $.ajax({
            url: '/api/tracking/detail/' + trackingCode,
            error: response => {
                $('.loader').hide();
                $('#div-unavailable').show();
            },
            success: response => {
                if (response.data) {

                    $('#tracking-code').text('Objeto #' + response.data.tracking_code);
                    $('title').html('Rastreamento - ' + response.data.tracking_code);

                    checkpoints = response.data.checkpoints;

                    if(checkpoints.length <= 3){
                        $('#btn-see-more').hide();
                    }

                    let max = checkpoints.length > 3 ? 3 : checkpoints.length;

                    for (let i = 0; i < max; i++) {
                        let data = `<tr>
                                    <td>${checkpoints[i].created_at}</td>
                                    <td>${checkpoints[i].event}</td>
                                </tr>`;
                        $('#table-tracking').append(data);
                    }

                    let dispatched = 0;
                    let out_for_delivery = 0;
                    let delivered = 0;
                    let exception = 0;

                    for (let checkpoint of checkpoints) {
                        switch (checkpoint.tracking_status_enum) {
                            case 2: //dispatched
                                dispatched = 1;
                                break;
                            case 4: // out_for_delivery
                                out_for_delivery = 1;
                                break;
                            case 3: //delivered
                                delivered = 1;
                                break;
                            case 5: //exception
                                exception = 1;
                                break;
                        }
                    }

                    let position = 0;

                    if (delivered) {
                        $('.tracking-icon').addClass('active');
                    } else if (out_for_delivery) {
                        $('.tracking-icon').eq(2).addClass('active');
                        $('.tracking-icon').eq(1).addClass('active');
                        if (exception) position = 2;
                    } else if (dispatched) {
                        $('.tracking-icon').eq(1).addClass('active');
                        if (exception) position = 1;
                    }

                    if (position) {
                        $('<div class="tracking-icon exception"><i class="material-icons">error</i></div><div class="line"></div>').insertAfter($('.line').eq(position));
                    }

                    $('.loader').hide();
                    $('#div-tracking-info').show();
                } else {
                    $('.loader').hide();
                    $('#div-unavailable').show();
                }
            }
        });

        $('#btn-see-more').on('click', function () {
            for (let i = 3; i < checkpoints.length; i++) {
                let data = `<tr>
                            <td>${checkpoints[i].created_at}</td>
                            <td>${checkpoints[i].event}</td>
                        </tr>`;
                $('#table-tracking').append(data);
            }
            $(this).hide();
        });
    });
</script>
</body>
</html>
