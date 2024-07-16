<?php 
// Flush rewrite rules on activation and deactivation
function leadgenpro_plugin_activate()
{
    flush_rewrite_rules();

}