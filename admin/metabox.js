jQuery(document).ready(function() {
    var $ = jQuery;
    
    if (typeof(google) != 'object' || jQuery('#mpv_canvas').size() == 0)
        return;
    
    /**
     *
     * Handle functionalities to GoogleMaps API version 3
     *
     */
    var map_options = {
        'zoom':14,
        'scrollwheel':false,
        'draggableCursor':'default',
        'center': new google.maps.LatLng(-23.56367, -46.65372),
        'mapTypeId': google.maps.MapTypeId.ROADMAP
        }
    googlemap = new google.maps.Map(document.getElementById("mpv_canvas"), map_options);
    var googlemarker = null;

    function fill_fields(lat, lng) {
        $("#mpv_lat").val(lat);
        $("#mpv_lon").val(lng);
    }

    // place a marker on the map
    function load_post_marker(lat, lng) {
        try{
            lat = parseFloat(lat);
            lng = parseFloat(lng);
            if(lat && lng) {
                if(googlemarker) {
                    googlemarker.setPosition(new google.maps.LatLng(lat, lng));
                }else{
                    fill_fields(lat, lng);
                    googlemarker = new google.maps.Marker({
                        map: googlemap,
                        draggable: true,
                        position: new google.maps.LatLng(lat, lng)
                    });
                    googlemap.panTo(googlemarker.getPosition());
                }
            }
            return googlemarker;
        } catch(e) {  }
        return null;
    }

    // plot marker on saved location and define map drag event
    if(load_post_marker($("#mpv_lat").val(), $("#mpv_lon").val())) {
        google.maps.event.addListener(googlemarker, 'drag', function(e) {fill_fields(e.latLng.lat(), e.latLng.lng());});
    }

    // define map click event
    var clicklistener = google.maps.event.addListener(googlemap, 'click', function(event) {
        place_marker(event.latLng);
    });

    // callback for map click event
    var place_marker = function(location) {
        if(googlemarker === null) {
            load_post_marker(location.lat(), location.lng());
        } else {
            googlemarker.setPosition(location);
            fill_fields(location.lat(), location.lng());
            google.maps.event.addListener(googlemarker, 'drag', function(e) {fill_fields(e.latLng.lat(), e.latLng.lng());});
        }
        if(clicklistener){
            google.maps.event.removeListener(clicklistener);
        }
        google.maps.event.addListener(googlemap, 'click', function(e) {
            googlemarker.setPosition(e.latLng);
            fill_fields(e.latLng.lat(), e.latLng.lng());
        });
    }

    // activate google service
    var geocoder = new google.maps.Geocoder();

    // callback to handle google geolocation result
    function geocode_callback(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var location = results[0].geometry.location;
            googlemap.setCenter(location);
            fill_fields(location.lat(), location.lng());

            if(googlemarker) {
                googlemarker.setPosition(location)
            } else {
                googlemarker = new google.maps.Marker({
                    map: googlemap,
                    draggable: true,
                    position: location
                });
            }
        }
    }

    // the search bar, where user can type an address
    $("#mpv_search_address").keypress(function(e){
        if(e.charCode===13 || e.keyCode===13){ // carriage return
            geocoder.geocode({'address': $(this).val()}, geocode_callback);
            return false;
        }
    });

    // the button to place marker on specified coords
    $("#mpv_load_coords").click(function(){load_post_marker($("#mpv_lat").val(), $("#mpv_lon").val())});

    $('#mpv_lat,#mpv_lon').keypress(function(e) {
        if(e.charCode===13 || e.keyCode===13){ // carriage return
            $("#mpv_load_coords").click();
            return false;
        }
    });

    // change the map pin
    $("#mapasdevista_metabox .iconlist input").change(function(e) {
        if(googlemarker) {
            var id = $(this).attr('id');
            var anchor = pinsanchor[id];
            var img_el = $(this).parents('div.icon').find('img');

            var pin = new google.maps.MarkerImage(img_el.attr('src'),
                        new google.maps.Size(img_el.attr('width'), img_el.attr('height')),
                        new google.maps.Point(0, 0),
                        new google.maps.Point(anchor.x, anchor.y));
            googlemarker.setIcon(pin);
        }
    }).change();
    $("#mapasdevista_metabox .iconlist :checked").change();

    // let the user resize map
    $("#mpv_canvas").resizable({ handles: 's'});

    /**
     *
     * Handle functionalities to image as a map
     *
     */
    function available_height() { return Math.floor($(window).height()*0.95); }
    function available_width() { return Math.floor($(window).width()*0.95); }

    var $dialog = $('#dialog').dialog({
                    'modal': true,
                    'autoOpen' : false,
                    'title': "Pin location"
                });
    var $panel = $('#dialog .panel')
                .mousedown(function(ed) {
                    if(ed.target.className.match(/pin/)) {
                        return false;
                    }

                    var start_Y = this.scrollTop;
                    var start_x = this.scrollLeft;

                    $panel.mousemove(function(em) {
                        this.scrollTop  = start_Y + ed.pageY - em.pageY;
                        this.scrollLeft = start_x + ed.pageX - em.pageX;
                    });
                });
    $(document).mouseup(function(e){ $panel.unbind('mousemove'); });

    // vary according to user selection
    var $map_pin_input = null;
    var $map_coords_input = null;

    // make a draggable pin and create event that
    // stores pin coords when dragging stops
    var $pin = $panel.find('img.pin').draggable({
        'stop': function(e,ui) {
            var coord = ($pin.css('left')+","+$pin.css('top')).replace(/px/g,'');
            $map_coords_input.val(coord);
        }
    });

    // event that let user change $pin image by choosing
    // available images in the .iconlist inside #dialog box
    $dialog.find('.iconlist .icon').click(function() {
        var $img = $(this).find('img');
        $map_pin_input.val($(this).attr('id').replace(/^[^0-9]+/,''));
        $pin.attr('src', $img.attr('src'));

        $dialog.find('.iconlist .icon').removeClass('selected');
        $(this).addClass('selected');
    });
    
    
    // Loads the content of the dialog
    $("#image-maps img").click(function(e) {
        if($panel.find('img').length > 1){
            $panel.find('img:last').remove();
        }

        // load a new Image object to get real dimensions
        var image = new Image();
        image.src = $(this).siblings('.full_image_src').val();
        $panel.append(image);

        // remove the really annoying browser behavior
        image.onmouseup   = function(e) {return false;};
        image.onmousedown = function(e) {return false;};
        image.onmousemove = function(e) {return false;};

        // chrome workaround
        var dim = {w: Math.min(image.width, available_width()),
                    h: Math.min(image.height, available_height())};

        // the dialog dimensions
        $dialog.dialog('option', 'width', dim.w)
                .dialog('option', 'height', dim.h);

        // bind the coord input to be used in this map
        $map_coords_input = $(this).parents('.icon')
                                .find('input[type=checkbox]')
                                .attr('checked',true);

        // bind the pin input to be used in this map
        $map_pin_input = $(this).parents('.icon')
                                .find('input[name][type=hidden]');

        // load the selected if exist, otherwise select first from .iconlist
        var icon_id = $map_pin_input.val();
        if(icon_id) {
            $dialog.find('.iconlist .icon').removeClass('selected');
            $pin.attr('src', $dialog.find('.iconlist #icon-'+icon_id+' img').attr('src'));
            $dialog.find('.iconlist #icon-'+icon_id).addClass('selected');
        } else {
            $dialog.find('.iconlist .icon').removeClass('selected');
            $pin.attr('src', $dialog.find('.iconlist .icon:first img').attr('src'));
            $dialog.find('.iconlist .icon:first').addClass('selected').trigger('click');
        }

        // set pin_coords to string avoid 'null' error
        var pin_coords = ($map_coords_input.val()||'').match(/^(-?[0-9]+),(-?[0-9]+)$/);

        // move pin to stored position if exist, otherwise 0,0
        if(pin_coords) {
            $pin.css('left', pin_coords[1]+"px")
                .css('top', pin_coords[2]+"px");
        } else {
            $pin.css('top', 0).css('left', 0);
        }

        // finally, open the dialog
        $dialog.dialog('open');
    });
});
