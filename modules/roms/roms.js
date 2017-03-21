(function ($) {
    
    var roms = (function () {
      var init = function () {
        $(window).resize(function () {
          var height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - 200 - $('.dropzone').height();
          if ($('#beta')) {
            height = height - $('#beta').height() - 50;
            $('#beta').bind('closed.bs.alert', function () {
              var height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - 250 - $('.dropzone').height();
              var size = Math.round(height / 20);
              
              $("#nav-emulator").attr("size", size);
              $("#nav-emulator").css("height", height);
              $("#rom-data").css("height", height + 150 + $('.dropzone').height());
            });
          }
          var size = Math.round(height / 20);
           
          $("#nav-emulator").attr("size", size);
          $("#nav-emulator").css("height", height);
          $("#rom-data").css("height", height + 150 + $('.dropzone').height());
        });

        $(window).trigger('resize');

        $("#rom-data").bind("scroll", function () {
          $(window).trigger('resize');
        });
      };
      
      return {
        init: init
      };
    })();

    $.extend(true, window, {
      core: {
        roms: roms
      }
    });

    $(function () {
        core.roms.init();
    });

}(jQuery));