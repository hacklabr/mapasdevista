(function($){
    $(document).ready(function() {
        $('#theme_color_box').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                //console.log(this);
                $('#theme_color_box').css('backgroundColor', '#' + hex);
                $('#theme_color_r').val(rgb.r);
                $('#theme_color_g').val(rgb.g);
                $('#theme_color_b').val(rgb.b);
            },
            onBeforeShow: function () {
                var r = $('#theme_color_r').val();
                var g = $('#theme_color_g').val();
                var b = $('#theme_color_b').val();
                var color = {r: r, g: g, b: b};
                $(this).ColorPickerSetColor(color);
            },
            
        });
        
        $('#theme_color_box').css('background-color', 'rgb(' + $('#theme_color_r').val() +','+ $('#theme_color_g').val() +','+ $('#theme_color_b').val() +')');
        
        
        
        $('#bg_color_box').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                //console.log(this);
                $('#bg_color_box').css('backgroundColor', '#' + hex);
                $('#bg_color_r').val(rgb.r);
                $('#bg_color_g').val(rgb.g);
                $('#bg_color_b').val(rgb.b);
            },
            onBeforeShow: function () {
                var r = $('#bg_color_r').val();
                var g = $('#bg_color_g').val();
                var b = $('#bg_color_b').val();
                var color = {r: r, g: g, b: b};
                $(this).ColorPickerSetColor(color);
            },
            
        });
        
        $('#bg_color_box').css('background-color', 'rgb(' + $('#bg_color_r').val() +','+ $('#bg_color_g').val() +','+ $('#bg_color_b').val() +')');
        
        
        
        $('#font_color_box').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                //console.log(this);
                $('#font_color_box').css('backgroundColor', '#' + hex);
                $('#font_color_r').val(rgb.r);
                $('#font_color_g').val(rgb.g);
                $('#font_color_b').val(rgb.b);
            },
            onBeforeShow: function () {
                var r = $('#font_color_r').val();
                var g = $('#font_color_g').val();
                var b = $('#font_color_b').val();
                var color = {r: r, g: g, b: b};
                $(this).ColorPickerSetColor(color);
            },
            
        });
        
        $('#font_color_box').css('background-color', 'rgb(' + $('#font_color_r').val() +','+ $('#font_color_g').val() +','+ $('#font_color_b').val() +')');
        
        
        
        
        
    });
})(jQuery);
