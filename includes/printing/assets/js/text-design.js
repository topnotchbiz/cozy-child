var design = {
	zIndex: 1,
	output: [],
	designs: {},
	success_uploads: {},
	remaining_cnt: 0,
	totalQty: 0,
	prints_uploaded: false,
	dropzone: null,
	canvas_width: 1000,
	detail_row_height: 60,
	ini: function() {
		design.designer.fonts = {};
		design.designer.fontActive = {};
		design.designer.initFonts();
	},
	designer: {
		fonts: {},
		fontActive: {},
		initFonts: function() {
			jQuery('.ndx-TextFont').css('display', 'none');

			var currFont = jQuery('.ndx-TextFont').val();

			var html =
				'<span class="ndx-SpanFont" data-index="' +
				jQuery('.ndx-TextFont')[0].selectedIndex +
				'" style="font-family:' +
				currFont +
				'">';
			html += currFont;
			html += '</span>';

			jQuery('.ndx-TextFont').after(html);

			jQuery('#ndx-FontsListScroller').height(jQuery('.ndx-TextFont').children().length * 100);

			document.addEventListener(
				'scroll',
				function(event) {
					if (event.target.id === 'ndx-Fonts-list') {
						var scrollTop = event.target.scrollTop;
						design.designer.loadFonts(parseInt(scrollTop / 100));
					}
				},
				true
			);
		},
		loadFonts: function(index) {
			var listLen = jQuery('.ndx-TextFont').children().length;
			var containerHei = jQuery('.ndx-Fonts-listContainer').height();

			var selInd = 0;

			if (index >= 0) {
				selInd = index;
			} else {
				selInd = jQuery('.ndx-TextFont')[0].selectedIndex;

				jQuery('#ndx-Fonts-list').scrollTop(selInd * 100);
			}

			var startInd = Math.max(selInd - 2, 0);

			var endInd = Math.min(startInd + parseInt(containerHei / 100) + 4, listLen);

			if (listLen - parseInt(containerHei / 100) < startInd) {
				startInd = listLen - parseInt(containerHei / 100) - 2;
			}

			if (startInd == endInd) {
				console.log('error: startInd is equals to endInd');
				return;
			}

			jQuery('#active-googlefonts').remove();
			jQuery('#ndx-FontsListScroller').empty();

			var sampleTxt = design.item.get()[0].item.text.split('\n')[0];
			var activefonts = '';

			for (var i = startInd; i < endInd; i++) {
				var opt = jQuery('.ndx-TextFont option:nth-child(' + (i + 1) + ')').attr('value');
				var html =
					'<div class="ndx-FontListItem" data-font="' +
					opt +
					'" data-index="' +
					i +
					'" style="top:' +
					i * 100 +
					'px;">';
				html += '<div class="ndx-FontListItem-font">';
				html += '<span style="font-family:' + opt + '">' + sampleTxt + '</span>';
				html += '</div>';
				html += '<div class="ndx-FontListItem-name">' + opt + '</div>';
				html += '</div>';

				jQuery('#ndx-FontsListScroller').append(html);

				activefonts += opt.replace(/ /g, '+') + ':400,700|';
			}

			jQuery('head').append(
				"<link id='active-googlefonts' href='https://fonts.googleapis.com/css?family=" +
					activefonts.substring(0, activefonts.length - 1) +
					"' rel='stylesheet' type='text/css'>"
			);
		},
		addFonts: function(val) {
			var id = design.item.get().attr('id');

			if (jQuery('#selected-font-' + id).length > 0) {
				var prevFont = jQuery('#selected-font-' + id).data('font');

				if (prevFont != val) {
					jQuery('#selected-font-' + id).remove();

					var fontStr = val.replace(/ /g, '+') + ':400,700|';

					jQuery('head').append(
						"<link id='selected-font-" +
							id +
							"' data-font='" +
							val +
							"' href='https://fonts.googleapis.com/css?family=" +
							fontStr.substring(0, fontStr.length - 1) +
							"' rel='stylesheet' type='text/css'>"
					);
				}
			} else {
				var fontStr = val.replace(/ /g, '+') + ':400,700|';

				jQuery('head').append(
					"<link id='selected-font-" +
						id +
						"' data-font='" +
						val +
						"' href='https://fonts.googleapis.com/css?family=" +
						fontStr.substring(0, fontStr.length - 1) +
						"' rel='stylesheet' type='text/css'>"
				);
			}
		},
		removeFont: function() {
			var id = design.item.get().attr('id');
			jQuery('#selected-font-' + id).remove();
		},
		changeFont: function(e) {
			var selected_font = jQuery(e).val();
			design.text.update('fontfamily', selected_font);
		},
		initDesigner: function(all) {
			if (all) {
				jQuery('.ndx-Design-printableArea .content-inner').html('');
			}

			jQuery('.ndx-TextInput-textarea').val('');
			jQuery('.ndx-OutlineControl-value')
				.val(0)
				.trigger('change');
			jQuery('.ndx-ColorSwatch').val('#000000');
			jQuery('.ndx-ColorSwatch').spectrum({
				preferredFormat: 'hex',
				showInput: true,
				allowEmpty: true,
			});
			jQuery('.ndx-PanelContainer .ndx-NavHeader-close').trigger('click');
			this.changeCenterItem(false);
			this.changeAlign();
		},
		changeCenterItem: function(state) {
			if (typeof state == 'undefined' || state == true) {
				jQuery('.ndx-CenteringTool .ndx-ToolsMenuButton').removeClass('isDisabled');
			} else {
				jQuery('.ndx-CenteringTool .ndx-ToolsMenuButton').addClass('isDisabled');
			}
		},
		changeAlign: function(align) {
			if (typeof align === 'undefined') {
				align = 'center';
			}

			jQuery('.ndx-AlignmentTool .ndx-ToolsMenuButton.isActive').removeClass('isActive');
			jQuery('.ndx-AlignmentTool .ndx-ToolsMenuButton.ndx-Align-' + align).addClass('isActive');
		},
		changeLayering: function(d) {
			jQuery('.ndx-LayeringTool .ndx-ToolsMenuButton').addClass('isDisabled');

			switch (d) {
				case 'upper':
					jQuery('.ndx-LayeringTool .ndx-Layering-upper').removeClass('isDisabled');
					break;
				case 'lower':
					jQuery('.ndx-LayeringTool .ndx-Layering-lower').removeClass('isDisabled');
					break;
			}
		},
	},
	text: {
		create: function() {
			var item = {};

			if ( !!jQuery('.ndx-DesignStyle-button.isActive').length ) {
				item.design = jQuery('.ndx-DesignStyle-button.isActive').data('style');
			}

			if (typeof type == 'undefined') {
				item.type = 'text';
				item.remove = true;
				item.rotate = true;
			} else {
				item.type = type;
				item.remove = false;
				item.edit = false;
			}

			var o = this.getValue();

			item.text = o.text;
			item.fontFamily = o.fontFamily;
			item.color = o.color;
			item.stroke = o.stroke;
			item.strokeWidth = o.strokeWidth;

			var div = document.createElement('div');
			var node = document.createTextNode(o.text);
			div.appendChild(node);
			div.style.fontSize = o.fontSize;
			div.style.fontFamily = o.fontFamily;

			var svgNS = 'http://www.w3.org/2000/svg',
				text = document.createElementNS(svgNS, 'text');

			text.setAttributeNS(null, 'fill', o.color);
			text.setAttributeNS(null, 'x', 0);
			text.setAttributeNS(null, 'y', 0);
			text.setAttributeNS(null, 'text-anchor', 'middle');
			text.setAttributeNS(null, 'font-size', o.fontSize);
			text.setAttributeNS(null, 'font-family', o.fontFamily);

			if (typeof o.fontWeight != 'undefined') {
				text.setAttributeNS(null, 'font-weight', o.fontWeight);
			}

			if (typeof o.strokeWidth != 'undefined' && o.strokeWidth != 0) {
				text.setAttributeNS(null, 'stroke', o.stroke);
				text.setAttributeNS(null, 'stroke-width', o.strokeWidth / 50 + '%');
				text.setAttributeNS(null, 'stroke-linecap', 'round');
				text.setAttributeNS(null, 'stroke-linejoin', 'round');
			}
			if (typeof o.rotate != 'undefined') {
				text.setAttributeNS(null, 'transform', o.rotate);
			}

			var texts = o.text.split('\n');
			text.textContent = '';
			var fontSize = text.getAttribute('font-size').split('px');

			for (var i = 0; i < texts.length; i++) {
				var tspan = document.createElementNS(svgNS, 'tspan');
				var dy = 0;
				// if (i > 0) dy = fontSize[0];
				if (i > 0) dy = '1.117em';
				tspan.setAttributeNS(null, 'x', '50%');
				tspan.setAttributeNS(null, 'dy', dy);
				var content = document.createTextNode(texts[i]);
				tspan.appendChild(content);
				text.appendChild(tspan);
			}

			if (typeof o.style != 'undefined') {
				text.setAttributeNS(null, 'style', o.style);
			}

			var g = document.createElementNS(svgNS, 'g');
			g.id = Math.random();
			g.appendChild(text);

			var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
			svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
			svg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
			svg.appendChild(g);

			item.file = '';
			item.confirmColor = false;
			item.svg = svg;

			design.item.create(item);

			setTimeout(function() {
				design.text.setSize(design.item.get());
			}, 200);
		},
		getValue: function() {
			var o = {};
			o.text = jQuery('.ndx-TextInput-textarea').val();
			o.color = jQuery('.ndx-TextColor').val();
			o.fontSize = 40;
			o.fontFamily = jQuery('.ndx-TextFont').val();
			o.stroke = jQuery('.ndx-OutlineColor').val();
			o.strokeWidth = jQuery('.ndx-OutlineControl-value').val();

			o.alignItemC = false;

			var align = jQuery('.ndx-AlignmentTool .ndx-ToolsMenuButton.isActive').data('align');

			if (align) {
				o.align = align;
			} else {
				o.align = 'center';
			}

			if (jQuery('.ndx-Style-italic').hasClass('isActive')) {
				o.Istyle = 'italic';
			}

			if (jQuery('.ndx-Style-bold').hasClass('isActive')) {
				o.weight = 'bold';
			}

			if (jQuery('.ndx-Style-underline').hasClass('isActive')) {
				o.decoration = 'underline';
			}

			return o;
		},
		setValue: function(o) {
			jQuery('.ndx-TextInput-textarea').val(o.text);
			jQuery('.ndx-TextFont')
				.val(o.fontFamily)
				.trigger('change');
			jQuery('.ndx-OutlineControl-value')
				.val(o.strokeWidth)
				.trigger('change');
			jQuery('.ndx-OutlineColor').val(o.stroke);
			jQuery('.ndx-TextColor').val(o.color);

			jQuery('.ndx-ColorSwatch').spectrum({
				preferredFormat: 'hex',
				showInput: true,
				allowEmpty: true,
			});

			if (o.alignItemC) {
				design.designer.changeCenterItem();
			}

			if (typeof o.align == 'undefined') o.align = 'center';
			else design.designer.changeAlign(o.align);

			design.designer.changeLayering();

			if (design.zIndex - o.zIndex > 5) {
				design.designer.changeLayering('upper');
			}

			if (o.zIndex > 1) {
				design.designer.changeLayering('lower');
			}

			if (typeof o.Istyle != 'undefined' && o.Istyle == 'italic')
				jQuery('.ndx-Style-italic').addClass('isActive');
			else jQuery('.ndx-Style-italic').removeClass('isActive');

			if (typeof o.weight != 'undefined' && o.weight == 'bold') jQuery('.ndx-Style-bold').addClass('isActive');
			else jQuery('.ndx-Style-bold').removeClass('isActive');

			if (typeof o.decoration != 'undefined' && o.decoration == 'underline')
				jQuery('.ndx-Style-underline').addClass('isActive');
			else jQuery('.ndx-Style-underline').removeClass('isActive');
		},
		update: function(label, value) {
			var e = design.item.get();

			if (e.length == 0) return;

			var txt = e.find('text');

			if (typeof label != 'undefined' && label != '') {
				var obj = document.getElementById(e.attr('id'));

				obj.item.alignItemC = true;

				switch (label) {
					case 'fontfamily':
						txt[0].setAttributeNS(null, 'font-family', value);
						obj.item.fontFamily = value;
						design.designer.addFonts(value);

						setTimeout(function() {
							design.text.setSize(design.item.get());
						}, 200);
						break;
					case 'color':
						var color = jQuery('.ndx-TextColor').val();
						txt[0].setAttributeNS(null, 'fill', color);
						obj.item.color = color;
						break;
					case 'text':
						var text = jQuery('.ndx-TextInput-textarea').val();
						jQuery('.layer.active span').html(text.substring(0, 20));
						obj.item.text = text;
						var texts = text.split('\n');
						var svgNS = 'http://www.w3.org/2000/svg';
						txt[0].textContent = '';
						var fontSize = txt[0].getAttribute('font-size').split('px');
						for (var i = 0; i < texts.length; i++) {
							var tspan = document.createElementNS(svgNS, 'tspan');
							var dy = 0;
							if (i > 0) dy = fontSize[0];
							tspan.setAttributeNS(null, 'dy', dy);
							tspan.setAttributeNS(null, 'x', '50%');
							var content = document.createTextNode(texts[i]);
							tspan.appendChild(content);
							txt[0].appendChild(tspan);
						}

						if (i > 1) {
							design.designer.changeAlign('center');
						}

						this.setSize(e);
						break;
					case 'alignL':
						obj.item.align = 'left';
						design.text.align(e, 'left');
						break;
					case 'alignC':
						obj.item.align = 'center';
						design.text.align(e, 'center');
						break;
					case 'alignR':
						obj.item.align = 'right';
						design.text.align(e, 'right');
						break;
					case 'alignItemC':
						var editor_width = jQuery('.ndx-Design-printableArea .content-inner').width();
						var editor_height = jQuery('.ndx-Design-printableArea .content-inner').height();
						e.css('left', (editor_width - e.width()) / 2 + 'px');
						e.css('top', (editor_height - e.height()) / 2 + 'px');
						obj.item.alignItemC = false;
						break;
					case 'styleI':
						var o = jQuery('.ndx-Style-italic');
						if (o.hasClass('isActive')) {
							o.removeClass('isActive');
							txt.css('font-style', 'normal');
							obj.item.Istyle = 'normal';
						} else {
							o.addClass('isActive');
							txt.css('font-style', 'italic');
							obj.item.Istyle = 'italic';
						}

						this.setSize(e);
						break;
					case 'styleB':
						var o = jQuery('.ndx-Style-bold');
						if (o.hasClass('isActive')) {
							o.removeClass('isActive');
							txt.css('font-weight', 'normal');
							obj.item.weight = 'normal';
						} else {
							o.addClass('isActive');
							txt.css('font-weight', 'bold');
							obj.item.weight = 'bold';
						}
						this.setSize(e);
						break;
					case 'styleU':
						var o = jQuery('.ndx-Style-underline');
						if (o.hasClass('isActive')) {
							o.removeClass('isActive');
							txt.css('text-decoration', 'none');
							obj.item.decoration = 'none';
						} else {
							o.addClass('isActive');
							txt.css('text-decoration', 'underline');
							obj.item.decoration = 'underline';
						}
						this.setSize(e);
						break;
					case 'layeringL':
						var zIndex = obj.item.zIndex;
						var lowerObj = document.getElementById('item-' + parseInt((zIndex - 6) / 5));
						lowerObj.item.zIndex = zIndex;
						lowerObj.setAttribute('id', 'item-' + parseInt((zIndex - 1) / 5));
						lowerObj.style.zIndex = zIndex;
						obj.item.zIndex = zIndex - 5;
						obj.setAttribute('id', 'item-' + parseInt((zIndex - 6) / 5));
						obj.style.zIndex = zIndex - 5;

						design.designer.changeLayering();

						if (design.zIndex - obj.item.zIndex > 5) {
							design.designer.changeLayering('upper');
						}

						if (obj.item.zIndex > 1) {
							design.designer.changeLayering('lower');
						}

						break;
					case 'layeringU':
						var zIndex = obj.item.zIndex;
						var upperObj = document.getElementById('item-' + parseInt((zIndex + 4) / 5));
						upperObj.item.zIndex = zIndex;
						upperObj.setAttribute('id', 'item-' + parseInt((zIndex - 1) / 5));
						upperObj.style.zIndex = zIndex;
						obj.item.zIndex = zIndex + 5;
						obj.setAttribute('id', 'item-' + parseInt((zIndex + 4) / 5));
						obj.style.zIndex = zIndex + 5;

						design.designer.changeLayering();

						if (design.zIndex - obj.item.zIndex > 5) {
							design.designer.changeLayering('upper');
						}

						if (obj.item.zIndex > 1) {
							design.designer.changeLayering('lower');
						}

						break;
					case 'outline-width':
						txt[0].setAttributeNS(null, 'stroke', jQuery('.ndx-OutlineColor').val());
						txt[0].setAttributeNS(null, 'stroke-width', value / 50 + '%');
						txt[0].setAttributeNS(null, 'stroke-linecap', 'round');
						txt[0].setAttributeNS(null, 'stroke-linejoin', 'round');
						obj.item.strokeWidth = value;
						break;
					case 'outline':
						txt[0].setAttributeNS(null, 'stroke', value);
						txt[0].setAttributeNS(
							null,
							'stroke-width',
							jQuery('.ndx-OutlineControl-value').val() / 50 + '%'
						);
						obj.item.stroke = value;
						break;
					default:
						txt[0].setAttributeNS(null, label, value);
						break;
				}
			}

			design.designer.changeCenterItem();

			design.prints_uploaded = false;
		},
		setSize: function(e) {
			var cloned_item = e.clone();
			cloned_item.attr('class', '');
			cloned_item.attr('style', '');
			cloned_item.css('overflow', 'hidden');
			cloned_item.css('position', 'absolute');
			cloned_item.css('width', '0');
			cloned_item.css('height', '0');
			cloned_item.appendTo('.ndx-Product-position--active .ndx-Design-printableArea .content-inner');

			var txt = cloned_item.find('text');
			var $w = parseFloat(txt[0].getBoundingClientRect().width);
			var $h = parseFloat(txt[0].getBoundingClientRect().height);
			e.css('width', $w + 'px');
			e.css('height', $h + 'px');
			var svg = e.find('svg'),
				width = svg[0].getAttribute('width'),
				height = svg[0].getAttribute('height'),
				view = svg[0].getAttribute('viewBox').split(' '),
				vw = (view[2] * $w) / width,
				vh = (view[3] * $h) / height;
			svg[0].setAttributeNS(null, 'width', $w);
			svg[0].setAttributeNS(null, 'height', $h);
			svg[0].setAttributeNS(null, 'viewBox', '0 0 ' + vw + ' ' + vh);

			cloned_item.remove();
		},
		align: function(e, type) {
			var txt = e.find('text');
			var tspan = e.find('tspan');

			switch (type) {
				case 'left':
					txt[0].setAttributeNS(null, 'text-anchor', 'start');
					break;
				case 'right':
					txt[0].setAttributeNS(null, 'text-anchor', 'end');
					break;
				default:
					txt[0].setAttributeNS(null, 'text-anchor', 'middle');
					break;
			}

			for (i = 0; i < tspan.length; i++) {
				switch (type) {
					case 'left':
						tspan[i].setAttributeNS(null, 'x', '0');
						break;
					case 'right':
						tspan[i].setAttributeNS(null, 'x', '100%');
						break;
					default:
						tspan[i].setAttributeNS(null, 'x', '50%');
						break;
				}
			}
		},
	},
	upload: {
		create: function(file) {
			var o = {};

			if ( !!jQuery('.ndx-DesignStyle-button.isActive').length ) {
				o.design = jQuery('.ndx-DesignStyle-button.isActive').data('style');
			}

			o.type = 'clipart';
			o.file = { 'name': file.name };
			o.remove = true;
			o.rotate = true;
			o.width = 100;
			o.height = 0;
			o.org_wid = 0;
			o.org_hei = 0;

			var reader = new FileReader();

			reader.onload = function(e) {
				var image = new Image();
				image.src = e.target.result;

				image.onload = function() {
					o.org_wid = this.width;
					o.org_hei = this.height;
					o.height = (100 * o.org_hei) / o.org_wid;

					var dataURL = reader.result;

					o.file['dataUrl'] = dataURL;

					var content =
						'<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 ' +
						o.height +
						'" width="100" height="' +
						o.height +
						'"><g><image x="0" y="0" width="100" height="' +
						o.height +
						'" xlink:href="' +
						dataURL +
						'" /></g></svg>';
					o.svg = jQuery.parseHTML(content);
					design.item.create(o);
				};
			};

			if (file) {
				reader.readAsDataURL(file);
			}
		},
		update: function(label) {
			var e = design.item.get();

			if (e.length == 0) return;

			if (typeof label != 'undefined' && label != '') {
				var obj = document.getElementById(e.attr('id'));

				obj.item.alignItemC = true;

				switch (label) {
					case 'alignItemC':
						var editor_width = jQuery(
							'.ndx-Product-position--active .ndx-Design-printableArea .content-inner'
						).width();
						var editor_height = jQuery(
							'.ndx-Product-position--active .ndx-Design-printableArea .content-inner'
						).height();
						e.css('left', (editor_width - e.width()) / 2 + 'px');
						e.css('top', (editor_height - e.height()) / 2 + 'px');
						obj.item.alignItemC = false;
						design.designer.changeCenterItem();
						break;
					case 'layeringL':
						var zIndex = obj.item.zIndex;
						var lowerObj = document.getElementById('item-' + parseInt((zIndex - 6) / 5));
						lowerObj.item.zIndex = zIndex;
						lowerObj.setAttribute('id', 'item-' + parseInt((zIndex - 1) / 5));
						lowerObj.style.zIndex = zIndex;
						obj.item.zIndex = zIndex - 5;
						obj.setAttribute('id', 'item-' + parseInt((zIndex - 6) / 5));
						obj.style.zIndex = zIndex - 5;

						design.designer.changeLayering();

						if (design.zIndex - obj.item.zIndex > 5) {
							design.designer.changeLayering('upper');
						}

						if (obj.item.zIndex > 1) {
							design.designer.changeLayering('lower');
						}

						break;
					case 'layeringU':
						var zIndex = obj.item.zIndex;
						var upperObj = document.getElementById('item-' + parseInt((zIndex + 4) / 5));
						upperObj.item.zIndex = zIndex;
						upperObj.setAttribute('id', 'item-' + parseInt((zIndex - 1) / 5));
						upperObj.style.zIndex = zIndex;
						obj.item.zIndex = zIndex + 5;
						obj.setAttribute('id', 'item-' + parseInt((zIndex + 4) / 5));
						obj.style.zIndex = zIndex + 5;

						design.designer.changeLayering();

						if (design.zIndex - obj.item.zIndex > 5) {
							design.designer.changeLayering('upper');
						}

						if (obj.item.zIndex > 1) {
							design.designer.changeLayering('lower');
						}

						break;
				}
			}

			design.prints_uploaded = false;
		},
		setValue: function(o) {
			if (o.alignItemC) {
				design.designer.changeCenterItem();
			}

			if (design.zIndex - o.zIndex > 5) {
				design.designer.changeLayering('upper');
			}

			if (o.zIndex > 1) {
				design.designer.changeLayering('lower');
			}
		},
	},
	item: {
		create: function(item) {
			jQuery('.ndx-Product-position--active .ndx-Design-printableArea').css('overflow', 'visible');

			var e = jQuery('.ndx-Product-position--active .ndx-Design-printableArea .content-inner'),
				span = document.createElement('span');
			var n = -1;

			jQuery('.ndx-Product-position--active .ndx-Design-printableArea .drag-item').each(function() {
				var index = jQuery(this)
					.attr('id')
					.replace('item-', '');
				if (index > n) n = parseInt(index);
			});

			var n = n + 1;

			span.className = 'drag-item-selected drag-item';
			span.id = 'item-' + n;
			span.item = item;
			item.id = n;

			jQuery(span).data('id', item.id);
			jQuery(span).data('type', item.type);
			jQuery(span).data('file', item.file);
			span.style.zIndex = item.zIndex = design.zIndex;
			design.zIndex = design.zIndex + 5;
			span.style.width = item.width;
			span.style.height = item.height;
			jQuery(span).append(item.svg);

			if (item.remove == true) {
				var remove = document.createElement('div');
				remove.className = 'item-remove-on fa-trash-o fa';
				remove.setAttribute('title', 'Remove Item');
				jQuery(span).append(remove);
			}

			var edit = document.createElement('div');
			edit.className = 'item-edit-on fa-pencil fa';

			jQuery(span).append(edit);

			e.append(span);

			if (jQuery(span).find('text').length > 0) {
				// Set width and height of SVG and its wrapper for proper dimensions
				var $width = jQuery(span)
						.find('text')[0]
						.getBBox().width,
					$height = jQuery(span)
						.find('text')[0]
						.getBBox().height;

				var svg = jQuery(span).find('svg')[0];
				svg.setAttribute('viewBox', '0 0 ' + $width + ' ' + $height);
				svg.setAttribute('width', $width);
				svg.setAttribute('height', $height);

				var text = jQuery(span).find('text')[0];
				text.setAttribute('x', '50%');
				text.setAttribute('y', 36.5);

				item.width = $width;
				item.height = $height;
			}

			var center = {},
				area = jQuery('.ndx-Product-position--active .ndx-Design-printableArea .content-inner');
			center.left = parseFloat((jQuery(area).width() - item.width) / 2);
			center.top = parseFloat((jQuery(area).height() - item.height) / 2);

			span.style.left = center.left + 'px';
			span.style.top = center.top - 20 + 'px';
			span.style.width = item.width + 'px';
			span.style.height = item.height + 'px';

			this.move(jQuery(span));
			this.resize(jQuery(span));

			if (item.rotate == true) this.rotate(jQuery(span));

			// jQuery('.ndx-App-menuOverlay').css('display', 'none');

			this.select(span);

			design.prints_uploaded = false;
		},
		move: function(e) {
			if (!e) e = jQuery('.drag-item-selected');
			e.draggable({
				scroll: false,
				containment: 'parent',
				drag: function(event, ui) {
					var e = ui.helper;

					var o = e.parent().parent();
					var left = o.css('left');
					left = parseFloat(left.replace('px', ''));

					var top = o.css('top');
					top = parseFloat(top.replace('px', ''));
					var width = o.css('width');
					width = parseFloat(width.replace('px', ''));

					var height = o.css('height');
					height = parseFloat(height.replace('px', ''));

					var $left = ui.position.left,
						$top = ui.position.top,
						$width = e.width(),
						$height = e.height();
					if ($left < 0 || $top < 0 || $left + $width > width || $top + $height > height) {
						e.data('block', true);
						e.css('border', '1px solid #FF0000');
					} else {
						e.data('block', false);
					}
				},
				stop: function(event, ui) {
					design.designer.changeCenterItem();
					ui.helper[0].item.alignItemC = true;
					design.prints_uploaded = false;
				},
			});
		},
		resize: function(e, handles) {
			if (typeof handles == 'undefined') handles = 'se';

			if (handles == 'se') {
				var auto = true;
				e = e;
			} else {
				var auto = false;
			}
			if (!e) e = jQuery('.drag-item-selected');

			e.resizable({
				minHeight: 15,
				minWidth: 15,
				aspectRatio: auto,
				handles: handles,
				containment: 'parent',
				start: function(event, ui) {},
				stop: function(event, ui) {
					design.designer.changeCenterItem();
					ui.helper[0].item.alignItemC = true;
					design.prints_uploaded = false;
				},
				resize: function(event, ui) {
					var e = ui.element;
					var o = e.parent().parent();
					var left = o.css('left');
					left = parseFloat(left.replace('px', ''));

					var top = o.css('top');
					top = parseFloat(top.replace('px', ''));
					var width = o.css('width');
					width = parseFloat(width.replace('px', ''));

					var height = o.css('height');
					height = parseFloat(height.replace('px', ''));

					var $left = parseFloat(ui.position.left),
						$top = parseFloat(ui.position.top),
						$width = parseFloat(ui.size.width),
						$height = parseFloat(ui.size.height);
					if ($left + $width > width || $top + $height > height) {
						e.data('block', true);
						e.css('border', '1px solid #FF0000');
						jQuery(this)
							.resizable('widget')
							.trigger('mouseup');
						if (parseFloat(left + $left + $width) > 490 || parseFloat(top + $top + $height) > 490) {
						}
					} else {
						e.data('block', false);
					}

					var svg = e.find('svg');

					svg[0].setAttributeNS(null, 'width', $width);
					svg[0].setAttributeNS(null, 'height', $height);
					svg[0].setAttributeNS(null, 'preserveAspectRatio', 'none');
				},
			});
		},
		rotate: function(e) {
			if (typeof e != Object) var o = jQuery(e);
			else var o = e;

			o.rotatable({
				angle: 0,
				stop: function(event, ui) {
					o.data('rotate', parseFloat(ui.angle.stop));
					design.prints_uploaded = false;
				},
			});
		},
		select: function(e) {
			jQuery('.ndx-Product-position--active .ndx-Design-printableArea').css('overflow', 'visible');

			if ( jQuery(window).width() > 1024 ) {
				var o = jQuery(e),
					type = o.data('type');

				switch (type) {
					case 'clipart':
						jQuery('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item:last-child').trigger('click');
						jQuery('.ndx-UploadCard .ndx-DecorationTools').css('display', 'block');
						design.upload.setValue(e.item);
						break;
					case 'text':
						jQuery('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item:first-child').trigger('click');
						jQuery('.ndx-TextTools_Lg .ndx-DecorationTools').css('display', 'block');
						design.text.setValue(e.item);
						break;
				}
			}

			jQuery(e).addClass('drag-item-selected');
			jQuery(e).resizable({
				disabled: false,
				handles: 'e',
			});
			jQuery(e).draggable({
				disabled: false,
			});
		},
		unselect: function(e) {
			jQuery('.ndx-Design-printableArea .drag-item-selected').each(function() {
				jQuery(this).removeClass('drag-item-selected');
				jQuery(this).resizable({
					disabled: true,
					handles: 'e',
				});
				jQuery(this).draggable({
					disabled: true,
				});
			});

			jQuery('.ndx-DecorationTools').css('display', 'none');

			design.designer.initDesigner();
		},
		remove: function(e) {
			switch (e[0].item.type) {
				case 'clipart':
					design.dropzone.removeFile(e[0].item.file);
					break;

				default:
					design.designer.removeFont();
					break;
			}

			design.designer.initDesigner();
			e.remove();
			design.prints_uploaded = false;
		},
		get: function() {
			var e = jQuery('.ndx-Product-position--active .ndx-Design-printableArea .drag-item-selected');
			return e;
		},
		refresh: function(name) {
			var e = this.get();
			switch (name) {
				case 'rotate':
					e.rotatable('setValue', 0);
					break;
			}
		},
		flip: function(n) {
			var e = this.get(),
				svg = e.find('svg'),
				transform = '';
			var viewBox = svg[0].getAttributeNS(null, 'viewBox');
			var size = viewBox.split(' ');

			if (typeof e.data('flipX') == 'undefined') e.data('flipX', true);
			if (e.data('flipX') === true) {
				transform = 'translate(' + size[2] + ', 0) scale(-1,1)';
				e.data('flipX', false);
			} else {
				transform = 'translate(0, 0) scale(1,1)';
				e.data('flipX', true);
			}
			var g = jQuery(svg[0]).children('g');
			if (g.length > 0) g[0].setAttributeNS(null, 'transform', transform);
		},
		updateSize: function(w, h) {
			var e = design.item.get(),
				svg = e.find('svg'),
				view = svg[0].getAttributeNS(null, 'viewBox'),
				width = svg[0].getAttributeNS(null, 'width'),
				height = svg[0].getAttributeNS(null, 'height');

			if (e.length == 0) return;

			view = view.split(' ');
			svg[0].setAttributeNS(null, 'width', w);
			svg[0].setAttributeNS(null, 'height', h);
			svg[0].setAttributeNS(null, 'viewBox', '0 0 ' + (w * view[2]) / width + ' ' + (h * view[3]) / height);
			jQuery(e).css({
				width: w + 'px',
				height: h + 'px',
			});
		},
	},
	convert: {
		radDeg: function(rad) {
			if (rad.indexOf('rotate') != -1) {
				var v = rad.replace('rotate(', '');
				v = v.replace('rad)', '');
			} else {
				var v = parseFloat(rad);
			}

			var deg = (v * 180) / Math.PI;

			if (deg < 0) deg = 360 + deg;
			return Math.round(deg);
		},
		px: function(value) {
			if (value.indexOf('px') != -1) {
				value = value.replace('px', '');
			}

			return parseFloat(value);
		},
	},
	svg: {
		items: function() {
			jQuery('.ndx-Product-position').each(function(index) {
				if (jQuery(this).find('.ndx-Design-printableArea .drag-item').length <= 0) {
					design.output[index] = null;
					return;
				}

				var area = {};
				var wrap_wid = jQuery(this).find('.ndx-Design-printableArea').width();
				var wrap_hei = jQuery(this).find('.ndx-Design-printableArea').height();

				area.width = design.canvas_width;
				area.height = parseInt(area.width / wrap_wid * wrap_hei);

				var ratio = area.width / wrap_wid;

				var inner_wid = jQuery(this)
					.find('.ndx-Design-printableArea .content-inner')
					.width();
				var inner_hei = jQuery(this)
					.find('.ndx-Design-printableArea .content-inner')
					.height();
				var offset_wid = ( wrap_wid - inner_wid ) / 2;
				var offset_hei = ( wrap_hei - inner_hei ) / 2;

				console.log(offset_wid, offset_hei);

				var obj = [];

				design.output[index] = {};
				design.output[index].texts = [];
				design.output[index].images = [];

				jQuery(this)
					.find('.ndx-Design-printableArea .drag-item')
					.each(function(i, dragItem) {
						obj[i] = {};
						obj[i].top = design.convert.px( jQuery(dragItem).css('top') ) + offset_hei;
						obj[i].left = design.convert.px( jQuery(dragItem).css('left') ) + offset_wid;
						obj[i].width = design.convert.px( jQuery(dragItem).css('width') );
						obj[i].height = design.convert.px( jQuery(dragItem).css('height') );
						obj[i].boxWid = design.convert.px(
							jQuery(dragItem)
								.find('svg')[0]
								.getAttribute('viewBox')
								.split(' ')[2]
						);
						obj[i].boxHei = design.convert.px(
							jQuery(dragItem)
								.find('svg')[0]
								.getAttribute('viewBox')
								.split(' ')[3]
						);

						var svg = jQuery(dragItem).find('svg');
						svg_clone = jQuery(svg).clone();
						svg_clone.attr('width', obj[i].boxWid);
						svg_clone.attr('height', obj[i].boxHei);

						if (typeof jQuery(dragItem).data('rotate') != 'undefined') {
							obj[i].angle = parseFloat(jQuery(dragItem).data('rotate'));

							svg_clone
								.find('g')
								.attr(
									'transform',
									'rotate(' +
										(obj[i].angle / Math.PI) * 180 +
										' ' +
										obj[i].boxWid / 2 +
										' ' +
										obj[i].boxHei / 2 +
										')'
								);
						}

						// svg_clone.prepend(
						// '<rect width="' + obj[i].boxWid + '" height="' + obj[i].boxHei + '" fill="#ff0000"></rect>'
						// );

						obj[i].svg = svg_clone;

						var image = jQuery(svg).find('image');
						if (typeof image[0] == 'undefined') {
							obj[i].img = false;
						} else {
							obj[i].img = true;
							var src = jQuery(image).attr('xlink:href');
							obj[i].src = src;
							obj[i].file = jQuery(dragItem)[0].item.file;
						}

						obj[i].design = dragItem.item.design;
						obj[i].zIndex = dragItem.style.zIndex;

						if (svg_clone.find('text').length > 0) {
							var text = {};
							text.text = jQuery(dragItem).text();
							text.font = svg_clone.find('text').attr('font-family');
							text.color = svg_clone.find('text').attr('fill');
							text.stroke = svg_clone.find('text').attr('stroke');
							text.strokeWidth = svg_clone.find('text').attr('stroke-width');
							obj[i].text = text;
						}
					});

				obj.sort(function(obj1, obj2) {
					return obj1.zIndex - obj2.zIndex;
				});

				if ( obj.length > 0 ) {
					var canvas = document.createElement('canvas');
					canvas.width = area.width;
					canvas.height = area.height;
					var context = canvas.getContext('2d');

					var bgImg = new Image();
					bgImg.src = jQuery(this)
						.find('.ndx-Product-photo-main')
						.attr('src');

					var imgWid = parseFloat(
						jQuery(this)
							.find('.ndx-Product-photo-main')
							.attr('width')
					);
					var imgHei = parseFloat(
						jQuery(this)
							.find('.ndx-Product-photo-main')
							.attr('height')
					);

					var bgHei = canvas.height;
					var bgWid = (area.height / imgHei) * imgWid;

					if ( jQuery(this).find('.ndx-Product-photo-main').width() == jQuery(this).width() ) {
						bgHei = (area.width / imgWid) * imgHei;
						bgWid = canvas.width;
					}

					context.translate(canvas.width / 2, canvas.height / 2);
					context.drawImage(bgImg, -bgWid / 2, -bgHei / 2, bgWid, bgHei);
					context.translate(-canvas.width / 2, -canvas.height / 2);

					for (var i = 0; i < obj.length; i++) {
						if (typeof obj[i] != 'undefined') {
							var item = obj[i];

							if (item.img == true) {
								var img = new Image();
								img.src = item.src;

								context.translate(
									(item.left + item.width / 2) * ratio,
									(item.top + item.height / 2) * ratio
								);
								context.rotate(item.angle);
								context.drawImage(
									img,
									(-item.width / 2) * ratio,
									(-item.height / 2) * ratio,
									item.width * ratio,
									item.height * ratio
								);
								context.rotate(-item.angle);
								context.translate(
									-(item.left + item.width / 2) * ratio,
									-(item.top + item.height / 2) * ratio
								);

								item.file.design = item.design;

								design.output[index].images.push(item.file);
							} else {
								item.svg = new XMLSerializer().serializeToString(item.svg[0]);
								context.drawSvg(
									item.svg,
									(item.left / item.width) * item.boxWid,
									(item.top / item.height) * item.boxHei,
									item.width * ratio,
									item.height * ratio
								);

								var tempCanv = document.createElement('canvas');
								tempCanv.width = area.width;
								tempCanv.height = area.height + design.detail_row_height;
								var tempCntxt = tempCanv.getContext('2d');

								var tempWid = area.width / Math.sqrt(2) - 50;
								var tempHei = area.height / Math.sqrt(2) - 50;
								var itemWid = tempWid;
								var itemHei = (tempWid * item.height) / item.width;

								if (itemHei > tempHei) {
									itemHei = tempHei;
									itemWid = (tempHei * item.width) / item.height;
								}

								tempCntxt.translate(area.width / 2, area.height / 2);

								tempCntxt.drawSvg(item.svg, -item.boxWid / 2, -item.boxHei / 2, itemWid, itemHei);

								tempCntxt.translate(-area.width / 2, -area.height / 2);

								var bg_detailRect = design.svg.createRect(area.width);
								tempCntxt.drawSvg(bg_detailRect, 0, area.height);

								var txt = 'Text: ' + item.text.text;
								txt += ' | ' + 'Font: ' + item.text.font;
								txt += ' | ' + 'Colour: ' + item.text.color;

								if (
									typeof item.text.strokeWidth !== 'undefined' &&
									typeof item.text.stroke != 'undefined'
								) {
									txt += ' | ' + 'Outline: ' + item.text.stroke;
									txt += ' | ' + 'Outline-Width: ' + item.text.strokeWidth;
								}

								var text_svg = design.svg.createDetailText(txt);
								tempCntxt.drawSvg(text_svg, 0, area.height);

								design.output[index].texts.push({
									'canvas': tempCanv,
									'design': item.design,
									'name': item.text.text + '.png'
								});
								tempCntxt.restore();
							}

							context.restore();
						}
					}

					design.output[index].detail = canvas;
				}
			});
		},
		createRect: function(width) {
			var svgNS = 'http://www.w3.org/2000/svg',
				rect = document.createElementNS(svgNS, 'rect');
			rect.setAttributeNS(null, 'width', width);
			rect.setAttributeNS(null, 'height', design.detail_row_height);
			rect.setAttributeNS(null, 'fill', '#000000');
			var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
			svg.setAttributeNS(null, 'width', width);
			svg.setAttributeNS(null, 'height', design.detail_row_height);
			svg.setAttributeNS(null, 'viewBox', '0 0 ' + width + ' ' + design.detail_row_height);
			svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
			svg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
			svg.appendChild(rect);
			svg = jQuery('<div></div>')
				.html(jQuery(svg))
				.html();
			return svg;
		},
		createDetailText: function(content) {
			var svgNS = 'http://www.w3.org/2000/svg',
				tspan = document.createElementNS(svgNS, 'tspan'),
				text = document.createElementNS(svgNS, 'text');

			tspan.setAttributeNS(null, 'x', '50%');
			tspan.setAttributeNS(null, 'dy', 40);

			text.setAttributeNS(null, 'fill', '#FFFFFF');
			text.setAttributeNS(null, 'x', '50%');
			text.setAttributeNS(null, 'y', 0);
			text.setAttributeNS(null, 'text-anchor', 'middle');
			text.setAttributeNS(null, 'font-size', 15 + 'px');
			text.setAttributeNS(null, 'font-family', 'Arial');
			content = document.createTextNode(content);
			tspan.appendChild(content);
			text.appendChild(tspan);

			var g = document.createElementNS(svgNS, 'g');
			g.id = Math.random();
			g.appendChild(text);

			var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
			svg.setAttributeNS(null, 'width', design.canvas_width);
			svg.setAttributeNS(null, 'height', design.detail_row_height);
			svg.setAttributeNS(null, 'viewBox', '0 0 ' + design.canvas_width + ' ' + design.detail_row_height);
			svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
			svg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
			svg.appendChild(g);
			svg = jQuery('<div></div>')
				.html(jQuery(svg))
				.html();
			return svg;
		},
	},
};

(function($) {
	$(document).ready(function() {
		design.ini();

		$('.ndx-TextInput-textarea').on('keyup', function() {
			var textVal = $('.ndx-TextInput-textarea').val();

			if (textVal == '') {
				return;
			}

			var selected = design.item.get();

			if (!selected.length || selected[0].item.type == 'clipart') {
				return;
			}

			design.text.update('text', textVal);
		});

		$('.ndx-TextInput-button').on('click', function() {
			var textVal = $('.ndx-TextInput-textarea').val();

			if (textVal == '') {
				return;
			}

			var selected = design.item.get();

			if (!selected.length || selected[0].item.type == 'clipart') {
				design.text.create();
			}

			if ( $(window).width() < 1025 ) {
				$('.ndx-PanelContainer .ndx-NavHeader-close').trigger('click');
			}
		});

		$('.ndx-TextFont').on('change', function() {
			design.designer.changeFont(this);
			$('.ndx-SpanFont').css('font-family', $(this).val());
			$('.ndx-SpanFont').text($(this).val());
		});

		$('.ndx-TextColor').change(function() {
			design.text.update('color', $(this).val());
		});

		$('.ndx-OutlineColor').change(function() {
			design.text.update('outline', $(this).val());
		});

		$('.ndx-ColorSwatch').spectrum({
			preferredFormat: 'hex',
			showInput: true,
			allowEmpty: true,
		});

		var slider = $('#ndx-OutlineControl-slider').slider({
			min: 0,
			max: 100,
			range: 'min',
			value: 0,
			slide: function(event, ui) {
				$('.ndx-OutlineControl-value').val(ui.value);
				design.text.update('outline-width', ui.value);
			},
		});

		$('.ndx-OutlineControl-value').change(function() {
			slider.slider('value', $(this).val());
		});

		$(document).on('click', '.ndx-CenteringTool .ndx-ToolsMenuButton:not(.isDisabled)', function() {
			design.text.update('alignItemC', true);
			design.designer.changeCenterItem(false);
		});

		$(document).on('click', '.ndx-AlignmentTool .ndx-ToolsMenuButton', function() {
			design.text.update(['alignL', 'alignC', 'alignR'][$(this).index()]);
			design.designer.changeAlign($(this).data('align'));
		});

		$(document).on('click', '.ndx-LayeringTool .ndx-Layering-upper', function() {
			design.text.update('layeringU');
		});

		$(document).on('click', '.ndx-LayeringTool .ndx-Layering-lower', function() {
			design.text.update('layeringL');
		});

		$(document).on('click', '.ndx-FontStyleTool .ndx-Style-italic', function() {
			design.text.update('styleI');
		});

		$(document).on('click', '.ndx-FontStyleTool .ndx-Style-bold', function() {
			design.text.update('styleB');
		});

		$(document).on('click', '.ndx-FontStyleTool .ndx-Style-underline', function() {
			design.text.update('styleU');
		});

		$('.ndx-IconCardTool--font').on('click', function() {
			if (design.item.get().length > 0) {
				$(this)
					.closest('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard')
					.find('.ndx-OverlayFonts')
					.css('display', '');
				design.designer.loadFonts();
			}
		});

		$('.ndx-Content-main').on('mousedown', function(e) {
			var topCurso = !document.all ? e.clientY : event.clientY;
			var leftCurso = !document.all ? e.clientX : event.clientX;
			var mouseDownAt = document.elementFromPoint(leftCurso, topCurso);

			if ( 
				mouseDownAt.parentNode.className == 'ndx-Design-printableArea' && 
				$('.ndx-Sidebar-container').css('display') == 'none' 
			) {
				design.item.unselect();
				e.preventDefault();
			}
		});

		$(document).on('click', '.drag-item', function() {
			design.item.select(this);
		});

		$(document).on('click', '.item-remove-on', function() {
			design.item.remove( $(this).closest('.drag-item') );
		});

		$(document).on('click', '.item-edit-on', function() {
			if ( $(window).width() < 1025 ) {
				var e = $(this).closest('.drag-item')[0];
				var o = $(e),
					type = o.data('type');

				switch (type) {
					case 'clipart':
						jQuery('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item:last-child').trigger('click');
						jQuery('.ndx-UploadCard .ndx-DecorationTools').css('display', 'block');
						design.upload.setValue(e.item);
						break;
					case 'text':
						jQuery('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item:first-child').trigger('click');
						jQuery('.ndx-TextTools_Lg .ndx-DecorationTools').css('display', 'block');
						design.text.setValue(e.item);
						break;
				}
			}			
		});

		$(document).on('click', '.ndx-FontListItem', function() {
			var font = $(this).data('font');

			$('.ndx-SpanFont').css('font-family', font);
			$('.ndx-SpanFont').text(font);

			$(this)
				.closest('.ndx-OverlayFonts')
				.css('display', 'none');

			$('.ndx-TextFont')
				.val(font)
				.trigger('change');
		});

		design.dropzone = new Dropzone('#ndx-UploadDropzone', {
			url: '/',
			autoProcessQueue: false,
			createImageThumbnails: false
		});

		design.dropzone.on('addedfile', function(file) {
			design.upload.create(file);

			if ( $(window).width() < 768 ) {
				$('.ndx-ContentCard[data-index="3"] .ndx-NavHeader-close').trigger('click');
			}
		});

		$('.ndx-VerticalToolbar-buttonGroup[data-index="0"] .ndx-VerticalToolbar-item').on('click', function() {
			var ind = $(this).data('index');
			$('.ndx-DesignStyle-button.isActive').removeClass('isActive');
			$('.ndx-DesignStyle-button[data-style="' + ['embroid', 'screen', 'digital'][ind] + '"]').addClass('isActive');
			$('.ndx-PanelContainer .ndx-ContentCard').css('display', 'none');
			$('.ndx-ContentCard[data-index="1"]').css('display', '');
			$('.ndx-VerticalToolbar-buttonGroup').css('display', 'none');
			$('.ndx-VerticalToolbar-buttonGroup[data-index="1"]').css('display', '');
		});

		$('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item').on('click', function() {
			design.item.unselect();

			$('.ndx-App-menuContainer').addClass('ndx-App-opened');
			$('.ndx-VerticalToolbar-buttonGroup[data-index="0"]').hide();
			$('.ndx-VerticalToolbar-buttonGroup[data-index="1"]').show();

			$('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item.ndx-VerticalToolbar-item--active').removeClass(
				'ndx-VerticalToolbar-item--active'
			);

			$(this).addClass('ndx-VerticalToolbar-item--active');

			var index = $(this).data('index');

			$('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard').css('display', 'none');
			$('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard[data-index="' + (index + 1) + '"]').css('display', '');
		});

		$('.ndx-DesignStyle-button').on('click', function() {
			$('.ndx-DesignStyle-button.isActive').removeClass('isActive');
			$(this).addClass('isActive');
			$('.ndx-PanelContainer .ndx-ContentCard').css('display', 'none');
			$('.ndx-ContentCard[data-index="1"]').css('display', '');
			$('.ndx-VerticalToolbar-buttonGroup').css('display', 'none');
			$('.ndx-VerticalToolbar-buttonGroup[data-index="1"]').css('display', '');
		});

		$('.ndx-Welcome_Lg-button').on('click', function() {
			design.item.unselect();

			var index = $(this).data('index');

			$('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item.ndx-VerticalToolbar-item--active').removeClass(
				'ndx-VerticalToolbar-item--active'
			);
			$('.ndx-VerticalToolbar-buttonGroup[data-index="1"] .ndx-VerticalToolbar-item[data-index="' + index + '"]').addClass(
				'ndx-VerticalToolbar-item--active'
			);

			$('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard').css('display', 'none');
			$('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard[data-index="' + (index + 1) + '"]').css('display', '');
		});

		$('.ndx-NavHeader-back').on('click', function() {
			if ($(this).closest('.ndx-Overlay').length > 0) {
				$(this)
					.closest('.ndx-Overlay')
					.css('display', 'none');
			} else {
				$(this)
					.closest('.ndx-ContentCard')
					.css('display', 'none');
				$('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard:first-child').css('display', '');
			}

			$('.ndx-App-menuContainer').removeClass('ndx-App-opened');
		});

		$('.ndx-NavHeader-close').on('click', function() {
			$('.ndx-VerticalToolbar-item.ndx-VerticalToolbar-item--active').removeClass(
				'ndx-VerticalToolbar-item--active'
			);
			$('.ndx-Overlay').css('display', 'none');
			$('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard').css('display', 'none');
			$('.ndx-PanelContainer > .ndx-Panel > .ndx-ContentCard:first-child').css('display', '');
			$('.ndx-VerticalToolbar-buttonGroup[data-index="0"]').css('display', '');
			$('.ndx-VerticalToolbar-buttonGroup[data-index="1"]').css('display', 'none');
			$('.ndx-App-menuContainer').removeClass('ndx-App-opened');
		});

		$('.ndx-Menu.ndx-CanvasToolbar-menu').on('click', function() {
			$('.ndx-Menu.ndx-CanvasToolbar-menu.ndx-CanvasToolbar-menu--active').removeClass(
				'ndx-CanvasToolbar-menu--active'
			);
			$(this).addClass('ndx-CanvasToolbar-menu--active');

			$('.ndx-Product-position.ndx-Product-position--active').removeClass('ndx-Product-position--active');
			$('.ndx-Product-position:nth-child(' + ($(this).index() + 1) + ')').addClass(
				'ndx-Product-position--active'
			);

			design.item.unselect();
		});

		$('.ndx-Sidebar-container .ndx-NavHeader-close').on('click', function() {
			$('.ndx-App-menuContainer').show();
			$('.ndx-Variation-Gallery').show();
			$('.ndx-Sidebar-container').hide();

			$('.btn-get-price').show();
			$('.btn-add-to-cart').hide();

			design.designs = {};
		});
	});
})(jQuery);
