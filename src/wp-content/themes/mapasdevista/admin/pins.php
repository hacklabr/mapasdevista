<?php

add_action('init', 'mapasdevista_save_pins');

function mapasdevista_save_pins() {
    $error = array();

    if(isset($_POST['submit_pin']) && $_POST['submit_pin'] === 'true') {

        if(isset($_FILES['pin_file']) && $_FILES['pin_file']['size'] > 0) {
            include(ABSPATH . 'wp-admin/includes/file.php');  // para funcionar o
            include(ABSPATH . 'wp-admin/includes/image.php'); // media_handle_upload
            include(ABSPATH . 'wp-admin/includes/media.php'); //

            $r = media_handle_upload('pin_file', NULL);

            if(is_wp_error($r)) {
                function mapasdevista_save_pin_error_notice () {
                    echo '<div class="error"><p>' . __('Could not create directory.') . '</p></div>';
                };
                add_action('all_admin_notices', 'mapasdevista_save_pin_error_notice');
            } else {
                $pin_meta = get_post_meta($r, '_wp_attachment_metadata', true);

                wp_redirect(add_query_arg(array('action' => 'edit', 'pin' => $r)));
            }
        }
    }
}


function mapasdevista_pins_page() {

    $args = array(
        'post_type' => 'attachment',
        'meta_key' => 'pin',
    );

    $pins = get_posts($args);

?>
<div class="wrap pinpage">

<ul id="pinlist">
</ul>
<pre>
<?php print_r($pins);?>
</pre>


<form id="newpinform" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submit_pin" value="true"/>

    <h3><?php _e("Novo pin", 'mapasdevista'); ?></h3>
    <ul>
        <li>
            <label for="mpv_pinfile">Selecionar arquivo:</label>
            <input type="file" name="pin_file" id="mpv_pinfile"/>
        </li>
        <li>
            <label for="mpv_pinurl">Endereço do Pin:</label>
            <input type="text" name="pin_url" id="mpv_pinurl"/>
        </li>
        <li><input type="submit" value="Upload"/></li>
    </ul>
</form>

</div>
<?php
}
