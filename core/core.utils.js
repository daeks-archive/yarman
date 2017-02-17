
function toast(type, sticky, value) {
  $().toastmessage('showToast', {
    text: value,
    sticky: sticky,
    position: 'bottom-right',
    type: type
  });
}

function infobox(type, time, value) {
  $('#infobox').html('<div class="alert alert-' + type + '" tabindex="-1"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span>' + value.replace('Unexpected token <', '').trim() + '</span></div>');
  $('#infobox').show();
  if (time > 0) {
    setTimeout(function() {
      $("#infobox").hide();
    }, time);
  }
}

$(function() {
  $(function() {
    $("[data-toggle='tooltip']").tooltip();
  });
  $(function() {
    $("[data-toggle='popover']").popover({
      trigger: 'hover'
    });
  });
  
  $(document).ajaxSend(function(event, request, settings) {
    $('#loading').removeClass('hidden');
  });

  $(document).ajaxComplete(function(event, request, settings) {
    $('#loading').addClass('hidden');
  });

});