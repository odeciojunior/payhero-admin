<!-- New Register Overlay Modal -->
<div class="new-register-overlay">
    <div class="d-flex justify-content-center">
        <div class="new-register-overlay-container pb-4">
            <div class="new-register-overlay-header">
                <div class="new-register-overlay-title-container">
                    <div class="d-flex align-items-center">
                        <div style="position: absolute;">
                            <div class="new-register-btn close-modal modal-top-btn">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     width="24"
                                     height="24"
                                     viewBox="0 0 24 24"
                                     fill="none">
                                    <path d="M3.21967 10.7197C2.92678 11.0126 2.92678 11.4874 3.21967 11.7803L7.71967 16.2803C8.01256 16.5732 8.48744 16.5732 8.78033 16.2803C9.07322 15.9874 9.07322 15.5126 8.78033 15.2197L5.56066 12H20.25C20.6642 12 21 11.6642 21 11.25C21 10.8358 20.6642 10.5 20.25 10.5H5.56066L8.78033 7.28033C9.07322 6.98744 9.07322 6.51256 8.78033 6.21967C8.48744 5.92678 8.01256 5.92678 7.71967 6.21967L3.21967 10.7197Z"
                                          fill="#0050AF" />
                                </svg>
                            </div>
                        </div>
                        @php
                            $user = auth()->user();
                            $name = $user->name;
                            if ($user->is_cloudfox && $user->logged_id) {
                                $userModel = new \Modules\Core\Entities\User();
                                $query = $userModel
                                    ::select('name')
                                    ->where('id', $user->logged_id)
                                    ->get();
                                $name = $query[0]->name;
                            }
                        @endphp
                        <div class="new-register-overlay-title">Bem vindo,
                            <strong>{{ explode(' ', trim($name))[0] }}</strong>
                        </div>
                    </div>
                    <div class="new-register-overlay-subtitle">
                        Clique nos botões abaixo e siga o passo a passo para começar sua operação na Azcend
                    </div>
                </div>
            </div>
            <div class="new-register-overlay-body">
                <div id="new-register-first-page">
                    <div class="init-operation-container">
                        <div class="body">
                            <div class="user-informations-status">
                                <!-- JS load -->
                            </div>
                            <div class="user-biometry-status">
                                <!-- JS load -->
                            </div>
                            <div class="company-status">
                                <!-- JS load -->
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="button"
                                class="btn new-register-btn close-modal">Deixar para mais tarde</button>
                    </div>
                </div>
                <div id="new-register-steps-container">
                    <div class="new-register-card d-flex flex-column mb-4">
                        <div class="d-flex flex-column simple-border-bottom">
                            <div class="d-flex flex-row">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         width="40"
                                         height="40"
                                         viewBox="0 0 40 40"
                                         fill="none">
                                        <path d="M17.5005 25.9886C19.9858 25.9886 22.0005 28.0024 22.0005 30.4865V33.489L21.9847 33.7058C21.3632 37.9562 17.5435 40 11.1341 40C4.74771 40 0.867091 37.9796 0.0294137 33.778L0.000488281 33.485V30.4865C0.000488281 28.0024 2.01521 25.9886 4.50049 25.9886H17.5005ZM17.5005 28.9872H4.50049C3.67206 28.9872 3.00049 29.6584 3.00049 30.4865V33.3258C3.56007 35.7255 6.10174 37.0014 11.1341 37.0014C16.1662 37.0014 18.5938 35.7394 19.0005 33.3714V30.4865C19.0005 29.6584 18.3289 28.9872 17.5005 28.9872ZM11.0005 9.9962C14.8665 9.9962 18.0005 13.1287 18.0005 16.9929C18.0005 20.857 14.8665 23.9895 11.0005 23.9895C7.1345 23.9895 4.00049 20.857 4.00049 16.9929C4.00049 13.1287 7.1345 9.9962 11.0005 9.9962ZM35.5005 2C37.9858 2 40.0005 4.01376 40.0005 6.49786V13.4945C40.0005 15.9786 37.9858 17.9924 35.5005 17.9924H32.5907L28.2576 22.2732C27.2754 23.2432 25.6926 23.2337 24.7221 22.252C24.2598 21.7842 24.0005 21.1532 24.0005 20.4964L23.9992 17.9648C21.7499 17.7156 20.0005 15.8093 20.0005 13.4945V6.49786C20.0005 4.01376 22.0152 2 24.5005 2H35.5005ZM11.0005 12.9948C8.79135 12.9948 7.00049 14.7848 7.00049 16.9929C7.00049 19.201 8.79135 20.991 11.0005 20.991C13.2096 20.991 15.0005 19.201 15.0005 16.9929C15.0005 14.7848 13.2096 12.9948 11.0005 12.9948ZM35.5005 4.99857H24.5005C23.6721 4.99857 23.0005 5.66983 23.0005 6.49786V13.4945C23.0005 14.3226 23.6721 14.9938 24.5005 14.9938H26.9977L26.9999 19.2996L31.3583 14.9938H35.5005C36.3289 14.9938 37.0005 14.3226 37.0005 13.4945V6.49786C37.0005 5.66983 36.3289 4.99857 35.5005 4.99857Z"
                                              fill="#2E85EC" />
                                    </svg>
                                </div>
                                <div class="d-flex flex-column px-4">
                                    <span class="mb-3 font-size-16"
                                          style="color: #15034C"><strong>Queremos conhecer você!</strong></span>
                                    <span class="font-size-14"
                                          style="color: #5B5B5B">Temos algumas perguntas para conhecer melhor você e seu
                                        negócio.</span>
                                </div>
                            </div>
                            <div class="d-flex flex-row justify-content-center align-items-center py-20">
                                <div class="new-register-step"
                                     data-step="1">1</div>
                                <div class="new-register-step-bar">
                                    <div id="new-register-step-progress-bar-1"
                                         class="new-register-step-progress-bar"></div>
                                </div>
                                <div class="new-register-step"
                                     data-step="2">2</div>
                                <div class="new-register-step-bar">
                                    <div id="new-register-step-progress-bar-2"
                                         class="new-register-step-progress-bar"></div>
                                </div>
                                <div class="new-register-step"
                                     data-step="3">3</div>
                            </div>
                        </div>
                        <div id="new-register-step-container">
                            {{-- STEP 1 --}}
                            <div id="new-register-step-1-container"
                                 class="p-4">
                                <div class="d-flex flex-column align-items-center mb-4">
                                    <span class="mb-3 font-size-16 font-weight-600"
                                          style="color: #0B1D3D">Em qual nicho você se encaixa hoje?</span>
                                    <span class="font-size-14"
                                          style="color: #5B5B5B">Você pode selecionar mais de um.</span>
                                </div>
                                <div class="step-1-options-container">
                                    <div class="d-flex flex-column align-items-center">
                                        <div id="step-1-dropshipping-import"
                                             class="step-1-option"
                                             data-step-1-value="dropshipping-import"
                                             data-step-1-selected="0">
                                            <svg class="svg-icon-1"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 width="66"
                                                 height="66"
                                                 viewBox="0 0 66 66"
                                                 fill="none">
                                                <circle cx="33.0001"
                                                        cy="34.5895"
                                                        r="21.8761"
                                                        stroke="#2E85EC"
                                                        stroke-width="3.22653" />
                                                <path d="M54.8116 32.772C49.3143 35.9601 41.5745 37.9453 33.0001 37.9453C24.4256 37.9453 16.6858 35.9601 11.1885 32.772"
                                                      stroke="#2E85EC"
                                                      stroke-width="3.22653" />
                                                <path d="M41.4536 34.5895C41.4536 40.9111 40.3523 46.5576 38.6355 50.5635C37.7759 52.5693 36.7937 54.0895 35.7853 55.0847C34.7837 56.0732 33.8444 56.4656 33 56.4656C32.1556 56.4656 31.2162 56.0732 30.2147 55.0847C29.2063 54.0895 28.2241 52.5693 27.3645 50.5635C25.6476 46.5576 24.5464 40.9111 24.5464 34.5895C24.5464 28.2679 25.6476 22.6214 27.3645 18.6155C28.2241 16.6097 29.2063 15.0895 30.2147 14.0942C31.2162 13.1058 32.1556 12.7134 33 12.7134C33.8444 12.7134 34.7837 13.1058 35.7853 14.0942C36.7937 15.0895 37.7759 16.6097 38.6355 18.6155C40.3523 22.6214 41.4536 28.2679 41.4536 34.5895Z"
                                                      stroke="#2E85EC"
                                                      stroke-width="3.22653" />
                                            </svg>
                                        </div>
                                        <span class="font-size-14 font-weight-400 text-center"
                                              style="color: #5B5B5B">Dropshipping e importação</span>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <div id="step-1-physical-product"
                                             class="step-1-option"
                                             data-step-1-value="physical-product"
                                             data-step-1-selected="0">
                                            <svg class="svg-icon-2"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 width="56"
                                                 height="66"
                                                 viewBox="0 0 56 56"
                                                 fill="none">
                                                <path d="M17.6262 17.626V10.7102C17.6262 6.89074 20.7226 3.79443 24.5421 3.79443C25.8017 3.79443 26.9828 4.13123 28 4.71968C29.0172 4.13123 30.1983 3.79443 31.458 3.79443C35.2774 3.79443 38.3738 6.89074 38.3738 10.7102V17.626H40.1026C42.9672 17.626 45.2895 19.9483 45.2895 22.8129V45.2892C45.2895 49.1087 42.1931 52.205 38.3737 52.205H17.6263C13.8068 52.205 10.7104 49.1087 10.7104 45.2892V22.8129C10.7104 19.9483 13.0327 17.626 15.8973 17.626H17.6262ZM21.0842 10.7102V17.626H28V10.7102C28 8.80049 26.4518 7.25233 24.5421 7.25233C22.6323 7.25233 21.0842 8.80049 21.0842 10.7102ZM38.3737 48.7471C40.2834 48.7471 41.8316 47.199 41.8316 45.2892V22.8129C41.8316 21.858 41.0575 21.0839 40.1026 21.0839H34.9158V45.2892C34.9158 47.199 36.4639 48.7471 38.3737 48.7471ZM31.4579 21.0839H15.8973C14.9424 21.0839 14.1683 21.858 14.1683 22.8129V45.2892C14.1683 47.199 15.7165 48.7471 17.6263 48.7471H32.3831C31.7946 47.7299 31.4579 46.5489 31.4579 45.2892V21.0839ZM31.4579 10.7102V17.626H34.9159V10.7102C34.9159 8.80049 33.3677 7.25233 31.458 7.25233C31.1597 7.25233 30.8703 7.29009 30.5942 7.36108C31.1445 8.35335 31.4579 9.4952 31.4579 10.7102Z"
                                                      fill="#2E85EC" />
                                            </svg>
                                        </div>
                                        <span class="font-size-14 font-weight-400 text-center"
                                              style="color: #5B5B5B">Produtos físicos</span>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <div id="step-1-digital-product"
                                             class="step-1-option"
                                             data-step-1-value="digital-product"
                                             data-step-1-selected="0">
                                            <svg class="svg-icon-2"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 width="50"
                                                 height="66"
                                                 viewBox="0 0 50 50"
                                                 fill="none">
                                                <path d="M9.3953 11.5931C10.1391 8.94952 12.551 7.12305 15.2981 7.12305H35.7318C38.479 7.12305 40.8909 8.94952 41.6346 11.5931L46.7207 29.6705C47.8648 33.737 44.8076 37.7712 40.5818 37.7712H22.4507V34.7064H40.5818C42.776 34.7064 44.3633 32.6117 43.7693 30.5002L38.6832 12.4229C38.3113 11.1011 37.1054 10.1879 35.7318 10.1879H15.2981C13.9245 10.1879 12.7186 11.1011 12.3467 12.4229L11.2509 16.3175H8.65416C8.45413 16.3175 8.25588 16.3252 8.05971 16.3402L9.3953 11.5931ZM22.2974 43.9009H36.2456C37.0922 43.9009 37.7786 43.2148 37.7786 42.3685C37.7786 41.5221 37.0922 40.8361 36.2456 40.8361H22.4507V42.3685C22.4507 42.8933 22.3979 43.4057 22.2974 43.9009ZM11.7199 30.1092C12.9899 30.1092 14.0193 29.0801 14.0193 27.8106C14.0193 26.5411 12.9899 25.512 11.7199 25.512C10.45 25.512 9.4205 26.5411 9.4205 27.8106C9.4205 29.0801 10.45 30.1092 11.7199 30.1092ZM4.05518 23.9796C4.05518 21.4406 6.11415 19.3823 8.65402 19.3823H14.7858C17.3257 19.3823 19.3847 21.4406 19.3847 23.9796V42.3685C19.3847 44.9074 17.3257 46.9657 14.7858 46.9657H8.65402C6.11415 46.9657 4.05518 44.9074 4.05518 42.3685V23.9796ZM8.65402 22.4471C7.8074 22.4471 7.12107 23.1332 7.12107 23.9796V42.3685C7.12107 43.2148 7.8074 43.9009 8.65402 43.9009H14.7858C15.6324 43.9009 16.3188 43.2148 16.3188 42.3685V23.9796C16.3188 23.1332 15.6324 22.4471 14.7858 22.4471H8.65402Z"
                                                      fill="#2E85EC" />
                                            </svg>
                                        </div>
                                        <span class="font-size-14 font-weight-400 text-center"
                                              style="color: #5B5B5B">Produtos digitais</span>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <div id="step-1-classes"
                                             class="step-1-option"
                                             data-step-1-value="classes"
                                             data-step-1-selected="0">
                                            <svg class="svg-icon-2"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 width="66"
                                                 height="66"
                                                 viewBox="0 0 66 66"
                                                 fill="none">
                                                <path d="M10.7295 9.72461C6.03723 9.72461 2.2334 13.5721 2.2334 18.3182V46.0821C2.2334 50.8282 6.03723 54.6757 10.7295 54.6757H38.1843C42.8766 54.6757 46.6804 50.8282 46.6804 46.0821V41.9062L58.5518 50.134C60.7203 51.637 63.6666 50.0664 63.6666 47.4074V16.9919C63.6666 14.3334 60.7211 12.7627 58.5525 14.2649L46.6804 22.4885V18.3182C46.6804 13.5721 42.8766 9.72461 38.1843 9.72461H10.7295ZM46.6804 37.0982V27.2957L59.7453 18.2458V46.1531L46.6804 37.0982ZM42.7591 18.3182V46.0821C42.7591 48.6377 40.7109 50.7094 38.1843 50.7094H10.7295C8.20289 50.7094 6.15467 48.6377 6.15467 46.0821V18.3182C6.15467 15.7626 8.20289 13.6909 10.7295 13.6909H38.1843C40.7109 13.6909 42.7591 15.7626 42.7591 18.3182Z"
                                                      fill="#2E85EC" />
                                            </svg>
                                        </div>
                                        <span class="font-size-14 font-weight-400 text-center"
                                              style="color: #5B5B5B">Cursos</span>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <div id="step-1-subscriptions"
                                             class="step-1-option"
                                             data-step-1-value="subscriptions"
                                             data-step-1-selected="0">
                                            <svg class="svg-icon-2"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 width="66"
                                                 height="66"
                                                 viewBox="0 0 66 66"
                                                 fill="none">
                                                <path d="M11.822 44.5064C11.1244 45.204 11.1244 46.335 11.822 47.0325L22.5397 57.7502C23.2373 58.4478 24.3683 58.4478 25.0659 57.7502C25.7635 57.0527 25.7635 55.9216 25.0659 55.2241L17.3976 47.5557H52.3833C53.3699 47.5557 54.1696 46.756 54.1696 45.7695C54.1696 44.7829 53.3699 43.9832 52.3833 43.9832H17.3976L25.0659 36.3149C25.7635 35.6173 25.7635 34.4863 25.0659 33.7887C24.3683 33.0911 23.2373 33.0911 22.5397 33.7887L11.822 44.5064ZM53.6676 22.003C54.3369 21.3106 54.3369 20.2123 53.6676 19.52L43.3068 8.80227C42.6212 8.09298 41.4903 8.07383 40.781 8.7595C40.0717 9.44518 40.0526 10.576 40.7382 11.2853L48.1721 18.9752L13.0855 18.9752C12.0989 18.9752 11.2992 19.775 11.2992 20.7615C11.2992 21.748 12.0989 22.5478 13.0855 22.5478L48.1721 22.5478L40.7382 30.2377C40.0526 30.947 40.0717 32.0778 40.781 32.7635C41.4903 33.4492 42.6212 33.43 43.3068 32.7207L53.6676 22.003Z"
                                                      fill="#2E85EC" />
                                            </svg>
                                        </div>
                                        <span class="font-size-14 font-weight-400 text-center"
                                              style="color: #5B5B5B">Assinaturas</span>
                                    </div>
                                    <div class="d-flex flex-column align-items-center">
                                        <div id="step-1-others"
                                             class="step-1-option"
                                             data-step-1-value="others"
                                             data-step-1-selected="0">
                                            <svg class="svg-icon-2"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 width="56"
                                                 height="66"
                                                 viewBox="0 0 56 56"
                                                 fill="none">
                                                <path fill-rule="evenodd"
                                                      clip-rule="evenodd"
                                                      d="M41.0839 47.1859H29.644V39.0332H47.6093V40.6606C47.6093 44.2644 44.6878 47.1859 41.0839 47.1859ZM47.6093 35.7458H29.644V7.96807H41.0839C44.6878 7.96807 47.6093 10.8896 47.6093 14.4934V35.7458ZM50.8967 35.7458L50.8967 14.4934C50.8967 9.07396 46.5033 4.68066 41.0839 4.68066H29.644V4.68058H26.3566V4.68066H14.9167C9.4973 4.68066 5.104 9.07398 5.104 14.4934V40.6606C5.104 46.08 9.49732 50.4733 14.9167 50.4733H41.0839C46.5034 50.4733 50.8967 46.08 50.8967 40.6606L50.8967 39.0332V35.7458ZM26.3566 16.1204V7.96807H14.9167C11.3129 7.96807 8.39141 10.8896 8.39141 14.4934V16.1204H26.3566ZM26.3566 19.4078H8.39141V40.6606C8.39141 44.2644 11.3129 47.1859 14.9167 47.1859H26.3566V19.4078Z"
                                                      fill="#2E85EC" />
                                            </svg>
                                        </div>
                                        <span class="font-size-14 font-weight-400 text-center"
                                              style="color: #5B5B5B">Outros</span>
                                    </div>
                                </div>
                            </div>
                            {{-- STEP 2 --}}
                            <div id="new-register-step-2-container"
                                 class="p-4">
                                <span class="font-size-16 font-weight-600"
                                      style="color: #0B1D3D">Qual ecommerce você usa hoje?</span>
                                <div class="step-2-checkbox-container">
                                    <div class="d-flex">
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="integrated-store" />
                                                </div>
                                                <div>
                                                    <span>Loja integrada</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="wix" />
                                                </div>
                                                <div>
                                                    <span>Wix</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="woo-commerce" />
                                                </div>
                                                <div>
                                                    <span>WooComerce</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="shopify" />
                                                </div>
                                                <div>
                                                    <span>Shopify</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="other-ecommerce"
                                                           name="step-2-other-ecommerce-check" />
                                                </div>
                                                <div>
                                                    <span>Outros</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-2-checkbox-option">
                                            <div style="width: 100%">
                                                <input type="text"
                                                       id="other-ecommerce-name"
                                                       name="step-2-other-ecommerce"
                                                       placeholder="Qual outro?"
                                                       disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="font-size-16 font-weight-600 mt-4"
                                      style="color: #0B1D3D">Como você ficou sabendo da Azcend?</span>
                                <div class="step-2-checkbox-container">
                                    <div class="d-flex">
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="cloudfox-referer-facebook" />
                                                </div>
                                                <div>
                                                    <span>Facebook</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="cloudfox-referer-ad" />
                                                </div>
                                                <div>
                                                    <span>Anúncios</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="cloudfox-referer-linkedin" />
                                                </div>
                                                <div>
                                                    <span>Linkedin</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="cloudfox-referer-youtube" />
                                                </div>
                                                <div>
                                                    <span>Youtube</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="step-2-checkbox-option">
                                            <div class="d-flex flex-row">
                                                <div>
                                                    <input type="checkbox"
                                                           id="cloudfox-referer-other"
                                                           name="step-2-know-cloudfox-check" />
                                                </div>
                                                <div>
                                                    <span>Outros</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-2-checkbox-option">
                                            <div style="width: 100%">
                                                <input type="text"
                                                       id="know-cloudfox"
                                                       name="step-2-know-cloudfox"
                                                       placeholder="Qual outro?"
                                                       disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- STEP 3 --}}
                            <div id="new-register-step-3-container"
                                 class="p-4">
                                <div class="d-flex flex-column mb-4">
                                    <span class="font-size-16 font-weight-600 mb-4"
                                          style="color: #0B1D3D">Qual o seu site de vendas?</span>
                                    <input type="text"
                                           id="step-3-sales-site"
                                           name="step-3-sales-site"
                                           placeholder="Cole aqui o link para o seu site"
                                           class="form-control mb-3 new-register-input-validation" />
                                    <div class="d-flex align-items-center pt-5">
                                        <input type="checkbox"
                                               id="step-3-sales-site-check"
                                               name="step-3-sales-site-check" />
                                        <span class="font-size-12 font-weight-400"
                                              style="color: #636363; margin-left: 10px;">Não tenho um site de
                                            vendas</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column mb-4 mt-4">
                                    <span class="font-size-16 font-weight-600 mb-4"
                                          style="color: #0B1D3D">Qual gateway você utiliza hoje?</span>
                                    <input type="text"
                                           id="step-3-gateway"
                                           name="step-3-gateway"
                                           placeholder="Insira aqui o gateway"
                                           class="form-control mb-3 new-register-input-validation" />
                                    <div class="d-flex align-items-center pt-5">
                                        <input type="checkbox"
                                               id="step-3-gateway-check"
                                               name="step-3-gateway-check" />
                                        <span class="font-size-12 font-weight-400"
                                              style="color: #636363; margin-left: 10px;">Não utilizo nenhum
                                            gateway</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="font-size-16 font-weight-600 mt-4"
                                          style="color: #0B1D3D">Qual seu faturamento mensal médio?</span>
                                    <span class="font-size-14 font-weight-400 mt-3"
                                          style="color: #5B5B5B">Modifique o valor arrastando a bolinha para os
                                        lados.</span>
                                    <div id="new-register-month-revenue"
                                         class="d-flex align-items-center justify-content-center mt-4"
                                         style="color: #636363; margin-bottom: 1rem;">
                                        <span class="font-size-12 font-weight-500">R$</span>
                                        <span class="font-size-20 font-weight-700 ml-3">20.000,00</span>
                                    </div>
                                    <input id="new-register-range"
                                           type="range"
                                           min="5"
                                           max="1000"
                                           step="1"
                                           value="20" />
                                </div>
                            </div>
                            {{-- STEP 4 FINISH --}}
                            <div id="new-register-step-4-container"
                                 class="p-4">
                                <div class="d-flex justify-content-center my-4">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         width="198"
                                         height="201"
                                         viewBox="0 0 198 201"
                                         fill="none">
                                        <g filter="url(#filter0_d_1056_4936)">
                                            <circle cx="99.5"
                                                    cy="100"
                                                    r="60"
                                                    fill="#2E85EC" />
                                            <path d="M76.7832 103.245L89.7637 116.226L97.3644 108.625L108.864 97.125L114.023 91.966L122.215 83.7744"
                                                  stroke="white"
                                                  stroke-width="8"
                                                  stroke-linecap="round"
                                                  stroke-linejoin="round" />
                                        </g>
                                        <rect opacity="0.5"
                                              x="34.5488"
                                              y="29"
                                              width="51.5427"
                                              height="51.5427"
                                              rx="25.7713"
                                              fill="#2E85EC"
                                              fill-opacity="0.2" />
                                        <rect opacity="0.5"
                                              x="148.5"
                                              y="61.8701"
                                              width="29.3594"
                                              height="29.3594"
                                              rx="14.6797"
                                              fill="#2E85EC"
                                              fill-opacity="0.2" />
                                        <rect opacity="0.5"
                                              x="20.5"
                                              y="103.87"
                                              width="31.5329"
                                              height="31.5329"
                                              rx="15.7665"
                                              fill="#2E85EC"
                                              fill-opacity="0.2" />
                                        <rect opacity="0.5"
                                              x="134.787"
                                              y="135.821"
                                              width="36.5935"
                                              height="36.5935"
                                              rx="18.2967"
                                              fill="#2E85EC"
                                              fill-opacity="0.2" />
                                        <defs>
                                            <filter id="filter0_d_1056_4936"
                                                    x="9.5"
                                                    y="14"
                                                    width="180"
                                                    height="180"
                                                    filterUnits="userSpaceOnUse"
                                                    color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0"
                                                         result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha"
                                                               type="matrix"
                                                               values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                               result="hardAlpha" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="15" />
                                                <feColorMatrix type="matrix"
                                                               values="0 0 0 0 0.290196 0 0 0 0 0.227451 0 0 0 0 1 0 0 0 0.3 0" />
                                                <feBlend mode="normal"
                                                         in2="BackgroundImageFix"
                                                         result="effect1_dropShadow_1056_4936" />
                                                <feBlend mode="normal"
                                                         in="SourceGraphic"
                                                         in2="effect1_dropShadow_1056_4936"
                                                         result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="d-flex flex-column mt-4">
                                    <span class="font-size-24 text-center mb-3"
                                          style="color: #0B1D3D; word-wrap: break-word;">

                                        @php
                                            $user = auth()->user();
                                            $name = $user->name;
                                            if ($user->is_cloudfox && $user->logged_id) {
                                                $userModel = new \Modules\Core\Entities\User();
                                                $query = $userModel
                                                    ::select('name')
                                                    ->where('id', $user->logged_id)
                                                    ->get();
                                                $name = $query[0]->name;
                                            }
                                        @endphp

                                        <strong>Obrigado, {{ explode(' ', trim($name))[0] }}</strong>
                                    </span>
                                    <p class="font-size-14 font-weight-400 text-center mb-4"
                                       style="color: #636363">As
                                        suas respostas ajudam a Azcend a entender melhor o negócio de cada um
                                        de nossos clientes de forma individualizada. Isso nos auxilia a desenvolver
                                        novas funcionalidades focadas no sucesso da sua operação!
                                    </p>
                                    <span class="font-size-20 font-weight-600 text-center"
                                          style="color: #2E85EC">Seja bem vindo e boas vendas!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="new-register-steps-actions"
                         class="d-flex justify-content-between flex-wrap-reverse">
                        <div class="d-flex justify-content-center finish-later-container">
                            <button type="button"
                                    class="btn new-register-btn close-modal align-self-center">Terminar
                                depois
                            </button>
                        </div>
                        <div class="new-register-step-btn-container">
                            <button id="new-register-previous-step"
                                    type="button"
                                    class="btn btn-light new-register-step-btn">Voltar
                            </button>
                            <button id="new-register-next-step"
                                    type="button"
                                    class="btn btn-primary new-register-step-btn"
                                    data-step-btn
                                    disabled>Prosseguir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
