(function($){
     $('select#type').change(function(){
        var val = $(this).val();

        $('[name^=content_]').parents('.clearfix').hide();
        $('[name^=content_'+ val +']').parents('.clearfix').show();
    }).change();
})(jQuery);