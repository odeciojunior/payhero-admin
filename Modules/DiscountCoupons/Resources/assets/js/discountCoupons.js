let statusCupons = {
    1: "success",
    0: "secondary",
};
var edit_rules = []
var cancel_edit_rules = []
// var items_selected = []


let projectId = $(window.location.pathname.split('/')).get(-1);


function count_plans_coupons(qtde) { //thumbnails
    
    

    $('#c-show_plans').html('')
    
    $.ajax({
        data: {
                total: 1,
                list: 'plan',
                search: '',
                project_id: projectId,
                //page: params.page || 1
            }
        ,

        method: "GET",
        url: "/api/plans/user-plans",
        
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: function error(response) {
            errorAjaxResponse(response);
            
        }, success: function success(response) {
            
            
            
            

            var html_show_plans = ''
            for(i in response.thumbnails){

                
                var toolTip = 'aria-describedby="tt'+response.thumbnails[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+response.thumbnails[i].name+'"'


                var img = response.thumbnails[i].products[0].photo?response.thumbnails[i].products[0].photo:'https://cloudfox-files.s3.amazonaws.com/produto.svg'
                
                html_show_plans += `
                

                    <span ${toolTip} class="plan_thumbnail" style="width:43px; height:43px;
                    background-repeat: no-repeat; background-position: center center; 
                    background-size: cover !important;  background-image: url('`+img+`'), url('https://cloudfox-files.s3.amazonaws.com/produto.svg');  "></span>
                
                `
            }

            $('#c-show_plans').html(html_show_plans)
            $('#c-show_plans').css('height','48px')

            $('[data-toggle="tooltip"]').tooltip('dispose')
    
            $('[data-toggle="tooltip"]').tooltip({
                container: '.page'
            });

            if(response.total > 8){
                var rest = response.total - 8
                $('#c-show_plans').append('<div class="plans_rest">+'+rest+'</div>')

            }
            

            
        }
    });
    
}

function plans_count2() {
    if(items_selected.length > 0 && items_selected.length < 11){
        
        var plans_count = items_selected.length + ' plano'+(items_selected.length>1?'s':'')
        $('#planos-count2, #planos-count-edit2').html(plans_count);

        $('#c-show_plans').css('margin-top','10px')
        
        $('#c-show_plans').css('height','88px')
        // $('#c-show_plans').addClass('mostrar_mais_detalhes')
        
        
        if($('#mostrar_mais_label2').html()=='Mostrar menos'){
            
            $('#mostrar_mais2').trigger('click')
        }

    }else{
        

        $('#c-show_plans').removeClass('mostrar_mais_detalhes')

        $('#c-show_plans').css('height','48px')
        
        //c-show_plans
        $('#c-show_plans').css('margin-top','20px')

        if(items_selected.length > 10){
            var plans_count = items_selected.length + ' plano'+(items_selected.length>1?'s':'')
            $('#planos-count2, #planos-count-edit2').html(plans_count);
        }else{

            $('#planos-count2, #planos-count-edit2').html('Todos os planos');
            count_plans_coupons(items_selected.length)
        }

    }

    if(items_selected.length>2 && items_selected.length<11){
        $('#mostrar_mais2').show();
        
    }else{
        $('#mostrar_mais2').hide();

    }
}


function coupon_rules(data) {
    
    var html = 'Desconto em '
    var value = ''
    if(data.type == 0){
        html += '<strong>porcentagem</strong>'
        value = data.value+'%'
    }else{
        html += '<strong>dinheiro</strong>'
        value = 'R$'+data.value
    }
    var expires = data.expires?data.expires:'Não vence';
    if(data.expires_days < 0){
        expires = '<span id="c-expire-label" style="color:">Vencido</span>'
    }
    if(data.expires_days > 0){
        expires = '<span style="color:">Vence em '+data.expires_days+' dia(s)</span>'
    }
    if(data.expires_days == 0){
        expires = '<span style="color:">Vence hoje</span>'
    }
    if(data.expires_days >= 0){
        $('#c-edit_status_label').html('Desconto ativo');
        $('#c-edit_status').prop('checked', true);
    }else{
        $('#c-edit_status_label').html('Desativado');
        $('#c-edit_status').prop('checked', false);
    }
    //console.log(data.expires_days);
    html += '<br><small>'+expires+'</small><br>'
    html += '<strong>'+value+' de desconto</strong> em compras <strong>de R$'+data.rule_value+' ou mais</strong> com o cupom <strong>'+data.code+'</strong>'
    
    $('#c-rules').html(html)
}

function show_plans(){
    if(items_selected.length > 10){
        var html_show_plans = ''
        for(i in items_selected){
            
            if(i>7) break;
            var toolTip = 'aria-describedby="tt'+items_selected[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+items_selected[i].name+'"'

            html_show_plans += `<span ${toolTip} class="plan_thumbnail" style="width:43px; height:43px;
            background-repeat: no-repeat; background-position: center center; 
            background-size: cover !important; background: url('`+items_selected[i].image+`'), url('https://cloudfox-files.s3.amazonaws.com/produto.svg');"></span>`
        }

        $('#show_plans, #c-show_plans').removeClass('mostrar_mais_detalhes')
        $('#show_plans, #c-show_plans').css('margin-top',20);

        $('#show_plans, #c-show_plans').html(html_show_plans)

        $('[data-toggle="tooltip"]').tooltip('dispose')
    
        $('[data-toggle="tooltip"]').tooltip({
            container: '.page'
        });

        var rest = items_selected.length - 8
        $('#show_plans, #c-show_plans').append('<div class="plans_rest">+'+rest+'</div>')
        
        $('#c-show_plans, #show_plans').css('height', 48)
        return false
    }else{
        $('#c-show_plans, #show_plans').css('height', 88)

    }


    var show_plans = ''
    for(i in items_selected){
        
        if(items_selected[i].name.length > 18){

            toolTip = 'aria-describedby="tt'+items_selected[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+items_selected[i].name+'"'
        }else{
            toolTip = ''
        }
        
        show_plans += `<div ${toolTip} class="item_raw" >
    
            <span style="background-image: url('https://cloudfox-files.s3.amazonaws.com/produto.svg')" class="image">
                <span style="background-image: url(`+(items_selected[i].image?items_selected[i].image:'https://cloudfox-files.s3.amazonaws.com/produto.svg')+`)" class="image2"></span>
            </span>

            <span class="title text-overflow-title">`+items_selected[i].name+`</span>
            <span class="description text-overflow-description">`+items_selected[i].description+`</span>
        </div>`
    }
    if(show_plans)
        $('#show_plans, #c-show_plans').html(show_plans)
    
    $('[data-toggle="tooltip"]').tooltip('dispose')

    $('[data-toggle="tooltip"]').tooltip({
        container: '.page'
    });
}


noDiscountsFound = `<div class="mt-20 d-flex justify-content-center align-items-center">
<img src="/build/global/img/empty-state-table.svg" style="margin-right: 60px;">
<div class="text-left">
    <h1 style="font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;">Nenhum desconto configurado</h1>
    <p style="font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;">Cadastre o seu primeiro desconto para poder
    <br>gerenciá-los nesse painel.</p>
    <button type="button" style="width: auto; height: auto; padding: .429rem 1rem !important;" class="btn btn-primary add-desconto">Adicionar desconto</button>
</div>
</div>`



var page = null
function atualizarCoupon() {
    var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    if (link == null) {
        link = page
    }
    if (link == null) {
        link = '/api/project/' + projectId + '/couponsdiscounts';
    } else {
        page = link
        link = '/api/project/' + projectId + '/couponsdiscounts' + link;
    }
    

    loadOnTable('#data-table-coupon', '#tabela-coupom');
    $.ajax({
        method: "GET",
        data:{name:$('#search-name').val()},
        url: link,
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $("#data-table-coupon").html('');
            
            if (response.data == '') {
                $("#data-table-coupon").html(noDiscountsFound);
                $('.add-desconto').on('click',function(){
                    $('#add-coupon').trigger('click')
                })
                $('#tabela-coupon thead').hide()
                $('#coupon-panel').hide()
                //console.log(99);
            } else {
                $('#tabela-coupon thead').show()
                $('#coupon-panel').show()

                $('#count-coupons').html(response.meta.total)
                $.each(response.data, function (index, value) {
                    let data = `<tr>
                        <td class=""><strong>${value.discount}</strong></td>
                        <td class="">${value.name}<br><span class="small-text">${value.plans}</span></td>
                        <td class="">${value.value}</td>
                        <td class="">${value.code}</td>
                        <td class="" style="vertical-align: middle; text-align:center">
                            <span class="badge badge-${statusCupons[value.status]}">${value.status_translated}</span>
                        </td>



                        <td class="mg-responsive text-right" style="line-height: 1;">
                            <div class="d-flex justify-content-end align-items-right" style="margin-right:-10px">
                                <a role="button" title='Editar' class="mg-responsive edit-coupon pointer" discount="${value.discount}" coupon="${value.id}"><span class="o-eye-1"></span> </a>
                                <a role="button" title='Excluir' class="mg-responsive delete-coupon pointer" coupon="${value.id}"><span class='o-bin-1'></span></a>
                            </div>
                        </td>

                        
                    </tr>`;

                    $("#data-table-coupon").append(data);
                });
                // response.meta.current_page = 2
                pagination(response, 'coupons', atualizarCoupon);
            }
        }
    });
}

var items_placeholder = `<div id="items_loading"> 
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div> </div>`

function run_search(search, now){
    
    search_holder = search
    var search2 = $('#search_input_description_value').val()
    

    
    var loading = $('.item_placeholder').is(':visible')
    
    if(loading && !now) return
    
    if(search.length > 0 || now){
        
        $('#search_result, #search_result2').html(items_placeholder);
        //animateItemsPlaceholder()
        
        var items_saved = mount_selected_items(search, search2)
        // console.log(items_saved);
        $.ajax({
            data: {
                    most_sales: 1,
                    list: 'plan',
                    search: search,
                    search2: search2,
                    items_saved: items_selected,
                    project_id: projectId,
                    limit:30
                    //page: params.page || 1
                }
            ,

            method: "GET",
            url: "/api/plans/user-plans",
            
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
                
            }, success: function success(response) {
                
                if(search_holder != search){
                    run_search(search_holder, 1)
                    return
                }

                var data = response.data
                var items = ''
                for(plan in data){

                    var skip = false
                    for(i in items_selected){
                        if(items_selected[i].id == data[plan].id)
                            skip = true;
                    }
                    if(skip) continue;
                    
                    var toolTip
                    if(data[plan].name.length > 18){

                        toolTip = 'aria-describedby="tt'+data[plan].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+data[plan].name+'"'
                    }else{
                        toolTip = ''
                    }

                    var item = `<div ${toolTip} class="item" data-id="`+data[plan].id+`" data-image="`+data[plan].photo+`" data-name="`+data[plan].name+`" data-description="`+data[plan].description+`" >
                                    
                                    <span style="background-image: url('https://cloudfox-files.s3.amazonaws.com/produto.svg')" class="image">
                                    
                                        <span style="background-image: url(`+(data[plan].photo?data[plan].photo:'https://cloudfox-files.s3.amazonaws.com/produto.svg')+`)" class="image2"></span>
                                    </span>

                                    <span class="title text-overflow-title">`+data[plan].name+`</span>
                                    <span class="description text-overflow-description">`+data[plan].description+`</span>
                                    <svg class="selected_check " style="display: none" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"/>                            <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"/>                            </svg>    
                                    <svg class="empty_check " width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9" stroke="#9B9B9B"/>                            </svg>
                                </div>`;
                    items += item;
                }

                if(items.length > 0 || items_saved){
                    
                    $('#search_result, #search_result2').html(items_saved + items);
                    
                    $('#search_result, #search_result2').mCustomScrollbar('destroy')

                    $('#search_result, #search_result2').mCustomScrollbar()
                    
                    set_item_click()
                }else{
                    $('#search_result, #search_result2').mCustomScrollbar('destroy')

                    $('#search_result, #search_result2').html(`
                    <div class="not-found">
                        <img src="/build/global/img/not-found.svg" >
                        <div class="title">
                        Nenhum resultado encontrado.</div>
                        <div class="description">
                        Por aqui, nenhum plano com esse nome.
                        </div>
                    </div>`);

                }
                
            }
        });
    }else{
        
        run_search('',1)

    }
}

$(function () {
    function show_rules(rules){
        var rules_html = '<ol>'
        for(i in rules){
            rules_html += `<li>
                            Na compra 
                            <strong>`+ (rules[i].buy=='above_of'?'acima de ':'de ') +
                            rules[i].qtde +` itens</strong>,
                            aplicar desconto de <strong>
                            `+ (rules[i].type=='percent'?rules[i].value+'%':'R$' + rules[i].value) +`
                            </strong>
                        </li>`;
        }
        rules_html += '</ol>'
        
        if(rules_html.indexOf('%') > 0)
            $('.rules-label').html('Por Valor em Porcentagem')
        
        if(rules_html.indexOf('R$') > 0)
            $('.rules-label').html('Por Valor em R$')
        
        if(rules_html.indexOf('%')  > 0 && rules_html.indexOf('R$')  > 0)
            $('.rules-label').html('Por Valor em R$ e Porcentagem')

        $('.rules').html(rules_html)
    }

    //comportamento da tela
    var cuponType = 0;
    $('.coupon-value').mask('00%', {reverse: true});

    $(document).on('change', '#edit-coupon-type', function (event) {
        if ($(this).val() == 1) {
            cuponType = 1;
            $(".coupon-value").mask('#.##0,00', {reverse: true}).removeAttr('maxlength');
        } else {
            cuponType = 0;
            $('.coupon-value').mask('00%', {reverse: true});
        }
    });
    $(document).on('change', '#create-coupon-type', function (event) {
        if ($(this).val() == 1) {
            cuponType = 1;            
            $(".coupon-value").mask('#.##0,00', {reverse: true});          
              
        } else {
            cuponType = 0;
            $('.coupon-value').mask('00%', {reverse: true});
        }
    });
    $(".rule-value").mask('#.##0,00', {reverse: true});

    $('.rule-value').on('blur', function () {
        applyMaskManually(this);
    });

    $('.coupon-value').on('blur', function () {
        if(cuponType==1){
            applyMaskManually(this);
        }
    });

    function applyMaskManually(classValue){
        if ($(classValue).val().length == 1) {
            let val = '0,0' + $(classValue).val();
            $(classValue).val(val);
        } else if ($(classValue).val().length == 2) {
            let val = '0,' + $(classValue).val();
            $(classValue).val(val);
        }
    }

    $('.tab_coupons').on('click', function () {
        atualizarCoupon();
        $(this).off();
    });

    // carregar modal de detalhes
    $(document).on('click', '.details-coupon', function () {
        let coupon = $(this).attr('coupon');
        $("#btn-modal").hide();
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $('#modal-detail-coupon .coupon-name').html(response.data.name);
                $('#modal-detail-coupon .coupon-code').html(response.data.code);
                $('#modal-detail-coupon .coupon-type').html(response.data.type);
                $('#modal-detail-coupon .coupon-value').html(response.data.type == 'Valor' ? 'R$ ' + response.data.value : response.data.value + '%');
                $('#modal-detail-coupon .rule-value').html('R$ ' + response.data.rule_value);
                $('#modal-detail-coupon .coupon-status').html(response.data.status == '1'
                    ? '<span class="badge badge-success text-left">Ativo</span>'
                    : '<span class="badge badge-secondary">Inativo</span>');
                $('#modal-detail-coupon').modal('show');

            }
        });
    });
    // Edit discount
    function edit_name(){
        $('#edit-name').hide()
        $('#edit-plans').hide()
        $('#edit-rules').hide()


        $('#edit-name-box').animate({height:146})
        $('#display_name').hide()
        $('#display_name_edit').show()

        // 44 146
        $('#name-edit').focus()
        $('#name-edit').val($('#d-name').html());
        

        $('#cancel_name_edit').click(function(){
            $('#edit-plans').show()
            $('#edit-rules').show()

            $('#display_name_edit').hide()
            $('#display_name').show()
            $('#edit-name').show()
            $('#edit-name-box').animate({height:44})

        })   
    }
    
    function reset_edit_buttons() {
        $('#edit-name').show()
        $('#edit-plans').show()
        $('#edit-rules').show()
    }

    function edit_plans(){
        
        if($('#mostrar_mais_label').html() == 'Mostrar menos'){

            $('#mostrar_mais').trigger('click');
        }

        $('#edit_step0').hide();
        $('#edit_step2').hide();
        $('#edit_step1').show();
        $('.form-control').each(function(){
            
            $(this).val('');
        })
        
        $('#search_input2').focus();

        $('#edit-finish-btn').hide()
        $('#plans-actions').show()

        
        if(items_selected.length>0){
            var items_thumbs = ''
            for(i in items_selected){
                
                // if(i>7) break;
                
                var toolTip = 'aria-describedby="tt'+items_selected[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+items_selected[i].name+'"'
                

                items_thumbs +=  `
                <span ${toolTip} class="plan_thumbnail" style="width:56px; height:56px;
                background-repeat: no-repeat; background-position: center center; 
                background-size: cover !important; background: url('`+items_selected[i].image+`'), url('/build/global/img/produto.png')"></span>`
                
            }
            
            $('.edit-plans-thumbs').html(items_thumbs)

            if(items_selected.length > 9){

                $('.edit-disc-plans-thumbs-scroll').css('margin-bottom', 16)
                $('.edit-disc-plans-thumbs-scroll').mCustomScrollbar('destroy')
                $('.edit-disc-plans-thumbs-scroll').mCustomScrollbar({
                    axis: 'x',
                    advanced: {
                      autoExpandHorizontalScroll: true
                    }
                  })
            }else{
                $('.edit-disc-plans-thumbs-scroll').mCustomScrollbar('destroy')
                $('.edit-disc-plans-thumbs-scroll').css('margin-bottom', 0)

            }

            $('[data-toggle="tooltip"]').tooltip('dispose')
    
            $('[data-toggle="tooltip"]').tooltip({
                container: '.page'
            });

        }else{
            count_plans2()
        }

        
        $('#search_input_description_value').val('')
        run_search('', 1)

        
    }


    $('.cancel-btn').click(function(){
        $('#edit_step1').hide();
        $('#edit_step2').hide();
        $('#edit_step0').show();
    })

    $('.btn-edit-plans-save').click(function(){
        
        
        $('#show_plans').html('');


        $('#plans_value').val( JSON.stringify(items_selected));
        $('#save_name_edit').click()
        $('.cancel-btn').click()

        $('#edit-finish-btn').show()
        $('#plans-actions').hide()

        plans_count()
    })
    
    
    $('.btn-save-edit-rules').click(function(){

        if(!toggleDiscountRulesAlert(edit_rules.length)){
            return false
        }

        $('#rules_edited').val( JSON.stringify(edit_rules));
        $('#save_name_edit').click()
        $('#edit-rules-back').click()
    })

    $("#type_percent-edit").on('click', function () {
        
        $("#percent-edit").show()
        $("#value-edit").hide()
    })
    $("#type_value-edit").on('click', function () {
        
        $("#value-edit").show()
        $("#percent-edit").hide()
    })

    $('#value-edit').mask('#.##0,00', {reverse: true});


    //var rules = edit_rules
    
    $("#add_rule-edit").on('click', function () {
        
        var rule_id = edit_rules.length+1;
        for(i in edit_rules){
            edit_rules[i].id = ++i
        }
        

        if(!$('#qtde-edit').val() || $('#qtde-edit').val() == 0){
            $('#qtde-edit').addClass('warning-input')
            alertCustom("error", 'Digite um valor acima de 0');
            $('#qtde-edit').focus()
            return false
        }
        
        if($("#type_percent-edit").prop('checked') && (!$('#percent-edit').val() || $('#percent-edit').val() == 0 )){
            $('#percent-edit').focus()
            $('#percent-edit').addClass('warning-input')
            alertCustom("error", 'Digite um valor acima de 0');
            return false
        }
        
        if($("#type_value-edit").prop('checked') && (!$('#value-edit').val() || $('#value-edit').val().replace(',','').replace('.','') == 0 )){
            $('#value-edit').focus()
            $('#value-edit').addClass('warning-input')
            alertCustom("error", 'Digite um valor acima de 0');
            return false
        }

        

        edit_rules.push({
            id:rule_id,
            buy:$('#buy-edit').val(),
            type:$("#type_percent-edit").prop('checked')?'percent':'value',
            qtde:$('#qtde-edit').val(),
            value:$("#type_percent-edit").prop('checked')?$('#percent-edit').val():$('#value-edit').val()
        })
        
        toggleDiscountRulesAlert(1)
        mount_rules(edit_rules);
        return false;
    })

    function mount_rules(rules, edit){
        let rules_html = ''
        
        for(i in rules){
            rules_html += `<div class="rule_holder">
                                <div class="rule_box">
                                    Na compra
                                    <strong>`+ (rules[i].buy=='above_of'?'acima de ':'de ') +
                                    rules[i].qtde +` itens</strong>,
                                    aplicar desconto de <strong>
                                    `+ (rules[i].type=='percent'?rules[i].value+'%':'R$' + rules[i].value) +`
                                    </strong>

                                    <svg data-id="`+rules[i].id+`"  style="float:right; margin:4px 4px 0 18px" class="pointer delete2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 1L1 15M1 1L15 15L1 1Z" stroke="#5E6576" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                    <svg data-id="`+rules[i].id+`" style="float:right;" class="pointer edit2" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.8397 5.7993L19.8987 3.74294L17.2652 1.10974L15.2065 3.1661" stroke="#3D4456" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3.19598 15.163L5.82952 17.7962M5.82952 17.7962L17.8395 5.79928L15.2063 3.16608L3.19598 15.163L1.10156 19.8903L5.82952 17.7962V17.7962Z" stroke="#3D4456" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                </div>

                                <div class="rule_box_edit" style="display:none">

                                    Na compra
                                    <select id="buy" class="buy  w-auto d-inline-block adjust-select">
                                        <option `+ (rules[i].buy=='above_of'?'selected':'') +` value="above_of">acima de</option>
                                        <option `+ (rules[i].buy=='of'?'selected':'') +` value="of">de</option>
                                    </select>
                                    
                                    <input value="`+ rules[i].qtde +`" class="qtde input-pad" type="text" onkeyup="$(this).removeClass('warning-input')"
                                     style="width: 60px; height: 49px;
                                    margin-top: 2px;" maxlength="2" data-mask="0#" />
                                    itens, aplicar desconto de
                                    <input maxlength="9" value="`+ (rules[i].type=='value'?rules[i].value:'') +`" class="input-pad value value_edit" type="text" onkeyup="$(this).removeClass('warning-input')"
                                     style="`+ (rules[i].type=='percent'?'display: none;':'') +` width: 86px; height:46px" />
                                    <input value="`+ (rules[i].type=='percent'?rules[i].value:'') +`" type="text" onkeyup="$(this).removeClass('warning-input')"
                                     style="width: 86px; `+ (rules[i].type=='value'?'display: none;':'') +` height:46px" class="input-pad percent" maxlength="2"
                                        data-mask="0#" autocomplete="off">

                                    <svg  class="pointer float-right save-edit-rule2" style="" width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.629 0.8994C19.1237 1.43193 19.1237 2.29534 18.629 2.82787L7.229 15.1006C6.73434 15.6331 5.93233 15.6331 5.43766 15.1006L0.370998 9.64605C-0.123666 9.11352 -0.123666 8.25011 0.370998 7.71758C0.865662 7.18505 1.66767 7.18505 2.16234 7.71758L6.33333 12.2079L16.8377 0.8994C17.3323 0.366867 18.1343 0.366867 18.629 0.8994Z" fill="#5E6576"/>
                                    </svg>
                                        

                                    

                                </div>
                            </div>`;
        }
        $('#rules-edit').html(rules_html)

        $('#rules-edit').mCustomScrollbar('destroy')
        $('#rules-edit').mCustomScrollbar()

        $('.rule_box_edit select.buy').each(function () {
          
            $(this).siriusSelect()
        })
        
        $('.value').mask('#.###,#0', {reverse: true});

        $('.value_edit').on('change', function () {
            if($(this).val().length<3){
                $(this).val($(this).val().padStart(2, '0'))
                $(this).val(','+$(this).val())
                $(this).val($(this).val().padStart(4, '0'))
            }
        })


        set_rules_events()
        if(!edit){

            $('#percent-edit').val('')
            $('#value-edit').val('')
            $('#qtde-edit').val('')
        }

        if(edit_rules.length > 0){
            $('#empty-rules2').hide()
        }else{
            $('#empty-rules2').show()
        }
    }

    function toggleDiscountRulesAlert(rules)
    {
        if(rules==0){

            $('.inputs-warning2').addClass('warning')
            $('.warning-text2').fadeIn()
            
            return false
            
        }else{
            $('.inputs-warning2').removeClass('warning')
            $('.warning-text2').fadeOut()
        }
        return true
    }

    function set_rules_events(){

        $(".delete2").on('click', function () {
            var id = $(this).attr('data-id')
            
            for(i in edit_rules){
                if(edit_rules[i].id == id){
                    edit_rules.splice(i,1)
                }
            }
            mount_rules(edit_rules);
        })
        
        $(".edit2").on('click', function () {
            var id = $(this).attr('data-id')
            
            $(this).parents('.rule_holder').find('.rule_box').hide()
            $(this).parents('.rule_holder').find('.rule_box_edit').show()
            
            for(i in edit_rules){
                if(edit_rules[i].id == id){
                    editingRule = i
                }
            }
            

            
            // $('.btn-save-edit-rules').prop('disabled', true);


        })

        $('.save-edit-rule2').on('click', function(){
            
            var that = this;
                function go(obj) {
                    return $(that).parents('.rule_holder').find(obj)
                }
                
                if(!go('.qtde').val() || go('.qtde').val()==0){
                    go('.qtde').focus()
                    go('.qtde').addClass('warning-input')
                    alertCustom("error", 'Digite um valor acima de 0');
                    return false;
                }
                if(edit_rules[editingRule].type=='percent'){
                    if(!go('.percent').val() || go('.percent').val()==0){
                        go('.percent').focus()
                        go('.percent').addClass('warning-input')
                        alertCustom("error", 'Digite um valor acima de 0');
                        return false;
                    }
                }else{
                    if(!go('.value').val() || go('.value').val().replace(',','').replace('.','')==0){
                        go('.value').focus()
                        go('.value').addClass('warning-input')
                        alertCustom("error", 'Digite um valor acima de 0');
                        return false;
                    }
                }
                
                
                edit_rules[editingRule].buy = go('#buy').val(),
                edit_rules[editingRule].qtde = go('.qtde').val(),
                edit_rules[editingRule].value = edit_rules[editingRule].type=='percent'?go('.percent').val():go('.value').val()
                mount_rules(edit_rules, 1);
        
                
                
                
        
                $(this).parents('.rule_holder').find('.rule_box_edit').hide()
                $(this).parents('.rule_holder').find('.rule_box').show()
    
            
        })
    }
    var editingRule = 0
    



    $('#edit-rules').click(function(){
        

        $('#edit_step0').hide();
        $('#edit_step1').hide();
        $('#edit_step2').show();
        
        mount_rules(edit_rules)
    })

    $('#edit-rules-back').click(function(){
        $('#edit_step0').show();
        $('#edit_step1').hide();
        $('#edit_step2').hide();
        // edit_rules = Object.assign(edit_rules, cancel_edit_rules)
    })

    $('#edit_status').click(function(){
        if($(this).is(':checked')){
            // $('#edit_status_label').css('color', '#41DC8F');
            $('#edit_status_label').html('Desconto ativo');
            
        }else{
            // $('#edit_status_label').css('color', '#9B9B9B');
            $('#edit_status_label').html('Desativado');
            
            
        }
        $('#set_status').val(1)

        $('#save_name_edit').click()
    })

    $('.edit-finish-btn').click(function(){
        $('#modal-button-close-2').click()
    })

    $('#form-update-discount').submit(function(){
        return false;
    })

    $("#save_name_edit").on('click', function () {
        let formData = new FormData(document.getElementById('form-update-discount'));
        let id = $('#discount-id').val();

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/discounts/" + id,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                if (response.status === 400) {
                    //atualizarCoupon();
                }

                errorAjaxResponse(response);
            },
            success: function success(data) {

                alertCustom("success", data.message);
                atualizarCoupon();
                
                $('#display_name_edit').hide()
                $('#display_name').show()
                
                if($('#name-edit').val())
                    $('#d-name').html($('#name-edit').val())
                

                show_plans()
                show_rules(edit_rules)


                $('#edit-plans').show()
                $('#edit-rules').show()
                $('#edit-name').show()


            }
        });
        return false
    });
    // End edit discount
    
    

    
    $('#nao_vence').on('click', function(){
        if($(this).prop('checked')){
            $('#date_range').prop('disabled', true)
            $('#date_range').val('')
        }else{
            $('#date_range').prop('disabled', false)
            $('#date_range').focus()

        }
    })

    $('#nao_vence2').on('click', function(){
        if($(this).prop('checked')){
            $('#date_range2').prop('disabled', true)
            $('#date_range2').val('')
        }else{
            $('#date_range2').prop('disabled', false)
            $('#date_range2').focus()
        }
    })

    

    $('#date_range2').val('DD/MM/YYYY')
        .dateRangePicker({
            format: 'DD/MM/YYYY',
            singleDate: true,
            showShortcuts: true,
            startDate: new Date(),
	        endDate: false,
            container: '#modal-edit-coupon',
            customShortcuts: [
                {
                    name: 'Hoje',
                    dates: () => [moment().startOf('day').toDate(), new Date()]
                },
                {
                    name: '7 dias',
                    dates: () => [moment().add(6, 'days').toDate(), moment().add(6, 'days').toDate()]
                },
                {
                    name: '15 dias',
                    dates: () => [moment().add(14, 'days').toDate(), moment().add(14, 'days').toDate()]
                },
                {
                    name: '1 mês',
                    dates: () => [moment().add(30, 'days').toDate(), moment().add(30, 'days').toDate()]
                },
                {
                    name: '3 meses',
                    dates: () => [moment().add(90, 'days').toDate(), moment().add(90, 'days').toDate()]
                }
            ],
        })
        .bind('datepicker-opened',function()
        {
            $('.modal-open .modal').animate({scrollTop: $(document).height() + $(window).height()});
            $('.date-picker-wrapper').attr('tabindex',0).focus()
        });

    $('#edit-name').on('click', edit_name);
    $('#edit-plans').on('click',edit_plans);
    // carregar modal de edicao
    $(document).on('click', '.edit-coupon', function () {
        let coupon = $(this).attr('coupon');
        var discount = $(this).attr('discount');
        
        

        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon + "/edit",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                
                if(response.status==1){
                    // $('#edit_status_label').css('color', '#41DC8F');
                    $('#edit_status_label').html('Desconto ativo');
                    $('#edit_status').prop('checked', true);
                    
                }else{
                    // $('#edit_status_label').css('color', '#9B9B9B');
                    $('#edit_status_label').html('Desativado');
                    $('#edit_status').prop('checked', false);
                }
                
                
                    
                
                if(discount=='Progressivo'){
                    $('.cancel-btn').trigger('click')
                    $('#cancel_name_edit').trigger('click')
                    $('#edit_step0').show()
                    $('#edit_step1').hide()
                    $('#edit_step2').hide()
                    // console.log(response);
                    $('#edit-discount').show();
                    $('#edit-coupon').hide();


                    $('#set_status').val(0);
                    // $('#edit_status').val('');
                    

                    $('#discount-id').val(coupon);

                    $('#modal-edit-coupon').modal('show');
                    
                    $('#d-name').html(response.name);
                    $('#name-edit').val(response.name);
                    
                    

                    
                    if(response.plans != null){
                        items_selected = JSON.parse(response.plans)
                        
                        plans_count()

                        $('#plans_value').val(response.plans);
                    }else{
                        items_selected = []
                        $('#plans_value').val('');
                    }
                        
                    
                    
                    

                    show_plans()
                    
                    //rules
                    $('#rules_edited').val(response.progressive_rules);
                    var rules = JSON.parse(response.progressive_rules)
                    edit_rules = rules
                    
                    show_rules(rules)
                    

                }else{
                    if(response.plans != null){
                        items_selected = JSON.parse(response.plans)
                        plans_count2()
                        plans_count()

                    }else{
                        items_selected = []
                    }
                    // mount_selected_items()
                    // set_item_click()
                    show_plans()
                    coupon_rules(response)

                    $('#c-cancel_name_edit').trigger('click')
                    $('#c-set_status').val(0);
                    // $('#c-edit_status').val('');

                    $('#edit-discount').hide();
                    $('#edit-coupon').show();
                    
                    $('#coupon-id2').val(coupon);
                    
                    $('#d-code, #d-code2').html(response.code);
                    $('#c-d-name, #c-d-name2').html(response.name);
                    $('#c-code-edit').val(response.code);
                    $('#c-name-edit').val(response.name);
                    
                    $('#c-edit_step1').hide()
                    $('#c-edit_step2').hide()
                    $('#c-edit_step0').show()
                    
                    
                    
                    response.rule_value = response.rule_value.replace(',','.')
                    response.value = response.value.replace(',','.')
                    $('#2minimum_value').val(response.rule_value);
                    

                    if (response.type == 1) {
                        $('#2c_type_value').prop('checked',true).click();
                    } else {
                        $('#2c_type_percent').prop('checked',true).click();
                    }
                    if($('#2c_type_value').prop('checked')){
                        $('#2discount_value').val(response.value)
                        
                    }
                    if($('#2c_type_percent').prop('checked')){
                        $('#2percent_value').val(response.value)
                        
                    }
                    $('#2c_value').val(response.value)
                    
                    $('#date_range2').val(response.expires_date);
                    if(!response.expires_date ){
                        $('#nao_vence2').prop('checked', true);
                    }else{
                        $('#nao_vence2').prop('checked', false);
                        $('#date_range2').prop('disabled', false)

                        
                    }
                    
                    if(response.status==1){
                        // $('#c-edit_status_label').css('color', '#41DC8F');
                        $('#c-edit_status_label').html('Desconto ativo');
                        $('#c-edit_status').prop('checked', true);
                        
                    }else{
                        // $('#c-edit_status_label').css('color', '#9B9B9B');
                        $('#c-edit_status_label').html('Desativado');
                        $('#c-edit_status').prop('checked', false);
                        
                        
                    }

                    $('#modal-edit-coupon').modal('show');
                }
            }
        });
    });

    // carregar modal delecao
    $(document).on('click', '.delete-coupon', function (event) {
        let coupon = $(this).attr('coupon');
        $('#modal-delete-coupon .btn-delete1').attr('coupon', coupon);
        $("#modal-delete-coupon").modal('show');
    });

    

    

    function count_plans2() { //thumbnails on editing
        
        $('.edit-plans-thumbs').html('')


        $.ajax({
            data: {
                    total: 1,
                    list: 'plan',
                    search: '',
                    project_id: projectId,
                    //page: params.page || 1
                }
            ,

            method: "GET",
            url: "/api/plans/user-plans",
            
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
                
            }, success: function success(response) {
                
                
                
                var toolTip = 'aria-describedby="tt'+response.thumbnails[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+response.thumbnails[i].name+'"'



                var html_show_plans = ''
                for(i in response.thumbnails){
                    html_show_plans += `<span ${toolTip} class="plan_thumbnail" style="width:56px; height:56px;
                    background-repeat: no-repeat; background-position: center center; 
                    background-size: cover !important; background: url('`+response.thumbnails[i].products[0].photo+`'), url('https://cloudfox-files.s3.amazonaws.com/produto.svg');)"></span>`
                }

                $('.edit-plans-thumbs').html(html_show_plans)

                $('[data-toggle="tooltip"]').tooltip('dispose')
    
                $('[data-toggle="tooltip"]').tooltip({
                    container: '.page'
                });

                if(response.total > 8){
                    var rest = response.total - 8
                    $('.edit-plans-thumbs').append('<div style="margin-top:14px" class="plans_rest">+'+rest+'</div>')

                }


                
            }
        });
        
    }

    function plans_count() {
        if(items_selected.length > 0){
            
            var plans_count = items_selected.length + ' plano'+(items_selected.length>1?'s':'')
            $('#planos-count, #planos-count-edit').html(plans_count);

            $('#plans_holder').css('height','auto')
            $('#show_plans').css('margin-top','10px')

    
            //$('#show_plans').addClass('mostrar_mais_detalhes')

        }else{
            $('#plans_holder').css('height','158px')
            $('#show_plans').css('margin-top','20px')
            
            $('#planos-count, #planos-count-edit').html('Todos os planos');

            count_plans()
        }

        if(items_selected.length>2 && items_selected.length<11){
            $('#mostrar_mais').show();
            
        }else{
            $('#mostrar_mais').hide();
            $('#show_plans').removeClass('mostrar_mais_detalhes')
            $('#show_plans').css({
                height: "88px"
            });

        }
    }


    

    //cria novo cupom
    $('#modal-create-coupon .btn-save').on('click', function () {
        let formData = new FormData(document.getElementById('form-register-coupon'));

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {

                errorAjaxResponse(response);
            },
            success: function success() {

                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Desconto adicionado!");
                atualizarCoupon();
                clearFields();
            }
        });
    });

    //atualizar cupom
    $("#modal-edit-coupon .btn-update").on('click', function () {
        let formData = new FormData(document.getElementById('form-update-coupon'));
        let coupon = $('#modal-edit-coupon .coupon-id').val();

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                if (response.status === 400) {
                    atualizarCoupon();
                }

                errorAjaxResponse(response);
            },
            success: function success(data) {

                alertCustom("success", data.message);
                atualizarCoupon();
            }
        });
    });

    //deletar cupom
    $('#modal-delete-coupon .btn-delete1').on('click', function () {
        let coupon = $(this).attr('coupon');
        
        $.ajax({
            method: "DELETE",
            url: "/api/project/" + projectId + "/couponsdiscounts/" + coupon,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(data) {

                alertCustom("success", "Registro removido com sucesso");
                atualizarCoupon();
            }

        });
    });

    

    //Limpa campos
    function clearFields() {
        $('.coupon-name').val('');
        $('.coupon-value').val('');
        $('.coupon-code').val('');
        $('.rule-value').val('');
    }


    
    //
    $('#edit_step0').mCustomScrollbar()
    $('#coupon_edit_step0').mCustomScrollbar()

});

function count_plans() { //thumbnails
        
    $('#show_plans').html('')

    $.ajax({
        data: {
                total: 1,
                list: 'plan',
                search: '',
                project_id: projectId,
                //page: params.page || 1
            }
        ,

        method: "GET",
        url: "/api/plans/user-plans",
        
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: function error(response) {
            errorAjaxResponse(response);
            
        }, success: function success(response) {
            
            
            var toolTip = 'aria-describedby="tt'+response.thumbnails[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+response.thumbnails[i].name+'"'

            
            var html_show_plans = ''
            for(i in response.thumbnails){
                html_show_plans += `<span ${toolTip} class="plan_thumbnail" style="width:43px; height:43px;
                background-repeat: no-repeat; background-position: center center; 
                background-size: cover !important; background: url('`+response.thumbnails[i].products[0].photo+`'), url('https://cloudfox-files.s3.amazonaws.com/produto.svg');"></span>`
            }

            $('#show_plans').removeClass('mostrar_mais_detalhes')

            $('#show_plans').html(html_show_plans)

            $('[data-toggle="tooltip"]').tooltip('dispose')

            $('[data-toggle="tooltip"]').tooltip({
                container: '.page'
            });

            if(response.total > 8){
                var rest = response.total - 8
                $('#show_plans').append('<div class="plans_rest">+'+rest+'</div>')

            }


            
        }
    });
    
}

var timer_desc
function set_description_value(obj, obj2){
    $('#search_input_description_value').val($(obj).val())
    $(obj2).trigger('keyup')
}

function toggleSelect(obj){
    if($('.selected_check', obj).is(':visible')){
        $('.selected_check', obj).hide()
        $('.empty_check', obj).show()
        $(obj).removeClass('item_selected')
        return false;
    }else{
        $('.empty_check', obj).hide()
        $('.selected_check', obj).show()
        $(obj).addClass('item_selected')
        return true;
    }
}

function set_item_click(){

    $('.item').on('click', function () {
        if(toggleSelect($(this))){

            items_selected.push({
                id:$(this).attr('data-id'),
                name:$(this).attr('data-name'),
                description:$(this).attr('data-description'),
                image:$(this).attr('data-image')
            })
        }else{
            for(i in items_selected){
                if(items_selected[i].id == $(this).attr('data-id')){

                    items_selected.splice(i,1);
                }
            }
        }
        if(items_selected.length > 0){
            $('.next-btn, .coupon-next, .c-edit-plans-save').prop('disabled',false)
        }else{
            $('.next-btn, .coupon-next, .c-edit-plans-save').prop('disabled',true)
        };
    })


    $('[data-toggle="tooltip"]').tooltip('dispose')
    
    $('[data-toggle="tooltip"]').tooltip({
        container: '.page'
    });
}

function mount_selected_items(search, search2){
    var items = ''

    if(items_selected.length == 0){
        return [];
    }

    for(i in items_selected){
        
        if(search){
            if(items_selected[i].name.toLowerCase().search(search.toLowerCase()) < 0){
                continue
            }
        }
        if(search2){
            if(items_selected[i].description.toLowerCase().search(search2.toLowerCase()) < 0){
                continue
            }
        }
        

        var toolTip
        if(items_selected[i].name.length > 18){

            toolTip = 'aria-describedby="tt'+items_selected[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+items_selected[i].name+'"'
        }else{
            toolTip = ''
        }
        var item = `<div ${toolTip} class="item item_selected"  data-id="`+items_selected[i].id+`" data-image="`+items_selected[i].image+`" data-name="`+items_selected[i].name+`" data-description="`+items_selected[i].description+`" >

                        <span style="background-image: url('https://cloudfox-files.s3.amazonaws.com/produto.svg')" class="image">
                            <span style="background-image: url(`+(items_selected[i].image?items_selected[i].image:'https://cloudfox-files.s3.amazonaws.com/produto.svg')+`)" class="image2"></span>
                        </span>
                        <span class="title text-overflow-title">`+items_selected[i].name+`</span>
                        <span class="description text-overflow-description">`+items_selected[i].description+`</span>
                        <svg class="selected_check "  width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"/>                            <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"/>                            </svg>
                        <svg class="empty_check " style="display: none" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9" stroke="#9B9B9B"/>                            </svg>
                    </div>`;
            items += item;
    }
    //$('#search_result, #search_result2').html(items);

    // $('[data-toggle="tooltip"]').tooltip()
    return items;
}

var items_selected = []

$(function () {

    $('#bt-search').click(function () {
        atualizarCoupon()
    })

    $('#search-name').on('keypress',function(e) {
        if(e.which == 13) {
            atualizarCoupon()
            return false
        }
    });

    let projectId = $(window.location.pathname.split('/')).get(-1);

    $('#value').mask('#.###,#0', {reverse: true});



    var rules = []
    var rule_id = 1;
    $("#add_rule1").on('click', function () {



        if(!$('#qtde').val() || $('#qtde').val() == 0){
            $('#qtde').focus()
            $('#qtde').addClass('warning-input')
            alertCustom("error", 'Digite um valor acima de 0');
            return false
        }

        if($("#type_percent").prop('checked') && (!$('#percent').val() || $('#percent').val() == 0 )){
            $('#percent').focus()
            $('#percent').addClass('warning-input')
            alertCustom("error", 'Digite um valor acima de 0');
            return false
        }

        if($("#type_value").prop('checked') && (!$('#value').val() || $('#value').val().replace(',','').replace('.','') == 0 )){
            $('#value').focus()
            $('#value').addClass('warning-input')
            alertCustom("error", 'Digite um valor acima de 0');
            return false
        }
        rules.push({
            id:rule_id++,
            buy:$('#buy').val(),
            type:$("#type_percent").prop('checked')?'percent':'value',
            qtde:$('#qtde').val(),
            value:$("#type_percent").prop('checked')?$('#percent').val():$('#value').val()
        })

        toggleDiscountRulesAlert(1)

        mount_rules(rules);
        return false;
    })

    function mount_rules(rules, edit){
        let rules_html = ''
        for(i in rules){
            rules_html += `

                        <div class="rule_holder">
                            <div class="rule_box">
                                Na compra
                                <strong>`+ (rules[i].buy=='above_of'?'acima de ':'de ') +
                                rules[i].qtde +` itens</strong>,
                                aplicar desconto de <strong>
                                `+ (rules[i].type=='percent'?rules[i].value+'%':'R$' + rules[i].value) +`
                                </strong>

                                <svg data-id="`+rules[i].id+`"  style="float:right; margin:4px 4px 0 18px" class="pointer delete" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 1L1 15M1 1L15 15L1 1Z" stroke="#5E6576" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                <svg data-id="`+rules[i].id+`" style="float:right;" class="pointer edit" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.8397 5.7993L19.8987 3.74294L17.2652 1.10974L15.2065 3.1661" stroke="#3D4456" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3.19598 15.163L5.82952 17.7962M5.82952 17.7962L17.8395 5.79928L15.2063 3.16608L3.19598 15.163L1.10156 19.8903L5.82952 17.7962V17.7962Z" stroke="#3D4456" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                            </div>

                            <div class="rule_box_edit" style="display:none">

                                Na compra
                                <select id="buy1" class="buy w-auto d-inline-block adjust-select" style="width: 124px; height: 44px;">
                                    <option `+ (rules[i].buy=='above_of'?'selected':'') +` value="above_of">acima de</option>
                                    <option `+ (rules[i].buy=='of'?'selected':'') +` value="of">de</option>
                                </select>
                                <input value="`+ rules[i].qtde +`" class="input-pad qtde" type="text" onkeyup="$(this).removeClass('warning-input')"
                                 style="width: 60px; height: 49px;
                                margin-top: 2px;" maxlength="2" data-mask="0#" />
                                itens, aplicar desconto de
                                <input maxlength="9" value="`+ (rules[i].type=='value'?rules[i].value:'') +`" class="input-pad value value_edit" type="text" onkeyup="$(this).removeClass('warning-input')"
                                 style="`+ (rules[i].type=='percent'?'display: none;':'') +` width: 86px; height:46px" />
                                <input value="`+ (rules[i].type=='percent'?rules[i].value:'') +`" type="text" onkeyup="$(this).removeClass('warning-input')"
                                 style="width: 86px; `+ (rules[i].type=='value'?'display: none;':'') +` height:46px" class="input-pad percent" maxlength="2"
                                    data-mask="0#" autocomplete="off">

                                <svg  class="pointer float-right save-edit-rule" width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.629 0.8994C19.1237 1.43193 19.1237 2.29534 18.629 2.82787L7.229 15.1006C6.73434 15.6331 5.93233 15.6331 5.43766 15.1006L0.370998 9.64605C-0.123666 9.11352 -0.123666 8.25011 0.370998 7.71758C0.865662 7.18505 1.66767 7.18505 2.16234 7.71758L6.33333 12.2079L16.8377 0.8994C17.3323 0.366867 18.1343 0.366867 18.629 0.8994Z" fill="#5E6576"/>
                                </svg>




                            </div>
                        </div>
                        `;
        }



        $('#rules').html(rules_html);

        $('#rules').mCustomScrollbar('destroy')
        $('#rules').mCustomScrollbar()

        $('.rule_box_edit select.buy').each(function () {
            
            $(this).siriusSelect()
        })




        $('.value').mask('#.###,#0', {reverse: true});

        $('.value_edit').on('change', function () {
            if($(this).val().length<3){
                $(this).val($(this).val().padStart(2, '0'))
                $(this).val(','+$(this).val())
                $(this).val($(this).val().padStart(4, '0'))
            }
        })

        set_rules_events()
        if(!edit){

            $('#percent').val('')
            $('#value').val('')
            $('#qtde').val('')
        }

        if(rules.length > 0){
            //$('.finish-btn').prop('disabled',false)
            $('#empty-rules').hide()
        }else{
            //$('.finish-btn').prop('disabled',true)
            $('#empty-rules').show()

        }
    }

    $(".finish-btn").on('click', function () {

        if(!toggleDiscountRulesAlert(rules.length)){
            return false
        }

        $('#discount_rules').val(JSON.stringify(rules))
        $('#discount_plans').val(JSON.stringify(items_selected))

        let formData = new FormData(document.getElementById('form-register-discount'));

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {


                errorAjaxResponse(response);
            },
            success: function success() {




                $("#modal-button-close-1").click();

                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Desconto adicionado!");
                atualizarCoupon();

                setTimeout(() => {
                    $('.edit-coupon').first().click()
                }, 400);

            }
        });
        return false;
    })

    function set_rules_events(){

        $(".delete").on('click', function () {
            var id = $(this).attr('data-id')

            for(i in rules){
                if(rules[i].id == id){
                    rules.splice(i,1)
                }
            }
            mount_rules(rules);
        })

        $(".edit").on('click', function () {
            var id = $(this).attr('data-id')
            $(this).parents('.rule_holder').find('.rule_box').hide()
            $(this).parents('.rule_holder').find('.rule_box_edit').show()

            for(i in rules){
                if(rules[i].id == id){
                    editingRule1 = i
                }
            }



            $('.finish-btn').prop('disabled', true);


        })

        $('.save-edit-rule').on('click', function(){
            var that = this;
            function go(obj) {
                return $(that).parents('.rule_holder').find(obj)
            }

            if(!go('.qtde').val() || go('.qtde').val()==0){
                go('.qtde').focus()
                go('.qtde').addClass('warning-input')
                alertCustom("error", 'Digite um valor acima de 0');
                return false;
            }
            if(rules[editingRule1].type=='percent'){
                if(!go('.percent').val() || go('.percent').val()==0){
                    go('.percent').focus()
                    go('.percent').addClass('warning-input')
                    alertCustom("error", 'Digite um valor acima de 0');
                    return false;
                }
            }else{
                if(!go('.value').val() || go('.value').val().replace(',','').replace('.','')==0){
                    go('.value').focus()
                    go('.value').addClass('warning-input')
                    alertCustom("error", 'Digite um valor acima de 0');
                    return false;
                }
            }

            rules[editingRule1].buy = go('#buy1').val(),
            rules[editingRule1].qtde = go('.qtde').val(),
            rules[editingRule1].value = rules[editingRule1].type=='percent'?go('.percent').val():go('.value').val()
            mount_rules(rules, 1);

            



            $('.finish-btn').prop('disabled', false);

            $(this).parents('.rule_holder').find('.rule_box_edit').hide()
            $(this).parents('.rule_holder').find('.rule_box').show()
        })
    }
    var editingRule1 = 0




    $("#type_percent").on('click', function () {

        $("#percent").show()
        $("#value").hide()
    })
    $("#type_value").on('click', function () {

        $("#value").show()
        $("#percent").hide()
    })
    $("#add-coupon").on('click', function () {

        $('#select-type').show();
        $('#select-coupon').hide();
        $('#select-discount').hide();

        $('.step1').show()
        $('.step2').hide()

        $('#step1').show()
        $('#step2').hide()

        $('#select-type-body').css( {'height':'281px'});

        $('#modal-create-holder').css( {'width':'400px'});

        $('#create_name').hide()


        items_selected = []
        //run_search('',1)
        $('#search_input_description_value').val('')

    })

    $('.next-btn').on('click', function () {
        $('#step1').hide();
        $('#step2').show();
        $('.finish-btn').prop('disabled',false)
        

    })

    $('.back-btn').on('click', function () {
        $('#step2').hide();
        $('#step1').show();
        $('#search_input').focus();
    })

    $('.cancel-btn').on('click', function () {
        $('#select-type').show();
        $('#select-coupon').hide();
        $('#select-discount').hide();

        $('#edit-finish-btn').show()
        $('#plans-actions').hide()

        $('#select-type-body').css( {'height':'281px'});

        $('#modal-create-holder').css( {'width':'400px'});

        $('#create_name').hide()

    })

    $('input').on('change', function () {
        $(this).val($(this).val().replace( /[^a-zA-Z0-9/,/. ]/gm, ''))

    })

    $('#coupon').on('click', function () {

        $('#date_range').removeClass('warning-input')
        $('#date_range').val('')

        $('#search_result, #search_result2').html('');

        $('#modal-create-holder').css( {'width':'600px'});



        $('.search_coupon').val('')

        $('#select-type').fadeOut('fast','',function () {

            $('#select-coupon').fadeIn('fast','', function () {

                $('.search_coupon').trigger('focus');

                $('#search_input_description_value').val('')
                run_search('', 1)


            })
        })



        $('#c_name').val('')
        $('#c_code').val('')
        $('#discount_value').val('')
        $('#percent_value').val('')
        $('#minimum_value').val('')
        $('#nao_vence').prop('checked', false)

        items_selected = []

    });

    $('#discount').on('click', function () {

        $('#select-type-body').animate(
            {'height':'417px'},
            '400',
            'swing',
            slide_name
        );

        function slide_name(params) {
            $('#create_name').fadeIn();
            $('#new_name').val('');
            $('#new_name').focus();
        }




    });

    $('#new_continue').on('click', function(){

        toggleDiscountRulesAlert(1)
        
        $('#date_range').prop('disabled', false)

        $('#search_result, #search_result2').html('');

        if(!$('#new_name').val()){
            $('#new_name').focus()
            return false
        }


        $('#discount_name').val($('#new_name').val())


        $('#select-type').fadeOut('fast','',function () {

            $('#select-discount').fadeIn('fast','', function () {

                $('#search_input').trigger('focus');

                $('#search_input_description_value').val('')
                run_search('', 1)


            })
        })




        $('#new_namme').val('')
        $('#search_input').val('')

        $('#modal-create-holder').css( {'width':'600px'});


        items_selected = []
        rules = []
        mount_rules(rules)
    })





    // Search type event
    var searchTimeout
    $('#search_input, #search_input2').on('keyup', function(){


        var search = $(this).val()

        $('[data-toggle="tooltip"]').tooltip('dispose')
        clearTimeout(searchTimeout)
        searchTimeout = setTimeout(
            () => {
                run_search(search)
            },
            1200
        )


    })

    
    var items_placeholder = `<div id="items_loading">
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div>
    <div class="item_placeholder"></div> </div>`

    var search_holder
    // function run_search(search, now){
    //     search_holder = search
    //     var search2 = $('#search_input_description_value').val()
        

    //     var items_saved = mount_selected_items()

    //     var loading = $('.item_placeholder').is(':visible')

    //     if(loading && !now) return

    //     if(search.length > 0 || now){

    //         $('#search_result, #search_result2').html(items_placeholder);
    //         //animateItemsPlaceholder()

    //         $.ajax({
    //             data: {
    //                     most_sales: 1,
    //                     list: 'plan',
    //                     search: search,
    //                     search2: search2,
    //                     items_saved: items_selected,
    //                     project_id: projectId,
    //                     limit:30
    //                     //page: params.page || 1
    //                 }
    //             ,

    //             method: "GET",
    //             url: "/api/plans/user-plans",

    //             dataType: "json",
    //             headers: {
    //                 'Authorization': $('meta[name="access-token"]').attr('content'),
    //                 'Accept': 'application/json',
    //             },
    //             error: function error(response) {
    //                 errorAjaxResponse(response);

    //             }, success: function success(response) {

    //                 if(search_holder != search){
    //                     run_search(search_holder, 1)
    //                     return
    //                 }

    //                 var data = response.data
    //                 var items = ''
    //                 for(plan in data){

    //                     var skip = false
    //                     for(i in items_selected){
    //                         if(items_selected[i].id == data[plan].id)
    //                             skip = true;
    //                     }
    //                     if(skip) continue;

    //                     var toolTip
    //                     if(data[plan].name.length > 18){

    //                         toolTip = 'aria-describedby="tt'+data[plan].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+data[plan].name+'"'
    //                     }else{
    //                         toolTip = ''
    //                     }

    //                     var item = `<div ${toolTip} class="item" data-id="`+data[plan].id+`" data-image="`+data[plan].photo+`" data-name="`+data[plan].name+`" data-description="`+data[plan].description+`" >

    //                                     <span style="background-image: url('https://cloudfox-files.s3.amazonaws.com/produto.svg')" class="image">

    //                                         <span style="background-image: url(`+(data[plan].photo?data[plan].photo:'https://cloudfox-files.s3.amazonaws.com/produto.svg')+`)" class="image2"></span>
    //                                     </span>

    //                                     <span class="title text-overflow-title">`+data[plan].name+`</span>
    //                                     <span class="description text-overflow-description">`+data[plan].description+`</span>
    //                                     <svg class="selected_check " style="display: none" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"/>                            <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"/>                            </svg>
    //                                     <svg class="empty_check " width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9" stroke="#9B9B9B"/>                            </svg>
    //                                 </div>`;
    //                     items += item;
    //                 }

    //                 if(items.length > 0 || (!search & !search2)){

    //                     $('#search_result, #search_result2').html(items + items_saved);
    //                     //$('#search_result, #search_result2').css('overflow-y', 'scroll')
    
    //                     $('#search_result, #search_result2').mCustomScrollbar('destroy')

    //                     $('#search_result, #search_result2').mCustomScrollbar()
    //                     // alert(0)
    //                     set_item_click()
    //                 }else{
    //                     $('#search_result, #search_result2').mCustomScrollbar('destroy')
                        
    //                     //$('#search_result, #search_result2').css('overflow-y', 'hidden')
    //                     $('#search_result, #search_result2').html(`
    //                     <div class="not-found">
    //                         <img src="/build/global/img/not-found.svg" >
    //                         <div class="title">
    //                         Nenhum resultado encontrado.</div>
    //                         <div class="description">
    //                         Por aqui, nenhum plano com esse nome.
    //                         </div>
    //                     </div>`);

    //                 }

    //             }
    //         });
    //     }else{

    //         run_search('',1)

    //     }
    // }

    // Create new cupouns
    $('#discount_value').mask('#.###,#0', {reverse: true});
    $('#minimum_value').mask('#.###,#0', {reverse: true});
    $('#2discount_value').mask('#.###,#0', {reverse: true});
    $('#2minimum_value').mask('#.###,#0', {reverse: true});


    $('.coupon-next').click(function () {
        $('.step1').hide()
        $('.step2').show()
        $('#c_name').focus()

    })

    // $('.add-coupon').click(function () {
    //     alert('i')
    // })

    $('.add-coupon-back').click(function () {
        $('.step2').hide()
        $('.step1').show()
        $('.search_coupon').focus()

    })


    $('#c_type_value').click(function () {
        $('#percent_opt').hide()
        $('#money_opt').show()
        $('#money_opt input').focus()
    })

    $('#c_type_percent').click(function () {
        $('#money_opt').hide()
        $('#percent_opt').show()
        $('#percent_opt input').focus()
    })

    // $('#c_name').keyup(validate_coupon);
    // $('#c_code').keyup(validate_coupon);
    // $('#discount_value').keyup(validate_coupon);
    // $('#percent_value').keyup(validate_coupon);
    // $('#minimum_value').keyup(validate_coupon);
    // $('#c_type_value').click(validate_coupon);
    // $('#c_type_percent').click(validate_coupon);

    function validate_coupon() {
        var ok = true;
        //
        if(!$('#c_name').val()) ok = false;

        if(!$('#c_code').val()) ok = false;

        if(!$('#minimum_value').val()) ok = false;

        if($('#c_type_value').prop('checked') && !$('#discount_value').val()) ok = false;

        if($('#c_type_percent').prop('checked') && !$('#percent_value').val()) ok = false;

        //
        // if(ok){
        //     $('.add-coupon').prop('disabled',false)
        // }else{
        //     $('.add-coupon').prop('disabled',true)
        // }

    }

    $('#value-edit, #value, #discount_value, #minimum_value, #2discount_value, #2minimum_value').on('change', function () {
        if($(this).val().length<3){
            $(this).val($(this).val().padStart(2, '0'))
            $(this).val(','+$(this).val())
            $(this).val($(this).val().padStart(4, '0'))
        }
    })

    $(".add-coupon").on('click', function () {
        if(!$('#c_name').val()){
            $('#c_name').focus().addClass('warning-input')
            alertCustom("error", 'Preencha um nome para o cupom');
            return false;
        }

        if(!$('#c_code').val()){
            $('#c_code').focus().addClass('warning-input')
            alertCustom("error", 'Preencha um código para o cupom');
            return false;
        }


        if($('#c_type_value').prop('checked') && (!$('#discount_value').val() || $('#discount_value').val().replace(',','').replace('.','') == 0 ) ) {
            $('#discount_value').focus().addClass('warning-input')
            
            alertCustom("error", 'Preencha um valor acima de R$ 0,00');
            return false;
        }
        if($('#c_type_percent').prop('checked') && (!$('#percent_value').val() || $('#percent_value').val() == 0) ){
            $('#percent_value').focus().addClass('warning-input')
            alertCustom("error", 'Preencha um valor acima de 0');

            return false;
        }
        if(!$('#minimum_value').val() || $('#minimum_value').val().replace(',','').replace('.','') == 0 ){
            alertCustom("error", 'Preencha um valor acima de R$ 0,00');

            $('#minimum_value').focus().addClass('warning-input')
            return false;
        }
        // console.log($('#date_range').val(), $('#nao_vence').is(':checked')); return false;
        if($('#date_range').val()=='' && !$('#nao_vence').is(':checked')){
            $('#date_range').focus().addClass('warning-input')
            alertCustom("error", 'Preencha uma data de vencimento ou marque "Não vence"');
            return false;
        }

        if($('#c_type_value').prop('checked')) $('#c_value').val($('#discount_value').val());

        if($('#c_type_percent').prop('checked')) $('#c_value').val($('#percent_value').val());

        $('#c_plans').val(JSON.stringify(items_selected))
        let formData = new FormData(document.getElementById('form-register-coupon'));




        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/couponsdiscounts",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {


                errorAjaxResponse(response);
            },
            success: function success() {






                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Cupom de desconto adicionado!");
                atualizarCoupon();

                $("#modal-button-close-4").click();

            }
        });
        return false;
    })

    $('#c-edit-name').click(edit_name);

    function edit_name(){
        $(this).hide()
        $('#c-edit-plans').hide()
        $('#c-edit-rules').hide()

        $('#c-display_name').hide()
        $('#c-display_name_edit').show()
        $('#edit-name-box-c').animate({'height':162})
        
        
        $('#c-name-edit').focus()
        $('#c-name-edit').val($('#c-d-name').html());
        $('#c-code-edit').val($('#d-code').html());



        $('#c-cancel_name_edit').click(function(){

            $('#c-edit-name').show()

            $('#c-display_name_edit').hide()
            $('#c-display_name').show()

            $('#c-edit-plans').show()
            $('#c-edit-rules').show()

            $('#edit-name-box-c').animate({height:68})

        })
    }
    
    var cupom_data = []
    $("#c-save_name_edit").on('click', function () {
        let formData = new FormData(document.getElementById('form-update-coupon'));
        let id = $('#coupon-id2').val();
        $('#c-cancel_name_edit').click()
        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/discounts/" + id,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                if (response.status === 400) {
                    //atualizarCoupon();
                }

                errorAjaxResponse(response);
            },
            success: function success(data) {

                alertCustom("success", data.message);
                atualizarCoupon();

                $('#c-display_name_edit').hide()
                $('#c-display_name').show()

                if($('#c-name-edit').val())
                    $('#c-d-name').html($('#c-name-edit').val())

                if($('#c-code-edit').val())
                    $('#d-code').html($('#c-code-edit').val())


                show_plans()
                $('.c-plans-back').click()

                $('#c-edit-plans').show()
                $('#c-edit-rules').show()

                $('#c-edit-name').show()


                //coupon_rules()
                // show_rules(edit_rules)
                plans_count2()

                if(items_selected.length == 0)
                    count_plans_coupons()


            }
        });
        return false
    });


    function count_plans2() { //thumbnails on editing
        
        $('.edit-plans-thumbs').html('')


        $.ajax({
            data: {
                    total: 1,
                    list: 'plan',
                    search: '',
                    project_id: projectId,
                    //page: params.page || 1
                }
            ,

            method: "GET",
            url: "/api/plans/user-plans",
            
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
                
            }, success: function success(response) {
                
                
                
                var toolTip = 'aria-describedby="tt'+response.thumbnails[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+response.thumbnails[i].name+'"'



                var html_show_plans = ''
                for(i in response.thumbnails){
                    html_show_plans += `<span ${toolTip} class="plan_thumbnail" style="width:56px; height:56px;
                    background-repeat: no-repeat; background-position: center center; 
                    background-size: cover !important; background: url('`+response.thumbnails[i].products[0].photo+`'), url('https://cloudfox-files.s3.amazonaws.com/produto.svg');)"></span>`
                }

                $('.edit-plans-thumbs').html(html_show_plans)

                $('[data-toggle="tooltip"]').tooltip('dispose')
    
                $('[data-toggle="tooltip"]').tooltip({
                    container: '.page'
                });

                if(response.total > 8){
                    var rest = response.total - 8
                    $('.edit-plans-thumbs').append('<div style="margin-top:14px" class="plans_rest">+'+rest+'</div>')

                }


                
            }
        });
        
    }


    $('#c-edit-plans').click(function () {

        // console.log('oi');
        // $('#search_result, #search_result2').html('');

        // $('.form-control').each(function(){    
        //     $(this).val('');
        // })

        $('#search_input2').val('')
        $('#search_input_description').val('')
        $('#search_input2').trigger('focus')


        $('#c-edit_step0').hide()
        $('#c-edit_step1').show()



        if(items_selected.length>0){
            var items_thumbs = ''
            for(i in items_selected){
                
                // if(i>7) break;
                
                var toolTip = 'aria-describedby="tt'+items_selected[i].id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="'+items_selected[i].name+'"'
                

                items_thumbs +=  `
                <span ${toolTip} class="plan_thumbnail" style="width:56px; height:56px;
                background-repeat: no-repeat; background-position: center center; 
                background-size: cover !important; background: url('`+items_selected[i].image+`'), url('/build/global/img/produto.png')"></span>`
                
            }
            
            $('.edit-plans-thumbs').html(items_thumbs)

            if(items_selected.length > 9){

                $('.edit-plans-thumbs-scroll').css('margin-bottom', 16)
                $('.edit-plans-thumbs-scroll').mCustomScrollbar('destroy')
                $('.edit-plans-thumbs-scroll').mCustomScrollbar({
                    axis: 'x',
                    advanced: {
                      autoExpandHorizontalScroll: true
                    }
                  })
            }else{
                $('.edit-plans-thumbs-scroll').mCustomScrollbar('destroy')
                $('.edit-plans-thumbs-scroll').css('margin-bottom', 0)

            }

            $('[data-toggle="tooltip"]').tooltip('dispose')
    
            $('[data-toggle="tooltip"]').tooltip({
                container: '.page'
            });

        }else{
            count_plans2()
        }

        $('#search_input_description_value').val('')
        run_search('',1);

    })

    $('.c-plans-back').click(function () {
        $('#c-edit_step1').hide()
        $('#c-edit_step2').hide()

        $('#c-edit_step0').show()
    });

    $('.c-edit-plans-save').click(function () {
        // console.log(items_selected);
        $('#edited-plans').val(JSON.stringify(items_selected))
        $('#c-save_name_edit').click()
        plans_count()
    });

    function plans_count() {
        if(items_selected.length > 0){
            
            var plans_count = items_selected.length + ' plano'+(items_selected.length>1?'s':'')
            $('#planos-count, #planos-count-edit').html(plans_count);

            $('#plans_holder').css('height','auto')
            $('#show_plans').css('margin-top','10px')

    
            //$('#show_plans').addClass('mostrar_mais_detalhes')

        }else{
            $('#plans_holder').css('height','158px')
            $('#show_plans').css('margin-top','20px')
            
            $('#planos-count, #planos-count-edit').html('Todos os planos');

            count_plans()
        }

        if(items_selected.length>2 && items_selected.length<11){
            $('#mostrar_mais').show();
            
        }else{
            $('#mostrar_mais').hide();
            $('#show_plans').removeClass('mostrar_mais_detalhes')
            $('#show_plans').css({
                height: "88px"
            });

        }
    }

    $('#c-edit-rules').click(function () {
        $('#c-edit_step0').hide()
        $('#c-edit_step2').show()
        if($('#nao_vence2').prop('checked')){
            $('#date_range2').prop('disabled', true)
        }

    })

    $('.rule-coupon-back').click(function () {
        $('#c-edit_step2').hide()
        $('#c-edit_step0').show()
    })


    $('#2c_type_value').click(function () {
        $('#2percent_opt').hide()
        $('#2money_opt').show()
        $('#2money_opt input').focus()
    })

    $('#2c_type_percent').click(function () {
        $('#2money_opt').hide()
        $('#2percent_opt').show()
        $('#2percent_opt input').focus()
    })

    $('.update-rule-coupon').click(function () {

        if($('#2c_type_value').prop('checked') && (!$('#2discount_value').val() || $('#2discount_value').val().replace(',','').replace('.','') == 0 ) ) {
            $('#2discount_value').focus().addClass('warning-input')
            alertCustom("error", 'Preencha um valor acima de R$ 0,00');

            return false;
        }
        if($('#2c_type_percent').prop('checked') && (!$('#2percent_value').val() || $('#2percent_value').val() == 0) ){
            $('#2percent_value').focus().addClass('warning-input')
            alertCustom("error", 'Preencha um valor acima de 0');

            return false;
        }
        if(!$('#2minimum_value').val() || $('#2minimum_value').val().replace(',','').replace('.','') == 0 ){
            $('#2minimum_value').focus().addClass('warning-input')
            alertCustom("error", 'Preencha um valor acima de R$ 0,00');

            return false;
        }

        if($('#2c_type_value').prop('checked')) $('#2c_value').val($('#2discount_value').val());

        if($('#2c_type_percent').prop('checked')) $('#2c_value').val($('#2percent_value').val());

        cupom_data.value = $('#2c_value').val()
        cupom_data.rule_value = $('#2minimum_value').val()
        cupom_data.type = $('#2c_type_value').prop('checked')?1:0
        cupom_data.expires = $('#date_range2').val()?' '+$('#date_range2').val():null

        if(cupom_data.expires){
            var da = moment(cupom_data.expires,'DD/MM/YYYY');
            var db = moment.now();
            
            cupom_data.expires_days = da.diff(db, 'days')
            cupom_data.expires_days++
        }

        cupom_data.code = $('#d-code').html()
        if($('#nao_vence2').prop('checked')) cupom_data.expires = 0
        
        
        coupon_rules(cupom_data)
        $('#c-save_name_edit').click()


    })
    
    $('#c-edit_status').click(function(){
        if($('#c-expire-label').html()=='Vencido'){
            alertCustom("error", "Não é possivel ativar um cupom vencido!");
            return false
        }
        if($(this).is(':checked')){
            // $('#c-edit_status_label').css('color', '#41DC8F');
            $('#c-edit_status_label').html('Desconto ativo');

        }else{
            // $('#c-edit_status_label').css('color', '#9B9B9B');
            $('#c-edit_status_label').html('Desativado');
        }
        $('#c-set_status').val(1)

        $('#c-save_name_edit').click()
    })

    $('#all-plans').click(function () {
        items_selected = []
        // $('.coupon-next').click()
        $('.step1').hide()
        $('.step2').show()
        $('#c_name').focus()

        return false
    })


    $('#all-plans2').click(function () {
        items_selected = []
        $('.c-edit-plans-save').click()
        return false
    })

    $('#all-plans3').click(function () {

        items_selected = []
        $('#step1').hide();
        $('#step2').show();

        return false
    })

    $('#all-plans4').click(function () {
        items_selected = []
        $('.btn-edit-plans-save').click()
        return false
    })


    $('#date_range, #date_range2').mask('99/99/9999',{placeholder:"DD/MM/YYYY"});

    $('#date_range').val('DD/MM/YYYY')
        .dateRangePicker({
            format: 'DD/MM/YYYY',
            singleDate: true,
            showShortcuts: true,
            startDate: new Date(),
	        endDate: false,
            selectForward: true,
            container: '#modal-create-coupon',
            customShortcuts: [
                {
                    name: 'Hoje',
                    dates: () => [moment().startOf('day').toDate(), new Date()]
                },
                {
                    name: '7 dias',
                    dates: () => [moment().add(6, 'days').toDate(), moment().add(6, 'days').toDate()]
                },
                {
                    name: '15 dias',
                    dates: () => [moment().add(14, 'days').toDate(), moment().add(14, 'days').toDate()]
                },
                {
                    name: '1 mês',
                    dates: () => [moment().add(30, 'days').toDate(), moment().add(30, 'days').toDate()]
                },
                {
                    name: '3 meses',
                    dates: () => [moment().add(90, 'days').toDate(), moment().add(90, 'days').toDate()]
                }
            ],
        }).bind('datepicker-opened',function()
        {
            $('.modal-open .modal').animate({scrollTop: $(document).height() + $(window).height()});
            $('.date-picker-wrapper').attr('tabindex',0).focus()
            
        });



    $('#mostrar_mais').click(function () {
        if($('#show_plans').hasClass('mostrar_menos')){

            $('#show_plans').stop(true, false).animate({
                height: "164px"
            });

            $('#show_plans').removeClass('mostrar_menos')

            //$('#show_plans').addClass('mostrar_mais_detalhes')

            $('#mostrar_mais_label').html('Mostrar menos')
            $('#mm-arrow-down').hide()
            $('#mm-arrow-up').show()

        }else{
            $('#show_plans').stop(true, false).animate({
                height: "88px"
            });


            var myDiv = document.getElementById('show_plans');
            myDiv.scrollTop = 0;

            $('#show_plans').removeClass('mostrar_mais_detalhes')

            $('#show_plans').addClass('mostrar_menos')

            $('#mostrar_mais_label').html('Ver todos os planos')
            $('#mm-arrow-up').hide()
            $('#mm-arrow-down').show()
        }

    })



    $('#mostrar_mais2').click(function () {
        if($('#c-show_plans').hasClass('mostrar_menos')){

            $('#c-show_plans').stop(true, false).animate({
                height: "164px"
            });

            $('#c-show_plans').removeClass('mostrar_menos')

            //$('#c-show_plans').addClass('mostrar_mais_detalhes')

            $('#mostrar_mais_label2').html('Mostrar menos')
            $('#mm-arrow-down2').hide()
            $('#mm-arrow-up2').show()

        }else{
            $('#c-show_plans').stop(true, false).animate({
                height: "88px"
            });

            var myDiv = document.getElementById('c-show_plans');
            myDiv.scrollTop = 0;

            $('#c-show_plans').removeClass('mostrar_mais_detalhes')

            $('#c-show_plans').addClass('mostrar_menos')

            $('#mostrar_mais_label2').html('Ver todos os planos')
            $('#mm-arrow-up2').hide()
            $('#mm-arrow-down2').show()
        }

    })

    function toggleDiscountRulesAlert(rules)
    {
        if(rules==0){

            $('.inputs-warning').addClass('warning')
            $('.warning-text').fadeIn()

            return false

        }else{
            $('.inputs-warning').removeClass('warning')
            $('.warning-text').fadeOut()
        }
        return true
    }


});
