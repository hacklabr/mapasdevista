(function($){

    $(document).ready(function() {
    
        mapstraction = new mxn.Mapstraction('map', mapinfo.api);
        
        mapstraction.setCenterAndZoom(new mxn.LatLonPoint(parseFloat(mapinfo.lat), parseFloat(mapinfo.lng)), parseInt(mapinfo.zoom));
        
        if (mapinfo.api == 'googlev3') {
            mapstraction.setMapType(mxn.Mapstraction[mapinfo.type.toUpperCase()]);
        }
        
        
        
        // Load posts
        
        jQuery.ajax({
            type: 'post',
            
            url: mapinfo.ajaxurl,
            data: {
                teste: 'teste',
                action: 'mapasdevista_get_posts'
            },
            success: function(data) {
                
                
                console.log(data);
                
            }
            
        });
        
    
    });
    
})(jQuery);

