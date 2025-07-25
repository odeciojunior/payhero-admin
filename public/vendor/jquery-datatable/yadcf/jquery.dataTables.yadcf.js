/*!
* Yet Another DataTables Column Filter - (yadcf)
*
* File:        jquery.dataTables.yadcf.js
* Version:     0.9.1
*
* Author:      Daniel Reznick
* Info:        https://github.com/vedmack/yadcf
* Contact:     vedmack@gmail.com
* Twitter:     @danielreznick
* Q&A          http://stackoverflow.com/questions/tagged/yadcf
*
* Copyright 2015 Daniel Reznick, all rights reserved.
* Copyright 2015 Released under the MIT License
*
* This source file is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE. See the license files for details.
*/
/*
* Parameters:
*
*
* -------------

* column_number
                Required:           true
                Type:               int
                Description:        The number of the column to which the filter will be applied

* filter_type
                Required:           false
                Type:               String
                Default value:      'select'
                Possible values:    select / multi_select / auto_complete / text / date / range_number / range_number_slider / range_date / custom_func / multi_select_custom_func
                Description:        The type of the filter to be used in the column

* custom_func
                Required:           true (when filter_type is custom_func / multi_select_custom_func)
                Type:               function
                Default value:      undefined
                Description:        should be pointing to a function with the following signature myCustomFilterFunction(filterVal, columnVal, rowValues, stateVal) , where `filterVal` is the value from the select box,
                                    `columnVal` is the value from the relevant row column, `rowValues` is an array that holds the values of the entire row and `stateVal` which holds the current state of the table row DOM
                                    , stateVal is perfect to handle situations in which you placing radiobuttons / checkbox inside table column. This function should return true if the row matches your condition and the row should be displayed) and false otherwise
                Note:               When using multi_select_custom_func as filter_type filterVal will hold an array of selected values from the multi select element

* data
                Required:           false
                Type:               Array (of string or objects)
                Description:        When the need of predefined data for filter is needed just use an array of strings ["value1","value2"....] (supported in select / multi_select / auto_complete filters) or
                                    array of objects [{value: 'Some Data 1', label: 'One'}, {value: 'Some Data 3', label: 'Three'}] (supported in select / multi_select filters)
                Note:               that when filter_type is custom_func / multi_select_custom_func this array will populate the custom filter select element

* data_as_is
                Required:           false
                Type:               boolean
                Default value:      false
                Description:        When set to true, the value of the data attribute will be fed into the filter as is (without any modification/decoration).
                                    Perfect to use when you want to define your own <option></option> for the filter
                Note:               Currently supported by the select / multi_select filters

* append_data_to_table_data
                Required:           false
                Type:               String
                Default value:      undefined
                Possible values:    before / sorted
                Description:        Use 'before' to place your data array before the values that yadcf grabs from the table
                                    use 'sorted' to place the data array sorted along with the values that yadcf grabs from the table
                Note:               'sorted' option will have affect only if you data is an array of primitives (not objects)

* column_data_type
                Required:           false
                Type:               String
                Default value:      'text'
                Possible values:    text / html / rendered_html
                Description:        The type of data in column , use "html" when you have some html code in the column (support parsing of multiple elements per cell),
                                    use rendered_html when you are using render function of columnDefs or similar, that produces a html code, note that both types rendered_html and html have a fallback for simple text parsing

* text_data_delimiter
                Required:           false
                Type:               String
                Description:        Delimiter that seperates text in table column, for example text_data_delimiter: ","

* html_data_type
                Required:           false
                Type:               String
                Default value:      'text'
                Possible values:    text / value / id / selector
                Description:        When using "html" for column_data_type argument you can choose how exactly to parse your html element/s in column , for example use "text" for the following <span class="someClass">Some text</span>
                Special notes:      when using selector you must provide a valid selector string for the html_data_selector property

* html_data_selector
                Required:           false
                Type:               String
                Default value:      undefined
                Possible values:    any valid selector string, for example 'li:eq(1)'
                Description:        allows for advanced text value selection within the html located in the td element
                Special notes:      know that the selector string "begin is search" from (and not outside) the first element of the html inside the td
                                    (supported by range_number_slider / select / auto_complete)

* html5_data
                Required:           false
                Type:               String
                Default value:      undefined
                Possible values:    data-filter / data-search / anything that is supported by datatables
                Description:        Allows to filter based on data-filter / data-search attributes of the <td> element, read more: http://www.datatables.net/examples/advanced_init/html5-data-attributes.html

* filter_container_id
                Required:           false
                Type:               String
                Description:        In case that user don't want to place the filter in column header , he can pass an id of the desired container for the column filter

* filter_container_selector
                Required:           false
                Type:               String
                Description:        In case that user don't want to place the filter in column header , he can pass a (jquery) selector of the desired container for the column filter

* filter_default_label
                Required:           false
                Type:               String / Array of string in case of range_number filter (first entry is for the first input and the second entry is for the second input
                Default value:      'Select value'
                Description:        The label that will appear in the select menu filter when no value is selected from the filter

* omit_default_label
                Required:           false
                Type:               boolean
                Default value:      false
                Description:        Prevent yadcf from adding "default_label" (Select value / Select values)
                Note                Currently supported in select / multi_select / custom_func / multi_select_custom_func

* filter_reset_button_text
                Required:           false
                Type:               String / boolean
                Default value:      'x'
                Description:        The text that will appear inside the reset button next to the select drop down (set this to false (boolean) in order to hide it from that column filter)

* enable_auto_complete (this attribute is deprecated , and will become obsolete in the future , so you better start using filter_type: "auto_complete")
                Required:           false
                Type:               boolean
                Default value:      false
                Description:        Turns the filter into an autocomplete input - make use of the jQuery UI Autocomplete widget (with some enhancements)

* sort_as
                Required:           false
                Type:               String
                Default value:      'alpha'
                Possible values:    alpha / num / alphaNum / none
                Description:        Defines how the values in the filter will be sorted, alphabetically / numerically / alphanumeric / custom / not sorted at all (none is useful to preserve
                                    the order of the data attribute as is)
                Note:               When custom value is set you must provide a custom sorting function for the sort_as_custom_func property

* sort_as_custom_func
                Required:           false
                Type:               function
                Default value:      undefined
                Description:        Allows to provide a custom sorting function for the filter elements

* sort_order
                Required:           false
                Type:               String
                Default value:      'asc'
                Possible values:    asc / desc
                Description:        Defines the order in which the values in the filter will be sorted, ascending or descending

* date_format
                Required:           false
                Type:               String
                Default value:      'mm/dd/yyyy'
                Possible values:    mm/dd/yyyy / dd/mm/yyyy / hh:mm (when using datepicker_type: 'bootstrap-datetimepicker')
                Description:        Defines the format in which the date values are being parsed into Date object
                Note:               You can replace the / separator with other one , for example mm-dd-yy

* moment_date_format
                Required:           false
                Type:               String
                Default value:      undefined
                Possible values:    Any format accepted by momentjs
                Description:        Defines the format in which the date values are being parsed into Date object by momentjs library
                Note:               Currently relevant only when using datepicker_type: 'bootstrap-datetimepicker')

* ignore_char
                Required:           false
                Type:               String
                Description:        Tells the range_number and range_number_slide to ignore specific char while filtering (that char can used as number separator)
                Note:               Use double escape for regex chars , e.g \\$ , also you can use multiple ignore chars with | , e.g '_|\\.|\\$'

* filter_match_mode
                Required:           false
                Type:               String
                Default value:      contains
                Possible values:    contains / exact / startsWith / regex
                Description:        Allows to control the matching mode of the filter (supported in select / auto_complete / text filters)

* exclude
                Required:           false
                Type:               boolean
                Default value:      undefined
                Description:        Adds a checkbox next to the filter that allows to do a "not/exclude" filtering (acts the same  all filter_match_mode)
                Note:               Currently available for the text filter

* exclude_label
                Required:           false
                Type:               String
                Default value:      'exclude'
                Description:        The label that will appear above the exclude checkbox

* select_type
                Required:           false
                Type:               String
                Default value:      undefined
                Possible values:    chosen / select2 / custom_select
                Description:        Turns the simple select element into Chosen / Select2 (make use of the Chosen / Select2 select jQuery plugins)
                Note:               When using custom_select , make sure to call the initSelectPluginCustomTriggers,
                                    before calling yadcf constructor / init function

* select_type_options
                Required:           false
                Type:               Object
                Default value:      {}
                Description:        This parameter will be passed "as is" to the Chosen/Select2 plugin constructor

* filter_plugin_options
                Required:           false
                Type:               Object
                Default value:      undefined
                Description:        This parameter will be passed to the jQuery Autocomplete / jQuery Slider / Bootstrap Datetimepicker

* case_insensitive
                Required:           false
                Type:               boolean
                Default value:      true
                Description:        Do case-insensitive filtering (supported in select / auto_complete / text filters)


* filter_delay
                Required:           false
                Type:               integer
                Default value:      undefined
                Description:        Delay filter execution for a XXX milliseconds - filter will fire XXX milliseconds after the last keyup.
                Special notes:      Currently supported in text / range_number / range_date filters / range_number_slider

* datepicker_type
                Required:           false
                Type:               String
                Default value:      'jquery-ui'
                Possible values:    'jquery-ui' / 'bootstrap-datetimepicker'
                Description:        You can choose datapicker library from defined in special notes
                Special notes:      Currently supported only jQueryUI datepicker (datepicker) and Bootstrap datepicker (eonasdan-bootstrap-datetimepicker)
                                    Bootstrap datepicker depends moment library. This plugin depends moment too.

* style_class
                Required:           false
                Type:               String
                Description:        Allows adding additional class/classes to filter - available for the following filters:
                                    select / multi_select / text / custom_func / multi_select_custom_func

* reset_button_style_class
                Required:           false
                Type:               String
                Description:        Allows adding additional class/classes to filter reset button


* Global Parameters (per table rather than per column)
*
* Usage example yadcf.init(oTable,[{column_number : 0}, {column_number: 3}],{cumulative_filtering: true});
* -------------

* externally_triggered
                Required:           false
                Type:               boolean
                Default value:      false
                Description:        Filters will filter only when yadcf.exFilterExternallyTriggered(table_arg) is called
                Special notes:      Useful when you want to build some form with filters and you want to trigger the filter when that form
                                    "submit" button is clicked (instead of filtering per filter input change)

* cumulative_filtering
                Required:           false
                Type:               boolean
                Default value:      false
                Description:        Change the default behaviour of the filters so its options will be populated from the filtered rows (remaining
                                    table data after filtering) only, unlike the normal behaviour in which the options of the filters are from all the table data


* filters_position
                Required:           false
                Type:               String
                Default value:      header
                Possible values:    'header' / 'footer'
                Description:        Filters can be placed in the header (thead) or in the footer (tfoot) of the table,
                Note:               When 'footer' you must provide a valid tfoot elemet in your table


* filters_tr_index
                Required:           false
                Type:               integer
                Default value:      undefined
                Description:        Allow to control the index of the <tr> inside the thead of the table, e.g when one <tr> is used for headers/sort and
                                    another <tr> is used for filters
*
*
*
*
* External API functions:
*
*
* -------------

* exFilterColumn
                Description:        Allows to trigger filter/s externally/programmatically (support ALL filter types!!!) , perfect for showing table with pre filtered columns
                Arguments:          table_arg: (variable of the datatable),
                                    array of pairs: column number String/Object with from and to, filter_value (the actual string value that we want to filter by)
                Usage example:      yadcf.exFilterColumn(oTable, [[0, 'Some Data 2']]); //pre filter one column
                                    yadcf.exFilterColumn(oTable, [[0, 'Some Data 1'], [1, {from: 111, to: 1110}], [2, {from: "", to: "11/25/2014"}]]); //pre filter several columns
                                    yadcf.exFilterColumn(oTable, [[0, ['Some Data 1','Some Data 2']]]); // for pre filtering multi select filter you should use array with values (or an array with single value)

* exGetColumnFilterVal
                Description:        Allows to retrieve  column current filtered value (support ALL filter types!!!)
                Arguments:          table_arg: (variable of the datatable),
                                    column number:  column number from which we want the value
                Usage example:      yadcf.exGetColumnFilterVal(oTable,1);
                Return value:       String (for simple filter) / Object (for range filter) with from and to properties / Array of strings for multi_select filter


* exResetAllFilters
                Description:        Allows to reset all filters externally/programmatically (support ALL filter types!!!) , perfect for adding a "reset all" button to your page!
                Arguments:          table_arg: (variable of the datatable)
                                    noRedraw:   (boolean) , use it if you don't want your table to be reloaded after the filter reset,
                                                for example if you planning to call exFilterColumn function right after the exResetAllFilters (to avoid two AJAX requests)
                Usage example:      yadcf.exResetAllFilters(oTable);

* exResetFilters
                Description:        Allows to reset specific filters externally/programmatically (support ALL filter types!!!) , can be used for resetting one or more filters
                Arguments:          table_arg: (variable of the datatable)
                                    array with columns numbers
                                    noRedraw:   (boolean) , use it if you don't want your table to be reloaded after the filter reset,
                                                for example if you planning to call exFilterColumn function right after the exResetFilters (to avoid two AJAX requests)
                Usage example:      yadcf.exResetAllFilters(oTable, [1,2]);

* initSelectPluginCustomTriggers
                Description:        Allows to set any select jquery plugin initialize and refresh functions. jQuery selector will be passed to the user defined function to initialize and refresh the plugin.
                                    Great for integrating any jquey select plugin  (Selectize / MultiSelect / etc)
                Arguments:          initFunc  : function which will initialize the plugin
                                    refreshFunc : function that will refresh the plugin.
                                    destroyFunc : function that will destroy the plugin (upon table destroy even trigger).
                Usage example:      yadcf.initSelectPluginCustomTriggers(function($filterSelector){$filterSelector.multiselect({});}, function($filterSelector){$filterSelector.multiselect("refresh")}, , function($filterSelector){$filterSelector.multiselect("destroy")});

* exFilterExternallyTriggered
                Description:        Triggers all the available filters, should be used only when the externally_triggered option used
                Arguments:          table_arg: (variable of the datatable)
                Usage example:      yadcf.exResetAllFilters(table_arg);
*
*
*
* Server-side processing API (see more on showcase):
*
* From server to client:
* In order to populate the filters with data from server (select / auto_complete / range_number_slider (min and max values), you should add to your current json respond the following properties:
* lets say for first column you add yadcf_data_0 filled with array of values, for column second column yadcf_data_1 and so on...
*
* From client to server:
* Read the filtered value like this (for first column) req.getParameter("columns[0][search][value]"); <- java code , php/.Net/etc you just need to get it from the request
* Range filter value will arrive delimited by  -yadcf_delim- , so just split it into an array or something like this: String[] minMax = sSearch_0.split("-yadcf_delim-");
*
*


*
*
*
* Working with filters for multiple tables:
*
*
* -------------

* initMultipleTables
                Description:        Allows to create filter that will affect multiple tables / multiple column(s) in multiple tables
                Arguments:          Array of tables,
                                    Array of objects with properties for each filter
                Usage example:      yadcf.initMultipleTables([oTable, oTable2], [{
                                        column_number: [0, 1], filter_container_id: 'multi-table-filter-0', filter_default_label: 'Filter all tables columns 1 and 2!'
                                    },
                                    {
                                        column_number: [2], filter_container_id: 'multi-table-filter-1', filter_default_label: 'Filter all tables column 3!'
                                    }]);
                Valid properties:   filter_type: 'text' (default) / 'select' / 'multi_select',
                                    column_number: not required (in that case the filter will be global)
                                                   can be either number(single column filter) or array of numbers(multiple columns filter)
                                    filter_container_id: '' (required),
                Note:               All the usual properties of yadcf should be supported in initMultipleTables too!

* initMultipleColumns
                Description:        Allows to create filter that will affect multiple column(s) in in a particular table
                Arguments:          Table variable,
                                    Array of objects with properties for each filter
                Usage example:      yadcf.initMultipleColumns(oTable, [{
                                        column_number: [0, 1], filter_container_id: 'multi-table-filter-0', filter_default_label: 'Filter columns 1 and 2!'
                                    },
                                    {
                                        column_number: [2, 3], filter_container_id: 'multi-table-filter-1', filter_default_label: 'Filter column 3 and 4!'
                                    }]);
                Valid properties:   filter_type: 'text' (default) / 'select' / 'multi_select',
                                    column_number: not required (in that case the filter will be global)
                                                   can be either number(single column filter) or array of numbers(multiple columns filter)
                                    filter_container_id: '' (required),
                Note:               All the usual properties of yadcf should be supported in initMultipleColumns too!
*/
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        root.yadcf = factory(root.jQuery);
    }
}(this, function ($) {
    var yadcf = (function () {

        'use strict';

        var tablesDT = {},
            oTables = {},
            oTablesIndex = {},
            options = {},
            plugins = {},
            exFilterColumnQueue = [],
            yadcfDelay,
            reA = /[^a-zA-Z]/g,
            reN = /[^0-9]/g,
            selectElementCustomInitFunc,
            selectElementCustomRefreshFunc,
            selectElementCustomDestroyFunc,
            placeholderLang = {
                'select': $.i18n._('selecione'),
                'select_multi': $.i18n._('selecione'),
                'filter': $.i18n._('digite para filtrar'),
                'range': [$.i18n._('from'), $.i18n._('to')],
                'date': $.i18n._('selecione')
            },
            settingsMap = {};

        //From ColReorder (SpryMedia Ltd (www.sprymedia.co.uk))
        function getSettingsObjFromTable(dt) {
            var oDTSettings;
            if ($.fn.dataTable.Api) {
                oDTSettings = new $.fn.dataTable.Api(dt).settings()[0];
            } else if (dt.fnSettings) {// 1.9 compatibility
                // DataTables object, convert to the settings object
                oDTSettings = dt.fnSettings();
            } else if (typeof dt === 'string') {// jQuery selector
                if ($.fn.dataTable.fnIsDataTable($(dt)[0])) {
                    oDTSettings = $(dt).eq(0).dataTable().fnSettings();
                }
            } else if (dt.nodeName && dt.nodeName.toLowerCase() === 'table') {
                // Table node
                if ($.fn.dataTable.fnIsDataTable(dt.nodeName)) {
                    oDTSettings = $(dt.nodeName).dataTable().fnSettings();
                }
            } else if (dt instanceof jQuery) {
                // jQuery object
                if ($.fn.dataTable.fnIsDataTable(dt[0])) {
                    oDTSettings = dt.eq(0).dataTable().fnSettings();
                }
            } else {
                // DataTables settings object
                oDTSettings = dt;
            }
            return oDTSettings;
        }

        function arraySwapValueWithIndex(pArray) {
            var tmp = [],
                i;
            for (i = 0; i < pArray.length; i++) {
                tmp[pArray[i]] = i;
            }
            return tmp;
        }

        function arraySwapValueWithIndex2(pArray) {
            var tmp = [],
                i;
            for (i = 0; i < pArray.length; i++) {
                tmp[pArray[i]._ColReorder_iOrigCol] = i;
            }
            return tmp;
        }

        function initColReorder2(settingsDt, table_selector_jq_friendly) {
            if (settingsDt.oSavedState != undefined && settingsDt.oSavedState.ColReorder !== undefined) {
                if (plugins[table_selector_jq_friendly] === undefined) {
                    plugins[table_selector_jq_friendly] = {};
                    plugins[table_selector_jq_friendly].ColReorder = arraySwapValueWithIndex(settingsDt.oSavedState.ColReorder);
                }
            } else if (settingsDt.aoColumns[0]._ColReorder_iOrigCol !== undefined) {
                if (plugins[table_selector_jq_friendly] === undefined) {
                    plugins[table_selector_jq_friendly] = {};
                    plugins[table_selector_jq_friendly].ColReorder = arraySwapValueWithIndex2(settingsDt.aoColumns);
                }
            }
        }

        function initColReorderFromEvent(table_selector_jq_friendly) {
            plugins[table_selector_jq_friendly] = undefined;
        }

        function columnsArrayToString(column_number) {
            var column_number_obj = {};
            if (column_number !== undefined) {
                if (column_number instanceof Array) {
                    column_number_obj.column_number_str = column_number.join('_');
                } else {
                    column_number_obj.column_number_str = column_number;
                    column_number = [];
                    column_number.push(column_number_obj.column_number_str);
                }
            } else {
                column_number_obj.column_number_str = 'global';
            }
            column_number_obj.column_number = column_number;
            return column_number_obj;
        }

        function getOptions(selector) {
            return options[selector];
        }

        function eventTargetFixUp(pEvent) {
            if (pEvent.target === undefined) {
                pEvent.target = pEvent.srcElement;
            }
            return pEvent;
        }

        function dot2obj(tmpObj, dot_refs) {
            var i = 0;
            dot_refs = dot_refs.split(".");
            for (i = 0; i < dot_refs.length; i++) {
                tmpObj = tmpObj[dot_refs[i]];
            }
            return tmpObj;
        }

        function setOptions(selector_arg, options_arg, params) {
            var tmpOptions = {},
                i,
                j,
                col_num_as_int,
                default_options = {
                    filter_type: "select",
                    enable_auto_complete: false,
                    sort_as: "alpha",
                    sort_order: "asc",
                    date_format: "mm/dd/yyyy",
                    ignore_char: undefined,
                    filter_match_mode: "contains",
                    select_type: undefined,
                    select_type_options: {},
                    case_insensitive: true,
                    column_data_type: 'text',
                    html_data_type: 'text',
                    exclude_label: 'exclude',
                    style_class: '',
                    reset_button_style_class: '',
                    datepicker_type: 'jquery-ui',
                    range_data_type: 'single',
                    range_data_type_delim: '-',
                    omit_default_label: false
                },
                adaptContainerCssClassImpl = function (dummy) {
                    return '';
                };

            $.extend(true, default_options, params);

            if (options_arg.length === undefined) {
                options[selector_arg] = options_arg;
                return;
            }
            for (i = 0; i < options_arg.length; i++) {
                if (options_arg[i].date_format !== undefined && options_arg[i].moment_date_format === undefined) {
                    options_arg[i].moment_date_format = options_arg[i].date_format;
                }
                if (options_arg[i].select_type === 'select2') {
                    default_options.select_type_options = {
                        adaptContainerCssClass: adaptContainerCssClassImpl
                    };
                }
                //no individual reset button for externally_triggered mode
                if (default_options.externally_triggered === true) {
                    options_arg[i].filter_reset_button_text = false;
                }
                //validate custom function required attributes
                if (options_arg[i].filter_type !== undefined && options_arg[i].filter_type.indexOf('custom_func') !== -1) {
                    if (options_arg[i].custom_func === undefined) {
                        console.log('Error: You are trying to use filter_type: "custom_func / multi_select_custom_func" for column ' + options_arg[i].column_number + ' but there is no such custom_func attribute provided (custom_func: \"function reference goes here...\")');
                        return;
                    }
                }
                col_num_as_int = +options_arg[i].column_number;
                if (isNaN(col_num_as_int)) {
                    tmpOptions[options_arg[i].column_number_str] = $.extend(true, {}, default_options, options_arg[i]);
                } else {
                    tmpOptions[col_num_as_int] = $.extend(true, {}, default_options, options_arg[i]);
                }
            }
            options[selector_arg] = tmpOptions;
        }

        //taken and modified from DataTables 1.10.0-beta.2 source
        function yadcfVersionCheck(version) {
            var aThis = $.fn.dataTable.ext.sVersion.split('.'),
                aThat = version.split('.'),
                iThis,
                iThat,
                i,
                iLen;

            for (i = 0, iLen = aThat.length; i < iLen; i++) {
                iThis = parseInt(aThis[i], 10) || 0;
                iThat = parseInt(aThat[i], 10) || 0;

                // Parts are the same, keep comparing
                if (iThis === iThat) {
                    continue;
                }

                // Parts are different, return immediately
                return iThis > iThat;
            }

            return true;
        }

        function calculateColumnNumber(column_number, pTable) {
            var col_num_visible_iter,
                col_num_visible = column_number;
            for (col_num_visible_iter = 0; col_num_visible_iter < pTable.fnSettings().aoColumns.length && col_num_visible_iter < column_number; col_num_visible_iter++) {
                if (pTable.fnSettings().aoColumns[col_num_visible_iter].bVisible === false) {
                    col_num_visible++;
                }
            }
            return col_num_visible;
        }

        function resetIApiIndex() {
            $.fn.dataTableExt.iApiIndex = 0;

        }

        function escapeRegExp(string) {
            return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        }

        function escapeRegExpInArray(arr) {
            var i;
            for (i = 0; i < arr.length; i++) {
                arr[i] = arr[i].replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
            }
            return arr;
        }

        function replaceAll(string, find, replace) {
            return string.replace(new RegExp(escapeRegExp(find), 'g'), replace);
        }

        function getTableId(obj) {
            var tableId;
            if (obj.table !== undefined) {
                tableId = obj.table().node().id;
            } else {
                tableId = getSettingsObjFromTable(obj).sTableId;
            }
            return tableId;
        }

        function generateTableSelectorJQFriendly2(obj) {
            var tmpStr;
            if (obj.oInstance !== undefined && obj.oInstance.selector !== undefined) {
                tmpStr = obj.oInstance.selector;
            } else if (obj.selector !== undefined) {
                tmpStr = obj.selector;
            } else {
                tmpStr = obj.table().node().id;
            }
            tmpStr = replaceAll(tmpStr, ".", "-");
            tmpStr = replaceAll(tmpStr, ' ', '');
            return tmpStr.replace(":", "-").replace("(", "").replace(")", "").replace("#", "-");
        }

        function generateTableSelectorJQFriendlyNew(tmpStr) {
            tmpStr = replaceAll(tmpStr, ":", "-");
            tmpStr = replaceAll(tmpStr, "(", "");
            tmpStr = replaceAll(tmpStr, ")", "");
            tmpStr = replaceAll(tmpStr, ",", "");
            tmpStr = replaceAll(tmpStr, ".", "-");
            tmpStr = replaceAll(tmpStr, "#", "-");
            tmpStr = replaceAll(tmpStr, ' ', '');
            return tmpStr;
        }

        yadcfDelay = (function () {
            var timer = 0;
            return function (callback, ms, param) {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback(param);
                }, ms);
                return timer;
            };
        }());

        function initializeSelectPlugin(selectType, $selectObject, select_type_options) {
            if (selectType === 'chosen') {
                $selectObject.chosen(select_type_options);
                $selectObject.next().attr("onclick", "yadcf.stopPropagation(event);").attr("onmousedown", "yadcf.stopPropagation(event);");
            } else if (selectType === 'select2') {
                select_type_options.locale = 'pt-BR';
                select_type_options.minimumInputLength = 2;
                select_type_options.allowClear = true;
                select_type_options.placeholder = 'Selecione o valor';
                $selectObject.select2(select_type_options);
                if ($selectObject.next().hasClass('select2-container')) {
                    $selectObject.next()
                        .attr("onclick", "yadcf.stopPropagation(event);")
                        .attr("onmousedown", "yadcf.stopPropagation(event);");
                }
            } else if (selectType === 'custom_select') {
                selectElementCustomInitFunc($selectObject);
                $selectObject.next().attr("onclick", "yadcf.stopPropagation(event);").attr("onmousedown", "yadcf.stopPropagation(event);");
            }
        }

        function refreshSelectPlugin(columnObj, $selectObject, val) {
            var selectType = columnObj.select_type,
                select_type_options = columnObj.select_type_options;
            if (selectType === 'chosen') {
                $selectObject.trigger("chosen:updated");
            } else if (selectType === 'select2') {
                select_type_options.locale = 'pt-BR';
                select_type_options.minimumInputLength = 2;
                select_type_options.allowClear = true;
                select_type_options.placeholder = 'Selecione o valor';
                $selectObject.select2(select_type_options);
                $selectObject.val(val);
            } else if (selectType === 'custom_select') {
                selectElementCustomRefreshFunc($selectObject);
            }
        }

        function initSelectPluginCustomTriggers(initFunc, refreshFunc, destroyFunc) {
            selectElementCustomInitFunc = initFunc;
            selectElementCustomRefreshFunc = refreshFunc;
            selectElementCustomDestroyFunc = destroyFunc;
        }

        //Used by exFilterColumn for translating readable search value into proper search string for datatables filtering
        function yadcfMatchFilterString(table_arg, column_number, selected_value, filter_match_mode, multiple, exclude) {
            var case_insensitive = yadcf.getOptions(table_arg.selector)[column_number].case_insensitive,
                ret_val;

            if (selected_value === '') {
                return '';
            }

            table_arg.fnSettings().aoPreSearchCols[column_number].bSmart = false;
            table_arg.fnSettings().aoPreSearchCols[column_number].bRegex = true;
            table_arg.fnSettings().aoPreSearchCols[column_number].bCaseInsensitive = case_insensitive;

            if (multiple === undefined || multiple === false) {
                if (exclude !== true) {
                    if (filter_match_mode === "contains") {
                        table_arg.fnSettings().aoPreSearchCols[column_number].bSmart = true;
                        table_arg.fnSettings().aoPreSearchCols[column_number].bRegex = false;
                        ret_val = selected_value;
                    } else if (filter_match_mode === "exact") {
                        ret_val = "^" + selected_value + "$";
                    } else if (filter_match_mode === "startsWith") {
                        ret_val = "^" + selected_value;
                    } else if (filter_match_mode === "regex") {
                        ret_val = selected_value;
                    }
                } else {
                    ret_val = "^((?!" + selected_value + ").)*$";
                }
            } else {
                if (filter_match_mode !== 'regex') {
                    selected_value = escapeRegExpInArray(selected_value);
                }
                if (filter_match_mode === "contains") {
                    ret_val = selected_value.join("|");
                } else if (filter_match_mode === "exact") {
                    ret_val = "^(" + selected_value.join("|") + ")$";
                } else if (filter_match_mode === "startsWith") {
                    ret_val = "^(" + selected_value.join("|") + ")";
                } else if (filter_match_mode === "regex") {
                    ret_val = selected_value;
                }
            }
            return ret_val;
        }

        function yadcfMatchFilter(oTable, selected_value, filter_match_mode, column_number, exclude) {
            var case_insensitive = yadcf.getOptions(oTable.selector)[column_number].case_insensitive;
            if (exclude !== true) {
                if (filter_match_mode === "contains") {
                    oTable.fnFilter(selected_value, column_number, false, true, true, case_insensitive);
                } else if (filter_match_mode === "exact") {
                    selected_value = escapeRegExp(selected_value);
                    oTable.fnFilter("^" + selected_value + "$", column_number, true, false, true, case_insensitive);
                } else if (filter_match_mode === "startsWith") {
                    selected_value = escapeRegExp(selected_value);
                    oTable.fnFilter("^" + selected_value, column_number, true, false, true, case_insensitive);
                } else if (filter_match_mode === "regex") {
                    try {
                        //validate regex, only call fnFilter if valid
                        new RegExp(selected_value);
                    } catch (error) {
                        return;
                    }
                    oTable.fnFilter(selected_value, column_number, true, false, true, case_insensitive);
                }
            } else {
                oTable.fnFilter("^((?!" + selected_value + ").)*$", column_number, true, false, true, case_insensitive);
            }
        }

        function yadcfParseMatchFilter(tmpStr, filter_match_mode) {
            var retVal;
            if (filter_match_mode === "contains") {
                retVal = tmpStr;
            } else if (filter_match_mode === "exact") {
                retVal = tmpStr.substring(1, tmpStr.length - 1);
                retVal = retVal.replace(/([\\])/g, '');
            } else if (filter_match_mode === "startsWith") {
                retVal = tmpStr.substring(1, tmpStr.length);
                retVal = retVal.replace(/([\\])/g, '');
            } else if (filter_match_mode === "regex") {
                retVal = tmpStr;
            }
            return retVal;
        }

        function doFilterCustomDateFunc(arg, table_selector_jq_friendly, column_number) {
            var oTable = oTables[table_selector_jq_friendly],
                yadcfState,
                columnObj = getOptions(oTable.selector)[column_number];

            if (arg === 'clear' && exGetColumnFilterVal(oTable, column_number) === '') {
                return;
            }

            if (arg.value !== undefined && arg.value !== "-1") {
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).addClass("inuse");
            } else {
                //wehn arg === 'clear' or arg.value === '-1'
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).val('-1').focus();
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).removeClass("inuse");
                refreshSelectPlugin(columnObj, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number), '-1');
            }

            if (!oTable.fnSettings().oLoadedState) {
                oTable.fnSettings().oLoadedState = {};
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }
            if (oTable.fnSettings().oFeatures.bStateSave === true) {
                if (oTable.fnSettings().oLoadedState.yadcfState !== undefined && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] !== undefined) {
                    oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number] =
                        {
                            'from': arg.value
                        };
                } else {
                    yadcfState = {};
                    yadcfState[table_selector_jq_friendly] = [];
                    yadcfState[table_selector_jq_friendly][column_number] = {
                        'from': arg.value
                    };
                    oTable.fnSettings().oLoadedState.yadcfState = yadcfState;
                }
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }

            oTable.fnDraw();
        }

        function calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly) {
            var column_number_filter;
            if ((settingsDt.oSavedState != undefined && settingsDt.oSavedState.ColReorder !== undefined) ||
                settingsDt._colReorder != undefined ||
                (plugins[table_selector_jq_friendly] !== undefined && plugins[table_selector_jq_friendly].ColReorder !== undefined)) {
                initColReorder2(settingsDt, table_selector_jq_friendly);
                column_number_filter = plugins[table_selector_jq_friendly].ColReorder[column_number];
            } else {
                column_number_filter = column_number;
            }
            return column_number_filter;
        }

        function doFilter(arg, table_selector_jq_friendly, column_number, filter_match_mode) {
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];

            var oTable = oTables[table_selector_jq_friendly],
                selected_value,
                column_number_filter,
                columnObj,
                settingsDt = getSettingsObjFromTable(oTable);

            column_number_filter = calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly);

            columnObj = getOptions(oTable.selector)[column_number];
            if (arg === "clear") {
                if (exGetColumnFilterVal(oTable, column_number) === '') {
                    return;
                }
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).val("-1").focus();
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).removeClass("inuse");
                $(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val", "-1");
                oTable.fnFilter("", column_number_filter);
                resetIApiIndex();

                refreshSelectPlugin(columnObj, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number), '-1');
                return;
            }

            $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).addClass("inuse");

            $(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val", arg.value);

            selected_value = $.trim($(arg).find('option:selected').val());

            if (arg.value !== "-1") {
                yadcfMatchFilter(oTable, selected_value, filter_match_mode, column_number_filter);
            } else {
                oTable.fnFilter("", column_number_filter);
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).removeClass("inuse");
            }
            resetIApiIndex();
        }

        function doFilterMultiSelect(arg, table_selector_jq_friendly, column_number, filter_match_mode) {
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            var oTable = oTables[table_selector_jq_friendly],
                selected_values = $(arg).val(),
                selected_values_trimmed = [],
                i,
                stringForSearch,
                column_number_filter,
                settingsDt = getSettingsObjFromTable(oTable);

            column_number_filter = calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly);
            $(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val", selected_values);

            if (selected_values !== null) {
                for (i = selected_values.length - 1; i >= 0; i--) {
                    if (selected_values[i] === "-1") {
                        selected_values.splice(i, 1);
                        break;
                    }
                }
                for (i = 0; i < selected_values.length; i++) {
                    selected_values_trimmed.push($.trim(selected_values[i]));
                }
                if (selected_values_trimmed.length !== 0) {
                    if (filter_match_mode !== "regex") {
                        stringForSearch = selected_values_trimmed.join('narutouzomaki');
                        stringForSearch = stringForSearch.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
                        stringForSearch = stringForSearch.split('narutouzomaki').join('|');
                        if (filter_match_mode === "contains") {
                            oTable.fnFilter(stringForSearch, column_number_filter, true, false, true);
                        } else if (filter_match_mode === "exact") {
                            oTable.fnFilter("^(" + stringForSearch + ")$", column_number_filter, true, false, true);
                        } else if (filter_match_mode === "startsWith") {
                            oTable.fnFilter("^(" + stringForSearch + ")", column_number_filter, true, false, true);
                        }
                    } else {
                        stringForSearch = selected_values_trimmed.join('|');
                        oTable.fnFilter(stringForSearch, column_number_filter, true, false, true);
                    }
                } else {
                    oTable.fnFilter("", column_number_filter);
                }
            } else {
                oTable.fnFilter("", column_number_filter);
            }
            resetIApiIndex();
        }

        function yadcfParseMatchFilterMultiSelect(tmpStr, filter_match_mode) {
            var retVal;
            if (filter_match_mode === "contains") {
                retVal = tmpStr;
            } else if (filter_match_mode === "exact") {
                retVal = tmpStr.substring(1, tmpStr.length - 1);
                retVal = retVal.substring(1, retVal.length - 1);
            } else if (filter_match_mode === "startsWith") {
                retVal = tmpStr.substring(1, tmpStr.length);
                retVal = retVal.substring(1, retVal.length - 1);
            } else if (filter_match_mode === "regex") {
                retVal = tmpStr;
            }
            return retVal;
        }

        function doFilterAutocomplete(arg, table_selector_jq_friendly, column_number, filter_match_mode) {
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            var oTable = oTables[table_selector_jq_friendly],
                column_number_filter,
                settingsDt = getSettingsObjFromTable(oTable);

            column_number_filter = calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly);

            if (arg === "clear") {
                if (exGetColumnFilterVal(oTable, column_number) === '') {
                    return;
                }
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).val("").focus();
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).removeClass("inuse");
                $(document).removeData("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val");
                oTable.fnFilter("", column_number_filter);
                resetIApiIndex();
                return;
            }

            $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).addClass("inuse");

            $(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val", arg.value);

            yadcfMatchFilter(oTable, arg.value, filter_match_mode, column_number_filter);

            resetIApiIndex();
        }

        function autocompleteSelect(event, ui) {
            event = eventTargetFixUp(event);
            var table_column = event.target.id.replace("yadcf-filter-", ""),
                dashIndex = table_column.lastIndexOf("-"),
                table_selector_jq_friendly = table_column.substring(0, dashIndex),
                col_num = parseInt(table_column.substring(dashIndex + 1), 10),
                filter_match_mode = $(event.target).attr("filter_match_mode");
            doFilterAutocomplete(ui.item, table_selector_jq_friendly, col_num, filter_match_mode);
        }

        function sortNumAsc(a, b) {
            return a - b;
        }

        function sortNumDesc(a, b) {
            return b - a;
        }

        function findMinInArray(array, columnObj) {
            var narray = [], i, num;
            for (i = 0; i < array.length; i++) {
                if (array[i] !== null) {
                    if (columnObj.ignore_char !== undefined) {
                        array[i] = array[i].toString().replace(columnObj.ignore_char, "");
                    }
                    if (columnObj.range_data_type === 'single') {
                        num = +array[i];
                    } else {
                        num = array[i].split(columnObj.range_data_type_delim);
                        num = num[0];
                    }
                    if (!isNaN(num)) {
                        narray.push(num);
                    }
                }
            }
            return Math.min.apply(Math, narray);
        }

        function findMaxInArray(array, columnObj) {
            var narray = [], i, num;
            for (i = 0; i < array.length; i++) {
                if (array[i] !== null) {
                    if (columnObj.ignore_char !== undefined) {
                        array[i] = array[i].toString().replace(columnObj.ignore_char, "");
                    }
                    if (columnObj.range_data_type === 'single') {
                        num = +array[i];
                    } else {
                        num = array[i].split(columnObj.range_data_type_delim);
                        num = num[1];
                    }
                    if (!isNaN(num)) {
                        narray.push(num);
                    }
                }
            }
            return Math.max.apply(Math, narray);
        }

        function addRangeNumberAndSliderFilterCapability(table_selector_jq_friendly, fromId, toId, col_num, ignore_char, sliderMaxMin) {

            $.fn.dataTableExt.afnFiltering.push(
                function (settingsDt, aData, iDataIndex, rowData) {
                    var min,
                        max,
                        val,
                        retVal = false,
                        table_selector_jq_friendly_local = table_selector_jq_friendly,
                        current_table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(settingsDt),
                        ignore_char_local = ignore_char,
                        column_data_type,
                        html_data_type,
                        i,
                        columnObjKey,
                        columnObj,
                        column_number_filter,
                        valFrom,
                        valTo;

                    if (table_selector_jq_friendly_local !== current_table_selector_jq_friendly) {
                        return true;
                    }
                    columnObj = getOptions(settingsDt.oInstance.selector)[col_num];
                    if (columnObj.filter_type === 'range_number_slider') {
                        min = $('#' + fromId).text();
                        max = $('#' + toId).text();
                    } else {
                        min = $('#' + fromId).val();
                        max = $('#' + toId).val();
                    }

                    column_number_filter = calcColumnNumberFilter(settingsDt, col_num, table_selector_jq_friendly);

                    if (rowData !== undefined) {
                        aData = rowData;
                        if (columnObj.column_number_data !== undefined) {
                            column_number_filter = columnObj.column_number_data;
                            val = dot2obj(aData, column_number_filter);
                        } else {
                            val = aData[column_number_filter];
                        }
                    } else {
                        val = aData[column_number_filter];
                    }
                    if (!isFinite(min) || !isFinite(max)) {
                        return true;
                    }
                    column_data_type = columnObj.column_data_type;
                    html_data_type = columnObj.html_data_type;

                    if (column_data_type === "html" || column_data_type === "rendered_html") {
                        if (html_data_type === undefined) {
                            html_data_type = "text";
                        }
                        if ($(val).length !== 0) {
                            switch (html_data_type) {
                                case "text":
                                    val = $(val).text();
                                    break;
                                case "value":
                                    val = $(val).val();
                                    break;
                                case "id":
                                    val = val.id;
                                    break;
                                case "selector":
                                    val = $(val).find(columnObj.html_data_selector).text();
                                    break;
                            }
                        }
                    } else {
                        if (typeof val === 'object') {
                            if (columnObj.html5_data !== undefined) {
                                val = val['@' + columnObj.html5_data];
                            }
                        }
                    }
                    if (ignore_char_local !== undefined) {
                        min = min.replace(ignore_char_local, "");
                        max = max.replace(ignore_char_local, "");
                        if (val) {
                            val = val.toString().replace(ignore_char_local, "");
                        } else {
                            val = "";
                        }
                    }
                    //omit empty rows when filtering
                    if (columnObj.filter_type === 'range_number_slider') {
                        if (val === '' && ((+min) !== sliderMaxMin.min || (+max) !== sliderMaxMin.max)) {
                            return false;
                        }
                    } else {
                        if (val === '' && (min !== '' || max !== '')) {
                            return false;
                        }
                    }
                    min = (min !== "") ? (+min) : min;
                    max = (max !== "") ? (+max) : max;
                    if (columnObj.range_data_type === 'single') {
                        val = (val !== "") ? (+val) : val;
                        if (min === "" && max === "") {
                            retVal = true;
                        } else if (min === "" && val <= max) {
                            retVal = true;
                        } else if (min <= val && "" === max) {
                            retVal = true;
                        } else if (min <= val && val <= max) {
                            retVal = true;
                        } else if (val === '' || isNaN(val)) {
                            retVal = true;
                        }
                    } else if (columnObj.range_data_type === 'range') {
                        val = val.split(columnObj.range_data_type_delim);
                        valFrom = (val[0] !== "") ? (+val[0]) : val[0];
                        valTo = (val[1] !== "") ? (+val[1]) : val[1];
                        if (min === "" && max === "") {
                            retVal = true;
                        } else if (min === "" && valTo <= max) {
                            retVal = true;
                        } else if (min <= valFrom && "" === max) {
                            retVal = true;
                        } else if (min <= valFrom && valTo <= max) {
                            retVal = true;
                        } else if ((valFrom === '' || isNaN(valFrom)) && (valTo === '' || isNaN(valTo))) {
                            retVal = true;
                        }
                    }
                    return retVal;
                }
            );
        }

        function addCustomFunctionFilterCapability(table_selector_jq_friendly, filterId, col_num) {

            $.fn.dataTableExt.afnFiltering.push(
                function (settingsDt, aData, iDataIndex, stateVal) {
                    var filterVal = $('#' + filterId).val(),
                        columnVal,
                        retVal = false,
                        table_selector_jq_friendly_local = table_selector_jq_friendly,
                        current_table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(settingsDt),
                        custom_func,
                        column_number_filter;

                    if (table_selector_jq_friendly_local !== current_table_selector_jq_friendly || filterVal === '-1') {
                        return true;
                    }

                    column_number_filter = calcColumnNumberFilter(settingsDt, col_num, table_selector_jq_friendly);

                    columnVal = aData[column_number_filter] === "-" ? 0 : aData[column_number_filter];

                    custom_func = getOptions(settingsDt.oInstance.selector)[col_num].custom_func;

                    retVal = custom_func(filterVal, columnVal, aData, stateVal);

                    return retVal;
                }
            );
        }

        function addRangeDateFilterCapability(table_selector_jq_friendly, fromId, toId, col_num, date_format) {

            $.fn.dataTableExt.afnFiltering.push(
                function (settingsDt, aData, iDataIndex, rowData) {
                    var min = document.getElementById(fromId) !== null ? document.getElementById(fromId).value : "",
                        max = document.getElementById(toId) !== null ? document.getElementById(toId).value : "",
                        val,
                        retVal = false,
                        table_selector_jq_friendly_local = table_selector_jq_friendly,
                        current_table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(settingsDt),
                        column_data_type,
                        html_data_type,
                        i,
                        columnObjKey,
                        columnObj,
                        column_number_filter,
                        min_time,
                        max_time,
                        dataRenderFunc;

                    if (table_selector_jq_friendly_local !== current_table_selector_jq_friendly) {
                        return true;
                    }
                    columnObj = getOptions(settingsDt.oInstance.selector)[col_num];

                    column_number_filter = calcColumnNumberFilter(settingsDt, col_num, table_selector_jq_friendly);
                    if (typeof columnObj.column_number_data === 'function' || typeof columnObj.column_number_render === 'function') {
                        dataRenderFunc = true;
                    }
                    if (rowData !== undefined && dataRenderFunc !== true) {
                        if (columnObj.column_number_data !== undefined) {
                            column_number_filter = columnObj.column_number_data;
                            val = dot2obj(rowData, column_number_filter);
                        } else {
                            val = rowData[column_number_filter];
                        }
                    } else {
                        val = aData[column_number_filter];
                    }

                    column_data_type = columnObj.column_data_type;
                    html_data_type = columnObj.html_data_type;

                    if (column_data_type === "html" || column_data_type === "rendered_html") {
                        if (html_data_type === undefined) {
                            html_data_type = "text";
                        }
                        if ($(val).length !== 0) {
                            switch (html_data_type) {
                                case "text":
                                    val = $(val).text();
                                    break;
                                case "value":
                                    val = $(val).val();
                                    break;
                                case "id":
                                    val = val.id;
                                    break;
                                case "selector":
                                    val = $(val).find(columnObj.html_data_selector).text();
                                    break;
                            }
                        }
                    } else {
                        if (typeof val === 'object') {
                            if (columnObj.html5_data !== undefined) {
                                val = val['@' + columnObj.html5_data];
                            }
                        }
                    }
                    //omit empty rows when filtering
                    if (val === '' && (min !== '' || max !== '')) {
                        return false;
                    }
                    try {
                        if (min.length === (date_format.length + 2) || columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                            if (columnObj.datepicker_type === 'jquery-ui') {
                                min = (min !== "") ? $.datepicker.parseDate(date_format, min) : min;
                            } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                                min = (min !== "") ? moment(min, columnObj.moment_date_format).toDate() : min;
                            }
                        }
                    } catch (err1) {
                    }
                    try {
                        if (max.length === (date_format.length + 2) || columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                            if (columnObj.datepicker_type === 'jquery-ui') {
                                max = (max !== "") ? $.datepicker.parseDate(date_format, max) : max;
                            } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                                max = (max !== "") ? moment(max, columnObj.moment_date_format).toDate() : max;
                            }
                        }
                    } catch (err2) {
                    }
                    try {
                        if (columnObj.datepicker_type === 'jquery-ui') {
                            val = (val !== "") ? $.datepicker.parseDate(date_format, val) : val;
                        } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                            val = (val !== "") ? moment(val, columnObj.moment_date_format).toDate() : val;
                        }
                    } catch (err3) {
                    }

                    if (date_format.toLowerCase() !== 'hh:mm') {
                        if ((min === "" || !(min instanceof Date)) && (max === "" || !(max instanceof Date))) {
                            retVal = true;
                        } else if (min === "" && val <= max) {
                            retVal = true;
                        } else if (min <= val && "" === max) {
                            retVal = true;
                        } else if (min <= val && val <= max) {
                            retVal = true;
                        }
                    } else {
                        min_time = moment(min);
                        min_time = min_time.minutes() + min_time.hours() * 60;
                        if (isNaN(min_time)) {
                            min_time = '';
                        }
                        max_time = moment(max);
                        max_time = max_time.minutes() + max_time.hours() * 60;
                        if (isNaN(max_time)) {
                            max_time = '';
                        }
                        val = moment(val);
                        val = val.minutes() + val.hours() * 60;

                        if ((min === "" || !(moment(min, date_format).isValid())) && (max === "" || !(moment(max, date_format).isValid()))) {
                            retVal = true;
                        } else if (min_time === "" && val <= max_time) {
                            retVal = true;
                        } else if (min_time <= val && "" === max_time) {
                            retVal = true;
                        } else if (min_time <= val && val <= max_time) {
                            retVal = true;
                        }
                    }
                    return retVal;
                }
            );
        }

        function addRangeNumberFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, ignore_char) {
            var fromId = "yadcf-filter-" + table_selector_jq_friendly + "-from-" + column_number,
                toId = "yadcf-filter-" + table_selector_jq_friendly + "-to-" + column_number,
                filter_selector_string_tmp,
                filter_wrapper_id,
                oTable,
                columnObj,
                filterActionStr;

            filter_wrapper_id = "yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number;

            if ($("#" + filter_wrapper_id).length > 0) {
                return;
            }
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            oTable = oTables[table_selector_jq_friendly];
            columnObj = getOptions(oTable.selector)[column_number];

            //add a wrapper to hold both filter and reset button
            $(filter_selector_string).append("<div onmousedown=\"yadcf.stopPropagation(event);\" onclick=\"yadcf.stopPropagation(event);\"  id=\"" + filter_wrapper_id + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";
            filter_selector_string_tmp = filter_selector_string;

            $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-inner-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"no-print yadcf-filter-wrapper-inner" + " -" + table_selector_jq_friendly + "-" + column_number + "\"></div>");
            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper-inner";

            filterActionStr = 'onkeyup="yadcf.rangeNumberKeyUP(\'' + table_selector_jq_friendly + '\',event);"';
            if (columnObj.externally_triggered === true) {
                filterActionStr = '';
            }

            $(filter_selector_string).append("<input onkeydown=\"yadcf.preventDefaultForEnter(event);\" placeholder=\"" + filter_default_label[0] + "\" id=\"" + fromId + "\" class=\"yadcf-filter-range-number yadcf-filter-range\" " + filterActionStr + "></input>");
            $(filter_selector_string).append("<span class=\"yadcf-filter-range-number-seperator\" >" +
                "</span>");
            $(filter_selector_string).append("<input onkeydown=\"yadcf.preventDefaultForEnter(event);\" placeholder=\"" + filter_default_label[1] + "\" id=\"" + toId + "\" class=\"yadcf-filter-range-number yadcf-filter-range\" " + filterActionStr + "></input>");

            // if (filter_reset_button_text !== false) {
            //     $(filter_selector_string_tmp).append("<button type=\"button\" onmousedown=\"yadcf.stopPropagation(event);\" " +
            //         "onclick=\"yadcf.stopPropagation(event);yadcf.rangeClear('" + table_selector_jq_friendly + "',event," + column_number + "); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
            // }

            if (oTable.fnSettings().oFeatures.bStateSave === true && oTable.fnSettings().oLoadedState) {
                if (oTable.fnSettings().oLoadedState.yadcfState && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number]) {
                    $('#' + fromId).val(oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from);
                    if (oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from !== "") {
                        $('#' + fromId).addClass("inuse");
                    }
                    $('#' + toId).val(oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to);
                    if (oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to !== "") {
                        $('#' + toId).addClass("inuse");
                    }
                }
            }
            resetIApiIndex();

            if (oTable.fnSettings().oFeatures.bServerSide !== true) {
                addRangeNumberAndSliderFilterCapability(table_selector_jq_friendly, fromId, toId, column_number, ignore_char);
            }

        }

        function dateSelectSingle(pDate, pEvent, clear) {
            var oTable,
                date,
                event,
                column_number,
                dashIndex,
                table_selector_jq_friendly,
                column_number_filter,
                settingsDt,
                columnObj;

            if (pDate.type === 'dp') {
                event = pDate.target;
            } else {
                date = pDate;
                event = pEvent;
            }
            column_number = $(event).attr('id').replace('yadcf-filter-', '').replace('-date', '').replace('-reset', '');
            dashIndex = column_number.lastIndexOf("-");
            table_selector_jq_friendly = column_number.substring(0, dashIndex);

            column_number = column_number.substring(dashIndex + 1);
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            oTable = oTables[table_selector_jq_friendly];
            settingsDt = getSettingsObjFromTable(oTable);
            columnObj = getOptions(oTable.selector)[column_number];

            if (pDate.type === 'dp') {
                if (moment($(event).val(), columnObj.date_format).isValid()) {
                    date = $(event).val();
                } else {
                    clear = 'clear';
                }
                $(event).blur();
            }

            column_number_filter = calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly);

            if (clear === undefined) {
                oTable.fnFilter(date, column_number_filter);
                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).addClass("inuse");
            } else if (clear === 'clear') {
                if (exGetColumnFilterVal(oTable, column_number) === '') {
                    return;
                }
                oTable.fnFilter('', column_number_filter);
                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val('').removeClass("inuse");
            }

            resetIApiIndex();
        }

        function dateSelect(pDate, pEvent) {
            var oTable,
                column_number,
                dashIndex,
                table_selector_jq_friendly,
                yadcfState,
                from,
                to,
                date,
                event,
                columnObj;

            if (pDate.type === 'dp') {
                event = pDate.target;
            } else {
                date = pDate;
                event = pEvent;
            }

            column_number = $(event).attr("id").replace("yadcf-filter-", "").replace("-from-date", "").replace("-to-date", "");
            dashIndex = column_number.lastIndexOf("-");
            table_selector_jq_friendly = column_number.substring(0, dashIndex);

            column_number = column_number.substring(dashIndex + 1);

            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];

            oTable = oTables[table_selector_jq_friendly];
            columnObj = getOptions(oTable.selector)[column_number];

            if (pDate.type === 'dp') {
                event = pDate.target;
                if (pDate.date === false || !moment($(event).val(), columnObj.date_format).isValid()) {
                    $(event).removeClass("inuse");
                    $(event).data("DateTimePicker").minDate(false);
                } else {
                    $(event).addClass("inuse");
                }
                $(event).blur();
            } else {
                $(event).addClass("inuse");
            }

            if ($(event).attr("id").indexOf("-from-") !== -1) {
                from = document.getElementById($(event).attr("id")).value;
                to = document.getElementById($(event).attr("id").replace("-from-", "-to-")).value;
            } else {
                to = document.getElementById($(event).attr("id")).value;
                from = document.getElementById($(event).attr("id").replace("-to-", "-from-")).value;
            }

            if (oTable.fnSettings().oFeatures.bServerSide !== true) {
                oTable.fnDraw();
            } else {
                oTable.fnFilter(from + '-yadcf_delim-' + to, column_number);
            }

            if (!oTable.fnSettings().oLoadedState) {
                oTable.fnSettings().oLoadedState = {};
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }
            if (oTable.fnSettings().oFeatures.bStateSave === true) {
                if (oTable.fnSettings().oLoadedState.yadcfState !== undefined && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] !== undefined) {
                    oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number] =
                        {
                            'from': from,
                            'to': to
                        };
                } else {
                    yadcfState = {};
                    yadcfState[table_selector_jq_friendly] = [];
                    yadcfState[table_selector_jq_friendly][column_number] = {
                        'from': from,
                        'to': to
                    };
                    oTable.fnSettings().oLoadedState.yadcfState = yadcfState;
                }
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }

            resetIApiIndex();
        }

        function addRangeDateFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, date_format) {
            var fromId = "yadcf-filter-" + table_selector_jq_friendly + "-from-date-" + column_number,
                toId = "yadcf-filter-" + table_selector_jq_friendly + "-to-date-" + column_number,
                filter_selector_string_tmp,
                filter_wrapper_id,
                oTable,
                columnObj,
                datepickerObj = {},
                filterActionStr,
                filterClass = '',
                $fromInput,
                $toInput,
                innerWrapperAdditionalClass = '';

            filter_wrapper_id = "yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number;

            if ($("#" + filter_wrapper_id).length > 0) {
                return;
            }
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            oTable = oTables[table_selector_jq_friendly];
            columnObj = getOptions(oTable.selector)[column_number];
            if (columnObj.datepicker_type === 'bootstrap-datepicker') {
                innerWrapperAdditionalClass = 'input-daterange';
            }
            //add a wrapper to hold both filter and reset button
            $(filter_selector_string).append("<div onmousedown=\"yadcf.stopPropagation(event);\" onclick=\"yadcf.stopPropagation(event);\"  id=\"" + filter_wrapper_id + "\" class=\"yadcf-filter-wrapper\"></div>");
            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";
            filter_selector_string_tmp = filter_selector_string;

            $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-inner-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"no-print yadcf-filter-wrapper-inner " + innerWrapperAdditionalClass + "\"></div>");
            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper-inner";

            filterActionStr = 'onkeyup="yadcf.rangeDateKeyUP(\'' + table_selector_jq_friendly + '\',\'' + date_format + '\',event);"';
            if (columnObj.externally_triggered === true) {
                filterActionStr = '';
            }

            $(filter_selector_string).append("<input onkeydown=\"yadcf.preventDefaultForEnter(event);\" placeholder=\"" + filter_default_label[0] + "\" id=\"" + fromId + "\" class=\"yadcf-filter-range-date yadcf-filter-range\" " + filterActionStr + "></input>");
            $(filter_selector_string).append("<span class=\"yadcf-filter-range-date-seperator\" >" +
                "</span>");
            $(filter_selector_string).append("<input onkeydown=\"yadcf.preventDefaultForEnter(event);\" placeholder=\"" + filter_default_label[1] + "\" id=\"" + toId + "\" class=\"yadcf-filter-range-date yadcf-filter-range\" " + filterActionStr + "></input>");

            $fromInput = $("#" + fromId);
            $toInput = $("#" + toId);

            // if (filter_reset_button_text !== false) {
            //     $(filter_selector_string_tmp).append("<button type=\"button\" onmousedown=\"yadcf.stopPropagation(event);\" " +
            //         "onclick=\"yadcf.stopPropagation(event);yadcf.rangeClear('" + table_selector_jq_friendly + "',event," + column_number + "); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
            // }

            if (columnObj.datepicker_type === 'jquery-ui') {
                datepickerObj.dateFormat = date_format;
            } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                datepickerObj.format = date_format;
            }

            if (columnObj.externally_triggered !== true) {
                if (columnObj.datepicker_type === 'jquery-ui') {
                    datepickerObj.onSelect = dateSelect;
                }
                // for 'bootstrap-datetimepicker' its implemented below...
            }

            datepickerObj = $.extend({}, datepickerObj, columnObj.filter_plugin_options);

            if (columnObj.datepicker_type === 'jquery-ui') {
                $fromInput.datepicker($.extend(datepickerObj, {
                    onClose: function (selectedDate) {
                        $toInput.datepicker('option', 'minDate', selectedDate);
                    }
                }));
                $toInput.datepicker($.extend(datepickerObj, {
                    onClose: function (selectedDate) {
                        $fromInput.datepicker('option', 'maxDate', selectedDate);
                    }
                }));

            } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                datepickerObj.useCurrent = false;
                $fromInput.datetimepicker(datepickerObj);
                $toInput.datetimepicker(datepickerObj);
                if (columnObj.externally_triggered !== true) {
                    $fromInput.add($toInput).on('dp.hide', dateSelect);
                }
            } else if (columnObj.datepicker_type === 'bootstrap-datepicker') {
                //$fromInput.datepicker({});
                //$toInput.datepicker({});
            }

            if (oTable.fnSettings().oFeatures.bStateSave === true && oTable.fnSettings().oLoadedState) {
                if (oTable.fnSettings().oLoadedState.yadcfState && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number]) {
                    $('#' + fromId).val(oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from);
                    if (oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from !== "") {
                        $('#' + fromId).addClass("inuse");
                    }
                    $('#' + toId).val(oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to);
                    if (oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to !== "") {
                        $('#' + toId).addClass("inuse");
                    }
                }
            }

            if (oTable.fnSettings().oFeatures.bServerSide !== true) {
                addRangeDateFilterCapability(table_selector_jq_friendly, fromId, toId, column_number, date_format);
            }

            resetIApiIndex();
        }

        function addDateFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, date_format) {
            var dateId = "yadcf-filter-" + table_selector_jq_friendly + "-" + column_number,
                filter_selector_string_tmp,
                filter_wrapper_id,
                oTable,
                columnObj,
                datepickerObj = {},
                filterActionStr;

            filter_wrapper_id = "yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number;

            if ($("#" + filter_wrapper_id).length > 0) {
                return;
            }
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            oTable = oTables[table_selector_jq_friendly];
            columnObj = getOptions(oTable.selector)[column_number];

            //add a wrapper to hold both filter and reset button
            $(filter_selector_string).append("<div onmousedown=\"yadcf.stopPropagation(event);\" onclick=\"yadcf.stopPropagation(event);\" id=\"" + filter_wrapper_id + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";
            filter_selector_string_tmp = filter_selector_string;

            filterActionStr = 'onkeyup="yadcf.dateKeyUP(\'' + table_selector_jq_friendly + '\',\'' + date_format + '\',event);"';
            if (columnObj.externally_triggered === true) {
                filterActionStr = '';
            }

            $(filter_selector_string).append("<input onkeydown=\"yadcf.preventDefaultForEnter(event);\" placeholder=\"" + filter_default_label + "\" id=\"" + dateId + "\" class=\"yadcf-filter-date\" " + filterActionStr + "></input>");

            // if (filter_reset_button_text !== false) {
            //     $(filter_selector_string_tmp).append('<button type="button" id="' + dateId + '-reset" ' + 'onmousedown="yadcf.stopPropagation(event);" ' +
            //         'onclick="yadcf.stopPropagation(event);yadcf.dateSelectSingle(\'' + table_selector_jq_friendly + '\',yadcf.eventTargetFixUp(event).target, \'clear\'); return false;" class="yadcf-filter-reset-button ' + columnObj.reset_button_style_class + '">' + filter_reset_button_text + '</button>');
            // }

            if (columnObj.datepicker_type === 'jquery-ui') {
                datepickerObj.dateFormat = date_format;
            } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                datepickerObj.format = date_format;
            }

            if (columnObj.externally_triggered !== true) {
                if (columnObj.datepicker_type === 'jquery-ui') {
                    datepickerObj.onSelect = dateSelectSingle;
                }
            }

            datepickerObj = $.extend({}, datepickerObj, columnObj.filter_plugin_options);

            if(columnObj.filter_type === 'bap_date_range_picker'){

                $("#" + dateId).daterangepicker(
                    window.DATATABLE_DATERANGE_CONFIG
                );
                $("#" + dateId).on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(window.APPLICATION_USER_DATE_FORMAT) + ' - ' + picker.endDate.format(window.APPLICATION_USER_DATE_FORMAT));
                    //APLY SEARCH
                    yadcf.exFilterExternallyTriggered(oTable);
                });
                $("#" + dateId).on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    yadcf.exResetFilters(oTable, [columnObj.column_number]);

                });
            } else if(columnObj.filter_type === 'bap_date_picker'){ //TODO STILL IN WORK !!!

                $("#" + dateId).daterangepicker(
                    window.DATATABLE_SINGLEDATE_CONFIG
                );
                $("#" + dateId).on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(window.APPLICATION_USER_DATE_FORMAT));
                    //APLY SEARCH
                    yadcf.exFilterExternallyTriggered(oTable);
                });
                $("#" + dateId).on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    yadcf.exResetFilters(oTable, [columnObj.column_number]);

                });

            }else  if (columnObj.datepicker_type === 'jquery-ui') {
                $("#" + dateId).datepicker(datepickerObj);
            } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                datepickerObj.useCurrent = false;
                $("#" + dateId).datetimepicker(datepickerObj);
                if (columnObj.externally_triggered !== true) {
                    if (datepickerObj.format.toLowerCase() !== 'hh:mm') {
                        $("#" + dateId).on('dp.change', dateSelectSingle);
                    } else {
                        $("#" + dateId).on('dp.hide', dateSelectSingle);
                    }
                }
            } else if (columnObj.datepicker_type === 'bootstrap-datepicker') {
                $("#" + dateId).datepicker({});
            }

            if (oTable.fnSettings().aoPreSearchCols[column_number].sSearch !== '') {
                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(oTable.fnSettings().aoPreSearchCols[column_number].sSearch).addClass("inuse");
            }

            resetIApiIndex();
        }

        function rangeNumberSldierDrawTips(min_tip_val, max_tip_val, min_tip_id, max_tip_id, table_selector_jq_friendly, column_number) {
            var first_handle = $(".yadcf-number-slider-filter-wrapper-inner.-" + table_selector_jq_friendly + "-" + column_number),// + " .ui-slider-handle:first"),
                last_handle = $(".yadcf-number-slider-filter-wrapper-inner.-" + table_selector_jq_friendly + "-" + column_number),// + " .ui-slider-handle:last"),
                min_tip_inner,
                max_tip_inner;

            min_tip_inner = "<div id=\"" + min_tip_id + "\" class=\"yadcf-filter-range-number-slider-min-tip-inner\">" + min_tip_val + "</div>";
            max_tip_inner = "<div id=\"" + max_tip_id + "\" class=\"yadcf-filter-range-number-slider-max-tip-inner\">" + max_tip_val + "</div>";

            if (first_handle.length === 1) {
                first_handle = $(".yadcf-number-slider-filter-wrapper-inner.-" + table_selector_jq_friendly + "-" + column_number + " .ui-slider-handle:first");
                $(first_handle).addClass("yadcf-filter-range-number-slider-min-tip").html(min_tip_inner);

                last_handle = $(".yadcf-number-slider-filter-wrapper-inner.-" + table_selector_jq_friendly + "-" + column_number + " .ui-slider-handle:last");
                $(last_handle).addClass("yadcf-filter-range-number-slider-max-tip").html(max_tip_inner);
            } else {
                //migth happen when scrollX is used or when filter row is being duplicated by DT
                $($(first_handle)[0]).find('.ui-slider-handle:first').addClass("yadcf-filter-range-number-slider-min-tip").html(min_tip_inner);
                $($(last_handle)[0]).find('.ui-slider-handle:last').addClass("yadcf-filter-range-number-slider-max-tip").html(max_tip_inner);

                $($(first_handle)[1]).find('.ui-slider-handle:first').addClass("yadcf-filter-range-number-slider-min-tip").html(min_tip_inner);
                $($(last_handle)[1]).find('.ui-slider-handle:last').addClass("yadcf-filter-range-number-slider-max-tip").html(max_tip_inner);
            }
        }

        function rangeNumberSliderChange(table_selector_jq_friendly, event, ui) {
            event = eventTargetFixUp(event);
            var oTable,
                min_val,
                max_val,
                slider_inuse,
                yadcfState,
                column_number = $(event.target).attr('id').replace("yadcf-filter-", "").replace(table_selector_jq_friendly, "").replace("-slider-", ""),
                columnObj,
                keyUp;

            oTable = oTables[table_selector_jq_friendly];
            columnObj = getOptions(oTable.selector)[column_number];

            keyUp = function () {

                $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];

                if (oTable.fnSettings().oFeatures.bServerSide !== true) {
                    oTable.fnDraw();
                } else {
                    oTable.fnFilter(ui.values[0] + '-yadcf_delim-' + ui.values[1], column_number);
                }
                min_val = +$($(event.target).parent().find(".yadcf-filter-range-number-slider-min-tip-hidden")).text();
                max_val = +$($(event.target).parent().find(".yadcf-filter-range-number-slider-max-tip-hidden")).text();

                if (min_val !== ui.values[0]) {
                    $($(event.target).find(".ui-slider-handle")[0]).addClass("inuse");
                    slider_inuse = true;
                } else {
                    $($(event.target).find(".ui-slider-handle")[0]).removeClass("inuse");
                }
                if (max_val !== ui.values[1]) {
                    $($(event.target).find(".ui-slider-handle")[1]).addClass("inuse");
                    slider_inuse = true;
                } else {
                    $($(event.target).find(".ui-slider-handle")[1]).removeClass("inuse");
                }

                if (slider_inuse === true) {
                    $(event.target).find(".ui-slider-range").addClass("inuse");
                } else {
                    $(event.target).find(".ui-slider-range").removeClass("inuse");
                }

                if (!oTable.fnSettings().oLoadedState) {
                    oTable.fnSettings().oLoadedState = {};
                    oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
                }
                if (oTable.fnSettings().oFeatures.bStateSave === true) {
                    if (oTable.fnSettings().oLoadedState.yadcfState !== undefined && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] !== undefined) {
                        oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number] =
                            {
                                'from': ui.values[0],
                                'to': ui.values[1]
                            };
                    } else {
                        yadcfState = {};
                        yadcfState[table_selector_jq_friendly] = [];
                        yadcfState[table_selector_jq_friendly][column_number] = {
                            'from': ui.values[0],
                            'to': ui.values[1]
                        };
                        oTable.fnSettings().oLoadedState.yadcfState = yadcfState;
                    }
                    oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
                }

                resetIApiIndex();
            };

            if (columnObj.filter_delay === undefined) {
                keyUp();
            } else {
                yadcfDelay(function () {
                    keyUp();
                }, columnObj.filter_delay);
            }
        }

        function addRangeNumberSliderFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, min_val, max_val, ignore_char) {
            var sliderId = "yadcf-filter-" + table_selector_jq_friendly + "-slider-" + column_number,
                min_tip_id = "yadcf-filter-" + table_selector_jq_friendly + "-min_tip-" + column_number,
                max_tip_id = "yadcf-filter-" + table_selector_jq_friendly + "-max_tip-" + column_number,
                filter_selector_string_tmp,
                filter_wrapper_id,
                oTable,
                min_state_val = min_val,
                max_state_val = max_val,
                columnObj,
                slideFunc,
                changeFunc,
                sliderObj,
                sliderMaxMin = {
                    min: min_val,
                    max: max_val
                },
                settingsDt,
                currSliderMin = $("#" + sliderId).slider("option", "min"),
                currSliderMax = $("#" + sliderId).slider("option", "max"),
                redrawTable;

            filter_wrapper_id = "yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number;

            if ($("#" + filter_wrapper_id).length > 0 && (currSliderMin === min_val && currSliderMax === max_val)) {
                return;
            }

            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            oTable = oTables[table_selector_jq_friendly];
            settingsDt = settingsMap[generateTableSelectorJQFriendly2(oTable)];

            if ($("#" + filter_wrapper_id).length > 0) {
                $("#" + sliderId).slider("destroy");
                $("#" + filter_wrapper_id).remove();
                redrawTable = true;
            }

            columnObj = getOptions(oTable.selector)[column_number];

            if (settingsDt.oFeatures.bStateSave === true && settingsDt.oLoadedState) {
                if (settingsDt.oLoadedState.yadcfState && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly] && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number]) {
                    if (min_val !== settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from) {
                        min_state_val = settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from;
                    }
                    if (max_val !== settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to) {
                        max_state_val = settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to;
                    }
                }
            }

            if (isFinite(min_val) && isFinite(max_val) && isFinite(min_state_val) && isFinite(max_state_val)) {

                //add a wrapper to hold both filter and reset button
                $(filter_selector_string).append("<div onmousedown=\"yadcf.stopPropagation(event);\" onclick=\"yadcf.stopPropagation(event);\"  id=\"" + filter_wrapper_id + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
                filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";
                filter_selector_string_tmp = filter_selector_string;

                $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-inner-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"yadcf-number-slider-filter-wrapper-inner" + " -" + table_selector_jq_friendly + "-" + column_number + "\"></div>");
                filter_selector_string = filter_selector_string + " div.yadcf-number-slider-filter-wrapper-inner";

                $(filter_selector_string).append("<div id=\"" + sliderId + "\" class=\"yadcf-filter-range-number-slider\"></div>");
                filter_selector_string = filter_selector_string + " #" + sliderId;

                $(filter_selector_string).append("<span class=\"yadcf-filter-range-number-slider-min-tip-hidden hide\">" + min_val + "</span>");
                $(filter_selector_string).append("<span class=\"yadcf-filter-range-number-slider-max-tip-hidden hide\">" + max_val + "</span>");

                if (columnObj.externally_triggered !== true) {
                    slideFunc = function (event, ui) {
                        rangeNumberSldierDrawTips(ui.values[0], ui.values[1], min_tip_id, max_tip_id, table_selector_jq_friendly, column_number);
                        rangeNumberSliderChange(table_selector_jq_friendly, event, ui);
                    };
                    changeFunc = function (event, ui) {
                        rangeNumberSldierDrawTips(ui.values[0], ui.values[1], min_tip_id, max_tip_id, table_selector_jq_friendly, column_number);
                        if (event.originalEvent || $(event.target).slider("option", "yadcf-reset") === true) {
                            $(event.target).slider("option", "yadcf-reset", false);
                            rangeNumberSliderChange(table_selector_jq_friendly, event, ui);
                        }
                    };
                } else {
                    slideFunc = function (event, ui) {
                        rangeNumberSldierDrawTips(ui.values[0], ui.values[1], min_tip_id, max_tip_id, table_selector_jq_friendly, column_number);
                    };
                    changeFunc = function (event, ui) {
                        rangeNumberSldierDrawTips(ui.values[0], ui.values[1], min_tip_id, max_tip_id, table_selector_jq_friendly, column_number);
                    };
                }
                sliderObj = {
                    range: true,
                    min: min_val,
                    max: max_val,
                    values: [min_state_val, max_state_val],
                    create: function (event, ui) {
                        rangeNumberSldierDrawTips(min_state_val, max_state_val, min_tip_id, max_tip_id, table_selector_jq_friendly, column_number);
                    },
                    slide: slideFunc,
                    change: changeFunc
                };

                if (columnObj.filter_plugin_options !== undefined) {
                    $.extend(sliderObj, columnObj.filter_plugin_options);
                }

                $("#" + sliderId).slider(sliderObj);

                // if (filter_reset_button_text !== false) {
                //     $(filter_selector_string_tmp).append("<button type=\"button\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                //         "onclick=\"yadcf.stopPropagation(event);yadcf.rangeNumberSliderClear('" + table_selector_jq_friendly + "',event); return false;\" class=\"yadcf-filter-reset-button range-number-slider-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
                // }
            }

            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            oTable = oTables[table_selector_jq_friendly];
            if (settingsDt.oFeatures.bStateSave === true && settingsDt.oLoadedState) {
                if (settingsDt.oLoadedState.yadcfState && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly] && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number]) {
                    if (isFinite(min_val) && min_val !== settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from) {
                        $($(filter_selector_string).find(".ui-slider-handle")[0]).addClass("inuse");
                    }
                    if (isFinite(max_val) && max_val !== settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to) {
                        $($(filter_selector_string).find(".ui-slider-handle")[1]).addClass("inuse");
                    }
                    if ((isFinite(min_val) && isFinite(max_val)) && (min_val !== settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from || max_val !== settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].to)) {
                        $($(filter_selector_string).find(".ui-slider-range")).addClass("inuse");
                    }
                }
            }
            resetIApiIndex();

            if (settingsDt.oFeatures.bServerSide !== true) {
                addRangeNumberAndSliderFilterCapability(table_selector_jq_friendly, min_tip_id, max_tip_id, column_number, ignore_char, sliderMaxMin);
            }
            if (redrawTable === true) {
                oTable.fnDraw(false);
            }
        }

        function destroyThirdPartyPlugins(table_arg) {

            var tableOptions,
                table_selector_jq_friendly,
                settingsDt,
                columnObjKey,
                column_number,
                optionsObj,
                fromId,
                toId;

            //check if the table arg is from new datatables API (capital "D")
            if (table_arg.settings !== undefined) {
                table_arg = table_arg.settings()[0].oInstance;
            }
            tableOptions = getOptions(table_arg.selector);
            table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(table_arg);
            settingsDt = getSettingsObjFromTable(table_arg);

            for (columnObjKey in tableOptions) {
                if (tableOptions.hasOwnProperty(columnObjKey)) {
                    optionsObj = tableOptions[columnObjKey];
                    column_number = optionsObj.column_number;

                    switch (optionsObj.filter_type) {
                        case 'multi_select':
                        case 'multi_select_custom_func':
                        case 'select':
                        case 'custom_func':
                            switch (optionsObj.select_type) {
                                case 'chosen':
                                    $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).chosen('destroy');
                                    break;
                                case 'select2':
                                    $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).select2('destroy');
                                    break;
                                case 'custom_select':
                                    if (selectElementCustomDestroyFunc !== undefined) {
                                        selectElementCustomDestroyFunc($("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number));
                                    }
                                    break;
                            }
                            break;
                        case 'auto_complete':
                            $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).autocomplete("destroy");
                            break;
                        case 'date':
                            switch (optionsObj.select_type) {
                                case 'jquery-ui':
                                    $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).datepicker("destroy");
                                    break;
                                case 'bootstrap-datetimepicker':
                                    $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).destroy();
                                    break;
                            }
                            break;
                        case 'range_date':
                            fromId = "yadcf-filter-" + table_selector_jq_friendly + "-from-date-" + column_number;
                            toId = "yadcf-filter-" + table_selector_jq_friendly + "-to-date-" + column_number;
                            switch (optionsObj.select_type) {
                                case 'jquery-ui':
                                    $("#" + fromId).datepicker("destroy");
                                    $("#" + toId).datepicker("destroy");
                                    break;
                                case 'bootstrap-datetimepicker':
                                    $("#" + fromId).destroy();
                                    $("#" + toId).destroy();
                                    break;
                            }
                            break;
                        case 'range_number_slider':
                            $("#yadcf-filter-" + table_selector_jq_friendly + "-slider-" + column_number).slider("destroy");
                            break;
                    }
                }
            }
        }

        function removeFilters(oTable, args, table_selector) {
            var tableId = getTableId(oTable);
            $('#' + tableId + ' .yadcf-filter-wrapper').remove();
            if (yadcfVersionCheck('1.10')) {
                $(document).off('draw.dt', oTable.selector);
                $(document).off('xhr.dt', oTable.selector);
                $(document).off('column-visibility.dt', oTable.selector);
                $(document).off('destroy.dt', oTable.selector);
            } else {
                $(document).off('draw', oTable.selector);
                $(document).off('destroy', oTable.selector);
            }
            destroyThirdPartyPlugins(oTable);
        }

        /* alphanum.js (C) Brian Huisman
		   Based on the Alphanum Algorithm by David Koelle
		   The Alphanum Algorithm is discussed at http://www.DaveKoelle.com
		*/
        function sortAlphaNum(a, b) {
            function chunkify(t) {
                var tz = new Array();
                var x = 0, y = -1, n = 0, i, j;

                while (i = (j = t.charAt(x++)).charCodeAt(0)) {
                    var m = (i == 46 || (i >= 48 && i <= 57));
                    if (m !== n) {
                        tz[++y] = "";
                        n = m;
                    }
                    tz[y] += j;
                }
                return tz;
            }

            if (typeof a === 'object' && typeof a.label === 'string') {
                a = a.label;
            }
            if (typeof b === 'object' && typeof b.label === 'string') {
                b = b.label;
            }

            var aa = chunkify(a.toLowerCase());
            var bb = chunkify(b.toLowerCase());

            for (var x = 0; aa[x] && bb[x]; x++) {
                if (aa[x] !== bb[x]) {
                    var c = Number(aa[x]), d = Number(bb[x]);
                    if (c == aa[x] && d == bb[x]) {
                        return c - d;
                    } else return (aa[x] > bb[x]) ? 1 : -1;
                }
            }
            return aa.length - bb.length;
        }

        function sortColumnData(column_data, columnObj) {
            var numArray = [],
                alphaArray = [];
            if (columnObj.filter_type === "select" || columnObj.filter_type === "auto_complete" || columnObj.filter_type === "multi_select" || columnObj.filter_type === 'multi_select_custom_func' || columnObj.filter_type === "custom_func") {
                if (columnObj.sort_as === "alpha") {
                    if (columnObj.sort_order === "asc") {
                        column_data.sort();
                    } else if (columnObj.sort_order === "desc") {
                        column_data.sort();
                        column_data.reverse();
                    }
                } else if (columnObj.sort_as === "num") {
                    if (columnObj.sort_order === "asc") {
                        column_data.sort(sortNumAsc);
                    } else if (columnObj.sort_order === "desc") {
                        column_data.sort(sortNumDesc);
                    }
                } else if (columnObj.sort_as === "alphaNum") {
                    if (columnObj.sort_order === "asc") {
                        column_data.sort(sortAlphaNum);
                    } else if (columnObj.sort_order === "desc") {
                        column_data.sort(sortAlphaNum);
                        column_data.reverse();
                    }
                } else if (columnObj.sort_as === "custom") {
                    column_data.sort(columnObj.sort_as_custom_func);
                }
            }
            return column_data;
        }

        function getFilteredRows(table) {
            var dataTmp,
                data = [],
                i;
            if (yadcfVersionCheck('1.10')) {
                dataTmp = table._('tr', {filter: 'applied'});
            } else {
                dataTmp = table.rows({filter: 'applied'}).data().toArray();
            }
            for (i = 0; i < dataTmp.length; i++) {
                data.push({
                    _aData: dataTmp[i]
                });
            }
            return data;
        }

        function parseTableColumn(pTable, columnObj, table_selector_jq_friendly, pSettings) {
            var col_inner_elements,
                col_inner_data,
                j,
                k,
                col_filter_array = {},
                column_data = [],
                data,
                data_length,
                settingsDt,
                column_number_filter;

            if (pSettings !== undefined) {
                settingsDt = pSettings;
            } else {
                settingsDt = getSettingsObjFromTable(pTable);
            }

            if (columnObj.cumulative_filtering !== true) {
                data = settingsDt.aoData;
                data_length = data.length;
            } else {
                data = getFilteredRows(pTable);
                data_length = data.length;
            }
            if (columnObj.col_filter_array !== undefined) {
                col_filter_array = columnObj.col_filter_array;
            }
            column_number_filter = calcColumnNumberFilter(settingsDt, columnObj.column_number, table_selector_jq_friendly);
            if (isNaN(settingsDt.aoColumns[column_number_filter].mData) && typeof settingsDt.aoColumns[column_number_filter].mData !== 'object') {
                columnObj.column_number_data = settingsDt.aoColumns[column_number_filter].mData;
            }
            if (isNaN(settingsDt.aoColumns[column_number_filter].mRender) && typeof settingsDt.aoColumns[column_number_filter].mRender !== 'object') {
                columnObj.column_number_render = settingsDt.aoColumns[column_number_filter].mRender;
            }

            for (j = 0; j < data_length; j++) {
                if (columnObj.column_data_type === "html") {
                    if (columnObj.column_number_data === undefined) {
                        col_inner_elements = $(data[j]._aData[column_number_filter]);
                    } else {
                        col_inner_elements = dot2obj(data[j]._aData, columnObj.column_number_data);
                        col_inner_elements = $(col_inner_elements);
                    }
                    if (col_inner_elements.length > 0) {
                        for (k = 0; k < col_inner_elements.length; k++) {
                            switch (columnObj.html_data_type) {
                                case "text":
                                    col_inner_data = $(col_inner_elements[k]).text();
                                    break;
                                case "value":
                                    col_inner_data = $(col_inner_elements[k]).val();
                                    break;
                                case "id":
                                    col_inner_data = col_inner_elements[k].id;
                                    break;
                                case "selector":
                                    col_inner_data = $(col_inner_elements[k]).find(columnObj.html_data_selector).text();
                                    break;
                            }

                            if ($.trim(col_inner_data) !== '' && !(col_filter_array.hasOwnProperty(col_inner_data))) {
                                col_filter_array[col_inner_data] = col_inner_data;
                                column_data.push(col_inner_data);
                            }
                        }
                    } else {
                        col_inner_data = col_inner_elements.selector;
                        if ($.trim(col_inner_data) !== '' && !(col_filter_array.hasOwnProperty(col_inner_data))) {
                            col_filter_array[col_inner_data] = col_inner_data;
                            column_data.push(col_inner_data);
                        }
                    }

                } else if (columnObj.column_data_type === "text") {
                    if (columnObj.text_data_delimiter !== undefined) {
                        if (columnObj.column_number_data === undefined) {
                            col_inner_elements = data[j]._aData[column_number_filter].split(columnObj.text_data_delimiter);
                        } else {
                            col_inner_elements = dot2obj(data[j]._aData, columnObj.column_number_data);
                            col_inner_elements = (col_inner_elements + '').split(columnObj.text_data_delimiter);
                        }
                        for (k = 0; k < col_inner_elements.length; k++) {
                            col_inner_data = col_inner_elements[k];
                            if ($.trim(col_inner_data) !== '' && !(col_filter_array.hasOwnProperty(col_inner_data))) {
                                col_filter_array[col_inner_data] = col_inner_data;
                                column_data.push(col_inner_data);
                            }
                        }
                    } else {
                        if (columnObj.column_number_data === undefined) {
                            col_inner_data = data[j]._aData[column_number_filter];
                            if (typeof col_inner_data === 'object') {
                                if (columnObj.html5_data !== undefined) {
                                    col_inner_data = col_inner_data['@' + columnObj.html5_data];
                                } else {
                                    console.log('Warning: Looks like you have forgot to define the html5_data attribute for the ' + columnObj.column_number + ' column');
                                    return;
                                }
                            }
                        } else if (data[j]._aFilterData !== undefined && data[j]._aFilterData !== null) {
                            col_inner_data = data[j]._aFilterData[column_number_filter];
                        } else {
                            col_inner_data = dot2obj(data[j]._aData, columnObj.column_number_data);
                        }
                        if ($.trim(col_inner_data) !== '' && !(col_filter_array.hasOwnProperty(col_inner_data))) {
                            col_filter_array[col_inner_data] = col_inner_data;
                            column_data.push(col_inner_data);
                        }
                    }
                } else if (columnObj.column_data_type === "rendered_html") {
                    col_inner_elements = data[j]._aFilterData[column_number_filter];
                    if (typeof col_inner_elements !== 'string') {
                        col_inner_elements = $(col_inner_elements);
                        if (col_inner_elements.length > 0) {
                            for (k = 0; k < col_inner_elements.length; k++) {
                                switch (columnObj.html_data_type) {
                                    case "text":
                                        col_inner_data = $(col_inner_elements[k]).text();
                                        break;
                                    case "value":
                                        col_inner_data = $(col_inner_elements[k]).val();
                                        break;
                                    case "id":
                                        col_inner_data = col_inner_elements[k].id;
                                        break;
                                    case "selector":
                                        col_inner_data = $(col_inner_elements[k]).find(columnObj.html_data_selector).text();
                                        break;
                                }
                            }
                        } else {
                            col_inner_data = col_inner_elements.selector;
                        }
                    } else {
                        col_inner_data = col_inner_elements;
                    }
                    if ($.trim(col_inner_data) !== '' && !(col_filter_array.hasOwnProperty(col_inner_data))) {
                        col_filter_array[col_inner_data] = col_inner_data;
                        column_data.push(col_inner_data);
                    }
                }
            }
            columnObj.col_filter_array = col_filter_array;
            return column_data;
        }

        function appendFilters(oTable, args, table_selector, pSettings) {

            var i = 0,
                $filter_selector,
                filter_selector_string,

                data,
                filter_container_id,
                column_number_data,
                column_number,
                column_position,
                column_data_type,
                html_data_type,
                text_data_delimiter,
                filter_default_label,
                filter_reset_button_text,
                enable_auto_complete,
                sort_as,
                sort_order,
                date_format,
                ignore_char,
                filter_match_mode,

                column_data,
                column_data_temp,
                options_tmp,
                j,
                k,
                data_length,
                col_inner_elements,
                col_inner_data,
                ii,
                table_selector_jq_friendly,
                min_val,
                max_val,
                col_num_visible,
                col_num_visible_iter,
                tmpStr,
                columnObjKey,
                columnObj,
                filters_position,
                unique_th,
                settingsDt,
                filterActionStr,
                custom_func_filter_value_holder,
                exclude_str,
                tableDT,
                columnFilterVal;

            if (pSettings === undefined) {
                settingsDt = getSettingsObjFromTable(oTable);
            } else {
                settingsDt = pSettings;
            }
            settingsMap[generateTableSelectorJQFriendly2(oTable)] = settingsDt;

            table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(oTable);
            tableDT = tablesDT[table_selector_jq_friendly];

            initColReorder2(settingsDt, table_selector_jq_friendly);

            filters_position = $(document).data(table_selector + "_filters_position");
            if (settingsDt.oScroll.sX !== '' || settingsDt.oScroll.sY !== '') {
                table_selector = '.yadcf-datatables-table-' + table_selector_jq_friendly;
            }
            if (settingsDt.oApi._fnGetUniqueThs !== undefined) {
                unique_th = settingsDt.oApi._fnGetUniqueThs(settingsDt);
            }
            for (columnObjKey in args) {
                if (args.hasOwnProperty(columnObjKey)) {
                    columnObj = args[columnObjKey];

                    options_tmp = '';
                    tmpStr = '';
                    data = columnObj.data;
                    column_data = [];
                    column_data_temp = [];
                    filter_container_id = columnObj.filter_container_id;
                    column_number = columnObj.column_number;
                    column_number = +column_number;
                    column_position = column_number;

                    if (plugins[table_selector_jq_friendly] !== undefined && (plugins[table_selector_jq_friendly] !== undefined && plugins[table_selector_jq_friendly].ColReorder !== undefined)) {
                        column_position = plugins[table_selector_jq_friendly].ColReorder[column_number];
                    }

                    columnObj.column_number = column_number;
                    column_number_data = undefined;
                    if (isNaN(settingsDt.aoColumns[column_position].mData) && typeof settingsDt.aoColumns[column_position].mData !== 'object') {
                        column_number_data = settingsDt.aoColumns[column_position].mData;
                        columnObj.column_number_data = column_number_data;
                    }
                    if (isNaN(settingsDt.aoColumns[column_position].mRender) && typeof settingsDt.aoColumns[column_position].mRender !== 'object') {
                        columnObj.column_number_render = settingsDt.aoColumns[column_position].mRender;
                    }
                    column_data_type = columnObj.column_data_type;
                    html_data_type = columnObj.html_data_type;
                    text_data_delimiter = columnObj.text_data_delimiter;
                    filter_default_label = columnObj.filter_default_label;
                    filter_reset_button_text = columnObj.filter_reset_button_text;
                    enable_auto_complete = columnObj.enable_auto_complete;
                    sort_as = columnObj.sort_as;
                    sort_order = columnObj.sort_order;
                    date_format = columnObj.date_format;
                    if (columnObj.datepicker_type === 'jquery-ui') {
                        date_format = date_format.replace("yyyy", "yy");
                    }
                    if (columnObj.datepicker_type === 'bootstrap-datetimepicker' && columnObj.filter_plugin_options !== undefined && columnObj.filter_plugin_options.format !== undefined) {
                        date_format = columnObj.filter_plugin_options.format;
                    }
                    columnObj.date_format = date_format;

                    if (columnObj.ignore_char !== undefined && !(columnObj.ignore_char instanceof RegExp)) {
                        ignore_char = new RegExp(columnObj.ignore_char, "g");
                        columnObj.ignore_char = ignore_char;
                    }
                    filter_match_mode = columnObj.filter_match_mode;

                    if (column_number === undefined) {
                        alert("You must specify column number");
                        return;
                    }

                    if (enable_auto_complete === true) {
                        columnObj.filter_type = "auto_complete";
                    }

                    if (filter_default_label === undefined) {
                        if (columnObj.filter_type === "select" || columnObj.filter_type === 'custom_func') {
                            filter_default_label = placeholderLang.select;
                        } else if (columnObj.filter_type === "multi_select" || columnObj.filter_type === 'multi_select_custom_func') {
                            filter_default_label = placeholderLang.select_multi;
                        } else if (columnObj.filter_type === "auto_complete" || columnObj.filter_type === "text") {
                            filter_default_label = placeholderLang.filter;
                        } else if (columnObj.filter_type === "range_number" || columnObj.filter_type === "range_date") {
                            filter_default_label = placeholderLang.range;
                        } else if (columnObj.filter_type === "date") {
                            filter_default_label = placeholderLang.date;
                        } else if (columnObj.filter_type === "bap_date_range_picker") {
                            filter_default_label = placeholderLang.date;
                        } else if (columnObj.filter_type === "bap_date_picker") {
                            filter_default_label = placeholderLang.date;
                        }
                        columnObj.filter_default_label = filter_default_label;
                    }

                    if (filter_reset_button_text === undefined) {
                        filter_reset_button_text = "";
                    }

                    if (data !== undefined) {
                        for (ii = 0; ii < data.length; ii++) {
                            column_data.push(data[ii]);
                        }
                    }
                    if (data === undefined || columnObj.append_data_to_table_data !== undefined) {
                        columnObj.col_filter_array = undefined;
                        column_data_temp = parseTableColumn(oTable, columnObj, table_selector_jq_friendly, settingsDt);
                        if (columnObj.append_data_to_table_data !== 'before') {
                            column_data = column_data.concat(column_data_temp);
                        } else {
                            column_data_temp = sortColumnData(column_data_temp, columnObj);
                            column_data = column_data.concat(column_data_temp);
                        }
                    }

                    if (columnObj.append_data_to_table_data === undefined || columnObj.append_data_to_table_data === 'sorted') {
                        column_data = sortColumnData(column_data, columnObj);
                    }

                    if (columnObj.filter_type === "range_number_slider") {
                        min_val = findMinInArray(column_data, columnObj);
                        max_val = findMaxInArray(column_data, columnObj);
                    }

                    if (filter_container_id === undefined && columnObj.filter_container_selector === undefined) {
                        //Can't show filter inside a column for a hidden one (place it outside using filter_container_id)
                        if (settingsDt.aoColumns[column_position].bVisible === false) {
                            //console.log('Yadcf warning: Can\'t show filter inside a column N#' + column_number + ' for a hidden one (place it outside using filter_container_id)');
                            continue;
                        }

                        if (filters_position !== 'thead') {
                            if (unique_th === undefined) {
                                //handle hidden columns
                                col_num_visible = column_position;
                                for (col_num_visible_iter = 0; col_num_visible_iter < settingsDt.aoColumns.length && col_num_visible_iter < column_position; col_num_visible_iter++) {
                                    if (settingsDt.aoColumns[col_num_visible_iter].bVisible === false) {
                                        col_num_visible--;
                                    }
                                }
                                column_position = col_num_visible;
                                filter_selector_string = table_selector + ' ' + filters_position + ' th:eq(' + column_position + ')';
                            } else {
                                filter_selector_string = table_selector + ' ' + filters_position + ' th:eq(' + $(unique_th[column_position]).index() + ')';
                            }
                        } else {
                            if (columnObj.filters_tr_index === undefined) {
                                filter_selector_string = table_selector + ' ' + filters_position + ' tr:eq(' + $(unique_th[column_position]).parent().index() + ') th:eq(' + $(unique_th[column_position]).index() + ')';
                            } else {
                                filter_selector_string = table_selector + ' ' + filters_position + ' tr:eq(' + columnObj.filters_tr_index + ') th:eq(' + $(unique_th[column_position]).index() + ')';
                            }
                        }
                        $filter_selector = $(filter_selector_string).find(".yadcf-filter");
                    } else {
                        if (filter_container_id !== undefined) {
                            columnObj.filter_container_selector = "#" + filter_container_id;
                        }
                        if ($(columnObj.filter_container_selector).length === 0) {
                            console.log("ERROR: Filter container could not be found, columnObj.filter_container_selector: " + columnObj.filter_container_selector);
                            continue;
                        }
                        filter_selector_string = columnObj.filter_container_selector;
                        $filter_selector = $(filter_selector_string).find(".yadcf-filter");
                    }

                    if (columnObj.filter_type === "select" || columnObj.filter_type === 'custom_func' || columnObj.filter_type === "multi_select" || columnObj.filter_type === 'multi_select_custom_func') {
                        if (columnObj.data_as_is !== true) {
                            if (columnObj.omit_default_label !== true) {
                                if (columnObj.filter_type === "select" || columnObj.filter_type === 'custom_func') {
                                    options_tmp = "<option value=\"" + "-1" + "\">" + filter_default_label + "</option>";

                                    if (columnObj.select_type === 'select2' && columnObj.select_type_options.placeholder !== undefined && columnObj.select_type_options.allowClear === true) {
                                        options_tmp = "<option value=\"\"></option>";
                                    }
                                } else if (columnObj.filter_type === "multi_select" || columnObj.filter_type === 'multi_select_custom_func') {
                                    if (columnObj.select_type === undefined) {
                                        options_tmp = "<option data-placeholder=\"true\" value=\"" + "-1" + "\">" + filter_default_label + "</option>";
                                    } else {
                                        options_tmp = "";
                                    }
                                }
                            }

                            if (columnObj.append_data_to_table_data === undefined) {
                                if (typeof column_data[0] === 'object') {
                                    for (ii = 0; ii < column_data.length; ii++) {
                                        options_tmp += "<option value=\"" + (column_data[ii].value + '').replace(/"/g, '&quot;') + "\">" + column_data[ii].label + "</option>";
                                    }
                                } else {
                                    for (ii = 0; ii < column_data.length; ii++) {
                                        options_tmp += "<option value=\"" + (column_data[ii] + '').replace(/"/g, '&quot;') + "\">" + column_data[ii] + "</option>";
                                    }
                                }
                            } else {
                                for (ii = 0; ii < column_data.length; ii++) {
                                    if (typeof column_data[ii] === 'object') {
                                        options_tmp += "<option value=\"" + (column_data[ii].value + '').replace(/"/g, '&quot;') + "\">" + column_data[ii].label + "</option>";
                                    } else {
                                        options_tmp += "<option value=\"" + (column_data[ii] + '').replace(/"/g, '&quot;') + "\">" + column_data[ii] + "</option>";
                                    }
                                }
                            }
                        } else {
                            options_tmp = columnObj.data;
                        }
                        column_data = options_tmp;
                    }
                    if ($filter_selector.length === 1) {
                        if (columnObj.filter_type === "select" || columnObj.filter_type === "multi_select" || columnObj.filter_type === 'custom_func' || columnObj.filter_type === 'multi_select_custom_func') {
                            if (columnObj.filter_type === 'custom_func' || columnObj.filter_type === 'multi_select_custom_func') {
                                custom_func_filter_value_holder = $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val();
                            }
                            $filter_selector.empty();
                            $filter_selector.append(column_data);
                            if (settingsDt.aoPreSearchCols[column_position].sSearch !== '') {
                                tmpStr = settingsDt.aoPreSearchCols[column_position].sSearch;
                                if (columnObj.filter_type === "select") {
                                    tmpStr = yadcfParseMatchFilter(tmpStr, getOptions(oTable.selector)[column_number].filter_match_mode);
                                    $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr).addClass("inuse");
                                } else if (columnObj.filter_type === "multi_select") {
                                    tmpStr = yadcfParseMatchFilterMultiSelect(tmpStr, getOptions(oTable.selector)[column_number].filter_match_mode);
                                    tmpStr = tmpStr.replace(/\\/g, "");
                                    tmpStr = tmpStr.split("|");
                                    $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr);
                                }
                            }
                            if (columnObj.filter_type === 'custom_func' || columnObj.filter_type === 'multi_select_custom_func') {
                                tmpStr = custom_func_filter_value_holder;
                                if (tmpStr === '-1' || tmpStr === undefined) {
                                    $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr);
                                } else {
                                    $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr).addClass("inuse");
                                }
                            }

                            initializeSelectPlugin(columnObj.select_type, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number), columnObj.select_type_options);
                            if (columnObj.cumulative_filtering === true && columnObj.select_type === 'chosen') {
                                refreshSelectPlugin(columnObj, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number));
                            }
                        } else if (columnObj.filter_type === "auto_complete") {
                            $(document).data("yadcf-filter-" + table_selector_jq_friendly + "-" + column_number, column_data);
                        }
                    } else {
                        if (filter_container_id === undefined && columnObj.filter_container_selector === undefined) {
                            if ($(filter_selector_string + " div.DataTables_sort_wrapper").length > 0) {
                                $(filter_selector_string + " div.DataTables_sort_wrapper").css("display", "inline-block");
                            }
                        } else {
                            if (filter_container_id !== undefined) {
                                columnObj.filter_container_selector = "#" + filter_container_id;
                            }
                            if ($("#yadcf-filter-wrapper-" + columnObj.filter_container_selector).length === 0) {
                                $(columnObj.filter_container_selector).append("<div id=\"yadcf-filter-wrapper-" + generateTableSelectorJQFriendlyNew(columnObj.filter_container_selector) + "\"></div>");
                            }
                            filter_selector_string = "#yadcf-filter-wrapper-" + generateTableSelectorJQFriendlyNew(columnObj.filter_container_selector);
                        }

                        if (columnObj.filter_type === "select" || columnObj.filter_type === 'custom_func') {

                            //add a wrapper to hold both filter and reset button
                            $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
                            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";

                            if (columnObj.filter_type === "select") {
                                filterActionStr = 'onchange="yadcf.doFilter(this, \'' + table_selector_jq_friendly + '\', ' + column_number + ', \'' + filter_match_mode + '\');"';
                                if (columnObj.externally_triggered === true) {
                                    filterActionStr = '';
                                }
                                $(filter_selector_string).append("<select id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"yadcf-filter form-control " + columnObj.style_class + "\" " +
                                    filterActionStr + " onkeydown=\"yadcf.preventDefaultForEnter(event);\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);'>" + column_data + "</select>");
                                // if (filter_reset_button_text !== false) {
                                //     $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" " +
                                //         "id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "-reset\" onmousedown=\"yadcf.stopPropagation(event);\" onclick=\"yadcf.stopPropagation(event);yadcf.doFilter('clear', '" + table_selector_jq_friendly + "', " + column_number + "); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
                                // }
                            } else {
                                filterActionStr = 'onchange="yadcf.doFilterCustomDateFunc(this, \'' + table_selector_jq_friendly + '\', ' + column_number + ');"';
                                if (columnObj.externally_triggered === true) {
                                    filterActionStr = '';
                                }
                                $(filter_selector_string).append("<select id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"yadcf-filter " + columnObj.style_class + "\" " +
                                    filterActionStr + " onkeydown=\"yadcf.preventDefaultForEnter(event);\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);'>" + column_data + "</select>");
                                // if (filter_reset_button_text !== false) {
                                //     $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                                //         "onclick=\"yadcf.stopPropagation(event);yadcf.doFilterCustomDateFunc('clear', '" + table_selector_jq_friendly + "', " + column_number + "); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
                                // }

                                if (settingsDt.oFeatures.bStateSave === true && settingsDt.oLoadedState) {
                                    if (settingsDt.oLoadedState.yadcfState && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly] && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number]) {
                                        tmpStr = settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from;
                                        if (tmpStr === '-1' || tmpStr === undefined) {
                                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr);
                                        } else {
                                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr).addClass("inuse");
                                        }
                                    }
                                }
                                if (settingsDt.oFeatures.bServerSide !== true) {
                                    addCustomFunctionFilterCapability(table_selector_jq_friendly, "yadcf-filter-" + table_selector_jq_friendly + "-" + column_number, column_number);
                                }
                            }

                            if (settingsDt.aoPreSearchCols[column_position].sSearch !== '') {
                                tmpStr = settingsDt.aoPreSearchCols[column_position].sSearch;
                                tmpStr = yadcfParseMatchFilter(tmpStr, getOptions(oTable.selector)[column_number].filter_match_mode);
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr).addClass("inuse");
                            }

                            if (columnObj.select_type !== undefined) {
                                initializeSelectPlugin(columnObj.select_type, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number), columnObj.select_type_options);
                                if (columnObj.cumulative_filtering === true && columnObj.select_type === 'chosen') {
                                    refreshSelectPlugin(columnObj, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number));
                                }
                            }

                        } else if (columnObj.filter_type === "multi_select" || columnObj.filter_type === 'multi_select_custom_func') {

                            //add a wrapper to hold both filter and reset button
                            $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
                            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";

                            if (columnObj.filter_type === "multi_select") {
                                filterActionStr = 'onchange="yadcf.doFilterMultiSelect(this, \'' + table_selector_jq_friendly + '\', ' + column_number + ', \'' + filter_match_mode + '\');"';
                                if (columnObj.externally_triggered === true) {
                                    filterActionStr = '';
                                }
                                $(filter_selector_string).append("<select multiple data-placeholder=\"" + filter_default_label + "\" id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"yadcf-filter form-control " + columnObj.style_class + "\" " +
                                    filterActionStr + " onkeydown=\"yadcf.preventDefaultForEnter(event);\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);'>" + column_data + "</select>");

                                // if (filter_reset_button_text !== false) {
                                //     $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                                //         "onclick=\"yadcf.stopPropagation(event);yadcf.doFilter('clear', '" + table_selector_jq_friendly + "', " + column_number + "); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
                                // }

                                if (settingsDt.aoPreSearchCols[column_position].sSearch !== '') {
                                    tmpStr = settingsDt.aoPreSearchCols[column_position].sSearch;
                                    tmpStr = yadcfParseMatchFilterMultiSelect(tmpStr, getOptions(oTable.selector)[column_number].filter_match_mode);
                                    tmpStr = tmpStr.replace(/\\/g, "");
                                    tmpStr = tmpStr.split("|");
                                    $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr);
                                }
                            } else {
                                filterActionStr = 'onchange="yadcf.doFilterCustomDateFunc(this, \'' + table_selector_jq_friendly + '\', ' + column_number + ');"';
                                if (columnObj.externally_triggered === true) {
                                    filterActionStr = '';
                                }
                                $(filter_selector_string).append("<select multiple data-placeholder=\"" + filter_default_label + "\" id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"yadcf-filter form-control " + columnObj.style_class + "\" " +
                                    filterActionStr + " onkeydown=\"yadcf.preventDefaultForEnter(event);\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);'>" + column_data + "</select>");

                                // if (filter_reset_button_text !== false) {
                                //     $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                                //         "onclick=\"yadcf.stopPropagation(event);yadcf.doFilterCustomDateFunc('clear', '" + table_selector_jq_friendly + "', " + column_number + "); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
                                // }

                                if (settingsDt.oFeatures.bStateSave === true && settingsDt.oLoadedState) {
                                    if (settingsDt.oLoadedState.yadcfState && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly] && settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number]) {
                                        tmpStr = settingsDt.oLoadedState.yadcfState[table_selector_jq_friendly][column_number].from;
                                        if (tmpStr === '-1' || tmpStr === undefined) {
                                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr);
                                        } else {
                                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr).addClass("inuse");
                                        }
                                    }
                                }
                                if (settingsDt.oFeatures.bServerSide !== true) {
                                    addCustomFunctionFilterCapability(table_selector_jq_friendly, "yadcf-filter-" + table_selector_jq_friendly + "-" + column_number, column_number);
                                }
                            }

                            if (columnObj.filter_container_selector === undefined && columnObj.select_type_options.width === undefined) {
                                columnObj.select_type_options = $.extend(columnObj.select_type_options, {width: $(filter_selector_string).closest("th").width() + "px"});
                            }
                            if (columnObj.filter_container_selector !== undefined && columnObj.select_type_options.width === undefined) {
                                columnObj.select_type_options = $.extend(columnObj.select_type_options, {width: $(filter_selector_string).closest(columnObj.filter_container_selector).width() + "px"});
                            }

                            if (columnObj.select_type !== undefined) {
                                initializeSelectPlugin(columnObj.select_type, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number), columnObj.select_type_options);
                                if (columnObj.cumulative_filtering === true && columnObj.select_type === 'chosen') {
                                    refreshSelectPlugin(columnObj, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number));
                                }
                            }

                        } else if (columnObj.filter_type === "auto_complete") {

                            //add a wrapper to hold both filter and reset button
                            $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
                            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";

                            filterActionStr = 'onkeyup="yadcf.autocompleteKeyUP(\'' + table_selector_jq_friendly + '\',event);"';
                            if (columnObj.externally_triggered === true) {
                                filterActionStr = '';
                            }
                            $(filter_selector_string).append("<input onkeydown=\"yadcf.preventDefaultForEnter(event);\" id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"yadcf-filter\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);" +
                                "' placeholder='" + filter_default_label + "'" + " filter_match_mode='" + filter_match_mode + "' " + filterActionStr + "></input>");
                            $(document).data("yadcf-filter-" + table_selector_jq_friendly + "-" + column_number, column_data);

                            // if (filter_reset_button_text !== false) {
                            //     $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                            //         "onclick=\"yadcf.stopPropagation(event);yadcf.doFilterAutocomplete('clear', '" + table_selector_jq_friendly + "', " + column_number + "); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
                            // }

                        } else if (columnObj.filter_type === "text") {

                            //add a wrapper to hold both filter and reset button
                            $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-" + table_selector_jq_friendly + "-" + column_number + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
                            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";

                            filterActionStr = 'onkeyup="yadcf.textKeyUP(event,\'' + table_selector_jq_friendly + '\', ' + column_number + ');"';
                            if (columnObj.externally_triggered === true) {
                                filterActionStr = '';
                            }

                            exclude_str = '';
                            if (columnObj.exclude === true) {
                                if (columnObj.externally_triggered !== true) {
                                    exclude_str = '<span class="yadcf-exclude-wrapper" onmousedown="yadcf.stopPropagation(event);" onclick="yadcf.stopPropagation(event);">' +
                                        '<div class="yadcf-label small">' + columnObj.exclude_label + '</div><input type="checkbox" title="' + columnObj.exclude_label + '" onclick="yadcf.stopPropagation(event);yadcf.textKeyUP(event,\'' + table_selector_jq_friendly + '\',' + column_number + ');"></span>';
                                } else {
                                    exclude_str = '<span class="yadcf-exclude-wrapper" onmousedown="yadcf.stopPropagation(event);" onclick="yadcf.stopPropagation(event);">' +
                                        '<div class="yadcf-label small">' + columnObj.exclude_label + '</div><input type="checkbox" title="' + columnObj.exclude_label + '" onclick="yadcf.stopPropagation(event);"></span>';
                                }
                            }

                            $(filter_selector_string).append(exclude_str + "<input type=\"text\" onkeydown=\"yadcf.preventDefaultForEnter(event);\" id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "\" style=\"width:100%\" class=\"yadcf-filter form-control" + columnObj.style_class + "\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);" +
                                "' placeholder='" + filter_default_label + "'" + " filter_match_mode='" + filter_match_mode + "' " + filterActionStr + "></input>");

                            // if (filter_reset_button_text !== false) {
                            //     $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" " + " id=\"yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "-reset\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                            //         "onclick=\"yadcf.stopPropagation(event);yadcf.textKeyUP(event,'" + table_selector_jq_friendly + "', '" + column_number + "', 'clear'); return false;\" class=\"yadcf-filter-reset-button " + columnObj.reset_button_style_class + "\">" + filter_reset_button_text + "</button>");
                            // }

                            if (settingsDt.aoPreSearchCols[column_position].sSearch !== '') {
                                tmpStr = settingsDt.aoPreSearchCols[column_position].sSearch;
                                if (columnObj.exclude === true) {
                                    if (tmpStr.indexOf('^((?!') !== -1) {
                                        $('#yadcf-filter-wrapper-' + table_selector_jq_friendly + '-' + column_number).find(':checkbox').prop('checked', true);
                                    }
                                    tmpStr = tmpStr.substring(5, tmpStr.indexOf(').)'));
                                }
                                tmpStr = yadcfParseMatchFilter(tmpStr, getOptions(oTable.selector)[column_number].filter_match_mode);
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr).addClass("inuse");
                            }

                        } else if (columnObj.filter_type === "bap_date_range_picker") {

                            addDateFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, date_format);

                        } else if (columnObj.filter_type === "bap_date_picker") {

                            addDateFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, date_format);

                        } else if (columnObj.filter_type === "date") {

                            addDateFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, date_format);

                        } else if (columnObj.filter_type === "range_number") {

                            addRangeNumberFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, ignore_char);

                        } else if (columnObj.filter_type === "range_number_slider") {

                            addRangeNumberSliderFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, min_val, max_val, ignore_char);

                        } else if (columnObj.filter_type === "range_date") {

                            addRangeDateFilter(filter_selector_string, table_selector_jq_friendly, column_number, filter_reset_button_text, filter_default_label, date_format);

                        }
                    }

                    if ($(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val") !== undefined && $(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val") !== "-1") {
                        $(filter_selector_string).find(".yadcf-filter").val($(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val"));
                    }
                    if (columnObj.filter_type === "auto_complete") {
                        columnObj.filter_plugin_options = {
                            source: $(document).data("yadcf-filter-" + table_selector_jq_friendly + "-" + column_number),
                            select: autocompleteSelect
                        };
                        if (columnObj.externally_triggered === true) {
                            delete columnObj.filter_plugin_options.select;
                        }
                        $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).autocomplete(columnObj.filter_plugin_options);
                        if (settingsDt.aoPreSearchCols[column_position].sSearch !== '') {
                            tmpStr = settingsDt.aoPreSearchCols[column_position].sSearch;
                            tmpStr = yadcfParseMatchFilter(tmpStr, getOptions(oTable.selector)[column_number].filter_match_mode);
                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(tmpStr).addClass("inuse");
                        }
                    }
                }
            }
            if (exFilterColumnQueue.length > 0) {
                (exFilterColumnQueue.shift())();
            }
        }

        function rangeClear(table_selector_jq_friendly, event, column_number) {
            event = eventTargetFixUp(event);
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            var oTable = oTables[table_selector_jq_friendly],
                yadcfState,
                settingsDt,
                column_number_filter,
                currentFilterValues,
                columnObj,
                dateRange;

            settingsDt = getSettingsObjFromTable(oTable);

            column_number_filter = calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly);

            currentFilterValues = exGetColumnFilterVal(oTable, column_number);
            if (currentFilterValues.from === '' && currentFilterValues.to === '') {
                return;
            }

            columnObj = getOptions(oTable.selector)[column_number];

            $(event.target).parent().find(".yadcf-filter-range").val("");
            dateRange = $(event.target).parent().find(".yadcf-filter-range-date");
            if (dateRange.length > 1) {
                if (columnObj.datepicker_type === 'jquery-ui') {
                    $(dateRange[0]).datepicker('option', 'maxDate', null);
                    $(dateRange[1]).datepicker('option', 'minDate', null);
                }
            }
            if ($(event.target).parent().find(".yadcf-filter-range-number").length > 0) {
                $($(event.target).parent().find(".yadcf-filter-range")[0]).focus();
            }

            if (oTable.fnSettings().oFeatures.bServerSide !== true) {
                oTable.fnDraw();
            } else {
                oTable.fnFilter('-yadcf_delim-', column_number_filter);
            }

            if (!oTable.fnSettings().oLoadedState) {
                oTable.fnSettings().oLoadedState = {};
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }
            if (oTable.fnSettings().oFeatures.bStateSave === true) {
                if (oTable.fnSettings().oLoadedState.yadcfState !== undefined && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] !== undefined) {
                    oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number] =
                        {
                            'from': "",
                            'to': ""
                        };
                } else {
                    yadcfState = {};
                    yadcfState[table_selector_jq_friendly] = [];
                    yadcfState[table_selector_jq_friendly][column_number] = {
                        'from': "",
                        'to': ""
                    };
                    oTable.fnSettings().oLoadedState.yadcfState = yadcfState;
                }
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }
            resetIApiIndex();

            $(event.target).parent().find(".yadcf-filter-range").removeClass("inuse");

            return;
        }

        function rangeNumberSliderClear(table_selector_jq_friendly, event) {
            event = eventTargetFixUp(event);
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            var oTable = oTables[table_selector_jq_friendly],
                min_val,
                max_val,
                currentFilterValues,
                column_number;

            column_number = parseInt($(event.target).prev().find(".yadcf-filter-range-number-slider").attr("id").replace("yadcf-filter-" + table_selector_jq_friendly + "-slider-", ""), 10);

            min_val = +$($(event.target).parent().find(".yadcf-filter-range-number-slider-min-tip-hidden")).text();
            max_val = +$($(event.target).parent().find(".yadcf-filter-range-number-slider-max-tip-hidden")).text();

            currentFilterValues = exGetColumnFilterVal(oTable, column_number);
            if (+currentFilterValues.from === min_val && +currentFilterValues.to === max_val) {
                return;
            }

            $(event.target).prev().find(".yadcf-filter-range-number-slider").slider("option", "yadcf-reset", true);
            $(event.target).prev().find(".yadcf-filter-range-number-slider").slider("option", "values", [min_val, max_val]);

            $($(event.target).prev().find(".ui-slider-handle")[0]).attr("tabindex", -1).focus();

            $($(event.target).prev().find(".ui-slider-handle")[0]).removeClass("inuse");
            $($(event.target).prev().find(".ui-slider-handle")[1]).removeClass("inuse");
            $(event.target).prev().find(".ui-slider-range").removeClass("inuse");

            oTable.fnDraw();
            resetIApiIndex();

            return;
        }

        function dateKeyUP(table_selector_jq_friendly, date_format, event) {
            var oTable,
                date,
                dateId,
                column_number,
                columnObj;

            event = eventTargetFixUp(event);

            dateId = event.target.id;
            date = document.getElementById(dateId).value;

            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            oTable = oTables[table_selector_jq_friendly];
            column_number = parseInt(dateId.replace("yadcf-filter-" + table_selector_jq_friendly + "-", ""), 10);
            columnObj = getOptions(oTable.selector)[column_number];

            try {
                if (columnObj.datepicker_type === 'jquery-ui') {
                    if (date.length === (date_format.length + 2)) {
                        date = (date !== "") ? $.datepicker.parseDate(date_format, date) : date;
                    }
                }
            } catch (err1) {
            }

            if (date instanceof Date || moment(date, columnObj.date_format).isValid()) {
                $("#" + dateId).addClass('inuse');
                oTable.fnFilter(document.getElementById(dateId).value, column_number);
                resetIApiIndex();
            } else if (date === "" || $.trim(event.target.value) === '') {
                $("#" + dateId).removeClass('inuse');
                $('#' + event.target.id).removeClass('inuse');
                oTable.fnFilter('', column_number);
                resetIApiIndex();
            }
        }

        function rangeDateKeyUP(table_selector_jq_friendly, date_format, event) {
            event = eventTargetFixUp(event);
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            var oTable = oTables[table_selector_jq_friendly],
                min,
                max,
                fromId,
                toId,
                column_number,
                columnObj,
                keyUp,
                settingsDt,
                column_number_filter;

            column_number = parseInt($(event.target).attr("id").replace('-from-date-', '').replace('-to-date-', '').replace('yadcf-filter-' + table_selector_jq_friendly, ''), 10);
            columnObj = getOptions(oTable.selector)[column_number];
            settingsDt = getSettingsObjFromTable(oTable);
            column_number_filter = calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly);

            keyUp = function () {
                if (event.target.id.indexOf("-from-") !== -1) {
                    fromId = event.target.id;
                    toId = event.target.id.replace("-from-", "-to-");

                    min = document.getElementById(fromId).value;
                    max = document.getElementById(toId).value;
                } else {
                    toId = event.target.id;
                    fromId = event.target.id.replace("-to-", "-from-");

                    max = document.getElementById(toId).value;
                    min = document.getElementById(fromId).value;
                }

                //moment(min, "YYYY-MM-DD hh:mm:ss").toDate() typeof Date

                if (columnObj.datepicker_type === 'jquery-ui') {
                    try {
                        if (min.length === (date_format.length + 2)) {
                            min = (min !== "") ? $.datepicker.parseDate(date_format, min) : min;
                        }
                    } catch (err1) {
                    }
                    try {
                        if (max.length === (date_format.length + 2)) {
                            max = (max !== "") ? $.datepicker.parseDate(date_format, max) : max;
                        }
                    } catch (err2) {
                    }
                } else if (columnObj.datepicker_type === 'bootstrap-datetimepicker') {
                    try {
                        min = moment(min, columnObj.moment_date_format).toDate();
                        if (isNaN(min.getTime())) {
                            min = '';
                        }
                    } catch (err3) {
                    }
                    try {
                        max = moment(max, columnObj.moment_date_format).toDate();
                        if (isNaN(max.getTime())) {
                            max = '';
                        }
                    } catch (err4) {
                    }
                }

                if (((max instanceof Date) && (min instanceof Date) && (max >= min)) || min === "" || max === "") {

                    if (oTable.fnSettings().oFeatures.bServerSide !== true) {
                        oTable.fnDraw();
                    } else {
                        oTable.fnFilter(document.getElementById(fromId).value + '-yadcf_delim-' + document.getElementById(toId).value, column_number_filter);
                    }

                    if (min instanceof Date) {
                        $("#" + fromId).addClass("inuse");
                    } else {
                        $("#" + fromId).removeClass("inuse");
                    }
                    if (max instanceof Date) {
                        $("#" + toId).addClass("inuse");
                    } else {
                        $("#" + toId).removeClass("inuse");
                    }

                    if ($.trim(event.target.value) === "" && $(event.target).hasClass("inuse")) {
                        $("#" + event.target.id).removeClass("inuse");
                    }

                }
                resetIApiIndex();
            };

            if (columnObj.filter_delay === undefined) {
                keyUp(table_selector_jq_friendly, event);
            } else {
                yadcfDelay(function () {
                    keyUp(table_selector_jq_friendly, event);
                }, columnObj.filter_delay);
            }
        }

        function rangeNumberKeyUP(table_selector_jq_friendly, event) {
            event = eventTargetFixUp(event);
            $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
            var oTable = oTables[table_selector_jq_friendly],
                min,
                max,
                fromId,
                toId,
                yadcfState,
                column_number,
                options,
                keyUp;

            column_number = parseInt($(event.target).attr("id").replace('-from-', '').replace('-to-', '').replace('yadcf-filter-' + table_selector_jq_friendly, ''), 10);
            options = getOptions(oTable.selector)[column_number];

            keyUp = function () {

                if (event.target.id.indexOf("-from-") !== -1) {
                    fromId = event.target.id;
                    toId = event.target.id.replace("-from-", "-to-");

                    min = document.getElementById(fromId).value;
                    max = document.getElementById(toId).value;
                } else {
                    toId = event.target.id;
                    fromId = event.target.id.replace("-to-", "-from-");

                    max = document.getElementById(toId).value;
                    min = document.getElementById(fromId).value;
                }

                min = (min !== "") ? (+min) : min;
                max = (max !== "") ? (+max) : max;

                if ((!isNaN(max) && !isNaN(min) && (max >= min)) || min === "" || max === "") {

                    if (oTable.fnSettings().oFeatures.bServerSide !== true) {
                        oTable.fnDraw();
                    } else {
                        oTable.fnFilter(min + '-yadcf_delim-' + max, column_number);
                    }
                    if (document.getElementById(fromId).value !== "") {
                        $("#" + fromId).addClass("inuse");
                    }
                    if (document.getElementById(toId).value !== "") {
                        $("#" + toId).addClass("inuse");
                    }

                    if ($.trim(event.target.value) === "" && $(event.target).hasClass("inuse")) {
                        $("#" + event.target.id).removeClass("inuse");
                    }
                    if (!oTable.fnSettings().oLoadedState) {
                        oTable.fnSettings().oLoadedState = {};
                        oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
                    }
                    if (oTable.fnSettings().oFeatures.bStateSave === true) {
                        if (oTable.fnSettings().oLoadedState.yadcfState !== undefined && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] !== undefined) {
                            oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number] =
                                {
                                    'from': min,
                                    'to': max
                                };
                        } else {
                            yadcfState = {};
                            yadcfState[table_selector_jq_friendly] = [];
                            yadcfState[table_selector_jq_friendly][column_number] = {
                                'from': min,
                                'to': max
                            };
                            oTable.fnSettings().oLoadedState.yadcfState = yadcfState;
                        }
                        oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
                    }
                }
                resetIApiIndex();
            };

            if (options.filter_delay === undefined) {
                keyUp();
            } else {
                yadcfDelay(function () {
                    keyUp();
                }, options.filter_delay);
            }
        }

        function doFilterMultiTablesMultiSelect(tablesSelectors, event, column_number_str, clear) {

            var columnsObj = getOptions(tablesSelectors + '_' + column_number_str)[column_number_str],
                regex = false,
                smart = true,
                caseInsen = true,
                tablesAsOne,
                tablesArray = oTables[tablesSelectors],
                selected_values = $(event.target).val(),
                i;

            event = eventTargetFixUp(event);
            tablesAsOne = new $.fn.dataTable.Api(tablesArray);

            if (clear !== undefined || selected_values == undefined || selected_values.length === 0) {
                if (clear !== undefined) {
                    $(event.target).parent().find('select').val('-1').focus();
                    $(event.target).parent().find('selectn ').removeClass("inuse");
                }
                if (columnsObj.column_number instanceof Array) {
                    tablesAsOne.columns(columnsObj.column_number).search('').draw();
                } else {
                    tablesAsOne.search('').draw();
                }

                refreshSelectPlugin(columnsObj, $('#' + columnsObj.filter_container_id + ' select'), '-1');
                return;
            }

            $(event.target).addClass("inuse");

            regex = true;
            smart = false;
            caseInsen = columnsObj.case_insensitive;

            if (selected_values !== null) {
                for (i = selected_values.length - 1; i >= 0; i--) {
                    if (selected_values[i] === "-1") {
                        selected_values.splice(i, 1);
                        break;
                    }
                }
                if (selected_values.length !== 0) {
                    selected_values = selected_values.join('narutouzomaki');
                    selected_values = selected_values.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
                    selected_values = selected_values.split('narutouzomaki').join('|');
                }
            }
            if (columnsObj.filter_match_mode === "exact") {
                selected_values = "^" + selected_values + "$";
            } else if (columnsObj.filter_match_mode === "startsWith") {
                selected_values = "^" + selected_values;
            }
            if (columnsObj.column_number instanceof Array) {
                tablesAsOne.columns(columnsObj.column_number).search(selected_values, regex, smart, caseInsen).draw();
            } else {
                tablesAsOne.search(selected_values, regex, smart, caseInsen).draw();
            }
        }

        function doFilterMultiTables(tablesSelectors, event, column_number_str, clear) {

            var columnsObj = getOptions(tablesSelectors + '_' + column_number_str)[column_number_str],
                regex = false,
                smart = true,
                caseInsen = true,
                serachVal,
                tablesAsOne,
                tablesArray = oTables[tablesSelectors];

            event = eventTargetFixUp(event);
            tablesAsOne = new $.fn.dataTable.Api(tablesArray);

            if (clear !== undefined || event.target.value === '-1') {
                if (clear !== undefined) {
                    $(event.target).parent().find('select').val('-1').focus();
                    $(event.target).parent().find('select').removeClass("inuse");
                }
                if (columnsObj.column_number instanceof Array) {
                    tablesAsOne.columns(columnsObj.column_number).search('').draw();
                } else {
                    tablesAsOne.search('').draw();
                }

                refreshSelectPlugin(columnsObj, $('#' + columnsObj.filter_container_id + ' select'), '-1');

                return;
            }

            $(event.target).addClass("inuse");

            serachVal = event.target.value;
            smart = false;
            caseInsen = columnsObj.case_insensitive;
            /*
			if (columnsObj.filter_match_mode === "contains") {
				regex = false;
			} else if (columnsObj.filter_match_mode === "exact") {
				regex = true;
				serachVal = "^" + serachVal + "$";
			} else if (columnsObj.filter_match_mode === "startsWith") {
				regex = true;
				serachVal = "^" + serachVal;
			}*/
            if (columnsObj.column_number instanceof Array) {
                tablesAsOne.columns(columnsObj.column_number).search(serachVal, regex, smart, caseInsen).draw();
            } else {
                tablesAsOne.search(serachVal, regex, smart, caseInsen).draw();
            }
        }

        function textKeyUpMultiTables(tablesSelectors, event, column_number_str, clear) {

            var keyUp,
                columnsObj = getOptions(tablesSelectors + '_' + column_number_str)[column_number_str],
                regex = false,
                smart = true,
                caseInsen = true,
                serachVal,
                tablesAsOne,
                tablesArray = oTables[tablesSelectors];

            event = eventTargetFixUp(event);
            tablesAsOne = new $.fn.dataTable.Api(tablesArray);

            keyUp = function (tablesAsOne, event, clear) {

                if (clear !== undefined || event.target.value === '') {
                    if (clear !== undefined) {
                        $(event.target).prev().val("").focus();
                        $(event.target).prev().removeClass("inuse");
                    } else {
                        $(event.target).val("").focus();
                        $(event.target).removeClass("inuse");
                    }
                    if (columnsObj.column_number instanceof Array) {
                        tablesAsOne.columns(columnsObj.column_number).search('').draw();
                    } else {
                        tablesAsOne.search('').draw();
                    }
                    return;
                }

                $(event.target).addClass("inuse");

                serachVal = event.target.value;
                smart = false;
                caseInsen = columnsObj.case_insensitive;
                /*
				if (columnsObj.filter_match_mode === "contains") {
					regex = false;
				} else if (columnsObj.filter_match_mode === "exact") {
					regex = true;
					serachVal = "^" + serachVal + "$";
				} else if (columnsObj.filter_match_mode === "startsWith") {
					regex = true;
					serachVal = "^" + serachVal;
				}
	*/
                if (columnsObj.column_number instanceof Array) {
                    tablesAsOne.columns(columnsObj.column_number).search(serachVal, regex, smart, caseInsen).draw();
                } else {
                    tablesAsOne.search(serachVal, regex, smart, caseInsen).draw();
                }

            };

            if (columnsObj.filter_delay === undefined) {
                keyUp(tablesAsOne, event, clear);
            } else {
                yadcfDelay(function () {
                    keyUp(tablesAsOne, event, clear);
                }, columnsObj.filter_delay);
            }
        }

        function textKeyUP(ev, table_selector_jq_friendly, column_number, clear) {
            var column_number_filter,
                oTable = oTables[table_selector_jq_friendly],
                keyUp,
                columnObj,
                settingsDt = getSettingsObjFromTable(oTable),
                exclude,
                keyCodes = [37, 38, 39, 40];

            if (keyCodes.indexOf(ev.keyCode) !== -1) {
                return;
            }
            column_number_filter = calcColumnNumberFilter(settingsDt, column_number, table_selector_jq_friendly);

            columnObj = getOptions(oTable.selector)[column_number];

            keyUp = function (table_selector_jq_friendly, column_number, clear) {
                $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];

                if (clear === 'clear' || $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).val() === '') {
                    if (clear === 'clear' && exGetColumnFilterVal(oTable, column_number) === '') {
                        return;
                    }
                    $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).val("").focus();
                    $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).removeClass("inuse");
                    oTable.fnFilter("", column_number_filter);
                    resetIApiIndex();
                    return;
                }

                if (columnObj.exclude === true) {
                    exclude = $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).closest('.yadcf-filter-wrapper').find('.yadcf-exclude-wrapper :checkbox').prop('checked');
                }
                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).addClass("inuse");

                yadcfMatchFilter(oTable, $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).val(), columnObj.filter_match_mode, column_number_filter, exclude);

                resetIApiIndex();
            };

            if (columnObj.filter_delay === undefined) {
                keyUp(table_selector_jq_friendly, column_number, clear);
            } else {
                yadcfDelay(function () {
                    keyUp(table_selector_jq_friendly, column_number, clear);
                }, columnObj.filter_delay);
            }
        }

        function autocompleteKeyUP(table_selector_jq_friendly, event) {
            var oTable,
                column_number,
                keyCodes = [37, 38, 39, 40];

            event = eventTargetFixUp(event);

            if (keyCodes.indexOf(event.keyCode) !== -1) {
                return;
            }

            if (event.target.value === "" && event.keyCode === 8 && $(event.target).hasClass("inuse")) {
                $.fn.dataTableExt.iApiIndex = oTablesIndex[table_selector_jq_friendly];
                oTable = oTables[table_selector_jq_friendly];
                column_number = parseInt($(event.target).attr("id").replace("yadcf-filter-" + table_selector_jq_friendly + "-", ""), 10);

                $("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number).removeClass("inuse");
                $(document).removeData("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val");
                oTable.fnFilter("", column_number);
                resetIApiIndex();
            }
        }

        function isDOMSource(tableVar) {
            var settingsDt;
            settingsDt = getSettingsObjFromTable(tableVar);
            if (settingsDt.sAjaxSource == undefined && settingsDt.ajax == undefined && settingsDt.oFeatures.bServerSide !== true) {
                return true;
            }
            return false;
        }

        function scrollXYHandler(oTable, table_selector) {
            var $tmpSelector,
                filters_position = $(document).data(table_selector + "_filters_position"),
                table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(oTable);

            if (filters_position === 'thead') {
                filters_position = '.dataTables_scrollHead';
            } else {
                filters_position = '.dataTables_scrollFoot';
            }
            if (oTable.fnSettings().oScroll.sX !== '' || oTable.fnSettings().oScroll.sY !== '') {
                $tmpSelector = $(table_selector).closest('.dataTables_scroll').find(filters_position + ' table');
                $tmpSelector.addClass('yadcf-datatables-table-' + table_selector_jq_friendly);
            }
        }

        function firstFromObject(obj) {
            var key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) {
                    return key;
                }
            }
        }

        function initAndBindTable(oTable, table_selector, index, pTableDT) {

            var table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(oTable),
                table_selector_tmp;
            oTables[table_selector_jq_friendly] = oTable;
            tablesDT[table_selector_jq_friendly] = pTableDT;
            oTablesIndex[table_selector_jq_friendly] = index;

            scrollXYHandler(oTable, table_selector);

            if (isDOMSource(oTable)) {
                table_selector_tmp = table_selector;
                if (table_selector.indexOf(":eq") !== -1) {
                    table_selector_tmp = table_selector.substring(0, table_selector.lastIndexOf(":eq"));
                }
                appendFilters(oTable, getOptions(table_selector_tmp), table_selector);
                if (getOptions(table_selector_tmp)[firstFromObject(getOptions(table_selector_tmp))].cumulative_filtering === true) {
                    //when filters should be populated only from visible rows (non filtered)
                    $(document).off('search.dt', oTable.selector).on('search.dt', oTable.selector, function (e, settings, json) {
                        var table_selector_tmp = oTable.selector;
                        if (table_selector.indexOf(":eq") !== -1) {
                            table_selector_tmp = table_selector.substring(0, table_selector.lastIndexOf(":eq"));
                        }
                        appendFilters(oTable, getOptions(table_selector_tmp), oTable.selector, settings);
                    });
                }
            } else {
                appendFilters(oTable, getOptions(table_selector), table_selector);
                if (yadcfVersionCheck('1.10')) {
                    $(document).off('xhr.dt', oTable.selector).on('xhr.dt', oTable.selector, function (e, settings, json) {
                        var col_num,
                            column_number_filter,
                            table_selector_jq_friendly = generateTableSelectorJQFriendly2(oTable);
                        if (json == undefined) {
                            console.log('datatables xhr.dt event came back with null as data (nothing for yadcf to do with it).');
                            return;
                        }
                        if (settings.oSavedState !== null) {
                            initColReorder2(settings, table_selector_jq_friendly);
                        }
                        for (col_num in yadcf.getOptions(settings.oInstance.selector)) {
                            if (yadcf.getOptions(settings.oInstance.selector).hasOwnProperty(col_num)) {
                                if (json['yadcf_data_' + col_num] !== undefined) {
                                    column_number_filter = col_num;
                                    if (settings.oSavedState !== null && plugins[table_selector_jq_friendly] !== undefined) {
                                        column_number_filter = plugins[table_selector_jq_friendly].ColReorder[col_num];
                                    }
                                    yadcf.getOptions(settings.oInstance.selector)[col_num].data = json['yadcf_data_' + column_number_filter];
                                }
                            }
                        }
                    });
                }
            }
            //events that affects both DOM and Ajax
            if (yadcfVersionCheck('1.10')) {
                $(document).off('draw.dt', oTable.selector).on('draw.dt', oTable.selector, function (event, settings) {
                    appendFilters(oTable, yadcf.getOptions(settings.oInstance.selector), settings.oInstance.selector, settings);
                });
                $(document).off('column-visibility.dt', oTable.selector).on('column-visibility.dt', oTable.selector, function (e, settings, col_num, state) {
                    var obj = {},
                        columnsObj = getOptions(settings.oInstance.selector);
                    if (state === true && settings._oFixedColumns === undefined) {
                        if ((plugins[table_selector_jq_friendly] !== undefined && plugins[table_selector_jq_friendly].ColReorder !== undefined)) {
                            col_num = plugins[table_selector_jq_friendly].ColReorder[col_num];
                        } else if (settings.oSavedState != undefined && settings.oSavedState.ColReorder !== undefined) {
                            col_num = settings.oSavedState.ColReorder[col_num];
                        }
                        obj[col_num] = yadcf.getOptions(settings.oInstance.selector)[col_num];
                        if (obj[col_num] !== undefined) {
                            obj[col_num].column_number = col_num;
                            if (obj[col_num] !== undefined) {
                                appendFilters(oTables[yadcf.generateTableSelectorJQFriendly2(settings)],
                                    obj,
                                    settings.oInstance.selector, settings);
                            }
                        }
                    } else if (settings._oFixedColumns !== undefined) {
                        appendFilters(oTables[yadcf.generateTableSelectorJQFriendly2(settings)],
                            columnsObj,
                            settings.oInstance.selector, settings);
                    }
                });
                $(document).off('column-reorder.dt', oTable.selector).on('column-reorder.dt', oTable.selector, function (e, settings, json) {
                    var table_selector_jq_friendly = generateTableSelectorJQFriendly2(oTable);
                    initColReorderFromEvent(table_selector_jq_friendly);
                });
                $(document).off('destroy.dt', oTable.selector).on('destroy.dt', oTable.selector, function (event, ui) {
                    removeFilters(oTable, yadcf.getOptions(ui.oInstance.selector), ui.oInstance.selector);
                });
            } else {
                $(document).off('draw', oTable.selector).on('draw', oTable.selector, function (event, settings) {
                    appendFilters(oTable, yadcf.getOptions(settings.oInstance.selector), settings.oInstance.selector, settings);
                });
                $(document).off('destroy', oTable.selector).on('destroy', oTable.selector, function (event, ui) {
                    removeFilters(oTable, yadcf.getOptions(ui.oInstance.selector), ui.oInstance.selector);
                });
            }
            if (oTable.fnSettings().oFeatures.bStateSave === true) {
                if (yadcfVersionCheck('1.10')) {
                    $(oTable.selector).off('stateSaveParams.dt').on('stateSaveParams.dt', function (e, settings, data) {
                        if (settings.oLoadedState && settings.oLoadedState.yadcfState !== undefined) {
                            data.yadcfState = settings.oLoadedState.yadcfState;
                        } else {
                            data.naruto = 'kurama';
                        }
                    });
                } else {
                    $(oTable.selector).off('stateSaveParams').on('stateSaveParams', function (e, settings, data) {
                        if (settings.oLoadedState && settings.oLoadedState.yadcfState !== undefined) {
                            data.yadcfState = settings.oLoadedState.yadcfState;
                        } else {
                            data.naruto = 'kurama';
                        }
                    });
                }
                //when using DOM source
                if (isDOMSource(oTable)) {
                    //we need to make sure that the yadcf state will be saved after page reload
                    oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
                    //redraw the table in order to apply the filters
                    oTable.fnDraw(false);
                }
            }
        }

        $.fn.yadcf = function (options_arg, params) {

            var tmpParams,
                i = 0,
                selector,
                tableSelector = '#' + this.fnSettings().sTableId;

            //in case that instance.selector will be undefined (jQuery 3)
            if (this.selector === undefined) {
                this.selector = tableSelector;
            }

            if (params === undefined) {
                params = {};
            }

            if (typeof params === 'string') {
                tmpParams = params;
                params = {};
                params.filters_position = tmpParams;
            }
            if (params.filters_position === undefined || params.filters_position === 'header') {
                params.filters_position = 'thead';
            } else {
                params.filters_position = 'tfoot';
            }
            if (params.language !== undefined) {
                for (tmpParams in placeholderLang) {
                    if (placeholderLang.hasOwnProperty(tmpParams)) {
                        if (params.language[tmpParams] !== undefined) {
                            placeholderLang[tmpParams] = params.language[tmpParams];
                        }
                    }
                }
            }
            $(document).data(this.selector + "_filters_position", params.filters_position);

            if ($(this.selector).length === 1) {
                setOptions(this.selector, options_arg, params);
                initAndBindTable(this, this.selector, 0);
            } else {
                for (i; i < $(this.selector).length; i++) {
                    $.fn.dataTableExt.iApiIndex = i;
                    selector = this.selector + ":eq(" + i + ")";
                    setOptions(this.selector, options_arg, params);
                    initAndBindTable(this, selector, i);
                }
                $.fn.dataTableExt.iApiIndex = 0;
            }
            return this;
        };

        function init(oTable, options_arg, params) {
            var instance = oTable.settings()[0].oInstance,
                i = 0,
                selector,
                tmpParams,
                tableSelector = '#' + oTable.table().node().id;

            //in case that instance.selector will be undefined (jQuery 3)
            if (instance.selector === undefined) {
                instance.selector = tableSelector;
            }

            if (params === undefined) {
                params = {};
            }

            if (typeof params === 'string') {
                tmpParams = params;
                params = {};
                params.filters_position = tmpParams;
            }
            if (params.filters_position === undefined || params.filters_position === 'header') {
                params.filters_position = 'thead';
            } else {
                params.filters_position = 'tfoot';
            }
            if (params.language !== undefined) {
                for (tmpParams in placeholderLang) {
                    if (placeholderLang.hasOwnProperty(tmpParams)) {
                        if (params.language[tmpParams] !== undefined) {
                            placeholderLang[tmpParams] = params.language[tmpParams];
                        }
                    }
                }
            }
            $(document).data(instance.selector + "_filters_position", params.filters_position);

            if ($(instance.selector).length === 1) {
                setOptions(instance.selector, options_arg, params);
                initAndBindTable(instance, instance.selector, 0, oTable);
            } else {
                for (i; i < $(instance.selector).length; i++) {
                    $.fn.dataTableExt.iApiIndex = i;
                    selector = instance.selector + ":eq(" + i + ")";
                    setOptions(instance.selector, options_arg, params);
                    initAndBindTable(instance, selector, i, oTable);
                }
                $.fn.dataTableExt.iApiIndex = 0;
            }
        }

        function appendFiltersMultipleTables(tablesArray, tablesSelectors, colObjDummy) {
            var filter_selector_string = "#" + colObjDummy.filter_container_id,
                $filter_selector = $(filter_selector_string).find(".yadcf-filter"),
                table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendlyNew(tablesSelectors),
                options_tmp,
                ii,
                column_number_str = columnsArrayToString(colObjDummy.column_number).column_number_str,
                tableTmp,
                tableTmpArr,
                tableTmpArrIndex,
                filterOptions = getOptions(tablesSelectors + '_' + column_number_str)[column_number_str],
                column_number_index,
                columnsTmpArr,
                settingsDt,
                tmpStr,
                columnForStateSaving;

            //add a wrapper to hold both filter and reset button
            $(filter_selector_string).append("<div id=\"yadcf-filter-wrapper-" + table_selector_jq_friendly + '-' + column_number_str + "\" class=\"no-print yadcf-filter-wrapper\"></div>");
            filter_selector_string = filter_selector_string + " div.yadcf-filter-wrapper";
            if (column_number_str.indexOf('_') !== -1) {
                columnForStateSaving = column_number_str.split('_')[0];
            } else {
                columnForStateSaving = column_number_str;
            }

            switch (filterOptions.filter_type) {
                case 'text':
                    $(filter_selector_string).append("<input type=\"text\" id=\"yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str + "\" style=\"width:100%\" class=\"yadcf-filter form-control\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);" +
                        "' placeholder='" + filterOptions.filter_default_label + "'" + " onkeyup=\"yadcf.textKeyUpMultiTables('" + tablesSelectors + "',event,'" + column_number_str + "');\"></input>");
                    if (filterOptions.filter_reset_button_text !== false) {
                        $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" " + " id=\"yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str + "-reset\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                            "onclick=\"yadcf.stopPropagation(event);yadcf.textKeyUpMultiTables('" + tablesSelectors + "', event,'" + column_number_str + "','clear'); return false;\" class=\"yadcf-filter-reset-button " + filterOptions.reset_button_style_class + "\">" + filterOptions.filter_reset_button_text + "</button>");
                    }
                    if (tablesArray[0].table !== undefined) {
                        tableTmp = $('#' + tablesArray[0].table().node().id).dataTable();
                    } else {
                        tableTmp = tablesArray[0];
                    }
                    settingsDt = getSettingsObjFromTable(tableTmp);
                    if (settingsDt.aoPreSearchCols[columnForStateSaving].sSearch !== '') {
                        tmpStr = settingsDt.aoPreSearchCols[columnForStateSaving].sSearch;
                        tmpStr = yadcfParseMatchFilter(tmpStr, filterOptions.filter_match_mode);
                        $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number_str).val(tmpStr).addClass("inuse");
                    }
                    break;
                case 'select':
                case 'multi_select':
                    if (filterOptions.select_type === undefined) {
                        options_tmp = "<option data-placeholder=\"true\" value=\"" + "-1" + "\">" + filterOptions.filter_default_label + "</option>";
                    } else {
                        options_tmp = "";
                    }
                    if (filterOptions.select_type === 'select2' && filterOptions.select_type_options.placeholder !== undefined && filterOptions.select_type_options.allowClear === true) {
                        options_tmp = "<option value=\"\"></option>";
                    }
                    if (filterOptions.data === undefined) {
                        filterOptions.data = [];
                        tableTmpArr = tablesSelectors.split(',');
                        for (tableTmpArrIndex = 0; tableTmpArrIndex < tableTmpArr.length; tableTmpArrIndex++) {
                            if (tablesArray[tableTmpArrIndex].table !== undefined) {
                                tableTmp = $('#' + tablesArray[tableTmpArrIndex].table().node().id).dataTable();
                            } else {
                                tableTmp = tablesArray[tableTmpArrIndex];
                            }
                            if (isDOMSource(tableTmp)) {
                                //check if ajax source, if so, listen for dt.draw
                                columnsTmpArr = filterOptions.column_number;
                                for (column_number_index = 0; column_number_index < columnsTmpArr.length; column_number_index++) {
                                    filterOptions.column_number = columnsTmpArr[column_number_index];
                                    filterOptions.data = filterOptions.data.concat(parseTableColumn(tableTmp, filterOptions, table_selector_jq_friendly));
                                }
                                filterOptions.column_number = columnsTmpArr;
                            } else {
                                $(document).off('draw.dt', '#' + tablesArray[tableTmpArrIndex].table().node().id).on('draw.dt', '#' + tablesArray[tableTmpArrIndex].table().node().id, function (event, ui) {
                                    var options_tmp = '',
                                        ii;
                                    columnsTmpArr = filterOptions.column_number;
                                    for (column_number_index = 0; column_number_index < columnsTmpArr.length; column_number_index++) {
                                        filterOptions.column_number = columnsTmpArr[column_number_index];
                                        filterOptions.data = filterOptions.data.concat(parseTableColumn(tableTmp, filterOptions, table_selector_jq_friendly, ui));
                                    }
                                    filterOptions.column_number = columnsTmpArr;
                                    filterOptions.data = sortColumnData(filterOptions.data, filterOptions);
                                    for (ii = 0; ii < filterOptions.data.length; ii++) {
                                        options_tmp += "<option value=\"" + filterOptions.data[ii] + "\">" + filterOptions.data[ii] + "</option>";
                                    }
                                    $('#' + filterOptions.filter_container_id + ' select').empty().append(options_tmp);

                                    if (filterOptions.select_type !== undefined) {
                                        initializeSelectPlugin(filterOptions.select_type, $('#' + filterOptions.filter_container_id + ' select'), filterOptions.select_type_options);
                                        if (filterOptions.cumulative_filtering === true && filterOptions.select_type === 'chosen') {
                                            refreshSelectPlugin(filterOptions, $('#' + filterOptions.filter_container_id + ' select'));
                                        }
                                    }
                                });
                            }
                        }
                    }

                    filterOptions.data = sortColumnData(filterOptions.data, filterOptions);

                    if (tablesArray[0].table !== undefined) {
                        tableTmp = $('#' + tablesArray[0].table().node().id).dataTable();
                    } else {
                        tableTmp = tablesArray[0];
                    }
                    settingsDt = getSettingsObjFromTable(tableTmp);

                    if (typeof filterOptions.data[0] === 'object') {
                        for (ii = 0; ii < filterOptions.data.length; ii++) {
                            options_tmp += "<option value=\"" + filterOptions.data[ii].value + "\">" + filterOptions.data[ii].label + "</option>";
                        }
                    } else {
                        for (ii = 0; ii < filterOptions.data.length; ii++) {
                            options_tmp += "<option value=\"" + filterOptions.data[ii] + "\">" + filterOptions.data[ii] + "</option>";
                        }
                    }
                    if (filterOptions.filter_type === 'select') {
                        $(filter_selector_string).append("<select class=\"pmd-select2 form-control\" id=\"yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str + "\" class=\"yadcf-filter form-control\" " +
                            "onchange=\"yadcf.doFilterMultiTables('" + tablesSelectors + "',event,'" + column_number_str + "')\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);'>" + options_tmp + "</select>");
                        if (settingsDt.aoPreSearchCols[columnForStateSaving].sSearch !== '') {
                            tmpStr = settingsDt.aoPreSearchCols[columnForStateSaving].sSearch;
                            tmpStr = yadcfParseMatchFilter(tmpStr, filterOptions.filter_match_mode);
                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number_str).val(tmpStr).addClass("inuse");
                        }
                    } else if (filterOptions.filter_type === 'multi_select') {
                        $(filter_selector_string).append("<select multiple data-placeholder=\"" + filterOptions.filter_default_label + "\" id=\"yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str + "\" class=\"yadcf-filter form-control\" " +
                            "onchange=\"yadcf.doFilterMultiTablesMultiSelect('" + tablesSelectors + "',event,'" + column_number_str + "')\" onmousedown=\"yadcf.stopPropagation(event);\" onclick='yadcf.stopPropagation(event);'>" + options_tmp + "</select>");
                        if (settingsDt.aoPreSearchCols[columnForStateSaving].sSearch !== '') {
                            tmpStr = settingsDt.aoPreSearchCols[columnForStateSaving].sSearch;
                            tmpStr = yadcfParseMatchFilterMultiSelect(tmpStr, filterOptions.filter_match_mode);
                            tmpStr = tmpStr.replace(/\\/g, "");
                            tmpStr = tmpStr.split("|");
                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number_str).val(tmpStr);
                        }
                    }
                    if (filterOptions.filter_type === 'select') {
                        if (filterOptions.filter_reset_button_text !== false) {
                            $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" " + " id=\"yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str + "-reset\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                                "onclick=\"yadcf.stopPropagation(event);yadcf.doFilterMultiTables('" + tablesSelectors + "', event,'" + column_number_str + "','clear'); return false;\" class=\"yadcf-filter-reset-button " + filterOptions.reset_button_style_class + "\">" + filterOptions.filter_reset_button_text + "</button>");
                        }
                    } else if (filterOptions.filter_type === 'multi_select') {
                        if (filterOptions.filter_reset_button_text !== false) {
                            $(filter_selector_string).find(".yadcf-filter").after("<button type=\"button\" " + " id=\"yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str + "-reset\" onmousedown=\"yadcf.stopPropagation(event);\" " +
                                "onclick=\"yadcf.stopPropagation(event);yadcf.doFilterMultiTablesMultiSelect('" + tablesSelectors + "', event,'" + column_number_str + "','clear'); return false;\" class=\"yadcf-filter-reset-button " + filterOptions.reset_button_style_class + "\">" + filterOptions.filter_reset_button_text + "</button>");
                        }
                    }

                    if (filterOptions.select_type !== undefined) {
                        initializeSelectPlugin(filterOptions.select_type, $("#yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str), filterOptions.select_type_options);
                        if (filterOptions.cumulative_filtering === true && filterOptions.select_type === 'chosen') {
                            refreshSelectPlugin(filterOptions, $("#yadcf-filter-" + table_selector_jq_friendly + '-' + column_number_str));
                        }
                    }
                    break;
                default:
                    alert('Filters Multiple Tables does not support ' + filterOptions.filter_type);
            }
        }

        function initMultipleTables(tablesArray, filtersOptions) {
            var i,
                tablesSelectors = '',
                default_options = {
                    filter_type: "text",
                    filter_container_id: '',
                    filter_reset_button_text: '',
                    case_insensitive: true
                },
                columnsObjKey,
                columnsObj,
                columnsArrIndex,
                column_number_str,
                dummyArr;

            for (columnsArrIndex = 0; columnsArrIndex < filtersOptions.length; columnsArrIndex++) {
                dummyArr = [];
                columnsObj = filtersOptions[columnsArrIndex];
                if (columnsObj.filter_default_label === undefined) {
                    if (columnsObj.filter_type === "select" || columnsObj.filter_type === 'custom_func') {
                        columnsObj.filter_default_label = "Select value";
                    } else if (columnsObj.filter_type === "multi_select" || columnsObj.filter_type === 'multi_select_custom_func') {
                        columnsObj.filter_default_label = "Select values";
                    } else if (columnsObj.filter_type === "auto_complete" || columnsObj.filter_type === "text") {
                        columnsObj.filter_default_label = 'Type to filter';
                    } else if (columnsObj.filter_type === "range_number" || columnsObj.filter_type === "range_date") {
                        columnsObj.filter_default_label = ["from", "to"];
                    } else if (columnsObj.filter_type === "date") {
                        columnsObj.filter_default_label = "Select a date";
                    }
                }
                columnsObj = $.extend({}, default_options, columnsObj);

                column_number_str = columnsArrayToString(columnsObj.column_number).column_number_str;
                columnsObj.column_number_str = column_number_str;

                dummyArr.push(columnsObj);
                tablesSelectors = '';
                for (i = 0; i < tablesArray.length; i++) {
                    if (tablesArray[i].table !== undefined) {
                        tablesSelectors += tablesArray[i].table().node().id + ',';
                    } else {
                        tablesSelectors += getSettingsObjFromTable(tablesArray[i]).sTableId;
                    }
                }
                tablesSelectors = tablesSelectors.substring(0, tablesSelectors.length - 1);

                setOptions(tablesSelectors + '_' + column_number_str, dummyArr);
                oTables[tablesSelectors] = tablesArray;
                appendFiltersMultipleTables(tablesArray, tablesSelectors, columnsObj);
            }
        }

        function initMultipleColumns(table, filtersOptions) {
            var tablesArray = [];
            tablesArray.push(table);
            initMultipleTables(tablesArray, filtersOptions);
        }

        function stopPropagation(evt) {
            if (evt.stopPropagation !== undefined) {
                evt.stopPropagation();
            } else {
                evt.cancelBubble = true;
            }
        }

        function preventDefaultForEnter(evt) {
            if (evt.keyCode === 13) {
                if (evt.preventDefault) {
                    evt.preventDefault();
                } else {
                    evt.returnValue = false;
                }
            }
        }

        //--------------------------------------------------------
        function exInternalFilterColumnAJAXQueue(table_arg, col_filter_arr) {
            return function () {
                exFilterColumn(table_arg, col_filter_arr, true);
            };
        }

        function exFilterColumn(table_arg, col_filter_arr, ajaxSource) {
            var table_selector_jq_friendly,
                j,
                tmpStr,
                column_number,
                column_position,
                filter_value,
                fromId,
                toId,
                sliderId,
                optionsObj,
                min,
                max,
                exclude = false;
            //check if the table arg is from new datatables API (capital "D")
            if (table_arg.settings !== undefined) {
                table_arg = table_arg.settings()[0].oInstance;
            }
            table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(table_arg);
            if (isDOMSource(table_arg) || ajaxSource === true) {
                for (j = 0; j < col_filter_arr.length; j++) {
                    column_number = col_filter_arr[j][0];
                    column_position = column_number;
                    exclude = false;
                    if (plugins[table_selector_jq_friendly] !== undefined && (plugins[table_selector_jq_friendly] !== undefined && plugins[table_selector_jq_friendly].ColReorder !== undefined)) {
                        column_position = plugins[table_selector_jq_friendly].ColReorder[column_number];
                    }
                    optionsObj = getOptions(table_arg.selector)[column_number];
                    filter_value = col_filter_arr[j][1];

                    switch (optionsObj.filter_type) {
                        case 'auto_complete':
                        case 'text':
                        case 'bap_date_range_picker':
                        case 'bap_date_picker':
                        case 'date':
                            if (filter_value !== undefined && filter_value.indexOf('_exclude_') !== -1) {
                                exclude = true;
                                filter_value = filter_value.replace('_exclude_', '');
                            }
                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(filter_value);
                            if (filter_value !== '') {
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).addClass('inuse');
                            } else {
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).removeClass('inuse');
                            }
                            tmpStr = yadcfMatchFilterString(table_arg, column_position, filter_value, optionsObj.filter_match_mode, false, exclude);
                            table_arg.fnSettings().aoPreSearchCols[column_position].sSearch = tmpStr;
                            break;
                        case 'select':
                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(filter_value);
                            if (filter_value !== '') {
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).addClass('inuse');
                            } else {
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).removeClass('inuse');
                            }
                            tmpStr = yadcfMatchFilterString(table_arg, column_position, filter_value, optionsObj.filter_match_mode, false);
                            table_arg.fnSettings().aoPreSearchCols[column_position].sSearch = tmpStr;
                            if (optionsObj.select_type !== undefined) {
                                refreshSelectPlugin(optionsObj, $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number), '-1');
                            }
                            break;
                        case 'multi_select':
                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(filter_value);
                            tmpStr = yadcfMatchFilterString(table_arg, column_position, filter_value, optionsObj.filter_match_mode, true);
                            table_arg.fnSettings().aoPreSearchCols[column_position].sSearch = tmpStr;
                            if (optionsObj.select_type !== undefined) {
                                refreshSelectPlugin(optionsObj, $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number), '-1');
                            }
                            break;
                        case 'range_date':
                            fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-from-date-' + column_number;
                            toId = 'yadcf-filter-' + table_selector_jq_friendly + '-to-date-' + column_number;
                            $('#' + fromId).val(filter_value.from);
                            if (filter_value.from !== '') {
                                $('#' + fromId).addClass('inuse');
                            } else {
                                $('#' + fromId).removeClass('inuse');
                            }
                            $('#' + toId).val(filter_value.to);
                            if (filter_value.to !== '') {
                                $('#' + toId).addClass('inuse');
                            } else {
                                $('#' + toId).removeClass('inuse');
                            }
                            if (table_arg.fnSettings().oFeatures.bServerSide === true) {
                                min = filter_value.from;
                                max = filter_value.to;
                                table_arg.fnSettings().aoPreSearchCols[column_position].sSearch = min + '-yadcf_delim-' + max;
                            }
                            saveStateSave(table_arg, column_number, table_selector_jq_friendly, filter_value.from, filter_value.to);
                            break;
                        case 'range_number':
                            fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-from-' + column_number;
                            toId = 'yadcf-filter-' + table_selector_jq_friendly + '-to-' + column_number;
                            $('#' + fromId).val(filter_value.from);
                            if (filter_value.from !== '') {
                                $('#' + fromId).addClass('inuse');
                            } else {
                                $('#' + fromId).removeClass('inuse');
                            }
                            $('#' + toId).val(filter_value.to);
                            if (filter_value.to !== '') {
                                $('#' + toId).addClass('inuse');
                            } else {
                                $('#' + toId).removeClass('inuse');
                            }
                            if (table_arg.fnSettings().oFeatures.bServerSide === true) {
                                table_arg.fnSettings().aoPreSearchCols[column_position].sSearch = filter_value.from + '-yadcf_delim-' + filter_value.to;
                            }
                            saveStateSave(table_arg, column_number, table_selector_jq_friendly, filter_value.from, filter_value.to);
                            break;
                        case 'range_number_slider':
                            sliderId = 'yadcf-filter-' + table_selector_jq_friendly + '-slider-' + column_number;
                            fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-min_tip-' + column_number;
                            toId = 'yadcf-filter-' + table_selector_jq_friendly + '-max_tip-' + column_number;
                            if (filter_value.from !== '') {
                                min = $('#' + fromId).closest('.yadcf-filter-range-number-slider').find(".yadcf-filter-range-number-slider-min-tip-hidden").text();
                                max = $('#' + fromId).closest('.yadcf-filter-range-number-slider').find(".yadcf-filter-range-number-slider-max-tip-hidden").text();
                                $('#' + fromId).text(filter_value.from);
                                if (min !== filter_value.from) {
                                    $('#' + fromId).parent().addClass('inuse');
                                    $('#' + fromId).parent().parent().find('ui-slider-range').addClass('inuse');
                                } else {
                                    $('#' + fromId).parent().removeClass('inuse');
                                    $('#' + fromId).parent().parent().find('ui-slider-range').removeClass('inuse');
                                }
                                $('#' + sliderId).slider('values', 0, filter_value.from);
                            }
                            if (filter_value.to !== '') {
                                $('#' + toId).text(filter_value.to);
                                if (max !== filter_value.to) {
                                    $('#' + toId).parent().addClass('inuse');
                                    $('#' + toId).parent().parent().find('.ui-slider-range').addClass('inuse');
                                } else {
                                    $('#' + toId).parent().removeClass('inuse');
                                    $('#' + toId).parent().parent().find('.ui-slider-range').removeClass('inuse');
                                }
                                $('#' + sliderId).slider('values', 1, filter_value.to);
                            }
                            if (table_arg.fnSettings().oFeatures.bServerSide === true) {
                                table_arg.fnSettings().aoPreSearchCols[column_position].sSearch = filter_value.from + '-yadcf_delim-' + filter_value.to;
                            }
                            saveStateSave(table_arg, column_number, table_selector_jq_friendly, filter_value.from, filter_value.to);
                            break;
                        case 'custom_func':
                        case 'multi_select_custom_func':
                            $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).val(filter_value);
                            if (filter_value !== '') {
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).addClass('inuse');
                            } else {
                                $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number).removeClass('inuse');
                            }
                            if (table_arg.fnSettings().oFeatures.bServerSide === true) {
                                table_arg.fnSettings().aoPreSearchCols[column_position].sSearch = filter_value;
                            }
                            if (optionsObj.select_type !== undefined) {
                                refreshSelectPlugin(optionsObj, $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number), filter_value);
                            }
                            saveStateSave(table_arg, column_number, table_selector_jq_friendly, filter_value, '');
                            break;
                    }
                }
                if (table_arg.fnSettings().oFeatures.bServerSide !== true) {
                    table_arg.fnDraw();
                } else {
                    setTimeout(function () {
                        table_arg.fnDraw();
                    }, 10);
                }
            } else {
                exFilterColumnQueue.push(exInternalFilterColumnAJAXQueue(table_arg, col_filter_arr));
            }
        }

        function exGetColumnFilterVal(table_arg, column_number) {
            var retVal,
                fromId,
                toId,
                table_selector_jq_friendly,
                optionsObj,
                $filterElement;

            //check if the table arg is from new datatables API (capital "D")
            if (table_arg.settings !== undefined) {
                table_arg = table_arg.settings()[0].oInstance;
            }

            optionsObj = getOptions(table_arg.selector)[column_number];
            table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(table_arg);

            $filterElement = $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number);
            switch (optionsObj.filter_type) {
                case 'select':
                case 'custom_func':
                    retVal = $filterElement.val();
                    if (retVal === '-1') {
                        retVal = '';
                    }
                    break;
                case 'auto_complete':
                case 'text':
                case 'bap_date_range_picker':
                case 'bap_date_picker':
                case 'date':
                    retVal = $filterElement.val();
                    if ($filterElement.prev().hasClass('yadcf-exclude-wrapper') && $filterElement.prev().find('input').prop('checked') === true) {
                        retVal = '_exclude_' + retVal;
                    }
                    break;
                case 'multi_select':
                    retVal = $filterElement.val();
                    if (retVal === null) {
                        retVal = '';
                    }
                    break;
                case 'range_date':
                    retVal = {};
                    fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-from-date-' + column_number;
                    toId = 'yadcf-filter-' + table_selector_jq_friendly + '-to-date-' + column_number;

                    retVal.from = $('#' + fromId).val();
                    retVal.to = $('#' + toId).val();
                    break;
                case 'range_number':
                    retVal = {};
                    fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-from-' + column_number;
                    toId = 'yadcf-filter-' + table_selector_jq_friendly + '-to-' + column_number;

                    retVal.from = $('#' + fromId).val();
                    retVal.to = $('#' + toId).val();
                    break;
                case 'range_number_slider':
                    retVal = {};
                    fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-min_tip-' + column_number;
                    toId = 'yadcf-filter-' + table_selector_jq_friendly + '-max_tip-' + column_number;

                    retVal.from = $('#' + fromId).text();
                    retVal.to = $('#' + toId).text();

                    break;
                default:
                    console.log('exGetColumnFilterVal error: no such filter_type: ' + optionsObj.filter_type);
            }
            return retVal;
        }

        function clearStateSave(oTable, column_number, table_selector_jq_friendly) {
            var yadcfState;
            if (oTable.fnSettings().oFeatures.bStateSave === true) {
                if (!oTable.fnSettings().oLoadedState) {
                    oTable.fnSettings().oLoadedState = {};
                    oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
                }
                if (oTable.fnSettings().oLoadedState.yadcfState !== undefined && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] !== undefined) {
                    oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number] = undefined;
                } else {
                    yadcfState = {};
                    yadcfState[table_selector_jq_friendly] = [];
                    yadcfState[table_selector_jq_friendly][column_number] = undefined;
                    oTable.fnSettings().oLoadedState.yadcfState = yadcfState;
                }
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }
        }

        function saveStateSave(oTable, column_number, table_selector_jq_friendly, from, to) {
            var yadcfState;
            if (oTable.fnSettings().oFeatures.bStateSave === true) {
                if (!oTable.fnSettings().oLoadedState) {
                    oTable.fnSettings().oLoadedState = {};
                }
                if (oTable.fnSettings().oLoadedState.yadcfState !== undefined && oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly] !== undefined) {
                    oTable.fnSettings().oLoadedState.yadcfState[table_selector_jq_friendly][column_number] = {
                        'from': from,
                        'to': to
                    };
                } else {
                    yadcfState = {};
                    yadcfState[table_selector_jq_friendly] = [];
                    yadcfState[table_selector_jq_friendly][column_number] = {
                        'from': from,
                        'to': to
                    };
                    oTable.fnSettings().oLoadedState.yadcfState = yadcfState;
                }
                oTable.fnSettings().oApi._fnSaveState(oTable.fnSettings());
            }
        }

        function exResetAllFilters(table_arg, noRedraw, columns) {
            var table_selector_jq_friendly,
                column_number,
                fromId,
                toId,
                sliderId,
                tableOptions,
                optionsObj,
                columnObjKey,
                settingsDt = getSettingsObjFromTable(table_arg),
                i,
                $filterElement;

            //check if the table arg is from new datatables API (capital "D")
            if (table_arg.settings !== undefined) {
                table_arg = table_arg.settings()[0].oInstance;
            }
            tableOptions = getOptions(table_arg.selector);
            table_selector_jq_friendly = yadcf.generateTableSelectorJQFriendly2(table_arg);
            settingsDt = getSettingsObjFromTable(table_arg);

            for (columnObjKey in tableOptions) {
                if (tableOptions.hasOwnProperty(columnObjKey)) {
                    optionsObj = tableOptions[columnObjKey];
                    column_number = optionsObj.column_number;

                    if (columns !== undefined && $.inArray(column_number, columns) === -1) {
                        continue;
                    }
                    $(document).removeData("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val");

                    $filterElement = $('#yadcf-filter-' + table_selector_jq_friendly + '-' + column_number);

                    switch (optionsObj.filter_type) {



                        case 'select':
                        case 'custom_func':
                            $filterElement.val('-1').removeClass('inuse');
                            table_arg.fnSettings().aoPreSearchCols[column_number].sSearch = '';
                            if (optionsObj.select_type !== undefined) {
                                refreshSelectPlugin(optionsObj, $filterElement, '-1');
                            }
                            break;
                        case 'auto_complete':
                        case 'bap_date_range_picker':
                        case 'bap_date_picker':
                        case 'text':
                        case 'date':
                            $filterElement.val('').removeClass('inuse');
                            table_arg.fnSettings().aoPreSearchCols[column_number].sSearch = '';
                            if ($filterElement.prev().hasClass('yadcf-exclude-wrapper')) {
                                $filterElement.prev().find('input').prop('checked', false);
                            }
                            break;
                        case 'multi_select':
                        case 'multi_select_custom_func':
                            $filterElement.val('-1');
                            $(document).data("#yadcf-filter-" + table_selector_jq_friendly + "-" + column_number + "_val", undefined);
                            table_arg.fnSettings().aoPreSearchCols[column_number].sSearch = '';
                            if (optionsObj.select_type !== undefined) {
                                refreshSelectPlugin(optionsObj, $filterElement, '-1');
                            }
                            break;
                        case 'range_date':
                            fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-from-date-' + column_number;
                            toId = 'yadcf-filter-' + table_selector_jq_friendly + '-to-date-' + column_number;
                            $('#' + fromId).val('');
                            $('#' + fromId).removeClass('inuse');
                            $('#' + toId).val('');
                            $('#' + toId).removeClass('inuse');
                            if (table_arg.fnSettings().oFeatures.bServerSide === true) {
                                table_arg.fnSettings().aoPreSearchCols[column_number].sSearch = '';
                            }
                            clearStateSave(table_arg, column_number, table_selector_jq_friendly);
                            break;
                        case 'range_number':
                            fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-from-' + column_number;
                            toId = 'yadcf-filter-' + table_selector_jq_friendly + '-to-' + column_number;
                            $('#' + fromId).val('');
                            $('#' + fromId).removeClass('inuse');
                            $('#' + toId).val('');
                            $('#' + toId).removeClass('inuse');
                            if (table_arg.fnSettings().oFeatures.bServerSide === true) {
                                table_arg.fnSettings().aoPreSearchCols[column_number].sSearch = '';
                            }
                            clearStateSave(table_arg, column_number, table_selector_jq_friendly);
                            break;
                        case 'range_number_slider':
                            sliderId = 'yadcf-filter-' + table_selector_jq_friendly + '-slider-' + column_number;
                            fromId = 'yadcf-filter-' + table_selector_jq_friendly + '-min_tip-' + column_number;
                            toId = 'yadcf-filter-' + table_selector_jq_friendly + '-max_tip-' + column_number;
                            $('#' + fromId).text('');
                            $('#' + fromId).parent().removeClass('inuse');
                            $('#' + fromId).parent().parent().find('ui-slider-range').removeClass('inuse');
                            $('#' + toId).text('');
                            $('#' + toId).parent().removeClass('inuse');
                            $('#' + toId).parent().parent().find('.ui-slider-range').removeClass('inuse');
                            $('#' + sliderId).slider("option", "values", [$('#' + fromId).parent().parent().find('.yadcf-filter-range-number-slider-min-tip-hidden').text(), $('#' + fromId).parent().parent().find('.yadcf-filter-range-number-slider-max-tip-hidden').text()]);
                            if (table_arg.fnSettings().oFeatures.bServerSide === true) {
                                table_arg.fnSettings().aoPreSearchCols[column_number].sSearch = '';
                            }
                            clearStateSave(table_arg, column_number, table_selector_jq_friendly);
                            break;
                    }

                }
            }
            if (noRedraw !== true) {
                //clear global filter
                settingsDt.oPreviousSearch.sSearch = '';
                if (settingsDt.aanFeatures.f !== undefined) {
                    for (i = 0; i < settingsDt.aanFeatures.f.length; i++) {
                        $('input', settingsDt.aanFeatures.f[i]).val('');
                    }
                }
                //end of clear global filter
                table_arg.fnDraw(settingsDt);
            }
        }

        function exResetFilters(table_arg, columns, noRedraw) {
            exResetAllFilters(table_arg, noRedraw, columns);
        }

        function exFilterExternallyTriggered(table_arg) {
            var columnsObj,
                columnObjKey,
                columnObj,
                filterValue,
                filtersValuesSingleElem,
                filtersValuesArr = [];

            //check if the table arg is from new datatables API (capital "D")
            if (table_arg.settings !== undefined) {
                table_arg = table_arg.settings()[0].oInstance;
            }
            columnsObj = getOptions(table_arg.selector);

            for (columnObjKey in columnsObj) {
                if (columnsObj.hasOwnProperty(columnObjKey)) {
                    columnObj = columnsObj[columnObjKey];
                    filterValue = exGetColumnFilterVal(table_arg, columnObj.column_number);
                    filtersValuesSingleElem = [];
                    filtersValuesSingleElem.push(columnObj.column_number);
                    filtersValuesSingleElem.push(filterValue);
                    filtersValuesArr.push(filtersValuesSingleElem);
                }
            }
            exFilterColumn(table_arg, filtersValuesArr, true);
        }

        return {
            init: init,
            doFilter: doFilter,
            doFilterMultiSelect: doFilterMultiSelect,
            doFilterAutocomplete: doFilterAutocomplete,
            autocompleteKeyUP: autocompleteKeyUP,
            getOptions: getOptions,
            rangeNumberKeyUP: rangeNumberKeyUP,
            rangeDateKeyUP: rangeDateKeyUP,
            rangeClear: rangeClear,
            rangeNumberSliderClear: rangeNumberSliderClear,
            stopPropagation: stopPropagation,
            exFilterColumn: exFilterColumn,
            exGetColumnFilterVal: exGetColumnFilterVal,
            exResetAllFilters: exResetAllFilters,
            dateKeyUP: dateKeyUP,
            dateSelectSingle: dateSelectSingle,
            textKeyUP: textKeyUP,
            doFilterCustomDateFunc: doFilterCustomDateFunc,
            eventTargetFixUp: eventTargetFixUp,
            initMultipleTables: initMultipleTables,
            initMultipleColumns: initMultipleColumns,
            textKeyUpMultiTables: textKeyUpMultiTables,
            doFilterMultiTables: doFilterMultiTables,
            doFilterMultiTablesMultiSelect: doFilterMultiTablesMultiSelect,
            generateTableSelectorJQFriendlyNew: generateTableSelectorJQFriendlyNew,
            exFilterExternallyTriggered: exFilterExternallyTriggered,
            exResetFilters: exResetFilters,
            initSelectPluginCustomTriggers: initSelectPluginCustomTriggers,
            preventDefaultForEnter: preventDefaultForEnter,
            generateTableSelectorJQFriendly2: generateTableSelectorJQFriendly2
        };

    }());
    if (window) {
        window.yadcf = yadcf;
    }
    return yadcf;
}));
