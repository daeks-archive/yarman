(function ($) {
    
    var form = (function () {
      var init = function () {
        $('[data-provider="datepicker"]').datepicker({
          format: "yyyy/mm/dd"
        });
        
        $("[data-title='tooltip']").tooltip();
        
        $("[data-toggle='lazy']").lazyload();
        
        $("[data-title='popover']").popover({
          trigger: 'hover'
        });
      };
      
      return {
        init: init
      };
    })();

    $.extend(true, window, {
      core: {
        form: form
      }
    });

    $(function () {
        core.form.init();
    });

}(jQuery));