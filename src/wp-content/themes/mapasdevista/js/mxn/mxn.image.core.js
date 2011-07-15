
mxn.register('openlayers', {	

	Mapstraction: {

		init: function(element, api, image_src){
			var me = this;
			
            
            // joga a imagem no element
            
            var image = new Image();
            
            image.src = image_src;
            
            document.getElementById(element).appendChild(image);
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
			this.maps[api] = map;
			this.loaded[api] = true;
		},

		

		addMarker: function(marker, old) {
			var map = this.maps[this.api];
			var pin = marker.toProprietary(this.api);
			if (!this.layers.markers) {
				this.layers.markers = new OpenLayers.Layer.Markers('markers');
				map.addLayer(this.layers.markers);
			}
			this.layers.markers.addMarker(pin);
			return pin;
		},

		removeMarker: function(marker) {
			var map = this.maps[this.api];
			var pin = marker.proprietary_marker;
			this.layers.markers.removeMarker(pin);
			pin.destroy();
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

	Marker: {

		toProprietary: function() {
			var size, anchor, icon;
			if(this.iconSize) {
				size = new OpenLayers.Size(this.iconSize[0], this.iconSize[1]);
			}
			else {
				size = new OpenLayers.Size(21,25);
			}

			if(this.iconAnchor) {
				anchor = new OpenLayers.Pixel(this.iconAnchor[0], this.iconAnchor[1]);
			}
			else {
				// FIXME: hard-coding the anchor point
				anchor = new OpenLayers.Pixel(-(size.w/2), -size.h);
			}

			if(this.iconUrl) {
				icon = new OpenLayers.Icon(this.iconUrl, size, anchor);
			}
			else {
				icon = new OpenLayers.Icon('http://openlayers.org/dev/img/marker-gold.png', size, anchor);
			}
			var marker = new OpenLayers.Marker(this.location.toProprietary("openlayers"), icon);

			if(this.infoBubble) {
				var popup = new OpenLayers.Popup(null,
					this.location.toProprietary("openlayers"),
					new OpenLayers.Size(100,100),
					this.infoBubble,
					true
				);
				popup.autoSize = true;
				var theMap = this.map;
				if(this.hover) {
					marker.events.register("mouseover", marker, function(event) {
						theMap.addPopup(popup);
						popup.show();
					});
					marker.events.register("mouseout", marker, function(event) {
						popup.hide();
						theMap.removePopup(popup);
					});
				}
				else {
					var shown = false;
					marker.events.register("mousedown", marker, function(event) {
						if (shown) {
							popup.hide();
							theMap.removePopup(popup);
							shown = false;
						} else {
							theMap.addPopup(popup);
							popup.show();
							shown = true;
						}
					});
				}
			}

			if(this.hoverIconUrl) {
				icon = this.iconUrl || 'http://openlayers.org/dev/img/marker-gold.png';
				hovericon = this.hoverIconUrl;
				marker.events.register("mouseover", marker, function(event) {
					marker.setUrl(hovericon);
				});
				marker.events.register("mouseout", marker, function(event) {
					marker.setUrl(icon);
				});
			}

			if(this.infoDiv){
				// TODO
			}
			return marker;
		},

		openBubble: function() {		
			// TODO: Add provider code
		},

		hide: function() {
			this.proprietary_marker.display( false );
		},

		show: function() {
			this.proprietary_marker.display( true );
		},

		update: function() {
			// TODO: Add provider code
		}

	}

});
