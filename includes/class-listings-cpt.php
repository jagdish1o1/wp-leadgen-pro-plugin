<?php

include_once plugin_dir_path(__FILE__) . 'helper-functions.php';
include_once plugin_dir_path(__FILE__) . 'class-settings.php';

class LGP_ListingsCPT
{
    public function register()
    {
        add_action('init', array($this, 'create_cpt'));
        add_action('acf/include_fields', array($this, 'register_fields'));
        
        // URL rewriting Rules
        add_filter('post_type_link', array($this, 'listings_post_type_link'), 10, 2);
        add_filter('rewrite_rules_array', array($this, 'custom_rewrite_rules'));
        add_action('save_post_listings', array($this, 'flush_permalinks_on_save'), 10, 3);
        
    }
    public function create_cpt()
    {
        $labels = array(
            'name' => __('Cities', 'leadgenpro'),
            'singular_name' => __('City', 'leadgenpro'),
            'menu_name' => __('Cities', 'leadgenpro'),
            'name_admin_bar' => __('City', 'leadgenpro'),
            'add_new' => __('Add New', 'leadgenpro'),
            'add_new_item' => __('Add New City', 'leadgenpro'),
            'new_item' => __('New City', 'leadgenpro'),
            'edit_item' => __('Edit City', 'leadgenpro'),
            'view_item' => __('View City', 'leadgenpro'),
            'all_items' => __('All Cities', 'leadgenpro'),
            'search_items' => __('Search Cities', 'leadgenpro'),
            'parent_item_colon' => __('Parent Cities:', 'leadgenpro'),
            'not_found' => __('No Cities found.', 'leadgenpro'),
            'not_found_in_trash' => __('No Cities found in Trash.', 'leadgenpro'),
            'featured_image' => _x('City Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'leadgenpro'),
            'archives' => _x('City archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'leadgenpro'),
            'insert_into_item' => _x('Insert into City', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'leadgenpro'),
            'uploaded_to_this_item' => _x('Uploaded to this City', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'leadgenpro'),
            'filter_items_list' => _x('Filter Cities list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'leadgenpro'),
            'items_list_navigation' => _x('Cities list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'leadgenpro'),
            'items_list' => _x('Cities list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'leadgenpro'),
        );
        $args = array(
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail'),
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'listing', 'with_front' => false),
            'taxonomies' => array('state'),
        );
        register_post_type('listings', $args);

    }

    public function flush_permalinks_on_save($post_id, $post, $update)
    {
        if ('listings' !== $post->post_type || wp_is_post_revision($post_id)) {
            return;
        }
        flush_rewrite_rules();
    }

    public function register_fields()
    {
        $json_file = plugin_dir_path(__FILE__) . 'fields/cpt-listings.json';
        if (file_exists($json_file)) {
            $field_group = json_decode(file_get_contents($json_file), true);
            acf_add_local_field_group($field_group);
        }

    }

    public function listings_post_type_link($post_link, $post)
    {
        if ('listings' === $post->post_type) {
            $custom_uri = get_field('custom_uri', $post->ID);
            if ($custom_uri) {
                $post_link = home_url($custom_uri);
            }
        }
        return $post_link;
    }

    public function custom_rewrite_rules($rules)
    {
        $new_rules = array();
        $listings = get_posts(
            array(
                'post_type' => 'listings',
                'numberposts' => -1,
            )
        );

        foreach ($listings as $listing) {
            $custom_uri = get_post_meta($listing->ID, 'custom_uri', true);
            if ($custom_uri) {
                $new_rules[$custom_uri . '/?$'] = 'index.php?post_type=listings&p=' . $listing->ID;
            }
        }

        $terms = get_terms(
            array(
                'taxonomy' => 'state',
                'hide_empty' => false,
            )
        );

        foreach ($terms as $term) {
            $custom_uri = get_term_meta($term->term_id, 'custom_uri', true);
            if ($custom_uri) {
                $new_rules[$custom_uri . '/?$'] = 'index.php?taxonomy=state&term=' . $term->slug;
            }
        }

        return $new_rules + $rules;
    }

}
