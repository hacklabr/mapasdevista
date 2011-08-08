<?php 

$theme_options = get_option('mapasdevista_theme_options'); 

$opacity = (int) $theme_options['bg_opacity'];
if (!is_int($opacity)) $opacity = 80;
$filtersOpacity = $opacity >= 5 ? $opacity - 5 : 0;
$opacity = $opacity / 100;
$filtersOpacity = $filtersOpacity / 100;

$bgColor = 'rgba(' . $theme_options['bg_color']['r'] . ',' . $theme_options['bg_color']['g'] . ', ' . $theme_options['bg_color']['b'] . ', ' . $opacity . ')';
$bgFiltersColor = 'rgba(' . $theme_options['bg_color']['r'] . ',' . $theme_options['bg_color']['g'] . ', ' . $theme_options['bg_color']['b'] . ', ' . $filtersOpacity . ')';
$fontColor = 'rgb(' . $theme_options['font_color']['r'] . ',' . $theme_options['font_color']['g'] . ', ' . $theme_options['font_color']['b'] . ')';
$themeColor = 'rgb(' . $theme_options['theme_color']['r'] . ',' . $theme_options['theme_color']['g'] . ', ' . $theme_options['theme_color']['b'] . ')';

?>

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

/* Results */
#toggle-results { background:<?php echo $bgColor; ?>; cursor:pointer; padding:4px 4px 0 4px; position:fixed; right:0; top:120px; }
#results { background:<?php echo $bgColor; ?>; color:<?php echo $fontColor; ?>; display:none; max-height:65%; overflow:auto; padding:9px; position:fixed; right:35px; top:120px; width:30%; }
#results h1 { font-size:18px; margin-bottom:27px; }
.result { border-bottom:2px solid rgba(0,0,0,0.5); display:none; margin-bottom:27px; }
.result .pin { float:left; width:60px; }
.result .content { margin-left:60px; }
.result h1 { margin-bottom:3px !important; }
.result h1 a { color:<?php echo $fontColor; ?>; text-decoration:none; text-transform:uppercase; }
.result h1 a:hover { text-decoration:underline; }
.result p.date { background:<?php echo $themeColor; ?>; display:inline-block; font-size:14px; margin-bottom:3px; padding:0 3px; }
.result p.author a { color:<?php echo $themeColor; ?>; text-decoration:none; }
.result p.author a:hover { text-decoration:underline; }

/* Ballon */
.balloon { background:<?php echo $bgColor; ?>; border-radius:9px; -moz-border-radius:9px; }

/* Post Overlay */
#post_overlay { display:none; position:relative; }
#post_overlay a#close_post_overlay {  background:<?php echo $bgColor; ?>; cursor:pointer; padding:4px 4px 0 4px; position:absolute; right:0; top:120px; z-index:1000; }
#post_overlay a#close_post_overlay:hover { background:<?php echo $themeColor; ?>; }
#post_overlay .entry { background:<?php echo $bgColor; ?> !important; height:400px; overflow:auto; padding:36px; position:absolute; right:35px; top:120px; width:60%; z-index:1000; }
#post_overlay .date { font-family:"Bebas"; font-size:22px; }
#post_overlay .entry-content { font-size:13px; padding:0 36px 0 18px; }

section#entry-gallery { margin-bottom:27px; }
section#entry-gallery figure { float:left; margin:9px; }
section#entry-gallery figure img { display:block; }

section#entry-image { margin-bottom:36px; }

/* Misc */
.box { padding:18px; }

.alignleft  { float:left; }
.alignright { float:right; }

/* Clearfix */
.clearfix:after, .container:after { content:"\0020";display:block;height:0;clear:both;visibility:hidden;overflow:hidden; }
.clearfix, .container { display:block; }
.clear { clear:both; }