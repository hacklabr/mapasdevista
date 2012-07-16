(function($){
    $(document).ready(function() {
        
        var leaflet = new L.Map('map', {
            center: new L.LatLng(mapinfo.lat, mapinfo.lng),
            zoom: mapinfo.zoom,
            minZoom: mapinfo.min_zoom > 0 ? mapinfo.min_zoom : null,
            maxZoom: 20,
            //maxBounds: mapinfo.sw_lat > 0 ? new L.LatLngBounds(new L.LatLng(parseFloat(mapinfo.sw_lat),parseFloat(mapinfo.sw_lng)), new L.LatLng(parseFloat(mapinfo.ne_lat),parseFloat(mapinfo.ne_lng))) : null
        });
        
        
        var streetsLayerUrl = 'http://d.tiles.mapbox.com/v3/mapbox.mapbox-streets/{z}/{x}/{y}.png';
        var streetsLayer = new L.TileLayer(streetsLayerUrl, {maxZoom:9});
        
        leaflet.addLayer(streetsLayer);
        
        // Load posts

        $.post(
            mapinfo.ajaxurl,
            {
                get: 'totalPosts',
                action: 'mapasdevista_get_posts',
                api: mapinfo.api,
                page_id: mapinfo.page_id,
                search: mapinfo.search
            },
            function(data) {
                totalPosts = parseInt(data);
                
                if(totalPosts > 0)
                    loadPosts(totalPosts, 0);
                
                jQuery('#posts-loader-total').html(totalPosts);
                jQuery('#posts-loader').show();
            }
            );

        function loadPosts(total, offset) {

            var posts_per_page = 10;

            $.ajax({
                type: 'post',
                url: mapinfo.ajaxurl,
                dataType: 'json',
                data: {
                    page_id: mapinfo.page_id,
                    action: 'mapasdevista_get_posts',
                    get: 'posts',
                    api: mapinfo.api,
                    offset: offset,
                    total: total,
                    posts_per_page: posts_per_page,
                    search: mapinfo.search
                },
                success: function(data) {
                    
                    //console.log('loaded posts:'+offset);

                    if (data.newoffset != 'end') {
                        loadPosts(total, data.newoffset);
                        jQuery('#posts-loader-loaded').html(data.newoffset);
                    } else {
                        jQuery('#posts-loader').hide();
                    }
                
                    
                    for (var p = 0; p < data.posts.length; p++) {
                        
                        var pin = data.posts[p].pin;
                        if(data.posts[p].link){
                            $(document).data('links-'+data.posts[p].ID,  data.posts[p].link);
                        }
                        
                        
                        var pin_size = [pin['1'], pin['2']];

                        var ll = new L.LatLng( data.posts[p].location.lat, data.posts[p].location.lon );
                        var marker = new L.Marker(ll);
                        /*
                        if(mapinfo.api !== 'image' && pin['anchor']) {
                            var adjust = mapinfo.api==='openlayers'?-1:1;
                            var pin_anchor = [parseInt(pin['anchor']['x']) * adjust, parseInt(pin['anchor']['y']) * adjust];
                            marker.setIcon(pin[0], pin_size, pin_anchor);
                        } else {
                            marker.setIcon(pin[0]);
                        }
                        */
                        if(pin['clickable']) {
                            /*
                            marker.setAttribute( 'ID', data.posts[p].ID );
                            marker.setAttribute( 'title', data.posts[p].title );
                            marker.setAttribute( 'date', data.posts[p].date );
                            marker.setAttribute( 'post_type', data.posts[p].post_type );
                            marker.setAttribute( 'number', data.posts[p].number );
                            marker.setAttribute( 'author', data.posts[p].author );
                            */
                             
                            marker.bindPopup($('#balloon_' + data.posts[p].ID).html());
                            //marker.setLabel(data.posts[p].title);
                            
                            
                            //marker.setHover = true;
                            //marker.click.addHandler(function(event) { console.log(event); });
                            
                            /*
                            for (var att = 0; att < data.posts[p].terms.length; att++) {

                                if (typeof(marker.attributes[ data.posts[p].terms[att].taxonomy ]) != 'undefined') {
                                    marker.attributes[ data.posts[p].terms[att].taxonomy ].push(data.posts[p].terms[att].slug);
                                } else {
                                    marker.attributes[ data.posts[p].terms[att].taxonomy ] = [ data.posts[p].terms[att].slug ];
                                }

                            }
                            * */
                        }
                        
                        $('#balloon_' + data.posts[p].ID).remove();

                        leaflet.addLayer(marker);
                        
                        /*
                        if (mapinfo.api == 'openlayers' && pin['clickable']) {
                            marker.proprietary_marker.icon.imageDiv.onclick = function(event) {
                                marker.click.fire();
                            }
                        }
                        */ 

                    }

                }

            });

        }
        
        
    });
})(jQuery);
