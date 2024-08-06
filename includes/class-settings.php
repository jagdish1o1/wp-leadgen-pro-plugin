<?php

class LGP_SettingsPage
{
    public function register()
    {
        add_action('admin_menu', array($this, 'custom_settings_page'));
        add_action('admin_init', array($this, 'custom_settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'custom_settings_enqueue_scripts'));
        add_action('wp_ajax_update_custom_uri', array($this, 'update_custom_uri'));
    }

    public function custom_settings_page()
    {
        add_submenu_page(
            'edit.php?post_type=listings',
            'Listings Settings',
            'Settings',
            'manage_options',
            'listings-settings',
            array($this, 'listings_settings_page_html')
        );
    }

    public function listings_settings_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?= esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('listings_settings');
                do_settings_sections('listings-settings');
                submit_button('Save Settings');
                ?>
            </form>
            <div class="">
                <p><strong>Update Custom URI</strong></p>
                <p>Click the below button to fix redirect issue in google search console.</p>
                <p>This only required if you've created the site using WP Leadgen Pro v1.0.6 or below.</p>
                <button id="update-custom-uri" class="button button-primary">Update Custom URI</button>
                <p style="color:red;">Note: Please don't do multiple times, it only require once.</p>
            </div>
        </div>
        <?php
    }

    public function custom_settings_init()
    {
        register_setting('listings_settings', 'listings_settings_options');

        add_settings_section(
            'listings_settings_section',
            'Listings Settings',
            array($this, 'listings_settings_section_cb'),
            'listings-settings'
        );

        $this->add_settings_fields();
    }

    private function add_settings_fields()
    {
        $fields = array(
            'phone_number' => array('Phone Number', 'number', 'Only add number without any prefix or space like this: 18583410290'),
            'custom_uri_structure' => array('Custom URI Structure', 'select', array('state_postname' => 'State/City Name', 'default' => 'Default')),
            'default_service_image' => array('Default Businesses Image', 'image'),
            'services_provider_list_cta' => array('Enable/Disable Service Provider List CTA', 'true_false', array('show' => 'Show CTA', 'hide' => 'Hide CTA')),
        );

        foreach ($fields as $id => $field) {
            $args = array('label_for' => $id, 'type' => $field[1]);
            if (isset($field[2])) {
                if (is_array($field[2])) {
                    $args['options'] = $field[2];
                } else {
                    $args['description'] = $field[2];
                }
            }

            add_settings_field(
                $id,
                $field[0],
                array($this, 'listings_settings_field_cb'),
                'listings-settings',
                'listings_settings_section',
                $args
            );
        }
    }

    public function listings_settings_section_cb($args)
    {
        return $args;
    }

    public function listings_settings_field_cb($args)
    {

        $options = get_option('listings_settings_options');
        $id = $args['label_for'];
        $type = $args['type'];
        $value = isset($options[$id]) ? $options[$id] : '';

        switch ($type) {
            case 'number':
                echo "<input type='number' id='$id' name='listings_settings_options[$id]' value='$value' min='10' />";
                if (isset($args['description'])) {
                    echo "<p class='description'>{$args['description']}</p>";
                }
                break;
            case 'select':
                echo "<select id='$id' name='listings_settings_options[$id]'>";
                foreach ($args['options'] as $key => $label) {
                    $selected = ($value === $key) ? 'selected' : '';
                    echo "<option value='$key' $selected>$label</option>";
                }
                echo "</select>";
                break;
            case 'true_false':
                echo "<select id='$id' name='listings_settings_options[$id]'>";
                foreach ($args['options'] as $key => $label) {
                    $selected = ($value === $key) ? 'selected' : '';
                    echo "<option value='$key' $selected>$label</option>";
                }
                echo "</select>";
                break;
            case 'image':
                $image_url = wp_get_attachment_url($value);
                echo "<div class='image-preview-wrapper'>";
                if ($image_url) {
                    echo "<img id='{$id}_preview' src='$image_url' style='max-width:100px;max-height:100px;'>";
                } else {
                    echo "<img id='{$id}_preview' src='' style='max-width:100px;max-height:100px;display:none;'>";
                }
                echo "</div>";
                echo "<input id='$id' type='hidden' name='listings_settings_options[$id]' value='$value' />";
                echo "<input id='{$id}_button' type='button' class='button' value='Upload image' />";
                echo "<input id='{$id}_remove' type='button' class='button' value='Remove image' />";
                break;
        }
    }

    public function custom_settings_enqueue_scripts($hook)
    {
        if ('listings_page_listings-settings' !== $hook) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script('lgp-settings-js', plugin_dir_url(__FILE__) . 'js/lgp-settings.js', array('jquery'), null, true);
        wp_localize_script(
            'lgp-settings-js',
            'lgpSettings',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'defaultImagePlaceholder' => plugins_url('img/default-service-image.png', __FILE__),
            )
        );
    }

    public static function get_custom_option($key, $default = '')
    {
        $options = get_option('listings_settings_options');
        return isset($options[$key]) ? $options[$key] : $default;
    }


    public function update_custom_uri()
    {
        $args = array(
            'post_type' => 'listings',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        $listings = get_posts($args);

        foreach ($listings as $listing) {
            $custom_uri = get_post_meta($listing->ID, 'custom_uri', true);
            if ($custom_uri && substr($custom_uri, -1) !== '/') {
                update_post_meta($listing->ID, 'custom_uri', $custom_uri . '/');
            }
        }
        // Remove permalink cache
        flush_rewrite_rules();
        wp_send_json_success(array('message' => 'Custom URI updated for all published listings.'));
    }
}