jQuery(document).ready(function() {
    var $ = jQuery;

    var map_options = {
        'zoom':14,
        'scrollwheel':true,
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
        if(e.charCode===13){ // carriage return
            geocoder.geocode({'address': $(this).val()}, geocode_callback);
            return false;
        }
    });

    // the button to place marker on specified coords
    $("#mpv_load_coords").click(function(){load_post_marker($("#mpv_lat").val(), $("#mpv_lon").val())});

    // change the map pin
    $("#mapasdevista_metabox .iconlist input").change(function(e) {
        if(googlemarker) {
            var id = $(this).attr('id');
            var anchor = pinsanchor[id];
            var img_el = $(this).parents('div.icon').find('img');

            var pin = new google.maps.MarkerImage(img_el.attr('src'),
                        new google.maps.Size(img_el.width(), img_el.height()),
                        new google.maps.Point(0, 0),
                        new google.maps.Point(anchor.x, anchor.y));
            googlemarker.setIcon(pin);
        }
    }).change();

    // let the user resize map
    $("#mpv_canvas").resizable({ handles: 's'});
});
