jQuery(document).ready(function(){
    jQuery('#redDebug').append('<div id="redDebug-visibility"></div>');
    jQuery('#redDebug-bar a').click(function(){
        var element = jQuery(this);
        var position = element.offset();
        var bottom = element.parents('#redDebug-bar').outerHeight() - 1;
        var open = "#redDebug-panel-" + element.attr('rel');

        //position using it to move element
        var panel = jQuery(open);
        var isDisplay = (panel.css('display') != 'none');

        // RedDebug panel
        jQuery('.reddebug-panel').RedDebugModal('hide');
        jQuery('.modal-backdrop').remove();

        // Move title
        if(panel.find('.modal-body>h1').length == 1)
        {
            panel.find('.modal-title').html(jQuery(open).find('.modal-body > h1').html());
            panel.find('.modal-body > h1').remove();
        }

        // Move element
        panel.css({"bottom": bottom});

        if(!isDisplay){
            panel.RedDebugModal('show');
            jQuery('.modal-backdrop').click(function(){
                jQuery('.reddebug-panel').RedDebugModal('hide');
                jQuery('.modal-backdrop').remove();
            });
        }



    });

});
