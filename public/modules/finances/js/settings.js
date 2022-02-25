$(document).ready(function () {

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
    var companySelect = $('#transfers_company_select')
    var withdrawalByPeriod = $('#withdrawal_by_period')
    var frequencyContainer = $('.frequency-container')
    var frequencyButtons = frequencyContainer.find('.btn')
    var weekdaysContainer = $('.weekdays-container')
    var weekdaysButtons = weekdaysContainer.find('.btn')
    var dayContainer = $('.day-container')
    var dayButtons = dayContainer.find('.btn')
    var withdrawalByAmount = $('#withdrawal_by_value')
    var withdrawalAmount = $('#withdrawal_amount')

    window.getSettings = function (companyId, settingsId = null, notify = false) {
        clearSettingsForm()
        $.ajax({
            method: "GET",
            url: '/api/withdrawals/settings/' + companyId + (settingsId ? '/' + settingsId : ''),
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                if (notify) {
                    alertCustom('warning', 'Nenhuma configuração de saque automático encontrada para a empresa selecionada')
                }
                clearSettingsForm()
            },
            success: response => {
                settingsData = response.data
                fillSettingsForm(settingsData)
            }
        });
    }

    window.saveSettings = function (data) {

        settingsData = Object.assign(settingsData, {
            company_id: companySelect.val(),
            amount: withdrawalAmount.val(),
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

    window.deleteSettings = function (id) {
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

    window.validateSettingsData = function (settingsData) {
        if (settingsData.rule === SETTINGS_RULE_PERIOD) {
            if (!settingsData.frequency) return false
            if (settingsData.frequency === SETTINGS_FREQUENCY_WEEKLY && settingsData.weekday === null) return false
            if (settingsData.frequency === SETTINGS_FREQUENCY_MONTHLY && !settingsData.day) return false
        }
        let amount = Number.parseInt(settingsData.amount.replace(/\D/g, ''))
        if (settingsData.rule === SETTINGS_RULE_AMOUNT && amount < 10000) return false

        return true
    }

    window.fillSettingsForm = function (data) {
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
                dayButtons.removeClass('active')
                dayButtons.each((i, el) => {
                    el = $(el)
                    if (el.data('day') == data.day) el.addClass('active')
                })
                weekdaysContainer.addClass('d-none').removeClass('d-flex')
                dayContainer.addClass('d-flex').removeClass('d-none')
            }
        }

        if (data?.rule === SETTINGS_RULE_AMOUNT) {
            let valueMask = (data.amount).toFixed(2).replace('.',',')
            withdrawalByAmount.prop('checked', true).trigger('change')
            withdrawalAmount.val(valueMask).focus()
        }
    }

    window.clearSettingsForm = function () {
        settingsData = {
            company_id: companySelect.val(),
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

    window.onWithdrawalByPeriodChange = function () {
        var card = withdrawalByPeriod.closest('.card')

        if (withdrawalByPeriod.is(':checked')) {
            settingsData.rule = SETTINGS_RULE_PERIOD
            withdrawalByAmount.prop('checked', false).trigger('change')
            frequencyButtons.removeClass('disabled').prop('disabled', false)
            weekdaysButtons.removeClass('disabled').prop('disabled', false)
            dayButtons.removeClass('disabled').prop('disabled', false)
            card.find('[type=submit]').removeClass('disabled').addClass('btn-success').prop('disabled', false)
            card.addClass('bg-light').removeClass('bg-lighter')
            if (!settingsData.frequency)
                frequencyContainer.find('[data-frequency="daily"]').trigger('click')
        } else {
            settingsData.rule === SETTINGS_RULE_PERIOD ? settingsData.rule = null : ''
            frequencyButtons.addClass('disabled').removeClass('active').prop('disabled', true)
            weekdaysButtons.addClass('disabled').removeClass('active').prop('disabled', true)
            dayButtons.addClass('disabled').removeClass('active').prop('disabled', true)
            card.find('[type=submit]').addClass('disabled').removeClass('btn-success').prop('disabled', true)
            card.addClass('bg-lighter').removeClass('bg-light')
            settingsData = Object.assign(settingsData, {
                frequency: null,
                weekday: null,
                day: null
            })
        }
    }

    window.onWithdrawalByAmountChange = function () {
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

    companySelect.on('change', function () {
        getSettings($(this).val())
    })

    frequencyButtons.on('click', function () {
        frequencyButtons.removeClass('active')
        weekdaysButtons.removeClass('active')
        settingsData.frequency = $(this).addClass('active').data('frequency')
        settingsData.weekday = null;
        settingsData.day = null;

        if (settingsData.frequency === SETTINGS_FREQUENCY_DAILY) {
            weekdaysButtons.addClass('active')
            weekdaysContainer.addClass('d-flex').removeClass('d-none')
            dayContainer.addClass('d-none').removeClass('d-flex')
            dayButtons.removeClass('active')
        } else if (settingsData.frequency === SETTINGS_FREQUENCY_WEEKLY) {
            weekdaysContainer.addClass('d-flex').removeClass('d-none')
            weekdaysContainer.find('[data-weekday="1"]').trigger('click')
            dayContainer.addClass('d-none').removeClass('d-flex')
            dayButtons.removeClass('active')
        } else if (settingsData.frequency === SETTINGS_FREQUENCY_MONTHLY) {
            weekdaysContainer.addClass('d-none').removeClass('d-flex')
            dayContainer.addClass('d-flex').removeClass('d-none')
            dayContainer.find('[data-day="01"]').trigger('click')
        }
    });

    weekdaysButtons.on('click', function () {
        if (settingsData.frequency === 'weekly') {
            weekdaysButtons.removeClass('active')
            $(this).addClass('active')
            settingsData.weekday = $(this).data('weekday')
        }
    })

    dayButtons.on('click', function () {
        if (settingsData.frequency === 'monthly') {
            dayButtons.removeClass('active')
            $(this).addClass('active')
            settingsData.day = $(this).data('day')
        }
    })

    withdrawalByPeriod.on('change', function () {
        onWithdrawalByPeriodChange()
        if (!settingsData.rule && settingsData.id) {
            deleteSettings(settingsData.id)
        }
    })

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

    if (!isEmpty(companySelect.val())) {
        getSettings(companySelect.val())
    } else {
        onWithdrawalByAmountChange()
        onWithdrawalByPeriodChange()
    }
})
