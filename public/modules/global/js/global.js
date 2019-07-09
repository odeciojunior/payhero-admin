$(document).ready(function(){

    var current_url = window.location.pathname;

    if(current_url.includes('dashboard')){
        $("#dashboard_img").attr('src','/modules/global/assets/img/icon-red/dashboard-d.svg');
        $("#dashboard_img").parent().parent().addClass('menu-active');
        $("#dashboard_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('showcase')){
        $("#showcase_img").attr('src','/modules/global/assets/img/icon-red/vitrine-d.svg');
        $("#showcase_img").parent().parent().addClass('menu-active');
        $("#showcase_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('sales') || current_url.includes('recoverycart')){
        $("#sales_img").attr('src','/modules/global/assets/img/icon-red/vendas-d.svg');
        $("#sales_img").parent().parent().addClass('menu-active');
        $("#sales-link").addClass('menu-active');
        $("#sales_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('projects')){
        $("#projects_img").attr('src','/modules/global/assets/img/icon-red/projetos-d.svg');
        $("#projects_img").parent().parent().addClass('menu-active');
        $("#projects-link").addClass('menu-active');
        $("#projects_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('products')){
        $("#products_img").attr('src','/modules/global/assets/img/icon-red/produtos-d.svg');
        $("#products_img").parent().parent().addClass('menu-active');
        $("#products-link").addClass('menu-active');
        $("#products_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('atendimento')){
        $("#attendance_img").attr('src','/modules/global/assets/img/icon-red/atendimento-d.svg');
        $("#attendance_img").parent().parent().addClass('menu-active');
        $("#attendance_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('afiliados')){
        $("#affiliate_img").attr('src','/modules/global/assets/img/icon-red/afiliados-d.svg');
        $("#affiliate_img").parent().parent().addClass('menu-active');
        $("#affiliate_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('transfers')){
        $("#finances_img").attr('src','/modules/global/assets/img/icon-red/financas-d.svg');
        $("#finances_img").parent().parent().addClass('menu-active');
        $("#finances_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('reports')){
        $("#reports_img").attr('src','/modules/global/assets/img/icon-red/ferramentas-d.svg');
        $("#reports_img").parent().parent().addClass('menu-active');
        $("#reports_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('apps')){
        $("#apps_img").attr('src','/modules/global/assets/img/icon-red/aplicativos-d.svg');
        $("#apps_img").parent().parent().addClass('menu-active');
        $("#apps_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }
    else if(current_url.includes('invitations')){
        $("#invitations_img").attr('src','/modules/global/assets/img/icon-red/convites-d.svg');
        $("#invitations_img").parent().parent().addClass('menu-active');
        $("#invitations_img").css('height','28px').css('width','28px').css('margin-left','-5px');
    }

});

function alertCustom(type, message){

    swal({
        position: 'bottom',
        type: type,
        toast: 'true',
        title: message,
        showConfirmButton: false,
        timer: 6000
    });
}

$(document).ajaxStart(function(event, jqXHR, ajaxOptions, data) {
    $(".loading").css("visibility", "visible");
})

$(document).ajaxError(function(event, jqXHR, ajaxOptions, data) {
    $(".loading").css("visibility", "hidden");
})

$(document).ajaxSuccess(function(event, jqXHR, ajaxOptions, data) {
    $(".loading").css("visibility", "hidden");
})

function loading(elementId)
{
    $(elementId).html('');
    $(elementId).append('<div class="loading"></div>');

}




