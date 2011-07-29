<?php 

if ($_POST['install'] == 1) {

    if (current_user_can('manage_options')) {
        
        $args = $_POST; // in the future we can choose some options to the map
        if ($feedback = mapasdevista_create_homepage_map($args) === true) {
            wp_redirect(site_url());
            exit;
        } else {
            _e('Error creating the map: ', 'mapasdevista');
            echo $feedback;
        }
        
    } else {
        _e('You dont have permission to do that', 'mapasdevista');
    }

}

_e('Hi there! In order to start using your map: 1. set up a page as your home page and 2. create a map in this page. Or click here and Ill do it for you', 'mapasdevista');

?>

<form method="post">
    <input type="hidden" name="install" value="1">
    <input type="submit" value="<?php _e('Create my first map for me', 'mapasdevista'); ?>" />
</form>

