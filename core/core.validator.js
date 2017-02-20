(function($){
    
    var validator = (function() {
      var init = function() {
        $('form[data-toggle="validator"]').formValidation({
          feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
          }
        });
      };
      
      return {
        init: init
      };
    })();

    $.extend(true, window, {
      core: {
        validator: validator
      }
    });

    $(function() {
        core.validator.init();
    });

}(jQuery));