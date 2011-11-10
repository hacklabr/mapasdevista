<?php

include( mapasdevista_get_template('template/_init-vars', null, false) );

include( mapasdevista_get_template('template/_load-js', null, false) );

include( mapasdevista_get_template('template/_filter-menus', null, false) );

    include( mapasdevista_get_template('template/_header', null, false) );

        include( mapasdevista_get_template('mapasdevista-loop', 'filter', false) );

        include( mapasdevista_get_template('mapasdevista-loop', 'bubble', false) );

        include( mapasdevista_get_template('template/_filters', null, false) );

    include( mapasdevista_get_template('template/_footer', null, false) );

?>
