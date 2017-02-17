$(function() {

  $(document.body).on('click', '[data-toggle="async"]', function(e) {
    e.preventDefault();
    var loadurl = $(this).attr('data-query');
    var target = $($(this).attr('data-target'));
    $.get(loadurl, function(data) {
      try {
        var obj = $.parseJSON(data);
        if (obj.status == 200) {
          if (obj.event.length > 0) {
            if (obj.data.length > 0) {
              toast('success', false, obj.data);
            }
            eval(obj.event);
          } else {
            var data = $('<textarea/>').html(obj.data).val();
            target.html(data);
          }
        } else if (obj.status == 500) {
          toast('danger', false, obj.data);
        } else {
          toast('danger', true, obj.data);
        }
      } catch (e) {
        infobox('danger', 0, e.message + data);
      }
    });
    return false;
  });

  $('[data-toggle="select"]').on('change', function(e) {
    e.preventDefault();
    var loadurl = $(this).attr('data-query') + encodeURIComponent($(this).val());
    var target = $($(this).attr('data-target'));
    $.get(loadurl, function(data) {
      try {
        var obj = $.parseJSON(data);
        if (obj.status == 200) {
          if (obj.event.length > 0) {
            if (obj.data.length > 0) {
              toast('success', false, obj.data);
            }
            eval(obj.event);
          } else {
            var data = $('<textarea/>').html(obj.data).val();
            target.html(data);
          }
        } else if (obj.status == 500) {
          toast('danger', false, obj.data);
        } else {
          toast('danger', true, obj.data);
        }
      } catch (e) {
        infobox('danger', 0, e.message + data);
      }
    });
    return false;
  });

});