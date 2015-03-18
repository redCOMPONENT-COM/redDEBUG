jQuery(document).ready(function(){
    jQuery('#redDebug').append('<div id="redDebug-visibility"></div>');
    jQuery('#redDebug-bar a').click(function(){

        jQuery('.reddebug-panel').RedDebugModal('hide');
        var element = jQuery(this);
        var position = element.offset();
        var xw = element.outerWidth();
        var xh = element.outerHeight();
        var open = "#redDebug-panel-" + element.attr('rel');
        //position using it to move element

        var left = position.left + xw;
        var bottom = xh;

        if(jQuery(open).find('.modal-body>h1').length == 1)
        {
            jQuery(open).find('.modal-title').html(jQuery(open).find('.modal-body > h1').html());
            jQuery(open).find('.modal-body > h1').remove();
        }
        jQuery(open).css({"left":(position.left), "bottom": bottom});
        jQuery(open).RedDebugModal('show');


    });

    var ModalPrefix = function(){

    }
});
