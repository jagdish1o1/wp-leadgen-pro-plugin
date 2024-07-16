<?php 
// Flush rewrite rules on activation and deactivation
function leadgenpro_plugin_deactivate()
{
    flush_rewrite_rules();
}