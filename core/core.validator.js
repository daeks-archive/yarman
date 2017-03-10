(function ($) {
    
    var validator = (function () {
      var init = function () {
        $('form[data-validate="form"]').formValidation({
          feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
          },
          submitButtons: 'button[data-validate="form"]'
        })
        .on('success.field.fv', function (e, data) {
          if (data.fv.getInvalidFields().length > 0) {
            $('button[data-validate="form"]').prop('disabled', true);
          } else {
            $('button[data-validate="form"]').prop('disabled', false);
          }
        })
        .on('err.field.fv', function (e, data) {
          $('button[data-validate="form"]').prop('disabled', true);
        })
        .on('input', function (e, data) {
          if (typeof $('#' + e.target.id).attr('data-fv') == 'undefined') {
            $('button[data-validate="form"]').prop('disabled', false);
          }
        })
        .on('change', function (e, data) {
          if (typeof $('#' + e.target.id).attr('data-fv') == 'undefined') {
            $('button[data-validate="form"]').prop('disabled', false);
          }
        })
        .on('changeDate', function (e) {
          $('#' + e.target.id).datepicker('hide').trigger('input');
          $('button[data-validate="form"]').prop('disabled', false);
        });
      };
      
      var reinit = function () {
        $('form[data-validate="modal"]').formValidation({
          feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
          },
          submitButtons: 'button[data-validate="modal"]'
        })
        .on('success.field.fv', function (e, data) {
          if (data.fv.getInvalidFields().length > 0) {
            $('button[data-validate="modal"]').prop('disabled', true);
          } else {
            $('button[data-validate="modal"]').prop('disabled', false);
          }
        })
        .on('err.field.fv', function (e, data) {
          $('button[data-validate="modal"]').prop('disabled', true);
        })
        .on('input', function (e, data) {
          if (typeof $('#' + e.target.id).attr('data-fv') == 'undefined') {
            $('button[data-validate="modal"]').prop('disabled', false);
          }
        })
        .on('change', function (e, data) {
          if (typeof $('#' + e.target.id).attr('data-fv') == 'undefined') {
            $('button[data-validate="modal"]').prop('disabled', false);
          }
        })
        .on('changeDate', function (e) {
          $('#' + e.target.id).datepicker('hide').trigger('input');
          $('button[data-validate="modal"]').prop('disabled', false);
        });
      };
      
      return {
        init: init,
        reinit: reinit
      };
    })();

    $.extend(true, window, {
      core: {
        validator: validator
      }
    });

    $(function () {
        core.validator.init();
    });

}(jQuery));