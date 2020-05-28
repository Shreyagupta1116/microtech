<?php

birch_ns( 'appointer.view.appointments.edit.clientlist.cancel', function( $ns ) {

        birch_defn( $ns, 'init', function() use( $ns ) {
                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );
                add_action( 'appointer_view_register_common_scripts_after',
                    array( $ns, 'register_scripts' ) );
            } );

        birch_defn( $ns, 'wp_admin_init', function() use ( $ns ) {

                add_action( 'wp_ajax_appointer_view_appointments_edit_clientlist_cancel_cancel',
                    array( $ns, 'ajax_cancel' ) );

                add_action( 'appointer_view_enqueue_scripts_post_edit_after',
                    array( $ns, 'enqueue_scripts_post_edit' ) );

                add_filter( 'appointer_view_appointments_edit_clientlist_get_item_actions',
                    array( $ns, 'add_item_action' ), 40, 2 );

            } );

        birch_defn( $ns, 'register_scripts', function() {
                global $appointer;

                $version = $appointer->get_product_version();

                wp_register_script( 'appointer_view_appointments_edit_clientlist_cancel',
                    $appointer->plugin_url() . '/assets/js/view/appointments/edit/clientlist/cancel/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view' ), "$version" );

            } );

        birch_defn( $ns, 'add_item_action', function( $item_actions, $item ) {
                $action_html = '<a href="javascript:void(0);" data-item-id="%s">%s</a>';
                $item_actions['cancel'] = sprintf( $action_html, $item['ID'], __( 'Cancel', 'appointer' ) );
                return $item_actions;
            } );

        birch_defn( $ns, 'enqueue_scripts_post_edit', function( $arg ) {

                if ( $arg['post_type'] != 'birs_appointment' ) {
                    return;
                }

                global $appointer;

                $appointer->view->register_3rd_scripts();
                $appointer->view->register_3rd_styles();
                $appointer->view->enqueue_scripts(
                    array(
                        'appointer_view_appointments_edit_clientlist_cancel'
                    )
                );
            } );

        birch_defn( $ns, 'ajax_cancel', function() use ( $ns ) {
                global $appointer;

                $client_id = $_POST['birs_client_id'];
                $appointment_id = $_POST['birs_appointment_id'];
                $appointment1on1 = $appointer->model->booking->get_appointment1on1(
                    $appointment_id, $client_id );
                $success = array(
                    'code' => 'reload',
                    'message' => ''
                );
                if ( $appointment1on1 ) {
                    $appointer->model->booking->cancel_appointment1on1( $appointment1on1['ID'] );
                    $cancelled = $appointer->model->booking->if_appointment_cancelled( $appointment_id );
                    if ( $cancelled ) {
                        $cal_url = admin_url( 'admin.php?page=appointer_calendar' );
                        $refer_query = parse_url( wp_get_referer(), PHP_URL_QUERY );
                        $hash_string = $appointer->view->get_query_string( $refer_query,
                            array(
                                'calview', 'locationid', 'staffid', 'currentdate'
                            )
                        );
                        if ( $hash_string ) {
                            $cal_url = $cal_url . '#' . $hash_string;
                        }
                        $success = array(
                            'code' => 'redirect_to_calendar',
                            'message' => json_encode( array(
                                    'url' => htmlentities( $cal_url )
                                ) )
                        );
                    }
                }
                $appointer->view->render_ajax_success_message( $success );
            } );

    } );
