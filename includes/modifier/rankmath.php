<?php 

if (!defined('WPINC')) {
    die;
}

class LGP_RankMath_Modifier {
    
    public function register() {
        if ($this->is_rankmath_active()) {
            add_filter('rank_math/frontend/canonical', array($this, 'lgp_rankmath_canonical'));
        }
    }

    private function is_rankmath_active() {
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active('seo-by-rank-math/rank-math.php');
    }

    public function lgp_rankmath_canonical($canonical) {
        return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}