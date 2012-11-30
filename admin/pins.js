jQuery(document).ready(function() {
    var $ = jQuery;
    /* the user will change coords values clicking on image or using
     * keyboard arrows. both image and <input /> delegate focus to
     * the <input />*/

    // retrieve image dimensions
    var image = new Image();
    image.src = $("#the-image").attr('src');

    var image_panel_el = document.getElementById("image-panel");

    // represent the desired point that will be the anchor on map
    var image_anchor = {
        'x': 0,
        'y': 0,

        // set X position and draw it on screen
        'set_x' : function (x) {
            this.x = x < 0 ? 0 : x > image.width ? image.width : x ; // 0 < x < image.width
            $("#image-x-ruler").css('left', image_panel_el.offsetLeft + this.x);
        },

        // set Y position and draw it on screen
        'set_y' : function (y) {
            this.y = y < 0 ? 0 : y > image.height ? image.height : y ; // 0 < x < image.width
            $("#image-y-ruler").css('top', image_panel_el.offsetTop + this.y);
        }
    }

    // draw stored values when page LOAD
    $(window).load(function() {
        // retrive image wrapper element and fix its dimensions
        $(image_panel_el).css('width', image.width+'px').css('height', image.height+'px');

        var initial = $("#pin_anchor").focus().val().match(/^([0-9]+),([0-9]+)$/);
        if( initial ) {
            image_anchor.set_x(parseInt(initial[1]));
            image_anchor.set_y(parseInt(initial[2]));
        } else {
            image_anchor.set_x(Math.floor(image.width / 2));
            image_anchor.set_y(Math.floor(image.height / 2));
        }
    });

    // fix the image and rulers when user resizes the browser
    $(window).resize(function() {
        image_anchor.set_x(image_anchor.x);
        image_anchor.set_y(image_anchor.y);
    });

    // keyboard event to move rulers
    var accel = 0.4;
    var veloc = 1;
    $("#pin_anchor").keydown(function(e) {
        if(e.keyCode == 37) {        // <
            if(image_anchor.x > 0) image_anchor.set_x(Math.floor(image_anchor.x - veloc));
        } else if(e.keyCode == 38) { // ^
            if(image_anchor.y > 0) image_anchor.set_y(Math.floor(image_anchor.y - veloc));
        } else if(e.keyCode == 39) { // >
            if(image_anchor.x < image.width) image_anchor.set_x(Math.floor(image_anchor.x + veloc));
        } else if(e.keyCode == 40) { // v
            if(image_anchor.y < image.height) image_anchor.set_y(Math.floor(image_anchor.y + veloc));
        }
        if( e.keyCode > 36 && e.keyCode < 41 ){
            $(this).val(image_anchor.x + "," + image_anchor.y);
            veloc = veloc + accel;
        }
        return false;
    });
    // reset velocity
    $("#pin_anchor").keyup(function(e) { veloc = 1; });

    // mouse events to move rulers
    var mousepressed = false;
    $("#the-image").mousedown(function(e) {
        mousepressed = true;
        // we cant use offsetX and offsetY, firefox does not support it
        image_anchor.set_x(Math.round(e.clientX - $(e.target).offset().left));
        image_anchor.set_y(Math.round(e.clientY - $(e.target).offset().top));
        $("#pin_anchor").val(image_anchor.x + "," + image_anchor.y);
        return false;
    });
    $("#the-image").mousemove(function(e) {
        if(mousepressed){
            // we cant use offsetX and offsetY, firefox does not support it
            image_anchor.set_x(Math.round(e.clientX - $(e.target).offset().left));
            image_anchor.set_y(Math.round(e.clientY - $(e.target).offset().top));
            $("#pin_anchor").val(image_anchor.x + "," + image_anchor.y);
        }
        return false;
    });
    $(document).mouseup(function(e) { mousepressed = false;});
    $("#image-panel-background").mouseup(function(e) {$("#pin_anchor").focus();});
});
