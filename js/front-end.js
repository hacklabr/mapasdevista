(function($){
    $(document).ready(function() {
        
        mxn.Marker.prototype._old_openBubble = mxn.Marker.prototype.openBubble;
        
        mxn.Marker.prototype.openBubble = function(){ 
            for (var ii = 0; ii < mapstraction.markers.length; ii ++) {
                mapstraction.markers[ii].closeBubble();
            }
            this._old_openBubble();
        }
        
        hWindow = $(window).height();
        
        $("#toggle-filters").toggle(
            function() {
                $(this).html("<img src='"+mapinfo.baseurl+"/img/hide-filters.png'/> " + messages.hide_filters);
                
                $('.hide_when_show_filters').hide();
                
                $(this).parent().animate({
                    "bottom": hWindow/3
                }, 450);
                $("#filters").animate({
                    height: (hWindow/3)
                }, 450);
                
            },
            function() {
                $(this).html("<img src='"+mapinfo.baseurl+"/img/show-filters.png'/> " + messages.show_filters);
                
                $('.show_when_hide_filters').hide();
                
                $(this).parent().animate({
                    "bottom": "0"
                }, 450);
                $("#filters").animate({
                    height: "0"
                }, 450);
            }
            );
        
        $("#toggle-side-menu").hover(
            function() {
                $(".map-menu-side").show();
            },
            function() {
                $(".map-menu-side").hide();
            }
            );

        $(".map-menu-side").hover(
            function() {
                $(this).show();
            },
            function() {
                $(this).hide();
            }
            );
        
        $("#toggle-results").toggle(
            function() { 
                $(this).find("img").attr("src",mapinfo.baseurl+"/img/hide-results.png");
                $('.hide_when_show_results').hide();
                $("#results").show();
            },
            function() { 
                $(this).find("img").attr("src",mapinfo.baseurl+"/img/show-results.png");
                $('.show_when_hide_results').hide();
                $("#results").hide();
            }
            );
        

        // wp_localize_script não mantém o tipo do dado no javascript
        mapinfo.control_zoom = mapinfo.control_zoom != "false" ? mapinfo.control_zoom : false;
        mapinfo.control_pan = mapinfo.control_pan == "true";
        mapinfo.control_map_type = mapinfo.control_map_type == "true";

        mapstraction = new mxn.Mapstraction('map', mapinfo.api);

        if(mapinfo.api === 'image') {
            mapstraction.setImage(mapinfo.image_src);
            $(window).resize(function(e) {
                $("#map").css('height', $(window).height())
                .css('width', $(window).width());
            }).trigger('resize');
        } else if(mapinfo.api === 'googlev3') {
            mapstraction.maps[mapinfo.api].setOptions({
                mapTypeControl: mapinfo.control_map_type,
                panControl: mapinfo.control_pan,
                zoomControl: mapinfo.control_zoom != false,
                zoomControlOptions:{
                    style: mapinfo.control_zoom ? google.maps.ZoomControlStyle[mapinfo.control_zoom.toUpperCase()] : 0 ,
                    position: google.maps.ControlPosition.LEFT_CENTER
                },
                panControlOptions: {
                    position: google.maps.ControlPosition.LEFT_CENTER
                }
            });
        } else {
            mapstraction.addControls({
                pan: mapinfo.control_map_type,
                zoom: mapinfo.control_zoom,
                map_type: mapinfo.control_map_type
            });
        }
        var old_applyFilter = mapstraction.applyFilter;
        
        mapstraction.applyFilter = function(o, f) {
            var vis = true;

            switch (f[1]) {
                
                case 'in':

                    if ( typeof(o.getAttribute( f[0] )) == 'undefined' ) {
                        vis = false;
                    } else if ( o.getAttribute( f[0] ).indexOf(f[2]) == -1 ) {
                        vis = false;
                    }
                    break;
                default:
                    vis = old_applyFilter(o, f);
                    break;
            }

            return vis;
        };
        
        mapstraction.doFilter = function(showCallback, hideCallback) {
            var map = this.maps[this.api];
            var visibleCount = 0;
            var f;
            if (this.filters) {
                switch (this.api) {
                    case 'multimap':
                        /* TODO polylines aren't filtered in multimap */
                        var mmfilters = [];
                        for (f=0; f<this.filters.length; f++) {
                            mmfilters.push( new MMSearchFilter( this.filters[f][0], this.filters[f][1], this.filters[f][2] ));
                        }
                        map.setMarkerFilters( mmfilters );
                        map.redrawMap();
                        break;
                    case 'dummy':
                        break;
                    default:
                        var vis;
                        for (var m=0; m<this.markers.length; m++) {
                            vis = mapstraction.logicalOperator != 'OR' || this.filters.length == 0;
                            for (f = 0; f < this.filters.length; f++) {
                                if(mapstraction.logicalOperator == 'OR'){
                                    if (this.applyFilter(this.markers[m], this.filters[f])) {
                                        vis = true;
                                    }
                                }else{
                                    if (! this.applyFilter(this.markers[m], this.filters[f])) {
                                        vis = false;
                                    }
                                }
                            }
                            if (vis) {
                                visibleCount ++;
                                if (showCallback){
                                    showCallback(this.markers[m]);
                                }
                                else {
                                    this.markers[m].show();
                                }
                            } 
                            else { 
                                if (hideCallback){
                                    hideCallback(this.markers[m]);
                                }
                                else {
                                    this.markers[m].hide();
                                }
                            }

                            this.markers[m].setAttribute("visible", vis);
                        }
                        break;
                }
            }
            return visibleCount;
        };

        mapstraction.setCenterAndZoom(new mxn.LatLonPoint(parseFloat(mapinfo.lat), parseFloat(mapinfo.lng)), parseInt(mapinfo.zoom));

        if (mapinfo.api == 'googlev3') {
            mapstraction.setMapType(mxn.Mapstraction[mapinfo.type.toUpperCase()]);
        }

        
        // Watch for zoom limit
        mapinfo.min_zoom = parseInt(mapinfo.min_zoom);
        if (mapinfo.min_zoom > 0) {
            mapstraction.changeZoom.addHandler(function() {
                if (mapstraction.getZoom() < mapinfo.min_zoom) {
                    mapstraction.setZoom(mapinfo.min_zoom);
                    mapasdevista.updateHash();   
                }
            });
        }
        mapinfo.max_zoom = parseInt(mapinfo.max_zoom);
        if (mapinfo.max_zoom > 0) {
            mapstraction.changeZoom.addHandler(function() {
                if (mapstraction.getZoom() > mapinfo.max_zoom) {
                    mapstraction.setZoom(mapinfo.max_zoom);
                    mapasdevista.updateHash();
                }
            });
        }
        
        // Watch for pan limit 
        //mapstraction.setBounds( new mxn.BoundingBox( parseFloat(mapinfo.sw_lat), parseFloat(mapinfo.sw_lng), parseFloat(mapinfo.ne_lat), parseFloat(mapinfo.ne_lng) ) ); 
        //top
        mapinfo.ne_lat = parseFloat(mapinfo.ne_lat);
        mapinfo.ne_lng = parseFloat(mapinfo.ne_lng);
        mapinfo.sw_lat = parseFloat(mapinfo.sw_lat);
        mapinfo.sw_lng = parseFloat(mapinfo.sw_lng);
            
        if (mapinfo.sw_lat != 0 && mapinfo.sw_lng != 0 && mapinfo.ne_lat != 0 && mapinfo.ne_lng != 0) {
                
            mapstraction.endPan.addHandler(function() {
                var coord = mapstraction.getCenter();
                coord.lat = parseFloat(coord.lat);
                coord.lng = parseFloat(coord.lon);
                var lat;
                var lng;
                    
                lat = coord.lat < mapinfo.sw_lat ? mapinfo.sw_lat : coord.lat;
                if (lat == coord.lat) lat = coord.lat > mapinfo.ne_lat ? mapinfo.ne_lat : coord.lat;
                    
                lng = coord.lng < mapinfo.sw_lng ? mapinfo.sw_lng : coord.lon;
                if (lng == coord.lon) lng = coord.lon > mapinfo.ne_lng ? mapinfo.ne_lng : coord.lon;
                    
                if ( lat != coord.lat || lng != coord.lon) {
                    //console.log ('position changed');
                    mapstraction.setCenter(new mxn.LatLonPoint(lat, lng));
                    mapasdevista.updateHash();
                }
                    
            });
            
        }
            
        // Update Hash on drag
        mapstraction.endPan.addHandler(function() {
            if(!mapstraction.skipUpdateHash)
                mapasdevista.updateHash();
            mapstraction.skipUpdateHash = false;
        });
        
        // Update Hash on zoom
        mapstraction.changeZoom.addHandler(function() {
        
            mapasdevista.updateHash();
            
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
                
                if(totalPosts > 0)
                    loadPosts(totalPosts, 0);
                
                jQuery('#posts-loader-total').html(totalPosts);
                jQuery('#posts-loader').show();
            }
            );

        function loadPosts(total, offset) {

            var posts_per_page = 100;

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

                        var ll = new mxn.LatLonPoint( data.posts[p].location.lat, data.posts[p].location.lon );
                        var marker = new mxn.Marker(ll);
                        
                        if(mapinfo.api == 'googlev3'){
                            marker.toProprietary = function(){
                                var args = Array.prototype.slice.call(arguments);
                                var gmarker = mxn.Marker.prototype.toProprietary.apply(this,args);
                                gmarker.setOptions({
                                    optimized: false
                                });
                                return gmarker;
                            }
                        }
                            
                        
                        if(mapinfo.api !== 'image' && pin['anchor']) {
                            var adjust = mapinfo.api==='openlayers'?-1:1;
                            var pin_anchor = [parseInt(pin['anchor']['x']) * adjust, parseInt(pin['anchor']['y']) * adjust];
                            marker.setIcon(pin[0], pin_size, pin_anchor);
                        } else {
                            marker.setIcon(pin[0]);
                        }

                        if(pin['clickable']) {
                            marker.setAttribute( 'ID', data.posts[p].ID );
                            marker.setAttribute( 'title', data.posts[p].title );
                            marker.setAttribute( 'date', data.posts[p].date );
                            marker.setAttribute( 'post_type', data.posts[p].post_type );
                            marker.setAttribute( 'number', data.posts[p].number );
                            marker.setAttribute( 'author', data.posts[p].author );
                            marker.setInfoBubble($('#balloon_' + data.posts[p].ID).html());
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
                        }
                        $('#balloon_' + data.posts[p].ID).remove();

                        mapstraction.addMarker( marker );
                        if (mapinfo.api == 'openlayers' && pin['clickable']) {
                            marker.proprietary_marker.icon.imageDiv.onclick = function(event) {
                                marker.click.fire();
                            }
                        }

                    }

                }

            });

        }

        // Filters events
            
        $("#logical_oparator input").click(function (){
            mapstraction.logicalOperator = $(this).val();
            mapstraction.doFilter();
        });

        mapstraction.logicalOperator = $("#logical_oparator input").val();
        
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
                mapstraction.addFilter('number', 'le', 10);
            } else {
                mapstraction.removeFilter('number', 'le', 10);
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
        
        $('.js-filter-by-author-link').live('click', function() {
        
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
        
        $('a.js-link-to-bubble').live('click', function() {
        
            var id = $(this).attr('id').replace(/[^0-9]+/g, '');
            var marker = mapasdevista.findMarkerById( id );
            marker.openBubble();
            
            if ($('#results').is(':visible')) {
                $('#toggle-results').click();
            }
            return false;
        
        });
        
        $('a.js-link-to-post').live('click', function() {
            mapasdevista.linkToPost(document.getElementById($(this).attr('id')));
            return false;
        });
        
        $('li.js-menu-link-to-post').each(function() {
            
            var el = document.getElementById($(this).attr('id'));
            $(this).find('a').click(function() {
            
                return mapasdevista.linkToPost(el);
            
            });
        });
        
        $('a#close_post_overlay').click(function() {
            $('#post_overlay').hide();
            mapasdevista.updateHash(false);
        });
        
        mapasdevista = {

            hashJustUpdated: false,
            
            lastHash: null,
            
            openGalleryImage: function(){
                if(!$("#mapasdevista-gallery-image").length){
                    var div = document.createElement('div');
                    $(div).attr('id','mapasdevista-gallery-image');
                    $(document.body).append(div);
                    $(div).append("<div id='mapasdevista-gallery-close'></div><h1></h1><img />");
                    $("#mapasdevista-gallery-image").hide().css({
                        zIndex: 10000, 
                        position: 'absolute'
                    });
                    $("#mapasdevista-gallery-close").click(function(){
                        $("#mapasdevista-gallery-image").hide();
                    })
                }
                var url = $(this).attr('href');
                var title = $(this).attr('title');
                
                var container = $("#mapasdevista-gallery-image").show();
                
                var h1 = $("#mapasdevista-gallery-image h1").html(title);
                var img = $("#mapasdevista-gallery-image img").attr('src',url);
                
                img.load(function(){
                    
                    var _img_max_height = Math.round($(window).height()*.8-h1.outerHeight()-parseInt(container.css('padding-top'))-parseInt(container.css('padding-bottom')));
                    img.css({
                        maxHeight: _img_max_height
                    });
                    
                    var _left = Math.round(($(window).width()-img.width())/2);
                    var _top = Math.round(($(window).height()-img.height()-h1.height())/2);
                    container.css({
                        left: _left, 
                        top: _top
                    });
                });
                
                return false;
            },
            
            linkToPost : function(el) {
            
                var post_id = $('#'+el.id).attr('id').replace(/[^0-9]+/g, '');
                
                if($(document).data('links-'+post_id)){
                    document.location = $(document).data('links-'+post_id);
                }
                
                mapasdevista.linkToPostById(post_id);
                
                return false;
            
            },
            
            linkToPostById : function(post_id) {
            
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
                            $("#post_overlay_content .gallery .gallery-item a").click(mapasdevista.openGalleryImage);
                            
                            //hide bubbles
                            for (var ii = 0; ii < mapstraction.markers.length; ii ++) {
                                mapstraction.markers[ii].closeBubble();
                            }
                            
                            $('#post_overlay').show();
                            ajaxizeComments();
                            mapasdevista.updateHash(post_id);
                            
                        }
                    }
                    );
            
            }, 
            
            findMarkerById : function(id) {
                
                for (var i=0; i<mapstraction.markers.length; i++) {
                    
                    if ( mapstraction.markers[i].attributes.ID == id ) {
                        return mapstraction.markers[i];
                    }
                    
                }

                return false;

                
            },
            
            checkHashChange : function() {
    	
                if (!mapasdevista.lastHash) {
                    // First execution
                    mapasdevista.checkAndNavigateToHash();
                    mapasdevista.lastHash = location.hash;
                } else if (location.hash != mapasdevista.lastHash) {
                    mapasdevista.lastHash = location.hash;
                    $(window.location).trigger(
                        'change'
                        );
                }
            },
            
            updateHash : function(post_id) {
                if($(document).data('skip_hash_update'))
                    return;
                
                var coord = mapstraction.getCenter();
                coord.lat = parseFloat(coord.lat);
                coord.lon = parseFloat(coord.lon);
                var zoom = mapstraction.getZoom();
                
                var p = false;
                var leavep = false;
                //console.log('vai');
                if (typeof(post_id) != 'undefined') {
                    if (post_id) {
                        p = post_id;
                    //console.log('veio p');
                    } 
                    
                } else {
                    leavep = true;    
                //console.log('nao veio nada');
                }
                

                var hash = 'lat=' + coord.lat + '&lng=' + coord.lon + '&zoom=' + zoom;
                
                if (leavep) {
                    var post_pattern = /p=([^&]+)/;
                    var post = post_pattern.exec(location.hash);
                    if (post)
                        p = post[1];
                }
                
                if (p) 
                    hash += '&p=' + p;
                
                
                location.hash = hash;

                mapasdevista.hashJustUpdated = true;
                
            
            },
            
            checkAndNavigateToHash : function() {
            
                // this function can be called either when the page is first loaded or when the user hits back or forward button
                
                
                var lat_pattern = /lat=([^&]+)/;
                var lat = lat_pattern.exec(location.hash);
                if (lat) {
                    
                    var lon_pattern = /lng=([^&]+)/;
                    var lon = lon_pattern.exec(location.hash);
                        
                    var zoom_pattern = /zoom=([^&]+)/;
                    var zoom = zoom_pattern.exec(location.hash);
                    mapstraction.skipUpdateHash = true;
                    mapstraction.setCenterAndZoom(
                            
                        new mxn.LatLonPoint(parseFloat(lat[1]), parseFloat(lon[1])), parseInt(zoom[1])
                        );
                        
                        
                }
                    
                var post_pattern = /p=([^&]+)/;
                var post = post_pattern.exec(location.hash);
                    
                if (post && !mapasdevista.hashJustUpdated) {
                        
                    mapasdevista.linkToPostById(post[1]);
                    
                }
                    
                mapasdevista.hashJustUpdated = false;
            
            }
        }
        
        $(document).data('gInterval', setInterval(mapasdevista.checkHashChange, 10));
    
        $(window.location).bind(
            'change',
            function() {
                mapasdevista.checkAndNavigateToHash();
            }
            );
        
        //SLIDESHOWS
        
        $('.slideshow').each(function() {
        
            var selector = '#' + $(this).attr('id');
            
            $(selector + ' img:gt(0)').hide();
            if($(selector + ' img:gt(0)').length > 0)
                setInterval(function(){
                    $(selector + ' :first-child').fadeOut()
                    .next('img').fadeIn()
                    .end().appendTo(selector);
                }, 
                3000);
        
        });

    });
    
    
    $(document).mousedown(function(){
        
        $(document).data('skip_hash_update',true);
        mapasdevista.checkHashChange(); 
    });
    
    $(document).mouseup(function(){
        
        $(document).data('skip_hash_update',false);
        mapasdevista.updateHash(false);
        mapasdevista.checkHashChange();
    });

})(jQuery);
