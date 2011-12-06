(function( $ ){

    var modal_ = '<div class="modal hide">'+
                        '<div class="modal-header">'+
                            '<a href="#" class="close">×</a>'+
                            '<h3 class="modal-title"></h3>'+
                        '</div>'+
                        '<div class="modal-body"></div>'+
                    '</div>';

    $('body').append(modal_);

    $('.modal').modal({
        backdrop: true,
        keyboard: true
    }).bind('show', function() {
        $('.modal-body').html($('<div class="ajaxLoader"></div>'));
    });

    var methods = {
        init:function (options) {

            return this.each(function () {

                var $this = $(this),
                    data = $this.data('ajaxDialog');

                if (!data) {
                    $(this).data('ajaxDialog', {
                        target:$this
                    });
                }

                $this.click(function(e){
                    e.stopPropagation();

                    var href_  = $(this).attr('href'),
                        title_ = $(this).attr('title');

                    $('.modal').modal('show');
                    $('.modal-title').text(title_);

                    $.ajax({
                        url: href_,
                        context: $('.modal-body'),
                        success: function(responseText){
                          $('.modal-body').html(responseText);
                        }
                    });

                    return false;
                });
            });
        },
        destroy:function () {
            return this.each(function () {

                var $this = $(this),
                    data = $this.data('ajaxDialog');

                // Namespacing FTW
                $(window).unbind('ajaxDialog');
                data.tooltip.remove();
                $this.removeData('ajaxDialog');

            });
        }
    };

     $.fn.ajaxDialog = function (method) {

         if (methods[method]) {
             return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
         } else if (typeof method === 'object' || !method) {
             return methods.init.apply(this, arguments);
         } else {
             $.error('Method ' + method + ' does not exist on jQuery.ajaxDialog');
         }

     };

})( jQuery );

(function($){
     $('select#type').change(function(){
        var val = $(this).val();

        $('[name^=content_]').parents('.clearfix').hide();
        $('[name^=content_'+ val +']').parents('.clearfix').show();
    }).change();

    $('.dropdown').dropdown();

    $('.ajaxDialog').ajaxDialog();

})(jQuery);