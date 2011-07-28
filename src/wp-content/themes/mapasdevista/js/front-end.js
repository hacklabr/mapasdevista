(function($){
    $(document).ready(function() {
        hWindow = window.innerHeight;
        $("#toggle-filters").toggle(
            function() {
                $(this).html("<img src='"+mapinfo.baseurl+"/img/hide-filters.png'/> esconder filtros");
                $(this).parent().animate({ "bottom": hWindow - hWindow/2 }, 450);
                $("#filters").animate({ height: (hWindow - hWindow/2) }, 450);
            },
            function() {
                $(this).html("<img src='"+mapinfo.baseurl+"/img/show-filters.png'/> mostrar filtros");
                $(this).parent().animate({ "bottom": "0" }, 450);
                $("#filters").animate({ height: "0" }, 450);
            }
        );
        
        $("#toggle-side-menu").hover(
            function() { $(".map-menu-side").show(); },
            function() { $(".map-menu-side").hide(); }
        );

        $(".map-menu-side").hover(
             function() { $(this).show(); },
             function() { $(this).hide(); }
         );
        
        $("#toggle-results").toggle(
            function() { 
                $(this).find("img").attr("src",mapinfo.baseurl+"/img/hide-results.png");
                $("#results").show();
            },
            function() { 
                $(this).find("img").attr("src",mapinfo.baseurl+"/img/show-results.png");
                $("#results").hide();
            }
        );

        mapstraction = new mxn.Mapstraction('map', mapinfo.api);

        if(mapinfo.api === 'image') {
            mapstraction.setImage(mapinfo.image_src);
            $(window).resize(function(e) {
                $("#map").css('height', $(window).height())
                         .css('width', $(window).width());
            }).trigger('resize');
        } else if(mapinfo.api === 'googlev3') {
            mapstraction.addControls({pan: true, zoom: 'large', overview: true, scale: true, map_type: true});
            mapstraction.maps[mapinfo.api].setOptions({
                zoomControlOptions:{
                                       style: google.maps.ZoomControlStyle.LARGE,
                                       position: google.maps.ControlPosition.LEFT_CENTER
                                   },
                panControlOptions: {
                                       position: google.maps.ControlPosition.LEFT_CENTER
                                   }
            });
        }

        mapstraction.applyFilter = function(o, f) {
            var vis = true;

            switch (f[1]) {
                case 'ge':
                    if (o.getAttribute( f[0] ) < f[2]) {
                        vis = false;
                    }
                    break;
                case 'le':
                    if (o.getAttribute( f[0] ) > f[2]) {
                        vis = false;
                    }
                    break;
                case 'eq':

                    if (o.getAttribute( f[0] ) != f[2]) {
                        vis = false;
                    }
                    break;
                case 'in':

                    if ( typeof(o.getAttribute( f[0] )) == 'undefined' ) {
                        vis = false;
                    } else if ( o.getAttribute( f[0] ).indexOf(f[2]) == -1 ) {
                        vis = false;
                    }
                    break;
            }

            return vis;
        };

        mapstraction.setCenterAndZoom(new mxn.LatLonPoint(parseFloat(mapinfo.lat), parseFloat(mapinfo.lng)), parseInt(mapinfo.zoom));

        if (mapinfo.api == 'googlev3') {
            mapstraction.setMapType(mxn.Mapstraction[mapinfo.type.toUpperCase()]);
        }

        
        // Watch for zoom limit
        mapinfo.min_zoom = parseInt(mapinfo.min_zoom);
        if (mapinfo.min_zoom > 0) {
            mapstraction.changeZoom.addHandler(function() {
                if (mapstraction.getZoom() < mapinfo.min_zoom)
                    mapstraction.setZoom(mapinfo.min_zoom);
            });
        }
        
        // Watch for pan limit
            //mapstraction.setBounds( new mxn.BoundingBox( parseFloat(mapinfo.sw_lat), parseFloat(mapinfo.sw_lng), parseFloat(mapinfo.ne_lat), parseFloat(mapinfo.ne_lng) ) ); 
            //top
            mapinfo.ne_lat = parseFloat(mapinfo.ne_lat);
            mapinfo.ne_lng = parseFloat(mapinfo.ne_lng);
            mapinfo.sw_lat = parseFloat(mapinfo.sw_lat);
            mapinfo.sw_lng = parseFloat(mapinfo.sw_lng);
            
            mapstraction.endPan.addHandler(function() {
                var coord = mapstraction.getCenter();
                coord.lat = parseFloat(coord.lat);
                coord.lng = parseFloat(coord.lng);
                var lat;
                var lng;
                
                lat = coord.lat < mapinfo.sw_lat ? mapinfo.sw_lat : coord.lat;
                if (lat == coord.lat) lat = coord.lat > mapinfo.ne_lat ? mapinfo.ne_lat : coord.lat;
                
                lng = coord.lng < mapinfo.sw_lng ? mapinfo.sw_lng : coord.lng;
                if (lng == coord.lng) lng = coord.lng > mapinfo.ne_lng ? mapinfo.ne_lng : coord.lng;
                
                if ( lat != coord.lat || lng != coord.lng) {
                    //console.log ('position changed');
                    mapstraction.setCenter(new mxn.LatLonPoint(lat, lng));
                }
                
            });
        
        
        
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
                loadPosts(totalPosts, 0);
            }
        );

        function loadPosts(total, offset) {

            var posts_per_page = 2;

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
                    } else {
                        //console.log('fim');
                    }

                    for (var p = 0; p < data.posts.length; p++) {
                        var pin = data.posts[p].pin;
                        var pin_size = [pin['1'], pin['2']];

                        var ll = new mxn.LatLonPoint( data.posts[p].location.lat, data.posts[p].location.lon );
                        var marker = new mxn.Marker(ll);

                        if(mapinfo.api !== 'image' && pin['anchor']) {
                            var pin_anchor = [parseInt(pin['anchor']['x']), parseInt(pin['anchor']['y'])];
                            marker.setIcon(pin[0], pin_size, pin_anchor);
                        } else {
                            marker.setIcon(pin[0]);
                        }

                        marker.setAttribute( 'ID', data.posts[p].ID );
                        marker.setAttribute( 'title', data.posts[p].title );
                        marker.setAttribute( 'date', data.posts[p].date );
                        marker.setAttribute( 'post_type', data.posts[p].post_type );
                        marker.setAttribute( 'number', data.posts[p].number );
                        marker.setAttribute( 'author', data.posts[p].author );
                        marker.setInfoBubble($('#balloon_' + data.posts[p].ID).html());
                        $('#balloon_' + data.posts[p].ID).remove();
                        marker.setLabel(data.posts[p].title);
                        //marker.setHover = true;
                        //marker.click.addHandler(function(event) { console.log(event); });
                        
                        
                        for (var att = 0; att < data.posts[p].terms.length; att++) {

                            if (typeof(marker.attributes[ data.posts[p].terms[att].taxonomy ]) != 'undefined') {
                                marker.attributes[ data.posts[p].terms[att].taxonomy ].push(data.posts[p].terms[att].slug);
                            } else {
                                marker.attributes[ data.posts[p].terms[att].taxonomy ] = [ data.posts[p].terms[att].slug ];
                            }

                        }
                        mapstraction.addMarker( marker );
                        
                        if (mapinfo.api == 'openlayers') {
                            marker.proprietary_marker.icon.imageDiv.onclick = function(event) {
                               marker.click.fire();
                            }
                        }

                    }

                }

            });

        }

        // Filters events

        $('.taxonomy-filter-checkbox').click(function() {

            var tax = $(this).attr('name').replace('filter_by_', '').replace('[]', '');
            var val = $(this).val();

            if ( $(this).attr('checked') ) {
                mapstraction.addFilter(tax, 'in', val);
            } else {
                mapstraction.removeFilter(tax, 'in', val);
            }

            mapstraction.doFilter();
            updateResults();

        });

        $('.post_type-filter-checkbox').click(function() {

            var val = $(this).val();

            if ( $(this).attr('checked') ) {
                mapstraction.addFilter('post_type', 'eq', val);
            } else {
                mapstraction.removeFilter('post_type', 'eq', val);
            }

            mapstraction.doFilter();
            updateResults();

        });
        
        $('.author-filter-checkbox').click(function() {

            var val = $(this).val();

            if ( $(this).attr('checked') ) {
                mapstraction.addFilter('author', 'eq', val);
            } else {
                mapstraction.removeFilter('author', 'eq', val);
            }

            mapstraction.doFilter();
            updateResults();

        });

        $('#filter_by_new').click(function() {

            if ( $(this).attr('checked') ) {
                mapstraction.addFilter('number', 'le', 2);
            } else {
                mapstraction.removeFilter('number', 'le', 2);
            }

            mapstraction.doFilter();
            updateResults();

        });
        
        function updateResults() {
            
            var count = 0;
            
            for (var i = 0; i < mapstraction.markers.length; i ++) {
                //console.log( mapstraction.markers[i].attributes );
                
                if (mapstraction.markers[i].attributes['visible']) {
                    $('#result_' + mapstraction.markers[i].attributes['ID']).show();
                    //console.log('mostra '+mapstraction.markers[i].attributes['ID']);
                    count++;
                } else {
                    $('#result_' + mapstraction.markers[i].attributes['ID']).hide();
                    //console.log('esconde '+'#result_' + mapstraction.markers[i].attributes['ID']);
                }
            }
            
            $('#filter_total').html(count);
            
        
        }
        
        // results links
        
        $('.js-filter-by-author-link').click(function() {
        
            var author_id = $(this).attr('id').replace('author-link-', '');
            if (!$('#filter_author_'+author_id).attr('checked'))
                $('#filter_author_'+author_id).click();
            return false;
        
        });


        // search
        
        $('#searchfield').focus(function() {
            if ($(this).val() == $(this).attr('title'))
                $(this).val('');
        }).blur(function() {
            if ($(this).val() == '')
                $(this).val($(this).attr('title'));
        });
        
        
        // Posts overlay
        //$('a.js-link-to-post').each(function() { console.log($(this).attr('id'));});
        $('a.js-link-to-post').each(function() {
        
            $(this).click(function() {
            
                return mapasdevista.linkToPost(document.getElementById($(this).attr('id')));
            
            });
        });
        
        $('a#close_post_overlay').click(function() {
            $('#post_overlay').hide();
        });
        
        mapasdevista = {

            linkToPost : function(el) {
            
                                  
                var post_id = $('#'+el.id).attr('id').replace(/[^0-9]+/g, '');
                
                $.post(
                    mapinfo.ajaxurl,
                    {
                        action: 'mapasdevista_get_post',
                        post_id: post_id
                    },
                    function(data) {
                        if (data != 'error') {
                            if ($('#results').is(':visible')) {
                                $('#toggle-results').click();
                            }
                            // var left = parseInt( $(window).width()/2 - $('#post_overlay').width() / 2 );
                            // $('#post_overlay').css('left', left + 'px').show();
                            $('#post_overlay_content').html(data);
                            $('#post_overlay').show();
                            
                        }
                    }
                );
                   
                return false;
            
            }

        }


    });

})(jQuery);



