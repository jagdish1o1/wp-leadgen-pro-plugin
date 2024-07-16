<?php

class LGP_Shortcodes
{
    public function register()
    {
        add_shortcode('all_states', array($this, 'all_states_callback'));
        add_shortcode('all_cities', array($this, 'all_cities_callback'));
        add_shortcode('service_providers_list', array($this, 'service_providers_list_callback'));
        add_shortcode('city_search', array($this, 'city_search_callback'));
        add_action('wp_enqueue_scripts', array($this, 'custom_css_for_listings'));
        
    }

    public function all_states_callback($atts)
    {
        $args = array(
            'taxonomy' => 'state'
        );
        $terms = get_terms($args);

        if (empty($terms)) {
            return "No states";
        }

        $template_file = plugin_dir_path(__FILE__) . 'shortcode/all-states.php';
        if (file_exists($template_file)) {
            ob_start();
            include $template_file;
            $output = ob_get_clean();
            return $output;
        } else {
            return 'all-states.php does not exists';
        }

    }

    public function all_cities_callback($atts)
    {
        $args = array(
            'post_type' => 'listings',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'city_name',
                    'compare' => 'EXISTS'
                )
            ),
            'orderby' => 'meta_value',
            'meta_key' => 'city_name',
            'order' => 'ASC',
            'posts_per_page' => -1
        );
        $cities = get_posts($args);

        if (empty($cities)) {
            return "No cities";
        }

        $template_file = plugin_dir_path(__FILE__) . 'shortcode/all-cities.php';
        if (file_exists($template_file)) {
            ob_start();
            include $template_file;
            $output = ob_get_clean();
            return $output;
        } else {
            return 'all-cities.php does not exists';
        }

    }
    public function service_providers_list_callback($atts)
    {
        $post_id = get_the_ID();
        $service_providers = get_field('service_providers', $post_id);


        if (is_wp_error($service_providers) && !is_array($service_providers)) {
            return "No service providers";
        }

        $template_file = plugin_dir_path(__FILE__) . 'shortcode/service-providers-list.php';
        if (file_exists($template_file)) {
            ob_start();
            include $template_file;
            $output = ob_get_clean();
            return $output;
        } else {
            return 'service-providers-list.php does not exists';
        }


    }

    public function city_search_callback($atts)
    {

        $args = array(
            'post_type' => 'listings',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'city_name',
                    'compare' => 'EXISTS'
                )
            ),
            'orderby' => 'meta_value',
            'meta_key' => 'city_name',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'fields' => 'ids'
        );
        $cities = get_posts($args);

        if (empty($cities)) {
            return "No cities";
        }

        $template_file = plugin_dir_path(__FILE__) . 'shortcode/city-search.php';
        if (file_exists($template_file)) {
            ob_start();
            include $template_file;
            $output = ob_get_clean();
            return $output;
        } else {
            return 'city-search.php does not exists';
        }

    }

    public function custom_css_for_listings()
    {

        if (is_singular('listings')) {

            wp_enqueue_style(
                'lgp-listings-css',
                plugin_dir_url(__FILE__) . 'css/main.css'
            );
        }
    }


}