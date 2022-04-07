<form id="finances-settings-form">
    <div class="card shadow card-tabs p-20">
        <div class="row justify-content-start align-items-center">
            <div class="col-12 col-sm-8 text-left">
                <h5 class="title-pad">Configurações</h5>
                <p class="p-0 m-0">Configure as finanças do seu negócio</p>
            </div>
            <div class="col-md-4">
                <div class="input-holder form-group">
                    <select style='border-radius:10px' class="form-control select-pad" name="company" id="settings_company_select"></select>
                </div>
            </div>
            <div class="row d-contents d-md-flex align-items-start p-20">
                <div class="col-12 col-md-6 mb-50">
                    <div class="card bg-light no-shadow mt-30">
                        <div class="card-body">
                            <h5 class="title-pad">
                                Saque automático por período
                                <label class="switch" style='float: right; top:3px'>
                                    <input type="checkbox" id="withdrawal_by_period" name="withdrawal_by_period" class='check'>
                                    <span class="slider round"></span>
                                </label>
                            </h5>
                            <p class="p-0 m-0">
                                Crie um saque automático de frequência diária, semanal ou mensal.
                                <br/>
                                O valor será automaticamente solicitado quando superior a R$ 100,00.
                            </p>
                            <br/>
                            <p class="mb-0">Frequência</p>
                            <div class="frequency-container py-10 d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center">
                                <button type="button" data-frequency="daily" class="btn btn-block m-0 mr-5 py-10">
                                    Diário
                                </button>

                                <button type="button" data-frequency="weekly" class="btn btn-block m-0 mx-5 py-10">
                                    Semanal
                                </button>

                                <button type="button" data-frequency="monthly" class="btn btn-block m-0 ml-5 py-10">
                                    Mensal
                                </button>
                            </div>

                            <div class="weekdays-container d-flex flex-wrap flex-md-nowrap align-items-center justify-content-between mt-20">
                                <button type="button" class="btn py-15" data-weekday="1">
                                    SEG
                                </button>
                                <button type="button" class="btn py-15" data-weekday="2">
                                    TER
                                </button>
                                <button type="button" class="btn py-15" data-weekday="3">
                                    QUA
                                </button>
                                <button type="button" class="btn py-15" data-weekday="4">
                                    QUI
                                </button>
                                <button type="button" class="btn py-15" data-weekday="5">
                                    SEX
                                </button>
                                <button type="button" class="btn py-15" data-weekday="6">
                                    SAB
                                </button>
                                <button type="button" class="btn py-15" data-weekday="0">
                                    DOM
                                </button>
                            </div>
                            <div class="day-container d-none flex-wrap flex-md-nowrap align-items-center justify-content-between mt-20">
                                @foreach (['01', '05', '10', '15', '20', '25', '30'] as $day)
                                    <button type="button" class="btn py-15" data-day="{{$day}}">
                                        {{$day}}
                                    </button>
                                @endforeach
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-md-5">
                                    <button type="submit" class="btn btn-block btn-success btn-success-1 py-10 px-15">
                                        <img style="height: 12px; margin-right: 4px" src=" {{ mix('build/global/img/svg/check-all.svg') }} ">
                                        &nbsp;Salvar&nbsp;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-lighter no-shadow mt-30">
                        <div class="card-body">
                            <h5 class="title-pad">
                                Saque automático por valor
                                <label class="switch" style='float: right; top:3px'>
                                    <input type="checkbox" id="withdrawal_by_value" name="withdrawal_by_value" class='check'>
                                    <span class="slider round"></span>
                                </label>
                            </h5>
                            <p class="p-0 m-0">
                                Crie um saque automático quando o saldo disponível for
                                superior ao valor informado abaixo.
                                <br/>O valor deve ser superior a R$ 100,00.
                            </p>
                            <br/>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$</span>
                                </div>
                                <input id="withdrawal_amount" name="withdrawal_amount" type="text" class="form-control" aria-label="Valor mínimo para saque">
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-md-5">
                                    <button type="submit" class="btn btn-block btn-default py-10 px-15">
                                        <img style="height: 12px; margin-right: 4px" src=" {{ mix('build/global/img/svg/check-all.svg') }} ">
                                        &nbsp;Salvar&nbsp;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
