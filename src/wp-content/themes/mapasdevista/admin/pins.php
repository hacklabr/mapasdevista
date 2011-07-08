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
                update_post_meta($r, '_mpv_pin', array('x'=>0, 'y'=>0));
                wp_redirect(add_query_arg(array('action' => 'edit', 'pin' => $r)));
            }
        }
    } elseif(isset($_POST['submit_pin']) && $_POST['submit_pin'] === 'edit') {
        if(isset($_GET['pin']) && is_numeric($_GET['pin'])) {
            $pin_id = intval(sprintf("%d", $_GET['pin']));

            if(isset($_POST['pin_anchor']) && preg_match('/^([0-9]+),([0-9]+)$/', $_POST['pin_anchor'], $coords)) {
                $anchor = array('x' => intval($coords[1]), 'y' => intval($coords[2]) );
                update_post_meta($pin_id, '_mpv_pin', $anchor);
            }

            wp_redirect(add_query_arg(array('action' => 'edit', 'pin' => $pin_id)));
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
                $pin->anchor = get_post_meta($pin_id, '_mpv_pin', true);
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
 * Imprime formulário de edição para pin existente.
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

    <script type="text/javascript">
    (function($) {
        var image_width = parseInt($("#the-image").attr('width'));
        var image_height = parseInt($("#the-image").attr('height'));

        var image_panel_el = document.getElementById("image-panel");
        $(image_panel_el).css('width', image_width).css('height', image_height);

        var image_anchor = {
            'x': 0,
            'y': 0,

            'set_x' : function (x) {
                this.x = x < 0 ? 0 : x > image_width ? image_width : x ;
                $("#image-x-ruler").css('left', image_panel_el.offsetLeft + this.x);
            },
            'set_y' : function (y) {
                this.y = y < 0 ? 0 : y > image_height ? image_height : y ;
                $("#image-y-ruler").css('top', image_panel_el.offsetTop + this.y);
            }
        }

        $(window).load(function() {
            var initial = $("#pin_anchor").val().match(/^([0-9]+),([0-9]+)$/);
            if( initial ) {
                image_anchor.set_x(parseInt(initial[1]));
                image_anchor.set_y(parseInt(initial[2]));
            } else {
                image_anchor.set_x(Math.floor(image_width / 2));
                image_anchor.set_y(Math.floor(image_height / 2));
            }
        });

        $(window).resize(function() {
            image_anchor.set_x(image_anchor.x);
            image_anchor.set_y(image_anchor.y);
        });

        /* eventos para o teclado */
        var accel = 0.4;
        var veloc = 1;
        $("#pin_anchor").keydown(function(e) {
            if(e.keyCode == 37) {        // <
                if(image_anchor.x > 0) image_anchor.set_x(Math.floor(image_anchor.x - veloc));
            } else if(e.keyCode == 38) { // ^
                if(image_anchor.y > 0) image_anchor.set_y(Math.floor(image_anchor.y - veloc));
            } else if(e.keyCode == 39) { // >
                if(image_anchor.x < image_width) image_anchor.set_x(Math.floor(image_anchor.x + veloc));
            } else if(e.keyCode == 40) { // v
                if(image_anchor.y < image_height) image_anchor.set_y(Math.floor(image_anchor.y + veloc));
            }
            if( e.keyCode > 36 && e.keyCode < 41 ){
                $(this).val(image_anchor.x + "," + image_anchor.y);
                veloc = veloc + accel;
            }
            return false;
        });
        $("#pin_anchor").keyup(function(e) { veloc = 1; });

        /* eventos para o mouse */
        var mousepressed = false;
        $("#the-image").mousedown(function(e) {
            mousepressed = true;
            image_anchor.set_x(e.layerX);
            image_anchor.set_y(e.layerY);
            $("#pin_anchor").val(image_anchor.x + "," + image_anchor.y);
            return false;
        });
        $("#the-image").mousemove(function(e) {
            if(mousepressed){
                image_anchor.set_x(e.layerX);
                image_anchor.set_y(e.layerY);
                $("#pin_anchor").val(image_anchor.x + "," + image_anchor.y);
            }
            return false;
        });
        $(document).mouseup(function(e) { mousepressed = false;});
        $("#image-panel-background").mouseup(function(e) {$("#pin_anchor").focus();});
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
        <a href="admin.php?page=mapasdevista_pins_page&action=edit&pin=<?php echo $pin->ID;?>"><?php echo  wp_get_attachment_image($pin->ID, array(64,64));?></a>
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
