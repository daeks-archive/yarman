(function ($) {
    
    var editor = (function () {
      var init = function () {
        if ($('#nav-editor').val().length > 0) {
          var element = $('#nav-editor');
          core.editor.load(element.attr('data-query') + encodeURIComponent(element.val()), element.attr('data-target'), element.attr('data-mode'));
        }
      
        $('#nav-editor').on('change', function (e) {
          e.preventDefault();
          return core.editor.load($(this).attr('data-query') + encodeURIComponent($(this).val()), $(this).attr('data-target'), $(this).attr('data-mode'));
        });
        
        $('#nav-emulator').on('change', function (e) {
          var editor = ace.edit('module-content');
          editor.destroy();
          editor.container.remove();
          $('#module-wrapper').html('<div id="module-content" style="width: 100%"></div>');
          $('[data-toggle="modal"]').prop('disabled', true);
        });
      };
      
      var load = function (loadurl, target, mode) {
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
                  minLines: size,
                  maxLines: size
                });
                editor.getSession().setMode('ace/mode/' + mode);
                editor.setValue(data, 1);
                editor.gotoLine(1);
                $('[data-toggle="modal"]').prop('disabled', false);
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
      };
      
      var save = function (loadurl, target) {
        var editor = ace.edit(target);
        $.post(loadurl, 'data=' + editor.getValue(), function (data) {
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
      };
      
      return {
        init: init,
        load: load,
        save: save
      };
    })();

    $.extend(true, window, {
      core: {
        editor: editor
      }
    });

    $(function () {
        core.editor.init();
    });

}(jQuery));