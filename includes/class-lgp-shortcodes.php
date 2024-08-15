<?php

include_once plugin_dir_path(__FILE__) . 'class-settings.php';

class LGP_Shortcodes
{
    public function register()
    {
        add_shortcode('country_name', array($this, 'country_name_callback'));
        add_shortcode('country_code', array($this, 'country_code_callback'));
        add_shortcode('niche_name', array($this, 'niche_name_callback'));
        add_shortcode('city_name', array($this, 'city_name_callback'));
        add_shortcode('state_name', array($this, 'state_name_callback'));
        add_shortcode('state_code', array($this, 'state_code_callback'));
        add_shortcode('all_states', array($this, 'all_states_callback'));
        add_shortcode('all_cities', array($this, 'all_cities_callback'));
        add_shortcode('city_search', array($this, 'city_search_callback'));
        add_shortcode('service_providers_list', array($this, 'service_providers_list_callback'));
        add_shortcode('total_service_providers', array($this, 'total_service_providers_callback'));
        add_action('wp_enqueue_scripts', array($this, 'custom_css_for_listings'));

    }

    public function country_code_callback($atts)
    {

        if (is_singular('listings')) {

            $post_id = get_the_ID();
            $country_code = get_field('country_code', $post_id);

            $default_country_code = LGP_SettingsPage::get_custom_option('country_code');
            return (!empty($country_code)) ? $country_code : $default_country_code;
        }

    }
    public function country_name_callback($atts)
    {

        if (is_singular('listings')) {

            $post_id = get_the_ID();
            $country_name = get_field('country_name', $post_id);

            $default_country_name = LGP_SettingsPage::get_custom_option('country_name');
            return (!empty($country_name)) ? $country_name : $default_country_name;
        }

    }
    public function niche_name_callback($atts)
    {

        if (is_singular('listings')) {

            $post_id = get_the_ID();
            $niche_name = get_field('niche_name', $post_id);

            $default_niche_name = LGP_SettingsPage::get_custom_option('niche_name');
            return (!empty($niche_name)) ? $niche_name : $default_niche_name;
        }

    }
    public function city_name_callback($atts)
    {

        if (is_singular('listings')) {

            $post_id = get_the_ID();
            $city_name = get_field('city_name', $post_id);
            return $city_name;
        } else {
            return 'Not available';
        }

    }
    public function state_code_callback($atts)
    {

        if (is_singular('listings')) {

            $post_id = get_the_ID();
            $state_code = get_field('state_code', $post_id);
            return $state_code;
        } else {
            return 'Not available';
        }

    }
    public function state_name_callback($atts)
    {

        if (is_singular('listings')) {

            $post_id = get_the_ID();
            $terms = get_the_terms($post_id, 'state');

            if ($terms && !is_wp_error($terms)) {
                $first_term = reset($terms);
                return $first_term->name;
            } else {
                return 'Not available';
            }
        } else {
            return 'Not available';
        }

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

    public function total_service_providers_callback()
    {
        if (is_singular('listings')) {
            $post_id = get_the_ID();
            $service_providers = get_field('service_providers', $post_id);
            return strval(count($service_providers));
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