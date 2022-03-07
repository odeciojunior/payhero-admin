<form id="form-register-discount">
    @csrf
    <div style="display: none" id="select-discount" class="">
        <div class="modal-content  s-border-radius" style="width:646px; height:706px">
            <div class="mdtpad simple-border-bottom ">
                <span class="  " id="modal-title"
                    style="color:#787878; font: normal normal bold 22px Muli;">
                    Novo desconto progressivo
            </span>
                <a id="modal-button-close-1" class="pointer close" role="button" data-dismiss="modal"
                    aria-label="Close">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1.73071L15 16.6999M15 1.73071L1 16.6999" stroke="#636363" stroke-width="2"
                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <div id="step1">
                <div style='min-height: 100px; position: relative; padding: 27px 24px 24px 29px'
                    class=" simple-border-bottom">

                    <div style="margin-bottom: 21px">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M23.2661 11C24.2237 11 25 11.7763 25 12.734V16.9434C25 17.6076 24.7362 18.2445 24.2667 18.7142L17.7155 25.2673C16.7372 26.2437 15.153 26.2444 14.1734 25.2684L10.7351 21.8351C9.75607 20.8578 9.75482 19.2719 10.7322 18.293L17.2823 11.7347C17.7521 11.2643 18.3896 11 19.0544 11H23.2661ZM23.2661 12.156H19.0544C18.6964 12.156 18.3531 12.2983 18.1002 12.5516L11.5405 19.1196C11.024 19.6475 11.0279 20.494 11.5518 21.0169L14.9897 24.4499C15.5175 24.9757 16.3717 24.9754 16.8985 24.4496L23.4492 17.8969C23.702 17.644 23.8441 17.301 23.8441 16.9434V12.734C23.8441 12.4148 23.5853 12.156 23.2661 12.156ZM21.1468 13.699C21.7852 13.699 22.3028 14.2166 22.3028 14.855C22.3028 15.4934 21.7852 16.011 21.1468 16.011C20.5084 16.011 19.9909 15.4934 19.9909 14.855C19.9909 14.2166 20.5084 13.699 21.1468 13.699Z" fill="#2E85EC"/>
                            </svg>
                            

                        <span class="sub-title"> Selecione os planos no desconto </span>

                        <div class="custom-control custom-checkbox" style="position: absolute; right: 32px; top: 30px;">
                            <span id="all-plans3" class="pointer"
                                style="color: #2E85EC; font-size: 16px; font-weight: 700">

                                Selecionar todos
                                <svg class=" " style="margin-left: 4px;
                                    margin-top: -2px;" width="19" height="20" viewBox="0 0 19 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"></circle>
                                    <path
                                        d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z"
                                        fill="white"></path>
                                </svg>
                            </span>

                        </div>

                    </div>

                    {{-- <div style="position: relative;">

                        <input class="input-pad" id="search_input" style="height: 48px; padding-right: 38px" autocomplete="off"
                            autofocus placeholder="Pesquiser plano por nome" type="text">
                        <svg style="position: absolute;
                            right: 7px;
                            top: 15px;" width="18" height="18" viewBox="0 0 18 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.9633 12.0239C9.80854 12.9477 8.34378 13.5001 6.75 13.5001C3.02208 13.5001 0 10.478 0 6.75003C0 3.02209 3.02208 0 6.75 0C10.4779 0 13.5 3.02209 13.5 6.75003C13.5 8.34377 12.9477 9.80852 12.024 10.9633L17.7803 16.7197C18.0732 17.0126 18.0732 17.4874 17.7803 17.7803C17.4874 18.0732 17.0126 18.0732 16.7197 17.7803L10.9633 12.0239ZM12 6.75003C12 3.85052 9.6495 1.50001 6.75 1.50001C3.85051 1.50001 1.5 3.85052 1.5 6.75003C1.5 9.64953 3.85051 12 6.75 12C9.6495 12 12 9.64953 12 6.75003Z"
                                fill="#636363" />
                        </svg>
                    </div> --}}
                    <div class="d-flex modal-new-layout box-description">
                        <input class="form-control form-control-lg search_input_create_discount" type="text" id="search_input" placeholder="Pesquisa por nome" style="border-top-right-radius: 0;border-bottom-right-radius: 0; height: 48px !important; border-right: 0;">
                        <div class="input-group input-group-lg" style="width: 650px;">
                            <input onkeyup="set_description_value(this, $('.search_input_create_discount'))" class="form-control" type="text" id="search_input_description" placeholder="Pesquisa por descrição" style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <img src="/modules/global/img/icon-search.svg" alt="Icon Search">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div id="search_result" class="mt-20 "
                        style=" height: 362px; width: 596px">

                        {{-- <div class="item item_selected" >
                            <span style="background-image: url(http://dev.woo.com/wp-content/uploads/2021/07/sunglasses-2.jpg)" class="image"></span>
                            <span class="title">Nome do produto mui </span>
                            <span class="description">Descrição do produto muito longo muito longo muito longo </span>
                            
                            <svg class="selected_check" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"/>
                                <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"/>
                                </svg>
                                
                            <svg class="empty_check " style="display: none" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="9.5" cy="10" r="9" stroke="#9B9B9B"/>
                                </svg>
                                
                        </div> --}}

                        {{-- <div class="item" >
                            <span style="background-image: url(http://dev.woo.com/wp-content/uploads/2021/07/sunglasses-2.jpg)" class="image"></span>
                            <span class="title">Nome do produto</span>
                            <span class="description">Descrição do produto</span>
                            <svg class="selected_check " style="display: none" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9.5" fill="#2E85EC"/>                            <path d="M13.5574 6.75215C13.7772 6.99573 13.7772 7.39066 13.5574 7.63424L8.49072 13.2479C8.27087 13.4915 7.91442 13.4915 7.69457 13.2479L5.44272 10.7529C5.22287 10.5093 5.22287 10.1144 5.44272 9.87083C5.66257 9.62725 6.01902 9.62725 6.23887 9.87083L8.09265 11.9247L12.7612 6.75215C12.9811 6.50856 13.3375 6.50856 13.5574 6.75215Z" fill="white"/>                            </svg>    
                            <svg class="empty_check " width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">                            <circle cx="9.5" cy="10" r="9" stroke="#9B9B9B"/>                            </svg>
                        </div> --}}


                    </div>

                </div>

                <div class="modal-footer " style="padding: 10px 20px 20px">
                    <div style="width: 100%" class="justify-center text-center mt-10 ">
                        <button class="btn cancel-btn" type="button">Voltar</button>
                        <button class="btn btn-primary next-btn" style="margin-left: 10px" disabled
                            type="button">Continuar</button>
                    </div>
                </div>

            </div>

            <div id="step2" style="display: none; position: relative; height: 622px">
                <div style='' class=" ">
                    <input name="name" id="discount_name" value="" type="hidden">
                    <input name="value" id="" value="1" type="hidden">
                    <input name="code" id="" value="" type="hidden">
                    <input name="discount" id="" value="1" type="hidden">
                    <input name="status" id="" value="1" type="hidden">
                    <input name="rule_value" id="" value="1" type="hidden">
                    <input name="plans" id="discount_plans" value="" type="hidden">
                    <input name="progressive_rules" id="discount_rules" value="" type="hidden">

                </div>

                <div style='min-height: 100px' class="inputs-warning pl-30 pr-25 simple-border-bottom">

                    <div class="mt-20 mb-20">

                        
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="8" fill="#F8F8F8"/>
                            <path d="M21.9925 12.8558C22.0509 12.4458 21.7659 12.0661 21.3559 12.0076C20.9458 11.9491 20.566 12.2341 20.5075 12.644L20.171 15.0028L16.6864 15.0025L16.9925 12.8587C17.051 12.4487 16.766 12.0689 16.356 12.0104C15.9459 11.9519 15.566 12.2368 15.5075 12.6468L15.1712 15.0023L13.7501 15.0021C13.3359 15.0021 13 15.3378 13 15.7519C13 16.166 13.3357 16.5017 13.7499 16.5018L14.9571 16.5019L14.5289 19.5009L12.75 19.501C12.3358 19.501 12 19.8367 12 20.2509C12 20.665 12.3358 21.0007 12.75 21.0006L14.3148 21.0006L14.0088 23.1441C13.9503 23.5541 14.2352 23.9338 14.6453 23.9924C15.0553 24.0509 15.4352 23.766 15.4937 23.356L15.83 21.0005L19.3153 21.0004L19.0094 23.1442C18.951 23.5542 19.236 23.9339 19.646 23.9924C20.0561 24.0509 20.4359 23.7659 20.4944 23.356L20.8305 21.0003L22.25 21.0002C22.6642 21.0002 23 20.6645 23 20.2504C23 19.8363 22.6642 19.5006 22.25 19.5006L21.0444 19.5007L21.4722 16.5026L23.2499 16.5028C23.6641 16.5028 24 16.1671 24 15.753C24 15.3389 23.6643 15.0032 23.2501 15.0031L21.6861 15.003L21.9925 12.8558ZM19.957 16.5024L19.5293 19.5007L16.0441 19.5009L16.4723 16.5021L19.957 16.5024Z" fill="#2E85EC"/>
                        </svg>
                        
                        
                        <span class="sub-title" style="padding-left: 10px; margin-bottom: 10px">
                            Regras para aplicação de desconto </span>
                    </div>






                    <label class=" mb-10">Adicionar novo desconto de</label>
                    <div class="row ">
                        <div class="col-3">
                            <input name="type" value="1" class="discount_radio " type="radio" id="type_value" checked style="outline: none" />
                            <label for="type_value">
                                Valor em R$
                            </label>
                        </div>
                        <div class="col-3">
                            <input name="type" value="0" class="discount_radio " type="radio" id="type_percent" style="outline: none"/>
                            <label for="type_percent">Porcentagem</label>
                        </div>
                    </div>

                    <div class="mt-10 h-60">

                        <div class="float-left " style="padding: 14px 8px 0 0">

                            Na compra
                        </div>


                        <div class="float-left" style="padding-right: 4px">

                            <select id="buy" class="sirius-select w-auto d-inline-block adjust-select">
                                <option value="above_of">acima de</option>
                                <option value="of">de</option>
                            </select>
                        </div>

                        <div class="float-left">

                            <input class="input-pad " id="qtde" type="text"
                                style="margin: 0 1px; width: 60px; height:49px" maxlength="2" data-mask="0#" />
                            itens, aplicar desconto de
                            <input class="input-pad " maxlength="9" id="value" type="text"
                                style="width: 86px; height:48px; margin-right: 8px" />
                            <input class="input-pad " type="text"
                                style="width: 86px; display: none; height:48px; margin-right: 8px" id="percent"
                                maxlength="2" data-mask="0#" autocomplete="off">
                        </div>

                        <div class="float-left">
                            <div id="add_rule1" class="add_rule pointer"
                                style=" margin-top: 1px; margin-left:0px; float:right; height:46px; width: 46px">
                            </div>
                        </div>



                    </div>

                    <div class="clear-both" style="height: 22px">

                        <div class="clear-both warning-text" style="font-weight: normal;
                                font-size: 13px;
                                line-height: 16px;
                                display:none;
                                color: #DE0000;">
                            <svg style="margin-top: -4px; margin-right: 4px " width="12" height="12" viewBox="0 0 12 12"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6 0C9.31371 0 12 2.68629 12 6C12 9.31371 9.31371 12 6 12C2.68629 12 0 9.31371 0 6C0 2.68629 2.68629 0 6 0ZM6 8C5.58579 8 5.25 8.33579 5.25 8.75C5.25 9.16421 5.58579 9.5 6 9.5C6.41421 9.5 6.75 9.16421 6.75 8.75C6.75 8.33579 6.41421 8 6 8ZM6 2.5C5.75454 2.5 5.55039 2.67688 5.50806 2.91012L5.5 3V6.5L5.50806 6.58988C5.55039 6.82312 5.75454 7 6 7C6.24546 7 6.44961 6.82312 6.49194 6.58988L6.5 6.5V3L6.49194 2.91012C6.44961 2.67688 6.24546 2.5 6 2.5Z"
                                    fill="#DE0000" />
                            </svg>
    
                            Você precisa adicionar pelo menos uma regra para criar um desconto progressivo
                        </div>

                    </div>    

                    <div class="mt-20 mb-10 clear-both">

                        <div style="font-size: 14px; font-weight: 700">Regras adicionadas</div>
                    </div>
                </div>


                <div id="rules" style="
                        height: 242px;
                        padding: 0 15px;
                        margin: 15px;
                        overflow-x: hidden;
                    ">
                    {{-- <div class="rule_box p-10">
                        Teste
                    </div> --}}
                </div>

                <div id="empty-rules" class="row" style="position: absolute; bottom:136px">
                    <div class="col-4">

                        <svg style="margin:45px 0 0 80px" width="150" height="153" viewBox="0 0 150 153" fill="none"
                            xmlns="http://www.w3.org/2000/svg">

                            <path
                                d="M75 150C116.421 150 150 116.421 150 75C150 33.5786 116.421 0 75 0C33.5786 0 0 33.5786 0 75C0 116.421 33.5786 150 75 150Z"
                                fill="url(#paint0_linear_397:1078)" />
                            <g filter="url(#filter0_d_397:1078)">
                                <mask id="mask0_397:1078" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                    width="150" height="150">
                                    <path
                                        d="M75 150C116.421 150 150 116.421 150 75C150 33.5786 116.421 0 75 0C33.5786 0 0 33.5786 0 75C0 116.421 33.5786 150 75 150Z"
                                        fill="url(#paint1_linear_397:1078)" />
                                </mask>
                                <g mask="url(#mask0_397:1078)">
                                    <path
                                        d="M118 43H32C29.2386 43 27 45.2386 27 48V153C27 155.761 29.2386 158 32 158H118C120.761 158 123 155.761 123 153V48C123 45.2386 120.761 43 118 43Z"
                                        fill="white" />
                                </g>
                            </g>
                            <path
                                d="M66 53H40C38.3431 53 37 54.3431 37 56C37 57.6569 38.3431 59 40 59H66C67.6569 59 69 57.6569 69 56C69 54.3431 67.6569 53 66 53Z"
                                fill="#E1EBFA" />
                            <path
                                d="M66 95H40C38.3431 95 37 96.3431 37 98C37 99.6569 38.3431 101 40 101H66C67.6569 101 69 99.6569 69 98C69 96.3431 67.6569 95 66 95Z"
                                fill="#E1EBFA" />
                            <path
                                d="M108 68H42C39.7909 68 38 69.7909 38 72V82C38 84.2091 39.7909 86 42 86H108C110.209 86 112 84.2091 112 82V72C112 69.7909 110.209 68 108 68Z"
                                stroke="#1485FD" stroke-width="2" />
                            <path
                                d="M108 109H42C39.2386 109 37 111.239 37 114V122C37 124.761 39.2386 127 42 127H108C110.761 127 113 124.761 113 122V114C113 111.239 110.761 109 108 109Z"
                                fill="#DFEAFB" />
                            <path
                                d="M53 32C55.2091 32 57 30.2091 57 28C57 25.7909 55.2091 24 53 24C50.7909 24 49 25.7909 49 28C49 30.2091 50.7909 32 53 32Z"
                                fill="white" />
                            <path
                                d="M75 32C77.2091 32 79 30.2091 79 28C79 25.7909 77.2091 24 75 24C72.7909 24 71 25.7909 71 28C71 30.2091 72.7909 32 75 32Z"
                                fill="#1485FD" />
                            <path
                                d="M97 32C99.2091 32 101 30.2091 101 28C101 25.7909 99.2091 24 97 24C94.7909 24 93 25.7909 93 28C93 30.2091 94.7909 32 97 32Z"
                                fill="white" />
                            <path
                                d="M86 88C88.7614 88 91 85.7614 91 83C91 80.2386 88.7614 78 86 78C83.2386 78 81 80.2386 81 83C81 85.7614 83.2386 88 86 88Z"
                                fill="#DFEAFB" />
                            <path
                                d="M89.907 104.37C89.107 104.37 88.36 104.37 87.68 104.327C86.8424 104.27 86.0366 103.984 85.3514 103.499C84.6661 103.014 84.1279 102.349 83.796 101.578L79.577 93.24C79.2675 92.8797 79.113 92.4117 79.1471 91.938C79.1812 91.4643 79.4011 91.0233 79.759 90.711C80.0521 90.4754 80.4178 90.3485 80.794 90.352C81.0709 90.3601 81.3427 90.4281 81.5908 90.5513C81.8389 90.6746 82.0573 90.8502 82.231 91.066L84.147 93.681L84.176 93.715V83.78C84.176 83.2871 84.3718 82.8144 84.7203 82.4659C85.0688 82.1173 85.5416 81.9215 86.0345 81.9215C86.5274 81.9215 87.0001 82.1173 87.3486 82.4659C87.6972 82.8144 87.893 83.2871 87.893 83.78V90.28C87.8714 90.0408 87.8999 89.7998 87.9766 89.5722C88.0533 89.3446 88.1766 89.1355 88.3386 88.9582C88.5006 88.781 88.6978 88.6394 88.9175 88.5425C89.1373 88.4456 89.3748 88.3956 89.615 88.3956C89.8551 88.3956 90.0926 88.4456 90.3124 88.5425C90.5321 88.6394 90.7293 88.781 90.8913 88.9582C91.0533 89.1355 91.1766 89.3446 91.2533 89.5722C91.3301 89.7998 91.3585 90.0408 91.337 90.28V91.635C91.3154 91.3958 91.3439 91.1548 91.4206 90.9272C91.4973 90.6996 91.6206 90.4905 91.7826 90.3132C91.9446 90.136 92.1418 89.9944 92.3615 89.8975C92.5813 89.8006 92.8188 89.7506 93.059 89.7506C93.2991 89.7506 93.5366 89.8006 93.7564 89.8975C93.9761 89.9944 94.1733 90.136 94.3353 90.3132C94.4973 90.4905 94.6206 90.6996 94.6973 90.9272C94.7741 91.1548 94.8025 91.3958 94.781 91.635V92.679C94.7594 92.4398 94.7879 92.1988 94.8646 91.9712C94.9413 91.7436 95.0646 91.5345 95.2266 91.3572C95.3886 91.18 95.5858 91.0384 95.8055 90.9415C96.0253 90.8446 96.2628 90.7946 96.503 90.7946C96.7431 90.7946 96.9806 90.8446 97.2004 90.9415C97.4201 91.0384 97.6173 91.18 97.7793 91.3572C97.9413 91.5345 98.0646 91.7436 98.1413 91.9712C98.2181 92.1988 98.2465 92.4398 98.225 92.679V99.016C98.191 100.965 97.31 104.251 94.211 104.251C93.986 104.261 92.08 104.371 89.911 104.371L89.907 104.37Z"
                                fill="#1485FD" stroke="white" />
                            <defs>
                                <filter id="filter0_d_397:1078" x="21" y="34" width="108" height="119"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="-3" />
                                    <feGaussianBlur stdDeviation="3" />
                                    <feColorMatrix type="matrix"
                                        values="0 0 0 0 0.788235 0 0 0 0 0.803922 0 0 0 0 0.85098 0 0 0 0.349 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                        result="effect1_dropShadow_397:1078" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_397:1078"
                                        result="shape" />
                                </filter>
                                <linearGradient id="paint0_linear_397:1078" x1="75" y1="0" x2="75" y2="150"
                                    gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#E3ECFA" />
                                    <stop offset="1" stop-color="#DAE7FF" />
                                </linearGradient>
                                <linearGradient id="paint1_linear_397:1078" x1="75" y1="0" x2="75" y2="150"
                                    gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#E3ECFA" />
                                    <stop offset="1" stop-color="#DAE7FF" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="col-8" style="padding: 50px; ">

                        <span style="font-size: 24px">Nenhuma regra aqui.</span>
                        <br>
                        <span style="font-size: 16px">Para prosseguir com a criação de um desconto progressivo, você
                            precisa
                            adicionar pelo menos uma regra.</span>
                    </div>



                </div>


                <div style="position: absolute;
                bottom: 0px;
                text-align: center;
                width:646px;
                height:84px;
                padding-bottom:20px;
                padding-top:20px;
                border-top:1px solid #EBEBEB">

                    <button class="btn back-btn mr-10" type="button">Voltar</button>
                    <button class="btn btn-primary finish-btn" type="button">Finalizar</button>

                </div>


            </div>

        </div>
    </div>
</form>
