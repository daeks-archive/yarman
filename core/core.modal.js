$(function() {

	$('.modal').on('loaded.bs.modal', centerModal);
	$('.modal').on('hidden.bs.modal', function(e) {
		$('.modal').removeData('bs.modal');
		$('#modal-content').html('');
	});

	$(window).on('resize', function() {
		$('.modal:visible').each(centerModal);
	});

	$('.modal').on('success.form.fv', function(event) {
		if ($('form[data-async]').length > 0) {
			var $form = $('form[data-async]');
			var $target = $($form.attr('data-target'));

			$.ajax({
				type: $form.attr('method'),
				url: $form.attr('action'),
				data: $form.serialize(),

				success: function(data, status) {
					try {
						var obj = $.parseJSON(data);
						if (obj.status == 200) {
							if (obj.event.length > 0) {
								$('.modal').modal('hide');
								if (obj.data.length > 0) {
									toast('success', false, obj.data);
								}
								eval(obj.event);
							} else {
								var data = $('<textarea/>').html(obj.data).val();
								$target.html(data);
							}
						} else if (obj.status == 500) {
							$('.modal').modal('hide');
							toast('danger', false, obj.data);
						} else {
							$('.modal').modal('hide');
							toast('danger', true, obj.data);
						}
					} catch (e) {
						$('.modal').modal('hide');
						infobox('danger', 0, e.message + data);
					}
				}
			});
			$(this).prop('disabled', true);
			event.preventDefault();
		}
	});

	function centerModal() {
		$(this).css('display', 'block');
		var $dialog = $(this).find('.modal-dialog');
		var offset = ($(window).height() - $dialog.height()) / 2;
		var bottomMargin = $dialog.css('marginBottom');
		bottomMargin = parseInt(bottomMargin);
		if (offset < bottomMargin) offset = bottomMargin;
		$dialog.css("margin-top", offset);
		$('#data').formValidation({
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			submitButtons: 'button[form="data"]'
		});
		$('[data-focus]').focus();
		$("[data-focus]").val($("[data-focus]").val());
	}

});