/**
 * admin javascript for toolkit profiles plugin
 */

// actions on Advanced Custom Fields
(function($){
    $(document).ready(function(){
        if (typeof acf !== 'undefined') {
            acf.add_action('ready', function(){
                var check_selected_table_columns = function(){
                    var max_columns = 6;
                    var table_checkboxes_checked = $('.acf-field-tk-table-view-fields :checkbox:checked').length;
                    if ( table_checkboxes_checked < max_columns ) {
                        $('.acf-field-tk-table-view-fields :checkbox').not(':checked').removeAttr("disabled");
                    }
                    if ( table_checkboxes_checked == max_columns ) {
                        $('.acf-field-tk-table-view-fields :checkbox').not(':checked').attr("disabled", true);
                    }
                    if ( table_checkboxes_checked > max_columns ) {
                        var number_checked = 0, reached_limit = false;
                        $('.acf-field-tk-table-view-fields :checkbox').each(function(){
                            if (reached_limit) {
                                $(this).prop("checked", false);
                            } else {
                                if ($(this).is(':checked')) {
                                    number_checked++
                                }
                                if (number_checked == max_columns) {
                                    reached_limit = true;
                                }
                            }
                        });
                        $('.acf-field-tk-table-view-fields :checkbox').not(':checked').attr("disabled", true);
                    }
                }
                $('.acf-field-tk-table-view-fields :checkbox').on('click', function(e) {
                    check_selected_table_columns();
                });
                check_selected_table_columns();
            });
        }
    });
})(jQuery);
