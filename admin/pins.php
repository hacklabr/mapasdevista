<?php

add_action('init', 'mapasdevista_save_pins');


function remove_pin($pin_id) {
    global $wpdb;

    wp_delete_attachment($pin_id);
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_mpv_%' AND meta_value = '{$pin_id}'");
}


/**
 * Create or update pins.
 */
function mapasdevista_save_pins() {
    $error = array();

    if(isset($_POST['submit_pin']) && $_POST['submit_pin'] === 'new') {

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
                update_post_meta($r, '_pin_anchor', array('x'=>0, 'y'=>0));
                wp_redirect(add_query_arg(array('action' => 'edit', 'pin' => $r)));
            }
        }
    } elseif(isset($_POST['submit_pin']) && $_POST['submit_pin'] === 'edit') {
        if(isset($_GET['pin']) && is_numeric($_GET['pin'])) {
            $pin_id = intval(sprintf("%d", $_GET['pin']));

            if(isset($_POST['pin_anchor']) && preg_match('/^([0-9]+),([0-9]+)$/', $_POST['pin_anchor'], $coords)) {
                $anchor = array('x' => intval($coords[1]), 'y' => intval($coords[2]) );
                update_post_meta($pin_id, '_pin_anchor', $anchor);
            }

            if(isset($_POST['pin_clickable']) && $_POST['pin_clickable'] === 'no') {
                update_post_meta($pin_id, '_pin_clickable', 'no');
            } else {
                delete_post_meta($pin_id, '_pin_clickable');
            }

            wp_redirect(add_query_arg(array('action' => 'edit', 'pin' => $pin_id)));
        }
    } else if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['pin'])) {
        if(isset($_GET['pin']) && is_numeric($_GET['pin'])) {
            $pin_id = intval(sprintf("%d", $_GET['pin']));
            $pin = get_post($pin_id);

            if($pin) {
                remove_pin($pin_id);
                wp_redirect(admin_url('admin.php?page=mapasdevista_pins_page&msg=pin-deleted'));
                die;
            }
        }
        wp_redirect(admin_url('admin.php?page=mapasdevista_pins_page&msg=pin-does-not-exist'));
        die;
    }

}

/**
 * Delegate the request to mapasdevista_pin_edit or mapasdevista_pins_list,
 * depending on user action.
 */
function mapasdevista_pins_page() {
    if(isset($_GET['action']) && $_GET['action'] === 'edit') {
        if(isset($_GET['pin']) && is_numeric($_GET['pin'])) {
            $pin_id = intval(sprintf("%d", $_GET['pin']));
            $pin = get_post($pin_id);

            if($pin) {
                $pin->anchor = get_post_meta($pin_id, '_pin_anchor', true);
                $pin->clickable = get_post_meta($pin_id, '_pin_clickable', true);
                mapasdevista_pin_edit($pin);
            } else {
                echo '<div class="error"><p>' . __("Sorry, no such page.", 'mapasdevista') . '</p></div>';
            }
        } else {
            echo '<div class="error"><p>' . __("Invalid post ID.", 'mapasdevista') . '</p></div>';
        }
    } else {
        mapasdevista_pins_list();
    }
}

/**
 * Print the pin edition form. This form is a little app linked that
 * depends of admin/pin.js.
 */
function mapasdevista_pin_edit($pin) { ?>
<div class="wrap pinpage">
    <h3><?php _e("Edit pin",'mapasdevista');?></h3>

    <form id="editpinform" method="post">
        <input type="hidden" name="submit_pin" value="edit"/>

        <ul>
            <li>
                <label for="pin_anchor" class="small"><?php _e("Pin anchor");?>:</label>
                <input id="pin_anchor" name="pin_anchor" type="text" value="<?php print $pin->anchor['x'].','.$pin->anchor['y'];?>"/>
            </li>
            <li>
                <span class="small"><?php _e("Clickable pin");?>:</span>
                <ul>
                    <li>
                        <label for="pin_clickable_yes" class="small"><?php _e("Yes");?>:</label>
                        <input id="pin_clickable_yes" name="pin_clickable" type="radio" value=""<?php if(!$pin->clickable) echo ' checked';?>/>
                    </li>
                    <li>
                        <label for="pin_clickable_no" class="small"><?php _e("No");?>:</label>
                        <input id="pin_clickable_no" name="pin_clickable" type="radio" value="no"<?php if($pin->clickable == 'no') echo ' checked';?>/>
                    </li>
                </ul>
            </li>
        </ul>

        <div id="image-panel-background">
            <div id="image-panel">
                <img id="the-image" src="<?php echo $pin->guid;?>"/>
            </div>

            <div id="image-x-ruler"></div>
            <div id="image-y-ruler"></div>
        </div>

        <p><input type="submit" value="Submit"/></p>
    </form>
</div>
<?php
}

/**
 * List available pins and place an <input/> to
 * upload new pins.
 */
function mapasdevista_pins_list() {
    $args = array(
        'post_type' => 'attachment',
        'meta_key' => '_pin_anchor',
        'posts_per_page' => '-1'
    );

    $pins = get_posts($args);

?>
<div class="wrap pinpage">

<h3><?php _e("Available pins", "mapasdevista");?></h3>
<div id="pinlist">
<?php foreach($pins as $pin): ?>
    <div class="icon">
        <a href="admin.php?page=mapasdevista_pins_page&action=edit&pin=<?php echo $pin->ID;?>"><?php echo  wp_get_attachment_image($pin->ID, array(64,64));?></a>
        <div class="icon-info">
            <span class="icon-name"><?php echo $pin->post_name;?></span>
        </div>
        <a href="admin.php?page=mapasdevista_pins_page&action=delete&pin=<?php echo $pin->ID;?>"><?php _e('Apagar');?></a>
    </div>
<?php endforeach;?>
</div>
<div class="clear"></div>


<h3><?php _e("New pin", 'mapasdevista'); ?></h3>
<form id="newpinform" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submit_pin" value="new"/>

    <ul>
        <li>
            <label for="mpv_pinfile"><?php _e("Upload");?>:</label>
            <input type="file" name="pin_file" id="mpv_pinfile"/>
        </li>
<?php /*<li>
            <label for="mpv_pinurl">Endereço do Pin:</label>
            <input type="text" name="pin_url" id="mpv_pinurl"/>
        </li>*/?>
        <li><input type="submit" value="Upload"/></li>
    </ul>
</form>

</div>
<?php
}
