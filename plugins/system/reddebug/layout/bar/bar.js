jQuery(document).ready(function(){
    jQuery('#redDebug-bar a').click(function(){
        jQuery('#redDebug-panel-' + jQuery(this).attr('rel')).dialog('open');
        return false;
    });
});
