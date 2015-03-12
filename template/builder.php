<?php
/**
 * Template Name: Page Builder
 *
 * This template is used for displaying the Layers page builder
 *
 * @package Layers
 * @since Layers 1.0.0
 */

get_header();

do_action('before_layers_builder_widgets');

// Dynamic Sidebar for this page
dynamic_sidebar( 'obox-layers-builder-' . get_the_ID() );

do_action('after_layers_builder_widgets');

get_footer();