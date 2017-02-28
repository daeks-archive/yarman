(function ($) {
    
    var logs = (function () {
      var init = function () {
        $('#nav-logs').on('change', function (e) {
          e.preventDefault();
          var loadurl = $(this).attr('data-query') + encodeURIComponent($(this).val());
          var target = $(this).attr('data-target');
          var mode = $(this).attr('data-mode');
          $('#' + target).addClass('hidden');
          
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
                  var height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - 150;
                  var size = Math.round(height / 15);
               
                  $('#' + target).css("height", height);
                  $('#' + target).removeClass('hidden');
                  var editor = ace.edit(target);
                  editor.setOptions({
                    maxLines: size
                  });
                  editor.getSession().setMode('ace/mode/' + mode);
                  editor.setValue(data, -1);
                  editor.gotoLine(editor.getSession().getLength());
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
        init: init
      };
    })();

    $.extend(true, window, {
      core: {
        logs: logs
      }
    });

    $(function () {
        core.logs.init();
    });

}(jQuery));