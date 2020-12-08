$(document).ready(function () {

    let projectId = $(window.location.pathname.split('/')).get(-1);
    let previewImage = $("#previewimage");
    //loadReviews();
    $('#tab_reviews').on('click', function () {
        loadReviews();
    })

    $.ajax({
        method: "GET",
        url: "/api/projectreviewsconfig/" + projectId,
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        }, success: function success(response) {
            let reviewConfig = response.data;
            localStorage.setItem('icon_type', reviewConfig.icon_type);
            localStorage.setItem('icon_color', reviewConfig.icon_color);
        }
    });

    var initStarsPlugin = function (el, score, readOnly = true) {
        var icon = localStorage.getItem('icon_type') || 'star';
        var starHalf = icon === 'star' ? `fa fa-${icon}-half-o` : `fa fa-${icon}`;
        var $el = $(el);
        $el.off();
        $el.html('');
        $el.css({'color': localStorage.getItem('icon_color')})
        $el.raty({
            half: true,
            readOnly: readOnly,
            starType: 'i',
            score: score,
            starHalf: starHalf,
            starOff: `fa fa-${icon}-o`,
            starOn: `fa fa-${icon}`,
            scoreName: 'stars'
        });
    }

    function loadReviews() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var url = '/api/projectreviews';

        if (link != null) {
            url += link;
        }

        loadOnTable('#data-table-reviews', '#table-reviews');
        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            data: {project_id: projectId},
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let dataTable = $('#data-table-reviews');
                let tableReviews = $('#table-reviews');

                dataTable.html('');

                if (response.data == '') {
                    dataTable.html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhum review encontrado</td></tr>");
                    tableReviews.addClass('table-striped');
                } else {
                    $('#config-review').removeClass('d-none').addClass('d-flex');
                    tableReviews.addClass('table-striped');
                    let data = '';
                    $.each(response.data, function (index, value) {
                        data = `
                        <tr>
                            <td>
                                <img src="${value.photo || 'https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/user-default.png'}"
                                class="img-fluid rounded-circle" width="35" height="35">
                                ${value.name}
                            </td>
                            <td>${value.description.substring(0, 50)}...</td>
                            <td>
                                <div id="stars-${value.id}" data-score="${value.stars}"></div>
                            </td>
                            <td>${value.active_flag ? `<span class="badge badge-success text-left">Ativo</span>` : `<span class="badge badge-danger">Desativado</span>`}</td>
                            <td style='text-align:center'>
                                <a role='button' title='Visualizar' class='mg-responsive details-review pointer' data-review="${value.id}" data-target='#modal-detail-review' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></a>
                                <a role='button' title='Editar' class='pointer edit-review mg-responsive' data-review="${value.id}"><i class='material-icons gradient'> edit </i></a>
                                <a role='button' title='Excluir' class='pointer delete-review mg-responsive' data-review="${value.id}" data-toggle="modal" data-target="#modal-delete-review"><i class='material-icons gradient'> delete_outline </i></a>
                            </td>
                        </tr>
                        `;
                        dataTable.append(data);
                        initStarsPlugin('#stars-' + value.id);
                    });
                    $('.div-config').show();
                    pagination(response, 'review', loadReviews);
                }

            }
        });
    }

    $("#add-review").on('click', function () {
        $('#modal_add_review .modal-title').html("Novo review");
        $(".bt-review-save").show();
        $(".bt-review-update").hide();

        $('#form_edit_review').hide()

        var form = $('#form_add_review');
        form.show();
        form.find('#add_name').val('');
        form.find('#add_description_review').val('');
        form.find('.stars').html('');

        initStarsPlugin('#review_add_stars', 5, false);
    });

    $(document).on('click', '.bt-review-save', function () {
        loadingOnScreen();
        var form_data = new FormData(document.getElementById('form_add_review'));
        form_data.append('project_id', projectId);

        $.ajax({
            method: "POST",
            url: "/api/projectreviews",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#modal_add_review').modal('hide');
                loadingOnScreenRemove();
                loadReviews();
                alertCustom('success', response.message);
                $("#add_review_apply_on_plans").val(null).trigger('change');
            }
        });
    });

    $(document).on('click', '.edit-review', function (event) {
        event.preventDefault();
        let reviewId = $(this).data('review');
        $('#modal_add_review .modal-title').html("Editar review");
        $(".bt-review-save").hide();
        $("#form_add_review").hide();
        $(".bt-review-update").show();

        var form = $('#form_edit_review');
        form.show();
        form.find('#edit_name').val('');
        form.find('#edit_description_review').val('');
        form.find('#review_edit_stars').html('');
        form.find('.review-id').val(reviewId);

        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/projectreviews/" + reviewId + '/edit',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {

            }, success: function (response) {
                let review = response.data;
                form.find('[name=name]').val(review.name);
                form.find('[name=description]').val(review.description);
                form.find('[name=active_flag]').val(review.active_flag);

                // Seleciona a opção do select de acordo com o que vem do banco
                form.find('#edit_review_apply_on_plans').html('');
                let applyOnPlans = [];
                for (let plan of review.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    form.find('#edit_review_apply_on_plans').append(`<option value="${plan.id}">${plan.name + (plan.description ? ' - ' + plan.description : '')}</option>`);
                }
                form.find('#edit_review_apply_on_plans').val(applyOnPlans).trigger('change');

                initStarsPlugin('#review_edit_stars', review.stars, false);


                // --------------------------
                // --------------------------


                // var p = $("#previewReviewImage");
                // $('#photoProject').unbind('change');
                // $("#photoProject").on('change', function () {
                //     var imageReader = new FileReader();
                //     imageReader.readAsDataURL(document.getElementById("photoProject").files[0]);
                //
                //     imageReader.onload = function (oFREvent) {
                //         p.attr('src', oFREvent.target.result).fadeIn();
                //
                //         p.on('load', function () {
                //
                //             var img = document.getElementById('previewimage');
                //             var x1, x2, y1, y2;
                //
                //             if (img.naturalWidth > img.naturalHeight) {
                //                 y1 = Math.floor(img.naturalHeight / 100 * 10);
                //                 y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                //                 x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                //                 x2 = x1 + (y2 - y1);
                //             } else {
                //                 if (img.naturalWidth < img.naturalHeight) {
                //                     x1 = Math.floor(img.naturalWidth / 100 * 10);
                //                     x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                //                     y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                //                     y2 = y1 + (x2 - x1);
                //                 } else {
                //                     x1 = Math.floor(img.naturalWidth / 100 * 10);
                //                     x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                //                     y1 = Math.floor(img.naturalHeight / 100 * 10);
                //                     y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                //                 }
                //             }
                //
                //             $('#previewimage').imgAreaSelect({
                //                 x1: x1, y1: y1, x2: x2, y2: y2,
                //                 aspectRatio: '1:1',
                //                 handles: true,
                //                 imageHeight: this.naturalHeight,
                //                 imageWidth: this.naturalWidth,
                //                 onSelectEnd: function (img, selection) {
                //                     $('#photo_x1').val(selection.x1);
                //                     $('#photo_y1').val(selection.y1);
                //                     $('#photo_w').val(selection.width);
                //                     $('#photo_h').val(selection.height);
                //                 }
                //             });
                //         })
                //     };
                // });
                //
                // $("#previewimage").unbind('click');
                // $("#previewimage").on("click", function () {
                //     $("#photoProject").click();
                // });

                // --------------------------
                // --------------------------


                loadingOnScreenRemove();
                $('#modal_add_review').modal('show');
                // END
            }
        });
    });

    $(document).on('click', '.delete-review', function (event) {
        event.preventDefault();
        let reviewId = $(this).data('review');
        $('.btn-delete-review').unbind('click');
        $('.btn-delete-review').on('click', function () {
            loadingOnScreen();
            $.ajax({
                method: "DELETE",
                url: "/api/projectreviews/" + reviewId,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    loadingOnScreenRemove()
                },
                success: function (response) {
                    loadingOnScreenRemove();
                    loadReviews();
                    alertCustom('success', response.message);
                }
            });
        });
    });

    $(document).on('click', '.bt-review-update', function (event) {
        event.preventDefault();
        loadingOnScreen();
        var form_data = new FormData(document.getElementById('form_edit_review'));
        let reviewId = $('#form_edit_review .review-id').val();
        $.ajax({
            method: "POST",
            url: "/api/projectreviews/" + reviewId,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $('#modal_add_review').modal('hide');
                loadingOnScreenRemove();
                loadReviews();
                alertCustom('success', response.message);
            }
        });
    });

    $(document).on('click', '.details-review', function (event) {
        event.preventDefault();
        let reviewId = $(this).data('review');
        $.ajax({
            method: "GET",
            url: "/api/projectreviews/" + reviewId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);

            }, success: function success(response) {
                let review = response.data;

                $('.review-photo').attr('src', review.photo || 'https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/user-default.png');
                $('.review-name').html(review.name);
                $('.review-description').html(review.description);
                $('.review-status').html(`${review.active_flag ? `<span class="badge badge-success text-left">Ativo</span>` : `<span class="badge badge-danger">Desativado</span>`}`);

                initStarsPlugin('.preview-stars', review.stars);

                var reviewApplyPlans = $('.review-apply-plans');
                reviewApplyPlans.html('');
                for (let applyPlan of review.apply_on_plans) {
                    reviewApplyPlans.append(`<span>${applyPlan.name}</span><br>`);
                }
            }
        });
    });

// Search plan
    $('#add_review_apply_on_plans, #edit_review_apply_on_plans, #add_review_offer_on_plans, #edit_review_offer_on_plans').select2({
        placeholder: 'Nome do plano',
        multiple: true,
        dropdownParent: $('#modal_add_review'),
        language: {
            noResults: function () {
                return 'Nenhum review encontrado';
            },
            searching: function () {
                return 'Procurando...';
            },
            loadingMore: function () {
                return 'Carregando mais reviews...';
            },
        },
        ajax: {
            data: function (params) {
                return {
                    list: 'plan',
                    search: params.term,
                    project_id: projectId,
                    page: params.page || 1
                };
            },
            method: "GET",
            url: "/api/plans/user-plans",
            delay: 300,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processResults: function (res) {
                let elemId = this.$element.attr('id');
                if ((elemId === 'add_review_apply_on_plans' || elemId === 'edit_review_apply_on_plans') && res.meta.current_page === 1) {
                    let allObject = {
                        id: 'all',
                        name: 'Qualquer plano',
                        description: ''
                    };
                    res.data.unshift(allObject);
                }

                return {
                    results: $.map(res.data, function (obj) {
                        return {id: obj.id, text: obj.name + (obj.description ? ' - ' + obj.description : '')};
                    }),
                    pagination: {
                        'more': res.meta.current_page !== res.meta.last_page
                    }
                };
            },
        }
    });

    $('#add_review_apply_on_plans, #edit_review_apply_on_plans').on('select2:select', function () {
        let selectPlan = $(this);
        if ((selectPlan.val().length > 1 && selectPlan.val().includes('all')) || (selectPlan.val().includes('all') && selectPlan.val() !== 'all')) {
            selectPlan.val('all').trigger("change");
        }
    });

    $(document).on('click', '#config-review', function (event) {
        event.preventDefault();
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/projectreviewsconfig/" + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);

            }, success: function success(response) {
                loadingOnScreenRemove();
                let reviewConfig = response.data;
                let formConfigReview = $('#form_config_review');

                formConfigReview.find('[name=icon_color]').val(reviewConfig.icon_color)
                formConfigReview.find('[name=icon_type][value=' + reviewConfig.icon_type + ']').prop('checked', true)
                formConfigReview.find('[name=icon_type]').parent('.radio-custom').find('i').css({color: reviewConfig.icon_color})

                let colorOptions = formConfigReview.find('.color-options > div');
                colorOptions.removeClass('active');
                formConfigReview.find('.color-options').find('[data-color="' + String(reviewConfig.icon_color).toLowerCase() + '"]').addClass('active')
                colorOptions.off().on('click', function () {
                    let color = $(this).data('color')
                    formConfigReview.find('[name=icon_color]').val(color);
                    formConfigReview.find('[name=icon_type]').parent('.radio-custom').find('i').css({color: color})
                    colorOptions.removeClass('active');
                    $(this).addClass('active');
                });

                $('#modal_config_review').modal('show');
            }
        });
    });

    $(document).on('click', '.bt-review-config-update', function (event) {
        event.preventDefault();
        // if ($('#countdown_flag').is(':checked') && $('#countdown_time').val() == '') {
        //     alertCustom('error', 'Preencha o campo Contagem');
        //     return false;
        // }
        loadingOnScreen();
        var form_data = new FormData(document.getElementById('form_config_review'));

        $.ajax({
            method: "POST",
            url: "/api/projectreviewsconfig/" + projectId,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                localStorage.setItem('icon_type', form_data.get('icon_type'))
                localStorage.setItem('icon_color', form_data.get('icon_color'))
                loadReviews();
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });

    $(document).on('click', '.btn-return-to-config', function (event) {
        event.preventDefault();
        $('#modal-view-review-config').modal('hide');
        $('#modal_config_review').modal('show');
    });

    // $(document).on('click', '.btn-view-config', function (event) {
    //     event.preventDefault();
    //     $('#modal_config_review').modal('hide');
    //     loadingOnScreen();
    //     $.ajax({
    //         method: "POST",
    //         url: "/api/projectreviewsconfig/previewreview",
    //         dataType: "json",
    //         data: {
    //             project_id: projectId,
    //         },
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         error: function error(response) {
    //             loadingOnScreenRemove();
    //             errorAjaxResponse(response);
    //
    //         }, success: function success(response) {
    //             loadingOnScreenRemove();
    //             let review = response.data;
    //
    //             $('#div-review-products').html('');
    //
    //             $('#review-header').html(review.header);
    //             $('#review-title').html(review.title);
    //             $('#review-description').html(review.description);
    //
    //             if (review.countdown_flag) {
    //                 $('#timer').show();
    //                 startCountdown(review.countdown_time);
    //             } else {
    //                 $('#timer').hide();
    //             }
    //
    //             let data = "";
    //
    //             for (let key in review.plans) {
    //
    //                 let plan = review.plans[key];
    //                 data += `<div class="product-info">
    //                             <div class="d-flex flex-column">`;
    //                 for (let product of plan.products) {
    //                     let firstVariant = Object.keys(product)[0];
    //                     data += `<div class="product-row">
    //                                 <img src="${product[firstVariant].photo}" class="product-img">
    //                                 <div class="ml-4">
    //                                     <h3>${product[firstVariant].amount}x ${product[firstVariant].name}</h3>`;
    //                     if (Object.keys(product).length > 1) {
    //                         data += `<select class="product-variant">`;
    //                         for (let i in product) {
    //                             data += `<option value="${i}">${product[i].description}</option>`;
    //                         }
    //                         data += `</select>`;
    //                     } else {
    //                         data += `<span class="text-muted">${product[firstVariant].description}</span>`;
    //                     }
    //                     data += `</div>
    //                          </div>`;
    //                 }
    //                 data += `</div>
    //                             <div class="d-flex flex-column mt-4 mt-md-0">`;
    //                 if (plan.discount) {
    //                     data += `<span class="original-price line-through">R$ ${plan.original_price}</span>
    //                                              <div class="d-flex mb-2">
    //                                                  <span class="price font-30 mr-1" style="line-height: .8">R$ ${plan.price}</span>
    //                                                  <span class="discount text-success font-weight-bold">${plan.discount}% OFF</span>
    //                                              </div>`;
    //                 }
    //
    //                 if (!isEmpty(plan.installments)) {
    //                     data += `<div class="form-group">
    //                                 <select class="installments">`;
    //                     for (let installment of plan.installments) {
    //                         data += `<option value="${installment['amount']}">${installment['amount']}X DE R$ ${installment['value']}</option>`;
    //                     }
    //                     data += `</select>
    //                          </div>`;
    //                 } else {
    //                     data += `<h2 class="text-primary mb-md-4"><b>R$ ${plan.price}</b></h2>`;
    //                 }
    //                 data += `<button class="btn btn-success btn-lg btn-buy">COMPRAR AGORA</button>
    //                      </div>
    //                 </div>`;
    //
    //                 if (parseInt(key) !== (review.plans.length - 1)) {
    //                     data += `<hr class="plan-separator">`;
    //                 }
    //             }
    //
    //             $('#div-review-products').append(data);
    //
    //             $('#modal-view-review-config').modal('show');
    //         }
    //     });
    //
    // });
});
