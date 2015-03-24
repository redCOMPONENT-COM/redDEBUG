jQuery(document).ready(function(){
    jQuery('#ip_fields .ip-row').css({'padding' : '0px 0px 10px 0px'});
    jQuery('#ip_fields .reddebug_ip_add').on('click', function(){
        var tr = jQuery('#ip_fields .ip-row:last').clone();
        tr.find('input').val('');
        jQuery('#ip_fields .list').append(tr);
    });

    jQuery(document).on('click', '#ip_fields .reddebug_ip_remove', function (){
        if(jQuery('#ip_fields .ip-row').length == 1)
        {
            jQuery(this).parents('.ip-row').find('input').val('');
        }
        else
        {
            jQuery(this).parents('.ip-row').remove();
        }
    });
});