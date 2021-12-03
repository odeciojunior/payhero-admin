"use strict";

$.fn.select3 = function (options) {
    var _options$language, _options$language2, _options$language3, _options$language4, _options$language5;

    let $select = $(this);
    let $originalSelect = $select.get(0).outerHTML;
    let $wrapper;
    let $options;
    let $searchInput;
    let $controls;
    let $searching;
    let $empty;
    let $loading;
    let $isAjax;
    let $currentPage;
    let $hasMorePages;
    const config = {
        placeholder: (options === null || options === void 0 ? void 0 : options.placeholder) || '',
        multiselect: (options === null || options === void 0 ? void 0 : options.multiselect) || false,
        language: {
            noResults: (options === null || options === void 0 ? void 0 : (_options$language = options.language) === null || _options$language === void 0 ? void 0 : _options$language.noResults) || 'Nenhum resultado encontrado',
            searching: (options === null || options === void 0 ? void 0 : (_options$language2 = options.language) === null || _options$language2 === void 0 ? void 0 : _options$language2.searching) || 'Procurando...',
            loadingMore: (options === null || options === void 0 ? void 0 : (_options$language3 = options.language) === null || _options$language3 === void 0 ? void 0 : _options$language3.loadingMore) || 'Carregando mais resultados...',
            uncheckAll: (options === null || options === void 0 ? void 0 : (_options$language4 = options.language) === null || _options$language4 === void 0 ? void 0 : _options$language4.uncheckAll) || 'Desmarcar todos',
            apply: (options === null || options === void 0 ? void 0 : (_options$language5 = options.language) === null || _options$language5 === void 0 ? void 0 : _options$language5.apply) || 'Aplicar'
        },
        ajax: (options === null || options === void 0 ? void 0 : options.ajax) || false,
        onSelect: (options === null || options === void 0 ? void 0 : options.onSelect) || false,
        onMultipleSelect: (options === null || options === void 0 ? void 0 : options.onMultipleSelect) || false
    };

    if (config.multiselect) {
        $select.prop('multiple', true);
    }

    const renderOptions = () => {
        $options.html('');
        $empty.hide();
        let selectOptions = 0;
        $select.children('option').each(function () {
            let $this = $(this);
            let text = $this.text();
            let searchValue = $searchInput.val().toLowerCase();
            let value = $this.val();
            let isSelected = $this.is(':selected');
            if (isSelected) selectOptions++;

            if (!searchValue || text.toLowerCase().includes(searchValue)) {
                let option = `<div class="select3-option ${config.multiselect ? 'multiselect' : ''} ${isSelected ? 'active' : ''}"
                                  data-value="${value}" >
                                  ${config.multiselect ? '<span class="select3-checkbox"></span>' : ''}
                                  <span class="select3-option-text">${text}</span>
                                </div>`;
                $options.append(option);
            }

            if (!$options.children().length) {
                $empty.show();
            }

            if (config.multiselect && selectOptions) {
                $controls.find('.select3-uncheck').show().text(`${config.language.uncheckAll} (${selectOptions})`);
            }
        });
    };

    const loadFromAjax = () => {
        if ($isAjax && $hasMorePages) {
            if ($currentPage === 1) {
                $searching.show();
                $options.html('');
            } else {
                $loading.show();
            }

            $.ajax({
                url: config.ajax.url,
                method: config.ajax.method,
                dataType: config.ajax.dataType,
                headers: config.ajax.headers,
                delay: config.ajax.delay,
                data: config.ajax.data({
                    term: $searchInput.val(),
                    page: $currentPage
                }),
                error: res => {
                    console.log(res);
                    $options.show();
                    $searching.hide();
                    $loading.hide();
                },
                success: res => {
                    setTimeout(() => {
                        let {
                            results,
                            pagination
                        } = config.ajax.processResults(res);
                        $hasMorePages = $searchInput.val() ? true : pagination.more;

                        if (results.length) {
                            for (let option of results) {
                                if (!$select.children(`option[value="${option.id}"]`).length) {
                                    $select.append(`<option value="${option.id}">${option.text}</option>`);
                                }
                            }
                        }

                        if ($select.children().length <= 5 && $hasMorePages) {
                            $currentPage++;
                            loadFromAjax();
                        }

                        $searching.hide();
                        $loading.hide();
                        renderOptions();
                    }, config.ajax.delay);
                }
            });
        }
    };

    // Functions
    $select.init.prototype.setup = function () {
        $wrapper = $select.wrap(`<div class="select3-container"></div>`).parent();
        let html = `<label class="select3-title">${config.placeholder}</label>
              <input class="select3-search" type="text">
              <div class="select3-searching" style="display:none;">${config.language.searching}</div>
              <div class="select3-options-container"></div>
              <div class="select3-empty" style="display:none;">${config.language.noResults}</div>
              <div class="select3-loading" style="display:none;">${config.language.loadingMore}</div>`;

        if (config.multiselect) {
            html += `<div class="select3-multi-controls">
                <button class="select3-uncheck">${config.language.uncheckAll}</button>
                <button class="select3-apply">${config.language.apply}</button>
              </div>`;
        }

        $wrapper.append(html);
        $options = $wrapper.find('.select3-options-container');
        $options.unbind('scroll').bind('scroll', function () {
            let scrolledToEnd = this.offsetHeight + this.scrollTop >= this.scrollHeight;

            if (scrolledToEnd) {
                $currentPage++;
                loadFromAjax();
            }
        });
        $controls = $wrapper.find('.select3-multi-controls');
        $controls.find('.select3-uncheck').on('click', function () {
            $select.val([]);
            $(this).hide();
            renderOptions();
        });
        $controls.find('.select3-apply').on('click', function () {
            if (config.onSelect) config.onSelect();
        });
        $wrapper.on('keyup', '.select3-search', function () {
            if ($hasMorePages) {
                $currentPage = 1;
                loadFromAjax();
            } else {
                renderOptions();
            }
        });
        $wrapper.on('click', '.select3-option', function () {
            if (!config.multiselect) {
                $wrapper.find('.select3-option').removeClass('active');
                $(this).addClass('active');
                $select.val($(this).data('value'));
                if (config.onSelect) config.onSelect();
            } else {
                $(this).toggleClass('active');
                let value = $(this).data('value');
                let values = $select.val();
                let index = values.indexOf(value);

                if (index === -1) {
                    values.push(value);
                } else {
                    values.splice(index, 1);
                }

                $select.val(values);
                let count = values.length;

                if (count) {
                    $controls.find('.select3-uncheck').show().text(`${config.language.uncheckAll} (${count})`);
                } else {
                    $controls.find('.select3-uncheck').hide();
                }
            }

            $select.trigger('change');
        });
        $searchInput = $wrapper.find('.select3-search');
        $searching = $wrapper.find('.select3-searching');
        $empty = $wrapper.find('.select3-empty');
        $loading = $wrapper.find('.select3-loading');
        $isAjax = !!config.ajax;
        $currentPage = 1;
        $hasMorePages = true;
        $searchInput.val('');
        $isAjax ? loadFromAjax() : renderOptions();
        return $select;
    };

    $select.init.prototype.destroy = function () {
        $select = $($originalSelect);
        $wrapper.replaceWith($select);
        return $select;
    };

    $select.init.prototype.clear = function () {
        return $(this).destroy().setup();
    };

    // Startup
    return $select.setup();
};
