(function ($) {
    
    var message = (function () {
      var init = function () {
       
      };
      
      var toast = function (type, sticky, value) {
        $().toastmessage('showToast', {
          text: value,
          sticky: sticky,
          position: 'bottom-right',
          type: type
        });
      };
      
      var infobox = function (type, time, value) {
        $('#infobox').html('<div class="alert alert-' + type + '" tabindex="-1"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span>' + value.replace('Unexpected token <', '').trim() + '</span></div>');
        $('#infobox').show();
        if (time > 0) {
          setTimeout(function () {
            $("#infobox").hide();
          }, time);
        }
      };
      
      return {
        init: init,
        toast: toast,
        infobox: infobox
      };
    })();

    $.extend(true, window, {
      core: {
        message: message
      }
    });

    $(function () {
        core.message.init();
    });

}(jQuery));
