<?php 

// IMAGENS
function get_theme_image($name) {
  return get_bloginfo('stylesheet_directory') . '/img/' . $name;
}

function theme_image($name, $params = null) {
  $extra = '';

  if(is_array($params)) {
    foreach($params as $param=>$value){
      $extra.= " $param=\"$value\" ";		
    }
  }

  echo '<img src="', get_theme_image($name), '" ', $extra ,' />';
}

?>
