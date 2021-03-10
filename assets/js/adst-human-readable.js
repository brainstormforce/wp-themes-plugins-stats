
//display and hide human readable Number format dropdown list.
jQuery("input[name='adst_r_option']").on('click',function () {
    jQuery('#title_num_option').css('display', (jQuery(this).val() === 'normal') ? 'table-row':'none');
});
