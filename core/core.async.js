(function ($) {
    
    var async = (function () {
      
      var time;
      
      var init = function () {
        $(document).ajaxSend(function(event, request, settings) {
          $('#loading').removeClass('hidden');
          core.async.time = new Date().getTime();
        });

        $(document).ajaxComplete(function(event, request, settings) {
          $('#loading').addClass('hidden');
          core.async.time = (new Date().getTime() - core.async.time) / 1000;
          $('#async').html('- queried in ' + core.async.time + 's');
        });
      
        $(document.body).on('click', '[data-toggle="async"]', function (e) {
          e.preventDefault();
          var loadurl = $(this).attr('data-query');
          var target = $($(this).attr('data-target'));
          $.get(loadurl, function (data) {
            try {
              var obj = $.parseJSON(data);
              if (obj.status == 200) {
                if (obj.event.length > 0) {
                  if (obj.data.length > 0) {
                    core.message.toast('success', false, obj.data);
                  }
                  eval(obj.event);
                } else {
                  var data = $('<textarea/>').html(obj.data).val();
                  target.html(data);
                  core.validator.init();
                  core.form.init();
                  core.proxy.init();
                }
              } else if (obj.status == 500) {
                core.message.toast('danger', false, obj.data);
              } else {
                core.message.toast('danger', true, obj.data);
              }
            } catch (e) {
              core.message.infobox('danger', 0, e.message + data);
            }
          });
          return false;
        });

        $('[data-toggle="select"]').on('change', function (e) {
          e.preventDefault();
          var loadurl = $(this).attr('data-query') + encodeURIComponent($(this).val());
          var target = $($(this).attr('data-target'));
          $.get(loadurl, function (data) {
            try {
              var obj = $.parseJSON(data);
              if (obj.status == 200) {
                if (obj.event.length > 0) {
                  if (obj.data.length > 0) {
                    core.message.toast('success', false, obj.data);
                  }
                  eval(obj.event);
                } else {
                  var data = $('<textarea/>').html(obj.data).val();
                  target.html(data);
                  core.validator.init();
                  core.form.init();
                  core.proxy.init();
                }
              } else if (obj.status == 500) {
                core.message.toast('danger', false, obj.data);
              } else {
                core.message.toast('danger', true, obj.data);
              }
            } catch (e) {
              core.message.infobox('danger', 0, e.message + data);
            }
          });
          return false;
        });
        
        $(document.body).on('submit', '[data-toggle="post"]', function (e) {
          e.preventDefault();
          var loadurl = $(this).attr('data-query');
          var target = $($(this).attr('data-target'));
          $.post(loadurl, $(this).serialize(), function (data) {
            try {
              var obj = $.parseJSON(data);
              if (obj.status == 200) {
                if (obj.event.length > 0) {
                  if (obj.data.length > 0) {
                    core.message.toast('success', false, obj.data);
                  }
                  eval(obj.event);
                } else {
                  var data = $('<textarea/>').html(obj.data).val();
                  target.html(data);
                }
              } else if (obj.status == 500) {
                core.message.toast('danger', false, obj.data);
              } else {
                core.message.toast('danger', true, obj.data);
              }
            } catch (e) {
              core.message.infobox('danger', 0, e.message + data);
            }
          });
          return false;
        });
      };
      
      return {
        init: init,
        time: time
      };
    })();

    $.extend(true, window, {
      core: {
        async: async
      }
    });

    $(function () {
        core.async.init();
    });

}(jQuery));
