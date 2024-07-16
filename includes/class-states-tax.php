<?php

include_once plugin_dir_path(__FILE__) . 'helper-functions.php';

class LGP_StateTax
{

    public function register()
    {

        add_action('init', array($this, 'register_state_taxonomy'));
        add_action('acf/include_fields', array($this, 'register_fields'));

        add_action('wp_enqueue_scripts', array($this, 'custom_css_for_services'));
        add_filter('template_include', array($this, 'load_custom_state_template'));

        add_action('created_state', array($this, 'auto_generate_state_custom_uri'), 10, 2);
        
        // Filter posts for archieve page on state
        add_action('pre_get_posts', array($this, 'filter_state_archive_query'));

    }


    public function register_state_taxonomy()
    {
        $labels = array(
            'name' => _x('States', 'taxonomy general name', 'leadgenpro'),
            'singular_name' => _x('State', 'taxonomy singular name', 'leadgenpro'),
            'search_items' => __('Search States', 'leadgenpro'),
            'all_items' => __('All States', 'leadgenpro'),
            'parent_item' => __('Parent State', 'leadgenpro'),
            'parent_item_colon' => __('Parent State:', 'leadgenpro'),
            'edit_item' => __('Edit State', 'leadgenpro'),
            'update_item' => __('Update State', 'leadgenpro'),
            'add_new_item' => __('Add New State', 'leadgenpro'),
            'new_item_name' => __('New State Name', 'leadgenpro'),
            'menu_name' => __('States', 'leadgenpro'),
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'state', 'with_front' => false),
        );
        register_taxonomy('state', array('listings'), $args);
    }

    public function register_fields()
    {
        $json_file = plugin_dir_path(__FILE__) . 'fields/tax-state.json';
        if (file_exists($json_file)) {
            $field_group = json_decode(file_get_contents($json_file), true);
            acf_add_local_field_group($field_group);
        }


    }

    public function custom_css_for_services()
    {
        if (is_tax('state') && is_archive()) {

            wp_enqueue_style(
                'lgp-state-archive-css',
                plugin_dir_url(__FILE__) . 'css/state-archive.css'
            );
        }
    }

    function load_custom_state_template($template)
    {
        if (is_tax('state')) {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/taxonomy-state.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }



    public function auto_generate_state_custom_uri($term_id, $tt_id)
    {
        $term = get_term($term_id, 'state');
        if (!is_wp_error($term)) {
            $custom_uri = $term->slug;
            update_term_meta($term_id, 'custom_uri', $custom_uri);
        }
    }

    function filter_state_archive_query($query)
    {
        if (!is_admin() && $query->is_main_query() && is_tax('state')) {
            $query->set('post_type', 'services');
        }
    }

}