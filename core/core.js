(function ($) {
    
    var core = (function () {

      var init = function () {
        $.get('/modules/setup/controller.php?action=init', function (data) {
          try {
            var obj = $.parseJSON(data);
            if (obj.status == 200) {
              if (obj.data == 0) {
                $('.modal-content').load('/modules/setup/dialog.php?action=setup',function(result){
                  $('.modal').modal({show:true});
                  $('.modal').css('display', 'block');
                  var $dialog = $('.modal').find('.modal-dialog');
                  var offset = ($(window).height() - $dialog.height()) / 2;
                  var bottomMargin = $dialog.css('marginBottom');
                  bottomMargin = parseInt(bottomMargin);
                  if (offset < bottomMargin) {
                    offset = bottomMargin;
                  }
                  $dialog.css("margin-top", offset);
                  $('button[data-query="modal-data"]').on('click', function (event) {
                    event.preventDefault();
                    $(this).html('<i class="fa fa-spinner fa-spin"></i> ' + $(this).html());
                    $(this).prop('disabled', true);
                    core.install();
                    return false;
                  });
                });
              }
            } else if (obj.status == 500) {
              core.toast('danger', false, obj.data);
            } else {
              core.toast('danger', true, obj.data);
            }
          } catch (e) {
            core.infobox('danger', 0, e.message + data);
          }
        });
      };
      
      var install = function() {
        if ($('form[data-toggle="modal"]').length > 0) {
          var $form = $('form[data-toggle="modal"]');
          var $target = $($form.attr('data-target'));
          
          $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),

            success: function (data, status) {
              try {
                var obj = $.parseJSON(data);
                if (obj.status == 200) {
                  $('.modal').modal('hide');
                  if (obj.data.length > 0) {
                    core.toast('success', false, obj.data);
                  }
                  eval(obj.event);
                } else if (obj.status == 301) {
                  var data = $('<textarea/>').html(obj.data).val();
                  $target.html(data);
                  eval(obj.event);
                }
              } catch (e) {
                $('.modal').modal('hide');
                core.infobox('danger', 0, e.message + data);
              }
            }
          });
        }
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
      
      var toast = function (type, sticky, value) {
        $().toastmessage('showToast', {
          text: value,
          sticky: sticky,
          position: 'bottom-right',
          type: type
        });
      };
      
      return {
        init: init,
        infobox: infobox,
        toast: toast,
        install: install
      };
    })();

    $.extend(true, window, {
      core: core
    });

    $(function () {
        core.init();
    });

}(jQuery));
