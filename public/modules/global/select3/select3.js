$.fn.select3 = function (options) {

    let $select = $(this);
    let $originalSelect = $select.get(0).outerHTML;

    let $wrapper;
    let $options;
    let $searchInput;
    let $searching;
    let $empty;
    let $loading;

    let $isAjax;
    let $currentPage;
    let $hasMorePages;

    const config = {
        placeholder: options?.placeholder || '',
        multiselect: options?.multiselect || false,
        language: {
            noResults: options?.language?.noResults || 'Nenhum resultado encontrado',
            searching: options?.language?.searching || 'Procurando...',
            loadingMore: options?.language?.loadingMore || 'Carregando mais resultados...'
        },
        ajax: options?.ajax || false
    }

    if (config.multiselect) {
        $select.prop('multiple', true);
    }

    const renderOptions = () => {
        $options.html('');
        $select.children('option')
            .each(function () {
                let $this = $(this);
                let text = $this.text();
                let searchValue = $searchInput.val().toLowerCase();
                if (!searchValue || text.toLowerCase().includes(searchValue)) {
                    let option = `<div class="select3-option ${config.multiselect ? 'multiselect' : ''} ${$this.is(':selected') ? 'active' : ''}"
                          data-value="${$this.val()}" >
                          ${config.multiselect ? '<span class="select3-checkbox"></span>' : ''}
                          <span class="select3-option-text">${text}</span>
                        </div>`
                    $options.append(option);
                }
            });
    }

    const loadFromAjax = () => {

        if ($isAjax && $hasMorePages) {

            if ($currentPage === 1) {
                $empty.hide();
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
                    page: $currentPage,
                }),
                error: res => {
                    console.log(res);
                    $options.show();
                    $searching.hide();
                    $loading.hide();
                },
                success: res => {
                    setTimeout(() => {
                        let {results, pagination} = config.ajax.processResults(res);
                        $hasMorePages = $searchInput.val() ? true : pagination.more;

                        for (let option of results) {
                            if (!$select.children(`option[value="${option.id}"]`).length) {
                                $select.append(`<option value="${option.id}">${option.text}</option>`);
                            }
                        }

                        if ($select.children().length <= 5 && $hasMorePages) {
                            $currentPage++;
                            loadFromAjax();
                        }

                        $searching.hide();
                        $loading.hide();
                        renderOptions();

                    }, config.ajax.delay)
                }
            });
        }
    }

    // Functions
    $select.init.prototype.setup = function () {
        $wrapper = $select.wrap(`<div class="select3-container"></div>`)
            .parent();
        let html = `<label class="select3-title">${config.placeholder}</label>
              <input class="select3-search" type="text">
              <div class="select3-searching" style="display:none;">${config.language.searching}</div>
              <div class="select3-options-container"></div>
              <div class="select3-empty" style="display:none;">${config.language.noResults}</div>
              <div class="select3-loading" style="display:none;">${config.language.loadingMore}</div>`;
        $wrapper.append(html);

        $options = $wrapper.find('.select3-options-container');

        $options.unbind('scroll')
            .bind('scroll', function () {
                let scrolledToEnd = this.offsetHeight + this.scrollTop >= this.scrollHeight;
                if (scrolledToEnd) {
                    $currentPage++;
                    loadFromAjax();
                }
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
        $select = $($originalSelect)
        $wrapper.replaceWith($select);
        return $select;
    };

    $select.init.prototype.clear = function () {
        return $(this).destroy()
            .setup();
    };

    // Events
    $(document).on('keyup', '.select3-container .select3-search', function () {
        if ($hasMorePages) {
            $currentPage = 1;
            loadFromAjax();
        } else {
            renderOptions();
        }
    });

    $(document).on('click', '.select3-container .select3-option', function () {
        if (!config.multiselect) {
            $('.select3-container .select3-option').not(this).removeClass('active');
            $(this).addClass('active');
            $select.val($(this).data('value'));
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
        }
        $select.trigger('change');
    });

    // Startup
    return $select.setup();
}
