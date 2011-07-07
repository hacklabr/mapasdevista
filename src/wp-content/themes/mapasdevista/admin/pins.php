<?php

add_action('init', 'mapasdevista_save_pins');

/**
 * Cria novos pins
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
                update_post_meta($r, '_mpv_pin', array(0,0));
                wp_redirect(add_query_arg(array('action' => 'edit', 'pin' => $r)));
            }
        }
    }
}

/**
 * Define se será acionada a função que imprime o formulário
 * para criar um novo pin ou se exibe o formulário para editar
 * um pin existente
 */
function mapasdevista_pins_page() {
    if(isset($_GET['action']) && $_GET['action'] === 'edit') {
        if(isset($_GET['pin']) && is_numeric($_GET['pin'])) {
            $pin_id = intval(sprintf("%d", $_GET['pin']));
            $pin = get_post($pin_id);

            if($pin) {
                mapasdevista_pin_edit($pin);
            } else {
                echo '<div class="error"><p>' . __("Sorry, no such page.") . '</p></div>';
            }
        } else {
            echo '<div class="error"><p>' . __("Invalid post ID.") . '</p></div>';
        }
    } else {
        mapasdevista_pins_list();
    }
}

/**
 * Imprime formulário de edição para pin existente.
 */
function mapasdevista_pin_edit($pin) { ?>
<div class="wrap pinpage">
    <h3><?php _e("Edit pin");?></h3>

    <form id="editpinform" method="post">
        <input type="hidden" name="submit_pin" value="new"/>

        <ul>
            <li>
                <label for="pin_width" class="small"><?php _e("Pin width");?>:</label>
                <input  id="pin_width" name="pin[width]" type="text"/>
            </li>
            <li>
                <label for="pin_height" class="small"><?php _e("Pin height");?>:</label>
                <input  id="pin_height" name="pin[height]" type="text"/>
            </li>
            <li>
                <label for="pin_anchor" class="small"><?php _e("Pin anchor");?>:</label>
                <input id="pin_anchor" name="pin[anchor]" type="text"/>
            </li>
        </ul>
    </form>

    <div id="image-panel-background">
        <div id="image-panel">
            <img id="the-image" src="<?php echo $pin->guid;?>"/>
        </div>

        <div id="image-x-ruler"></div>
        <div id="image-y-ruler"></div>

    </div>

    <p><input type="submit" value="Submit"/></p>

    <script type="text/javascript">
    (function($) {
        var image_width = parseInt($("#the-image").attr('width'));
        var image_height = parseInt($("#the-image").attr('height'));

        var image_anchor = {'x': Math.floor(image_width / 2),
                            'y': Math.floor(image_height / 2 )}

        $("#image-panel").css('width', image_width).css('height', image_height);

        var vertical_offset = $("#image-panel").attr('offsetLeft');
        var horizontal_offset = $("#image-panel").attr('offsetTop');


        $("#image-x-ruler").css('left', vertical_offset + image_anchor.x);
        $("#image-y-ruler").css('top', horizontal_offset + image_anchor.y);

        $("#pin_anchor").keydown(function(e) {
            if(e.keyCode == 37) {
                if(image_anchor.x > 0) image_anchor.x--;
                $("#image-x-ruler").css('left', vertical_offset + image_anchor.x);
            } else if(e.keyCode == 38) {
                if(image_anchor.y > 0) image_anchor.y--;
                $("#image-y-ruler").css('top', horizontal_offset + image_anchor.y);
            } else if(e.keyCode == 39) {
                if(image_anchor.x < image_width) image_anchor.x++;
                $("#image-x-ruler").css('left', vertical_offset + image_anchor.x);
            } else if(e.keyCode == 40) {
                if(image_anchor.y < image_height) image_anchor.y++;
                $("#image-y-ruler").css('top', horizontal_offset + image_anchor.y);
            }
            $(this).val(image_anchor.x + "," + image_anchor.y);
            return false;
        });
        $("#image-panel-background").click(function() { $("#pin_anchor").focus(); });

    })(jQuery);
    </script>
</div>
<?php
}

/**
 * Exibe listagem dos pins existentes e um pequeno
 * formulário para adicionar novos pins
 */
function mapasdevista_pins_list() {
    $args = array(
        'post_type' => 'attachment',
        'meta_key' => '_mpv_pin',
    );

    $pins = get_posts($args);

?>
<div class="wrap pinpage">

<h3><?php _e("Available pins", "mapasdevista");?></h3>
<div id="pinlist">
<?php foreach($pins as $pin): ?>
    <div class="icon">
        <a href="admin.php?page=mapasdevista_pins_page&action=edit&pin=<?php echo $pin->ID;?>"><img src="<?php echo $pin->guid;?>"/></a>
        <div class="icon-info">
            <span class="icon-name"><?php echo $pin->post_name;?></span>
        </div>
    </div>
<?php endforeach;?>
</div>
<div class="clear"></div>


<h3><?php _e("New pin", 'mapasdevista'); ?></h3>
<form id="newpinform" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submit_pin" value="new"/>

    <ul>
        <li>
            <label for="mpv_pinfile">Selecionar arquivo:</label>
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
