        <div id="search" class="clearfix">
            <?php mapasdevista_image("icn-search.png", array("id" => "search-icon")); ?>
            <form id="searchform" method="GET">
                <?php $searchValue = isset($_GET['mapasdevista_search']) && $_GET['mapasdevista_search'] != '' ? $_GET['mapasdevista_search'] : __('Search...', 'mapasdevista'); ?>
                <input id="searchfield" name="mapasdevista_search" type="text" value="<?php echo $searchValue; ?>" title="<?php _e('Search...', 'mapasdevista'); ?>" />
                <input type="image" src="<?php echo mapasdevista_get_image("submit.png"); ?>"/>
            </form>
            <div id="toggle-filters">
                <?php mapasdevista_image("show-filters.png"); ?> <?php _e('Show Filters', 'mapasdevista'); ?>
            </div>
        </div>

        <div id="filters" class="clearfix">
            <div class="box" class="clearfix">
                <?php if(!isset($mapinfo['logical_operator']) || !trim($mapinfo['logical_operator'])):?>
                    <div id='logical_oparator'>
                        <label><input name="logical_oparator" type='radio' value="AND" checked="checked" ><?php _e('Displays posts that match all the filters', 'mapasdevista'); ?></label>
                        <label><input name="logical_oparator" type='radio' value="OR" ><?php _e('Displays posts that match at least one of the filters', 'mapasdevista'); ?></label>
                    </div>
                <?php elseif($mapinfo['logical_operator'] == "AND"): ?>
                    <div id='logical_oparator'>
                        <input name="logical_oparator" type='hidden' value="AND" />
                    </div>
                <?php elseif($mapinfo['logical_operator'] == "OR"): ?>
                    <div id='logical_oparator'>
                        <input name="logical_oparator" type='hidden' value="OR" />
                    </div>
                <?php endif; ?>
                <?php if (is_array($mapinfo['filters'])): ?>
                    
                    <?php $counter = 1; // to decide when print div.clear ?>
                    
                    <?php foreach ($mapinfo['filters'] as $filter): ?>

                        <?php if ($filter == 'new') : ?>
                            
                            <p>
                                <input type="checkbox" name="filter_by_new" id="filter_by_new" value="1" />
                                <label for="filter_by_new"><?php _e('Show most recent posts', 'mapasdevista'); ?></label>
                            </p>

                        <?php elseif ($filter == 'post_types') : ?>

                            <ul class="filter-group" id="filter_post_types">
                                <li><h3><?php _e('Content Types', 'mapasdevista'); ?></h3></li>

                                <?php foreach ($mapinfo['post_types'] as $type) : ?>

                                    <li>
                                        <input type="checkbox" class="post_type-filter-checkbox" name="filter_by_post_type[]" value="<?php echo $type; ?>" id="filter_post_type_<?php echo $type; ?>"> 
                                        <label for="filter_post_type_<?php echo $type; ?>">
                                            <?php echo $wp_post_types[$type]->label; ?>
                                        </label>
                                    </li>

                                <?php endforeach; ?>

                            </ul>
                            
                        <?php elseif ($filter == 'author') : ?>

                            <ul class="filter-group" id="filter_author">
                                <li><h3><?php _e('Authors', 'mapasdevista'); ?></h3></li>
                                
                                <?php $users = get_users(); ?>
                                
                                <?php foreach ($users as $user) : ?>

                                    <li>
                                        <input type="checkbox" class="author-filter-checkbox" name="filter_by_author[]" value="<?php echo $user->ID; ?>" id="filter_author_<?php echo $user->ID; ?>"> 
                                        <label for="filter_author_<?php echo $user->ID; ?>">
                                            <?php echo $user->display_name; ?>
                                        </label>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        <?php endif; ?>

                        <?php $counter++; if( $counter % 5 == 0 ): ?>
                            <div class="clear"></div>
                        <?php endif;?>
                    <?php endforeach; ?>

                <?php endif; ?>

                <?php if (is_array($mapinfo['taxonomies'])): ?>

                    <?php foreach ($mapinfo['taxonomies'] as $filter): ?>

                        <ul class="filter-group filter-taxonomy" id="filter_taxonomy_<?php echo $filter; ?>">
                            <?php mapasdevista_taxonomy_checklist($filter); ?>
                        </ul>

                        <?php if( ($counter++) % 5 == 0 ): ?>
                            <div class="clear"></div>
                        <?php endif;?>

                    <?php endforeach; ?>

                <?php endif; ?>
            
                
                
                <?php

                    function mapasdevista_taxonomy_checklist($taxonomy, $parent = 0) {
                        global $posts, $wpdb;
                        $cur_page = get_queried_object();
                        $terms = array();
                        $terms_ids = array();
                        
                        $page_id = $cur_page->ID;
                        $posts_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key ='_mpv_inmap' AND meta_value = '$page_id'");
                       
                        foreach($posts_ids as $post_id){
                            $_terms = get_the_terms($post_id, $taxonomy);
                            if(is_array($_terms))
                                foreach($_terms as $_t){
                                    if(!in_array($_t->term_id,$terms_ids) && $_t->parent == $parent){
                                        $terms_ids[] = $_t->term_id;
                                        $terms[] = $_t;
                                    }
                                }
                        }
                        
                        if (!is_array($terms) || ( is_array($terms) && sizeof($terms) < 1 ) )
                            return;

                ?>
                        <?php if($parent == 0): ?>
                            <?php $tax = get_taxonomy($taxonomy); ?>
                            <li><h3><?php echo $tax->label; ?></h3></li>
                        <?php endif; ?>
                        <?php if ($parent > 0): ?>
                            <ul class='children'>
                        <?php endif; ?>

                        <?php foreach ($terms as $term): ?>
                            <li>
                                <input type="checkbox" class="taxonomy-filter-checkbox" value="<?php echo $term->slug; ?>" name="filter_by_<?php echo $taxonomy; ?>[]" id="filter_by_<?php echo $taxonomy; ?>_<?php echo $term->slug; ?>" />
                                <label for="filter_by_<?php echo $taxonomy; ?>_<?php echo $term->slug; ?>">
                                    <?php echo $term->name; ?>
                                </label>
                            </li>

                            <?php mapasdevista_taxonomy_checklist($taxonomy, $term->term_id); ?>

                        <?php endforeach; ?>

                        <?php if ($parent > 0): ?>
                            </ul>
                        <?php endif; ?>

                <?php
                    }
                ?>
                
                
                
                
            </div>
        </div>
