/**
 * Verify if parameter 'child' is descendent of parameter 'par'
 */
function is_descendent(par, child) {
    var stack = new Array();
    stack.push(par);
    while(stack.length > 0) {
        var node = stack.pop();
        if(node.nodeType === 1) {
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
                    document.body.style.cursor = 'move';
                    document.onmousemove = function(em) {
                        element.scrollTop  = start_Y + ed.pageY - em.pageY;
                        element.scrollLeft = start_x + ed.pageX - em.pageX;
                    };
                }
            };
            document.onmouseup = function(eb) {
                document.onmousemove = null;
                document.body.style.cursor = '';
                me.endPan.fire();
            };

            // define new function on mapstraction object that isn't specified in interface.
            this.setImage = function(image_src) {
                var image = new Image();
                //console.log(image_src);
                image.src = image_src;
                element.appendChild(image);
                element.image = image;

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
            this.element = element;
			this.maps[api] = this;
			//this.loaded[api] = true;
		},



        applyOptions: function(){

		},

		setCenterAndZoom: function(point, zoom) {
            this.setCenter(point);
        },

		addMarker: function(marker, old) {
			var map = this.maps[this.api];
			var pin = marker.toProprietary(this.api);
			map.element.appendChild(pin);
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
            var coord = {
                lat : map.element.scrollTop + map.element.clientHeight/2,
                lon : map.element.scrollLeft + map.element.clientWidth/2,
                lng : map.element.scrollLeft +map.element.clientWidth/2
            };
            
            
            return coord;
		},

		setCenter: function(point, options) {
			var map = this.maps[this.api];
            //console.log(point);
            var top = point.lat;
            var left = point.lon;
            var plusTop = 0;
            var plusLeft = 0;

            var scroll_left_max = Math.max(0, map.element.image.width - map.element.clientWidth);
            var scroll_top_max = Math.max(0, map.element.image.height - map.element.clientHeight);

            if (typeof(options) != 'undefined' && options.lat_offset)
                plusTop -= options.lat_offset;

            if (typeof(options) != 'undefined' && options.lon_offset)
                plusLeft -= options.lon_offset;
            
            //console.log(top);
            
            var target_top = (top - map.element.clientHeight/2) - plusTop;
            var target_left = (left - map.element.clientWidth/2) - plusLeft;

            //console.log(target_top);

            target_top = Math.min(Math.max(0, target_top), scroll_top_max);    // scroll_top_max  > target_top  > 0
            target_left = Math.min(Math.max(0, target_left), scroll_left_max); // scroll_left_max > target_left > 0
            
            //console.log(target_top);
            //console.log(plusTop);
            
            var distance_top = Math.abs(target_top - map.element.scrollTop);
            var distance_left = Math.abs(target_left - map.element.scrollLeft);

            var step = 10;
            var step_left = step * ((target_left - map.element.scrollLeft) > 0 ? 1 : -1);
            var step_top = ((target_top - map.element.scrollTop)>0 ? 1 : -1)
                           * ((distance_left>0) ? step * distance_top / distance_left : step);

            // TODO: melhorar isto
            setTimeout(function(){
                if(distance_top > 0) {
                    distance_top -= Math.abs(step_top);
                    map.element.scrollTop += step_top;
                }
                if(distance_left > 0) {
                    distance_left -= Math.abs(step_left);
                    map.element.scrollLeft += step_left;
                }
                if(distance_top || distance_left) {
                    setTimeout(arguments.callee,1);
                }
            },1);
            // map.element.scrollTop = target_top;
            // map.element.scrollLeft = target_left;

		},


		getBounds: function () {
			throw 'Not supported';
		},

		setBounds: function(bounds){
			throw 'Not supported';
		},

		getPixelRatio: function() {
			return 1;

		},
        
        getZoom: function() {
            return 1;
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
            iconImage.style.cursor = 'pointer';
            if (this.attributes['title'])
                iconImage.title = this.attributes['title'];

            if (this.attributes['ID'])
                iconImage.id = 'marker_' + this.attributes['ID'];

            iconImage.onclick = function(event) {
                this.mapstraction_marker.click.fire();
            }

            var thismarker = this;

            if(this.infoBubble) {

                var theMap = this.map;

                this.popup = new MapImage.Popup(this.infoBubble, this.location, iconImage, theMap);

                if(this.hover) {
                    iconImage.mouseover = function(event) {
                        thismarker.openBubble();
					};
				}
				else {
					iconImage.onclick = function(event) {
                        thismarker.openBubble();
					};

				}
			}

            return iconImage;

		},

		openBubble: function() {

            var scrollOffset = {
                lat_offset: 0,
                lon_offset: 0
            }

            // check if the bubble fits in the screen size (30 is the height of the filter bar)
            if ( parseInt( this.map.element.style.height.replace('px','') )/2 < this.popup.element.clientHeight - 30 ) {
                scrollOffset.lat_offset = this.popup.element.clientHeight - parseInt( this.map.element.style.height.replace('px','') )/2 +30;
            }

            var shown = this.popup.visibility;
            if (shown != 'hidden') {
                this.popup.hide();
            } else {
                this.map.setCenter(this.location, scrollOffset);
                this.popup.show();
            }

		},
        
        closeBubble: function() {
            this.popup.hide();
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


MapImage = {

    Popup : function(html, location, iconImage, map) {

        this.element = document.createElement('div');
        this.element.style.position = 'absolute';
        this.element.style.visibility = 'hidden';
        this.visibility = 'hidden';
        this.element.innerHTML = html;

        map.element.appendChild(this.element);

        bubbleHeight = this.element.clientHeight;
        bubbleWidth = this.element.clientWidth;

        var mapWidth = parseInt( map.element.scrollWidth );
        var mapHeight = parseInt( map.element.scrollHeight );

        // Lets find out the best position to display the bubble

        var bubbleLat = location.lat;
        var bubbleLon = location.lon + parseInt( iconImage.width + 3 );

        if (location.lat > mapHeight - bubbleHeight - 100) {
            bubbleLat = location.lat - bubbleHeight + iconImage.height;
        }

        if (location.lon > mapWidth - bubbleWidth) {
            bubbleLon = location.lon - bubbleWidth - 3;
        }

        var bubblePosition = {
            lat: bubbleLat,
            lon: bubbleLon
        }

        this.element.style.top = bubblePosition.lat + 'px';
        this.element.style.left = bubblePosition.lon + 'px';

        this.hide = function() {
            this.element.style.visibility = 'hidden';
            this.visibility = 'hidden';
        }

        this.show = function() {
            this.element.style.visibility = 'visible';
            this.visibility = 'visible';
        }

    }

}



