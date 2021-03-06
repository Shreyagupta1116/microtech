<?php

birch_ns( 'appointer.view.appointments.new', function( $ns ) {

        global $appointer;

        birch_defn( $ns, 'init', function() use ( $ns, $appointer ) {
            
                add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

                add_action( 'init', array( $ns, 'wp_init' ) );

                add_action( 'appointer_view_register_common_scripts_after',
                    array( $ns, 'register_scripts' ) );

                birch_defmethod( $appointer->view, 'load_post_new',
                    'birs_appointment', $ns->load_post_new );

                birch_defmethod( $appointer->view, 'enqueue_scripts_post_new',
                    'birs_appointment', $ns->enqueue_scripts_post_new );

            } );

        birch_defn( $ns, 'wp_init', function() use ( $ns ) {
                global $appointer;

                $appointer->view->register_script_data_fn(
                    'appointer_view_appointments_new', 'appointer_view_appointments_new',
                    array( $ns, 'get_script_data_fn_view_appointments_new' ) );
            } );

        birch_defn( $ns, 'wp_admin_init', function() use ( $ns ) {
                add_action( 'wp_ajax_appointer_view_appointments_new_schedule',
                    array( $ns, 'ajax_schedule' ) );
            } );

        birch_defn( $ns, 'get_script_data_fn_view_appointments_new', function() use( $ns ) {
                return array(
                    'services_staff_map' => $ns->get_services_staff_map(),
                    'services_prices_map' => $ns->get_services_prices_map(),
                    'services_duration_map' => $ns->get_services_duration_map(),
                    'locations_map' => $ns->get_locations_map(),
                    'locations_staff_map' => $ns->get_locations_staff_map(),
                    'locations_services_map' => $ns->get_locations_services_map(),
                    'locations_order' => $ns->get_locations_listing_order(),
                    'staff_order' => $ns->get_staff_listing_order(),
                    'services_order' => $ns->get_services_listing_order(),
                );
            } );

        birch_defn( $ns, 'get_locations_map', function() {
                global $appointer;

                return $appointer->model->get_locations_map();
            } );

        birch_defn( $ns, 'get_locations_staff_map', function() {
                global $appointer;

                return $appointer->model->get_locations_staff_map();
            } );

        birch_defn( $ns, 'get_locations_services_map', function() {
                global $appointer;

                return $appointer->model->get_locations_services_map();
            } );

        birch_defn( $ns, 'get_services_staff_map', function() {
                global $appointer;

                return $appointer->model->get_services_staff_map();
            } );

        birch_defn( $ns, 'get_locations_listing_order', function() {
                global $appointer;

                return $appointer->model->get_locations_listing_order();
            } );

        birch_defn( $ns, 'get_staff_listing_order', function() {
                global $appointer;

                return $appointer->model->get_staff_listing_order();
            } );

        birch_defn( $ns, 'get_services_listing_order', function() {
                global $appointer;

                return $appointer->model->get_services_listing_order();
            } );

        birch_defn( $ns, 'get_services_prices_map', function() {
                global $appointer;

                return $appointer->model->get_services_prices_map();
            } );

        birch_defn( $ns, 'get_services_duration_map', function() {
                global $appointer;

                return $appointer->model->get_services_duration_map();
            } );

        birch_defn( $ns, 'load_post_new', function( $arg ) use ( $ns ) {
                add_action( 'add_meta_boxes',
                    array( $ns, 'add_meta_boxes' ) );
            } );

        birch_defn( $ns, 'register_scripts', function() {
                global $appointer;

                $version = $appointer->get_product_version();

                wp_register_script( 'appointer_view_appointments_new',
                    $appointer->plugin_url() . '/assets/js/view/appointments/new/base.js',
                    array( 'appointer_view_admincommon', 'appointer_view', 'jquery-ui-datepicker' ), "$version" );
            } );

        birch_defn( $ns, 'enqueue_scripts_post_new', function( $arg ) {
                global $appointer;

                $appointer->view->register_3rd_scripts();
                $appointer->view->register_3rd_styles();
                $appointer->view->enqueue_scripts(
                    array(
                        'appointer_view_appointments_new'
                    )
                );
                $appointer->view->enqueue_styles( array( 'appointer_appointments_new' ) );
            } );

        birch_defn( $ns, 'add_meta_boxes', function() use ( $ns ) {
                add_meta_box( 'meta_box_birs_appointment_new_booking', __( 'Appointment Info', 'appointer' ),
                    array( $ns, 'render_booking_info' ), 'birs_appointment', 'normal', 'high' );
                add_meta_box( 'meta_box_birs_appointment_new_actions', __( 'Actions', 'appointer' ),
                    array( $ns, 'render_actions' ), 'birs_appointment', 'side', 'high' );
            } );

        birch_defn( $ns, 'get_time_options', function( $time ) {
                global $birchpress;

                $options = $birchpress->util->get_time_options( 5 );
                ob_start();
                $birchpress->util->render_html_options( $options, $time );
                return ob_get_clean();
            } );

        birch_defn( $ns, 'get_appointment_info_html', function() use ( $ns ) {
                global $birchpress;

                if ( isset( $_GET['apttimestamp'] ) ) {
                    $timestamp = $birchpress->util->get_wp_datetime( $_GET['apttimestamp'] );
                    $date = $timestamp->format( 'm/d/Y' );
                    $time = $timestamp->format( 'H' ) * 60 + $timestamp->format( 'i' );
                } else {
                    $date = '';
                    $time = 540;
                }
                $location_id = 0;
                $service_id = 0;
                $staff_id = 0;
                if ( isset( $_GET['locationid'] ) && $_GET['locationid'] != -1 ) {
                    $location_id = $_GET['locationid'];
                }
                if ( isset( $_GET['staffid'] ) && $_GET['staffid'] != -1 ) {
                    $staff_id = $_GET['staffid'];
                }
                ob_start();
?>
                <ul>
                    <li class="birs_form_field birs_appointment_location">
                        <label>
                            <?php _e( 'Location', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <select id="birs_appointment_location" name="birs_appointment_location"
                                data-value="<?php echo $location_id; ?>">
                            </select>
                        </div>
                    </li>
                    <li class="birs_form_field birs_appointment_service">
                        <label>
                            <?php _e( 'Service', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <select id="birs_appointment_service" name="birs_appointment_service"
                                data-value="<?php echo $service_id; ?>">
                            </select>
                        </div>
                    </li>
                    <li class="birs_form_field birs_appointment_staff">
                        <label>
                            <?php _e( 'Provider', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <select id="birs_appointment_staff" name="birs_appointment_staff"
                                data-value="<?php echo $staff_id; ?>">
                            </select>
                        </div>
                        <div class="birs_error" id="birs_appointment_service_error"></div>
                    </li>
                    <li class="birs_form_field birs_appointment_date">
                        <label>
                            <?php _e( 'Date', 'appointer' ); ?>
                        </label>
                        <input id="birs_appointment_date" name="birs_appointment_date" type="hidden" value="<?php echo $date; ?>">
                        <div  class="birs_field_content">
                            <div id="birs_appointment_datepicker">
                            </div>
                        </div>
                        <div class="birs_error" id="birs_appointment_date_error"></div>
                    </li>
                    <li class="birs_form_field birs_appointment_time">
                        <label>
                            <?php _e( 'Time', 'appointer' ); ?>
                        </label>
                        <div class="birs_field_content">
                            <select id="birs_appointment_time" name="birs_appointment_time" size='1'>
                                <?php echo $ns->get_time_options( $time ); ?>
                            </select>
                        </div>
                        <div class="birs_error" id="birs_appointment_time_error"></div>
                    </li>
                </ul>
<?php
                return ob_get_clean();
            } );

        birch_defn( $ns, 'get_client_info_html', function() {
                global $appointer;

                return $appointer->view->appointments->edit->clientlist->edit->get_client_info_html( 0 );
            } );

        birch_defn( $ns, 'get_appointment1on1_info_html', function() {
                global $appointer;

                return $appointer->view->appointments->edit->clientlist->edit->get_appointment1on1_info_html( 0, 0 );
            } );

        birch_defn( $ns, 'render_client_info_header', function() {
?>
                <h3 class="birs_section"><?php _e( 'Client Info', 'appointer' ); ?></h3>
<?php
            } );

        birch_defn( $ns, 'render_booking_info', function( $post ) use ( $ns ) {
                echo $ns->get_appointment_info_html();
?>
                <input type="hidden" id="birs_appointment_duration" name="birs_appointment_duration" />
<?php
                $ns->render_client_info_header();
?>
                <div id="birs_client_info_container">
<?php
                echo $ns->get_client_info_html();
?>
                </div>
                <h3 class="birs_section"><?php _e( 'Additional Info', 'appointer' ); ?></h3>
<?php
                echo $ns->get_appointment1on1_info_html();
?>
                <ul>
                    <li class="birs_form_field birs_please_wait" style="display:none;">
                        <label>
                            &nbsp;
                        </label>
                        <div class="birs_field_content">
                            <div><?php _e( 'Please wait...', 'appointer' ); ?></div>
                        </div>
                    </li>
                    <li class="birs_form_field">
                        <label>
                            &nbsp;
                        </label>
                        <div class="birs_field_content">
                            <input type="button" id="birs_appointment_actions_schedule" class="button-primary" value="<?php _e( 'Schedule', 'appointer' ); ?>" />
                        </div>
                    </li>
                </ul>
<?php
            } );

        birch_defn( $ns, 'render_actions', function() {
                global $appointer;

                $back_url = $appointer->view->appointments->edit->get_back_to_calendar_url();
?>
                <div class="submitbox">
                    <div style="float:left;">
                        <a href="<?php echo $back_url; ?>">
                            <?php _e( 'Back to Calendar', 'appointer' ); ?>
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
<?php
            } );

        birch_defn( $ns, 'validate_appointment_info', function() {
                global $birchpress;

                $errors = array();
                if ( !isset( $_POST['birs_appointment_staff'] ) || !isset( $_POST['birs_appointment_service'] ) ) {
                    $errors['birs_appointment_service'] = __( 'Please select a service and a provider', 'appointer' );
                }
                if ( !isset( $_POST['birs_appointment_date'] ) || !$_POST['birs_appointment_date'] ) {
                    $errors['birs_appointment_date'] = __( 'Date is required', 'appointer' );
                }
                if ( !isset( $_POST['birs_appointment_time'] ) || !$_POST['birs_appointment_time'] ) {
                    $errors['birs_appointment_time'] = __( 'Time is required', 'appointer' );
                }
                if ( isset( $_POST['birs_appointment_date'] ) && $_POST['birs_appointment_date'] &&
                    isset( $_POST['birs_appointment_time'] ) && $_POST['birs_appointment_time'] ) {
                    $datetime = array(
                        'date' => $_POST['birs_appointment_date'],
                        'time' => $_POST['birs_appointment_time']
                    );
                    $datetime = $birchpress->util->get_wp_datetime( $datetime );
                    if ( !$datetime ) {
                        $errors['birs_appointment_datetime'] = __( 'Date & time is incorrect', 'appointer' );
                    }
                }
                return $errors;
            } );

        birch_defn( $ns, 'validate_client_info', function() {
                $errors = array();
                if ( !$_POST['birs_client_name_first'] ) {
                    $errors['birs_client_name_first'] = __( 'This field is required', 'appointer' );
                }
                if ( !$_POST['birs_client_name_last'] ) {
                    $errors['birs_client_name_last'] = __( 'This field is required', 'appointer' );
                }
                if ( !$_POST['birs_client_email'] ) {
                    $errors['birs_client_email'] = __( 'Email is required', 'appointer' );
                } else if ( !is_email( $_POST['birs_client_email'] ) ) {
                    $errors['birs_client_email'] = __( 'Email is incorrect', 'appointer' );
                }
                if ( !$_POST['birs_client_phone'] ) {
                    $errors['birs_client_phone'] = __( 'This field is required', 'appointer' );
                }

                return $errors;
            } );

        birch_defn( $ns, 'validate_appointment1on1_info', function() {
                return array();
            } );

        birch_defn( $ns, 'ajax_schedule', function() use ( $ns ) {
                global $birchpress, $appointer;

                $appointment_errors = $ns->validate_appointment_info();
                $appointment1on1_errors = $ns->validate_appointment1on1_info();
                $client_errors = $ns->validate_client_info();
                $errors = array_merge( $appointment_errors, $appointment1on1_errors, $client_errors );
                if ( $errors ) {
                    $appointer->view->render_ajax_error_messages( $errors );
                }
                $client_config = array(
                    'base_keys' => array(),
                    'meta_keys' => $_POST['birs_client_fields']
                );
                $client_info = $appointer->view->merge_request( array(), $client_config, $_POST );
                unset( $client_info['ID'] );
                $client_id = $appointer->model->booking->save_client( $client_info );
                $appointment1on1_config = array(
                    'base_keys' => array(),
                    'meta_keys' => array_merge(
                        $appointer->model->get_appointment_fields(),
                        $appointer->model->get_appointment1on1_fields(),
                        $appointer->model->get_appointment1on1_custom_fields()
                    )
                );
                $appointment1on1_info =
                $appointer->view->merge_request( array(), $appointment1on1_config, $_POST );
                $datetime = array(
                    'date' => $_POST['birs_appointment_date'],
                    'time' => $_POST['birs_appointment_time']
                );
                $datetime = $birchpress->util->get_wp_datetime( $datetime );
                $timestamp = $datetime->format( 'U' );
                $appointment1on1_info['_birs_appointment_timestamp'] = $timestamp;
                $appointment1on1_info['_birs_client_id'] = $client_id;
                unset( $appointment1on1_info['ID'] );
                unset( $appointment1on1_info['_birs_appointment_id'] );
                $appointment1on1_id = $appointer->model->booking->make_appointment1on1( $appointment1on1_info );
                $appointer->model->booking->change_appointment1on1_status( $appointment1on1_id, 'publish' );

                if ( $appointment1on1_id ) {
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
                    $appointer->view->render_ajax_success_message( array(
                            'code' => 'success',
                            'message' => json_encode( array(
                                    'url' => htmlentities( $cal_url )
                                ) )
                        ) );
                }
            } );

    } );
