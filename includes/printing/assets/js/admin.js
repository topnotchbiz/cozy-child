/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function ($) {
	$('.jcrop-pos-img').each(function(i, e) {
		var jcrop_api;

		$(e).Jcrop({
			onSelect: function( c ) {
				$(e).closest('.pos-design-area').find('.pos-img-bounding').val( c.x + ',' + c.y + ',' + c.x2 + ',' + c.y2 );
			}
		}, function() {
			$(e).closest('.pos-design-area').find('.pos-img-area').val( $(e).width() + ',' + $(e).height() );

			jcrop_api = this;

			var bounding = $(e).closest('.pos-design-area').find('.pos-img-bounding').val();
			var c = bounding.split(',');

			if ( bounding && bounding !== '' ) {
				jcrop_api.setSelect( [ c[0], c[1], c[2], c[3] ] );
			}
		});
	});

	$('.add_position').click(function () {
		$(this).closest('table').find('tbody').append($(this).data('row'));
		$('body').trigger('row_added');
		return false;
	});

	$('#printing_position_rows, .printing_price_rows').on('click', '.remove', function () {
		$(this).closest('tr').remove();
		return false;
	});

	$('#printing_position_rows, .printing_price_rows').sortable({
		items: 'tr',
		cursor: 'move',
		axis: 'y',
		handle: '.sort',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function (event, ui) {
			ui.item.css('background-color', '#f6f6f6');
		},
		stop: function (event, ui) {
			ui.item.removeAttr('style');
		},
	});

	$('.seelct_all_fonts').click(function () {
		$('.fonts-list input[type="checkbox"]').attr('checked', 'checked');
	});

	$('.deseelct_all_fonts').click(function () {
		$('.fonts-list input[type="checkbox"]').removeAttr('checked');
	});

	$(document).on('click', '.wc-printing-images .image-wrapper', function (e) {
		e.preventDefault();

		var file_frame, t = $(this);

		if ( file_frame ) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.select_image = wp.media({
			title: 'Choose Image',
			multiple: false
		});

		file_frame.on('select', function () {
			var images = file_frame.state().get('selection').toJSON();

			for ( var i = 0; i < images.length; i++ ) {
				var image = images[i];

				if ( image.type === 'image' ) {
					var id = image.id, _image$sizes = image.sizes;

					_image$sizes = _image$sizes === undefined ? {} : _image$sizes;

					var thumbnail = _image$sizes.thumbnail, full = _image$sizes.full;
					var url = thumbnail ? thumbnail.url : full.url;
					var preview_html = '<img src="' + url + '" />';
					$(t).find('.image').html(preview_html);
					$(t).closest('td').find('input').val(id);
					$(t).addClass('has-image');
				}
			}

			$(t).closest('.woocommerce_variation').addClass('variation-needs-update');
      $('button.cancel-variation-changes, button.save-variation-changes').removeAttr('disabled');
      $('#variable_product_options').trigger('woocommerce_variations_input_changed');
		});

		file_frame.open();
	});

	$(document).on('click', '.wc-printing-images .remove-position-image', function (e) {
		e.preventDefault();
		e.stopPropagation();

		var t = $(this);
		$(t).closest('.image-wrapper').find('.image').html('<img src="/wp-content/uploads/woocommerce-placeholder-150x150.png">');
		$(t).closest('td').find('input').val('');
		$(t).closest('.image-wrapper').removeClass('has-image');
	});

	$('#publish').on('click', function(e) {
		var errorMsg = '';

		$('#printing_position_rows tr').each(function() {
			var posTxt = $(this).find('input[name^="positions[label]"]').val();

			if ( !posTxt || posTxt == '' ) {
				errorMsg = 'Text should be added for each position.';
				return;
			}

			var size = $(this).find('select[name^="positions[size]"]').val();

			if ( !size || size == '' ) {
				errorMsg = 'Size should be selected for each position.';
				return;
			}        
		});

		if ( errorMsg == '' && $('#printing_position_rows tr').length > 0 ) {
			errorMsg = 'Prices should be added to at least one of printing types.';

			$('.printing_price_rows').each(function() {
				if ( $(this).find('tr').length > 0 ) {
					errorMsg = '';
				}
			});
		}

		if ( errorMsg == '' ) {
			$('#printing_tab_data .printing_price_rows').each(function() {
				if ( $(this).find('tr').length > 0 && $(this).closest('table').find('tfoot input').val() == '' ) {
					errorMsg = 'You need to input minimal quantities for all printing types before submitting.';
					return;
				}
			});
		}


		if ( ! errorMsg ) {
			return;
		}

		e.preventDefault();
		alert( errorMsg );
	});
});
