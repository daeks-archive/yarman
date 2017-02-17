$(function() {

  $(document).ready(function() {
    $('.search').focus();
  });
  
  $('.search').keypress(function(e) {
    if(e.which == 13) {
        window.location = encodeURI($(this).attr("data-target") + $(this).val().replace(" ", "+"));
        e.preventDefault();
    }
  });

	$('.search').typeahead({
		source: function(query, process) {
			$.ajax({
				url: this.$element.attr("data-query"),
				type: 'GET',
				dataType: this.$element.attr("data-type"),
				data: 'q=' + query,
				success: function(data) {
					process(data);
				}
			});
		},
		updater: function(item) {
			window.location = encodeURI(this.$element.attr("data-target") + item.replace(" ", "+"));
			return item;
		}
	});
	
	$('.changesearch').on('click', function(event) {
    $('.search').attr('data-query', $(this).attr('data-query'));
    $('.search').attr('data-type', $(this).attr('data-type'));
    $('.search').attr('data-target', $(this).attr('data-target'));
    $('.searchicon').attr('src', $(this).attr('data-icon'));
    $('.search').attr('placeholder', 'Search');
    $('.search').prop('disabled', false );
    $('.search').focus();
    event.preventDefault();
	});
	
});