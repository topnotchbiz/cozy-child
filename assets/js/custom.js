(function( $ ) {
	$(document).ready(function() {
		$('.edgtf-single-product-images .thumbnail-slider').slick({
			dots: false,
			arrows: true,
			infinite: false,
			slidesToShow: 4,
			slidesToScroll: 4,
			asNavFor: '.edgtf-single-product-images  .woocommerce-product-gallery'
		});

		$('.edgtf-single-product-images  .woocommerce-product-gallery').slick({
			dots: false,
			arrows: true,
			infinite: false,
			fade: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			asNavFor: '.edgtf-single-product-images .thumbnail-slider'
		});

		$(document).on('click', '.thumbnail-slider .woocommerce-product-gallery__image', function() {
			$('.edgtf-single-product-images .woocommerce-product-gallery').slick( 'slickGoTo', parseInt($(this).data('slick-index')) );
		});

		$(document).on('click', '.edgtf-message.variation-terms-alert .edgtf-close', function(e) {
			e.preventDefault();

			$(this).closest('.edgtf-message.variation-terms-alert').slideUp('slow', function() {
				$(this).remove();
			});
		});

		setInterval(function() {
			var today = new Date();
			var tomorrow = new Date();
			tomorrow.setDate(today.getDate()+1);
			tomorrow.setHours(0)
			tomorrow.setMinutes(0)
			tomorrow.setSeconds(0)
			tomorrow.setMilliseconds(0)

			var t = Date.parse(tomorrow) - Date.parse(today);
			var seconds = Math.floor( (t/1000) % 60 );
			var minutes = Math.floor( (t/1000/60) % 60 );
			var hours = Math.floor( (t/(1000*60*60)) % 24 );
			hours,
			minutes,
			seconds

			$('.delivery-info .remaining').text(hours + 'h ' + minutes + 'm ' + seconds + 's');
		}, 1000);
	});
} )( jQuery );