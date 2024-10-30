<?php

$settings = array(
    array(
        'name' => __( 'General Configuration', 'clmte' ),
        'type' => 'title',
        'id'   => $prefix . 'general_config_settings',
    ),
    array(
        'id'       => $prefix . 'api_key',
        'name'     => __( 'API Key', 'clmte' ),
        'type'     => 'text',
        'desc_tip' => __( ' Your organisation\'s API Key, which can be found by creating an account and organisation at clmte.com', 'clmte' ),
    ),
    array(
        'id'       => $prefix . 'organisation_id',
        'name'     => __( 'Organisation ID', 'clmte' ),
        'type'     => 'text',
        'desc_tip' => __( ' Your organisation\'s Organisation ID, which can be found by creating an account and organisation at clmte.com', 'clmte' ),
    ),
    array(
        'id'      => $prefix . 'production_mode',
        'name'    => __( 'Production Mode?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Check this box to start using the plugin with real API calls.', 'clmte' ),
        'default' => 'no',
    ),
    array(
        'id'      => $prefix . 'reload_cart_on_update',
        'name'    => __( 'Reload Cart?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Check this box if there is trouble adding the offset to the cart.', 'clmte' ),
        'default' => 'no',
    ),  
    array(
        'id'      => $prefix . 'custom_offset_placement',
        'name'    => __( 'Custom Offset Placement?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Check this box to use custom placement for the carbon offsetting box with the shortcode below.', 'clmte' ),
        'default' => 'no',
    ),
    array(
        'id'      => $prefix . 'custom_receipt_placement',
        'name'    => __( 'Custom Receipt Placement?', 'clmte' ),
        'type'    => 'checkbox',
        'desc'    => __( 'Check this box to use custom placement for the carbon offset receipt with the shortcode below.', 'clmte' ),
        'default' => 'no',
    ),
    array(
        'id'   => '',
        'name' => __( 'General Configuration', 'clmte' ),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'general_config_settings',
    ),
);