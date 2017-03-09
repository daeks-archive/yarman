(function ($) {
    
    var proxy = (function () {
      var init = function () {
        $('[data-toggle="simpleproxy"]').bind('initproxy',function () {
          if (Modernizr && Modernizr.draganddrop && Modernizr.filereader && Modernizr.blob) {
            if (typeof $($(this).attr('data-key')) == 'undefined') {
              core.message.infobox('danger', 5000, 'No field #id specified');
            } else {
              $(this).fileupload({
                url: $(this).attr('data-query'),
                formData: {id : $($(this).attr('data-key')).val()},
                paramName: 'object',
                dropZone: null,
                done: function (e, data) {
                  try {
                    var obj = $.parseJSON(data.result);
                    if (obj.status == 200) {
                      if (obj.event.length > 0) {
                        if (obj.data.length > 0) {
                          core.message.toast('success', false, obj.data);
                        }
                        eval(obj.event);
                      } else {
                        var data = $('<textarea/>').html(obj.data).val();
                        $($(this).attr('data-target')).val(data).trigger('input');
                      }
                    } else if (obj.status == 500) {
                      core.message.toast('danger', false, obj.data);
                    } else {
                      core.message.toast('danger', true, obj.data);
                    }
                  } catch (e) {
                    core.message.infobox('danger', 0, e.message + '<br>' + data);
                  }
                }
              });
            }
          } else {
            core.message.infobox('danger', 5000, 'JQuery FileUpload is not supported by your browser!');
          }
        });
        $('[data-toggle="previewproxy"]').bind('initproxy',function () {
          if (Modernizr && Modernizr.draganddrop && Modernizr.filereader && Modernizr.blob) {
            if (typeof $($(this).attr('data-key')) == 'undefined') {
              core.message.infobox('danger', 5000, 'No field #id specified');
            } else {
              $(this).fileupload({
                url: $(this).attr('data-query'),
                formData: {id : $($(this).attr('data-key')).val(), data: $('#modal-data').serialize()},
                paramName: 'object',
                dropZone: $('.dropzone'),
                done: function (e, data) {
                  try {
                    var obj = $.parseJSON(data.result);
                    if (obj.status == 200) {
                      $('.modal').modal('hide');
                      if (obj.event.length > 0) {
                        if (obj.data.length > 0) {
                          core.message.toast('success', false, obj.data);
                        }
                        eval(obj.event);
                      } else {
                        var data = $('<textarea/>').html(obj.data).val();
                        $($(this).attr('data-target')).val(data).trigger('input');
                      }
                    } else if (obj.status == 500) {
                      $('.modal').modal('hide');
                      core.message.toast('danger', false, obj.data);
                    } else {
                      $('.modal').modal('hide');
                      core.message.toast('danger', true, obj.data);
                    }
                  } catch (e) {
                    $('.modal').modal('hide');
                    core.message.infobox('danger', 0, e.message + '<br>' + data);
                  }
                }
              });
            }
          } else {
            core.message.infobox('danger', 5000, 'JQuery FileUpload is not supported by your browser!');
          }
        });
        $('[data-toggle="simpleproxy"]').trigger('initproxy');
        $('[data-toggle="previewproxy"]').trigger('initproxy');
      };
            
      return {
        init: init
      };
    })();

    $.extend(true, window, {
      core: {
        proxy: proxy
      }
    });

    $(function () {
        core.proxy.init();
    });

}(jQuery));
