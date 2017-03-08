(function ($) {
    
    var admin = (function () {
      var init = function () {
      
      };
      
      return {
        init: init
      };
    })();

    $.extend(true, window, {
      core: {
        admin: admin
      }
    });

    $(function () {
        core.admin.init();
    });

}(jQuery));