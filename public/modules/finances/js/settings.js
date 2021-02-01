$(document).ready(function () {

    //Get companies list
    function getCompanies() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/companies?select=true",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (isEmpty(response.data)) {
                    loadingOnScreenRemove();
                    return;
                }

                let dataHtml = '';
                $(response.data).each(function (index, value) {
                    dataHtml += `<option country="${value.country}" value="${value.id}">${value.name}</option>`;
                });
                $("#settings_company_select").append(dataHtml);

                //Company withdrawal settings
                getSettings($("#settings_company_select").val())
                loadingOnScreenRemove();
            }
        });
    }

    getCompanies();

    //Settings

    const SETTINGS_FREQUENCY_DAILY = 'daily'
    const SETTINGS_FREQUENCY_WEEKLY = 'weekly'
    const SETTINGS_FREQUENCY_MONTHLY = 'monthly'
    const SETTINGS_RULE_PERIOD = 'period'
    const SETTINGS_RULE_AMOUNT = 'amount'

    var settingsData = {
        company_id: null,
        rule: null,      //rules: period, amount
        frequency: null, //frequency: daily, weekly, monthly
        weekday: null,   //from 0 (monday) to 6 (sunday) as mysql weekday() function
        day: null,       //day of month
        amount: 0,       //minimal amount to make withdrawal
    }

    var financesSettingsForm = $('#finances-settings-form')
    var withdrawalCompanySelect = financesSettingsForm.find('settings_company_select')
    var withdrawalByPeriod = $('#withdrawal_by_period')
    var frequencyContainer = $('.frequency-container')
    var frequencyButtons = frequencyContainer.find('.btn')
    var weekdaysContainer = $('.weekdays-container')
    var weekdaysButtons = weekdaysContainer.find('.btn')
    var dayContainer = $('.day-container')
    var withdrawalByAmount = $('#withdrawal_by_value')
    var withdrawalAmount = $('#withdrawal_amount')

    var getSettings = function (companyId, settingsId = null, notify = false) {
        clearSettingsForm()
        $.ajax({
            method: "GET",
            url: '/api/withdrawals/settings/' + companyId + (settingsId ? '/' + settingsId : ''),
            //data: data,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                if (notify) {
                    alertCustom('error', 'Nenhuma configuração de saque automático encontrada para a empresa selecionada')
                }
                clearSettingsForm()
            },
            success: response => {
                settingsData = response.data
                fillSettingsForm(settingsData)
            }
        });
    }

    var saveSettings = function (data) {

        settingsData = Object.assign(settingsData, {
            company_id: financesSettingsForm.find('#settings_company_select').val(),
            amount: withdrawalAmount.val(),
            day: dayContainer.find('select').val()
        })

        if (!validateSettingsData(data)) {
            alertCustom('error', 'Dados inválidos, verifique novamente as Configurações de Saque Automático')
            return false;
        }

        var method = !settingsData.id ? 'POST' : 'PUT';
        var resourceId = !settingsData.id ? '' : '/' + settingsData.id;

        $.ajax({
            method: method,
            url: '/api/withdrawals/settings' + resourceId,
            data: data,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                settingsData = Object.assign(settingsData, response.data)
                alertCustom('success', response.message)
            }
        });
    }

    var deleteSettings = function (id) {
        if (!id) {
            return false;
        }

        $.ajax({
            method: "DELETE",
            url: '/api/withdrawals/settings/' + id,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                settingsData = Object.assign(settingsData, {
                    id: null,
                    rule: null,
                    frequency: null,
                    weekday: null,
                    day: null,
                    amount: 0,
                })
                alertCustom('success', response.message)
            }
        });
    }

    var validateSettingsData = function (settingsData) {
        if (settingsData.rule === SETTINGS_RULE_PERIOD) {
            if (!settingsData.frequency) return false
            if (settingsData.frequency === SETTINGS_FREQUENCY_WEEKLY && settingsData.weekday === null) return false
            if (settingsData.frequency === SETTINGS_FREQUENCY_MONTHLY && !settingsData.day) return false
        }
        let amount = Number.parseInt(settingsData.amount.replace(/\D/g, ''))
        if (settingsData.rule === SETTINGS_RULE_AMOUNT && amount < 10000) return false

        return true
    }

    var fillSettingsForm = function (data) {
        if (data?.rule === SETTINGS_RULE_PERIOD) {
            withdrawalByPeriod.prop('checked', true).trigger('change')
            frequencyButtons.each((i, el) => {
                el = $(el)
                if (el.data('frequency') === data.frequency) el.addClass('active')
            })

            if (data.frequency === SETTINGS_FREQUENCY_DAILY) {
                weekdaysButtons.addClass('active')
                weekdaysContainer.addClass('d-flex').removeClass('d-none')
                dayContainer.addClass('d-none').removeClass('d-flex')
            } else if (data.frequency === SETTINGS_FREQUENCY_WEEKLY) {
                weekdaysButtons.removeClass('active')
                weekdaysButtons.each((i, el) => {
                    el = $(el)
                    if (el.data('weekday') == data.weekday) el.addClass('active')
                })
                weekdaysContainer.addClass('d-flex').removeClass('d-none')
                dayContainer.addClass('d-none').removeClass('d-flex')
            } else if (data.frequency === SETTINGS_FREQUENCY_MONTHLY) {
                weekdaysButtons.removeClass('active')
                dayContainer.find('select').val(data.day)
                weekdaysContainer.addClass('d-none').removeClass('d-flex')
                dayContainer.addClass('d-flex').removeClass('d-none')
            }
        }

        if (data?.rule === SETTINGS_RULE_AMOUNT) {
            withdrawalByAmount.prop('checked', true).trigger('change')
            withdrawalAmount.val(data.amount).focus()
        }
    }

    var clearSettingsForm = function () {
        settingsData = {
            company_id: financesSettingsForm.find('#settings_company_select').val(),
            rule: null,
            frequency: null,
            weekday: null,
            day: null,
            amount: 0
        }
        withdrawalByPeriod.prop('checked', false)
        withdrawalByAmount.prop('checked', false)
        onWithdrawalByPeriodChange()
        onWithdrawalByAmountChange()
    }

    financesSettingsForm.find('#settings_company_select').on('change', function () {
        getSettings($(this).val(), null, true)
    })

    frequencyButtons.on('click', function () {
        frequencyButtons.removeClass('active')
        settingsData.frequency = $(this).addClass('active').data('frequency')
        settingsData.weekday = null;
        weekdaysButtons.removeClass('active')

        if (settingsData.frequency === SETTINGS_FREQUENCY_DAILY) {
            weekdaysButtons.addClass('active')
        }

        if (settingsData.frequency !== SETTINGS_FREQUENCY_MONTHLY) {
            weekdaysContainer.addClass('d-flex').removeClass('d-none')
            dayContainer.addClass('d-none').removeClass('d-flex')
        } else {
            weekdaysContainer.addClass('d-none').removeClass('d-flex')
            dayContainer.addClass('d-flex').removeClass('d-none')
        }
    });

    weekdaysButtons.on('click', function () {
        if (settingsData.frequency === 'weekly') {
            weekdaysButtons.removeClass('active')
            $(this).addClass('active')
            settingsData.weekday = $(this).data('weekday')
        }
    })

    var onWithdrawalByPeriodChange = function () {
        var card = withdrawalByPeriod.closest('.card')

        if (withdrawalByPeriod.is(':checked')) {
            settingsData.rule = SETTINGS_RULE_PERIOD
            withdrawalByAmount.prop('checked', false).trigger('change')
            frequencyButtons.removeClass('disabled').prop('disabled', false)
            weekdaysButtons.removeClass('disabled').prop('disabled', false)
            card.find('[type=submit]').removeClass('disabled').addClass('btn-success').prop('disabled', false)
            card.addClass('bg-light').removeClass('bg-lighter')
            dayContainer.find('select').removeClass('disabled').prop('disabled', false)
        } else {
            settingsData.rule === SETTINGS_RULE_PERIOD ? settingsData.rule = null : ''
            frequencyButtons.addClass('disabled').removeClass('active').prop('disabled', true)
            weekdaysButtons.addClass('disabled').removeClass('active').prop('disabled', true)
            card.find('[type=submit]').addClass('disabled').removeClass('btn-success').prop('disabled', true)
            card.addClass('bg-lighter').removeClass('bg-light')
            dayContainer.find('select').addClass('disabled').prop('disabled', true).val(null)
            settingsData = Object.assign(settingsData, {
                frequency: null,
                weekday: null,
                day: null
            })
        }
    }

    withdrawalByPeriod.on('change', function () {
        onWithdrawalByPeriodChange()
        if (!settingsData.rule && settingsData.id) {
            deleteSettings(settingsData.id)
        }
    })

    var onWithdrawalByAmountChange = function () {
        var card = withdrawalByAmount.closest('.card')

        if (withdrawalByAmount.is(':checked')) {
            settingsData.rule = SETTINGS_RULE_AMOUNT
            withdrawalByPeriod.prop('checked', false).trigger('change')
            card.find('[type=submit]').addClass('btn-success').removeClass('disabled btn-default').prop('disabled', false)
            card.addClass('bg-light').removeClass('bg-lighter')
            withdrawalAmount.removeClass('disabled').prop('disabled', false).focus()
        } else {
            settingsData.rule === SETTINGS_RULE_AMOUNT ? settingsData.rule = null : ''
            card.find('[type=submit]').removeClass('btn-default').addClass('disabled btn-default').prop('disabled', true)
            card.addClass('bg-lighter').removeClass('bg-light')
            withdrawalAmount.addClass('disabled').prop('disabled', true).val('')
            settingsData = Object.assign(settingsData, {amount: 0})
        }
    }
    withdrawalByAmount.on('change', function () {
        onWithdrawalByAmountChange()
        if (!settingsData.rule && settingsData.id) {
            deleteSettings(settingsData.id)
        }
    })

    financesSettingsForm.on('submit', function (e) {
        e.preventDefault()
        saveSettings(settingsData)
        return false;
    })

    withdrawalAmount.maskMoney({thousands: '.', decimal: ',', allowZero: true});
    frequencyButtons.removeClass('active')
    if (withdrawalCompanySelect.val()) {
        getSettings(withdrawalCompanySelect.val())
    } else {
        onWithdrawalByAmountChange()
        onWithdrawalByPeriodChange()
    }

    $('#nav-settings-tab').on('click', function () {
        setTimeout(function () {
            if (settingsData.rule == SETTINGS_RULE_AMOUNT) withdrawalAmount.focus()
        }, 1500)
    })
});
