(function($){
    
    var charts = (function() {
      var init = function() {
        $('[data-provider="gauge"]').each(function() {
          var g = new JustGage({
            id: $(this).attr('id'),
            value: $(this).attr('data-query'),
            decimals: 2,
            min: ((typeof $(this).attr('data-query-min') == 'undefined')? 0 : $(this).attr('data-query-min')),
            max: ((typeof $(this).attr('data-query-max') == 'undefined')? 100 : $(this).attr('data-query-max')),
            title: ((typeof $(this).attr('title') == 'undefined')? '' : $(this).attr('title')),
            label: ((typeof $(this).attr('label') == 'undefined')? '' : $(this).attr('label'))
          });
        });
      };
      
      return {
        init: init
      };
    })();

    $.extend(true, window, {
      core: {
        charts: charts
      }
    });

    $(function() {
        core.charts.init();
    });

}(jQuery));
