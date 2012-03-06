<?php 


$theme_options = wp_parse_args( 
                    get_option('mapasdevista_theme_options'), 
                    get_theme_default_options()
                );
$opacity = (int) $theme_options['bg_opacity'];
if (!is_int($opacity)) $opacity = 80;
$filtersOpacity = $opacity >= 5 ? $opacity - 5 : 0;
$opacity = $opacity / 100;
$filtersOpacity = $filtersOpacity / 100;

if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8')){
    $bgColor = 'rgb(' . $theme_options['bg_color']['r'] . ',' . $theme_options['bg_color']['g'] . ', ' . $theme_options['bg_color']['b'].')';
    $bgFiltersColor = 'rgb(' . $theme_options['bg_color']['r'] . ',' . $theme_options['bg_color']['g'] . ', ' . $theme_options['bg_color']['b'] . ')';
    $fontColor = 'rgb(' . $theme_options['font_color']['r'] . ',' . $theme_options['font_color']['g'] . ', ' . $theme_options['font_color']['b'] . ')';
    $themeColor = 'rgb(' . $theme_options['theme_color']['r'] . ',' . $theme_options['theme_color']['g'] . ', ' . $theme_options['theme_color']['b'] . ')';

}else{
    $bgColor = 'rgba(' . $theme_options['bg_color']['r'] . ',' . $theme_options['bg_color']['g'] . ', ' . $theme_options['bg_color']['b'] . ', ' . $opacity . ')';
    $bgFiltersColor = 'rgba(' . $theme_options['bg_color']['r'] . ',' . $theme_options['bg_color']['g'] . ', ' . $theme_options['bg_color']['b'] . ', ' . $filtersOpacity . ')';
    $fontColor = 'rgb(' . $theme_options['font_color']['r'] . ',' . $theme_options['font_color']['g'] . ', ' . $theme_options['font_color']['b'] . ')';
    $themeColor = 'rgb(' . $theme_options['theme_color']['r'] . ',' . $theme_options['theme_color']['g'] . ', ' . $theme_options['theme_color']['b'] . ')';    
}

?>

body {padding: 0px !important; margin: 0px !important;}

/* Typography */
body, h1, h2, h3, h4, h5, h6 { color:<?php echo $fontColor; ?>; }
a { color:<?php echo $themeColor; ?>; }

/* Layout */
body { min-width:960px; }

/* Important!!! */
#map { height:100%; overflow:hidden; position:absolute; width:100%; }

/* Title of the Blog */
#blog-title     { left:90px; position:fixed; top:6px; max-width:237px; }
#blog-title img { max-width:237px; }

/* Generic classes to use in your child themes */
.mapasdevista-background                    {background:<?php echo $bgColor; ?>;}
.mapasdevista-background-with-hover         {background:<?php echo $bgColor; ?>;}
.mapasdevista-background-with-hover:hover   {background:<?php echo $themeColor; ?>;}
.mapasdevista-fontcolor             { color:<?php echo $fontColor; ?>; }
.mapasdevista-themecolor            { color:<?php echo $themeColor; ?>; }

/* Top Menu */
.map-menu-top                       { position:fixed; right:124px; top:6px; z-index:10; }
.map-menu-top ul                    { list-style:none; margin:0; padding:0; }
.map-menu-top ul li                 { float:left; padding:0; }
.map-menu-top ul li a               { background:<?php echo $bgColor; ?>; color:<?php echo $fontColor; ?>; display:block; padding:6px 9px; text-decoration:none; }
.map-menu-top ul li a:hover         { background:<?php echo $themeColor; ?>; }
.map-menu-top ul li:hover ul        { display:block; }
.map-menu-top ul li:hover ul li     { float:none; }
.map-menu-top ul ul                 { display:none; position:absolute; }

/* Side Menu */
#toggle-side-menu                   { position:absolute; top:120px; }
#toggle-side-menu-icon              { background:<?php echo $bgColor; ?>; padding:3px; }
#toggle-side-menu-icon:hover        { background:<?php echo $themeColor; ?>; }
.map-menu-side                      { display:none; left:33px; position:absolute; top:120px; z-index:10; }
.map-menu-side ul                   { list-style:none; margin:0; padding:0; width:160px; }
.map-menu-side ul li a              { background:<?php echo $bgColor; ?>; color:<?php echo $fontColor; ?>; display:block; padding:6px 9px; text-decoration:none; }
.map-menu-side ul li a:hover        { background:<?php echo $themeColor; ?>; }
.map-menu-side ul li:hover ul       { display:block; left:160px; }
.map-menu-side ul li:hover ul li    { float:none; }
.map-menu-side ul ul                { display:none; position:absolute; top:0; }

/* Top and Side Menu Links */
li.current-menu-item a              { background:<?php echo $themeColor; ?> !important; }
li.current-menu-item li a:hover     { background:<?php echo $bgColor; ?> !important; }

/* Seach Form */
#search { background:<?php echo $bgColor; ?>; bottom:0; height:28px; position:fixed; width:100%; }
#search-icon { background:<?php echo $themeColor; ?>; float:left; padding:3px; }
#searchform { height:28px; float:left; }
#searchform input[type="text"] { background:none; border:none; color:<?php echo $fontColor; ?>; float:left; height:28px; margin:0; padding:0 10px; width:140px; }
#searchform input[type="image"] { background:<?php echo $themeColor; ?>; padding:3px; }

/* Filters */
#toggle-filters { background:<?php echo $themeColor; ?>; color:<?php echo $fontColor; ?>; cursor:pointer; float:right; font-weight:bold; padding:4px 14px 2px 10px; text-transform:uppercase; width:177px;}
#toggle-filters img { margin-right:6px; vertical-align:middle; }
#filters { background:<?php echo $bgFiltersColor; ?>; bottom:0; color:<?php echo $fontColor; ?>; height:0; overflow:auto; position:fixed; width:100%; }
#filters h3 { background:rgba(255,255,255,0.2); color:<?php echo $fontColor; ?>; display:inline-block; font-size:12px; font-weight:bold; margin-left:-18px; padding:9px 18px; text-transform:uppercase; }
#filters ul { list-style:none; float:left; margin:0; padding:0; width:20%; }
#filters ul ul { border:none; float:none; width:auto; }
#filters ul li { margin:0 6px 6px 0; width:100%; }
#filters ul.children li { margin-left:18px; }

/* Posts Loader */
#posts-loader { display:none; background:<?php echo $bgColor; ?>; font-family:"Bebas"; font-size:22px; padding:4px 4px 0 4px; position:fixed; right:0; top:82px; }
#posts-loader span { font-size:18px; }

/* Results */
#toggle-results { background:<?php echo $bgColor; ?>; cursor:pointer; padding:4px 4px 0 4px; position:fixed; right:0; top:120px; }
#results { background:<?php echo $bgColor; ?>; color:<?php echo $fontColor; ?>; display:none; max-height:65%; overflow:auto; padding:9px; position:fixed; right:35px; top:120px; width:30%; }
#results h1 { font-size:18px; margin-bottom:27px; }
.result { border-bottom:2px solid rgba(0,0,0,0.5); margin-bottom:27px; }
.result .pin { float:left; width:60px; }
.result .pin img { height:auto; max-width:60px; }
.result .content { margin-left:60px; }
.result h1 { margin-bottom:3px !important; }
.result h1 a { color:<?php echo $fontColor; ?>; text-decoration:none; text-transform:uppercase; }
.result h1 a:hover { text-decoration:underline; }
.result p.date { background:<?php echo $themeColor; ?>; display:inline-block; font-size:14px; margin-bottom:3px; padding:0 3px; }
.result p.author a { color:<?php echo $themeColor; ?>; text-decoration:none; }
.result p.author a:hover { text-decoration:underline; }

/* Ballon */
.balloon            { background:#fff; color:<?php echo $bgColor; ?>; padding:18px; width:270px; }
.balloon h1         { font-size:22px; }
.balloon h1 a       { color:<?php echo $bgColor; ?>; text-decoration:none; }
.balloon h1 a:hover { color:<?php echo $themeColor; ?>; text-decoration:underline; }
.balloon img        { display:block; padding:0; }
.balloon .date      { background:<?php echo $themeColor; ?>; color:<?php echo $fontColor; ?>; font-family:"Bebas"; font-size:18px; padding:0 3px; }
.balloon .entry-gallery          { position:relative; height:203px; width:270px; }
.balloon .entry-gallery img   { position:absolute; }

/* Post Overlay */
#post_overlay                               { display:none; position:relative; }
#post_overlay a#close_post_overlay          { background:<?php echo $bgColor; ?>; cursor:pointer; padding:4px 4px 0 4px; position:absolute; right:0; top:120px; z-index:1000; }
#post_overlay a#close_post_overlay:hover    { background:<?php echo $themeColor; ?>; }
#post_overlay .entry                        { background:<?php echo $bgColor; ?> !important; height:400px; overflow:auto; padding:36px; position:absolute; right:35px; top:120px; width:60%; z-index:1000; }
#post_overlay .date                         { font-family:"Bebas"; font-size:22px; }
#post_overlay #entry-content                { font-size:13px; padding:0 36px; }
#post_overlay .entry-meta                   { border-bottom:2px solid <?php echo $bgColor; ?>; padding-bottom:9px; margin-bottom:27px; }

/* misc */
tbody tr:nth-child(even) td, tbody tr.even td {background:<?php echo $bgColor; ?>;}

#commentform { margin-bottom:18px;  }
#commentform label { display:block; }
#commentform input[type="text"] { border:none; padding:6px; width:150px; }
#commentform textarea#comment { border:none; clear:both; height:80px; width:660px; }

a.comment-reply-link { background:<?php echo $themeColor; ?>; color:<?php echo $fontColor; ?>; padding:3px 6px; text-decoration:none; }
.comment { border-bottom:2px solid <?php echo $bgColor; ?>; padding-bottom:9px; margin-bottom:18px; }
img.avatar { margin:3px 10px 9px 0px; }

#mapasdevista-gallery-image {max-width: 80%; max-height: 80%; padding:11px; background: <?php echo $bgColor; ?>;}

#mapasdevista-gallery-image #mapasdevista-gallery-close { float:right; cursor:pointer; width:27px; height:27px; background:url(<?php echo mapasdevista_get_baseurl().'/img/close.png'; ?>);}
#mapasdevista-gallery-image #mapasdevista-gallery-close:hover {background-color: <?php echo $themeColor; ?>}
