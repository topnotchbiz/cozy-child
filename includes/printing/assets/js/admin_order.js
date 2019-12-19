jQuery(document).ready(function ($) {
	$('.btn-download-prints').on('click', function() {
		$(this).closest('.printing-items').find('tbody a').each(function(i, e) {
			download( e );
		});
	});
});

/* Download an img */
function download( link ) {
	var evt = new MouseEvent("click", {
		"view": window,
		"bubbles": true,
		"cancelable": true
	});

	link.dispatchEvent(evt);
}