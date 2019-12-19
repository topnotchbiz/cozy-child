(function($) {
	$(document).ready(function() {
		if ($('.embroidered_buttons .embroidered_price_btn').length) {
			$('.embroidered_buttons .embroidered_price_btn').removeClass('close');
			$('.embroidered_buttons .embroidered_price_btn:not(:first-child)').addClass('close');
		}

		if ($('.embroidered_price_details .embroidered_price').length) {
			$('.embroidered_price_details .embroidered_price').removeClass('close');
			$('.embroidered_price_details .embroidered_price:not(:first-child)').addClass('close');
		}

		$('.embroidered_price_btn').click(function() {
			var current_tab_index = $('.embroidered_buttons .embroidered_price_btn').index(jQuery(this));
			$('.embroidered_price_btn').removeClass('close');
			$('.embroidered_price_btn').addClass('close');
			$(this).removeClass('close');

			$('.embroidered_price').removeClass('close');
			$('.embroidered_price').addClass('close');
			$('.embroidered_price_details .embroidered_price:nth-child(' + (current_tab_index + 1) + ')').removeClass(
				'close'
			);
		});

		$('.btn-add-logo').on('click', function() {
			$('html, body').animate({scrollTop: 70});

			$('.edgtf-single-product-holder').closest('.edgtf-container').slideUp(200, function() {
				$('.ndx-App-holder').slideDown(400, function() {
					if ( ! $('.ndx-Variation-Gallery--slider').hasClass('slick-slider') ) {
						$('.ndx-Variation-Gallery--slider').slick({
							dots: false,
							arrows: true,
							infinite: false,
							slidesToShow: 10,
							slidesToScroll: 1,
							responsive: [{
								breakpoint: 1024,
								settings: {
									slidesToShow: 8
								}
							}, {
								breakpoint: 599,
								settings: {
									slidesToShow: 3
								}
							}]
						});						
					}

					$('footer').hide();
					$(window).trigger('resize');
				});
			});
		});

		$('.btn-cancel-design').on('click', function() {
			$('.btn-get-price').show();
			$('.btn-add-to-cart').hide();
			$('.ndx-Sidebar-container .ndx-NavHeader-close').trigger('click');

			$('.ndx-App-holder').slideUp(200, function() {
				$('.edgtf-single-product-holder').closest('.edgtf-container').slideDown(400, function() {
					$('.edgtf-single-product-images .thumbnail-slider').find('.slick-next').trigger('click');
					$('.edgtf-single-product-images  .woocommerce-product-gallery').find('.slick-next').trigger('click');
					
					$('footer').show();

					$('html, body').animate({scrollTop: 70});
				});
			});
		});

		$('.btn-get-price').on('click', function() {
			if ( design.prints_uploaded == false ) {
				design.svg.items();
				
				var empty = true;

				for ( var i = 0; i < design.output.length; i++ ) {
					if ( design.output[i] ) {
						empty = false;
					}
				}

				if ( empty ) {
					return false;
				}

				var printTypes = {
					'embroid': 'Embroidery Stitching',
					'screen': '1 Colour Screen Printing',
					'digital': 'Digital Full Colour Printing'
				};

				var today = new Date();
				var yyyy = today.getFullYear();
				var mm = today.getMonth()+1; 
				var dd = today.getDate();
				var time = today.getTime();

				if ( dd < 10 ) {
					dd = '0' + dd;
				} 

				if ( mm < 10 ) {
					mm = '0' + mm;
				} 

				var prefix = yyyy + '-' + mm + '-' + dd + '-' + time + '-';

				design.designs = {};

				for ( var i = 0; i < design.output.length; i++ ) {
					if ( design.output[i] ) {
						var posTxt = $('.ndx-Menu.ndx-CanvasToolbar-menu:nth-child(' + (i+1) + ') .ndx-MenuButton-text').text();

						design.designs[posTxt] = {};

						for (var j = 0; j < design.output[i].texts.length; j++) {
							var txt = design.output[i].texts[j];

							if ( typeof design.designs[posTxt][txt.design] == 'undefined' ) {
								design.designs[posTxt][txt.design] = [];
							}

							design.designs[posTxt][txt.design].push({
								dataUrl: txt.canvas.toDataURL(),
								name: prefix + posTxt + '-' + txt.design + '-' + txt.name,
							});
						}

						for (var j = 0; j < design.output[i].images.length; j++) {
							var img = design.output[i].images[j];

							if ( typeof design.designs[posTxt][img.design] == 'undefined' ) {
								design.designs[posTxt][img.design] = [];
							}

							design.designs[posTxt][img.design].push({
								dataUrl: img.dataUrl,
								name: prefix + posTxt + '-' + img.design + '-' + img.name,
							});
						}

						design.designs[posTxt]['detail'] = {
							dataUrl: design.output[i].detail.toDataURL(),
							name: prefix + posTxt + '-' + 'Detail.png',
						};
					}
				}
			}

			$('.prod-Item-details-decos').empty();

			for ( var pos in design.designs ) {
				var decoStr = '';

				for ( var type in design.designs[pos] ) {
					if ( type !== 'detail' ) {
						decoStr += printTypes[type] + ', ';
					}					
				}

				var html = '<div class="prod-Item-details-deco">';
				html += pos + ': ' + decoStr.replace(/(, $)/g, '');
				html += '</div>'

				$('.prod-Item-details-decos').append(html);
			}

			$(this).hide();
			$('.btn-add-to-cart').show();

			$('.ndx-App-menuContainer').hide();
			$('.ndx-Variation-Gallery').hide();
			$('.ndx-Sidebar-container').show();
		});
		
		$('.prod-Variation-Cnt .variation_term_qty').on('keydown', function() {
			if ( $('.prod-Variation-Cnt .tooltip.is-Active').length > 0 ) {
				$('.prod-Variation-Cnt .tooltip.is-Active').removeClass('is-Active');
			}
		});

		$('.prod-Variation-Cnt .variation_term_qty').on('keyup', function() {
			var totalQty = 0;
			orderInfo = [];

			$('.prod-Variation-Cnt .variation_term_qty').each(function() {
				var val = $(this).val();

				if ( val !== '' && val !== '0' ) {
					totalQty += parseInt( val );
					orderInfo.push({
						'variation_id': $(this).data('variation_id'),
						'attribute_pa_color': $(this).data('attribute_pa_color'),
						'attribute_pa_size': $(this).data('attribute_pa_size'),
						'qty': parseInt( $(this).val() )
					});
				}
			});

			if ( totalQty > 0 ) {

				var printOpts = $('.ndx-App-holder').data('printing-opts');
				var designCost = 0;
				var minQty = 0;

				for ( var pos in design.designs ) {
					var minTemp = Infinity;

					for ( var prtStyle in design.designs[pos] ) {
						if ( prtStyle !== 'detail' ) {
							var styleData = printOpts['prices'][prtStyle];

							for ( var i = 0; i < styleData['qty_from'][i]; i++ ) {
								if ( minTemp > parseInt( styleData['qty_from'][i] ) ) {
									minTemp = parseInt( styleData['qty_from'][i] );
								}								
							}
						}
					}

					if ( minQty < minTemp ) {
						minQty = minTemp;
					}
				}

				if ( totalQty < minQty ) {
					$('.prod-Design-costs').html( '<span class="print-msg">You need to add at least ' + minQty + ' items.</span>' );
					design.totalQty = 0;
				} else {
					for ( var pos in design.designs ) {
						for ( var prtStyle in design.designs[pos] ) {
							if ( prtStyle !== 'detail' ) {
								var styleData = printOpts['prices'][prtStyle];

								if ( styleData.hasOwnProperty('size') ) {
									for ( var i = 0; i < styleData['size'].length; i++ ) {
										var qtyFrom = parseFloat(styleData['qty_from'][i]);
										var qtyTo = parseFloat(styleData['qty_to'][i]);
										var size = styleData['size'][i];

										if ( qtyTo == '' ) {
											qtyTo = Number.POSITIVE_INFINITY;
										}

										var posInd = printOpts['positions']['label'].indexOf(pos);

										if ( ( totalQty >= qtyFrom ) && ( totalQty <= qtyTo ) && size == printOpts['positions']['size'][posInd] ) {
											var posCost = !!styleData['pos_cost'][i] ? parseFloat(styleData['pos_cost'][i]) : 0;
											var setupCost = !!styleData['setup_cost'][i] ? parseFloat(styleData['setup_cost'][i]) : 0;
											
											designCost += setupCost / qtyFrom + posCost;
											break;
										}
									}
								}
							}
						}
					}

					var disPrice = Math.round( (parseFloat( $('.ndx-App-holder').data('display-price') ) + designCost) * 100 ) / 100;
					var html = '<div class="prod-Design-costs-item">';
					html += '<span class="woocommerce-Price-amount amount">';
					html += '<span class="woocommerce-Price-currencySymbol">&#36;</span>' + disPrice.toFixed(2) + '</span>';
					html += ' each inc GST (' + totalQty + ' items)';
					html += '</div>';
					html += '<div class="prod-Design-costs-total"><span class="woocommerce-Price-amount amount">';
					html += '<span class="woocommerce-Price-currencySymbol">&#36;</span>' + ( disPrice * totalQty ).toFixed(2) + '</span>';
					html += ' total cost inc GST';
					html += '</div>';

					$('.prod-Design-costs').html( html );

					design.totalQty = totalQty;
				}
			}
		});

		$(document).on('click', '.btn-add-to-cart:not(.loading)', function() {		
			if ( design.totalQty > 0 ) {
				var $thisbutton = $( '.btn-add-to-cart' );

				if ( $thisbutton.is( '.ajax_add_to_cart' ) ) {

					if ( ! $thisbutton.attr( 'data-product_id' ) ) {
						return true;
					}

					$thisbutton.removeClass( 'added' );
					$thisbutton.addClass( 'loading' );

					if ( ! design.prints_uploaded ) {
						processDesigns();
					} else {
						addToCartDesigns();
					}				

					return false;
				}
			} else {
				$( $('.prod-Variation-Cnt .variation_term_qty').get(0) ).trigger('keyup');
			}
		});

		$(document).on('click', '.ndx-CanvasToolbar-changeViewButton', function() {
			var label = $(this).data('pos-label');

			$('.ndx-Variation-slide').each(function() {
				var posImages = $(this).find('.ndx-Variation-slide-img').data('pos-images');
				$(this).find('img').attr('src', posImages[label][0][0]);
			});
		});

		$('.ndx-Product-position .ndx-Design-posLabel').each(function(i, e) {
			$(this).text( $('.ndx-CanvasToolbar-changeViews .ndx-CanvasToolbar-menu:nth-child(' + (i+1) + ') .ndx-MenuButton-text').text() + ' View');
		});

		$(document).on('click', '.ndx-Variation-slide', function(e) {
			var posImages = $(this).find('.ndx-Variation-slide-img').data('pos-images');

			$('.ndx-CanvasToolbar-menu').each(function() {
				var index = $(this).index();
				var label = $(this).find('.ndx-CanvasToolbar-changeViewButton').data('pos-label');
		
				$(this).find('.ndx-Product img')
					.attr('src', posImages[label][0][0])
					.attr('width', posImages[label][1][1])
					.attr('height', posImages[label][1][2]);

				$('.ndx-Product-position:nth-child(' + (index+1) + ') .ndx-Product-photo-main')
					.attr('src', posImages[label][1][0])
					.attr('width', posImages[label][1][1])
					.attr('height', posImages[label][1][2]);
			});
		});

		$(document).on('click', '.edgtf-btn-close', function() {
			$(this).closest('.mfp-content').find('.mfp-close').trigger('click');
		});

		$(window).trigger('resize');
	});

	$(window).resize(function() {
		$('.ndx-Product-position').each(function() {
			var width = parseFloat($(this).width());
			var height = parseFloat($(this).height());

			var $img = $(this).find('img');
			var attrW = parseFloat($img.attr('width'));
			var attrH = parseFloat($img.attr('height'));

			if ( ( width / height ) > ( attrW / attrH ) ) {
				$img.width( attrW / attrH * height );
				$img.height( height );
			} else {
				$img.width( width );
				$img.height( attrH / attrW * width );				
			}

			$img.css('margin-left', ($(this).width() - $img.width())/2 + 'px' )
				.css('margin-top', ($(this).height() - $img.height())/2 + 'px' );

			var wid = $img.width();
			var hei = $img.height();

			$(this).find('.ndx-Design-printableArea')
				.width(wid)
				.height(hei)
				.css('left', ($(this).width() - $img.width())/2 + 'px' )
				.css('right', ($(this).width() - $img.width())/2 + 'px' )
				.css('top', ($(this).height() - $img.height())/2 + 'px' )
				.css('bottom', ($(this).height() - $img.height())/2 + 'px' );
		});

		$('.ndx-App-holder').css('padding-bottom', $('.ndx-Add-to-Cart-holder').outerHeight() + 'px');

		$('.ndx-Design-printableArea .content-inner').each(function() {
			if ( $(this).data('design-area') && $(this).data('design-area') !== '' ) {
				var area = $(this).data('design-area').split(',');
				var bounding = $(this).data('design-bounding').split(',');
				var width = parseFloat($(this).closest('.ndx-Design-printableArea').width());
				var height = parseFloat($(this).closest('.ndx-Design-printableArea').height());


				$(this).css('position', 'absolute')
					.css( 'width', ( parseFloat(bounding[2]) - parseFloat(bounding[0]) ) / parseFloat(area[0]) * width + 'px' )
					.css( 'height', ( parseFloat(bounding[3]) - parseFloat(bounding[1]) ) / parseFloat(area[1]) * height + 'px' )
					.css( 'left', parseFloat(bounding[0]) / parseFloat(area[0]) * width )
					.css( 'top', parseFloat(bounding[1]) / parseFloat(area[1]) * height )
					.css( 'border', '1px dashed #000' );
			}
		});
	});

	function processDesigns() {
		design.remaining_cnt = 0;
		design.success_uploads = {};

		for ( var pos in design.designs ) {
			for ( var typeKey in design.designs[pos] ) {
				if ( typeKey == 'detail') {
					design.remaining_cnt++;	
				} else {
					design.remaining_cnt += design.designs[pos][typeKey].length;
				}
			}
		}

		for ( var pos in design.designs ) {
			for ( var typeKey in design.designs[pos] ) {
				for (var j = 0; j < design.designs[pos][typeKey].length; j++) {
					$.ajax({
						type: 'post',
						dataType: 'json',
						url: woocommerce_params.ajax_url,
						data: {
							action: 'upload_print',
							dataUrl: design.designs[pos][typeKey][j].dataUrl,
							name: design.designs[pos][typeKey][j].name,
							pos: pos,
							type: typeKey
						},
						success: function (response) {
							appendSuccessUrls( response );
						}
					});
				}
			}

			$.ajax({
				type: 'post',
				dataType: 'json',
				url: woocommerce_params.ajax_url,
				data: {
					action: 'upload_print',
					dataUrl: design.designs[pos].detail.dataUrl,
					name: design.designs[pos].detail.name,
					pos: pos
				},
				success: function (response) {
					appendSuccessUrls( response, 'detail');
				}
			});
		}
	}
	
	function appendSuccessUrls( result, type ) {
		if ( ! design.success_uploads.hasOwnProperty(result.pos) ) {
			design.success_uploads[result.pos] = {};
		}

		if ( type === 'detail' ) {
			design.success_uploads[result.pos]['detail'] = result.url;
		} else {
			if ( ! design.success_uploads[result.pos].hasOwnProperty('img') ) {
				design.success_uploads[result.pos]['img'] = [];
			}

			design.success_uploads[result.pos]['img'].push({
				url: result.url,
				type: result.type
			});
		}

		design.remaining_cnt--;

		if ( design.remaining_cnt == 0 ) {
			design.prints_uploaded = true;
			addToCartDesigns();
		}
	}

	function addToCartDesigns() {
		var data = {};
		var $thisbutton = $( '.btn-add-to-cart' );

		$.each( $thisbutton.data(), function( key, value ) {
			data[key] = value;
		});

		data['order_info'] = orderInfo;

		var tmpUploads = {};

		for( var key in design.designs ) {
			tmpUploads[key] = design.success_uploads[key];
		}

		design.success_uploads = tmpUploads;

		data['uploads'] = design.success_uploads;

		if ( $('.prod-Design-comments textarea').val() ) {
			data['comment'] = $('.prod-Design-comments textarea').val();
		}

		data['action'] = 'prints_to_cart';

		// Trigger event
		$( document.body ).trigger( 'adding_to_cart', [ $thisbutton, data ] );

		// Ajax action
		$.post( wc_add_to_cart_params.ajax_url, data, function( response ) {

			if ( ! response ) {
				return;
			}

			if ( response.error ) {
				console.log( response );
				return;
			}

			$thisbutton.removeClass( 'loading' );

			var fragments = response.fragments;
			var cart_hash = response.cart_hash;

			// Block fragments class
			if ( fragments ) {
				$.each( fragments, function( key ) {
					$( key ).addClass( 'updating' );
				});
			}

			// Block widgets and fragments
			$( '.updating' ).fadeTo( '400', '0.6' ).block({
				message: null,
				overlayCSS: {
					opacity: 0.6
				}
			});

			// Changes button classes
			$thisbutton.addClass( 'added' );

			// Replace fragments
			if ( fragments ) {
				$.each( fragments, function( key, value ) {
					$( key ).replaceWith( value );
				});
			}

			// Unblock
			$( '.updating' ).stop( true ).css( 'opacity', '1' ).unblock();

			$('.prod-Item-details-items').empty();
			
			$('.prod-Variation-Cnt .variation_term_qty').each(function() {
				var val = $(this).val();

				if ( val !== '' && val !== '0' ) {
					var colour = $(this).closest('tr').find('.colour_name').text();
					var size = $('.prod-Variation-Cnt thead tr th:nth-child(' + ( $(this).closest('td').index() + 1 ) + ')').text();
					var html = '<div class="prod-Item-details-item">';
					html += colour + ', ' + size + ' &times; ' + val;
					html += '</div>'

					$('.prod-Item-details-items').append(html);
				}
			});
			
			$('.prod-Design-content .prod-Item-canvasContainer').empty();

			for ( var key in design.success_uploads) {
				var print = design.success_uploads[key].detail;
				$('.prod-Design-content .prod-Item-canvasContainer').append('<img src="' + print + '"/>');
			}

			$.magnificPopup.open({
				items: {
    			src: $('#added-to-cart-popup').html(),
    			type: 'inline'
  			}
			});
		});

		return true;
	}
})(jQuery);
