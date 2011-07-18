/**
 * Verify if parameter 'child' is descendent of parameter 'par'
 */
function is_descendent(par, child) {
    var stack = new Array();
    stack.push(par);
    while(stack.length > 0) {
        var node = stack.pop();
        if(node.hasOwnProperty('nodeType') && node.nodeType === 1) {
            if(node === child) {
                return true;
            }
            for(var i = 0; i < node.childNodes.length; i++) {
                stack.push(node.childNodes[i]);
            }
        }
    }
    return false;
}

mxn.register('image', {

	Mapstraction: {

		init: function(element, api){
			var me = this;

            // style needed to define map box
            element.style.position = 'relative';
            element.style.overflow = 'hidden';

            // set of events to define map drag action
            element.onmousedown = function(ed) {
                if(is_descendent(element, ed.target)) {
                    var start_Y = element.scrollTop;
                    var start_x = element.scrollLeft;
                    document.onmousemove = function(em) {
                        element.scrollTop  = start_Y + ed.pageY - em.pageY;
                        element.scrollLeft = start_x + ed.pageX - em.pageX;
                    };
                }
            };
            document.onmouseup = function(eb) { document.onmousemove = null;};

            // define new function on mapstraction object that isn't specified in interface.
            this.setImage = function(image_src) {
                var image = new Image();
                console.log(image_src);
                image.src = image_src;
                element.appendChild(image);

                // reset these events to avoid the annoying browsers behavior
                image.onmouseup   = function(e){return false;};
                image.onmousedown = function(e){return false;};
                image.onmousemove = function(e){return false;};
            }

            // pega dados da imagem, tamanho etc.
            // registra eventos drag, click, etc.


            /*
			// deal with click
			map.events.register('click', map, function(evt){
				var lonlat = map.getLonLatFromViewPortPx(evt.xy);
				var point = new mxn.LatLonPoint();
				point.fromProprietary(api, lonlat);
				me.click.fire({'location': point });
			});

			// deal with map movement
			map.events.register('moveend', map, function(evt){
				me.moveendHandler(me);
				me.endPan.fire();
			});
			*/

            // ver o q é isso
			this.maps[api] = element;
			//this.loaded[api] = true;
		},



        applyOptions: function(){

		},

		setCenterAndZoom: function() {

        },

		addMarker: function(marker, old) {
			var map = this.maps[this.api];
			var pin = marker.toProprietary(this.api);
			map.appendChild(pin);
			return pin;
		},

		removeMarker: function(marker) {
			var map = this.maps[this.api];
			var pin = marker.proprietary_marker;
			pin.hide();
			//pin.destroy();
		},

		declutterMarkers: function(opts) {
			throw 'Not supported';
		},

		getCenter: function() {
			var map = this.maps[this.api];
			var pt = map.getCenter();
			var mxnPt = new mxn.LatLonPoint();
			mxnPt.fromProprietary(this.api, pt);
			return mxnPt;
		},

		setCenter: function(point, options) {
			var map = this.maps[this.api];
			var pt = point.toProprietary(this.api);
			map.setCenter(pt);
		},


		getBounds: function () {
			var map = this.maps[this.api];
			var olbox = map.calculateBounds();
			var ol_sw = new OpenLayers.LonLat( olbox.left, olbox.bottom );
			var mxn_sw = new mxn.LatLonPoint(0,0);
			mxn_sw.fromProprietary( this.api, ol_sw );
			var ol_ne = new OpenLayers.LonLat( olbox.right, olbox.top );
			var mxn_ne = new mxn.LatLonPoint(0,0);
			mxn_ne.fromProprietary( this.api, ol_ne );
			return new mxn.BoundingBox(mxn_sw.lat, mxn_sw.lon, mxn_ne.lat, mxn_ne.lon);
		},

		setBounds: function(bounds){
			var map = this.maps[this.api];
			var sw = bounds.getSouthWest();
			var ne = bounds.getNorthEast();

			if(sw.lon > ne.lon) {
				sw.lon -= 360;
			}

			var obounds = new OpenLayers.Bounds();

			obounds.extend(new mxn.LatLonPoint(sw.lat,sw.lon).toProprietary(this.api));
			obounds.extend(new mxn.LatLonPoint(ne.lat,ne.lon).toProprietary(this.api));
			map.zoomToExtent(obounds);
		},

		getPixelRatio: function() {
			return 1;

		},

	},

	LatLonPoint: {

		toProprietary: function() {
			var ollon = this.lon * 20037508.34 / 180;
			var ollat = Math.log(Math.tan((90 + this.lat) * Math.PI / 360)) / (Math.PI / 180);
			ollat = ollat * 20037508.34 / 180;
			return new OpenLayers.LonLat(ollon, ollat);
		},

		fromProprietary: function(olPoint) {
			var lon = (olPoint.lon / 20037508.34) * 180;
			var lat = (olPoint.lat / 20037508.34) * 180;
			lat = 180/Math.PI * (2 * Math.atan(Math.exp(lat * Math.PI / 180)) - Math.PI / 2);
			this.lon = lon;
			this.lat = lat;
		}

	},

    LatLonPoint: {

		toProprietary: function() {
			return [this.lon, this.lat];
		},

		fromProprietary: function(olPoint) {

			this.lon = olPoint[0];
			this.lat = olPoint[1];
		}

	},

	Marker: {

		toProprietary: function() {
			var size, anchor, icon;

            var iconImage = new Image();

            iconImage.src = this.iconUrl || 'http://openlayers.org/dev/img/marker-gold.png';

            iconImage.style.position = 'absolute';
            iconImage.style.top = this.location.lat + 'px';
            iconImage.style.left = this.location.lon + 'px';

            return iconImage;

		},

		openBubble: function() {
			// TODO: Add provider code
		},

		hide: function() {
			this.proprietary_marker.style.display = 'none';
		},

		show: function() {
			this.proprietary_marker.style.display = '';
		},

		update: function() {
			// TODO: Add provider code
		}

	}

});
