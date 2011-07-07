(function($){

    $(document).ready(function() {
    
        mapstraction = new mxn.Mapstraction('map', mapinfo.api);
        
        mapstraction.setCenterAndZoom(new mxn.LatLonPoint(parseFloat(mapinfo.lat), parseFloat(mapinfo.lng)), parseInt(mapinfo.zoom));
        
        if (mapinfo.api == 'googlev3') {
            mapstraction.setMapType(mxn.Mapstraction[mapinfo.type.toUpperCase()]);
        }
        
        
        
        // Load posts
        
        $.post(
            mapinfo.ajaxurl,
            {
                get: 'totalPosts',
                action: 'mapasdevista_get_posts',
                page_id: mapinfo.page_id
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
                    offset: offset,
                    total: total,
                    posts_per_page: posts_per_page
                },
                success: function(data) {
                    
                    console.log('loaded posts:'+offset);
                    console.log(data.posts);
                    
                    if (data.newoffset != 'end') {
                        loadPosts(total, data.newoffset);
                    } else {
                        console.log('fim');
                    }
                }
                
            });
        
        }
        
        
        
    
    });
    
})(jQuery);

