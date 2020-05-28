<?php

birch_ns( 'appointer.view.appointments', function( $ns ) {

        birch_defn( $ns, 'init', function() use ( $ns ) {
                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );
                add_action( 'init', array( $ns, 'wp_init' ) );
            } );

        birch_defn( $ns, 'wp_init', function() use ( $ns ) {
                $ns->register_post_type();
            } );

        birch_defn( $ns, 'wp_admin_init', function() use ( $ns ) {
                add_filter( 'post_updated_messages',
                    array( $ns, 'get_updated_messages' ) );
            } );

        birch_defn( $ns, 'register_post_type', function() use ( $ns ) {
                register_post_type( 'birs_appointment', array(
                        'labels' => array(
                            'name' => __( 'Appointments', 'appointer' ),
                            'singular_name' => __( 'Appointment', 'appointer' ),
                            'add_new' => __( 'New Appointment', 'appointer' ),
                            'add_new_item' => __( 'New Appointment', 'appointer' ),
                            'edit' => __( 'Edit', 'appointer' ),
                            'edit_item' => __( 'Edit Appointment', 'appointer' ),
                            'new_item' => __( 'New Appointment', 'appointer' ),
                            'view' => __( 'View Appointment', 'appointer' ),
                            'view_item' => __( 'View Appointment', 'appointer' ),
                            'search_items' => __( 'Search Appointments', 'appointer' ),
                            'not_found' => __( 'No Appointments found', 'appointer' ),
                            'not_found_in_trash' => __( 'No Appointments found in trash', 'appointer' ),
                            'parent' => __( 'Parent Appointment', 'appointer' )
                        ),
                        'description' => __( 'This is where appointments are stored.', 'appointer' ),
                        'public' => false,
                        'show_ui' => true,
                        'menu_icon' => 'dashicons-calendar',
                        'capability_type' => 'birs_appointment',
                        'map_meta_cap' => true,
                        'publicly_queryable' => false,
                        'exclude_from_search' => true,
                        'show_in_menu' => true,
                        'hierarchical' => false,
                        'show_in_nav_menus' => false,
                        'rewrite' => false,
                        'query_var' => true,
                        'supports' => array( '' ),
                        'has_archive' => false
                    )
                );
                register_post_type( 'birs_appointment1on1', array(
                        'labels' => array(
                            'name' => __( 'One-on-one appointments', 'appointer' ),
                            'singular_name' => __( 'One-on-one appointment', 'appointer' ),
                            'add_new' => __( 'New One-on-one Appointment', 'appointer' ),
                            'add_new_item' => __( 'New One-on-one Appointment', 'appointer' ),
                            'edit' => __( 'Edit', 'appointer' ),
                            'edit_item' => __( 'Edit One-on-one Appointment', 'appointer' ),
                            'new_item' => __( 'New One-on-one Appointment', 'appointer' ),
                            'view' => __( 'View One-on-one Appointment', 'appointer' ),
                            'view_item' => __( 'View One-on-one Appointment', 'appointer' ),
                            'search_items' => __( 'Search One-on-one Appointments', 'appointer' ),
                            'not_found' => __( 'No One-on-one Appointments found', 'appointer' ),
                            'not_found_in_trash' => __( 'No One-on-one Appointments found in trash', 'appointer' ),
                            'parent' => __( 'Parent One-on-one Appointment', 'appointer' )
                        ),
                        'description' => __( 'This is where one-on-one appointments are stored.', 'appointer' ),
                        'public' => false,
                        'show_ui' => false,
                        'menu_icon' => 'dashicons-calendar',
                        'capability_type' => 'birs_appointment',
                        'map_meta_cap' => true,
                        'publicly_queryable' => false,
                        'exclude_from_search' => true,
                        'show_in_menu' => false,
                        'hierarchical' => false,
                        'show_in_nav_menus' => false,
                        'rewrite' => false,
                        'query_var' => true,
                        'supports' => array( '' ),
                        'has_archive' => false
                    )
                );
            } );

        birch_defn( $ns, 'get_updated_messages', function( $messages ) {
                global $post, $post_ID;

                $messages['birs_appointment'] = array(
                    0 => '', // Unused. Messages start at index 1.
                    1 => __( 'Appointment updated.', 'appointer' ),
                    2 => __( 'Custom field updated.', 'appointer' ),
                    3 => __( 'Custom field deleted.', 'appointer' ),
                    4 => __( 'Appointment updated.', 'appointer' ),
                    5 => isset( $_GET['revision'] ) ? sprintf( __( 'Appointment restored to revision from %s', 'appointer' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                    6 => __( 'Appointment updated.', 'appointer' ),
                    7 => __( 'Appointment saved.', 'appointer' ),
                    8 => __( 'Appointment submitted.', 'appointer' ),
                    9 => sprintf( __( 'Appointment scheduled for: <strong>%1$s</strong>.', 'appointer' ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ) ),
                    10 => __( 'Appointment draft updated.', 'appointer' )
                );

                return $messages;
            } );

    } );
