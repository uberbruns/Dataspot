(function(w, d){

	// With Code From:
	// http://ejohn.org/projects/flexible-javascript-events/
	// http://de.andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
	// https://github.com/MattWilcox/Adaptive-Images

	var addEvent = function(obj, type, fn) {

		if (obj.attachEvent) {
			obj['e' + type + fn] = fn;
			obj[type + fn] = function() {obj['e' + type + fn](window.event);}
			obj.attachEvent('on'+type, obj[type + fn]);
		} else {
			obj.addEventListener(type, fn, false);
		}

	}


	var viewport = function() {

		var e = w, a = 'inner';
		if (!('innerWidth' in w)) {
			a = 'client';
			e = d.documentElement || d.body;
		}
		return { width : e[a+'Width'] , height : e[a+'Height'] }

	}


	var appendString = function(filename, string) {

		return filename.replace(/\.\w+$/,function(m){return"-"+string+m;});

	}


	var adaptImages = function() {

		var highPixelRatio = false;
		var noscriptElements = d.querySelectorAll("noscript.img");

		if(w.devicePixelRatio>1) {
			highPixelRatio=true;
		} else {
			var mQ= "(-o-min-device-pixel-ratio:3/2),(min-resolution:1.5dppx)";
			if (w.matchMedia&&w.matchMedia(mQ).matches) highPixelRatio = true;
		}


		for (var i = noscriptElements.length - 1; i >= 0; i--) {

			var getAttr = function(e, attr) { return e.getAttribute(attr); }

			var noscriptElement = noscriptElements[i];
			var imgSrc = getAttr(noscriptElement, "data-img-src");
			var imgAppendix = getAttr(noscriptElement, "data-appendix");
			var imgMaxViewportWidth = getAttr(noscriptElement, "data-max-vw");
			var imageElement = undefined;
			var vpWidth = viewport().width;
			var newImage = false;


			// Get or Create Image Object
			var dataImgId = getAttr(noscriptElement, "data-img-id");
			imageElement = d.getElementById(dataImgId);
			if (!dataImgId) {
				imageElement = new Image();
				newImage = true;
				imageElement.id = "img" + i;
				noscriptElement.setAttribute("data-img-id", imageElement.id);
			}


			// Image Source For Viewport
			if (imgAppendix && imgMaxViewportWidth) {
				var appendices = imgAppendix.split(",");
				var widths = imgMaxViewportWidth.split(",");
				for (var ii =  0; ii < Math.min(widths.length,appendices.length); ii++) {
					var appendix = appendices[ii];
					var width = parseInt(widths[ii]);
					if (width >= vpWidth) {
						imgSrc = appendString(imgSrc, appendix);
						break;
					}
				}
			}


			// Resolution
			if (highPixelRatio) {
				imgSrc = appendString(imgSrc, "2x");
			}


			// Set Image Source
			if (imageElement.src != imgSrc) {
				imageElement.src = imgSrc;
			}


			if (newImage) {

				// Transfer Attributes From Noscript to Image
				for (var ii = noscriptElement.attributes.length - 1; ii >= 0; ii--) {
					var attr = noscriptElement.attributes[ii];
					var name = attr.name;
					if (name.slice(0,8) == "data-img" && name != "data-img-src") {
						imageElement.setAttribute(name.slice(9), attr.value);
					};
				};

				// Add Element to DOM
				noscriptElement.parentNode.insertBefore(imageElement, noscriptElement);

			}

		}

	}

	adaptImages();
	addEvent(w, 'resize', adaptImages);

})(window, document);