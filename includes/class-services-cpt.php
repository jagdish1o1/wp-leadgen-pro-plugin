<?php

include_once plugin_dir_path(__FILE__) . 'helper-functions.php';
include_once plugin_dir_path(__FILE__) . 'class-settings.php';

class LGP_ServicesCPT
{

    public function register()
    {

        add_action('init', array($this, 'service_cpt'));
        add_action('acf/include_fields', array($this, 'service_cpt_fields'));
        add_filter('the_content', array($this, 'append_acf_fields_to_content'));
        add_filter('the_title', array($this, 'remove_single_services_title'), 10, 2);
        add_action('wp_enqueue_scripts', array($this, 'custom_css_for_services'));

        // Display cities on state archieve before all services
        add_action('pre_get_posts', array($this, 'display_listings_before_posts'));

        // change feature image on state archive        
        add_filter('post_thumbnail_html', array($this, 'replace_featured_image_with_acf'), 10, 5);
        add_filter('post_thumbnail_html', array($this, 'remove_service_feature_image_on_single'), 10, 5);


        // set default service image 
        add_action('save_post_services', array($this, 'set_default_featured_image_from_plugin'));

    }

    function remove_service_feature_image_on_single($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        if (is_singular('services')) {
            return '';
        }
        return $html;
    }

    function set_default_featured_image_from_plugin($post_id)
    {
        if (!has_post_thumbnail($post_id)) {
            $default_image = LGP_SettingsPage::get_custom_option('default_service_image');
            if ($default_image && isset($default_image['ID'])) {
                $default_image_id = $default_image['ID'];
                set_post_thumbnail($post_id, $default_image_id);
            }
        }
    }

    public function replace_featured_image_with_acf($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        if (!is_tax('state')) {
            return $html;
        }
        $acf_name = get_field('name', $post_id);
        $acf_image = get_field('image', $post_id);
        if ($acf_image) {
            $image_url = esc_url($acf_image);
            $html = '<img src="' . $image_url . '" alt="' . $acf_name . '" />';
        }
        return $html;
    }


    public function display_listings_before_posts($query)
    {
        if (!is_admin() && $query->is_main_query() && is_tax('state')) {
            add_action('loop_start', function () {
                if (!is_tax('state')) {
                    return;
                }

                // Get the current state term
                $term = get_queried_object();
                $args = array(
                    'post_type' => 'listings',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'state',
                            'field' => 'term_id',
                            'terms' => $term->term_id,
                        ),
                    ),
                    'posts_per_page' => -1,
                );
                $cities = get_posts($args);
                ob_start();
                ?>
                <style>
                    .cities {
                        display: flex;
                        gap: 10px;
                    }
                </style>
                <div style="grid-column: 1/4;background: white;padding:20px;display: grid;gap: 20px;width:100%;">
                    <h2><?php echo $term->name; ?> Cities</h2>
                    <div style="display: flex;gap: 10px;flex-wrap: wrap;">
                        <?php foreach ($cities as $city): ?>
                            <a href="<?php echo home_url(get_field('custom_uri', $city->ID)); ?>"
                                class="button"><?php echo the_field('city_name', $city->ID); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div style="grid-column: 1/4;display: grid;padding:10px;width:100%;">
                    <h2>Services in <?php echo $term->name; ?></h2>
                </div>
                <?php
            });
        }
    }

    public function custom_css_for_services()
    {
        if (is_singular('services')) {

            wp_enqueue_style(
                'lgp-services-css',
                plugin_dir_url(__FILE__) . 'css/business.css'
            );
        }
    }

    function remove_single_services_title($title)
    {
        if (is_singular('services')):
            return '';
        else:
            return $title;
        endif;
    }

    public function get_businesses($state_id, $exclude_id)
    {
        $args = array(
            'post_type' => 'services',
            'post_status' => 'publish',
            'numberposts' => 4,
            'tax_query' => array(
                array(
                    'taxonomy' => 'state',
                    'field' => 'term_id',
                    'terms' => $state_id
                )
            ),
            'post__not_in' => array($exclude_id)
        );
        $businesses = get_posts($args);
        if (is_array($businesses) && !empty($businesses)) {
            return $businesses;
        }
        return [];

    }

    public function append_acf_fields_to_content($content)
    {
        if (is_singular('services')) {

            $post_id = get_the_ID();
            $terms = get_the_terms($post_id, 'state');
            $state = $terms[0];
            $businesses = $this->get_businesses($state->term_id, $post_id);
            $url = get_field('url');

            ob_start();
            ?>

            <div class="service">
                <div class="service-image">
                    <img src="<?php echo the_field('image'); ?>" alt="<?php echo the_field('name'); ?>" lazy />
                </div>
                <div class="service-content">
                    <h1><?php echo the_field('name'); ?> in
                        <a style="text-decoration:underline;"
                            href="<?php echo esc_url(get_term_link($state->term_id)); ?>"><?php echo esc_html($state->name); ?></a>
                    </h1>
                    <address>Address: <?php echo the_field('address'); ?></address>
                    <rating>
                        Rating:
                        <?php echo the_field('rating'); ?>
                        <?php echo generate_star_rating(get_field('rating')); ?>
                    </rating>
                    <div class="button-group">
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"
                            class="button">Direction</a>
                        <a href="tel:+<?php echo the_field('phone_number', 'option'); ?>" target="_blank" rel="noopener noreferrer"
                            class="button">Call Now</a>
                    </div>
                </div>
            </div>

            <?php if (count($businesses) > 0): ?>
                <div class="related-services">
                    <h2>More Providers in <?php echo esc_html($state->name); ?> (Near You)</h2>
                    <div class="services">
                        <?php foreach ($businesses as $business): ?>
                            <div class="related-service">
                                <div class="related-service-image">
                                    <a href="<?php echo esc_url(get_permalink($business->ID)); ?>">
                                        <img src="<?php echo the_field('image', $business->ID); ?>"
                                            alt="<?php echo get_field('name', $business->ID); ?>" lazy />
                                    </a>
                                </div>
                                <div class="related-service-content">
                                    <a href="<?php echo esc_url(get_permalink($business->ID)); ?>">
                                        <h3><?php echo the_field('name', $business->ID); ?></h3>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            $serivce_content = ob_get_contents();
            ob_end_clean();
            $content .= $serivce_content;

        }
        return $content;
    }


    public function service_cpt()
    {

        $labels = array(
            'name' => __('Businesses', 'leadgenpro'),
            'singular_name' => __('Business', 'leadgenpro'),
            'menu_name' => __('Businesses', 'leadgenpro'),
            'name_admin_bar' => __('Business', 'leadgenpro'),
            'add_new' => __('Add New', 'leadgenpro'),
            'add_new_item' => __('Add New Business', 'leadgenpro'),
            'new_item' => __('New Business', 'leadgenpro'),
            'edit_item' => __('Edit Business', 'leadgenpro'),
            'view_item' => __('View Business', 'leadgenpro'),
            'all_items' => __('All Businesses', 'leadgenpro'),
            'search_items' => __('Search Businesses', 'leadgenpro'),
            'parent_item_colon' => __('Parent Businesses:', 'leadgenpro'),
            'not_found' => __('No Businesses found.', 'leadgenpro'),
            'not_found_in_trash' => __('No Businesses found in Trash.', 'leadgenpro'),
            'featured_image' => _x('Business Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'archives' => _x('Business archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'leadgenpro'),
            'insert_into_item' => _x('Insert into Business', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'leadgenpro'),
            'uploaded_to_this_item' => _x('Uploaded to this Business', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'leadgenpro'),
            'filter_items_list' => _x('Filter Businesses list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'leadgenpro'),
            'items_list_navigation' => _x('Businesses list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'leadgenpro'),
            'items_list' => _x('Businesses list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'leadgenpro'),
        );
        $args = array(
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail'),
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'business'),
            'taxonomies' => array('state'),
        );
        register_post_type('services', $args);
    }

    public function service_cpt_fields()
    {

        $json_file = plugin_dir_path(__FILE__) . 'fields/cpt-services.json';
        if (file_exists($json_file)) {
            $field_group = json_decode(file_get_contents($json_file), true);
            acf_add_local_field_group($field_group);
        }

    }

    function get_acf_fields($object)
    {
        $fields = get_fields($object['id']);
        return !empty($fields) ? $fields : [];
    }
    public function check_existing_service($cid) {
        $args = array(
            'post_type' => 'services',
            'meta_query' => array(
                array(
                    'key' => 'cid',
                    'value' => $cid,
                    'compare' => '='
                )
            ),
            'fields' => 'ids',
            'posts_per_page' => 1
        );
    
        $posts = get_posts($args);
    
        if (!empty($posts)) {
            return $posts[0];
        } else {
            return false;
        }
    }
    

    public function create_single_service($title, $service_provider_data, $state_name, $state_code)
    {

        // check existing service 
        $cid = $service_provider_data['cid'];
        if (isset($cid)) {
            $post_id = $this->check_existing_service($cid);
            if ($post_id) {
                return $post_id;
            }
        }

        // Set or create state taxonomy
        if (!empty($state_name)) {
            $term = get_term_by('name', $state_name, 'state');
            if (!$term) {
                $term = wp_insert_term($state_name, 'state');
                if (is_wp_error($term)) {
                    return $term;
                }
                $term_id = $term['term_id'];
                update_term_meta($term_id, 'custom_uri', slugify($state_code));
            } else {
                $term_id = $term->term_id;
            }
        }

        if (!isset($term_id)) {
            return null;
        }

        $post_id = wp_insert_post(
            array(
                'post_title' => ucwords($title),
                'post_type' => 'services',
                'post_status' => 'draft'
            )
        );

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        foreach ($service_provider_data as $key => $value) {
            if ($key === 'title') {
                update_field('name', $value, $post_id);
            } else if ($key === 'rating') {
                update_field('rating', intval($value['rating']), $post_id);
                update_field('rating_count', intval($value['count']), $post_id);
            } else if ($key === 'coordinates') {
                update_field('latitude', $value['coordinates']['latitude'], $post_id);
                update_field('longitude', $value['coordinates']['longitude'], $post_id);
            } else {
                update_field($key, $value, $post_id);
            }
        }

        // Assign state taxonomy to the post
        wp_set_object_terms($post_id, $term_id, 'state');

        // Setting default feature image to all the services from settings page
        if (!has_post_thumbnail($post_id)) {
            $default_image = get_field('default_service_image', 'option');
            if (isset($default_image) && !empty($default_image)) {
                $default_image_id = $default_image['ID'];
                set_post_thumbnail($post_id, $default_image_id);
            }
        }

        wp_update_post(
            array(
                'ID' => $post_id,
                'post_status' => 'publish'
            )
        );

        flush_rewrite_rules();

        return $post_id;
    }


}

