<?php

class LGP_SettingsPage
{
    public function register()
    {
        add_action('admin_menu', array($this, 'custom_settings_page'));
        add_action('admin_init', array($this, 'custom_settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'custom_settings_enqueue_scripts'));
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
            'default_service_image' => array('Default Businesses Image', 'image')
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
        wp_enqueue_script('lgp-settings-js', plugin_dir_url(__FILE__) . '/js/lgp-settings.js', array('jquery'), null, true);
        wp_localize_script('lgp-settings-js', 'lgpSettings', array(
            'defaultImagePlaceholder' => plugins_url('img/default-service-image.png', __FILE__),
        ));
    }

    public static function get_custom_option($key, $default = '')
    {
        $options = get_option('listings_settings_options');
        return isset($options[$key]) ? $options[$key] : $default;
    }
}