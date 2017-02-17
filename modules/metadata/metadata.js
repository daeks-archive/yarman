$(function () {
  var height = $(document).height() - 170;
  var size = Math.round(height / 20);
   
  $("#romlist").attr("size", size);
  $("#romlist").css("height", height);
   
  $(window).resize(function() {
    $("#romlist").css("height", $(document).height() - 170);
  });

  $(window).trigger('resize');
  
  $('#sys').on('change', function(e) {
    e.preventDefault();
    $('#tab').html('');
    return false;
  });
});
