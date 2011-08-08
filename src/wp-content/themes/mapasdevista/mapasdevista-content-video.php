<?php

$content = get_the_content();


// gets the first video in the post
preg_match_all('|http://[^"\'\s]+|', $content, $m);
foreach ($m[0] as $match) {
    if ( preg_match('#http://(www\.)?youtube.com/watch.*#i', $match) ) {
        $video = $match;
        break;
    }
    if ( preg_match('#http://(www\.)?vimeo\.com/.*#i', $match) ) {
        $video = $match;
        break;
    }
}

?>

<?php echo apply_filters('the_content', $video); ?>