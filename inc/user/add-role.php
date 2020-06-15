<?php
defined('ABSPATH') or die('No script kiddies please!');


global $wp_roles;  // Delcaring roles and collecting the author role capability here.
if (!isset($wp_roles))
    $wp_roles = new WP_Roles();

$subscriber = $wp_roles->get_role('subscriber');
$wp_roles->add_role('terra_user', 'Terraclassifieds user', $subscriber->capabilities);
